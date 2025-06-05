
<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");


function getStorageLocationListForGrn()
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `storage_location_type`="RM-WH" AND `storage_location_material_type`="RM" AND `storage_location_storage_type`="Open" AND `status`="active"', true);
}

function getItemCodeAndHsn($vendorCode, $vendorItemTitle)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    $vendorGoodsCodeObj = queryGet("SELECT `itemCode` FROM `" . ERP_VENDOR_ITEM_MAP . "` WHERE `branchId`='" . $branch_id . "' AND `vendorCode`='" . $vendorCode . "' AND `itemTitle`='" . strip_tags($vendorItemTitle) . "'");
    if ($vendorGoodsCodeObj["status"] == "success") {
        $itemCode = $vendorGoodsCodeObj["data"]["itemCode"];
        $goodsHsnObj = queryGet("SELECT `itemId`, `hsnCode` FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `branch`='" . $branch_id . "' AND `itemCode`='" . $itemCode . "'");
        if ($goodsHsnObj["status"] == "success") {
            return [
                "itemCode" => $itemCode,
                "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                "itemId" => $goodsHsnObj["data"]["itemId"]
            ];
        } else {
            return [
                "itemCode" => $vendorGoodsCodeObj["data"]["itemCode"],
                "itemHsn" => "",
                "itemId" => ""
            ];
        }
    } else {
        return [
            "itemCode" => "",
            "itemHsn" => "",
            "itemId" => ""
        ];
    }
}



