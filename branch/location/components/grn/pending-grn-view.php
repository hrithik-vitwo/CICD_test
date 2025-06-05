<?php
function getStorageLocationListForGrn()
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    global $isQaEnabled;

    // return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` AS storage LEFT JOIN `erp_storage_warehouse` ON erp_storage_warehouse.`warehouse_id`= storage.`warehouse_id` WHERE storage.`company_id`=' . $company_id . ' AND storage.`branch_id`=' . $branch_id . ' AND storage.`location_id`=' . $location_id . ' AND storage.`storage_location_type` IN ("RM-WH","FG-WH","QA","Asset") AND storage.`storage_location_material_type` IN ("RM","FG","QA","Asset") AND storage.`storage_location_storage_type`="Open" AND storage.`status`="active"', true);



    $slSql = 'SELECT * FROM
    `erp_storage_location` AS STORAGE
        LEFT JOIN `erp_storage_warehouse` ON erp_storage_warehouse.`warehouse_id` = STORAGE.`warehouse_id`
        WHERE STORAGE.`company_id` = ' . $company_id . '
        AND STORAGE.`branch_id` = ' . $branch_id . '
        AND STORAGE.`location_id` = ' . $location_id . ' 
        AND STORAGE.`storage_location_type` IN ("RM-WH", "FG-WH", "QA", "Asset") 
        AND STORAGE.`storage_location_material_type` IN ("RM", "FG", "QA", "Asset") 
        AND STORAGE.`storage_location_storage_type` = "Open" 
        AND STORAGE.`status` = "active"';

    return queryGet($slSql, true);


    // $isQaEnabled = 1;

    // if ($isQaEnabled == 1) {
    //     return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` AS storage LEFT JOIN `erp_storage_warehouse` ON erp_storage_warehouse.`warehouse_id`= storage.`warehouse_id` WHERE storage.`company_id`=' . $company_id . ' AND storage.`branch_id`=' . $branch_id . ' AND storage.`location_id`=' . $location_id . ' AND storage.`storage_location_type` = "QA" AND storage.`storage_location_material_type` = "QA" AND storage.`status`="active"', true);
    // } else {

    // }
}



function getCostCenterListForGrn()
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    return queryGet('SELECT * FROM `erp_cost_center` WHERE `company_id`=' . $company_id . ' AND `CostCenter_status`="active"', true);
}


function getSlabPercentage($amount, $slabArray)
{
    $slab = array_reduce($slabArray, function ($carry, $item) use ($amount) {
        $lowerLimit = $item[0];
        $upperLimit = $item[1];
        $percentage = $item[2];

        if ($amount >= $lowerLimit && ($upperLimit === null || $amount < $upperLimit)) {
            return $percentage;
        }

        return $carry;
    }, 0);

    return $slab;
}


