<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-bills-controller.php");
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
        $branchDeailsObj = queryGet("SELECT * FROM " . ERP_BRANCHES . " WHERE `branch_id`=" . $branch_id);
        if ($branchDeailsObj["status"] == "success") {
            $branchDeails = $branchDeailsObj["data"];
            $loginBranchGstin = $branchDeails["branch_gstin"];
        } else {
            return [
                "status" => "warning",
                "message" => "Branch not found!",
                "file" => $filename
            ];
        }
        $billControllerObj = new BillController();

        //$loginBranchGstin = "27AAGCC4935R1ZZ";
        //$loginBranchGstin="19AADCB0892P1Z4";
        $readInvoiceObj = $billControllerObj->readVendorBillsNew($link, $loginBranchGstin);
        if ($readInvoiceObj["status"] == "success") {




            //Insert the invoice in GRN table
            $inv_no = $readInvoiceObj["data"]["InvoiceId"] ?? "";
            $po_no = $readInvoiceObj["data"]["PurchaseOrder"] ?? "";
            $vendor_name = $readInvoiceObj["data"]["VendorAddressRecipient"] ?? "";
            if ($readInvoiceObj["data"]["gstin_data"]["vendor_gstin"] != "") {
                $gst_no = $readInvoiceObj["data"]["gstin_data"]["vendor_gstin"];
            } else {
                $gst_no = $readInvoiceObj["data"]["gstin_data"]["customer_gstin"];
            }

            // return $gst_no;
            // $grn_read_json = json_encode($readInvoiceObj["data"],true);
            $grn_read_json = serialize($readInvoiceObj);
            $original_file_name = $filename;
            $uploaded_file_name = $uploaded_filename;
            $branch_gst_no = $branchDeails["branch_gstin"] ?? "";
            $total_amt = $readInvoiceObj["data"]["InvoiceTotal"] ?? "";
            $vendorPan = substr($gst_no, 2, 10);
            $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $branchDeails["company_id"] . "' AND `vendor_pan` = '" . $vendorPan . "'");
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

            //Check Duplicate
            $check = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `company_id`='" . $branchDeails["company_id"] . "' AND `inv_no` = '" . $inv_no . "'");
            $check_posted_grn = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $branchDeails["company_id"] . "' AND `vendorDocumentNo` = '" . $inv_no . "'");

            if ($check["numRows"] == 0 && $check_posted_grn["numRows"] == 0) {

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
                            `vendor_name`='" . $vendor_name . "',
                            `vendor_credit_period`='" . $vendorCreditPeriod . "',
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
                    "file" => $filename
                ];
            }

            // return [
            //     "status" => "success",
            //     "message" => "Invoice successfully processed.",
            //     "invoiceData" => $readInvoiceObj["data"],
            //     "invoiceFile" => $filename,
            //     "branchDetails" => $branchDeails
            // ];
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
                "message" => "This is Invalid Invoice, can't proceed! (It seems not your invoice!)",
                "file" => $filename
            ];
        }
    } else {
        return [
            "status" => "warning",
            "message" => "Please do login first",
            "file" => $filename
        ];
    }
}