function processInvoice($link, $filename, $uploaded_filename)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    $total_amt = "";
    $gst_no = "";
    $status = "";
    $remarks = "";

    if (isset($branch_id) && $branch_id != "") {
        $loginBranchGstin = "";
        $branchDeails = [];
        $branchDeailsObj = queryGet("SELECT `erp_branches`.*,`erp_companies`.`company_name`, `erp_companies`.`company_pan`,`erp_companies`.`company_const_of_business` FROM `erp_branches`, `erp_companies` WHERE `erp_branches`.`company_id`=`erp_companies`.`company_id` AND `branch_id`=" . $branch_id);
        if ($branchDeailsObj["status"] == "success") {
            $branchDeails = $branchDeailsObj["data"];
            $loginBranchGstin = $branchDeails["branch_gstin"];
            $loginBranchName = $branchDeails["branch_name"];
            $loginCompanyName = $branchDeails["company_name"];
            $loginCompanyPan = $branchDeails["company_pan"];
            $loginCompanyConstOfBusiness = $branchDeails["company_const_of_business"];
        } else {
            return [
                "status" => "warning",
                "message" => "Branch not found!",
                "file" => $filename
            ];
        }
        // $billControllerObj = new BillController();

        //$loginBranchGstin = "27AAGCC4935R1ZZ";
        //$loginBranchGstin="19AADCB0892P1Z4";
        $ocrInvoiceControllerObj = new OcrInvoiceController();

        $invoiceRawData = $ocrInvoiceControllerObj->readInvoice($link);

        if (isset($invoiceRawData) && $invoiceRawData["status"] == "success") {
            if (isset($invoiceRawData["data"]["Items"]) && count($invoiceRawData["data"]["Items"]) > 0) {

                $readInvoiceObj = $ocrInvoiceControllerObj->processInvoiceRawData($invoiceRawData["data"], $loginBranchGstin);


                if ($readInvoiceObj["status"] == "success") {

                    //Insert the invoice in GRN table
                    $inv_no = $readInvoiceObj["data"]["InvoiceId"] ?? "";
                    $po_no = $readInvoiceObj["data"]["PurchaseOrder"] ?? "";
                    $vendor_name = $readInvoiceObj["data"]["VendorName"] ?? $readInvoiceObj["data"]["VendorAddressRecipient"];
                    $gst_no = $readInvoiceObj["data"]["VendorTaxId"];
                    $CustomerName = $readInvoiceObj["data"]["CustomerName"] ?? "";
                    $CustomerTaxId = $readInvoiceObj["data"]["CustomerTaxId"] ?? "";
                    $total_amt = $readInvoiceObj["data"]["InvoiceTotal"] ?? "";
                    $vendorPan = substr($gst_no, 2, 10);
                    // return $gst_no;
                    //echo json_encode($readInvoiceObj,true);




                    $grn_read_json = addslashes(serialize($readInvoiceObj));

                    $original_file_name = $filename;
                    $uploaded_file_name = $uploaded_filename;
                    $branch_gst_no = $branchDeails["branch_gstin"] ?? "";

                    $notYourInvoiceError = false;
                    if ($CustomerTaxId != "" && $CustomerTaxId != $loginBranchGstin) {
                        $notYourInvoiceError = true;
                    }
                    // else if(checkCompanyNames($CustomerName, $loginCompanyName) == false){
                    //     $notYourInvoiceError = true;
                    // }
                    if(!empty($inv_no) || $inv_no !== null){
                    if ($notYourInvoiceError) {
                        $status = "warning";
                        $remarks = "It seems not your invoice";

                        //Inster into Log

                        $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
                `company_id`='" . $company_id . "',
                `branch_id`='" . $branch_id . "',
                `location_id`='" . $location_id . "',
                `status`='" . $status . "',
                `remarks`='" . $remarks . "',
                `vendor_gst`='" . $gst_no . "',
                `price`='" . $total_amt . "',
                `file_name`='" . $filename . "'");

                        return [
                            "status" => "warning",
                            "message" => "It seems not your invoice",
                            "file" => $filename,
                            "gst_check" => true,
                            "customer_gst" => $CustomerTaxId,
                            "branch_gst" => $loginBranchGstin,
                            "unique_id" => $inserGrnlog["insertedId"],
                            "inv_no" => $inv_no,
                            "po_no" => $po_no,
                            "vendor_name" => $vendor_name,
                            "gst_no" => $gst_no,
                            "CustomerName" => $CustomerName,
                            "grn_read_json" => $grn_read_json,
                            "original_file_name" => $original_file_name,
                            "uploaded_file_name" => $uploaded_file_name,
                            "total_amt" => $total_amt,
                            "vendorPan" => $vendorPan
                        ];
                    }



                    // $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $branchDeails["company_id"] . "' AND `vendor_pan` = '" . $vendorPan . "'");


                    if (!empty($gst_no)) {
                        $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $branchDeails["company_id"] . "' AND `vendor_gstin` = '" . $gst_no . "'");
                    } else if (!empty($vendorPan)) {
                        $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $branchDeails["company_id"] . "' AND `vendor_pan` = '" . $vendorPan . "'");
                    } else {
                        if(!empty($vendor_name))
                        {
                            $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $branchDeails["company_id"] . "' AND (`trade_name`  LIKE '%" . $vendor_name . "%' OR `legal_name` = '%" . $vendor_name . "%')");
                        }
                        else
                        {
                            $vendorObj["status"] = "fail";
                        }
                        
                    }

                    $vendorCode = "";
                    $vendorId = "";
                    $vendorCreditPeriod = "";
                    $vendorSuggestionObj = [];
                    if ($vendorObj["status"] == "success") {
                        $vendorCode = $vendorObj["data"]["vendor_code"] ?? "";
                        $vendorId = $vendorObj["data"]["vendor_id"] ?? "";
                        $vendorCreditPeriod = $vendorObj["data"]["vendor_credit_period"] ?? "";
                        $vendor_name = $vendorObj["data"]["trade_name"] ?? "";
                    }

                    if ($vendorId == "") {
                        // Unregistered Vendor
                        $status = "success";
                        $remarks = "Invoice successfully processed.";

                        $inserGrnObj = queryInsert("INSERT INTO `erp_grn_multiple` SET 
                            `company_id`='" . $company_id . "',
                            `branch_id`='" . $branch_id . "',
                            `location_id`='" . $location_id . "',
                            `inv_no`='" . $inv_no . "',
                            `po_no`='" . $po_no . "',
                            `vendor_id`='" . $vendorId . "',
                            `vendor_code`='" . $vendorCode . "',
                            `vendor_name`='" . addslashes($vendor_name) . "',
                            `vendor_credit_period`='" . $vendorCreditPeriod . "',
                            `customer_gst`='" . $CustomerTaxId . "',
                            `gst_no`='" . $gst_no . "',
                            `grn_read_json`='" . $grn_read_json . "',
                            `original_file_name`='" . $original_file_name . "',
                            `uploaded_file_name`='" . $uploaded_file_name . "',
                            `branch_gst_no`='" . $branch_gst_no . "',
                            `total_amt`='" . $total_amt . "',
                            `created_by`='" . $created_by . "',
                            `updated_by`='" . $updated_by . "'");


                        //Inster into Log

                        $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
                 `company_id`='" . $company_id . "',
                 `branch_id`='" . $branch_id . "',
                 `location_id`='" . $location_id . "',
                 `status`='" . $status . "',
                 `remarks`='" . $remarks . "',
                 `vendor_gst`='" . $gst_no . "',
                 `price`='" . $total_amt . "',
                 `file_name`='" . $filename . "'");

                        return [
                            "status" => "success",
                            "message" => "Invoice successfully processed.",
                            "file" => $filename,
                            "id" => $inserGrnObj["insertedId"]
                        ];
                    } else {
                        //Registered Vendor
                        //Check Duplicate
                        if (!empty($inv_no) || $inv_no !== null) {

                        
                        $check = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `company_id`='" . $branchDeails["company_id"] . "' AND `inv_no` = '" . $inv_no . "' AND `vendor_id`='" . $vendorId . "' AND `grn_active_status`='active'");
                        // $check_posted_grn = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $branchDeails["company_id"] . "' AND `vendorDocumentNo` = '" . $inv_no . "'");

                        if ($check["numRows"] == 0) {

                            $status = "success";
                            $remarks = "Invoice successfully processed.";

                            $inserGrnObj = queryInsert("INSERT INTO `erp_grn_multiple` SET 
                            `company_id`='" . $company_id . "',
                            `branch_id`='" . $branch_id . "',
                            `location_id`='" . $location_id . "',
                            `inv_no`='" . $inv_no . "',
                            `po_no`='" . $po_no . "',
                            `vendor_id`='" . $vendorId . "',
                            `vendor_code`='" . $vendorCode . "',
                            `vendor_name`='" . addslashes($vendor_name) . "',
                            `vendor_credit_period`='" . $vendorCreditPeriod . "',
                            `customer_gst`='" . $CustomerTaxId . "',
                            `gst_no`='" . $gst_no . "',
                            `grn_read_json`='" . $grn_read_json . "',
                            `original_file_name`='" . $original_file_name . "',
                            `uploaded_file_name`='" . $uploaded_file_name . "',
                            `branch_gst_no`='" . $branch_gst_no . "',
                            `total_amt`='" . $total_amt . "',
                            `created_by`='" . $created_by . "',
                            `updated_by`='" . $updated_by . "'");


                            //Inster into Log

                            $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
                 `company_id`='" . $company_id . "',
                 `branch_id`='" . $branch_id . "',
                 `location_id`='" . $location_id . "',
                 `status`='" . $status . "',
                 `remarks`='" . $remarks . "',
                 `vendor_gst`='" . $gst_no . "',
                 `price`='" . $total_amt . "',
                 `file_name`='" . $filename . "'");

                            return [
                                "status" => "success",
                                "message" => "Invoice successfully processed.",
                                "file" => $filename,
                                "id" => $inserGrnObj["insertedId"]
                            ];
                        } else {
                            $status = "warning";
                            $remarks = "This Invoice already exists";

                            //Inster into Log

                            $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
                `company_id`='" . $company_id . "',
                `branch_id`='" . $branch_id . "',
                `location_id`='" . $location_id . "',
                `status`='" . $status . "',
                `remarks`='" . $remarks . "',
                `vendor_gst`='" . $gst_no . "',
                `price`='" . $total_amt . "',
                `file_name`='" . $filename . "'");

                            return [
                                "status" => "warning",
                                "message" => "This Invoice already exists",
                                "file" => $filename,
                                "gst_check" => false
                            ];
                        }
                    }
                    else{
                            $status = "warning";
                            $remarks = "Invoice No Not Found!";
                            $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
                `company_id`='" . $company_id . "',
                `branch_id`='" . $branch_id . "',
                `location_id`='" . $location_id . "',
                `status`='" . $status . "',
                `remarks`='" . $remarks . "',
                `vendor_gst`='" . $gst_no . "',
                `price`='" . $total_amt . "',
                `file_name`='" . $filename . "'");

                            return [
                                "status" => "warning",
                                "message" => "The system was unable to read the invoice properly. You can proceed through the Purchase Order (PO) instead",
                                "file" => $filename,
                                "gst_check" => false
                            ];

                    }
                    }
                    // return [
                    //     "status" => "success",
                    //     "message" => "Invoice successfully processed.",
                    //     "invoiceData" => $readInvoiceObj["data"],
                    //     "invoiceFile" => $filename,
                    //     "branchDetails" => $branchDeails
                    // ];
                }else{
                        return [
                            "status" => "warning",
                            "message" => "The system was unable to read the invoice properly. You can proceed through the Purchase Order (PO) instead",
                            "file" => $filename,
                            "gst_check" => false
                        ];
                }
                } else {

                    $status = "warning";
                    $remarks = "This is Invalid Invoice, cannot proceed.";

                    //Inster into Log

                    $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
            `company_id`='" . $company_id . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `status`='" . $status . "',
            `remarks`='" . $remarks . "',
            `vendor_gst`='',
            `price`='',
            `file_name`='" . $filename . "'");

                    return [
                        "status" => "warning",
                        "message" => "System unable to read Invoice, process denied!",
                        "file" => $filename,
                        "gst_check" => false
                    ];
                }
            } else {
                return [
                    "status" => "warning",
                    "message" => "System unable to read Invoice, process denied!",
                    "file" => $filename,
                    "gst_check" => false
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "System unable to read Invoice, process denied!",
                "file" => $filename,
                "gst_check" => false
            ];
        }
    } else {
        return [
            "status" => "warning",
            "message" => "Please do login first",
            "file" => $filename,
            "gst_check" => false
        ];
    }
}