function getItemCodeAndHsn($vendorCode, $vendorItemTitle, $baseAmt = 0)
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;

    $vendorGoodsCodeObj = queryGet("SELECT `itemId`,`itemCode`,`itemType` FROM `" . ERP_VENDOR_ITEM_MAP . "` WHERE `companyId`='" . $company_id . "' AND `vendorCode`='" . $vendorCode . "' AND `itemTitle`='" . strip_tags($vendorItemTitle) . "' ORDER BY `vendorItemMapId` DESC LIMIT 1");
    if ($vendorGoodsCodeObj["status"] == "success") {
        $itemCode = $vendorGoodsCodeObj["data"]["itemCode"];
        $itemType = $vendorGoodsCodeObj["data"]["itemType"];
        $item_id = $vendorGoodsCodeObj["data"]["itemId"];

        // console($item_id);

        // return $vendorGoodsCodeObj;


        $goodsHsnObj = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` as goods LEFT JOIN `erp_hsn_code` as hsn ON goods.hsnCode=hsn.hsnCode WHERE goods.company_id='" . $company_id . "' AND goods.itemId='" . $item_id . "'");

        if ($goodsHsnObj["status"] == "success") {

            $baseunitmeasure = $goodsHsnObj["data"]["baseUnitMeasure"];
            $tds_id = $goodsHsnObj["data"]["tds"];

            $getTds = queryGet("SELECT `TDSRate`,`slab_serialized` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");

            $slab = unserialize($getTds["data"]["slab_serialized"]);

            $percentage = getSlabPercentage($baseAmt, $slab);

            $getUOM = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $baseunitmeasure . "'");

            if ($getUOM["status"] == "success") {
                return [
                    "itemCode" => $itemCode,
                    "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                    "itemId" => $goodsHsnObj["data"]["itemId"],
                    "itemName" => $goodsHsnObj["data"]["itemName"],
                    "tax" => $goodsHsnObj["data"]["taxPercentage"],
                    "goodsType" => $goodsHsnObj["data"]["goodsType"],
                    "uom" => $getUOM["data"]["uomName"],
                    "uom_id" => $baseunitmeasure,
                    "tds" => $percentage,
                    "slab" => $slab,
                    "type" => $itemType
                ];
            } else {
                return [
                    "itemCode" => $itemCode,
                    "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                    "itemId" => $goodsHsnObj["data"]["itemId"],
                    "itemName" => $goodsHsnObj["data"]["itemName"],
                    "tax" => $goodsHsnObj["data"]["taxPercentage"],
                    "goodsType" => $goodsHsnObj["data"]["goodsType"],
                    "uom" => "",
                    "uom_id" => $baseunitmeasure,
                    "tds" => $percentage,
                    "slab" => $slab,
                    "type" => $itemType
                ];
            }
        } else {
            return [
                "itemCode" => $vendorGoodsCodeObj["data"]["itemCode"],
                "itemHsn" => "",
                "tax" => "",
                "goodsType" => "",
                "itemId" => "",
                "itemName" => "",
                "type" => $itemType
            ];
        }
    } else {
        return [
            "itemCode" => "",
            "itemHsn" => "",
            "itemId" => "",
            "tax" => "",
            "goodsType" => "",
            "itemName" => "",
            "type" => ""
        ];
    }
}



$id = $_GET["view"];
$grnNo = "GRN" . time() . rand(100, 999);

$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];

// console($companyCurrencyData["currency_name"]);

$InvoiceObj = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `grn_mul_id` = '" . $id . "'", false);
$InvoiceData = $InvoiceObj["data"];


if ($InvoiceData["vendor_code"] == "" && $InvoiceData["gst_no"] != "") {
    $Vendorgst = $InvoiceData["gst_no"];
    $checkGstSql = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id` = '" . $company_id . "' AND `company_branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `vendor_gstin`='" . $Vendorgst . "'", false);

    if ($checkGstSql["status"] == "success") {
        $checkGst = $checkGstSql["data"];
        $v_id = $checkGst["vendor_id"];
        $v_code = $checkGst["vendor_code"];
        $update = queryUpdate("UPDATE `erp_grn_multiple` SET `vendor_id`='" . $v_id . "', `vendor_code`='" . $v_code . "' WHERE `grn_mul_id`='" . $id . "'");
    }
}


$processInvoiceObj = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `grn_mul_id` = '" . $id . "'", false);
$invoiceDataGet = $processInvoiceObj["data"];

$invoice_data_json = unserialize(stripslashes($invoiceDataGet["grn_read_json"]));
// console($invoice_data_json);

$invoiceData = $invoice_data_json["data"];
$invoiceFile = $invoiceDataGet["uploaded_file_name"];

$removedItems = $invoiceData["RemovedItems"];
// console($removedItems);

$documentNo = $invoiceData["InvoiceId"] ?? "";
$documentDate = $invoiceData["InvoiceDate"] ?? "";
$dueDate = $invoiceData["DueDate"] ?? "";

$invoiceTotal = $invoiceData["InvoiceTotal"] ?? 0;
$invoiceSubTotal = $invoiceData["SubTotal"] ?? 0;
$invoiceTaxTotal = $invoiceData["TotalTax"] ?? 0;

$customerName = $invoiceData["CustomerName"] ?? "";
$customerPurchaseOrder = $invoiceDataGet["po_no"] ?? "";

global $branch_id;

$branchDeailsObj = queryGet("SELECT `erp_branches`.*,`erp_companies`.`company_name`, `erp_companies`.`company_pan`,`erp_companies`.`company_const_of_business` FROM `erp_branches`, `erp_companies` WHERE `erp_branches`.`company_id`=`erp_companies`.`company_id` AND `branch_id`=" . $branch_id);
if ($branchDeailsObj["status"] == "success") {
    $branchDeails = $branchDeailsObj["data"];
    $loginBranchGstin = $branchDeails["branch_gstin"];
}

$vendorId = $invoiceDataGet["vendor_id"];

$customerGstin = $invoiceDataGet["customer_gst"] != "" ? $invoiceDataGet["customer_gst"] : $loginBranchGstin;

$vendorGstin = $invoiceData["VendorTaxId"] ?? "";

$customerGstinStateCode = substr($customerGstin, 0, 2);

if ($vendorGstin == "" || $vendorGstin == NULL || !isset($vendorGstin)) {
    $vendorGstinStateCode = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=" . $vendorId . " AND `vendor_business_primary_flag`='1' ORDER BY `vendor_business_id` DESC", false)["data"]["state_code"] ?? "";
} else {
    $vendorGstinStateCode = substr($vendorGstin, 0, 2);
}


$vendorAddress = $invoiceData["VendorAddress"] ?? "";
$vendorAddressRecipient = $invoiceData["VendorAddressRecipient"] ?? "";

$vendorGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $vendorGstinStateCode)["data"]["gstStateName"] ?? "";
$customerGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $customerGstinStateCode)["data"]["gstStateName"] ?? "";

$vendorPan = substr($vendorGstin, 2, 10);

if ($vendorId == "" || $vendorId == NULL) {
    $vendorCode = $invoiceDataGet["vendor_code"];
    $vendorName = $invoiceDataGet["vendor_name"] ?? "";
    $vendorCreditPeriod = $invoiceDataGet["vendor_credit_period"];
    $vendor_status = "guest";
} else {
    $ven_details = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id` = '" . $company_id . "' AND `company_branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `vendor_id`='" . $vendorId . "'", false);
    $vendorCode = $ven_details["data"]["vendor_code"];
    $vendorName = $ven_details["data"]["trade_name"] ?? "";
    $vendorCreditPeriod = $ven_details["data"]["vendor_credit_period"];
    $vendor_status = $ven_details["data"]["vendor_status"];
}


$totalCGST = $invoiceData["cgstTotalTax"] == "" ? 0 : $invoiceData["cgstTotalTax"];
$totalSGST = $invoiceData["sgstTotalTax"] == "" ? 0 : $invoiceData["sgstTotalTax"];
$totalIGST = $invoiceData["igstTotalTax"] == "" ? 0 : $invoiceData["igstTotalTax"];

// console($invoiceData["Items"]);

$postStatus = $invoiceDataGet["status"];

$isPoEnabledCompany = false;

$isPoAndGrnInvoiceMatched = true;

$isGrnIvExist = false;
if ($vendorCode != "" && $documentNo != "") {
    $checkGrnExist = queryGet('SELECT `grnId` FROM `erp_grninvoice` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `vendorDocumentNo`="' . $documentNo . '" AND `vendorCode` ="' . $vendorCode . '"');
    if ($checkGrnExist["numRows"] > 0) {
        $isGrnIvExist = true;
    }
    // console($checkGrnExist);
}

if ($dueDate == "" && $vendorCreditPeriod != "" && $documentDate != "") {
    $tempDueDate = date_create($documentDate);
    date_add($tempDueDate, date_interval_create_from_date_string($vendorCreditPeriod . " days"));
    $dueDate = date_format($tempDueDate, "Y-m-d");
}

// console($dueDate);

// if (!$isGrnIvExist) {
$getStorageLocationListForGrnObj = getStorageLocationListForGrn();
$getCostCenterListForGrnObj = getCostCenterListForGrn();
?>

<style>
    .allocateclass table tr th {
        padding: 10px 15px;
        background: #003060;
        color: #fff;
        border-right: 1px solid #fff;
        font-weight: 500;
        font-size: 12px;
        text-align: left;
        white-space: nowrap;
    }

    .allocateclass table tr td {
        font-size: 12px;
        text-align: left;
        color: #3b3b3b;
        vertical-align: middle;
        background: #dbe5ee;
        padding: 5px 15px;
        white-space: nowrap;
    }
</style>

<form action="" method="POST" id="addNewGRNForm">
    <div class="row grn-create">
        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="card">
                <div class="card-header">
                    <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Doc info</h4>
                    </div>
                </div>
                <div class="card-body" id="customerInfo">

                    <div class="row grn-vendor-details">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <input type="hidden" name="grnCode" value="<?= $grnNo ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="grnType" value="grn">
                            <input type="hidden" name="vendorDocumentFile" value="<?= $invoiceFile ?>">
                            <input type="hidden" name="vendorGstinStateName" value="<?= $vendorGstinStateName . '(' . $vendorGstinStateCode . ')'; ?>">
                            <input type="hidden" name="locationGstinStateName" value="<?= $customerGstinStateName . '(' . $customerGstinStateCode . ')' ?>">
                            <!-- <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GRN No :&nbsp;</p>
                                        <p> <?= $grnNo ?></p>
                                    </div> -->
                            <div class="pending-grn-block">
                                <div class="display-flex">
                                    <i class="fa fa-check"></i>&nbsp;
                                    <p class="label-bold">Document No :&nbsp;</p>
                                    <p><input class="form-control" type="text" name="documentNo" id="docNoText" value="<?= $documentNo ?>"></p>
                                    <?php
                                    $button_enable = "true";
                                    $check_doc_no_query = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $company_id . "' AND `vendorDocumentNo` = '" . $documentNo . "' AND `vendorId`='" . $vendorId . "' AND `grnStatus`='active'");
                                    ?>
                                </div>
                                <span class="error text-danger text-xs" id="documentNoValidation"><?php if ($check_doc_no_query["numRows"] != 0) {
                                                                                                        $button_enable = "false";
                                                                                                        echo "This Invoice already exists";
                                                                                                    } ?></span>
                            </div>


                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document Date :&nbsp;</p>
                                <p><input type="date" name="documentDate" value="<?= date("Y-m-d", strtotime($documentDate)); ?>" class="form-control"></p>
                            </div>
                            <div class="display-flex grn-form-input-text">
                                <i class="fa fa-check"></i>
                                &nbsp;

                                <?php
                                $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
                                $check_var_data = $check_var_sql['data'];
                                // console($_SESSION);
                                // // console($check_var_sql);
                                // console($check_var_sql);
                                $max = $check_var_data['month_end'];
                                $min = $check_var_data['month_start'];
                                ?>

                                <p class="label-bold">Posting Date :</p>
                                &nbsp;
                                <?php
                                $dates = postingDateValidation();
                                $start_date = $dates['start_date'];
                                $end_date = $dates['end_date'];
                                $selected_date = $dates['selected_date'];

                                ?>
                                <input type="date" name="invoicePostingDate" value="<?= $selected_date; ?>" id="invoicePostingDateId" class="form-control" min="<?= $start_date ?>" max="<?= $end_date ?>" required>
                                <p class="text-danger text-xs" id="postdatelabel"></p>
                            </div>
                            <div class="display-flex grn-form-input-text">
                                <i class="fa fa-check"></i>
                                &nbsp;
                                <p class="label-bold">Due Date :</p>
                                &nbsp;
                                <input type="date" id="iv_due_date" name="invoiceDueDate" value="<?= $dueDate ?>" class="form-control" required>
                            </div>
                            <div class="display-flex grn-form-input-text">
                                <i class="fa fa-check"></i>
                                &nbsp;
                                <p class="label-bold">PO Number :</p>
                                &nbsp;
                                <input type="text" name="invoicePoNumber" id="invoicePoNumber" value="<?= $customerPurchaseOrder ?>" class="form-control">
                            </div>
                            <div class="display-flex grn-form-input-text">
                                <i class="fa fa-check"></i>
                                &nbsp;
                                <p class="label-bold">Functional Area :</p>
                                &nbsp;
                                <select name="funcArea" class="form-control" required>
                                    <option value="">Functional Area</option>
                                    <?php
                                    $check_func = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id");
                                    $funcs = $check_func['data']['companyFunctionalities'];
                                    $func_ex = explode(",", $funcs);

                                    foreach ($func_ex as $func) {
                                        $func_area = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$func", true);
                                        //console($func_area);

                                    ?>

                                        <option value="<?= $func_area['data'][0]['functionalities_id'] ?>"><?= $func_area['data'][0]['functionalities_name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>

                            </div>
                            <div class="display-flex grn-form-input-text">
                                <p class="label-bold text-italic" style="white-space: pre-line;"><span class="mr-2">*</span>Note : Map Functional area with this invoice to get the expense details functional area wise.</p>
                            </div>

                            <div>
                                <div class="display-flex"><i class="fa fa-check"></i>
                                    <p class="label-bold">&nbsp;Remark </p>
                                </div>
                                <textarea name="extra_remark" id="extra_remark" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-3">
            <div class="card">
                <div class="card-header">
                    <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Vendor info</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row grn-vendor-details">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <input type="hidden" name="vendorCode" id="invoiceVendorCodeInput" value="<?= $vendorCode ?>" class="form-control" />
                            <input type="hidden" name="vendorId" id="invoiceVendorIdInput" value="<?= $vendorId ?>" class="form-control" />
                            <input type="hidden" name="vendorName" id="vendorNameInput" value="<?= $vendorName ?>" class="form-control" />
                            <input type="hidden" name="vendorGstin" value="<?= $vendorGstin ?>" class="form-control" />
                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Code :&nbsp;</p>
                                <p id="invoiceVendorCodeSpan"><?= $vendorCode ?></p>
                            </div>
                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Name :&nbsp;</p>
                                <p id="vendorName"><?= $vendorName ?></p>
                            </div>
                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN :&nbsp;</p>
                                <p> <?= $vendorGstin ?></p>
                            </div>
                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN Status :&nbsp;</p>
                                <p id="vendorGstinStatus" class="status">Loding...</p>
                            </div>
                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Status :&nbsp;</p>
                                <p class="status"><?= $vendor_status ?></p>
                            </div>
                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">State :&nbsp;</p>
                                <p><?= $vendorGstinStateName ?>(<?= $vendorGstinStateCode ?>)</p>
                            </div>
                            <!-- <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Customer State :&nbsp;</p>
                                <p><?= $customerGstinStateName ?>(<?= $customerGstinStateCode ?>)</p>
                            </div> -->
                            <?php
                            $comp_currency = $companyCurrencyData["currency_name"];
                            ?>
                            <div class="currency-conversion-section mt-3">
                                <div class="static-currency">
                                    <input type="text" class="form-control" value="1" readonly>
                                    <input type="text" class="form-control text-right" value="<?= $comp_currency ?>" readonly>
                                </div>
                                <div class="dynamic-currency">
                                    <input type="text" name="currency_conversion_rate" id="currency_conversion_rate" value="1" class="form-control">
                                    <select id="selectCurrency" name="currency" class="form-control text-right">
                                        <?php

                                        $curr = queryGet("SELECT * FROM `erp_currency_type`", true);
                                        foreach ($curr['data'] as $data) {
                                        ?>
                                            <option value="<?= $data['currency_id'] ?>" data-currname="<?= $data['currency_name'] ?>" <?php if ($comp_currency == $data['currency_name']) {
                                                                                                                                            echo "selected";
                                                                                                                                        } ?>><?= $data['currency_name'] ?></option>
                                        <?php
                                        }
                                        ?>

                                    </select>
                                </div>
                                <div class="display-flex grn-form-input-text mt-3">
                                    <p class="label-bold text-italic" style="white-space: pre-line;">Vendor Currency</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="card card-tabs">
                <div class="card-header">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link text-secondary active" id="uploaded-invoice-preview-div-tab" data-toggle="pill" href="#uploaded-invoice-preview-div" role="tab" aria-controls="uploaded-invoice-preview-div" aria-selected="true">Uploaded Bill</a>
                        </li>
                        <span class="divider-vertical">|</span>
                        <?php
                        if ($vendorCode == "") {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link text-secondary" id="vendor-quick-registration-div-tab" data-toggle="pill" href="#vendor-quick-registration-div" role="tab" aria-controls="vendor-quick-registration-div" aria-selected="false">Quick Register</a>
                            </li>
                            <span class="divider-vertical">|</span>
                        <?php
                        }
                        if ($customerPurchaseOrder != "") {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link text-secondary" id="invoice-po-div-tab" data-toggle="pill" href="#po_details" role="tab" aria-controls="invoice-po-div" aria-selected="false">Matched with PO</a>
                            </li>
                            <span class="divider-vertical">|</span>
                        <?php
                        }
                        if ($vendorCode == "") {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link text-secondary" id="vendor_list_tab" data-toggle="pill" href="#vendor_list" role="tab" aria-controls="invoice-po-div" aria-selected="false">Vendor List</a>
                            </li>
                            <span class="divider-vertical">|</span>
                        <?php
                        }
                        ?>
                        <li class="nav-item">
                            <a class="nav-link text-secondary" id="invoice-po-list-tab" data-toggle="pill" href="#po_list" role="tab" aria-controls="invoice-po-list" aria-selected="false">Open PO List</a>
                        </li>
                        <span class="divider-vertical">|</span>
                        <li class="nav-item">
                            <a class="nav-link text-secondary" id="invoice-removed-item-list-tab" data-toggle="pill" href="#removed_items" role="tab" aria-controls="invoice-removed-item-list" aria-selected="false">Removed Items</a>
                        </li>

                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content tab-col" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade show active iframe-preview-btn" id="uploaded-invoice-preview-div" role="tabpanel" aria-labelledby="invoice-po-div-tab">
                            <iframe src='<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $invoiceFile ?>#view=fitH' id="grnInvoicePreviewIfram" width="100%" height="350"></iframe>
                            <div class="preview-btn-space mt-2">
                                <button type="button" class="btn btn-primary preview-btn" id="iframePreview" data-toggle="modal" data-target="#exampleModalCenter">
                                    Preview
                                </button>
                            </div>
                            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Invoice Preview</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body" style="height: 600px;">
                                            <div id="iframeHolder" class="iframeholder"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        if ($vendorCode == "") {
                        ?>
                            <div class="tab-pane fade quick-registration-vendor" id="vendor-quick-registration-div" role="tabpanel" aria-labelledby="invoice-po-list-tab">
                                <div class="container">
                                    <div class="row grn-vendor-details">
                                        <div class="display-flex alert-danger">
                                            <p class="text-bold" style="color: #ff0000;">Vendor not found!</p>
                                            <p><small class="text-danger">Please do quick add or go back and add vendor before continuing the GRN.</small></p>
                                        </div>
                                        <div class="display-flex">
                                            <p>Vendor Name :</p>&nbsp;
                                            <p><?= $vendorName ?></p>
                                        </div>
                                        <div class="display-flex">
                                            <p>Vendor GSTIN :</p>&nbsp;
                                            <p><?= $vendorGstin ?></p>
                                        </div>
                                        <div class="display-flex">
                                            <p>Vendor Address :</p>&nbsp;
                                            <p><?= $vendorAddress ?></p>
                                        </div>
                                        <div class="row">
                                            <a class="btn btn-sm btn-primary quick-add-vendor" data-toggle="modal" id="quickAddAjax" data-target="#dialogForVendorQuickAdd">Quick Add</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        if ($vendorCode == "") {
                            $sql_list = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE company_id='" . $company_id . "' AND company_branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' AND vendor_status='active' ORDER BY vendor_id DESC";
                            $qry_list = queryGet($sql_list, true);
                            $vendors_list = $qry_list["data"];
                        ?>
                            <div class="tab-pane fade quick-registration-vendor" id="vendor_list" role="tabpanel" aria-labelledby="invoice-po-list-tab">
                                <div class="container">
                                    <ul>
                                        <li></li>
                                        <li>
                                            <button id="refresh_po_list" class="btn btn-primary select-po float-right mt-3">Refresh</button>
                                        </li>
                                        <br>
                                        <br>

                                        <table class="table-sales-order grnTable table defaultDataTable grn-table">
                                            <thead>
                                                <tr>
                                                    <th>Sl No.</th>
                                                    <th>Vendor Code</th>
                                                    <th>Vendor Name</th>
                                                    <th>GSTIN</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $vendorSl = 1;
                                                foreach ($vendors_list as $eachvendor) {
                                                ?>
                                                    <tr>
                                                        <td><?= $vendorSl ?></td>
                                                        <td><?= $eachvendor["vendor_code"] ?></td>
                                                        <td><?= $eachvendor["trade_name"] ?></td>
                                                        <td><?= $eachvendor["vendor_gstin"] ?></td>
                                                        <td>
                                                            <a class="btn btn-sm btn-xs btn-secondary ml-2 vendorListClass" data-id="<?= $eachvendor["vendor_id"] ?>" data-name="<?= $eachvendor["trade_name"] ?>" data-code="<?= $eachvendor["vendor_code"] ?>" data-toggle="modal" data-target="#vendor_confirmation">Map Vendor</i></a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    $vendorSl++;
                                                }
                                                ?>



                                            </tbody>
                                        </table>

                                    </ul>
                                </div>
                            </div>
                        <?php
                        }
                        // if ($customerPurchaseOrder != "") {

                        $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9" AND `po_number`="' . $customerPurchaseOrder . '"');
                        $poDetails = $poDetailsObj["data"] ?? [];
                        $poId = $poDetails["po_id"] ?? 0;
                        $poItemsListObj = queryGet('SELECT * FROM `erp_branch_purchase_order_items` WHERE `po_id`=' . $poId, true);
                        $poItemsList = $poItemsListObj["data"] ?? [];

                        // foreach ($invoiceData["Items"] as $oneItemObj) {

                        //     $oneItemData = $oneItemObj;

                        //     $itemName = $oneItemData["Description"] ?? "";
                        //     $itemQty = $oneItemData["Quantity"] ?? "0";
                        //     $itemTax = $oneItemData["Tax"] ?? "0";
                        //     $itemUnitPrice = $oneItemData["UnitPrice"] ?? "0";
                        //     $itemTotalPrice = $oneItemData["Amount"] ?? "0";


                        // }

                        ?>
                        <div class="tab-pane fade quick-registration-vendor" id="po_details" role="tabpanel" aria-labelledby="invoice-po-div-tab">
                            <div class="container">
                                <ul>
                                    <li>PO Number: <?= $customerPurchaseOrder ?>
                                        <button id="refresh_po_match" type="button" class="btn btn-primary select-po float-right mt-3">Refresh</button>
                                    </li>
                                    <br>



                                    <table class="table-sales-order table defaultDataTable grn-table">
                                        <thead>
                                            <tr>
                                                <th>Sl No.</th>
                                                <th>Item Name</th>
                                                <th>Item Code</th>
                                                <th class="text-center" colspan=2>Quantity</th>
                                                <th class="text-center" colspan=2>Price</th>
                                                <th>Status</th>
                                            </tr>
                                            <tr>
                                                <th class="border-0"></th>
                                                <th class="border-0"></th>
                                                <th class="border-0"></th>
                                                <th class="border-left">PO</th>
                                                <th>Invoice</th>
                                                <th>PO</th>
                                                <th>Invoice</th>
                                                <th class="border-0"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $poItemSl = 1;

                                            $check_array = [];
                                            $check_array_1 = [];
                                            $po_id = [];
                                            $po_qty = [];
                                            foreach ($invoiceData["Items"] as $oneItemObj) {

                                                $itemName = $oneItemObj["Description"];

                                                if ($vendorCode != "") {
                                                    $itemCodeAndHsnObj = getItemCodeAndHsn($vendorCode, $itemName);
                                                    //  console($itemCodeAndHsnObj);
                                                    $internalItemId = $itemCodeAndHsnObj["itemId"];
                                                    $internalItemCode = $itemCodeAndHsnObj["itemCode"];
                                                    $internalItemUom = $itemCodeAndHsnObj["uom"];
                                                    $itemType = $itemCodeAndHsnObj["type"];
                                                    $itemHSN = $itemCodeAndHsnObj["itemHsn"];
                                                    $itemName = $itemCodeAndHsnObj["itemName"];
                                                }



                                            ?>

                                                <tr>
                                                    <td><?= $poItemSl ?></td>
                                                    <td>
                                                        <p style="white-space: pre-wrap;"><?= $itemName ?></p>
                                                    </td>
                                                    <td><?= $internalItemCode ?></td>
                                                    <?php
                                                    $quantity = "";
                                                    foreach ($poItemsList as $poItem) {
                                                        if ($poItem["itemName"] == $itemName) {
                                                            $quantity = $poItem["remainingQty"];
                                                            break;
                                                        } else {
                                                            $quantity = "";
                                                            continue;
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $quantity ?></td>
                                                    <td><?= $oneItemObj["Quantity"] ?></td>

                                                    <?php
                                                    $price = "";
                                                    foreach ($poItemsList as $poItem) {
                                                        if ($poItem["itemName"] == $itemName) {
                                                            $price = $poItem["unitPrice"];
                                                            break;
                                                        } else {
                                                            $price = "";
                                                            continue;
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $price ?></td>
                                                    <td><?= $oneItemObj["UnitPrice"] ?></td>
                                                    <?php
                                                    $match = "Mismatched";
                                                    $po_date = "";
                                                    foreach ($poItemsList as $poItem) {
                                                        if ($poItem["itemName"] == $itemName) {
                                                            if ($oneItemObj["UnitPrice"] == $poItem["unitPrice"]) {
                                                                if ($oneItemObj["Quantity"] == $poItem["remainingQty"]) {
                                                                    $po_date = $poItem["po_date"];
                                                                    $match = "Matched";
                                                                    array_push($check_array, "1");
                                                                    array_push($check_array_1, "1");
                                                                    break;
                                                                } elseif ($oneItemObj["Quantity"] < $poItem["remainingQty"]) {
                                                                    $po_updated_qty = $poItem["remainingQty"] - $oneItemObj["Quantity"];
                                                                    array_push($po_qty, $po_updated_qty);
                                                                    array_push($po_id, $poItem["po_item_id"]);
                                                                    array_push($check_array, "1");
                                                                    array_push($check_array_1, "0");
                                                                    continue;
                                                                } elseif ($oneItemObj["Quantity"] > $poItem["remainingQty"]) {
                                                                    array_push($check_array, "0");
                                                                    array_push($check_array_1, "0");
                                                                    continue;
                                                                }
                                                            } else {
                                                                // $match = "";
                                                                continue;
                                                            }
                                                        } else {
                                                            // $match = "";
                                                            continue;
                                                        }
                                                    }
                                                    ?>
                                                    <td><?= $match ?></td>
                                                </tr>


                                            <?php
                                                $poItemSl++;
                                                // }
                                                // $poItemName = $onePoItem["itemName"] ?? "";
                                                // $poUnitPrice = $onePoItem["unitPrice"] ?? "0";
                                                // $poQty = $onePoItem["qty"] ?? "0";


                                                // $nameMismatch = "mismatch";
                                                // $qtyMismatch = "mismatch";
                                                // foreach ($invoiceData["Items"] as $oneItemObj) {

                                                //     $oneItemData = $oneItemObj;
                                                //     $itemName = $oneItemData["Description"] ?? "";
                                                //     $itemQty = $oneItemData["Quantity"] ?? "0";
                                                //     $itemUnitPrice = $oneItemData["UnitPrice"] ?? "0";

                                                //     if ($itemName == $poItemName) {
                                                //         $nameMismatch = "match";

                                                //         if ($itemQty == $poQty) {
                                                //             $qtyMismatch = "match";
                                                //         }
                                                //     }
                                                // }


                                                // if ($nameMismatch == $qtyMismatch && $qtyMismatch == "match") {
                                                //     echo '<li>PO ' . $poItemSl . ' Item title ' . $nameMismatch . ' and Qty ' . $qtyMismatch . ': <i class="fa fa-check"></i></li>';
                                                // } else {
                                                //     echo '<li>PO ' . $poItemSl . ' Item title ' . $nameMismatch . ' and Qty ' . $qtyMismatch . ': <i class="fa fa-times"></i></li>';

                                                //     $isPoAndGrnInvoiceMatched = false;
                                                // }
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="po_date" name="po_date" value="<?= $po_date ?>">
                        <?php
                        // }
                        $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9" AND `vendor_id`="' . $vendorId . '"', true);
                        $poDetails = $poDetailsObj["data"] ?? [];

                        ?>
                        <div class="tab-pane fade quick-registration-vendor" id="po_list" role="tabpanel" aria-labelledby="invoice-po-list-tab">
                            <div class="container">
                                <ul>
                                    <li>
                                        <button id="refresh_po_list" type="button" class="btn btn-primary select-po float-right mt-3">Refresh</button>
                                    </li>
                                    <br>
                                    <br>

                                    <table class="table-sales-order table defaultDataTable grn-table">
                                        <thead>
                                            <tr>
                                                <th>Sl No.</th>
                                                <th>Vendor Name</th>
                                                <th>PO Number</th>
                                                <th>Total Items</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="open_po_list_table">
                                            <?php
                                            $poItemSl = 1;
                                            foreach ($poDetails as $poDetail) {
                                            ?>
                                                <tr>
                                                    <td><?= $poItemSl ?></td>
                                                    <td><?= $BranchPoObj->fetchVendorDetails($poDetail['vendor_id'])['data'][0]['trade_name'] ?></td>
                                                    <td><?= $poDetail["po_number"] ?></td>
                                                    <td><?= $poDetail["totalItems"] ?></td>
                                                    <td>
                                                        <a style="cursor:pointer" data-toggle="modal" data-target="#po_items" class="btn btn-sm btnModal" data-code="<?= $poDetail["po_number"] ?>" data-id="<?= $poDetail["po_id"] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                        <div class="modal fade invoice-iframe" id="po_items" tabindex="-1" aria-labelledby="exampleModal2Label" aria-hidden="true" data-bs-keyboard="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header py-1" style="background-color: #003060; color:white;">
                                                                        <h5 class="modal-title" style="color:white;">PO Items</h5>
                                                                        <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <table class="table-sales-order table defaultDataTable grn-table">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Item Code</th>
                                                                                    <th>Item Name</th>
                                                                                    <th>Ordered Quantity</th>
                                                                                    <th>Remaining Quantity</th>
                                                                                    <th>Date</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody id="po_table">




                                                                            </tbody>
                                                                        </table>
                                                                        <button id="select_po_button" class="btn btn-primary select-po float-right mt-3">Select PO</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!---modal end--->
                                                    </td>
                                                </tr>
                                            <?php
                                                $poItemSl++;
                                            }
                                            ?>



                                        </tbody>
                                    </table>

                                </ul>
                            </div>
                        </div>

                        <div class="tab-pane fade quick-registration-vendor" id="removed_items" role="tabpanel" aria-labelledby="invoice-removed-item-list">
                            <div class="container">
                                <ul>
                                    <!-- <li>
                                        <button id="refresh_po_list" type="button" class="btn btn-primary select-po float-right mt-3">Refresh</button>
                                    </li>
                                    <br>
                                    <br> -->

                                    <table class="table-sales-order table defaultDataTable grn-table">
                                        <thead>
                                            <tr>
                                                <th>Sl No.</th>
                                                <th>Item Name</th>
                                                <th>Item HSN</th>
                                                <th>Quantity</th>
                                                <th>Unit Price</th>
                                                <th>Basic Amount</th>
                                                <th>CGST</th>
                                                <th>SGST</th>
                                                <th>IGST</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $removedItemSl = 1;
                                            foreach ($removedItems as $removedItem) {
                                                $removeItemName = $removedItem["Description"] ?? "-";
                                                $removeItemHsn = $removedItem["ProductCode"] ?? "00000";
                                                $removeItemQuantity = $removedItem["Quantity"] ?? 0;
                                                $removeItemUnitPrice = $removedItem["UnitPrice"] ?? 0;
                                                $removeItemBasicPrice = $removeItemQuantity * $removeItemAmount;
                                                $removeItemTax = $removedItem["Tax"] ?? 0;
                                            ?>
                                                <tr>
                                                    <input type="hidden" name="" id="removedItemName_<?= $removedItemSl ?>" value="<?= $removeItemName ?>">
                                                    <input type="hidden" name="" id="removedItemHsn_<?= $removedItemSl ?>" value="<?= $removeItemHsn ?>">
                                                    <input type="hidden" name="" id="removedItemBasicPrice_<?= $removedItemSl ?>" value="<?= $removeItemBasicPrice ?>">
                                                    <input type="hidden" name="" id="removedItemTax_<?= $removedItemSl ?>" value="<?= $removeItemTax ?>">
                                                    <td><?= $removedItemSl ?></td>
                                                    <td><?= $removeItemName ?></td>
                                                    <td><?= $removeItemHsn ?></td>
                                                    <td><input type="number" step="any" name="" class="form-control" step="any" id="removedItemQuantity_<?= $removedItemSl ?>" value="<?= $removeItemQuantity ?>"></td>
                                                    <td><input type="number" step="any" name="" class="form-control" step="any" id="removedItemUnitPrice_<?= $removedItemSl ?>" value="<?= $removeItemUnitPrice ?>"></td>
                                                    <td><?= $removeItemBasicPrice ?></td>
                                                    <td>0</td>
                                                    <td>0</td>
                                                    <td><?= $removeItemTax ?></td>
                                                    <td><button type="button" id="addRemovedItems_<?= $removedItemSl ?>" class="btn btn-primary removedItemAdd">Add Items</button></td>
                                                </tr>
                                            <?php
                                                $removedItemSl++;
                                            }
                                            ?>



                                        </tbody>
                                    </table>

                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="col-lg-3 col-md-3 col-sm-3">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body" id="customerInfo">

                <div class="row grn-vendor-details">
                    <div class="col-lg-12 col-md-12 col-sm-12">

                        <div class="form-input">
                            <label>Functional Area</label>
                            <select name="funcArea" class="form-control">
                                <option value="">Functional Area</option>
                                <?php
                                $check_func = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id`=$location_id");
                                $funcs = $check_func['data']['companyFunctionalities'];
                                $func_ex = explode(",", $funcs);

                                foreach ($func_ex as $func) {
                                    $func_area = queryGet("SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`=$func", true);
                                    //console($func_area);

                                ?>

                                    <option value="<?= $func_area['data'][0]['functionalities_id'] ?>"><?= $func_area['data'][0]['functionalities_name'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div> -->


    <?php
    $goodsController = new GoodsController();
    $rmGoodsObj = $goodsController->getAllRMGoods();
    // console($rmGoodsObj);   
    ?>
    <style>
        /* Flex container to align items on the same line */
        .flex-container {
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
            /* Spacing between elements */
        }




        /* Dropdown styling */
        .form-input {
            margin: 0;
            /* Remove default margin */
            flex-grow: 1;
            /* Allow the dropdown to expand */
        }

        .select2-container {
            width: 100% !important;
            /* Ensure Select2 spans full width */
        }
    </style>
    <div class="flex-container">
        <!-- Hamburger Button -->
        <div class="hamburger quickadd-hamburger">
            <div class="wrapper-action">
                <i class="fa fa-plus"></i>
            </div>
        </div>

        <!-- Dropdown Section -->
        <div class="form-input my-2" id="itemSelect" style="display: none;">
            <select class="select2 form-control" name="" id="itemsDropDown" required>
                <option value="0" data-hsncode="" data-itemtitle="">Select Item</option>
                <?php
                if ($rmGoodsObj["status"] == "success") {
                    foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                        $oneRmGoods_item_id = $oneRmGoods["itemId"];
                        $oneRmGoods_summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$oneRmGoods_item_id' AND `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'", false);
                ?>
                        <option value="<?= $oneRmGoods["itemCode"]; ?>"
                            data-itemcode="<?= $oneRmGoods["itemCode"]; ?>"
                            data-itemqty="<?= $oneRmGoods_summary["data"]["itemTotalQty"] ?? 0 ?>"
                            data-itemprice="<?= $oneRmGoods_summary["data"]["movingWeightedPrice"] ?? 0 ?>"
                            data-qualityenable="<?= $oneRmGoods_summary["data"]["quality_enabled"] ?>"
                            data-default="<?= $oneRmGoods_summary["data"]["default_storage_location"] ?>"
                            data-qalocation="<?= $oneRmGoods_summary["data"]["qa_storage_location"] ?>"
                            data-taxpercent="<?= $oneRmGoods["taxPercentage"]; ?>"
                            data-name="<?= $oneRmGoods["itemName"]; ?>"
                            data-uom="<?= $oneRmGoods["uomName"]; ?>"
                            data-itemid="<?= $oneRmGoods["itemId"]; ?>"
                            data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>"
                            data-hsnPercent="<?= $oneRmGoods["taxPercentage"]; ?>"
                            data-goods="<?= $oneRmGoods["goodsType"]; ?>"
                            data-uomId="<?= $oneRmGoods["uomId"]; ?>"
                            data-tds="<?= $oneRmGoods["tds"]; ?>"
                            data-itemtitle="<?= $oneRmGoods["itemName"]; ?>">
                            <?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["taxPercentage"] . " %"; ?>
                        </option>
                <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <div class="grn-table pending-grn-view">
        <table class="table-sales-order table defaultDataTable grn-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Sl No.</th>
                    <th>Item Name</th>
                    <th>Internal Code</th>
                    <th>Item HSN</th>
                    <th>St. Loc. / Cost Center</th>
                    <th>Derived Qty
                        <button type="button" class="btn" data-toggle="tooltip" data-placement="top" title="<?= $data['remark'] ?>">
                            <i class="fa fa-info font-bold"></i>
                        </button>
                    </th>
                    <th>Invoiced Qty</th>
                    <th>Received Qty</th>
                    <th>Unit Price</th>
                    <th>Basic Amount</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>TDS %</th>
                    <!-- <th>Total Amount</th> -->
                    <th>Delete Item</th>
                </tr>
            </thead>
            <tbody id="itemsTable">
                <?php
                $sl = 0;
                $totalSubtotal = 0;
                $GrandtoalTotal = 0;
                $grandcgst = 0;
                $grandsgst = 0;
                $grandigst = 0;
                $totalTdsValue = 0;
                $totalTaxPercent = 0;
                $resArray = [];
                foreach ($invoiceData["Items"] as $oneItemObj) {

                    $oneItemData = $oneItemObj;

                    $itemHSN = "";
                    $tax = 0;
                    $goodsType = "";
                    $itemName = $oneItemData["Description"] ?? "";
                    $grnItemName = $oneItemData["Description"] ?? "";
                    $itemQty = $oneItemData["Quantity"] ?? "0";
                    $itemTax = $oneItemData["Tax"] ?? "0";
                    $itemUnitPrice = $oneItemData["UnitPrice"] ?? "0";
                    $Total = $oneItemData["Amount"] ?? "0";
                    $invoice_units = $oneItemData["Unit"] ?? "";
                    $cgst = $oneItemData["cgstTax"] == "" ? 0 : $oneItemData["cgstTax"];
                    $sgst = $oneItemData["sgstTax"] == "" ? 0 : $oneItemData["sgstTax"];
                    $igst = $oneItemData["igstTax"] == "" ? 0 : $oneItemData["igstTax"];

                    // if ($vendorGstinStateCode == $customerGstinStateCode) {
                    //     $itemTotalPrice = ($itemUnitPrice * $itemQty) + $cgst + $sgst;
                    // } else {
                    //     $itemTotalPrice = ($itemUnitPrice * $itemQty) + $igst;
                    // }

                    $baseAmt = ($itemUnitPrice * $itemQty);


                    $internalItemId = "";
                    $internalItemCode = "";
                    $internalItemHsn = "";
                    $tds = 0;
                    if ($vendorCode != "") {
                        $itemCodeAndHsnObj = getItemCodeAndHsn($vendorCode, $itemName, $baseAmt);
                        //  console($itemCodeAndHsnObj);
                        $internalItemId = $itemCodeAndHsnObj["itemId"];
                        $internalItemCode = $itemCodeAndHsnObj["itemCode"];
                        if (!empty($internalItemId)) {
                            $checkitem = checkItemImpactById($internalItemId);
                            if ($checkitem['status'] != "success") {
                                $resArray[] = ['itemCode' => $internalItemCode, 'message' => $checkitem["message"]];
                                continue;
                            }
                        }
                        $internalItemUom = $itemCodeAndHsnObj["uom"];
                        $internalItemuom_id = $itemCodeAndHsnObj["uom_id"];
                        $itemType = $itemCodeAndHsnObj["type"];
                        $itemHSN = $itemCodeAndHsnObj["itemHsn"];
                        $itemName = $itemCodeAndHsnObj["itemName"];
                        $tax = $itemCodeAndHsnObj["tax"];
                        $tds = $itemCodeAndHsnObj["tds"] ?? 0;
                        $goodsType = $itemCodeAndHsnObj["goodsType"];
                        $slab = $itemCodeAndHsnObj["slab"];
                    }
                    // $itemHSN = $oneItemData["ProductCode"] ?? $itemHSN;

                    //Check for mapped Item
                    if ($internalItemCode == "") {
                        $itemHSN = $oneItemData["ProductCode"];
                        $itemName = $oneItemData["Description"] ?? "";
                    }

                    $basic_amt = ($itemUnitPrice * $itemQty);

                    $tds_value = $basic_amt * ($tds / 100);


                    if (strtolower($itemName) == "cgst" || strtolower($itemName) == "sgst") {
                        continue;
                    }
                    if ($itemName == "") {
                        $itemName = "Item Name or Description not identified -" . uniqid();
                    }
                    $sl += 1;
                ?>

                    <tr id="grnItemRowTr_<?= $sl ?>">
                        <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                        <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                        <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                        <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                        <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemQty]" value="<?= $itemQty ?>" />
                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                        <!-- <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                        <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                        <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSValue]" value="<?= $tds_value ?>" />
                        <input type="hidden" class="ItemInvoiceTDSSlab" id="ItemInvoiceTDSSlab_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSSlab]" value='<?= json_encode($slab) ?>' />

                        <?php
                        if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                        ?>
                            <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceGoodsType]" value="goods" />
                        <?php
                        } else {
                        ?>
                            <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
                        <?php
                        }
                        ?>


                        <?php
                        $subtotal = ($itemUnitPrice * $itemQty);

                        $after_tax_apply = $subtotal * $tax / 100;

                        $tax_added_value = $subtotal + ($subtotal * $tax / 100);

                        ?>
                        <input type="hidden" value="<?= $tax_added_value ?>" id="grnItemInternalTaxValue_<?= $sl ?>" class="form-control text-xs itemInternalTaxValue" step="any">
                        <?php

                        $totalTaxPercent += $tax_added_value;


                        if ($vendorGstinStateCode == $customerGstinStateCode) {
                            $cgst = $after_tax_apply / 2;
                            $sgst = $after_tax_apply / 2;
                            $igst = 0;
                        } else {
                            $cgst = 0;
                            $sgst = 0;
                            $igst = $after_tax_apply;
                        }

                        $itemTotalPrice = ($basic_amt) + $cgst + $sgst + $igst - $tds_value;

                        ?>


                        <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPrice" id="ItemInvoiceTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemTotalPrice]" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPriceStatic" id="ItemInvoiceTotalPriceStatic_<?= $sl ?>" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceCGSTClass" id="ItemInvoiceCGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCGST]" value="<?= $cgst ?>" />
                        <input type="hidden" id="ItemInvoiceCGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCGSTNew]" value="<?= $cgst ?>" />
                        <input type="hidden" class="ItemInvoiceSGSTClass" id="ItemInvoiceSGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemSGST]" value="<?= $sgst ?>" />
                        <input type="hidden" id="ItemInvoiceSGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemSGSTNew]" value="<?= $sgst ?>" />
                        <input type="hidden" class="ItemInvoiceIGSTClass" id="ItemInvoiceIGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemIGST]" value="<?= $igst ?>" />
                        <input type="hidden" id="ItemInvoiceIGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemIGSTNew]" value="<?= $igst ?>" />
                        <input type="hidden" id="ItemInvoiceUnits_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUnits]" value="<?= $invoice_units ?>" />
                        <input type="hidden" id="ItemInvoiceUOM_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUOM]" value="<?= $internalItemUom ?>" />
                        <input type="hidden" id="ItemInvoiceUOMID_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUOMID]" value="<?= $internalItemuom_id ?>" />

                        <input type="hidden" id="itemtax_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemtax]" value="<?= $tax ?>" />

                        <td>
                            <?php if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                            ?>
                                <input type="checkbox" id="check_box_<?= $sl ?>" name="check_box" class="checkbx" value="<?= $sl ?>">
                        </td>
                    <?php } else { ?>
                        <input type="checkbox" id="check_box_<?= $sl ?>" style="display:none" name="check_box" class="checkbx" value="<?= $sl ?>"></td>

                    <?php }
                    ?>
                    <td><?= $sl ?></td>
                    <td id="grnItemNameTdSpan_<?= $sl ?>"><?= $itemName ?></td>
                    <td class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                        <?php
                        if ($postStatus != 0) {
                            echo $internalItemCode;
                        } else {
                            if ($internalItemCode == "") {
                                echo '<a class="btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCode" data-allocate="0" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCode">Map Code</i></a>';
                            } else {
                                echo $internalItemCode;
                                if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {

                                    echo '<a class="btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCodeChange"><i class="fas fa-pencil-alt"></i></a>';
                                } else {
                                    echo '<a class="btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCodeChange"><i class="fas fa-pencil-alt"></i></a>';
                                    echo '<a class="btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCode" data-itemrownum="' . $sl . '"  data-allocate="1" data-toggle="modal" data-target="#mapInvoiceItemCode">Allocate Cost</a>';
                                }
                            }
                        }
                        ?>
                    </td>
                    <td class="grnItemHSNTdSpan" id="grnItemHSNTdSpan_<?= $sl ?>"><?= $itemHSN ?></td>
                    <td id="grnItemStrgLocTdSpan_<?= $sl ?>" class="storageSelect">

                        <?php
                        if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                            //Get Summary
                            $itemId = $internalItemId;
                            $summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$itemId' AND `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'", false);

                        ?>
                            <select class="form-control text-xs storageLocationSelect" id="itemStorageLocationId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" required>
                                <option value="">Select storage location</option>
                                <?php

                                // getqaListForGrnObj
                                if ($summary["data"]["quality_enabled"] == '1') {

                                    $st_loc_id = $summary["data"]["qa_storage_location"];

                                    $rackDetailsObj = queryGet("SELECT rack_id FROM `erp_rack` WHERE storage_location_id = '" . $st_loc_id . "'", true);
                                    $options = "";
                                    // console($rackDetailsObj);
                                    foreach ($rackDetailsObj["data"] as $rackDetail) {
                                        $rack_id = $rackDetail["rack_id"];
                                        if (is_null($rack_id) || $rack_id == "")
                                            continue;
                                        $layerDetailsObj = queryGet("SELECT * FROM `erp_layer` WHERE rack_id = '" . $rack_id . "'", true);
                                        // console($rack_id);
                                        foreach ($layerDetailsObj["data"] as $layerDetail) {
                                            $layer_id = $layerDetail["layer_id"];
                                            if (is_null($layer_id) || $layer_id == "")
                                                continue;
                                            $binDetailsObj = queryGet("SELECT * FROM `erp_storage_bin` WHERE layer_id = '" . $layer_id . "'", true);
                                            // console($binDetailsObj);
                                            foreach ($binDetailsObj["data"] as $binDetail) {
                                                $bin_id = $binDetail["bin_id"];
                                                if (is_null($bin_id) || $bin_id == "")
                                                    continue;
                                                $bin_name = $binDetail["bin_name"];
                                                $options .=  "<option value='" . $bin_id . "'>" . $bin_name . "</option>";
                                            }
                                        }
                                    }


                                    foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                        if ($oneRmStorageLocation["storage_location_id"] == $summary["data"]["qa_storage_location"]) {
                                            echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                        } else {
                                            echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                        }
                                    }
                                } else {

                                    $st_loc_id = $summary["data"]["default_storage_location"];

                                    $rackDetailsObj = queryGet("SELECT rack_id FROM `erp_rack` WHERE storage_location_id = '" . $st_loc_id . "'", true);
                                    $options = "";
                                    // console($rackDetailsObj);
                                    foreach ($rackDetailsObj["data"] as $rackDetail) {
                                        $rack_id = $rackDetail["rack_id"];
                                        if (is_null($rack_id) || $rack_id == "")
                                            continue;
                                        $layerDetailsObj = queryGet("SELECT * FROM `erp_layer` WHERE rack_id = '" . $rack_id . "'", true);
                                        // console($rack_id);
                                        foreach ($layerDetailsObj["data"] as $layerDetail) {
                                            $layer_id = $layerDetail["layer_id"];
                                            if (is_null($layer_id) || $layer_id == "")
                                                continue;
                                            $binDetailsObj = queryGet("SELECT * FROM `erp_storage_bin` WHERE layer_id = '" . $layer_id . "'", true);
                                            // console($binDetailsObj);
                                            foreach ($binDetailsObj["data"] as $binDetail) {
                                                $bin_id = $binDetail["bin_id"];
                                                if (is_null($bin_id) || $bin_id == "")
                                                    continue;
                                                $bin_name = $binDetail["bin_name"];
                                                $options .=  "<option value='" . $bin_id . "'>" . $bin_name . "</option>";
                                            }
                                        }
                                    }

                                    foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                        if ($oneRmStorageLocation["storage_location_id"] == $summary["data"]["default_storage_location"]) {
                                            echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                        } else {
                                            echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                        }
                                    }
                                }

                                ?>
                            </select>
                            <input type="hidden" value="<?= $options ?>" id="grnItemAllBins_<?= $sl ?>" class="form-control">

                        <?php
                        } else {
                        ?>
                            <select class="form-control text-xs" id="itemStorageLocationId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" required>
                                <option value="">Select Cost Center</option>
                                <?php
                                foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                    echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>


                    </td>
                    <td id="grnItemStkQtyTdSpan_<?= $sl ?>">
                        <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                            <input type="number" step="any" id="itemStockQty_<?= $sl ?>" value="<?= $itemQty ?>" class="form-control text-xs w-50" name="grnItemList[<?= $sl ?>][itemStockQty]">
                            <p class="text-xs" id="grnItemUOM_<?= $sl ?>"><?= $internalItemUom ?></p>
                        </div>
                    </td>
                    <td id="grnItemInvoiceQtyTdSpan_<?= $sl ?>"><?= $itemQty . " " . $invoice_units ?> </td>
                    <td>
                        <div class="form-input">
                            <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemReceivedQty]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs received_quantity" required>
                        </div>
                    </td>
                    <!-- <td class="text-right" id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= number_format($itemUnitPrice, 2) ?></td> -->
                    <td>
                        <div class="input-group input-group-sm m-0" style="flex-wrap: nowrap;">
                            <div class="input-group-prepend">
                                <span class="input-group-text spanInvoiceCurrencyName" id="spanInvoiceCurrencyName_<?= $sl ?>"><?= $comp_currency ?></span>
                            </div>
                            <input type="number" name="grnItemList[<?= $sl ?>][itemUnitPriceOtherCurrency]" value="<?= number_format($itemUnitPrice, 2, '.', '') ?>" id="grnItemUnitPriceTdInput_<?= $sl ?>" class="form-control text-xs itemUnitPrice w-auto" step="any" required>
                            <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPricehidden]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceTdInputhidden_<?= $sl ?>" class="form-control text-xs itemUnitPricehidden">
                            <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceInrhidden_<?= $sl ?>" class="form-control text-xs grnItemUnitPriceInrhidden">
                        </div>
                        <span class="text-small spanUnitPriceINR" id="spanUnitPriceINR_<?= $sl ?>"></span>
                    </td>
                    <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= $comp_currency . ": " . number_format($itemUnitPrice * $itemQty, 2) ?></td>
                    <td class="text-right" id="grnItemInvoiceCGSTTdSpan_<?= $sl ?>"><?= $comp_currency . ": " . number_format($cgst, 2) ?></td>
                    <td class="text-right" id="grnItemInvoiceSGSTTdSpan_<?= $sl ?>"><?= $comp_currency . ": " . number_format($sgst, 2) ?></td>
                    <td class="text-right" id="grnItemInvoiceIGSTTdSpan_<?= $sl ?>"><?= $comp_currency . ": " . number_format($igst, 2) ?></td>

                    <?php
                    if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                    ?>
                        <td>
                            <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemTds]" value="<?= $tds ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-center p-0 px-2 text-xs itemTds border-0" style="width: 30px !important;" required>
                                <p class="text-xs">%</p>
                            </div>
                        </td>
                    <?php
                    } else {
                    ?>
                        <td>
                            <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemTds]" value="<?= $tds ?? 0 ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-center p-0 px-2 text-xs itemTds border-0" style="width: 30px !important;" required>
                                <p class="text-xs">%</p>
                            </div>
                        </td>
                    <?php
                    }
                    ?>
                    <input type="hidden" value="<?= $tax ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">
                    <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </span>
                    <td class="text-right" id="grnItemDeleteTdSpan_<?= $sl ?>">
                        <div id="grnItemSettingsTdSpan_<?= $sl ?>">
                            <?php
                            if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                            ?>
                                <button type="button" class="btn-view btn btn-primary delShedulingBtn" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $sl ?>">
                                    <i id="statusItemBtn_<?= $internalItemId ?>" class="statusItemBtn fa fa-cog"></i>
                                </button>
                            <?php
                            }
                            ?>
                        </div>
                        <button title="Delete Item" type="button" id="grnItemDeleteButton_<?= $sl ?>" class="btn btn-sm remove_row" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button>

                        <div class="modal modal-left left-item-modal fade deliveryScheduleModal discountViewModal discountViewModal_<?= $sl ?>" id="deliveryScheduleModal_<?= $sl ?>" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="left_modal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?= $itemName ?></h5>
                                    </div>
                                    <div class="modal-body multiBatchModelViewBody_<?= $sl ?>">
                                        <div class="qty-title d-flex justify-content-between mb-1 mb-3 pb-2 border-bottom">
                                            <h6 class="modal-title text-xs font-bold">Total Quantity: <span class="totalItemAmountModal" id="totalItemAmountModal_<?= $sl ?>"><?= $itemQty ?></span></h6>
                                            <div class="check-box text-left font-bold text-xs">
                                                <input type="checkbox" class="grnEnableCheckBxClass" value="1" id="grnEnableCheckBx_<?= $sl ?>" name="grnItemList[<?= $sl ?>][activateBatch]"> Enable check box to insert the manual Batch
                                                <input type="hidden" name="" id="grnStoreId_<?= $sl ?>" value="<?= $summary["data"]["default_storage_location"] ?? "" ?>">
                                            </div>
                                        </div>
                                        <p class="note mb-3">
                                            By default the generated doc (GRN000927) number will be the batch number
                                        </p>
                                        <div class="modal-add-row" id="modal-add-row_<?= $sl ?>">
                                            <!-- <div class="row manual-grn-plus-modal modal-cog-right">
                                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                                        <div class="form-input">
                                                            <label>Batch Number</label>
                                                            <input type="text" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][multipleBatch][1][batchNumber]" class="form-control multiDeliveryDate" id="multiDeliveryDate_<?= $sl ?>" placeholder="Batch Number">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                                        <div class="form-input">
                                                            <label>Quantity</label>
                                                            <input type="text" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][multipleBatch][1][qty]" class="form-control multiQuantity" data-itemid="<?= $sl ?>" id="multiQuantity_<?= $sl ?>" placeholder="quantity" value="<?= $itemQty ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                                        <a style="cursor: pointer" class="btn btn-primary addQtyBtn" id="addQtyBtn_<?= $sl ?>_<?= $vendor_id ?>">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                    </div>
                                                </div> -->
                                        </div>
                                        <?php
                                        $defaultMultiBatchRows[] = [
                                            "vendorId" => $vendor_id,
                                            "sl" => $sl,
                                            "qty" => $itemQty
                                        ];
                                        ?>
                                        <!-- <script>
                                                $(document).ready(function() {
                                                    console.log("Calling addGrnItemMultipleBatch(batchVendorId, id) to add the default row");
                                                    addGrnItemMultipleBatch(`<?= $vendor_id ?>`, `<?= $sl ?>`);

                                                });
                                            </script> -->
                                    </div>
                                    <div class="modal-footer modal-footer-fixed">
                                        <button type="button" class="btn btn-primary w-100" data-dismiss="modal" id="saveAndClose_<?= $sl ?>">Save & Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    </tr>
                    <tr class="span-error-tr">
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent" colspan="3">
                            <span class="error text-warning" id='grnItemMessage_<?= $sl ?>'>
                                <?php if (strtolower($invoice_units) != strtolower($internalItemUom)) echo "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>stock keeping unit and invoice driven unit is different"; ?>
                            </span>
                        </td>
                        <td class="bg-transparent"></td>
                        <td colspan="3" class="bg-transparent">
                            <?php // if ((float)$itemTotalPrice != (float)$Total) {echo "<span class='error calculate-error'>".$itemTotalPrice." is the difference</span>"; } 
                            ?>
                        </td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>

                    </tr>
                <?php
                    $totalSubtotal += ($itemUnitPrice * $itemQty);
                    $GrandtoalTotal += $itemTotalPrice;
                    $grandcgst += $cgst;
                    $grandsgst += $sgst;
                    $grandigst += $igst;
                    $totalTdsValue += $tds_value;
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="total-amount-grn-table">

        <div class="card">
            <div class="card-body p-0">
                <table>
                    <tbody>
                        <tr class="itemTotals">
                            <td colspan="10" style="background: none;">Sub Total</td>
                            <td class="text-right" id="grandSubTotalTd" style="background: none;"><?= $comp_currency . ": " . number_format($totalSubtotal, 2) ?></td>
                        </tr>

                        <?php

                        if (($grandcgst != 0 && $grandsgst != 0) || $grandigst != 0) {

                            $totalCGST = $grandcgst;
                            $totalSGST = $grandsgst;
                            $totalIGST = $grandigst;
                            if ($vendorGstinStateCode == $customerGstinStateCode) {
                                $toalTotal = $totalSubtotal + $totalCGST + $totalSGST - $totalTdsValue;
                            } else {
                                $toalTotal = $totalSubtotal + $totalIGST - $totalTdsValue;
                            }
                        } else {
                            if ($totalIGST == 0) {
                                $toalTotal = $totalSubtotal + $totalCGST + $totalSGST + $totalIGST - $totalTdsValue;
                            } else {
                                $totalCGST = $totalIGST / 2;
                                $totalSGST = $totalIGST / 2;
                                $totalIGST = 0;
                                $toalTotal = $totalSubtotal + $totalCGST + $totalSGST + $totalIGST - $totalTdsValue;
                            }
                        }

                        $totalTaxAdd = $totalCGST + $totalSGST + $totalIGST;
                        $totalTaxPercentage = ($totalSubtotal / $totalTaxAdd) * 100;

                        // console($vendorGstinStateCode."...........".$customerGstinStateCode);

                        if ($vendorGstinStateCode == $customerGstinStateCode) {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="10" style="background: none;">Total CGST</td>
                                <td class="text-right" style="background: none;" id="grandCgstTd"><?= $comp_currency . ": " . number_format($totalCGST, 2) ?></td>
                            </tr>
                            <tr class="itemTotals">
                                <td colspan="10" style="background: none;">Total SGST</td>
                                <td class="text-right" style="background: none;" id="grandSgstTd"><?= $comp_currency . ": " . number_format($totalSGST, 2) ?></td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="10" style="background: none;">Total IGST</td>
                                <td class="text-right" style="background: none;" id="grandIgstTd"><?= $comp_currency . ": " . number_format($totalIGST, 2) ?></td>
                            </tr>
                        <?php
                        }

                        ?>
                        <tr class="itemTotals">
                            <td colspan="10" style="background: none;">Total TDS</td>
                            <td class="text-right" id="grandTds" style="background: none;"><?= $comp_currency . ": -" . number_format($totalTdsValue, 2) ?></td>
                        </tr>
                        <tr class="itemTotals" id="roundoff" style="display: none;">
                            <td colspan="10" style="background: none;">Round Off</td>
                            <td class="text-right" id="round_off" style="background: none;"> <span><?= $comp_currency ?>:</span> <span id="roundoff_span">0.00</span> </td>
                            <input type="hidden" name="roundvalue" id="roundvalue" value="0.0">
                        </tr>
                        <tr class="itemTotals">
                            <input type="hidden" id="totalCGST" name="totalInvoiceCGST" value="<?= $totalCGST ?>">
                            <input type="hidden" id="totalSGST" name="totalInvoiceSGST" value="<?= $totalSGST ?>">
                            <input type="hidden" id="totalIGST" name="totalInvoiceIGST" value="<?= $totalIGST ?>">
                            <input type="hidden" id="totalTDS" name="totalInvoiceTDS" value="<?= $totalTdsValue ?>">
                            <input type="hidden" id="grandSubTotal" name="totalInvoiceSubTotal" value="<?= $totalSubtotal ?>">
                            <input type="hidden" id="grandTotal" name="totalInvoiceTotal" value="<?= $toalTotal ?>">
                            <td colspan="10" class="font-bold" style="background: none; border: 0;">Total Amount</td>
                            <td class="text-right font-bold" id="grandTotalTd" style="background: none; border: 0;"><?= $comp_currency . ": " . number_format($toalTotal, 2) ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <?php
        if ($postStatus == 0) {
        ?>
            <div id="internalotaxwarning">
                <span class="error text-warning text-xs">
                    <?php

                    if ($totalTaxPercent != $toalTotal) {
                    ?>
                        <i class='fa fa-exclamation-triangle' aria-hidden='true'></i>OCR Tax Percentage not matched with internal tax percentage
                    <?php
                    }

                    ?>
                </span>
            </div>
            <input type="hidden" name="addNewGrnFormSubmitBtn" value="formSubmit">
            <?php
            if ($button_enable == "true") {
                if ($vendor_status == 'inactive') {
            ?>
                    <button type="submit" id="addNewGrnFormSubmitBtn" value="Submit GRN" disabled class="btn btn-primary float-right mt-3 mb-3">Submit GRN</button>
                <?php
                } else {
                ?>
                    <button type="submit" id="addNewGrnFormSubmitBtn" value="Submit GRN" class="btn btn-primary float-right mt-3 mb-3">Submit GRN</button>
                <?php
                }
            } else {
                ?>
                <button type="submit" id="addNewGrnFormSubmitBtn" value="Submit GRN" disabled class="btn btn-primary float-right mt-3 mb-3">Submit GRN</button>
            <?php
            }
            ?>
        <?php
        }
        ?>
    </div>






    </div>


