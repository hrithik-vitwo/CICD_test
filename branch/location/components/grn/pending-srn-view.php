<?php

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

        // return $vendorItemTitle;


        $goodsHsnObj = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` as goods LEFT JOIN `erp_hsn_code` as hsn ON goods.hsnCode=hsn.hsnCode WHERE goods.company_id='" . $company_id . "' AND goods.itemId='" . $item_id . "'");
        if ($goodsHsnObj["status"] == "success") {

            // return $goodsHsnObj["data"]["itemName"];

            $baseunitmeasure = $goodsHsnObj["data"]["baseUnitMeasure"];
            $tds_id = $goodsHsnObj["data"]["tds"];

            $getUOM = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $baseunitmeasure . "'");

            $getTds = queryGet("SELECT `TDSRate`,`slab_serialized` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");
            $slab = unserialize($getTds["data"]["slab_serialized"]);

            $percentage = getSlabPercentage($baseAmt, $slab);

            if ($getUOM["status"] == "success") {
                return [
                    "itemCode" => $itemCode,
                    "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                    "itemId" => $goodsHsnObj["data"]["itemId"],
                    "itemName" => $goodsHsnObj["data"]["itemName"],
                    "tax" => $goodsHsnObj["data"]["taxPercentage"],
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
                "itemId" => "",
                "tax" => "",
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
            "itemName" => "",
            "type" => ""
        ];
    }
}



$id = $_GET["view"];
$grnNo = "SRN" . time() . rand(100, 999);

$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];

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
} else {
    $ven_details = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id` = '" . $company_id . "' AND `company_branch_id`='" . $branch_id . "' AND `location_id`='" . $location_id . "' AND `vendor_id`='" . $vendorId . "'", false);
    $vendorCode = $ven_details["data"]["vendor_code"];
    $vendorName = $ven_details["data"]["trade_name"] ?? "";
    $vendorCreditPeriod = $ven_details["data"]["vendor_credit_period"];
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
$getCostCenterListForGrnObj = getCostCenterListForGrn();
?>

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
                            <input type="hidden" name="grnType" value="srn">
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
                                    $check_doc_no_query = queryGet("SELECT * FROM `erp_grn` WHERE `companyId`='" . $company_id . "' AND `vendorDocumentNo` = '" . $documentNo . "' AND `vendorId`='" . $vendorId . "' AND `grnStatus`='active'");;
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
                                <input type="date" name="invoiceDueDate" value="<?= $dueDate ?>" class="form-control" required>
                            </div>
                            <div class="display-flex grn-form-input-text">
                                <i class="fa fa-check"></i>
                                &nbsp;
                                <p class="label-bold">PO Number :</p>
                                &nbsp;
                                <input type="text" name="invoicePoNumber" id="invoicePoNumber" value="<?= $customerPurchaseOrder ?>" class="form-control">
                            </div>



                            <div class="display-flex"><i class="fa fa-check"></i>
                                <p class="label-bold">&nbsp;Functional Area : </p>
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
                            <?php
                            if ($vendorCode != "") {
                            ?>
                                <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Status :&nbsp;</p>
                                    <p class="status">Active</p>
                                </div>
                            <?php
                            }
                            ?>
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
                            <iframe src='<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $invoiceFile ?>#view=fitH' id="grnInvoicePreviewIfram" width="100%" height="220"></iframe>
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
                                            <a class="btn btn-sm btn-primary quick-add-vendor" data-toggle="modal" data-target="#dialogForVendorQuickAdd">Quick Add</a>
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
                                        <li>
                                            <button id="refresh_po_list" class="btn btn-primary select-po float-right mt-3">Refresh</button>
                                        </li>
                                        <br>
                                        <br>

                                        <table class="table-sales-order srnTable table defaultDataTable grn-table">
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
                                                            <a class="btn btn-sm btn-xs btn-secondary ml-2 vendorListClass" data-id="<?= $eachvendor["vendor_id"] ?>" data-name="<?= $eachvendor["trade_name"] ?>" data-code="<?= $eachvendor["vendor_code"] ?>" data-toggle="modal" data-target="#vendor_confirmation_modal">Map Vendor</i></a>
                                                        </td>
                                                    </tr>

                                                    <div class="modal invoice-iframe" id="vendor_confirmation_modal">
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
                                        <button id="refresh_po_match" class="btn btn-primary select-po float-right mt-3">Refresh</button>
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


                                            foreach ($invoiceData["Items"] as $oneItemObj) {

                                                $itemName = $oneItemObj["Description"];

                                                if ($vendorCode != "") {
                                                    $itemCodeAndHsnObj = getItemCodeAndHsn($vendorCode, $itemName);
                                                    //  console($oneItemData["Description"]);
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
                                                            $quantity = $poItem["qty"];
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
                                                                if ($oneItemObj["Quantity"] == $poItem["qty"]) {
                                                                    $po_date = $poItem["po_date"];
                                                                    $match = "Matched";
                                                                    array_push($check_array, "1");
                                                                    array_push($check_array_1, "1");
                                                                    break;
                                                                } elseif ($oneItemObj["Quantity"] < $poItem["qty"]) {
                                                                    array_push($check_array, "1");
                                                                    array_push($check_array_1, "0");
                                                                    continue;
                                                                } elseif ($oneItemObj["Quantity"] > $poItem["qty"]) {
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
                                        <button id="refresh_po_list" class="btn btn-primary select-po float-right mt-3">Refresh</button>
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
                                                        <div class="modal invoice-iframe" id="po_items" tabindex="-1" aria-labelledby="exampleModal2Label" aria-hidden="true" data-bs-keyboard="true">
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
                                                    <td><input type="number" name="" step="any" class="form-control" id="removedItemQuantity_<?= $removedItemSl ?>" value="<?= $removeItemQuantity ?>"></td>
                                                    <td><input type="number" name="" step="any" class="form-control" id="removedItemUnitPrice_<?= $removedItemSl ?>" value="<?= $removeItemUnitPrice ?>"></td>
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

              
                    

                    <label for="">Cost Center</label>

                    <?php
                    $funcList = $BranchPoObj->fetchFunctionality()['data'];
                    foreach ($funcList as $data) {
                        $rand = rand(10, 100);
                    ?>
                                <div class="col-lg-6 col-md-6 col-sm-6">

                                    <div class="form-input">
                                        <input type="text" name="cost_center[<?= $rand ?>]['costcenter']" class="form-control costCenterName" value="<?= $data['CostCenter_code'] ?>" readonly>
                                        <input type="hidden" name="cost_center[<?= $rand ?>]['costcenter_id']" class="form-control costCenterName" value="<?= $data['CostCenter_id'] ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">

                                    <div class="form-input">
                                        <input type="text" name="cost_center[<?= $rand ?>]['rate']" class="form-control costCenterRate">
                                    </div>
                                </div>
                            <?php
                        }
                            ?>

                    </select>

                </div>
            </div>

        </div>
    </div> -->

    <div class="grn-table pending-grn-view">
        <table class="table-sales-order table defaultDataTable grn-table">
            <thead>
                <tr>
                    <th>Sl No.</th>
                    <th>Service Name</th>
                    <th>Service Code</th>
                    <th>Service HSN</th>
                    <th>Cost Center</th>
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
                foreach ($invoiceData["Items"] as $oneItemObj) {

                    $oneItemData = $oneItemObj;

                    $itemHSN = "";
                    $tax = 0;
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
                    $rand = rand(100, 1000);

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
                        $internalItemUom = $itemCodeAndHsnObj["uom"];
                        $internalItemuom_id = $itemCodeAndHsnObj["uom_id"];
                        $itemType = $itemCodeAndHsnObj["type"];
                        $itemHSN = $itemCodeAndHsnObj["itemHsn"];
                        $itemName = $itemCodeAndHsnObj["itemName"];
                        $tds = $itemCodeAndHsnObj["tds"];
                        $tax = $itemCodeAndHsnObj["tax"];
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

                    <tr id="grnItemRowTr_<?= $sl ?>">
                        <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                        <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                        <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                        <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                        <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemQty]" value="<?= $itemQty ?>" />
                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                        <!-- <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                        <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                        <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                        <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSValue]" value="<?= $tds_value ?>" />
                        <input type="hidden" class="ItemInvoiceTDSSlab" id="ItemInvoiceTDSSlab_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSSlab]" value='<?= json_encode($slab) ?>' />
                        <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
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



                        <td><?= $sl ?></td>
                        <td id="grnItemNameTdSpan_<?= $sl ?>"><?= $itemName ?></td>
                        <td class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                            <?php
                            if ($postStatus != 0) {
                                echo $internalItemCode;
                            } else {
                                if ($internalItemCode == "") {
                                    echo '<a class="btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCode" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCode">Map Code</i></a>';
                                } else {
                                    echo $internalItemCode;
                                    echo '<a class="btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCodeChange"><i class="fas fa-pencil-alt"></i></a>';
                                }
                            }
                            ?>
                        </td>
                        <td class="grnItemHSNTdSpan" id="grnItemHSNTdSpan_<?= $sl ?>"><?= $itemHSN ?></td>
                        <td id="grnItemStrgLocTdSpan_<?= $sl ?>" class="storageSelect">
                            <select class="form-control text-xs itemCostCenterId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" required>
                                <option value="">Select Cost Center</option>
                                <?php
                                foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                    echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                                }
                                ?>
                            </select>

                            <!-- <button id="cstcntr_btn_<?= $sl ?>" type="button" class="btn btn-info btn-lg cstcntr_btn" data-toggle="modal" data-target="#myModal_<?= $sl ?>">Select Cost Center</button> -->
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
                        <td>
                            <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                <input step="any" type="number" name="grnItemList[<?= $sl ?>][itemTds]" value="<?= $tds ?? 0 ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-center p-0 px-2 text-xs itemTds border-0" style="width: 30px !important;" required>
                                <p class="text-xs">%</p>
                            </div>
                        </td>
                        <input type="hidden" value="<?= $tax ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">
                        <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </span>
                        <td class="text-right" id="grnItemDeleteTdSpan_<?= $sl ?>"><button title="Delete Item" type="button" id="grnItemDeleteButton_<?= $sl ?>" class="btn btn-sm remove_row" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button></td>
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
                        </td>
                        <td class="bg-transparent"></td>
                        <td colspan="3" class="bg-transparent">
                            <?php // if ((float)$itemTotalPrice != (float)$Total) {echo "<span class='error calculate-error'>".$itemTotalPrice." is the difference</span>"; } 
                            ?>
                        </td>
                    </tr>

                    <!-- <div class="modal fade" id="myModal_<?= $sl ?>" role="dialog" data-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">

                                    <h4 class="modal-title">Select Cost Centers</h4>
                                    <?= $sl ?>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                    foreach ($funcList as $data) {
                                        $rand = rand(10, 100);
                                    ?>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6">

                                                <div class="form-input">
                                                    <input type="text" name="grnItemList[<?= $sl ?>][cost_center][<?= $rand ?>][code]" class="form-control costCenterName" value="<?= $data['CostCenter_code'] ?>" readonly>
                                                    <input type="hidden" name="grnItemList[<?= $sl ?>][cost_center][<?= $rand ?>][id]" class="form-control costCenterName" value="<?= $data['CostCenter_id'] ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6">

                                                <div class="form-input">
                                                    <input type="text" name="grnItemList[<?= $sl ?>][cost_center][<?= $rand ?>][rate]" class="form-control cstcntr_rate">
                                                     costCenterRate
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="modal-footer">
                                    <p id="modalAmount_<?= $sl ?>">Total Amount: <?= $itemUnitPrice * $itemQty ?></p>
                                    <button id="modalButton_<?= $sl ?>" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div> -->

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
                            <td colspan="9" style="background: none;">Sub Total</td>
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

                        if ($vendorGstinStateCode == $customerGstinStateCode) {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total CGST</td>
                                <td class="text-right" style="background: none;" id="grandCgstTd"><?= $comp_currency . ": " . number_format($totalCGST, 2) ?></td>
                            </tr>
                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total SGST</td>
                                <td class="text-right" style="background: none;" id="grandSgstTd"><?= $comp_currency . ": " . number_format($totalSGST, 2) ?></td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total IGST</td>
                                <td class="text-right" style="background: none;" id="grandIgstTd"><?= $comp_currency . ": " . number_format($totalIGST, 2) ?></td>
                            </tr>
                        <?php
                        }

                        ?>
                        <tr class="itemTotals">
                            <td colspan="9" style="background: none;">Total TDS</td>
                            <td class="text-right" id="grandTds" style="background: none;"><?= $comp_currency . ": -" . number_format($totalTdsValue, 2) ?></td>
                        </tr>
                        <tr class="itemTotals">
                            <input type="hidden" id="totalCGST" name="totalInvoiceCGST" value="<?= $totalCGST ?>">
                            <input type="hidden" id="totalSGST" name="totalInvoiceSGST" value="<?= $totalSGST ?>">
                            <input type="hidden" id="totalIGST" name="totalInvoiceIGST" value="<?= $totalIGST ?>">
                            <input type="hidden" id="totalTDS" name="totalInvoiceTDS" value="<?= $totalTdsValue ?>">
                            <input type="hidden" id="grandSubTotal" name="totalInvoiceSubTotal" value="<?= $totalSubtotal ?>">
                            <input type="hidden" id="grandTotal" name="totalInvoiceTotal" value="<?= $toalTotal ?>">
                            <td colspan="9" class="font-bold" style="background: none;">Total Amount</td>
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
            ?>
                <button type="submit" id="addNewGrnFormSubmitBtn" value="Submit GRN" class="btn btn-primary float-right mt-3 mb-3">Submit SRN</button>
            <?php
            } else {
            ?>
                <button type="submit" id="addNewGrnFormSubmitBtn" value="Submit GRN" disabled class="btn btn-primary float-right mt-3 mb-3">Submit SRN</button>
            <?php
            }
            ?>

        <?php
        }
        ?>

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
                    <!-- <input type="hidden" name="pendingGrnId" value="<?= $_GET["view"] ?>">
                    <label for="">Vendor Name</label>
                    <input type="text" name="vendorName" value="<?= $vendorName ?>" class="form-control" required>
                    <label for="">Vendor Gstin</label>
                    <input type="text" name="vendorGstin" value="<?= $vendorGstin ?>" class="form-control">
                    <label for="">Vendor Pan</label>
                    <input type="text" name="vendorPan" value="<?= $vendorPan ?>" class="form-control">
                    <label for="">Credit Period (days)</label>
                    <input type="number" name="creditPeriod" placeholder="E.g 30" class="form-control" required />
                    <div class="form-check ml-1">
                        <label class="form-check-label">
                            <input type="checkbox" name="notifyConcernPerson" checked class="form-check-input" value=""> <i class="fas fa-envelope"></i> Notify conserned person!
                        </label>
                    </div>
                    <hr>
                    <div class="col-md-12">
                        <div class="input-group btn-col">
                            <button type="submit" class="btn btn-primary btnstyle" id="vendorQuickAddFormSubmitBtn">Add Vendor</button>
                        </div>
                    </div> -->
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
                <h5 class="modal-title text-sm py-2 text-white">Map Item</h5>
                <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="mapInvoiceItemCodeForm">
                    <div class="form-input my-2">
                        <input type="hidden" name="modalItemSlNo" id="modalItemSlNo" value="0">
                        <label">Service Description</label>
                            <textarea name="modalItemDescription" id="modalItemDescription" cols="1" rows="3" class="form-control" readonly></textarea>
                    </div>
                    <div class="form-input my-2">
                        <input type="hidden" name="modalItemQtyMap" id="modalItemQtyMap">
                        <input type="hidden" name="modalItemAmt" id="modalItemAmt" value="0">
                        <label">Select Service Code</label>
                            <select class="form-control" name="modalItemCode" id="modalItemCodeDropDown" required>
                                <?php
                                $goodsController = new GoodsController();
                                $rmGoodsObj = $goodsController->getAllGRNServices();
                                if ($rmGoodsObj["status"] == "success") {
                                    echo '<option value="" data-hsncode="" data-itemtitle="">Select Service</option>';
                                    foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                                ?>
                                        <option value="<?= $oneRmGoods["itemCode"]; ?>" data-taxpercent="<?= $oneRmGoods["taxPercentage"]; ?>" data-tds="<?= $oneRmGoods["tds"]; ?>" data-name="<?= $oneRmGoods["itemName"]; ?>" data-uom="<?= $oneRmGoods["uomName"]; ?>" data-itemid="<?= $oneRmGoods["itemId"]; ?>" data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>" data-itemtitle="<?= $oneRmGoods["itemName"]; ?>"><?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["taxPercentage"] . " %"; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                    </div>
                    <button type="submit" name="mapItemCodeFormSubmitBtn" class="btn btn-primary btnstyle my-2">Map Code</button>
                </form>
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
                        <label>Service Description</label>
                        <textarea name="modalItemDescriptionChange" id="modalItemDescriptionChange" cols="1" rows="3" class="form-control" readonly></textarea>
                    </div>
                    <div class="form-input my-2">
                        <input type="hidden" name="modalItemQtyChange" id="modalItemQtyChange">
                        <input type="hidden" name="modalItemAmtChange" id="modalItemAmtChange" value="0">
                        <label>Select Service Code</label>
                        <select class="form-control" name="modalItemCodeChange" id="modalItemCodeDropDownChange" required>
                            <?php
                            $goodsController = new GoodsController();
                            $rmGoodsObj = $goodsController->getAllGRNServices();
                            if ($rmGoodsObj["status"] == "success") {
                                echo '<option value="" data-hsncode="" data-itemtitle="">Select Service</option>';
                                foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                            ?>
                                    <option value="<?= $oneRmGoods["itemCode"]; ?>" data-taxpercent="<?= $oneRmGoods["taxPercentage"]; ?>" data-tds="<?= $oneRmGoods["tds"]; ?>" data-name="<?= $oneRmGoods["itemName"]; ?>" data-uom="<?= $oneRmGoods["uomName"]; ?>" data-itemid="<?= $oneRmGoods["itemId"]; ?>" data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>" data-itemtitle="<?= $oneRmGoods["itemName"]; ?>"><?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["taxPercentage"] . " %"; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <button type="submit" name="mapItemCodeFormSubmitBtn" class="btn btn-primary btnstyle my-2">Change Code</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- modal end -->





</div>





<?php
// } else {
//     swalAlert('warning', 'Warning', 'Wrong attempt, IV Posted already!', LOCATION_URL . 'manage-pending-grn.php');
// }
?>

</div>

<script>
    $(document).ready(function() {
        console.log("hello there!");
        var type = "service";
        var obj = <?= json_encode($getCostCenterListForGrnObj) ?>;
        var id = <?= json_encode($id) ?>;
        var vendor_code = <?= json_encode($vendorGstinStateCode) ?>;
        var customer_code = <?= json_encode($customerGstinStateCode) ?>;
        var company_currency = <?= json_encode($comp_currency)  ?>;
        var serial_number = <?= json_encode($sl) ?>;
        var vendorGstNo = <?= json_encode($vendorGstin) ?>;


        $(document).on("keyup", ".itemUnitPrice", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];

            var tdsslab = $(`#ItemInvoiceTDSSlab_${rowNo}`).val();
            var arrayValue = JSON.parse(tdsslab);

            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;

            let baseAmt = itemQty * itemUnitPrice;

            var percentage = getSlabPercentage(baseAmt, arrayValue);

            $(`#grnItemTdsTdInput_${rowNo}`).val(percentage);
            let tds_value = baseAmt * (percentage / 100);

            $(`#ItemInvoiceTDSValue_${rowNo}`).val(tds_value);

            calculateOneItemAmounts(rowNo);
        });



        $(document).on("keyup", ".received_quantity", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            var tdsslab = $(`#ItemInvoiceTDSSlab_${rowNo}`).val();
            var arrayValue = JSON.parse(tdsslab);

            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;

            let baseAmt = itemQty * itemUnitPrice;

            var percentage = getSlabPercentage(baseAmt, arrayValue);

            $(`#grnItemTdsTdInput_${rowNo}`).val(percentage);
            let tds_value = baseAmt * (percentage / 100);

            $(`#ItemInvoiceTDSValue_${rowNo}`).val(tds_value);
            calculateOneItemAmounts(rowNo);

            console.log("End");
        });

        $(document).on("keyup", ".itemTds", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        function calculateOneItemAmounts(rowNo) {
            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;
            let cgst = (parseFloat($(`#ItemInvoiceCGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceCGSTNew_${rowNo}`).val()) : 0;
            let sgst = (parseFloat($(`#ItemInvoiceSGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceSGSTNew_${rowNo}`).val()) : 0;
            let igst = (parseFloat($(`#ItemInvoiceIGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceIGSTNew_${rowNo}`).val()) : 0;
            let tds = (parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) : 0;
            let tax = (parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) : 0;
            let itemStaticPrice = (parseFloat($(`#ItemInvoiceTotalPriceStatic_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceTotalPriceStatic_${rowNo}`).val()) : 0;

            let basicPrice = itemUnitPrice * itemQty;

            let cgstPercent = (cgst / itemStaticPrice) * 100;
            let sgstPercent = (sgst / itemStaticPrice) * 100;
            let igstPercent = (igst / itemStaticPrice) * 100;

            cgstPercent = isNaN(cgstPercent) ? 0 : cgstPercent;
            sgstPercent = isNaN(sgstPercent) ? 0 : sgstPercent;
            igstPercent = isNaN(igstPercent) ? 0 : igstPercent;

            let cgst_value = basicPrice * (cgstPercent / 100);
            let sgst_value = basicPrice * (sgstPercent / 100);
            let igst_value = basicPrice * (igstPercent / 100);

            let tds_value = basicPrice * (tds / 100);

            cgst_value = isNaN(cgst_value) ? 0 : cgst_value;
            sgst_value = isNaN(sgst_value) ? 0 : sgst_value;
            igst_value = isNaN(igst_value) ? 0 : igst_value;
            tds_value = isNaN(tds_value) ? 0 : tds_value;

            let totalItemPrice = basicPrice + cgst_value + sgst_value + igst_value - tds_value;

            let tax_value = basicPrice + (basicPrice * tax / 100);

            // console.log(totalItemPrice, cgst_value, sgst_value, igst_value);

            var curr_name = $("#selectCurrency").find(':selected').data("currname");
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            $(`#grnItemUnitPriceInrhidden_${rowNo}`).val(`${(itemUnitPrice / currency_rate_value).toFixed(2)}`);
            $(`#spanUnitPriceINR_${rowNo}`).html('');

            $(`#grnItemInvoiceTotalPriceTdSpan_${rowNo}`).html(totalItemPrice.toFixed(2));
            $(`#grnItemInvoiceBaseAmtTdSpan_${rowNo}`).html(`${curr_name}: ${(basicPrice).toFixed(2)}` + '<p class="text-small spanBasePriceINR" id="spanBasePriceINR_' + rowNo + '"></p>');
            $(`#ItemInvoiceTotalPrice_${rowNo}`).val((basicPrice / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceGrandTotalPrice_${rowNo}`).val((totalItemPrice / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceTDSValue_${rowNo}`).val(tds_value / currency_rate_value);
            $(`#grnItemInternalTaxValue_${rowNo}`).val((tax_value / currency_rate_value).toFixed(2));
            //have to change
            $(`#ItemInvoiceCGST_${rowNo}`).val((cgst_value / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceSGST_${rowNo}`).val((sgst_value / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceIGST_${rowNo}`).val((igst_value / currency_rate_value).toFixed(2));

            console.log($(`#ItemInvoiceCGST_${rowNo}`).val(), $(`#ItemInvoiceSGST_${rowNo}`).val(), $(`#ItemInvoiceIGST_${rowNo}`).val());

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
            let totalTds = 0;
            let totalInternalTax = 0;
            let totalInternalTaxValue = 0;
            // $(".ItemInvoiceGrandTotalPrice").each(function() {
            //     totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // });
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            // console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
            // let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;

            $(".ItemInvoiceTDSValue").each(function() {
                totalTds += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                console.log("TDS = ", parseFloat($(this).val()));
            });


            $(".itemInternalTax").each(function() {
                totalInternalTax += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemInternalTaxValue").each(function() {
                totalInternalTaxValue += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });


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


            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - totalTds;

            let alltax = ToTalcgst + ToTalsgst + ToTaligst;

            let getpercent = (parseFloat((alltax / grandSubTotalAmt) * 100) > 0) ? parseFloat((alltax / grandSubTotalAmt) * 100) : 0;

            // console.log(totalInternalTax);

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



            if (totalInternalTaxValue != totalAmount) {
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

            $("#grandCgstTd").html(`${curr_name}: ${(cgstDeduct * currency_rate_value).toFixed(2)}` + '<p class="text-small spanCgstGrandINR" id="spanCgstGrandINR"></p>');
            $("#grandSgstTd").html(`${curr_name}: ${(sgstDeduct * currency_rate_value).toFixed(2)}` + '<p class="text-small spanSgstGrandINR" id="spanSgstGrandINR"></p>');
            $("#grandIgstTd").html(`${curr_name}: ${(igstDeduct * currency_rate_value).toFixed(2)}` + '<p class="text-small spanIgstGrandINR" id="spanIgstGrandINR"></p>');

            $("#totalCGST").val((cgstDeduct).toFixed(2));
            $("#totalSGST").val((sgstDeduct).toFixed(2));
            $("#totalIGST").val((igstDeduct).toFixed(2));

            $("#grandTds").html(`${curr_name}: ` + "-" + (tdsDeduct * currency_rate_value).toFixed(2) + '<p class="text-small spangrandTDSINR" id="spangrandTDSINR"></p>');
            $("#totalTDS").val((tdsDeduct).toFixed(2));

            if (company_currency != curr_name) {
                $(`#spanSubTotalINR`).html(`${company_currency}: ${(grandSubTotalAmt).toFixed(2)}`);
                $(`#spangrandTotalINR`).html(`${company_currency}: ${(totalAmount).toFixed(2)}`);
                $(`#spangrandTDSINR`).html(`${company_currency}: ${(tdsDeduct).toFixed(2)}`);
                $(`#spanCgstGrandINR`).html(`${company_currency}: ${(cgstDeduct).toFixed(2)}`);
                $(`#spanSgstGrandINR`).html(`${company_currency}: ${(sgstDeduct).toFixed(2)}`);
                $(`#spanIgstGrandINR`).html(`${company_currency}: ${(igstDeduct).toFixed(2)}`);
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

        $(document).on("click", ".removedItemAdd", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];

            let removeItemName = $(`#removedItemName_${rowNo}`).val();
            let removeItemHsn = $(`#removedItemHsn_${rowNo}`).val();
            let removeItemQuantity = $(`#removedItemQuantity_${rowNo}`).val();
            let removeItemUnitPrice = $(`#removedItemUnitPrice_${rowNo}`).val();
            let removeItemBasicPrice = $(`#removedItemBasicPrice_${rowNo}`).val();
            let removeItemTax = $(`#removedItemTax_${rowNo}`).val();

            $.ajax({
                url: "ajaxs/grn/ajax-removed-item-srn.php?serial_number=" + serial_number + "&itemsName=" + removeItemName + "&removeItemQuantity=" + removeItemQuantity + "&removeItemUnitPrice=" + removeItemUnitPrice + "&removeItemTax=" + removeItemTax + "&removeItemBasicPrice=" + removeItemBasicPrice,
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



        $("#modalItemCodeDropDown").select2({
            dropdownParent: $("#mapInvoiceItemCode")
        });

        $("#modalItemCodeDropDownChange").select2({
            dropdownParent: $("#mapInvoiceItemCodeChange")
        });

        //$("#modalItemCodeDropDown").select2();

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
                    $("#vendor_confirmation_modal").hide();

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
        });

        $("#vendorNo").click(function() {
            $("#vendor_confirmation_modal").hide();
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

        $(document).on("click", ".openModalMapInvoiceItemCode", function() {
            let itemSlNo = $(this).data("itemrownum");
            let itemDescription = ($(`#ItemGRNName_${itemSlNo}`).val()).trim();
            let itemQty = $(`#grnItemQty_${itemSlNo}`).val();
            let itemAmt = $(`#ItemInvoiceTotalPrice_${itemSlNo}`).val();
            console.log(itemQty);
            $("#modalItemQtyMap").val(itemQty);
            $("#modalItemDescription").val(itemDescription);
            $("#modalItemAmt").val(itemAmt);
            $("#modalItemSlNo").val(itemSlNo);
            $('#modalItemCodeDropDown').prop('selectedIndex', 0);
        });

        $(document).on("click", ".openModalMapInvoiceItemCodeChange", function() {
            let itemSlNo = $(this).data("itemrownum");
            let itemDescription = ($(`#ItemGRNName_${itemSlNo}`).val()).trim();
            let itemQty = $(`#grnItemQty_${itemSlNo}`).val();
            let itemAmt = $(`#ItemInvoiceTotalPrice_${itemSlNo}`).val();
            console.log(itemQty);
            console.log(itemSlNo);
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
                console.log("maping item code");
                let itemSlNo = $("#modalItemSlNo").val();
                let itemCode = $("#modalItemCodeDropDown").val();
                let itemId = $("#modalItemCodeDropDown").find(':selected').data("itemid");
                let itemHSN = $("#modalItemCodeDropDown").find(':selected').data("hsncode");
                let itemUOM = $("#modalItemCodeDropDown").find(':selected').data("uom");
                let itemtds_id = $("#modalItemCodeDropDown").find(':selected').data("tds");
                let itemName = $("#modalItemCodeDropDown").find(':selected').data("name");
                let itemTaxPercent = $("#modalItemCodeDropDown").find(':selected').data("taxpercent");
                let itemTitle = ($("#modalItemDescription").val()).trim();
                let itemQty = $("#modalItemQtyMap").val();
                let baseAmt = $("#modalItemAmt").val();
                let itemType = type;
                let taskType = "map";

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

                $.ajax({
                    url: "ajaxs/grn/ajax-get-tds-slab.php?tds=" + itemtds_id,
                    type: "GET",
                    beforeSend: function() {},
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        $(`#ItemInvoiceTDSSlab_${itemSlNo}`).val(responseObj);

                    }
                });


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
                        itemUOM
                    },
                    beforeSend: function() {
                        console.log("Mapping...");
                    },
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        if (responseObj["status"] == "success") {
                            let mapData = responseObj["data"];
                            $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"] + " " + "<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='" + itemSlNo + "' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a>");
                            $(`#internalItemId_${itemSlNo}`).val(mapData["itemId"]);
                            $(`#internalItemCode_${itemSlNo}`).val(mapData["itemCode"]);
                            $(`#grnItemHSNTdSpan_${itemSlNo}`).html(mapData["itemHSN"]);
                            $(`#internalItemHsn_${itemSlNo}`).val(mapData["itemHSN"]);
                            $(`#grnItemUOM_${itemSlNo}`).html(mapData["itemUom"]);
                            $(`#grnItemNameTdSpan_${itemSlNo}`).html(itemName);
                            $(`#internalItemName_${itemSlNo}`).val(itemName);
                            // $(`#grnItemTdsTdInput_${itemSlNo}`).val(itemtds);
                            var itemInvoiceUnits = $(`#ItemInvoiceUnits_${itemSlNo}`).val();
                            var InternalItemUom = mapData["itemUom"];

                            if (itemInvoiceUnits.toLowerCase() == InternalItemUom.toLowerCase()) {
                                $(`#grnItemMessage_${itemSlNo}`).html("");
                            } else {
                                $(`#grnItemMessage_${itemSlNo}`).html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>stock keeping unit and invoice driven unit is different");
                            }

                            $.ajax({
                                url: "ajaxs/grn/ajax-get-tds-slab.php?tds=" + itemtds_id,
                                type: "GET",
                                beforeSend: function() {},
                                success: function(val_response) {
                                    // let responseObj = JSON.parse(response);
                                    $(`#ItemInvoiceTDSSlab_${itemSlNo}`).val(val_response);

                                }
                            });

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

                            $(`#mapInvoiceItemCode`).hide();

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
                console.log("changing item code");
                let itemSlNo = $("#modalItemSlNoChange").val();
                let itemCode = $("#modalItemCodeDropDownChange").val();
                let itemId = $("#modalItemCodeDropDownChange").find(':selected').data("itemid");
                let itemHSN = $("#modalItemCodeDropDownChange").find(':selected').data("hsncode");
                let itemUOM = $("#modalItemCodeDropDownChange").find(':selected').data("uom");
                let itemtds_id = $("#modalItemCodeDropDownChange").find(':selected').data("tds");
                let itemTaxPercent = $("#modalItemCodeDropDownChange").find(':selected').data("taxpercent");
                let itemTitle = ($("#modalItemDescriptionChange").val()).trim();
                let itemName = $("#modalItemCodeDropDownChange").find(':selected').data("name");
                let itemQty = $("#modalItemQtyChange").val();
                let baseAmt = $("#modalItemAmtChange").val();
                console.log(itemId);
                let itemType = type;
                let taskType = "change";

                // console.log(itemId);


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
                        itemUOM
                    },
                    beforeSend: function() {
                        console.log("Changing...");
                    },
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        if (responseObj["status"] == "success") {
                            let mapData = responseObj["data"];
                            $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"] + " " + "<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='" + itemSlNo + "' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a>");
                            $(`#internalItemId_${itemSlNo}`).val(mapData["itemId"]);
                            $(`#internalItemCode_${itemSlNo}`).val(mapData["itemCode"]);
                            $(`#grnItemHSNTdSpan_${itemSlNo}`).html(mapData["itemHSN"]);
                            $(`#internalItemHsn_${itemSlNo}`).val(mapData["itemHSN"]);
                            $(`#grnItemUOM_${itemSlNo}`).html(mapData["itemUom"]);
                            $(`#grnItemNameTdSpan_${itemSlNo}`).html(itemName);
                            $(`#internalItemName_${itemSlNo}`).val(itemName);
                            // $(`#grnItemTdsTdInput_${itemSlNo}`).val(itemtds);
                            var itemInvoiceUnits = $(`#ItemInvoiceUnits_${itemSlNo}`).val();
                            var InternalItemUom = mapData["itemUom"];
                            if (itemInvoiceUnits.toLowerCase() == InternalItemUom.toLowerCase()) {
                                $(`#grnItemMessage_${itemSlNo}`).html("");
                            } else {
                                $(`#grnItemMessage_${itemSlNo}`).html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>stock keeping unit and invoice driven unit is different");
                            }

                            $.ajax({
                                url: "ajaxs/grn/ajax-get-tds-slab.php?tds=" + itemtds_id,
                                type: "GET",
                                beforeSend: function() {},
                                success: function(val_response) {
                                    // let responseObj = JSON.parse(response);
                                    $(`#ItemInvoiceTDSSlab_${itemSlNo}`).val(val_response);

                                }
                            });

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
                            $(`#mapInvoiceItemCodeChange`).hide();

                            // console.log(itemType);

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

        // COST CENTER PERCENTAGE CALCULATION
        let totalCostCenter = $(".costCenterRate").length;
        let percentPerCostCenter = 100 / totalCostCenter;

        for (elem of $(".costCenterRate")) {
            $(elem).val(percentPerCostCenter);
        };

        $(document).on("keyup", ".costCenterRate", function() {
            let curPercent = $(this).val();
            let remainingPercent = 100 - curPercent;
            let remainingPercentPerCostCenter = remainingPercent / (totalCostCenter - 1);
            for (elem of $(".costCenterRate")) {

                if ($(elem).val() != curPercent) {
                    $(elem).val(remainingPercentPerCostCenter);
                }
            };
        });
        // COST CENTER PERCENTAGE CALCULATION

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

    });
</script>

<script>
    $(document).ready(function() {
        $(".srnTable").DataTable({
            "searching": true
        })
    })
</script>
<script src="<?= BASE_URL; ?>public/validations/pendingSrnValidation.js"></script>