if (!empty(array_filter($_FILES['grnInvoiceFilemultiple']['name']))) {

    $array = array();

    foreach ($_FILES['grnInvoiceFilemultiple']['tmp_name'] as $key => $value) {
        $file_tmpname = $_FILES['grnInvoiceFilemultiple']['tmp_name'][$key];
        $file_name = $_FILES['grnInvoiceFilemultiple']['name'][$key];
        $file_size = $_FILES['grnInvoiceFilemultiple']['size'][$key];

        $allowed_types = ['pdf', 'jpg', 'png', 'jpeg'];
        $maxsize = 2 * 1024 * 1024; // 10 MB

        if ($file_tmpname == "" || $file_tmpname == NULL) {
            continue;
        } else {
            $uploadedInvoiceObj = uploadFile(["name" => $file_name, "tmp_name" => $file_tmpname, "size" => $file_size], COMP_STORAGE_DIR . "/grn-invoice/", $allowed_types, $maxsize);

            if ($uploadedInvoiceObj["status"] == "success") {
                $uploadedInvoiceName = $uploadedInvoiceObj["data"];
                $uploadedInvoiceUrl = COMP_STORAGE_URL . "/grn-invoice/" . $uploadedInvoiceName;

                $processInvoiceObj = processInvoice($uploadedInvoiceUrl, $file_name, $uploadedInvoiceName);

                $array[] = $processInvoiceObj;

                //echo json_encode($processInvoiceObj);

                // kheye ese bosci bhai


            } else {
                // console($uploadedInvoiceObj);
                $array[] = array("status" => "failed", "message" => $uploadedInvoiceObj["message"], "file" => $file_name);
            }
        }
    }
} else {
    echo "No files selected.";
}