if (!empty(array_filter($_FILES['grnInvoiceFilemultiple']['name']))) {

    $upload_dir = '../../../bills' . DIRECTORY_SEPARATOR;

    $array = array();

    foreach ($_FILES['grnInvoiceFilemultiple']['tmp_name'] as $key => $value) {
        $file_tmpname = $_FILES['grnInvoiceFilemultiple']['tmp_name'][$key];
        $file_name = $_FILES['grnInvoiceFilemultiple']['name'][$key];
        $file_size = $_FILES['grnInvoiceFilemultiple']['size'][$key];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        $allowed_types = array('pdf', 'jpg', 'png', 'jpeg', 'gif');

        $maxsize = 2 * 1024 * 1024; // 10 MB

        if (in_array(strtolower($file_ext), $allowed_types)) {
            if ($file_size > $maxsize) {
                // echo json_encode("Error: File size is larger than the allowed limit.");

                $array[] = array("status" => "failed", "message" => "File size is larger than the allowed limit.", "file" => $file_name);
            } else {
                $uploaded_filename = time() . rand(10000, 99999) . "." . $file_ext;
                $filepath = $upload_dir . $uploaded_filename;

                if (file_exists($filepath)) {
                    if (move_uploaded_file($file_tmpname, $filepath)) {
                        // echo json_encode("{$file_name} successfully uploaded");
                        $processInvoiceObj = processInvoice($filepath, $file_name, $uploaded_filename);
                        $array[] = $processInvoiceObj;
                        // echo json_encode($array);
                    } else {
                        // echo json_encode("Error uploading {$file_name}");
                        $array[] = array("status" => "failed", "message" => "Error uploading {$file_name}", "file" => $file_name);
                    }
                } else {

                    if (move_uploaded_file($file_tmpname, $filepath)) {
                        // echo json_encode("{$file_name} successfully uploaded");
                        $processInvoiceObj = processInvoice($filepath, $file_name, $uploaded_filename);
                        $array[] = $processInvoiceObj;
                    } else {
                        $array[] = array("status" => "failed", "message" => "Error uploading {$file_name}", "file" => $file_name);
                    }
                }
            }
        } else {
            $array[] = array("status" => "failed", "message" => "{$file_ext} file type is not allowed", "file" => $file_name);
        }

        

    }
} else {
    echo "No files selected.";
}

$variable = "";

if (count($array) > 0) {

    foreach ($array as $key => $value) {
        if ($value["status"] == "success") {
            $variable .= "<div class='card grn-upload-card'><div class='card-body'>
        <div class='card-box'>
            <div class='file-name-status'>
                <p class='text-xs mb-2'>" . $value["file"] . "</p>
                <p class='text-sm font-bold text-success'>" . $value["message"] . "</p>
            </div>
            <div class='icon-status'>
                <i class='fa fa-check mr-2' style='border-radius: 50%; background: #198754; padding: 10px; color: #fff;'></i>
            </div>
        </div>
    </div></div>";
        } else {
            $variable .= "
        
        <div class='row'>
            <div class='col-lg-4 col-md-6 col-sm-12'>
            <div class='card grn-upload-card'>
            <div class='card-body'>
            <div class='card-box'>
                <div class='file-name-status'>
                    <p class='text-xs mb-2'>" . $value["file"] . "</p>
                    <p class='font-bold text-sm text-success'>" . $value["message"] . "</p>
                </div>
                <div class='icon-status'>
                <i class='fa fa-exclamation-triangle mr-2' style='border-radius: 50%; background: #feba22; padding: 10px; color: #fff;'></i>
                </div>
            </div>
        </div></div>
            </div>
          <div class='col-lg-8 col-md-6 col-sm-6'>
                <div class='card'>
                    <div class='card-body' style='overflow: auto;'>
                    <div class='row'>
                        <div class='col-lg-12 col-md-12 col-sm-12'>
                            <button class='btn btn-primary float-right m-3'>List</button>
                        </div>
                    </div>
                    <table class='table defaultDataTable table-hover' data-responsive='true'>
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Sl No.</th>
                            <th>Sl No.</th>
                            <th>Sl No.</th>
                            <th>Sl No.</th>
                            <th>Sl No.</th>
                            <th>Sl No.</th>
                            <th>Sl No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                        </tr>
                    </tbody>
                </table>
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


// $filesNum = count($_FILES);
// // Looping all files
// for ($i = 0; $i < $filesNum; $i++) {
//     // same the file
//     // move_uploaded_file($_FILES['file']['tmp_name'][$i], $_FILES['file']['name'][$i]);
//     // $processInvoiceObj = processInvoice($_FILES);

//     echo json_encode($_FILES["grnInvoiceFile"]["name"]);
// }



//console($processInvoiceObj["invoiceData"]);
?>