</form>


<!-- modal dialogForVendorQuickAdd -->
<div class="modal" id="dialogForVendorQuickAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-1" style="background-color: #003060; color:white;">
                <h5 class="modal-title">Vendor Quick Add</h5>
                <button type="button" id="dialogForVendorQuickAddCloseBtn" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <span class="text-muted">The Vendor is being added with the basic details. Account, POC, Other details need to be added latter.</span>
                <form action="" method="post" id="vendorQuickAddForm">

                </form>
            </div>
        </div>
    </div>
</div>
<!-- modal dialogForVendorQuickAdd end -->

<!-- modal -->
<div class="modal" id="mapInvoiceItemCode">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-1" style="background-color: #003060; color:white;">
                <h5 class="modal-title text-sm py-2 text-white" id="mapheader">Map Item</h5>
                <h5 class="modal-title text-sm py-2 text-white" id="allocateheader">Allocate Cost</h5>
                <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="select-type my-2">
                    <div class="map-item">
                        <input type="radio" id="map_radio" checked name="distribute" value="map">
                        <label for="html" id="mapitem">Map Item</label>
                    </div>
                    <div class="allocate-cost">
                        <input type="radio" id="allocate_radio" name="distribute" value="allocate">
                        <label for="css">Allocate Cost</label>
                    </div>
                </div>

                <form action="" method="post" id="mapInvoiceItemCodeForm">
                    <div class="form-input my-2">
                        <input type="hidden" name="modalItemSlNo" id="modalItemSlNo" value="0">
                        <input type="hidden" name="modalItemAmt" id="modalItemAmt" value="0">
                        <label>Item Description</label>
                        <textarea name="modalItemDescription" id="modalItemDescription" cols="1" rows="3" class="form-control" readonly></textarea>
                    </div>
                    <div class="form-input my-2">
                        <input type="hidden" name="modalItemQtyMap" id="modalItemQtyMap">
                        <label>Select Item Code</label>
                        <select class="form-control" name="modalItemCode" id="modalItemCodeDropDown" required>
                            <?php
                            $goodsController = new GoodsController();
                            $rmGoodsObj = $goodsController->getAllRMGoods();
                            if ($rmGoodsObj["status"] == "success") {
                                echo '<option value="" data-hsncode="" data-itemtitle="">Select Item</option>';
                                foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                                    $oneRmGoods_item_id = $oneRmGoods["itemId"];
                                    $oneRmGoods_summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$oneRmGoods_item_id' AND `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'", false);
                            ?>
                                    <option value="<?= $oneRmGoods["itemCode"]; ?>" data-qualityenable="<?= $oneRmGoods_summary["data"]["quality_enabled"] ?>" data-default="<?= $oneRmGoods_summary["data"]["default_storage_location"] ?>" data-qalocation="<?= $oneRmGoods_summary["data"]["qa_storage_location"] ?>" data-taxpercent="<?= $oneRmGoods["taxPercentage"]; ?>" data-tds="<?= $oneRmGoods["tds"]; ?>" data-goods="<?= $oneRmGoods['goodsType'] ?>" data-uomId="<?= $oneRmGoods["uomId"]; ?>" data-name="<?= $oneRmGoods["itemName"]; ?>" data-uom="<?= $oneRmGoods["uomName"]; ?>" data-itemid="<?= $oneRmGoods["itemId"]; ?>" data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>" data-hsnPercent="<?= $oneRmGoods['taxPercentage'] ?>" data-itemtitle="<?= $oneRmGoods["itemName"]; ?>"><?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["taxPercentage"] . " %"; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="mapItemCodeFormSubmitBtn" class="btn btn-primary btnstyle my-2">Map Code</button>
                </form>
                <div class="allocateclass" id="allocateclass">
                    <table id="alloc">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Adjust Unit Price</th>
                                <th>Basic Price</th>
                                <th>Adjusted Basic Price</th>
                            </tr>
                        </thead>
                        <input type="hidden" name="v_amount" id="v_amount">
                        <input type="hidden" name="vamount" id="vamount">
                        <tbody>

                        </tbody>
                    </table>



                    <p style="display: inline;">Allocate Amount :<span id="alc"></span></p> <br>

                    <p style="display: inline; color:red"> Variance Amount :<span id="variance"></span></p>
                </div>

                <button type="button" id="allocate_id" style="float: right;" name="" class="btn btn-primary btnstyle mt-2">Allocate Cost</button>
            </div>
        </div>
    </div>
</div>






<!-- modal end -->

<!-- modal -->
<div class="modal" id="mapInvoiceItemCodeChange">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-1" style="background-color: #003060; color:white;">
                <h5 class="modal-title text-sm py-2 text-white">Change Code</h5>
                <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <form action="" method="post" id="changeInvoiceItemCodeForm">
                    <div class="form-input my-2">
                        <input type="hidden" name="modalItemSlNoChange" id="modalItemSlNoChange" value="0">
                        <input type="hidden" name="modalItemAmtChange" id="modalItemAmtChange" value="0">
                        <label>Item Description</label>
                        <textarea name="modalItemDescriptionChange" id="modalItemDescriptionChange" cols="1" rows="3" class="form-control" readonly></textarea>
                    </div>
                    <div class="form-input my-2">
                        <input type="hidden" name="modalItemQtyChange" id="modalItemQtyChange">
                        <label>Select Item Code</label>
                        <select class=" select2 form-control" name="modalItemCodeChange" id="modalItemCodeDropDownChange" required>
                            <?php
                            $goodsController = new GoodsController();
                            $rmGoodsObj = $goodsController->getAllRMGoods();
                            if ($rmGoodsObj["status"] == "success") {
                                echo '<option value="" data-hsncode="" data-itemtitle="">Select Item</option>';
                                foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                                    $oneRmGoods_item_id = $oneRmGoods["itemId"];
                                    $oneRmGoods_summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$oneRmGoods_item_id' AND `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'", false);
                            ?>
                                    <option value="<?= $oneRmGoods["itemCode"]; ?>" data-qualityenable="<?= $oneRmGoods_summary["data"]["quality_enabled"] ?>" data-default="<?= $oneRmGoods_summary["data"]["default_storage_location"] ?>" data-qalocation="<?= $oneRmGoods_summary["data"]["qa_storage_location"] ?>" data-taxpercent="<?= $oneRmGoods["taxPercentage"]; ?>" data-name="<?= $oneRmGoods["itemName"]; ?>" data-uom="<?= $oneRmGoods["uomName"]; ?>" data-itemid="<?= $oneRmGoods["itemId"]; ?>" data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>" data-hsnPercent="<?= $oneRmGoods["taxPercentage"]; ?>" data-goods="<?= $oneRmGoods["goodsType"]; ?>" data-uomId="<?= $oneRmGoods["uomId"]; ?>" data-tds="<?= $oneRmGoods["tds"]; ?>" data-itemtitle="<?= $oneRmGoods["itemName"]; ?>"><?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["taxPercentage"] . " %"; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="mapItemCodeFormSubmitBtn" class="btn btn-primary btnstyle mt-2">Change Code</button>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- modal end -->


<div class="modal invoice-iframe" id="vendor_confirmation">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-1" style="background-color: #003060; color:white;">
                <h5 class="modal-title" style="color:white;">Confirmation</h5>
                <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="col-md-12">
                <div class="input-group btn-col">
                    <span id="confirmation_id">Are You sure ?</span>
                </div>
            </div>

            <div class="col-md-12">
                <div class="input-group btn-col">
                    <button type="button" class="btn btn-primary btnstyle" id="vendorYes">Yes</button> &nbsp;
                    <button type="button" class="btn btn-primary btnstyle" id="vendorNo">NO</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>





<?php
// } else {
//     swalAlert('warning', 'Warning', 'Wrong attempt, IV Posted already!', LOCATION_URL . 'manage-pending-grn.php');
// }
?>

</div>

<script>
    $(document).ready(function() {
        let arrJson = JSON.parse(`<?= json_encode($resArray, true) ?>`);
        if (arrJson.length > 0) {
            let combinedMessages = arrJson.map(item =>
                `Item Code: ${item.itemCode}\nMessage: ${item.message}`
            ).join('\n\n');

            Swal.fire({
                icon: 'warning',
                title: "Item Status",
                html: `<pre>${combinedMessages}</pre>`,
                showConfirmButton: true,
                confirmButtonText: "Okay"
            });
        }
    });
    let multipleBatchRowNo = 0;
    let multipleBatchRowNumber = 0;

    function addGrnItemMultipleBatch(slNumber, qty = 0, isFirstRow = false, bins = null) {
        multipleBatchRowNo += 1;

        let checkboxEnable = document.getElementById(`grnEnableCheckBx_${slNumber}`).checked;

        var batchHtml = `
            <div class="modal-add-row">
                <div class="row manual-grn-plus-modal modal-cog-right${isFirstRow ? ' dotted-border-area mx-1' : ''}">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="form-input">
                            <label>Batch Number</label>
                            <input type="text" name="grnItemList[${slNumber}][multipleBatch][${multipleBatchRowNo}][batchNumber]" class="form-control multiBatch multiBatchRowNumber_${slNumber}" data-itemid="${slNumber}" value="${!checkboxEnable ? 'GRNXXXXXXXXX' : ''}" id="multiDeliveryDate_${multipleBatchRowNo}" placeholder="Batch Number" ${checkboxEnable ? '' : 'readonly'}>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="form-input">
                            <label>Quantity</label>
                            <input type="number" step = "any" name="grnItemList[${slNumber}][multipleBatch][${multipleBatchRowNo}][quantity]" class="form-control multiQuantity multiBatchRowQuantity_${slNumber}" data-itemid="${slNumber}" id="multiQuantity_${multipleBatchRowNo}" placeholder="quantity" value="${qty}" ${isFirstRow ? 'readonly':''}>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="form-input">
                            <label>Bin</label>
                            <select class="form-control multiBatchOptions_${slNumber}" id="" name="grnItemList[${slNumber}][multipleBatch][${multipleBatchRowNo}][bin]">
                                <option value="0">Select Bin</option>` + bins + `</select>
                    </div>
                    </div>
                    ${isFirstRow ? (`
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <a style="cursor: pointer" class="btn btn-primary addQtyBtn addQtyBtnMultiOptions_${slNumber}" data-optiondata="" id="addQtyBtn_${slNumber}">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>`) : (`
                    <div class="col-lg-1 col-md-1 col-sm-1 dlt-popup deleteQtyBtnMultiOptions_${slNumber}" data-rowno="${multipleBatchRowNo}" id="deleteQtyBtn_${slNumber}">
                        <a style="cursor: pointer" class="btn btn-danger">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>`)}
                </div>
                 ${isFirstRow ? (`
                  <p class="note my-3">
                    Please record / enter the batch details of the last line item only - the quantity is automatically calculated.
                  </p>
                 `) : (``) }
            </div>`;

        $(`#modal-add-row_${slNumber}`).append(batchHtml);
    }


    function addGrnItemMultipleBatchNew(slNumber, qty = 0, isFirstRow = false, bins = null) {
        multipleBatchRowNumber = slNumber;

        var batchHtml = `
            <div class="modal-add-row">
                <div class="row manual-grn-plus-modal modal-cog-right${isFirstRow ? ' dotted-border-area mx-1' : ''}">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="form-input">
                            <label>Batch Number</label>
                            <input type="text" name="grnItemList[${slNumber}][multipleBatch][${multipleBatchRowNumber}][batchNumber]" class="form-control multiBatch multiBatchRowNumber_${slNumber}" data-itemid="${slNumber}" value="GRNXXXXXXXXX" id="multiDeliveryDate_${multipleBatchRowNumber}" placeholder="Batch Number" readonly>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="form-input">
                            <label>Quantity</label>
                            <input type="number" step = "any" name="grnItemList[${slNumber}][multipleBatch][${multipleBatchRowNumber}][quantity]" class="form-control multiQuantity multiBatchRowQuantity_${slNumber}" data-itemid="${slNumber}" id="multiQuantity_${multipleBatchRowNumber}" placeholder="quantity" value="${qty}" ${isFirstRow ? 'readonly':''}>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="form-input">
                            <label>Bin</label>
                            <select class="form-control multiBatchOptions_${slNumber}" id="" name="grnItemList[${slNumber}][multipleBatch][${multipleBatchRowNumber}][bin]">
                                <option value="0">Select Bin</option>` + bins + `</select>
                    </div>
                    </div>
                    ${isFirstRow ? (`
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <a style="cursor: pointer" class="btn btn-primary addQtyBtn addQtyBtnMultiOptions_${slNumber}" data-optiondata="" id="addQtyBtn_${slNumber}">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>`) : (`
                    <div class="col-lg-1 col-md-1 col-sm-1 dlt-popup deleteQtyBtnMultiOptions_${slNumber}" data-rowno="${multipleBatchRowNumber}" id="deleteQtyBtn_${slNumber}">
                        <a style="cursor: pointer" class="btn btn-danger">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>`)}
                </div>
                 ${isFirstRow ? (`
                  <p class="note my-3">
                    Please record / enter the batch details of the last line item only - the quantity is automatically calculated.
                  </p>
                 `) : (``) }
            </div>`;

        $(`#modal-add-row_${slNumber}`).html(batchHtml);
    }
</script>



<script>
    $(document).ready(function() {
        console.log("hello there!");
        var type = "goods";
        var obj = <?= json_encode($getStorageLocationListForGrnObj) ?>;
        var obj1 = <?= json_encode($getCostCenterListForGrnObj) ?>;
        var id = <?= json_encode($id) ?>;
        var inv_date = <?= json_encode($documentDate) ?>;
        var vendor_code = <?= json_encode($vendorGstinStateCode) ?>;
        var customer_code = <?= json_encode($customerGstinStateCode) ?>;
        var company_currency = <?= json_encode($comp_currency)  ?>;
        var serial_number = <?= json_encode($sl) ?>;
        var vendorGstNo = <?= json_encode($vendorGstin) ?>;


        $("#allocate_id").hide();


        $('input:radio[name="distribute"]').change(
            function() {
                if ($(this).is(':checked') && $(this).val() == 'map') {
                    $("#mapInvoiceItemCodeForm").show();
                    $("#allocate_id").hide();
                } else {
                    $("#mapInvoiceItemCodeForm").hide();
                    $("#allocate_id").show();
                }
            });

        $("#allocate_id").click(function(e) {

            let allocateStatus = 0;

            // ALLOCATE VALIDATION
            if ($("input:checkbox[class=checkbx]:checked").length === 0) {
                // $(".pending_grn_Allocate").remove();
                // $("[name='invoiceAllocate']").parent().append('<span class="error pending_grn_Allocate">Atleast One Item need to be checked</span>');
                // $(".pending_grn_Allocate").show();

                $(".notesAllocate").remove();
                $("#notesModalBody").append(
                    '<p class="notesAllocate font-monospace text-danger">Atleast One Item need to be checked</p>'
                );
            } else {
                // $(".pending_grn_Allocate").remove();

                $(".notesAllocate").remove();
                allocateStatus++;
            }

            if (allocateStatus !== 1) {
                e.preventDefault();
                $("#mapInvoiceItemCodeModalCloseBtn").click();
                $("#examplePendingGrnModal").modal("show");
            } else {
                // alert("Hello");
                var yourArray = [];
                let freightprice = (parseFloat($(`#modalItemAmt`).val()) > 0) ? parseFloat($(`#modalItemAmt`).val()) : 0;
                let freight_id = $("#modalItemSlNo").val();
                // alert(freightprice);

                let total_base = 0;
                $("input:checkbox[class=checkbx]:checked").each(function() {
                    var id = $(this).val();
                    let basic = (parseFloat($(`#ItemInvoiceTotalPrice_${id}`).val()) > 0) ? parseFloat($(`#ItemInvoiceTotalPrice_${id}`).val()) : 0;
                    total_base += basic;
                    yourArray.push($(this).val());
                });

                $("input:checkbox[class=checkbx]:checked").each(function() {
                    var id = $(this).val();
                    let basic_each = (parseFloat($(`#ItemInvoiceTotalPrice_${id}`).val()) > 0) ? parseFloat($(`#ItemInvoiceTotalPrice_${id}`).val()) : 0;
                    let qty = (parseFloat($(`#grnItemReceivedQtyTdInput_${id}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${id}`).val()) : 0;
                    // let x = (basic_each * freightprice) / total_base;
                    let x = (basic_each / total_base) * freightprice;
                    let base_after_freight = "<?= $comp_currency ?>: " + (basic_each + x);
                    let base_after_freight2 = basic_each + x;


                    let item_rate = base_after_freight2 / qty;
                    item_rate = item_rate.toFixed(2);
                    $(`#ItemInvoiceTotalPrice_${id}`).val(base_after_freight2);
                    $(`#grnItemInvoiceBaseAmtTdSpan_${id}`).html(base_after_freight);
                    $(`#grnItemUnitPriceTdInput_${id}`).val(item_rate);
                    $(`#ItemInvoiceTotalPrice_${freight_id}`).val(0);
                    $(`#grnItemTdsTdInput_${freight_id}`).attr("readonly", "true");
                    $(`#itemStockQty_${freight_id}`).attr("readonly", "true");

                    $(`#ItemInvoiceIGST_${freight_id}`).val(0);
                    $(`#ItemInvoiceCGST_${freight_id}`).val(0);
                    $(`#ItemInvoiceSGST_${freight_id}`).val(0);
                    $(`#grnItemTdsTdInput_${freight_id}`).val(0);
                    $(`#ItemInvoiceTDSValue_${freight_id}`).val(0);

                    calculateOneItemAmounts(id);

                    $(`#grnItemReceivedQtyTdInput_${id}`).attr("readonly", "true");
                    $(`#grnItemUnitPriceTdInput_${id}`).attr("disabled", "disabled");


                });
                let vamount = $("#v_amount").val();
                if (vamount !== 0) {
                    var lst_v = parseFloat($("#roundvalue").val()) || 0;

                    var new_v = lst_v + parseFloat(vamount);


                    $("#roundvalue").val(new_v.toFixed(2));
                    $("#roundoff_span").text(new_v.toFixed(2));
                }
                calculateGrandTotalAmount();

                // alert(total_base);
                // console.log(yourArray);

                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: 'success',
                    title: `&nbsp; Cost Allocated Successfuly`
                });
                $("#mapInvoiceItemCodeModalCloseBtn").click();
                $("#mapInvoiceItemCode").hide();
                $(`#grnItemCodeTdSpan_${freight_id}`).html("<p>(Allocated)</p>");
                $(`#grnItemStrgLocTdSpan_${freight_id}`).html("<p></p>");
                $(`#itemStockQty_${freight_id}`).html("<p></p>");
                $(`#grnItemInvoiceQtyTdSpan_${freight_id}`).html("<p></p>");
                $(`#grnItemReceivedQtyTdInput_${freight_id}`).attr("readonly", "true");


                $(`#grnItemUnitPriceTdInput_${freight_id}`).attr("disabled", "disabled");
                $(`#grnItemMessage_${freight_id}`).html("<p></p>");
            }

        });



        $(document).on("change", "#selectCurrency", function() {
            var selected_currency = $("#selectCurrency").find(':selected').data("currname");

            $.ajax({
                url: "ajaxs/ajax-currency-convert.php?company_currency=" + company_currency + "&selected_currency=" + selected_currency,
                type: "GET",
                beforeSend: function() {
                    $(`#currency_conversion_rate`).val("Loading....");
                },
                success: function(responseData) {
                    var responseObj = JSON.parse(responseData);
                    for (elem of $(".itemUnitPricehidden")) {
                        let rowNo = ($(elem).attr("id")).split("_")[1];
                        let newVal = ($(elem).val()) * responseObj;
                        // $(`#grnItemUnitPriceTdInput_${rowNo}`).val(newVal);
                        $(`#currency_conversion_rate`).val(responseObj);
                        $(`#spanInvoiceCurrencyName_${rowNo}`).html(selected_currency);

                        // $(elem).val(newVal);
                        calculateOneItemAmounts(rowNo);
                    };
                }
            });

        });

        $(document).on("keyup", "#currency_conversion_rate", function() {

            for (elem of $(".itemUnitPricehidden")) {
                let rowNo = ($(elem).attr("id")).split("_")[1];
                let newVal = ($(elem).val()) * $("#currency_conversion_rate").val();
                // $(`#grnItemUnitPriceTdInput_${rowNo}`).val(newVal);
                // $(elem).val(newVal);
                calculateOneItemAmounts(rowNo);
            };
        });


        $(document).on("click", ".removedItemAdd", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];

            let removeItemName = $(`#removedItemName_${rowNo}`).val();
            let removeItemHsn = $(`#removedItemHsn_${rowNo}`).val();
            let removeItemQuantity = $(`#removedItemQuantity_${rowNo}`).val();
            let removeItemUnitPrice = $(`#removedItemUnitPrice_${rowNo}`).val();
            let removeItemBasicPrice = $(`#removedItemBasicPrice_${rowNo}`).val();
            let removeItemTax = $(`#removedItemTax_${rowNo}`).val();

            $.ajax({
                url: "ajaxs/grn/ajax-removed-item-grn.php?serial_number=" + serial_number + "&itemsName=" + removeItemName + "&removeItemQuantity=" + removeItemQuantity + "&removeItemUnitPrice=" + removeItemUnitPrice + "&removeItemTax=" + removeItemTax + "&removeItemBasicPrice=" + removeItemBasicPrice,
                type: "GET",
                beforeSend: function() {
                    console.log("Adding new items...");
                    // $("#loaderGRN").show();
                },
                success: function(responseData) {
                    $("#itemsTable").append(responseData);
                    serial_number++;
                    // currency_change(curr_name);

                    // console.log(responseData);
                    // setTimeout(function() {
                    //     $("#loaderGRN").hide();
                    // }, 5000);
                }
            });
        });


        $("#itemsDropDown").on("change", function() {

            let itemId = $(this).find('option:selected').data('itemid') ?? 0;
            console.log(itemId);
            let faliledItemCheck = {};
            if (itemId != 0) {
                $.ajax({
                    type: "GET",
                    url: "<?= LOCATION_URL ?>ajaxs/ajax-item-failed-acc-checking.php",
                    data: {
                        act: 'checkItem',
                        itemId
                    },
                    async: false,
                    success: function(response) {
                        try {
                            let res = JSON.parse(response);
                            console.log(res);
                            faliledItemCheck = res;
                        } catch (e) {
                            console.log(e);
                        }
                    }
                });


                if (faliledItemCheck.status != 'success') {
                    $('#itemsDropDown').val(0).trigger('change');
                    Swal.fire({
                        icon: faliledItemCheck.status,
                        title: "Item Status",
                        text: faliledItemCheck.message,
                        showConfirmButton: true,
                        confirmButtonText: "Okay"
                    });

                } else {
                    let itemName = $(this).find('option:selected').data('name');
                    let itemHSN = $(this).find('option:selected').data('hsncode');
                    let itemUOM = $(this).find('option:selected').data('uom');
                    let itemBasicPrice = $(this).find('option:selected').data('basic');
                    let itemTaxPercent = $(this).find('option:selected').data('taxpercent');
                    let itemtds_id = $(this).find('option:selected').data('tds');
                    let uomId = $(this).find('option:selected').data('uomid');
                    let default_storage_location = $(this).find('option:selected').data('default');
                    let qa_enabled = $(this).find('option:selected').data('qualityenable');
                    let qa_location = $(this).find('option:selected').data('qalocation');
                    let itemHsnPercent = $(this).find('option:selected').data('hsnPercent');
                    let goodsType = $(this).find('option:selected').data('goods');
                    let itemQty = $(this).find('option:selected').data('itemqty');
                    let itemCode = $(this).find('option:selected').data('itemcode');
                    let baseAmt = 0;
                    if (isNaN($(this).find('option:selected').data('itemprice'))) {
                        baseAmt = 0;
                    } else {
                        baseAmt = $(this).find('option:selected').data('itemprice');
                    }


                    if (itemName && itemName.trim() !== "") {
                        var vendor_code = <?= json_encode($vendorGstinStateCode) ?>;
                        var customer_code = <?= json_encode($customerGstinStateCode) ?>;
                        $.ajax({
                            url: "ajaxs/grn/ajax-grn-manual-item.php?serial_number=" + serial_number + "&itemsName=" + itemName + "&itemQuantity=" + itemQty + "&itemUnitPrice=" + baseAmt + "&tax=" + itemTaxPercent + "&itemHSN=" + itemHSN + "&itemUOM=" + itemUOM + "&itemBasicPrice=" + itemBasicPrice + "&itemtds_id=" + itemtds_id + "&goodstype=" + goodsType + "&itemid=" + itemId + "&uomid=" + uomId + "&itemCode=" + itemCode + "&vendor_code=" + vendor_code + "&customer_code=" + customer_code,
                            type: "GET",
                            beforeSend: function() {
                                console.log("Adding new items...");
                                // $("#loaderGRN").show();
                            },
                            success: function(responseData) {

                                $("#itemsTable").append(responseData);

                                serial_number++;

                                $('#itemsDropDown').val(0).trigger('change');

                                setTimeout(() => {

                                    let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${serial_number}`).val()) > 0) ?
                                        parseFloat($(`#grnItemReceivedQtyTdInput_${serial_number}`).val()) :
                                        0;

                                    // Get binlist HTML and vendor ID
                                    var binlistHtml = $(`#grnItemAllBins_${serial_number}`).val();
                                    var vendorIdValue = $(`#itemVendorId_${serial_number}`).val();


                                    // Call the function for batch processing
                                    addGrnItemMultipleBatchNew(serial_number, itemQty, true, binlistHtml);

                                    // Calculate amounts
                                    calculateOneItemAmounts();
                                }, 0);
                            }
                        });
                    }
                }
            }

        });



        function getSlabPercentage(amount, slabArray) {
            var slab = $.grep(slabArray, function(item) {
                var lowerLimit = item[0];
                var upperLimit = item[1];
                return amount >= lowerLimit && (upperLimit === null || amount < upperLimit);
            });

            return slab.length > 0 ? slab[0][2] : 0;
        }

        $(document).on("keyup", ".itemUnitPrice", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];

            var tdsslab = $(`#ItemInvoiceTDSSlab_${rowNo}`).val();
            var arrayValue = JSON.parse(tdsslab);
            var goodstype = $(`#ItemInvoiceGoodsType_${rowNo}`).val();

            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;

            let baseAmt = itemQty * itemUnitPrice;

            console.log(goodstype);

            if (goodstype == "service") {
                var percentage = getSlabPercentage(baseAmt, arrayValue);

                $(`#grnItemTdsTdInput_${rowNo}`).val(percentage);
                let tds_value = baseAmt * (percentage / 100);

                $(`#ItemInvoiceTDSValue_${rowNo}`).val(tds_value);

            } else {
                $(`#grnItemTdsTdInput_${rowNo}`).val(0);
                $(`#ItemInvoiceTDSValue_${rowNo}`).val(0);
            }



            calculateOneItemAmounts(rowNo);
        });

        $(document).on("keyup", ".received_quantity", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];

            var tdsslab = $(`#ItemInvoiceTDSSlab_${rowNo}`).val();
            console.log(tdsslab);
            var arrayValue = JSON.parse(tdsslab);
            var goodstype = $(`#ItemInvoiceGoodsType_${rowNo}`).val();



            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;

            let baseAmt = itemQty * itemUnitPrice;

            var binlistHtml = $(`#grnItemAllBins_${rowNo}`).val();
            var vendorIdValue = $(`#itemVendorId_${rowNo}`).val();

            addGrnItemMultipleBatchNew(rowNo, itemQty, true, binlistHtml);
            $(`#totalItemAmountModal_${rowNo}`).html(itemQty);

            if (goodstype == "service") {
                var percentage = getSlabPercentage(baseAmt, arrayValue);

                $(`#grnItemTdsTdInput_${rowNo}`).val(percentage);
                let tds_value = baseAmt * (percentage / 100);

                $(`#ItemInvoiceTDSValue_${rowNo}`).val(tds_value);

            } else {
                $(`#grnItemTdsTdInput_${rowNo}`).val(0);
                $(`#ItemInvoiceTDSValue_${rowNo}`).val(0);
            }
            $(`#grnItemReceivedQtyTdInput_${rowNo}`).val(itemQty);
            calculateOneItemAmounts(rowNo);
        });

        $(document).on("keyup", ".itemTds", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        function calculateOneItemAmounts(rowNo) {
            var vendor_code = <?= json_encode($vendorGstinStateCode) ?>;
            var customer_code = <?= json_encode($customerGstinStateCode) ?>;
            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;
            let cgst = (parseFloat($(`#ItemInvoiceCGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceCGSTNew_${rowNo}`).val()) : 0;
            let sgst = (parseFloat($(`#ItemInvoiceSGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceSGSTNew_${rowNo}`).val()) : 0;
            let igst = (parseFloat($(`#ItemInvoiceIGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceIGSTNew_${rowNo}`).val()) : 0;
            let tds = (parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) : 0;
            let tax = (parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) : 0;
            let itemStaticPrice = (parseFloat($(`#ItemInvoiceTotalPriceStatic_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceTotalPriceStatic_${rowNo}`).val()) : 0;
            let itemtax = (parseFloat($(`#itemtax_${rowNo}`).val()) > 0) ? parseFloat($(`#itemtax_${rowNo}`).val()) : 0;
            let basicPrice = itemUnitPrice * itemQty;
            var cgst_value = 0;
            var sgst_value = 0;
            var igst_value = 0;
            if ((cgst == 0 && sgst == 0) || igst == 0 && itemtax != 0) {


                if (vendor_code == customer_code) {
                    cgst = itemtax / 2;
                    sgst = itemtax / 2;
                    igst = 0;
                } else {
                    igst = itemtax;
                    cgst = 0;
                    sgst = 0;
                }


                cgst_value = basicPrice * (cgst / 100);
                sgst_value = basicPrice * (sgst / 100);
                igst_value = basicPrice * (igst / 100);
            } else {

                let cgstPercent = (cgst / itemStaticPrice) * 100;
                let sgstPercent = (sgst / itemStaticPrice) * 100;
                let igstPercent = (igst / itemStaticPrice) * 100;

                cgstPercent = isNaN(cgstPercent) ? 0 : cgstPercent;
                sgstPercent = isNaN(sgstPercent) ? 0 : sgstPercent;
                igstPercent = isNaN(igstPercent) ? 0 : igstPercent;

                // console.log(itemStaticPrice, cgst, sgst, igst, cgstPercent, sgstPercent, igstPercent);

                cgst_value = basicPrice * (cgstPercent / 100);
                sgst_value = basicPrice * (sgstPercent / 100);
                igst_value = basicPrice * (igstPercent / 100);
            }


            let tds_value = basicPrice * (tds / 100);

            cgst_value = isNaN(cgst_value) ? 0 : cgst_value;
            sgst_value = isNaN(sgst_value) ? 0 : sgst_value;
            igst_value = isNaN(igst_value) ? 0 : igst_value;
            tds_value = isNaN(tds_value) ? 0 : tds_value;

            // console.log(basicPrice,cgst_value,sgst_value,igst_value,tds_value);

            let totalItemPrice = basicPrice + cgst_value + sgst_value + igst_value - tds_value;

            let tax_value = basicPrice + (basicPrice * tax / 100);

            // console.log(itemUnitPrice, itemQty, basicPrice, totalItemPrice, cgst, sgst, igst);

            var curr_name = $("#selectCurrency").find(':selected').data("currname");
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            $(`#grnItemUnitPriceInrhidden_${rowNo}`).val(`${(itemUnitPrice / currency_rate_value).toFixed(2)}`);
            $(`#spanUnitPriceINR_${rowNo}`).html('');

            $(`#grnItemInvoiceTotalPriceTdSpan_${rowNo}`).html(totalItemPrice.toFixed(2));
            $(`#grnItemInvoiceBaseAmtTdSpan_${rowNo}`).html(`${curr_name}: ${(basicPrice).toFixed(2)}` + '<p class="text-small spanBasePriceINR" id="spanBasePriceINR_' + rowNo + '"></p>');
            $(`#ItemInvoiceTotalPrice_${rowNo}`).val((basicPrice / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceGrandTotalPrice_${rowNo}`).val((totalItemPrice / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceTDSValue_${rowNo}`).val((tds_value / currency_rate_value));
            $(`#grnItemInternalTaxValue_${rowNo}`).val((tax_value / currency_rate_value).toFixed(2));

            $(`#ItemInvoiceCGST_${rowNo}`).val((cgst_value / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceSGST_${rowNo}`).val((sgst_value / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceIGST_${rowNo}`).val((igst_value / currency_rate_value).toFixed(2));

            $(`#grnItemInvoiceCGSTTdSpan_${rowNo}`).html(`${curr_name}: ${(cgst_value).toFixed(2)}` + '<p class="text-small spanCgstPriceINR" id="spanCgstPriceINR_' + rowNo + '"></p>');
            $(`#grnItemInvoiceSGSTTdSpan_${rowNo}`).html(`${curr_name}: ${(sgst_value).toFixed(2)}` + '<p class="text-small spanSgstPriceINR" id="spanSgstPriceINR_' + rowNo + '"></p>');
            $(`#grnItemInvoiceIGSTTdSpan_${rowNo}`).html(`${curr_name}: ${(igst_value).toFixed(2)}` + '<p class="text-small spanIgstPriceINR" id="spanIgstPriceINR_' + rowNo + '"></p>');

            if (curr_name != company_currency) {
                $(`#spanUnitPriceINR_${rowNo}`).html(`${company_currency}: ${(itemUnitPrice / currency_rate_value).toFixed(2)}`);
                $(`#spanBasePriceINR_${rowNo}`).html(`${company_currency}: ${(basicPrice / currency_rate_value).toFixed(2)}`);
                $(`#spanCgstPriceINR_${rowNo}`).html(`${company_currency}: ${(cgst_value / currency_rate_value).toFixed(2)}`);
                $(`#spanSgstPriceINR_${rowNo}`).html(`${company_currency}: ${(sgst_value / currency_rate_value).toFixed(2)}`);
                $(`#spanIgstPriceINR_${rowNo}`).html(`${company_currency}: ${(igst_value / currency_rate_value).toFixed(2)}`);
            }


            calculateGrandTotalAmount();
        }


        function calculateGrandTotalAmount() {
            let totalAmount = 0;
            let grandSubTotalAmt = 0;
            let totalInternalTax = 0;
            let totalTds = 0;
            let totalInternalTaxValue = 0;
            let roundoff = parseFloat($("#roundvalue").val() || 0.0);
            if (roundoff !== 0) {
                $("#roundoff").show();
            }
            // $(".ItemInvoiceGrandTotalPrice").each(function() {
            //     totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // });
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemInternalTax").each(function() {
                totalInternalTax += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".ItemInvoiceTDSValue").each(function() {
                totalTds += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemInternalTaxValue").each(function() {
                totalInternalTaxValue += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            // console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
            // let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;

            let ToTalcgst = 0;
            let ToTalsgst = 0;
            let ToTaligst = 0;

            $(".ItemInvoiceCGSTClass").each(function() {
                ToTalcgst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            $(".ItemInvoiceSGSTClass").each(function() {
                ToTalsgst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            $(".ItemInvoiceIGSTClass").each(function() {
                ToTaligst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            // let ToTalcgst = (parseFloat($(`#totalCGST`).val()) > 0) ? parseFloat($(`#totalCGST`).val()) : 0;
            // let ToTalsgst = (parseFloat($(`#totalSGST`).val()) > 0) ? parseFloat($(`#totalSGST`).val()) : 0;
            // let ToTaligst = (parseFloat($(`#totalIGST`).val()) > 0) ? parseFloat($(`#totalIGST`).val()) : 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - totalTds + (roundoff);

            let alltax = ToTalcgst + ToTalsgst + ToTaligst;

            let getpercent = (parseFloat((alltax / grandSubTotalAmt) * 100) > 0) ? parseFloat((alltax / grandSubTotalAmt) * 100) : 0;

            if (totalAmount != totalInternalTaxValue) {
                $("#internalotaxwarning").html("<span class='error text-warning text-xs'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>OCR Tax Percentage not matched with internal tax percentage</span>");
            } else {
                $("#internalotaxwarning").html("");
            }

            var curr_name = $("#selectCurrency").find(':selected').data("currname");
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            $("#grandSubTotalTd").html(`${curr_name}: ${(grandSubTotalAmt * currency_rate_value).toFixed(2)}` + '<p class="text-small spanSubTotalINR" id="spanSubTotalINR"></p>');
            $("#grandSubTotal").val((grandSubTotalAmt).toFixed(2));
            $("#grandTotalTd").html(`${curr_name}: ${(totalAmount * currency_rate_value).toFixed(2)}` + '<p class="text-small spangrandTotalINR" id="spangrandTotalINR"></p>');
            $("#grandTotal").val((totalAmount).toFixed(2));
            $("#grandTds").html(`${curr_name}: -${(totalTds * currency_rate_value).toFixed(2)}` + '<p class="text-small spangrandTDSINR" id="spangrandTDSINR"></p>');
            $("#totalTDS").val((totalTds).toFixed(2));

            $("#grandCgstTd").html(`${curr_name}: ${(ToTalcgst * currency_rate_value).toFixed(2)}` + '<p class="text-small spanCgstGrandINR" id="spanCgstGrandINR"></p>');
            $("#totalCGST").val((ToTalcgst).toFixed(2));
            $("#grandSgstTd").html(`${curr_name}: ${(ToTalsgst * currency_rate_value).toFixed(2)}` + '<p class="text-small spanSgstGrandINR" id="spanSgstGrandINR"></p>');
            $("#totalSGST").val((ToTalsgst).toFixed(2));
            $("#grandIgstTd").html(`${curr_name}: ${(ToTaligst * currency_rate_value).toFixed(2)}` + '<p class="text-small spanIgstGrandINR" id="spanIgstGrandINR"></p>');
            $("#totalIGST").val((ToTaligst).toFixed(2));

            if (company_currency != curr_name) {
                $(`#spanSubTotalINR`).html(`${company_currency}: ${(grandSubTotalAmt).toFixed(2)}`);
                $(`#spangrandTotalINR`).html(`${company_currency}: ${(totalAmount).toFixed(2)}`);
                $(`#spangrandTDSINR`).html(`${company_currency}: -${(totalTds).toFixed(2)}`);
                $(`#spanCgstGrandINR`).html(`${company_currency}: ${(ToTalcgst).toFixed(2)}`);
                $(`#spanSgstGrandINR`).html(`${company_currency}: ${(ToTalsgst).toFixed(2)}`);
                $(`#spanIgstGrandINR`).html(`${company_currency}: ${(ToTaligst).toFixed(2)}`);
            }
        }


        $(document).on("click", ".remove_row", function() {


            let rowNo = ($(this).attr("id")).split("_")[1];
            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;
            let cgst = (parseFloat($(`#ItemInvoiceCGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceCGST_${rowNo}`).val()) : 0;
            let sgst = (parseFloat($(`#ItemInvoiceSGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceSGST_${rowNo}`).val()) : 0;
            let igst = (parseFloat($(`#ItemInvoiceIGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceIGST_${rowNo}`).val()) : 0;
            let tds = (parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) : 0;
            let tax = (parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) : 0;

            let basicPrice = itemUnitPrice * itemQty;

            let tds_value = basicPrice * (tds / 100);
            let totalTds = 0;

            let ToTalcgst = 0;
            let ToTalsgst = 0;
            let ToTaligst = 0;

            $(".ItemInvoiceCGSTClass").each(function() {
                ToTalcgst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            $(".ItemInvoiceSGSTClass").each(function() {
                ToTalsgst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            $(".ItemInvoiceIGSTClass").each(function() {
                ToTaligst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".ItemInvoiceTDSValue").each(function() {
                totalTds += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(`#grnItemMessage_${rowNo}`).remove();
            $(this).parent().parent().remove();

            let totalAmount = 0;
            let grandSubTotalAmt = 0;
            let totalInternalTax = 0;
            let totalInternalTaxValue = 0;

            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemInternalTax").each(function() {
                totalInternalTax += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemInternalTaxValue").each(function() {
                totalInternalTaxValue += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            // let ToTalcgst = (parseFloat($(`#totalCGST`).val()) > 0) ? parseFloat($(`#totalCGST`).val()) : 0;
            // let ToTalsgst = (parseFloat($(`#totalSGST`).val()) > 0) ? parseFloat($(`#totalSGST`).val()) : 0;
            // let ToTaligst = (parseFloat($(`#totalIGST`).val()) > 0) ? parseFloat($(`#totalIGST`).val()) : 0;

            let cgstDeduct = ToTalcgst - cgst;
            let sgstDeduct = ToTalsgst - sgst;
            let igstDeduct = ToTaligst - igst;
            let tdsDeduct = totalTds - tds_value;

            totalAmount = grandSubTotalAmt + cgstDeduct + sgstDeduct + igstDeduct - tdsDeduct;

            let alltax = cgstDeduct + sgstDeduct + igstDeduct;

            let getpercent = (parseFloat((alltax / grandSubTotalAmt) * 100) > 0) ? parseFloat((alltax / grandSubTotalAmt) * 100) : 0;

            // console.log(totalInternalTax);

            if (totalInternalTaxValue != totalAmount) {
                $("#internalotaxwarning").html("<span class='error text-warning text-xs'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>OCR Tax Percentage not matched with internal tax percentage</span>");
            } else {
                $("#internalotaxwarning").html("");
            }

            var curr_name = $("#selectCurrency").find(':selected').data("currname");
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            $("#grandSubTotalTd").html((grandSubTotalAmt * currency_rate_value).toFixed(2) + '<p class="text-small spanSubTotalINR" id="spanSubTotalINR"></p>');
            $("#grandSubTotal").val((grandSubTotalAmt).toFixed(2));

            $("#grandTotalTd").html((totalAmount * currency_rate_value).toFixed(2) + '<p class="text-small spangrandTotalINR" id="spangrandTotalINR"></p>');
            $("#grandTotal").val((totalAmount).toFixed(2));

            $("#grandCgstTd").html((cgstDeduct * currency_rate_value).toFixed(2) + '<p class="text-small spanCgstGrandINR" id="spanCgstGrandINR"></p>');
            $("#grandSgstTd").html((sgstDeduct * currency_rate_value).toFixed(2) + '<p class="text-small spanSgstGrandINR" id="spanSgstGrandINR"></p>');
            $("#grandIgstTd").html((igstDeduct * currency_rate_value).toFixed(2) + '<p class="text-small spanIgstGrandINR" id="spanIgstGrandINR"></p>');

            $("#totalCGST").val((cgstDeduct).toFixed(2));
            $("#totalSGST").val((sgstDeduct).toFixed(2));
            $("#totalIGST").val((igstDeduct).toFixed(2));

            $("#grandTds").html("-" + (tdsDeduct * currency_rate_value).toFixed(2) + '<p class="text-small spangrandTDSINR" id="spangrandTDSINR"></p>');
            $("#totalTDS").val((tdsDeduct).toFixed(2));

            if (company_currency != curr_name) {
                $(`#spanSubTotalINR`).html(`${company_currency}: ${(grandSubTotalAmt ).toFixed(2)}`);
                $(`#spangrandTotalINR`).html(`${company_currency}: ${(totalAmount ).toFixed(2)}`);
                $(`#spangrandTDSINR`).html(`${company_currency}: ${(tdsDeduct ).toFixed(2)}`);
                $(`#spanCgstGrandINR`).html(`${company_currency}: ${(cgstDeduct).toFixed(2)}`);
                $(`#spanSgstGrandINR`).html(`${company_currency}: ${(sgstDeduct ).toFixed(2)}`);
                $(`#spanIgstGrandINR`).html(`${company_currency}: ${(igstDeduct ).toFixed(2)}`);
            }

        });


        $("#modalItemCodeDropDown").select2({
            dropdownParent: $("#mapInvoiceItemCode")
        });


        $("#modalItemCodeDropDownChange").select2({
            dropdownParent: $("#mapInvoiceItemCodeChange")
        });

        let vendorCode = `<?= $vendorCode ?>`;
        let vendorId = `<?= $vendorId ?>`;

        $("#refresh_po_list").click(function() {
            // alert("Hello");
            $.ajax({
                url: "ajaxs/grn/ajax-fetch-po.php?vendor_id=" + vendorId,
                type: "GET",
                beforeSend: function() {},
                success: function(responseData) {
                    var responseObj = JSON.parse(responseData);
                    console.log(responseData);
                    $("#open_po_list_table").html(responseObj);
                }
            });

        });



        if (vendorCode == "") {
            $("#vendor-quick-registration-div-tab").click();
            $("#itemTotalPrice")
        }

        $("#vendorQuickAddForm").on('submit', (function(e) {
            e.preventDefault();
            $.ajax({
                url: "ajaxs/vendor/ajax-vendor-quick-register.php",
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function() {
                    $("#vendorQuickAddFormSubmitBtn").html("Processing...");
                    console.log("Adding...");
                },
                success: function(response) {
                    let responseObj = JSON.parse(response);
                    $("#vendorQuickAddFormSubmitBtn").html("Added Successfully");
                    console.log(responseObj);
                    if (responseObj["status"] == "success") {
                        $("#invoiceVendorCodeInput").val(responseObj["vendorCode"]);
                        vendorId = responseObj["vendorId"];
                        $("#invoiceVendorIdInput").val(responseObj["vendorId"]);
                        $("#invoiceVendorCodeSpan").html(responseObj["vendorCode"]);
                        $("#vendorName").html(responseObj["vendorName"]);
                        $("#vendorNameInput").val(responseObj["vendorName"]);
                        $("#dialogForVendorQuickAddCloseBtn").click();

                        $("#uploaded-invoice-preview-div-tab").click();
                        $("#vendor-quick-registration-div").remove();
                        $("#vendor-quick-registration-div-tab").remove();
                        $("#vendor_list_tab").remove();

                        var creditPeriod = responseObj["creditPeriod"];
                        var temp_date = new Date(inv_date);
                        temp_date.setDate(temp_date.getDate() + creditPeriod);

                        let day = temp_date.getDate();
                        let month = temp_date.getMonth();
                        let year = temp_date.getFullYear();

                        let format = year + "-" + month + "-" + day;

                        $("#iv_due_date").val(format);

                        // alert(temp_date);


                    }

                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: responseObj["status"],
                        title: `&nbsp;${responseObj["message"]}`
                    });

                },
                error: function(e) {
                    console.log("error: " + e.message);
                }
            });
        }));


        $(".btnModal").click(function() {
            var passedID = $(this).data('id'); //get the id of the selected button
            var code = $(this).data('code');
            console.log(passedID);

            $.ajax({
                url: '<?= BASE_URL ?>/branch/location/ajaxs/po/ajax-get-po-items.php?po_id=' + passedID,
                type: 'GET',
                beforeSend: function() {},
                success: function(responseData) {
                    responseObj = JSON.parse(responseData);
                    $("#po_table").html(responseObj);
                    $("#select_po_button").val(code);
                }
            });


        });

        $("#select_po_button").click(function() {
            var passedCode = $(this).val();
            var vendor_code = $("#invoiceVendorCodeInput").val();

            console.log(passedCode);

            $.ajax({
                url: "ajaxs/po/ajax-update-po-grn.php?grn=" + id + "&po=" + passedCode + "&vendor_code=" + vendor_code,
                type: "GET",
                beforeSend: function() {},
                success: function(response) {
                    let responseObj = JSON.parse(response);
                    $("#po_date").val(response["po_date"]);
                    if ($("#invoicePoNumber").val() == "") {
                        $("#custom-tabs-three-tab").append("<span class='divider-vertical'>|</span><li class='nav-item'><a class='nav-link text-secondary' id='invoice-po-div-tab' data-toggle='pill' href='#po_details' role='tab' aria-controls='invoice-po-div' aria-selected='false'>Matched with PO</a></li>");
                    }
                    $("#invoicePoNumber").val(passedCode);
                    console.log(responseObj);

                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: responseObj["status"],
                        title: `&nbsp;${responseObj["message"]}`
                    });

                },
                error: function(e) {
                    console.log("error: " + e.message);
                }
            });

            $("#po_items").hide();
        });


        $(".vendorListClass").click(function() {
            var id = $(this).data('id');
            var code = $(this).data('code');
            var name = $(this).data('name');
            console.log(id);
            $("#confirmation_id").html("Are you sure you want to map this invoice with " + name + " ??");
            $("#vendorYes").val(id);

        });

        $("#refresh_po_match").click(function() {
            alert("Hello");
        });


        $(".quick-add-vendor").click(function() {
            // alert(1);

            $.ajax({
                type: "GET",
                url: `ajaxs/grn/ajax-grn-vendor-gstin.php?gstin=${vendorGstNo}&grnId=${id}`,
                beforeSend: function() {

                },
                success: function(response) {
                    // $(".checkAndVerifyGstinBtn").toggleClass("disabled");
                    //  $('.checkAndVerifyGstinBtn').html("Re-Verify");
                    responseObj = (response);
                    //responseObj = JSON.parse(responseObj);
                    // $("#VerifyGstinBtnDiv").hide();
                    // $("#multistepform").show();
                    $("#vendorQuickAddForm").html(responseObj);
                    //console.log(responseObj);
                    // load_js();
                }
            });

        });


        $("#vendorYes").click(function() {
            var vendor_id = $(this).val();
            $.ajax({
                url: "ajaxs/vendor/ajax-update-vendor-grn.php?grn=" + id + "&vendor=" + vendor_id,
                type: "GET",
                beforeSend: function() {},
                success: function(response) {
                    let responseObj = JSON.parse(response);
                    console.log(responseObj);

                    if (responseObj["status"] == "success") {
                        $("#invoiceVendorCodeInput").val(responseObj["code"]);
                        vendorId = vendor_id;
                        $("#invoiceVendorIdInput").val(vendor_id);
                        $("#invoiceVendorCodeSpan").html(responseObj["code"]);
                        $("#vendorName").html(responseObj["name"]);
                        $("#vendorNameInput").val(responseObj["name"]);
                        $("#dialogForVendorQuickAddCloseBtn").click();

                        $("#uploaded-invoice-preview-div-tab").click();
                        $("#vendor-quick-registration-div").remove();
                        $("#vendor-quick-registration-div-tab").remove();
                        $("#vendor_list_tab").remove();
                        $("#vendor_confirmation").hide();

                        var creditPeriod = responseObj["creditPeriod"];
                        var temp_date = new Date(inv_date);
                        temp_date.setDate(temp_date.getDate() + creditPeriod);

                        let day = temp_date.getDate();
                        let month = temp_date.getMonth();
                        let year = temp_date.getFullYear();

                        let format = year + "-" + month + "-" + day;

                        $("#iv_due_date").val(format);

                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: responseObj["status"],
                            title: `&nbsp;${responseObj["message"]}`
                        });
                    } else {
                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        Toast.fire({
                            icon: responseObj["status"],
                            title: `&nbsp;${responseObj["message"]}`
                        });
                    }

                },
                error: function(e) {
                    console.log("error: " + e.message);
                }
            });
        });


        $("#vendorNo").click(function() {
            $("#vendor_confirmation").hide();
        });


        $(document).on("#invoiceVendorCodeInput", ".change", function() {
            let vendorCode = $(this).val();
            console.log("vendorCode: " + vendorCode);
        });

        $("#addNewGRNForm").submit(function(e) {
            e.preventDefault();
            let vendorCode = $("#invoiceVendorCodeInput").val();

            if (vendorCode == "") {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Vendor Code not found, please do vendor quick registration before procced GRN!`
                });

            } else {
                let isAllItemCodesMapped = true;
                $(".grnItemHSNTdSpan").each(function() {
                    let hsnCodes = $(this).text();
                    if (hsnCodes == "") {
                        isAllItemCodesMapped = false;
                        return false;
                    }
                });

                if (!isAllItemCodesMapped) {
                    let Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    Toast.fire({
                        icon: `warning`,
                        title: `&nbsp;Please make sure all item codes have been mapped!`
                    });
                } else {
                    console.log("Verified all item codes, ready for submit the form!");
                    this.submit();
                }

            }

            console.log("vendorCode", vendorCode);
        });


        $(document).on('keyup', '.item-rate', function() {
            // Get the updated item_rate value
            let itemRate = parseFloat($(this).val()) || 0;

            // Find the row where this input belongs
            let row = $(this).closest('tr');

            // Get the qty value for this row
            let qty = parseFloat(row.find('.item-qty').text()) || 0;

            // Calculate the new total
            let updatedTotal = (itemRate * qty).toFixed(2);

            // Update the corresponding cell

            row.find('.item-rate-total').text(updatedTotal);
            newcal();
        });

        function newcal() {
            let grandTotal = 0;

            // Iterate through all elements with the class 'item-rate-total'
            $('.item-rate-total').each(function() {
                // Parse the text content as a float and add it to the grand total
                grandTotal += parseFloat($(this).text()) || 0;
            });

            // Update the display of the grand total (for example, in a specific element)
            $('#grand-total').text(grandTotal.toFixed(2));
            var t = $("#vamount").val();
            if (t == grandTotal) {
                $("#variance").text(0.0);
                document.getElementById('allocate_id').disabled = false;
            } else {
                $("#variance").text((grandTotal - t).toFixed(2));
                document.getElementById('allocate_id').disabled = true;
            }
        }

        $(document).on("click", ".openModalMapInvoiceItemCode", function() {
            $("#allocateclass").hide();
            $(".allocate-cost").hide();


            let itemSlNo = $(this).data("itemrownum");
            let allocate = $(this).data("allocate");
            let itemDescription = ($(`#ItemGRNName_${itemSlNo}`).val()).trim();
            let itemQty = $(`#grnItemQty_${itemSlNo}`).val();
            let itemAmt = parseFloat($(`#ItemInvoiceTotalPrice_${itemSlNo}`).val()) || 0;

            if (allocate == 1) {
                if ($("input:checkbox[class=checkbx]:checked").length === 0) {
                    $("#mapInvoiceItemCode").hide();

                    Swal.fire({
                        title: "Warning!",
                        text: "Atleast One Item need to be checked !",
                        icon: "warning"
                    });
                } else {


                    if (itemAmt > 0) {

                        const modalDialog = $("#mapInvoiceItemCode .modal-dialog");

                        // Add the modal-lg class if it is not already present
                        if (!modalDialog.hasClass("modal-lg")) {
                            modalDialog.addClass("modal-lg");
                        }
                        $(".allocate-cost").show();
                        $("#allocateclass").show();

                        $("#modalItemQtyMap").val(itemQty);
                        $("#modalItemDescription").val(itemDescription);
                        $("#modalItemSlNo").val(itemSlNo);
                        $("#modalItemAmt").val(itemAmt);
                        $('#modalItemCodeDropDown').prop('selectedIndex', 0);
                        if (allocate == 1) {

                            var total_base = 0;
                            $("input:checkbox[class=checkbx]:checked").each(function() {
                                var idd = $(this).val();
                                let basic = (parseFloat($(`#ItemInvoiceTotalPrice_${idd}`).val()) > 0) ? parseFloat($(`#ItemInvoiceTotalPrice_${idd}`).val()) : 0;
                                total_base += basic;


                            });
                            $("#alloc tbody").empty();
                            var total1 = 0;
                            var total2 = 0;
                            $("input:checkbox[class=checkbx]:checked").each(function() {
                                var id = $(this).val();
                                var itemname = $(`#grnItemNameTdSpan_${id}`).text();
                                var itemqty = $(`#grnItemReceivedQtyTdInput_${id}`).val();
                                var unitprice = $(`#grnItemUnitPriceTdInput_${id}`).val();


                                let basic_each = (parseFloat($(`#ItemInvoiceTotalPrice_${id}`).val()) > 0) ? parseFloat($(`#ItemInvoiceTotalPrice_${id}`).val()) : 0;
                                let qty = (parseFloat($(`#grnItemReceivedQtyTdInput_${id}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${id}`).val()) : 0;
                                // let x = (basic_each * itemAmt) / total_base;
                                let x = (basic_each / total_base) * itemAmt;
                                console.log(x, basic_each, itemAmt, total_base);
                                let base_after_freight = "<?= $comp_currency ?>: " + (basic_each + x);
                                let base_after_freight2 = basic_each + x;

                                let item_rate = base_after_freight2 / qty;




                                console.log(total2, unitprice, itemqty, x);

                                total2 = (total2 + (unitprice * itemqty) + x);
                                item_rate = item_rate.toFixed(2);
                                var v = (unitprice * itemqty) + x;
                                var v2 = (item_rate * itemqty);
                                console.log(v, v2);
                                var difference = (v - v2).toFixed(2);
                                total1 = total1 + (item_rate * qty);




                                var newRow = `
                                <tr>
                                    <td>${itemname}</td>
                                    <td class="item-qty">${itemqty}</td>
                                    <td>${unitprice}</td>
                                    
                                    <td>
                                        <input type="number" step="any" value="${item_rate}" readonly class="item-rate form-control" />
                                    </td>
                                    <td>${(unitprice * itemqty).toFixed(2)}</td>
                                    <td class="item-rate-total">${(item_rate * qty).toFixed(2)}</td>
                                </tr>
                            `;


                                $("#alloc tbody").append(newRow);
                                $("#alc").text(itemAmt);
                                console.log(total1);
                                console.log(total2);
                                var vvvv = (total2 - total1).toFixed(2);

                                $("#variance").text((total2 - total1).toFixed(2));
                                $("#vamount").val(total2.toFixed(2));
                                $("#v_amount").val((total2 - total1).toFixed(2));

                                // if (vvvv !== 0) {
                                //     var lst_v = parseFloat($("#roundvalue").val()) || 0;

                                //     var new_v = lst_v + parseFloat(vvvv);

                                //     $("#roundoff").show();
                                //     $("#roundvalue").val(new_v.toFixed(2));
                                //     $("#roundoff_span").text(new_v.toFixed(2));
                                // }





                            })

                            $("#map_radio").css("display", "none");
                            $("#mapitem").css("display", "none");
                            $("#mapheader").css("display", "none");
                            $("#allocateheader").css("display", "block");
                            $("#map_radio").prop("checked", false);
                            $("#allocate_radio").prop("checked", true);

                            $("#mapInvoiceItemCodeForm").hide();

                            $("#allocate_id").show();
                        } else {

                            $("#map_radio").css("display", "block");
                            $("#mapitem").css("display", "block");
                            $("#mapheader").css("display", "block");
                            $("#allocateheader").css("display", "none");
                            $("#allocate_radio").prop("checked", false);
                            $("#map_radio").prop("checked", true);
                            $("#mapInvoiceItemCodeForm").show();
                            $("#allocate_id").hide();
                        }
                    } else {

                        $("#mapInvoiceItemCode").hide();

                        Swal.fire({
                            title: "Warning!",
                            text: "Allocate Item Basic Amount should be greater than 0!",
                            icon: "warning"
                        });
                    }
                }
            } else {
                $("#modalItemQtyMap").val(itemQty);
                $("#modalItemDescription").val(itemDescription);
                $("#modalItemSlNo").val(itemSlNo);
                $("#modalItemAmt").val(itemAmt);
                $("#map_radio").css("display", "block");
                $("#mapitem").css("display", "block");
                $("#mapheader").css("display", "block");
                $("#allocateheader").css("display", "none");
                $("#allocate_radio").prop("checked", false);
                $("#map_radio").prop("checked", true);
                $("#mapInvoiceItemCodeForm").show();
                $("#allocate_id").hide();
            }

        });

        $(document).on("click", ".openModalMapInvoiceItemCodeChange", function() {
            let itemSlNo = $(this).data("itemrownum");
            let itemDescription = ($(`#ItemGRNName_${itemSlNo}`).val()).trim();
            let itemQty = $(`#grnItemQty_${itemSlNo}`).val();
            let itemAmt = $(`#ItemInvoiceTotalPrice_${itemSlNo}`).val();
            console.log(itemQty);
            $("#modalItemQtyChange").val(itemQty);
            $("#modalItemDescriptionChange").val(itemDescription);
            $("#modalItemAmtChange").val(itemAmt);
            $("#modalItemSlNoChange").val(itemSlNo);
            $('#modalItemCodeDropDownChange').prop('selectedIndex', 0);
        });



        $("#mapInvoiceItemCodeForm").submit(function(e) {
            e.preventDefault();
            let vendorCode = $("#invoiceVendorCodeInput").val();
            if (vendorCode != "") {

                let itemId = $("#modalItemCodeDropDown").find(':selected').data("itemid");
                let faliledItemCheck = {};
                $.ajax({
                    type: "GET",
                    url: "<?= LOCATION_URL ?>ajaxs/ajax-item-failed-acc-checking.php",
                    data: {
                        act: 'checkItem',
                        itemId
                    },
                    async: false,
                    success: function(response) {
                        try {
                            let res = JSON.parse(response);
                            console.log(res);
                            faliledItemCheck = res;
                        } catch (e) {
                            console.log(e);
                        }
                    }
                });

                if (faliledItemCheck.status != 'success') {
                    Swal.fire({
                        icon: faliledItemCheck.status,
                        title: "Item Status",
                        text: faliledItemCheck.message,
                        showConfirmButton: true,
                        confirmButtonText: "Okay"
                    });

                } else {
                    console.log("maping item code");
                    let itemSlNo = $("#modalItemSlNo").val();
                    let itemCode = $("#modalItemCodeDropDown").val();
                    let itemHSN = $("#modalItemCodeDropDown").find(':selected').data("hsncode");
                    let itemUOM = $("#modalItemCodeDropDown").find(':selected').data("uom");
                    let itemName = $("#modalItemCodeDropDown").find(':selected').data("name");
                    let itemHsnPercent = $("#modalItemCodeDropDown").find(':selected').data("hsnPercent");
                    let goodsType = $("#modalItemCodeDropDown").find(':selected').data("goods");
                    let itemtds_id = $("#modalItemCodeDropDown").find(':selected').data("tds");
                    let itemTaxPercent = $("#modalItemCodeDropDown").find(':selected').data("taxpercent");
                    let uomId = $("#modalItemCodeDropDown").find(':selected').data("uomid");
                    let default_storage_location = $("#modalItemCodeDropDown").find(':selected').data("default");
                    let qa_enabled = $("#modalItemCodeDropDown").find(':selected').data("qualityenable");
                    let qa_location = $("#modalItemCodeDropDown").find(':selected').data("qalocation");
                    let itemTitle = ($("#modalItemDescription").val()).trim();
                    let itemQty = $("#modalItemQtyMap").val();
                    let baseAmt = $("#modalItemAmt").val();

                    let taskType = "map";
                    let itemType;

                    if (goodsType == 1 || goodsType == 4 || goodsType == 5 || goodsType == 9) {
                        itemType = "goods";
                        $(`#grnItemTdsTdInput_${itemSlNo}`).val(0);
                        $(`#ItemInvoiceTDSValue_${itemSlNo}`).val(0);
                        $(`#grnItemSettingsTdSpan_${itemSlNo}`).html(`<button type='button' class='btn-view btn btn-primary delShedulingBtn' data-toggle='modal' data-target='#deliveryScheduleModal_${itemSlNo}'><i id='statusItemBtn_${itemId}' class='statusItemBtn fa fa-cog'></i></button>`);
                    } else {
                        itemType = "service";

                        $.ajax({
                            url: "ajaxs/grn/ajax-get-tds.php?base=" + baseAmt + "&tds=" + itemtds_id,
                            type: "GET",
                            beforeSend: function() {},
                            success: function(response) {
                                let responseObj = JSON.parse(response);
                                // console.log(responseObj);
                                $(`#grnItemTdsTdInput_${itemSlNo}`).val(responseObj);
                                let tds_value = baseAmt * (responseObj / 100);

                                $(`#ItemInvoiceTDSValue_${itemSlNo}`).val(tds_value);
                                setTimeout(calculateOneItemAmounts(itemSlNo), 30000);
                            }
                        });


                    }
                    $.ajax({
                        url: "ajaxs/vendor/ajax-map-vendor-item-to-internal-code.php",
                        type: "POST",
                        data: {
                            vendorId,
                            vendorCode,
                            itemTitle,
                            itemId,
                            itemCode,
                            itemHSN,
                            itemType,
                            taskType,
                            itemUOM,
                            itemHsnPercent,
                            itemTaxPercent
                        },
                        beforeSend: function() {
                            console.log("Mapping...");
                        },
                        success: function(response) {
                            let responseObj = JSON.parse(response);
                            if (responseObj["status"] == "success") {
                                let mapData = responseObj["data"];
                                if (mapData["itemType"] == "service") {
                                    $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"] + " " + "<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='" + itemSlNo + "' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a> <a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCode' data-itemrownum='" + itemSlNo + "' data-allocate='1' data-toggle='modal' data-target='#mapInvoiceItemCode'>Allocate Cost</a> ");
                                    $(`#check_box_${itemSlNo}`).css('display', 'none');
                                } else {
                                    $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"] + " " + "<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='" + itemSlNo + "' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a>");
                                    $(`#check_box_${itemSlNo}`).css('display', 'block');
                                }
                                // $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"] + " " + "<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='" + itemSlNo + "' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a>");
                                $(`#internalItemId_${itemSlNo}`).val(mapData["itemId"]);
                                $(`#internalItemCode_${itemSlNo}`).val(mapData["itemCode"]);
                                $(`#grnItemHSNTdSpan_${itemSlNo}`).html(mapData["itemHSN"]);
                                $(`#internalItemHsn_${itemSlNo}`).val(mapData["itemHSN"]);
                                $(`#grnItemUOM_${itemSlNo}`).html(mapData["itemUom"]);
                                $(`#grnItemNameTdSpan_${itemSlNo}`).html(itemName);
                                $(`#internalItemName_${itemSlNo}`).val(itemName);
                                $(`#ItemInvoiceUOMID_${itemSlNo}`).val(uomId);
                                $(`#itemtax_${itemSlNo}`).val(itemTaxPercent);

                                var itemInvoiceUnits = $(`#ItemInvoiceUnits_${itemSlNo}`).val();
                                var InternalItemUom = mapData["itemUom"];

                                if (itemInvoiceUnits && InternalItemUom &&
                                    typeof itemInvoiceUnits === 'string' &&
                                    typeof InternalItemUom === 'string' &&
                                    itemInvoiceUnits.toLowerCase() === InternalItemUom.toLowerCase()) {
                                    $(`#grnItemMessage_${itemSlNo}`).html(""); // Clear message if they match
                                } else {
                                    $(`#grnItemMessage_${itemSlNo}`).html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Stock keeping unit and invoice driven unit are different"); // Show error message if they don't match
                                }

                                $(`#mapInvoiceItemCode`).hide();

                                var storageLoc = "";
                                var derivedQty = "";
                                if (itemType == "goods") {
                                    storageLoc += "<select class='form-control text-xs' name='grnItemList[" + itemSlNo + "][itemStorageLocationId]' required><option value=''>Select storage location</option>";

                                    var objects = obj.data;

                                    if (qa_enabled == 1) {
                                        for (let i = 0; i < objects.length; i++) {
                                            if (objects[i].storage_location_id == qa_location) {
                                                storageLoc += "<option selected value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                            } else {
                                                storageLoc += "<option value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                            }

                                        }

                                        $.ajax({
                                            url: "ajaxs/grn/ajax-fetch-bin.php?st=" + qa_location,
                                            type: "GET",
                                            beforeSend: function() {
                                                $(`.multiBatchOptions_${itemSlNo}`).html(`<option value="0">Select Bin</option></select>`);
                                            },
                                            success: function(response) {
                                                let binList = JSON.parse(response);
                                                console.log(binList);
                                                $(`.multiBatchOptions_${itemSlNo}`).html('<option value="0">Select Bin</option>' + binList);
                                                // $(`.addQtyBtnMultiOptions_${itemSlNo}`).data("optiondata",'<option value="0">Select Bin</option></select>'+binList);
                                                // $('.vendorClass_' + vendoritemSlNo).remove();
                                                $(`#grnItemAllBins_${itemSlNo}`).val(binList);
                                            },
                                            error: function(e) {
                                                console.log("error: " + e.message);
                                            }

                                        });
                                    } else {
                                        for (let i = 0; i < objects.length; i++) {
                                            if (objects[i].storage_location_id == default_storage_location) {
                                                storageLoc += "<option selected value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                            } else {
                                                storageLoc += "<option value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                            }

                                        }

                                        $.ajax({
                                            url: "ajaxs/grn/ajax-fetch-bin.php?st=" + default_storage_location,
                                            type: "GET",
                                            beforeSend: function() {
                                                $(`.multiBatchOptions_${itemSlNo}`).html(`<option value="0">Select Bin</option></select>`);
                                            },
                                            success: function(response) {
                                                let binList = JSON.parse(response);
                                                console.log(binList);
                                                $(`.multiBatchOptions_${itemSlNo}`).html('<option value="0">Select Bin</option>' + binList);
                                                // $(`.addQtyBtnMultiOptions_${itemSlNo}`).data("optiondata",'<option value="0">Select Bin</option></select>'+binList);
                                                // $('.vendorClass_' + vendoritemSlNo).remove();
                                                $(`#grnItemAllBins_${itemSlNo}`).val(binList);
                                            },
                                            error: function(e) {
                                                console.log("error: " + e.message);
                                            }

                                        });
                                    }

                                    storageLoc += "</select>";

                                    $(`#grnItemStrgLocTdSpan_${itemSlNo}`).html(storageLoc);
                                    // $(`#grnItemStkQtyTdSpan_${itemSlNo}`).css("display", "block");

                                    derivedQty += "<div class='form-input d-flex' style='align-items: center; gap: 7px;'><input type='number' step='any' value='" + itemQty + "' class='form-control text-xs w-50' name='grnItemList[" + itemSlNo + "][itemStockQty]'><p class='text-xs' id='grnItemUOM_" + itemSlNo + "'>" + itemUOM + "</p></div>";
                                    $(`#grnItemStkQtyTdSpan_${itemSlNo}`).html(derivedQty);
                                    $(`#ItemInvoiceGoodsType_${itemSlNo}`).val("goods");

                                    console.log(storageLoc);

                                } else {

                                    storageLoc += "<select class='form-control text-xs' name='grnItemList[" + itemSlNo + "][itemStorageLocationId]' required><option value=''>Select Cost Center</option>";

                                    var objects = obj1.data;

                                    for (let i = 0; i < objects.length; i++) {
                                        storageLoc += "<option value='" + objects[i].CostCenter_id + "'>" + objects[i].CostCenter_code + " | " + objects[i].CostCenter_desc + "</option>";
                                    }

                                    storageLoc += "</select>";

                                    $(`#grnItemStrgLocTdSpan_${itemSlNo}`).html(storageLoc);
                                    // $(`#grnItemStkQtyTdSpan_${itemSlNo}`).css("display", "block");

                                    derivedQty += "<div class='form-input d-flex' style='align-items: center; gap: 7px;'><input type='number' step='any' value='" + itemQty + "' class='form-control text-xs w-50' name='grnItemList[" + itemSlNo + "][itemStockQty]'><p class='text-xs' id='grnItemUOM_" + itemSlNo + "'>" + itemUOM + "</p></div>";
                                    $(`#grnItemStkQtyTdSpan_${itemSlNo}`).html(derivedQty);
                                    $(`#ItemInvoiceGoodsType_${itemSlNo}`).val("service");

                                    $.ajax({
                                        url: "ajaxs/grn/ajax-get-tds-slab.php?tds=" + itemtds_id,
                                        type: "GET",
                                        beforeSend: function() {},
                                        success: function(val_response) {
                                            // let responseObj = JSON.parse(response);
                                            console.log("Hello World");
                                            console.log(val_response);
                                            $(`#ItemInvoiceTDSSlab_${itemSlNo}`).val(val_response);

                                        }
                                    });

                                }

                                let after_tax_apply = baseAmt * itemTaxPercent / 100;

                                let cgst_val = 0;
                                let sgst_val = 0;
                                let igst_val = 0;

                                if (vendor_code == customer_code) {
                                    cgst_val = after_tax_apply / 2;
                                    sgst_val = after_tax_apply / 2;
                                    igst_val = 0;
                                } else {
                                    igst_val = after_tax_apply;
                                }
                                cgst_val = parseFloat(cgst_val).toFixed(2);
                                sgst_val = parseFloat(sgst_val).toFixed(2);
                                igst_val = parseFloat(igst_val).toFixed(2);
                                baseAmt = parseFloat(baseAmt).toFixed(2);
                                $(`#ItemInvoiceCGST_${itemSlNo}`).val(cgst_val);
                                $(`#ItemInvoiceCGSTNew_${itemSlNo}`).val(cgst_val);
                                $(`#ItemInvoiceSGST_${itemSlNo}`).val(sgst_val);
                                $(`#ItemInvoiceSGSTNew_${itemSlNo}`).val(sgst_val);
                                $(`#ItemInvoiceIGST_${itemSlNo}`).val(igst_val);
                                $(`#ItemInvoiceIGSTNew_${itemSlNo}`).val(igst_val);
                                $(`#ItemInvoiceTotalPriceStatic_${itemSlNo}`).val(baseAmt);
                                $(`#grnItemInvoiceCGSTTdSpan_${itemSlNo}`).html(cgst_val);
                                $(`#grnItemInvoiceSGSTTdSpan_${itemSlNo}`).html(sgst_val);
                                $(`#grnItemInvoiceIGSTTdSpan_${itemSlNo}`).html(igst_val);

                                // calculateOneItemAmounts(itemSlNo);

                            }
                            setTimeout(calculateOneItemAmounts(itemSlNo), 30000);
                            console.log("Response::");
                            console.log(responseObj);
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: `${responseObj["status"]}`,
                                title: `&nbsp;${responseObj["message"]}`
                            });
                        },
                        error: function(e) {
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: `error`,
                                title: `&nbsp;Mapping failed, please try again!`
                            });
                            console.log("error: " + e.message);
                        }
                    });
                    setTimeout(calculateOneItemAmounts(itemSlNo), 30000);
                    $("#mapInvoiceItemCodeModalCloseBtn").click();
                    console.log("itemSlNo:", itemSlNo, ", itemCode:", itemCode, ", itemHSN:", itemHSN);
                }
            } else {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Please make sure vendor is registered!`
                });
            }
            $("#modalItemCodeDropDown").val("");
            $('#mapInvoiceItemCodeForm')[0].reset();
        });


        $("#changeInvoiceItemCodeForm").submit(function(e) {
            e.preventDefault();
            let vendorCode = $("#invoiceVendorCodeInput").val();
            if (vendorCode != "") {
                let faliledItemCheck = {};
                let itemFaliedCheckId = $("#modalItemCodeDropDownChange").find(':selected').data("itemid") ?? 0;

                $.ajax({
                    type: "GET",
                    url: "<?= LOCATION_URL ?>ajaxs/ajax-item-failed-acc-checking.php",
                    data: {
                        act: 'checkItem',
                        itemId: itemFaliedCheckId
                    },
                    async: false,
                    success: function(response) {
                        try {
                            let res = JSON.parse(response);
                            faliledItemCheck = res;
                        } catch (e) {
                            console.log(e);
                        }
                    }
                });

                if (faliledItemCheck.status != 'success') {
                    Swal.fire({
                        icon: faliledItemCheck.status,
                        title: "Item Status",
                        text: faliledItemCheck.message,
                        showConfirmButton: true,
                        confirmButtonText: "Okay"
                    });

                    return false;
                } else {

                    console.log("maping item code");
                    let itemSlNo = $("#modalItemSlNoChange").val();
                    let itemCode = $("#modalItemCodeDropDownChange").val();
                    let itemId = $("#modalItemCodeDropDownChange").find(':selected').data("itemid");
                    let itemHSN = $("#modalItemCodeDropDownChange").find(':selected').data("hsncode");
                    let itemUOM = $("#modalItemCodeDropDownChange").find(':selected').data("uom");
                    let itemHsnPercent = $("#modalItemCodeDropDownChange").find(':selected').data("hsnPercent");
                    let goodsType = $("#modalItemCodeDropDownChange").find(':selected').data("goods");
                    let itemtds_id = $("#modalItemCodeDropDownChange").find(':selected').data("tds");
                    let itemTaxPercent = $("#modalItemCodeDropDownChange").find(':selected').data("taxpercent");
                    let uomId = $("#modalItemCodeDropDownChange").find(':selected').data("uomid");
                    let itemTitle = ($("#modalItemDescriptionChange").val()).trim();
                    let itemName = $("#modalItemCodeDropDownChange").find(':selected').data("name");
                    let default_storage_location = $("#modalItemCodeDropDownChange").find(':selected').data("default");
                    let qa_enabled = $("#modalItemCodeDropDownChange").find(':selected').data("qualityenable");
                    let qa_location = $("#modalItemCodeDropDownChange").find(':selected').data("qalocation");
                    let itemQty = $("#modalItemQtyChange").val();
                    let baseAmt = $("#modalItemAmtChange").val();
                    console.log(itemQty);
                    let itemType;
                    let taskType = "change";

                    if (goodsType == 1 || goodsType == 4 || goodsType == 5 || goodsType == 9) {
                        itemType = "goods";
                        $(`#grnItemTdsTdInput_${itemSlNo}`).val(0);
                        $(`#ItemInvoiceTDSValue_${itemSlNo}`).val(0);
                        $(`#grnItemSettingsTdSpan_${itemSlNo}`).html(`<button type='button' class='btn-view btn btn-primary delShedulingBtn' data-toggle='modal' data-target='#deliveryScheduleModal_${itemSlNo}'><i id='statusItemBtn_${itemId}' class='statusItemBtn fa fa-cog'></i></button>`);
                    } else {
                        itemType = "service";

                        $.ajax({
                            url: "ajaxs/grn/ajax-get-tds.php?base=" + baseAmt + "&tds=" + itemtds_id,
                            type: "GET",
                            beforeSend: function() {},
                            success: function(response) {
                                let responseObj = JSON.parse(response);
                                // console.log(responseObj);
                                $(`#grnItemTdsTdInput_${itemSlNo}`).val(responseObj);
                                let tds_value = baseAmt * (responseObj / 100);

                                $(`#ItemInvoiceTDSValue_${itemSlNo}`).val(tds_value);
                                setTimeout(calculateOneItemAmounts(itemSlNo), 30000);
                            }
                        });


                    }

                    console.log(goodsType);

                    $.ajax({
                        url: "ajaxs/vendor/ajax-map-vendor-item-to-internal-code.php",
                        type: "POST",
                        data: {
                            vendorId,
                            vendorCode,
                            itemTitle,
                            itemId,
                            itemCode,
                            itemHSN,
                            itemType,
                            taskType,
                            itemUOM,
                            itemTaxPercent
                        },
                        beforeSend: function() {
                            console.log("Changing...");
                        },
                        success: function(response) {
                            let responseObj = JSON.parse(response);
                            if (responseObj["status"] == "success") {
                                let mapData = responseObj["data"];
                                if (mapData["itemType"] == "service") {
                                    $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"] + " " + "<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='" + itemSlNo + "' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a> <a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCode' data-itemrownum='" + itemSlNo + "' data-allocate='1' data-toggle='modal' data-target='#mapInvoiceItemCode'>Allocate Cost</a> ");
                                    $(`#check_box_${itemSlNo}`).css('display', 'none');
                                } else {
                                    $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"] + " " + "<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='" + itemSlNo + "' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a>");
                                    $(`#check_box_${itemSlNo}`).css('display', 'block');
                                }
                                // $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"] + " " + "<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='" + itemSlNo + "' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a>");
                                $(`#internalItemId_${itemSlNo}`).val(mapData["itemId"]);
                                $(`#internalItemCode_${itemSlNo}`).val(mapData["itemCode"]);
                                $(`#grnItemHSNTdSpan_${itemSlNo}`).html(mapData["itemHSN"]);
                                $(`#internalItemHsn_${itemSlNo}`).val(mapData["itemHSN"]);
                                $(`#grnItemUOM_${itemSlNo}`).html(mapData["itemUom"]);
                                $(`#grnItemNameTdSpan_${itemSlNo}`).html(itemName);
                                $(`#internalItemName_${itemSlNo}`).val(itemName);
                                $(`#ItemInvoiceUOMID_${itemSlNo}`).val(uomId);
                                $(`#itemtax_${itemSlNo}`).val(itemTaxPercent);
                                var itemInvoiceUnits = $(`#ItemInvoiceUnits_${itemSlNo}`).val();
                                var InternalItemUom = mapData["itemUom"];
                                if (itemInvoiceUnits.toLowerCase() == InternalItemUom.toLowerCase()) {
                                    $(`#grnItemMessage_${itemSlNo}`).html("");
                                } else {
                                    $(`#grnItemMessage_${itemSlNo}`).html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>stock keeping unit and invoice driven unit is different");
                                }
                                $(`#mapInvoiceItemCodeChange`).hide();

                                console.log(itemType);

                                var storageLoc = "";
                                var derivedQty = "";
                                if (itemType == "goods") {
                                    storageLoc += "<select class='form-control text-xs' name='grnItemList[" + itemSlNo + "][itemStorageLocationId]' required><option value=''>Select storage location</option>";

                                    var objects = obj.data;

                                    if (qa_enabled == 1) {
                                        for (let i = 0; i < objects.length; i++) {
                                            if (objects[i].storage_location_id == qa_location) {
                                                storageLoc += "<option selected value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                            } else {
                                                storageLoc += "<option value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                            }

                                        }

                                        $.ajax({
                                            url: "ajaxs/grn/ajax-fetch-bin.php?st=" + qa_location,
                                            type: "GET",
                                            beforeSend: function() {
                                                $(`.multiBatchOptions_${itemSlNo}`).html(`<option value="0">Select Bin</option></select>`);
                                            },
                                            success: function(response) {
                                                let binList = JSON.parse(response);
                                                console.log(binList);
                                                $(`.multiBatchOptions_${itemSlNo}`).html('<option value="0">Select Bin</option>' + binList);
                                                // $(`.addQtyBtnMultiOptions_${itemSlNo}`).data("optiondata",'<option value="0">Select Bin</option></select>'+binList);
                                                // $('.vendorClass_' + vendoritemSlNo).remove();
                                                $(`#grnItemAllBins_${itemSlNo}`).val(binList);
                                            },
                                            error: function(e) {
                                                console.log("error: " + e.message);
                                            }

                                        });

                                    } else {
                                        for (let i = 0; i < objects.length; i++) {
                                            if (objects[i].storage_location_id == default_storage_location) {
                                                storageLoc += "<option selected value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                            } else {
                                                storageLoc += "<option value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                            }

                                        }

                                        $.ajax({
                                            url: "ajaxs/grn/ajax-fetch-bin.php?st=" + default_storage_location,
                                            type: "GET",
                                            beforeSend: function() {
                                                $(`.multiBatchOptions_${itemSlNo}`).html(`<option value="0">Select Bin</option></select>`);
                                            },
                                            success: function(response) {
                                                let binList = JSON.parse(response);
                                                console.log(binList);
                                                $(`.multiBatchOptions_${itemSlNo}`).html('<option value="0">Select Bin</option>' + binList);
                                                // $(`.addQtyBtnMultiOptions_${itemSlNo}`).data("optiondata",'<option value="0">Select Bin</option></select>'+binList);
                                                // $('.vendorClass_' + vendoritemSlNo).remove();
                                                $(`#grnItemAllBins_${itemSlNo}`).val(binList);
                                            },
                                            error: function(e) {
                                                console.log("error: " + e.message);
                                            }

                                        });
                                    }

                                    // for (let i = 0; i < objects.length; i++) {
                                    //     storageLoc += "<option value='" + objects[i].storage_location_id + "'>" + objects[i].warehouse_code + " | " + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                    // }

                                    storageLoc += "</select>";

                                    $(`#grnItemStrgLocTdSpan_${itemSlNo}`).html(storageLoc);
                                    // $(`#grnItemStkQtyTdSpan_${itemSlNo}`).css("display", "block");
                                    derivedQty += "<div class='form-input d-flex' style='align-items: center; gap: 7px;'><input type='number' step='any' value='" + itemQty + "' class='form-control text-xs w-50' name='grnItemList[" + itemSlNo + "][itemStockQty]'><p class='text-xs' id='grnItemUOM_" + itemSlNo + "'>" + itemUOM + "</p></div>";
                                    $(`#grnItemStkQtyTdSpan_${itemSlNo}`).html(derivedQty);
                                    $(`#ItemInvoiceGoodsType_${itemSlNo}`).val("goods");

                                    console.log(storageLoc);

                                } else {
                                    storageLoc += "<select class='form-control text-xs' name='grnItemList[" + itemSlNo + "][itemStorageLocationId]' required><option value=''>Select Cost Center</option>";

                                    var objects = obj1.data;

                                    for (let i = 0; i < objects.length; i++) {
                                        storageLoc += "<option value='" + objects[i].CostCenter_id + "'>" + objects[i].CostCenter_code + " | " + objects[i].CostCenter_desc + "</option>";
                                    }

                                    storageLoc += "</select>";

                                    $(`#grnItemStrgLocTdSpan_${itemSlNo}`).html(storageLoc);
                                    // $(`#grnItemStkQtyTdSpan_${itemSlNo}`).css("display", "block");

                                    derivedQty += "<div class='form-input d-flex' style='align-items: center; gap: 7px;'><input type='number' step='any' value='" + itemQty + "' class='form-control text-xs w-50' name='grnItemList[" + itemSlNo + "][itemStockQty]'><p class='text-xs' id='grnItemUOM_" + itemSlNo + "'>" + itemUOM + "</p></div>";
                                    $(`#grnItemStkQtyTdSpan_${itemSlNo}`).html(derivedQty);
                                    $(`#ItemInvoiceGoodsType_${itemSlNo}`).val("service");

                                    $.ajax({
                                        url: "ajaxs/grn/ajax-get-tds-slab.php?tds=" + itemtds_id,
                                        type: "GET",
                                        beforeSend: function() {},
                                        success: function(val_response) {
                                            // let responseObj = JSON.parse(response);
                                            $(`#ItemInvoiceTDSSlab_${itemSlNo}`).val(val_response);

                                        }
                                    });
                                }

                                let after_tax_apply = baseAmt * itemTaxPercent / 100;

                                let cgst_val = 0;
                                let sgst_val = 0;
                                let igst_val = 0;

                                if (vendor_code == customer_code) {
                                    cgst_val = after_tax_apply / 2;
                                    sgst_val = after_tax_apply / 2;
                                    igst_val = 0;
                                } else {
                                    igst_val = after_tax_apply;
                                }
                                cgst_val = parseFloat(cgst_val).toFixed(2);
                                sgst_val = parseFloat(sgst_val).toFixed(2);
                                igst_val = parseFloat(igst_val).toFixed(2);
                                baseAmt = parseFloat(baseAmt).toFixed(2);
                                $(`#ItemInvoiceCGST_${itemSlNo}`).val(cgst_val);
                                $(`#ItemInvoiceCGSTNew_${itemSlNo}`).val(cgst_val);
                                $(`#ItemInvoiceSGST_${itemSlNo}`).val(sgst_val);
                                $(`#ItemInvoiceSGSTNew_${itemSlNo}`).val(sgst_val);
                                $(`#ItemInvoiceIGST_${itemSlNo}`).val(igst_val);
                                $(`#ItemInvoiceIGSTNew_${itemSlNo}`).val(igst_val);
                                $(`#ItemInvoiceTotalPriceStatic_${itemSlNo}`).val(baseAmt);
                                $(`#grnItemInvoiceCGSTTdSpan_${itemSlNo}`).html(cgst_val);
                                $(`#grnItemInvoiceSGSTTdSpan_${itemSlNo}`).html(sgst_val);
                                $(`#grnItemInvoiceIGSTTdSpan_${itemSlNo}`).html(igst_val);

                                // calculateOneItemAmounts(itemSlNo);

                            }
                            setTimeout(calculateOneItemAmounts(itemSlNo), 30000);
                            console.log("Response::");
                            console.log(responseObj);
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: `${responseObj["status"]}`,
                                title: `&nbsp;${responseObj["message"]}`
                            });
                        },
                        error: function(e) {
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            Toast.fire({
                                icon: `error`,
                                title: `&nbsp;Change failed, please try again!`
                            });
                            console.log("error: " + e.message);
                        }
                    });
                    setTimeout(calculateOneItemAmounts(itemSlNo), 30000);
                    $("#mapInvoiceItemCodeModalCloseBtn").click();
                    console.log("itemSlNo:", itemSlNo, ", itemCode:", itemCode, ", itemHSN:", itemHSN);
                }
            } else {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Please make sure vendor is registered!`
                });
            }
            $("#modalItemCodeDropDown").val("");
            $('#mapInvoiceItemCodeForm')[0].reset();
        });




        <?php
        if ($vendorGstin != "") {
        ?>
            $.ajax({
                url: '<?= BASE_URL ?>/branch/location/ajaxs/ajax-gst-details.php?gstin=<?= $vendorGstin ?>',
                type: 'GET',
                beforeSend: function() {
                    // <div id="vendorGstinStatusDiv"><p class="status">Active</p></div>
                    $("#vendorGstinStatus").html(`Loding...`);
                },
                success: function(responseData) {
                    responseObj = JSON.parse(responseData);
                    let gstinStatus = responseObj["data"]["sts"] ?? "Inactive";
                    $("#vendorGstinStatus").html(`${gstinStatus}`);
                }
            });

        <?php
        }
        ?>

        $(function() {
            $('#iframePreview').click(function() {
                if (!$('#iframe').length) {
                    $('#iframeHolder').html('<iframe src="<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $invoiceFile ?>" id="grnInvoicePreviewIfram" width="100%" height="100%"></iframe>');
                }
            });
        });


        $("input[name='mapInvoiceItemTypeChangeRadioBtn']").click(function() {
            var radioValue = $("input[name='mapInvoiceItemTypeChangeRadioBtn']:checked").val();
            type = radioValue;
            if (radioValue == "service") {
                $.ajax({
                    url: 'ajaxs/grn/ajax-grn-get-service.php?data=' + radioValue,
                    type: 'GET',
                    beforeSend: function() {
                        // <div id="vendorGstinStatusDiv"><p class="status">Active</p></div>
                        $("#modalItemCodeDropDownChange").html(`Loding...`);
                    },
                    success: function(responseData) {
                        responseObj = JSON.parse(responseData);
                        console.log(responseObj);
                        $("#modalItemCodeDropDownChange").html(responseObj);
                    }
                });
            } else {
                $.ajax({
                    url: 'ajaxs/grn/ajax-grn-get-service.php?data=' + radioValue,
                    type: 'GET',
                    beforeSend: function() {
                        // <div id="vendorGstinStatusDiv"><p class="status">Active</p></div>
                        $("#modalItemCodeDropDownChange").html(`Loding...`);
                    },
                    success: function(responseData) {
                        responseObj = JSON.parse(responseData);
                        console.log(responseObj);
                        $("#modalItemCodeDropDownChange").html(responseObj);
                    }
                });
            }
        });


        $("input[name='mapInvoiceItemTypeRadioBtn']").click(function() {
            var radioValue = $("input[name='mapInvoiceItemTypeRadioBtn']:checked").val();
            type = radioValue;
            if (radioValue == "service") {
                $.ajax({
                    url: 'ajaxs/grn/ajax-grn-get-service.php?data=' + radioValue,
                    type: 'GET',
                    beforeSend: function() {
                        // <div id="vendorGstinStatusDiv"><p class="status">Active</p></div>
                        $("#modalItemCodeDropDown").html(`Loding...`);
                    },
                    success: function(responseData) {
                        responseObj = JSON.parse(responseData);
                        console.log(responseObj);
                        $("#modalItemCodeDropDown").html(responseObj);
                    }
                });
            } else {
                $.ajax({
                    url: 'ajaxs/grn/ajax-grn-get-service.php?data=' + radioValue,
                    type: 'GET',
                    beforeSend: function() {
                        // <div id="vendorGstinStatusDiv"><p class="status">Active</p></div>
                        $("#modalItemCodeDropDown").html(`Loding...`);
                    },
                    success: function(responseData) {
                        responseObj = JSON.parse(responseData);
                        console.log(responseObj);
                        $("#modalItemCodeDropDown").html(responseObj);
                    }
                });
            }
        });

        function check_date() {


            let date = $("#invoicePostingDateId").val();

            let max = '<?php echo $max; ?>';
            let min = '<?php echo $min; ?>';


            if (date < min) {


                $("#postdatelabel").html("Invalid POsting Date");
                // document.getElementById("pobtn").disabled = true;
                // document.getElementById("podbtn").disabled = true;

            } else if (date > max) {
                $("#postdatelabel").html("Invalid Posting Date");
                // document.getElementById("pobtn").disabled = true;
                // document.getElementById("podbtn").disabled = true;
            } else {
                $("#postdatelabel").html("");
                // document.getElementById("pobtn").disabled = false;
                // document.getElementById("podbtn").disabled = false;

            }



        }
        $("#invoicePostingDateId").keyup(function() {
            // check_date();

        });

        $("#docNoText").keyup(function() {
            var doc_number = $("#docNoText").val();
            var vendor_id = $("#invoiceVendorIdInput").val();


            $.ajax({
                url: 'ajaxs/grn/ajax-inv-exists.php?doc=' + doc_number + '&vendor=' + vendor_id,
                type: 'GET',
                beforeSend: function() {
                    // <div id="vendorGstinStatusDiv"><p class="status">Active</p></div>
                    // $("#modalItemCodeDropDown").html(`Loding...`);
                },
                success: function(responseData) {
                    responseObj = JSON.parse(responseData);
                    console.log(responseObj);

                    if (responseObj == "true") {
                        $("#addNewGrnFormSubmitBtn").prop('disabled', true);
                        $("#documentNoValidation").html("This Invoice already exists");
                    } else {
                        $("#addNewGrnFormSubmitBtn").prop('disabled', false);
                        $("#documentNoValidation").html("");
                    }

                }
            });

        });

        $(document).on("change", ".storageLocationSelect", function() {
            let id = $(this).attr("id").split("_")[1];
            let st = $(`#itemStorageLocationId_${id}`).val();
            // alert(st);
            // $(`#grnSettingsButton_${id}`).data('storage', st);

            $.ajax({
                url: "ajaxs/grn/ajax-fetch-bin.php?st=" + st,
                type: "GET",
                beforeSend: function() {
                    $(`.multiBatchOptions_${id}`).html(`<option value="0">Select Bin</option></select>`);
                },
                success: function(response) {
                    let binList = JSON.parse(response);
                    console.log(binList);
                    $(`.multiBatchOptions_${id}`).html('<option value="0">Select Bin</option>' + binList);
                    // $(`.addQtyBtnMultiOptions_${id}`).data("optiondata",'<option value="0">Select Bin</option></select>'+binList);
                    // $('.vendorClass_' + vendorID).remove();
                    $(`#grnItemAllBins_${id}`).val(binList);
                },
                error: function(e) {
                    console.log("error: " + e.message);
                }

            });


        });

        //Multi row

        function calculateAndCheckBatchQuantity(rowNo, inputElement = null) {
            let totalQty = 0;
            $(`.multiBatchRowQuantity_${rowNo}`).each(function(index) {
                if (index > 0) {
                    totalQty += Number($(this).val());
                }
            });
            let itemQty = (parseFloat($(`#totalItemAmountModal_${rowNo}`).html()) > 0) ? parseFloat($(`#totalItemAmountModal_${rowNo}`).html()) : 0;
            let remainQuantity = itemQty - totalQty;
            if (remainQuantity > 0) {
                $(`#multiQuantity_${rowNo}`).val(remainQuantity);
            } else {
                if (inputElement != null) {
                    inputElement.val(0);
                }
            }
        }

        $(document).on("change", ".grnEnableCheckBxClass", function() {

            // let value = $(`#grnEnableCheckBx_${id}`).is(":checked");
            let id = $(this).attr("id").split("_")[1];
            if ($(this).is(':checked') && $(this).val() == 1) {
                $(`.multiBatchRowNumber_${id}`).removeAttr('readOnly');
                $(`.multiBatchRowNumber_${id}`).val("");
            } else {
                $(`.multiBatchRowNumber_${id}`).prop("readOnly", true);
                $(`.multiBatchRowNumber_${id}`).val("GRNXXXXXXXXX");
            }
            // alert(id);
        });


        // $(document).on("keyup", ".multiBatch", function() {
        //     let rowNo = $(this).data("itemid");
        //     alert(rowNo);
        //     checkEmpty(rowNo);
        // });

        $(document).on("keyup", ".multiQuantity", function() {
            let rowNo = $(this).data("itemid");

            console.warn("Recalculating the batch quantity!", rowNo);
            calculateAndCheckBatchQuantity(rowNo, $(this));
        });


        let defaultMultiBatchRows = JSON.parse(`<?= json_encode($defaultMultiBatchRows, true) ?>`);
        defaultMultiBatchRows.map(function(item) {
            var binlistHtml = $(`#grnItemAllBins_${item.sl}`).val();
            addGrnItemMultipleBatch(item.sl, item.qty, true, binlistHtml);
        });

        $(document).on("click", ".addQtyBtn", function() {
            let id = $(this).attr("id").split("_")[1];
            console.log("Appending new row!!!!", id);

            var binlistHtml = $(`#grnItemAllBins_${id}`).val();
            console.log(binlistHtml);

            addGrnItemMultipleBatch(id, 0, false, binlistHtml);

            // $(`.multiBatchOptions_${id}`).html(binlistHtml);
        });

        $(document).on("click", ".dlt-popup", function() {
            let id = $(this).attr("id").split("_")[1];
            let rowNo = $(this).data("rowno");
            let value = Number($(`#multiQuantity_${rowNo}`).val());
            let val = Number($(`#multiQuantity_${id}`).val());
            let remainQuantity = val + value;
            $(`#multiQuantity_${id}`).val(remainQuantity);

            $(this).parent().parent().remove();
        });


    });
</script>

<script>
    $(document).ready(function() {
        $(".grnTable").DataTable({
            "searching": true
        })
    })
</script>

<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>


<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#itemsDropDown').select2({
            placeholder: "Select Item",
            allowClear: true
        });

        // Toggle Show/Hide on Hamburger Click
        $('.quickadd-hamburger').click(function() {
            $('#itemSelect').toggle(); // Toggle the visibility
        });
    });
</script>


<script src="<?= BASE_URL; ?>public/validations/pendingGrnValidation.js"></script>