$variable = "";

if (count($array) > 0) {

    foreach ($array as $key => $value) {
        if ($value["status"] == "success") {
            $variable .= " <div class='row'>
            <div class='col-lg-4 col-md-6 col-sm-12'>
            <div class='card grn-upload-card'>
                <div class='card-body'>
                    <div class='card-box'>
                        <div class='file-name-status'>
                            <p class='text-xs mb-2'>" . $value["file"] . "</p>
                            <div class='result-status-text-icon'>
                            <p class='font-bold text-sm text-success'>" . $value["message"] . "</p>
                            <div class='icon-status'>
                            <i class='fa fa-check mr-2' style='border-radius: 50%; background: #22cc62; padding: 10px; color: #fff;'></i>
                        </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        ";
        } elseif ($value["status"] == "warning" && $value["gst_check"] == true) {

            $branch_gst = str_split($value["branch_gst"]);
            $customer_gst = str_split($value["customer_gst"]);
            $arrydiff = array_diff_assoc($branch_gst, $customer_gst);
            $each_array = [];
            foreach ($arrydiff as $arrydiffkey => $each_word) {
                $each_array[] = $arrydiffkey;
            }

            $variable .= "
        
            <div class='row'>
            <div class='col-lg-6 col-md-6 col-sm-12'>
                <div class='card grn-upload-card'>
                    <div class='card-body pl-2'>
                        <div class='card-box'>
                            <div class='file-name-status'>
                                <p class='text-xs mb-2'>" . $value["file"] . "</p>
                                <div class='result-status-text-icon'>
                                    <p class='font-bold text-sm text-success'>" . $value["message"] . "</p>
        
                                    <div class='icon-status'>
                                        <i class='fa fa-exclamation-triangle mr-2' style='border-radius: 50%; background: #feba22; padding: 10px; color: #fff;'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='card mismatch-grn-table'>
                <div class='card-body p-2'>
                    <table class='table table-nowrap grn-invoice-table'>
                        <tbody>
                            <tr>
                                <td class='font-bold text-left' style='width: 20%'>GSTIN FROM OCR</td>";

            for ($i = 0; $i < count($branch_gst); $i++) {

                if (in_array($i, $each_array)) {
                    $variable .= " 
                                        <td style='background-color: #f7bcbc;' class = 'gst_col_" . $value["unique_id"] . "_" . $i . "_1'>
                                            <p contenteditable='true' class = 'gst_" . $value["unique_id"] . " gst_input_change' data-id = '" . $value["unique_id"] . "_" . $i . "'>" . $customer_gst[$i] . "</p>
                                        </td>";
                } else {
                    $variable .= " 
                                        <td class = 'gst_col_" . $value["unique_id"] . "_" . $i . "_1'>
                                            <p contenteditable='true' class = 'gst_" . $value["unique_id"] . " gst_input_change' data-id = '" . $value["unique_id"] . "_" . $i . "'>" . $customer_gst[$i] . "</p>
                                        </td>";
                }
            }
            $variable .= "    
                            </tr>
                            <tr>
                                <td class='font-bold text-left' style='width: 20%'>MY GSTIN</td>";


            foreach ($branch_gst as $branchkey => $each_branch_gst) {
                if (in_array($branchkey, $each_array)) {
                    $variable .= "<td style='background-color: #f7bcbc;' class = 'gst_col_" . $value["unique_id"] . "_" . $branchkey . "_2'>" . $each_branch_gst . "</td>";
                } else {
                    $variable .= "<td class = 'gst_col_" . $value["unique_id"] . "_" . $branchkey . "_2'>" . $each_branch_gst . "</td>";
                }
            }

            $variable .= "      
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class='card-footer'>
                    <button data-id = '" . $value["unique_id"] . "' data-branch = '" . $value["branch_gst"] . "' data-inv_no = '" . $value["inv_no"] . "' data-po_no = '" . $value["po_no"] . "' data-vendor_name = '" . base64_encode($value["vendor_name"]) . "' data-gst_no = '" . $value["gst_no"] . "' data-CustomerName = '" . $value["CustomerName"] . "' data-grn_read_json = '" . base64_encode($value["grn_read_json"]) . "' data-original_file_name = '" . $value["original_file_name"] . "' data-uploaded_file_name = '" . $value["uploaded_file_name"] . "' data-total_amt = '" . $value["total_amt"] . "' data-vendorpan = '" . $value["vendorPan"] . "' class='btn btn-primary float-right grn_proceed' type = 'button'>Proceed</button>
                </div>
            </div>
            </div>
            <div class='col-lg-6 col-md-6 col-sm-12'>
                <div class='redirect-list float-right'>
                    <a href='" . LOCATION_URL . "manage-pending-grn.php' class='btn btn-primary'>Redirect to GRN List</a>
                </div>
            </div>
        </div>

        ";
        } else {
            $variable .= "
        
            <div class='row'>
            <div class='col-lg-4 col-md-6 col-sm-12'>
                <div class='card grn-upload-card'>
                    <div class='card-body'>
                        <div class='card-box'>
                            <div class='file-name-status'>
                                <p class='text-xs mb-2'>" . $value["file"] . "</p>
                                <div class='result-status-text-icon'>
                                    <p class='font-bold text-sm text-success'>" . $value["message"] . "</p>
        
                                    <div class='icon-status'>
                                        <i class='fa fa-exclamation-triangle mr-2' style='border-radius: 50%; background: #feba22; padding: 10px; color: #fff;'></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        '";
        }
    }
}

if (count($array) == 1 && $array[0]["status"] == "success") {
    echo json_encode(array("data" => $variable, "count" => 1, "status" => $array[0]["status"], "link" => LOCATION_URL . 'manage-pending-grn.php?view=' . $array[0]["id"], "ocr" => 10));
} else {
    echo json_encode(array("data" => $variable, "count" => count($array), "status" => "", "link" => "", "ocr" => 10));
}
