<?php

global $company_id;
global $branch_id;
global $location_id;
global $created_by;
global $updated_by;

function getCostCenterListForGrn()
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    return queryGet('SELECT * FROM `erp_cost_center` WHERE `company_id`=' . $company_id . ' AND `CostCenter_status`="active"', true);
}


function getItemCodeAndHsn($vendorCode, $vendorItemTitle)
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


        $goodsHsnObj = queryGet("SELECT `itemId`, `itemName`, `hsnCode`,`baseUnitMeasure`,`tds` FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `company_id`='" . $company_id . "' AND `itemId`='" . $item_id . "'");
        if ($goodsHsnObj["status"] == "success") {

            // return $goodsHsnObj["data"]["itemName"];

            $baseunitmeasure = $goodsHsnObj["data"]["baseUnitMeasure"];
            $tds_id = $goodsHsnObj["data"]["tds"];

            $getUOM = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $baseunitmeasure . "'");

            $getTds = queryGet("SELECT `TDSRate` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");

            if ($getUOM["status"] == "success") {
                return [
                    "itemCode" => $itemCode,
                    "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                    "itemId" => $goodsHsnObj["data"]["itemId"],
                    "itemName" => $goodsHsnObj["data"]["itemName"],
                    "uom" => $getUOM["data"]["uomName"],
                    "tds" => $getTds["data"]["TDSRate"],
                    "type" => $itemType
                ];
            } else {
                return [
                    "itemCode" => $itemCode,
                    "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                    "itemId" => $goodsHsnObj["data"]["itemId"],
                    "itemName" => $goodsHsnObj["data"]["itemName"],
                    "uom" => "",
                    "tds" => $getTds["data"]["TDSRate"],
                    "type" => $itemType
                ];
            }
        } else {
            return [
                "itemCode" => $vendorGoodsCodeObj["data"]["itemCode"],
                "itemHsn" => "",
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
            "itemName" => "",
            "type" => ""
        ];
    }
}



$id = $_GET["view"];
$grnNo = "SRN" . time() . rand(100, 999);

$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];

$processInvoiceObj = queryGet("SELECT * FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE erp_branch_purchase_order.po_number = '" . $id . "' AND erp_branch_purchase_order.company_id='" . $company_id . "' AND erp_branch_purchase_order.branch_id='" . $branch_id . "' AND erp_branch_purchase_order.location_id='" . $location_id . "'", false);
$invoiceDataGet = $processInvoiceObj["data"];

// $invoiceFile = $invoiceDataGet["uploaded_file_name"];

// $documentNo = $invoiceData["InvoiceId"] ?? "";
// $documentDate = $invoiceData["InvoiceDate"] ?? "";
// $dueDate = $invoiceData["DueDate"] ?? "";

$po_id = $invoiceDataGet["po_id"];
$currency = $invoiceDataGet["currency"];
$curr_name_query = queryGet("SELECT * FROM `erp_currency_type` WHERE currency_id = $currency", false);
$curr_name = $curr_name_query["data"]["currency_name"];
$conversion_rate = $invoiceDataGet["conversion_rate"];

// $po_item = queryGet("SELECT * FROM `erp_branch_purchase_order_items` LEFT JOIN `erp_inventory_items` ON erp_inventory_items.itemId=erp_branch_purchase_order_items.inventory_item_id LEFT JOIN `erp_hsn_code` ON erp_hsn_code.hsnCode=erp_inventory_items.hsnCode WHERE erp_branch_purchase_order_items.po_id = '" . $po_id . "'", true);

$po_item = queryGet("SELECT * FROM `erp_branch_purchase_order_items` LEFT JOIN `erp_inventory_items` ON erp_inventory_items.itemId = erp_branch_purchase_order_items.inventory_item_id LEFT JOIN `erp_hsn_code` ON erp_hsn_code.hsnCode = erp_inventory_items.hsnCode WHERE erp_branch_purchase_order_items.po_id = '" . $po_id . "' AND erp_branch_purchase_order_items.remainingQty > 0", true);

$po_item_data = $po_item["data"];
$subtotal = 0;
$total = 0;
$total_tax = 0;
foreach ($po_item_data as $po_data) {
    $unit_price = $po_data["unitPrice"];
    $qty  = $po_data["remainingQty"];
    $subtotal += $unit_price * $qty;

    $tax_percentage = $po_data["taxPercentage"];

    $tax_amt = ($unit_price * $qty) * $tax_percentage / 100;

    $total_tax += $tax_amt;

    $after_tax = ($unit_price * $qty) + $tax_amt;

    $total += $after_tax;
}

$invoiceTotal = $total ?? 0;
$invoiceSubTotal = $subtotal ?? 0;
$invoiceTaxTotal = $total_tax ?? 0;


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


$customerName = $loginBranchName ?? "";
$customerPurchaseOrder = $invoiceDataGet["po_number"] ?? "";

$customerGstin = $loginBranchGstin;
$vendorGstin = $invoiceDataGet["vendor_gstin"] ?? "";

$customerGstinStateCode = substr($customerGstin, 0, 2);

$vendor_id = $invoiceDataGet["vendor_id"];


if ($vendorGstin == "" || $vendorGstin == NULL || !isset($vendorGstin)) {
    $vendorGstinStateCode = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=" . $vendor_id . " AND `vendor_business_primary_flag`='1' ORDER BY `vendor_business_id` DESC", false)["data"]["state_code"] ?? "";
} else {
    $vendorGstinStateCode = substr($vendorGstin, 0, 2);
}


$vendorAddress = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=" . $vendor_id . " AND `vendor_business_primary_flag`='1' ORDER BY `vendor_business_id` DESC", false)["data"]["gstStateName"] ?? "";
$vendorAddressRecipient = "";

$vendorGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $vendorGstinStateCode)["data"]["gstStateName"] ?? "";
$customerGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $customerGstinStateCode)["data"]["gstStateName"] ?? "";

$vendorPan = substr($vendorGstin, 2, 10);

$vendorCode = $invoiceDataGet["vendor_code"];
$vendorId = $invoiceDataGet["vendor_id"];
$vendorName = $invoiceDataGet["trade_name"] ?? "";
$vendorCreditPeriod = $invoiceDataGet["vendor_credit_period"];

$functional_area = $invoiceDataGet["functional_area"];

$totalCGST = 0;
$totalSGST = 0;
$totalIGST = $total_tax == "" ? 0 : $total_tax;

// console($invoiceData["Items"]);

// $postStatus = $invoiceDataGet["status"];

// $isPoEnabledCompany = false;

// $isPoAndGrnInvoiceMatched = true;

// $isGrnIvExist = false;
// if ($vendorCode != "" && $documentNo != "") {
//     $checkGrnExist = queryGet('SELECT `grnId` FROM `erp_grninvoice` WHERE `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id . ' AND `vendorDocumentNo`="' . $documentNo . '" AND `vendorCode` ="' . $vendorCode . '"');
//     if ($checkGrnExist["numRows"] > 0) {
//         $isGrnIvExist = true;
//     }
//     // console($checkGrnExist);
// }

// if ($dueDate == "" && $vendorCreditPeriod != "" && $documentDate != "") {
//     $tempDueDate = date_create($documentDate);
//     date_add($tempDueDate, date_interval_create_from_date_string($vendorCreditPeriod . " days"));
//     $dueDate = date_format($tempDueDate, "Y-m-d");
// }

// console($dueDate);

// if (!$isGrnIvExist) {
$getCostCenterListForGrnObj = getCostCenterListForGrn();
?>

<form action="" method="POST" id="addNewGRNForm" enctype="multipart/form-data">
    <div class="row grn-create po-grn-view upload-file">
        <div class="col-lg-4 col-md-4 col-sm-4">
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
                            <input type="hidden" name="id" value="0">
                            <input type="hidden" name="grnType" value="srn">
                            <input type="hidden" name="vendorDocumentFile" value="">
                            <input type="hidden" name="vendorGstinStateName" value="<?= $vendorGstinStateName . '(' . $vendorGstinStateCode . ')'; ?>">
                            <input type="hidden" name="locationGstinStateName" value="<?= $customerGstinStateName . '(' . $customerGstinStateCode . ')' ?>">

                            <!-- <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GRN No :&nbsp;</p>
                                        <p> <?= $grnNo ?></p>
                                    </div> -->
                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document No :&nbsp;</p>
                                <p>
                                    <input type="text" name="documentNo[<?= $vendorId ?>]" value="" class="form-control" required>
                                </p>
                            </div>
                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document Date :&nbsp;</p>
                                <p><input type="date" name="documentDate[<?= $vendorId ?>]" value="" class="form-control" required></p>
                            </div>
                            <div class="display-flex grn-form-input-text flex-direction-row gap-2 mb-4">
                                <i class="fa fa-check"></i>
                                <?php
                                $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
                                $check_var_data = $check_var_sql['data'];
                                // console($_SESSION);
                                // // console($check_var_sql);
                                // console($check_var_sql);
                                $max = $check_var_data['month_end'];
                                $min = $check_var_data['month_start'];
                                ?>
                                &nbsp;
                                <p class="label-bold">Posting Date :</p>
                                &nbsp;
                                <input type="date" name="invoicePostingDate" value="<?= date("Y-m-d", strtotime($max)); ?>" class="form-control" min="<?= $min ?>" max="<?= $max ?>" required>
                            </div>
                            <div class="display-flex grn-form-input-text">
                                <i class="fa fa-check"></i>
                                &nbsp;
                                <?php

                                if ($dueDate == "" && $vendorCreditPeriod != "" && $max != "") {
                                    $tempDueDate = date_create($max);
                                    date_add($tempDueDate, date_interval_create_from_date_string($vendorCreditPeriod . " days"));
                                    $dueDate = date_format($tempDueDate, "Y-m-d");
                                }

                                ?>
                                <p class="label-bold">Due Date :</p>
                                &nbsp;
                                <input type="date" id="iv_due_date" name="invoiceDueDate[<?= $vendorId ?>]" value="<?= date("Y-m-d", strtotime($dueDate)); ?>" class="form-control" required>
                                <p class="text-danger text-xs" id="postdatelabel"></p>
                            </div>

                            <div class="head d-flex border-label">
                                <h6 class="font-bold text-xs mb-0">Vendor info</h6>
                                <hr>
                            </div>
                            <div class="dotted-border-area">
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
                                <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Customer State :&nbsp;</p>
                                    <p><?= $customerGstinStateName ?>(<?= $customerGstinStateCode ?>)</p>
                                </div>

                                <!-- <div class="currency-conversion-section mt-3">
                                    <div class="dynamic-currency">
                                        <input type="hidden" name="currency" id="" value="<?= $currency ?>" class="form-control">
                                        <input type="hidden" name="currency_conversion_rate" id="" value="<?= $conversion_rate ?>" class="form-control">
                                    </div>
                                </div> -->




                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">
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
                            <div class="grn-form-input-text">
                                <div class="display-flex grn-form-input-text">
                                    <i class="fa fa-check"></i>
                                    &nbsp;
                                    <p class="label-bold">PO Number :</p>
                                    &nbsp;
                                    <!-- <input type="text" name="invoicePoNumber" id="invoicePoNumber" value="<?= $customerPurchaseOrder ?>" class="form-control"> -->
                                    <button type="button" class="btn btn-primary po-add-btn" data-toggle="modal" data-target="#multiplePOListTable" id="po_list_button">
                                        Select PO from list
                                        <i class="fa fa-plus pl-2"></i>
                                    </button>
                                    <div class="modal fade right" id="multiplePOListTable" data-bs-keyboard="false" tabindex="-1" aria-labelledby="examplePendingGrnModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">Multiple PO List
                                                    <!-- <button type="button" class="btn btn-primary select-po-btn" id="selectPOBtn" disabled>Select</button> -->
                                                </div>
                                                <div class="modal-body">
                                                    <div class="multiplePolist">
                                                        <table class="table defaultDataTable grn-table">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>PO Number</th>
                                                                    <th>PO Date</th>
                                                                    <th>Reference Number</th>
                                                                    <th>Vendor Name</th>
                                                                    <th>Vendor Code</th>
                                                                    <th>PO Types</th>
                                                                    <th>Total Amount</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="open_po_list_table">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="customInvoicePoNumberMain" id="customInvoicePoNumberMain">
                                    <input name="invoicePoNumber" type="hidden" id="hiddenInputPO" value="" class="form-control">
                                    <ul id="itemList" class="item-list"></ul>
                                    <div class="d-flex add-po-number-area">
                                        <input class="form-control" type="text" id="customInvoicePoNumber" placeholder="enter manual PO number ..." />
                                        <button type="button" class="btn btn-primary" onclick="addItem()">Add</button>
                                    </div>
                                </div>
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

                                        if ($func_area['data'][0]['functionalities_id'] == $functional_area) {
                                    ?>
                                            <option value="<?= $func_area['data'][0]['functionalities_id'] ?>" selected><?= $func_area['data'][0]['functionalities_name'] ?>
                                            </option>
                                        <?php
                                        } else {
                                        ?>

                                            <option value="<?= $func_area['data'][0]['functionalities_id'] ?>"><?= $func_area['data'][0]['functionalities_name'] ?>
                                            </option>
                                    <?php
                                        }
                                    } ?>
                                </select>

                            </div>
                            <div class="display-flex grn-form-input-text mt-4">
                                <p class="label-bold text-italic" style="white-space: pre-line;"><span class="mr-2">*</span>Note : Map Functional area with this invoice to get the expense details functional area wise.</p>
                            </div>

                            <div>
                                <div class="display-flex"><i class="fa fa-check"></i>
                                    <p class="label-bold">&nbsp;Remark </p>
                                </div>
                                <textarea name="extra_remark" id="extra_remark" class="form-control" rows="2"></textarea>
                            </div>
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
                                        <option value="<?= $currency ?>" data-currname="<?= $curr_name ?>" selected><?= $curr_name ?></option>

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
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="card card-tabs">
                <div class="card-header">
                    <div class="head">
                        <i class="fa fa-file"></i>
                        <h4>Upload Bill</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content tab-col" id="custom-tabs-three-tabContent">
                        <div class="tab-pane fade show active iframe-preview-btn m-3" id="uploaded-invoice-preview-div" role="tabpanel" aria-labelledby="invoice-po-div-tab">
                            <div class="file-input">
                                <input type="file" name="invoice_file_name" id="fileInput" class="form-control">
                                <span class="button">Choose</span>
                                <span class="label" data-js-label>No file selected</label>
                            </div>
                            <div id="previewContainer" class="previewContainer"></div>
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
                                            <div id="previewModalContainer" class="previewModalContainer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php
                        $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9" AND `vendor_id`="' . $vendorId . '"', true);
                        $poDetails = $poDetailsObj["data"] ?? [];

                        ?>
                        <div class="tab-pane fade quick-registration-vendor" id="po_list" role="tabpanel" aria-labelledby="invoice-po-list-tab">
                            <div class="container">
                                <ul>
                                    <li>
                                        <button type="button" id="refresh_po_list" class="btn btn-primary select-po float-right mt-3">Refresh</button>
                                    </li>
                                    <br>
                                    <br>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grn-table">
        <table class="table-sales-order table defaultDataTable grn-table">
            <thead>
                <tr>
                    <th>Sl No.</th>
                    <th>PO No.</th>
                    <th>Service Name</th>
                    <th>Service Code</th>
                    <th>Service HSN</th>
                    <th>Cost Center</th>
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
                $po_ids = array();
                foreach ($po_item_data as $oneItemObj) {

                    $oneItemData = $oneItemObj;

                    $itemHSN = "";
                    $itemName = $oneItemData["itemName"] ?? "";
                    $grnItemName = $oneItemData["itemName"] ?? "";
                    $itemQty = $oneItemData["remainingQty"] ?? "0";
                    $itemUnitPrice = $oneItemData["unitPrice"] ?? "0";
                    $invoice_units = $oneItemData["uom"] ?? "";
                    $internalItemuom_id = $oneItemData["baseUnitMeasure"];
                    $goodsType = $oneItemData["goodsType"];
                    $po_item_id = $oneItemData["po_item_id"];

                    $subtotal = $itemUnitPrice * $itemQty;

                    $tax_percentage = $oneItemData["taxPercentage"];

                    $itemTax = ($itemUnitPrice * $itemQty) * $tax_percentage / 100;

                    $Total = ($itemUnitPrice * $itemQty) + $tax_amt;

                    $cgst = 0;
                    $sgst = 0;
                    $igst = $itemTax == "" ? 0 : $itemTax;


                    $internalItemId = "";
                    $internalItemCode = "";
                    $internalItemHsn = "";
                    $tds = 0;
                    $internalItemId = $oneItemData["inventory_item_id"];
                    $internalItemCode = $oneItemData["itemCode"];
                    $internalItemUom = $oneItemData["uom"];
                    // $itemType = $oneItemData["type"];
                    $itemHSN = $oneItemData["hsnCode"];
                    $itemName = $oneItemData["itemName"];
                    $tds_id = $oneItemData["tds"];
                    $tds_query = queryGet("SELECT `TDSRate` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");
                    $tds = $tds_query["data"]["TDSRate"] ?? 0;

                    $basic_amt = ($itemUnitPrice * $itemQty);

                    $tds_value = $basic_amt * ($tds / 100);

                    if ($vendorGstinStateCode == $customerGstinStateCode) {
                        $cgst = $itemTax / 2;
                        $sgst = $itemTax / 2;
                        $igst = 0;
                    } else {
                        $cgst = 0;
                        $sgst = 0;
                        $igst = $itemTax == "" ? 0 : $itemTax;
                    }

                    $itemTotalPrice = $basic_amt + $cgst + $sgst + $igst - $tds_value;

                    // if ($vendorGstinStateCode == $customerGstinStateCode) {
                    //     $itemTotalPrice = ($basic_amt) + $cgst + $sgst - $tds_value ;
                    // } else {
                    //     $itemTotalPrice = ($basic_amt) + $igst - $tds_value;
                    // }

                    array_push($po_ids, $oneItemData["po_item_id"]);

                    if ($itemName == "" || strtolower($itemName) == "cgst" || strtolower($itemName) == "sgst") {
                        continue;
                    }
                    $sl += 1;
                ?>

                    <tr id="grnItemRowTr_<?= $sl ?>">
                        <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                        <input type="hidden" id="internalPoItemId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][PoItemId]" value="<?= $oneItemData["po_item_id"] ?>" />
                        <input type="hidden" id="internalItemPo_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemPurchaseOrder]" value="<?= $id ?>" />
                        <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                        <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                        <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                        <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemQty]" value="<?= $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPriceStatic" id="ItemInvoiceTotalPriceStatic_<?= $sl ?>" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                        <!-- <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                        <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                        <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                        <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemInvoiceTDSValue]" value="<?= $tds_value ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPrice" id="ItemInvoiceTotalPrice_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTotalPrice]" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceCGSTClass" id="ItemInvoiceCGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCGST]" value="<?= $cgst ?>" />
                        <input type="hidden" class="ItemInvoiceSGSTClass" id="ItemInvoiceSGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemSGST]" value="<?= $sgst ?>" />
                        <input type="hidden" class="ItemInvoiceIGSTClass" id="ItemInvoiceIGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemIGST]" value="<?= $igst ?>" />
                        <input type="hidden" id="ItemInvoiceCGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCGSTNew]" value="<?= $cgst ?>" />
                        <input type="hidden" id="ItemInvoiceSGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemSGSTNew]" value="<?= $sgst ?>" />
                        <input type="hidden" id="ItemInvoiceIGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemIGSTNew]" value="<?= $igst ?>" />
                        <input type="hidden" id="ItemInvoiceUnits_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnits]" value="<?= $invoice_units ?>" />
                        <input type="hidden" id="ItemInvoiceUOM_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUOM]" value="<?= $internalItemUom ?>" />
                        <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
                        <input type="hidden" id="ItemInvoiceUOMID_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUOMID]" value="<?= $internalItemuom_id ?>" />


                        <td><?= $sl ?></td>
                        <td id="grnItemPOTdSpan_<?= $sl ?>">
                            <p class="pre-normal <?= $id ?>"><?= $id ?></p>
                        </td>
                        <td id="grnItemNameTdSpan_<?= $sl ?>">
                            <p class="pre-normal"><?= $itemName ?></p>
                        </td>
                        <td class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                            <?php
                            echo $internalItemCode;
                            ?>
                        </td>
                        <td class="grnItemHSNTdSpan" id="grnItemHSNTdSpan_<?= $sl ?>"><?= $itemHSN ?></td>
                        <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                            <select class="form-control text-xs itemCostCenterId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemStorageLocationId]" required>
                                <option value="">Select Cost Center</option>
                                <?php
                                foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                    echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <div class="form-input d-flex gap-2">
                                <input type="number" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemReceivedQty]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs received_quantity" required>
                                <input type="hidden" name="poItemId[<?= $po_item_id ?>]" id="grnPoInputQty_<?= $sl ?>" value="0">
                                <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemRemainQty]" id="grnPoInputRemainQty_<?= $sl ?>" value="0">
                                <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][poQty]" id="grnPoQty_<?= $sl ?>" value="<?= $itemQty ?>">
                                <p class="text-xs"><?= $internalItemUom ?></p>
                            </div>
                        </td>
                        <!-- <td class="text-right" id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= number_format($itemUnitPrice, 2) ?></td> -->
                        <td>
                            <div class="input-group input-group-sm m-0" style="flex-wrap: nowrap;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text spanInvoiceCurrencyName" id="spanInvoiceCurrencyName_<?= $sl ?>"><?= $curr_name ?></span>
                                </div>
                                <input type="number" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPriceOtherCurrency]" value="<?= number_format($itemUnitPrice, 2, '.', '') ?>" id="grnItemUnitPriceTdInput_<?= $sl ?>" class="form-control border py-3 text-right itemUnitPrice" required readonly>
                                <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPricehidden]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceTdInputhidden_<?= $sl ?>" class="form-control text-xs itemUnitPricehidden">
                                <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceInrhidden_<?= $sl ?>" class="form-control text-xs grnItemUnitPriceInrhidden">
                            </div>
                            <span class="text-small spanUnitPriceINR" id="spanUnitPriceINR_<?= $sl ?>"></span>
                        </td>
                        <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($itemUnitPrice * $itemQty, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceCGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($cgst, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceSGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($sgst, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceIGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($igst, 2) ?></td>
                        <td>
                            <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                <input type="number" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTds]" value="<?= $tds ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-xs itemTds" required>
                                <p class="text-xs">%</p>
                            </div>
                        </td>
                        <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </span>
                        <input type="hidden" value="<?= $tax_percentage ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">
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
                            <td class="text-right" id="grandSubTotalTd" style="background: none;"><?= $curr_name . " : " . number_format($totalSubtotal, 2) ?></td>
                        </tr>

                        <?php

                        $toalTotal = $GrandtoalTotal;
                        $totalCGST = $grandcgst;
                        $totalSGST = $grandsgst;
                        $totalIGST = $grandigst;

                        // if ($totalCGST == 0 && $totalSGST == 0 && $totalIGST == 0) {
                        //     $toalTotal = $GrandtoalTotal - $totalTdsValue;
                        //     $totalCGST = $cgst;
                        //     $totalSGST = $sgst;
                        //     $totalIGST = $igst;
                        // } else {
                        //     $toalTotal = $totalSubtotal + $totalCGST + $totalSGST + $totalIGST - $totalTdsValue;
                        // }


                        if ($vendorGstinStateCode == $customerGstinStateCode) {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total CGST</td>
                                <td class="text-right" style="background: none;" id="grandCgstTd"><?= $curr_name . " : " . number_format($totalCGST, 2) ?></td>
                            </tr>
                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total SGST</td>
                                <td class="text-right" style="background: none;" id="grandSgstTd"><?= $curr_name . " : " . number_format($totalSGST, 2) ?></td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total IGST</td>
                                <td class="text-right" style="background: none;" id="grandIgstTd"><?= $curr_name . " : " . number_format($totalIGST, 2) ?></td>
                            </tr>
                        <?php
                        }

                        ?>
                        <tr class="itemTotals">
                            <td colspan="9" style="background: none;">Total TDS</td>
                            <td class="text-right" id="grandTds" style="background: none;">-<?= number_format($totalTdsValue, 2) ?></td>
                        </tr>
                        <tr class="itemTotals">
                            <input type="hidden" id="totalCGST" name="totalInvoiceCGST" value="<?= $totalCGST ?>">
                            <input type="hidden" id="totalSGST" name="totalInvoiceSGST" value="<?= $totalSGST ?>">
                            <input type="hidden" id="totalIGST" name="totalInvoiceIGST" value="<?= $totalIGST ?>">
                            <input type="hidden" id="totalTDS" name="totalInvoiceTDS" value="<?= $totalTdsValue ?>">
                            <input type="hidden" id="grandSubTotal" name="totalInvoiceSubTotal" value="<?= $totalSubtotal ?>">
                            <input type="hidden" id="grandTotal" name="totalInvoiceTotal" value="<?= $toalTotal ?>">
                            <td colspan="9" style="background: none;">Total Amount</td>
                            <td class="text-right" id="grandTotalTd" style="background: none;"><?= $curr_name . " : " . number_format($toalTotal, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <input type="hidden" name="poStatus" value="0">
    <input type="hidden" name="addNewGrnFormSubmitBtn" value="formSubmit">
    <button type="submit" id="addNewGrnFormSubmitBtn" value="Submit GRN" class="btn btn-primary float-right mt-3 mb-3">Submit SRN</button>

</form>


<div class="modal invoice-iframe" id="po_items">
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
    const selectPOCheck = document.getElementById('selectPOCheck');
    const selectPOBtn = document.getElementById('selectPOBtn');

    selectPOCheck.addEventListener('change', function() {
        if (this.checked) {
            selectPOBtn.classList.add('primary-btn');
            selectPOBtn.removeAttribute('disabled');
        } else {
            selectPOBtn.classList.remove('primary-btn');
            selectPOBtn.setAttribute('disabled', true);
        }
    });
</script>

<script>
    var inputs = document.querySelectorAll(".file-input");

    for (var i = 0, len = inputs.length; i < len; i++) {
        customInput(inputs[i]);
    }

    function customInput(el) {
        const fileInput = el.querySelector('[type="file"]');
        const label = el.querySelector("[data-js-label]");

        fileInput.onchange = fileInput.onmouseout = function() {
            if (!fileInput.value) return;

            var value = fileInput.value.replace(/^.*[\\\/]/, "");
            el.className += " -chosen";
            label.innerText = value;
        };
    }
</script>
<script>
    $(document).ready(function() {
        console.log("hello there!");
        var type = "service";
        var obj = <?= json_encode($getCostCenterListForGrnObj) ?>;
        var id = <?= json_encode($id) ?>;
        var company_currency = <?= json_encode($comp_currency)  ?>;
        var curr_name = <?= json_encode($curr_name)  ?>;
        var curr_id = <?= json_encode($currency)  ?>;
        var conversion_rate = <?= json_encode($conversion_rate)  ?>;
        var serial_number = <?= json_encode($sl) ?>;
        var newInputArray = [];
        newInputArray.push(id);
        var currency_loaded = 0;
        var po_loaded = 0;
        var posted_loader = 0;


        function multiplePo(poNumber, type, serial_number) {

            // alert(newInputArray);
            if (type == "add") {
                // var is_exists = newInputArray.includes(poNumber);
                // console.log("---------------------------------");
                // console.log(newInputArray,poNumber,is_exists);
                // console.log("------------------------------------");
                // if(is_exists)
                // {
                $.ajax({
                    url: "ajaxs/grn/ajax-srn-po-items.php?po=" + poNumber + "&serial_number=" + serial_number,
                    type: "GET",
                    beforeSend: function() {
                        console.log("Adding new items...");
                    },
                    success: function(responseData) {
                        $("#itemsTable").append(responseData);
                        console.log("Adding new items completed!");
                        currency_change(curr_name);
                        console.log("Change currency func called success!");
                        // serial_number = 7;
                    }
                });
                // }

            } else {
                $("." + poNumber).parent().parent().remove();
                currency_change(curr_name);
            }
        }


        $(document).on("keyup", ".itemUnitPrice", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });



        $(document).on("keyup", ".received_quantity", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
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
            let poQty = (parseFloat($(`#grnPoQty_${rowNo}`).val()) > 0) ? parseFloat($(`#grnPoQty_${rowNo}`).val()) : 0;


            let basicPrice = itemUnitPrice * itemQty;

            let cgstPercent = (cgst / itemStaticPrice) * 100;
            let sgstPercent = (sgst / itemStaticPrice) * 100;
            let igstPercent = (igst / itemStaticPrice) * 100;

            let cgst_value = basicPrice * (cgstPercent / 100);
            let sgst_value = basicPrice * (sgstPercent / 100);
            let igst_value = basicPrice * (igstPercent / 100);
            let tds_value = basicPrice * (tds / 100);

            let totalItemPrice = basicPrice + cgst + sgst + igst - tds_value;
            let tax_value = basicPrice + (basicPrice * tax / 100);

            console.log(totalItemPrice, cgst, sgst, igst);

            var curr_name = $("#selectCurrency").find(':selected').data("currname");
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            $(`#grnItemInvoiceTotalPriceTdSpan_${rowNo}`).html(`${curr_name}: ${(totalItemPrice).toFixed(2)}`);
            $(`#grnItemInvoiceBaseAmtTdSpan_${rowNo}`).html(`${curr_name}: ${(basicPrice).toFixed(2)}` + '<p class="text-small spanBasePriceINR" id="spanBasePriceINR_' + rowNo + '"></p>');
            $(`#ItemInvoiceTotalPrice_${rowNo}`).val((basicPrice / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceGrandTotalPrice_${rowNo}`).val((totalItemPrice / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceTDSValue_${rowNo}`).val((tds_value / currency_rate_value).toFixed(2));
            $(`#grnItemInternalTaxValue_${rowNo}`).val((tax_value / currency_rate_value).toFixed(2));


            $(`#ItemInvoiceCGST_${rowNo}`).val((cgst_value / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceSGST_${rowNo}`).val((sgst_value / currency_rate_value).toFixed(2));
            $(`#ItemInvoiceIGST_${rowNo}`).val((igst_value / currency_rate_value).toFixed(2));

            $(`#grnItemInvoiceCGSTTdSpan_${rowNo}`).html(`${curr_name}: ${(cgst_value).toFixed(2)}` + '<p class="text-small spanCgstPriceINR" id="spanCgstPriceINR_' + rowNo + '"></p>');
            $(`#grnItemInvoiceSGSTTdSpan_${rowNo}`).html(`${curr_name}: ${(sgst_value).toFixed(2)}` + '<p class="text-small spanSgstPriceINR" id="spanSgstPriceINR_' + rowNo + '"></p>');
            $(`#grnItemInvoiceIGSTTdSpan_${rowNo}`).html(`${curr_name}: ${(igst_value).toFixed(2)}` + '<p class="text-small spanIgstPriceINR" id="spanIgstPriceINR_' + rowNo + '"></p>');


            $(`#spanInvoiceCurrencyName_${rowNo}`).html(`${curr_name}`);
            $(`#grnItemUnitPriceInrhidden_${rowNo}`).val(`${(itemUnitPrice / currency_rate_value).toFixed(2)}`);

            if (curr_name != company_currency) {
                $(`#spanUnitPriceINR_${rowNo}`).html(`${company_currency}: ${(itemUnitPrice / currency_rate_value).toFixed(2)}`);
                $(`#spanBasePriceINR_${rowNo}`).html(`${company_currency}: ${(basicPrice / currency_rate_value).toFixed(2)}`);
                $(`#spanCgstPriceINR_${rowNo}`).html(`${company_currency}: ${(cgst_value / currency_rate_value).toFixed(2)}`);
                $(`#spanSgstPriceINR_${rowNo}`).html(`${company_currency}: ${(sgst_value / currency_rate_value).toFixed(2)}`);
                $(`#spanIgstPriceINR_${rowNo}`).html(`${company_currency}: ${(igst_value / currency_rate_value).toFixed(2)}`);
            }

            $(`#grnPoInputQty_${rowNo}`).val(poQty - itemQty);
            $(`#grnPoInputRemainQty_${rowNo}`).val(poQty - itemQty);

            calculateGrandTotalAmount();
        }


        function calculateGrandTotalAmount() {
            let totalAmount = 0;
            let grandSubTotalAmt = 0;
            let totalTds = 0;
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


            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - totalTds;

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
                $(`#spangrandTDSINR`).html(`${company_currency}: ${(totalTds).toFixed(2)}`);
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

            $("#grandTds").html("-" + `${curr_name}: ${(tdsDeduct * currency_rate_value).toFixed(2)}` + '<p class="text-small spangrandTDSINR" id="spangrandTDSINR"></p>');
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



        $("#modalItemCodeDropDown").select2({
            dropdownParent: $("#mapInvoiceItemCode")
        });

        //$("#modalItemCodeDropDown").select2();

        let vendorCode = `<?= $vendorCode ?>`;
        let vendorId = `<?= $vendorId ?>`;


        $("#po_list_button").click(function() {
            // alert("Hello");
            $.ajax({
                url: "ajaxs/grn/ajax-fetch-multiple-po.php?vendor_id=" + vendorId + "&type=srn&currency=" + curr_id,
                type: "GET",
                beforeSend: function() {
                    $("#open_po_list_table").html('Loading.....');
                },
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
            $("#invoicePoNumber").val(passedCode);
            console.log(passedCode);

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
                        $(`#grnItemUnitPriceTdInput_${rowNo}`).val(newVal);
                        $(`#currency_conversion_rate`).val(responseObj);
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

        function currency_change(curr_name) {
            console.log("Currency details updating...");
            if (currency_loaded == 0) {
                $.ajax({
                    url: "ajaxs/ajax-currency-convert.php?company_currency=" + company_currency + "&selected_currency=" + curr_name,
                    type: "GET",
                    beforeSend: function() {
                        console.log("Loading currency change function...");
                        $(`#currency_conversion_rate`).val("Loading....");
                    },
                    success: function(responseData) {
                        currency_loaded = 1;
                        var responseObj = JSON.parse(responseData);
                        console.log("Response of change currency func:", responseObj);
                        serial_number = 0;
                        for (elem of $(".itemUnitPricehidden")) {
                            let rowNo = ($(elem).attr("id")).split("_")[1];
                            let newVal = ($(elem).val()) * conversion_rate;
                            $(`#grnItemUnitPriceTdInput_${rowNo}`).val(newVal);
                            $(`#currency_conversion_rate`).val(responseObj);
                            calculateOneItemAmounts(rowNo);
                            serial_number++;
                        };
                    }
                });
            } else {
                serial_number = 0;
                for (elem of $(".itemUnitPricehidden")) {
                    let rowNo = ($(elem).attr("id")).split("_")[1];
                    let newVal = ($(elem).val()) * conversion_rate;
                    $(`#grnItemUnitPriceTdInput_${rowNo}`).val(newVal);
                    // $(`#currency_conversion_rate`).val(responseObj);
                    calculateOneItemAmounts(rowNo);
                    serial_number++;
                };
            }

            $('#selectCurrency option').each(function() {
                // Compare the current option's value with curr_name
                console.log($(this).val());
                if ($(this).val() == curr_id) {
                    // If it matches, set the selected attribute and break the loop
                    $(this).prop('selected', true);
                    return false; // exit the loop
                }
            });
        }

        currency_change(curr_name);


        function showCombinedArray() {
            document.getElementById("hiddenInputPO").value = JSON.stringify(newInputArray);

            var hiddenInputValue = document.getElementById("hiddenInputPO").value;
            var itemListValues = [];
            var itemList = document.getElementById("itemList").getElementsByTagName("li");

            for (var i = 0; i < itemList.length; i++) {
                itemListValues.push(itemList[i].textContent);
            }

            var combinedArray = new Set([...newInputArray, ...itemListValues]);
            console.log(combinedArray);

            var ul = document.getElementById("itemList");
            newInputArray.forEach(function(item) {
                var itemExists = itemListValues.includes(item);
                if (!itemExists) {
                    var li = createListItem(item);
                    ul.appendChild(li);
                }
            });
        }

        function addItem() {
            var inputValue = document.getElementById("customInvoicePoNumber").value;
            if (!inputValue.trim()) {
                return;
            }

            var listItem = createListItem(inputValue);
            document.getElementById("itemList").appendChild(listItem);

            document.getElementById("customInvoicePoNumber").value = "";
            showCombinedArray();
        }

        function createListItem(item) {
            var li = document.createElement("li");
            li.textContent = item;

            var deleteButton = createDeleteButton();
            li.appendChild(deleteButton);

            return li;
        }

        function createDeleteButton() {
            var deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-danger", "delete-button");
            deleteButton.innerHTML = `<i class="fa fa-times"></i>`;
            deleteButton.onclick = function() {
                this.parentNode.remove();
                multiplePo(this.parentNode.textContent.trim(), "sub", serial_number);
                removeFromArray(this.parentNode.textContent.trim());
                showCombinedArray();
            };
            return deleteButton;
        }

        function removeFromArray(item) {
            var index = newInputArray.indexOf(item);
            if (index !== -1) {
                newInputArray.splice(index, 1);
            }
        }

        // window.onload = function() {
        showCombinedArray();
        // };

        function handleCheckboxChange(checkbox) {
            if (checkbox.checked) {
                var poNumber = checkbox.value;
                addItemToList(poNumber);
                multiplePo(poNumber, "add", serial_number);
            }
        }

        $(document).on("change", ".ajaxPoTableCheckBox", function() {
            handleCheckboxChange(this);
        });

        function addItemToList(item) {
            var ul = document.getElementById("itemList");
            var itemListValues = [];

            var itemList = ul.getElementsByTagName("li");
            for (var i = 0; i < itemList.length; i++) {
                itemListValues.push(itemList[i].textContent);
            }

            var itemExists = itemListValues.includes(item);
            if (!itemExists) {
                var li = createListItem(item);
                ul.appendChild(li);

                newInputArray.push(item);
                showCombinedArray();
            }
        }
    });
</script>

<script src="<?= BASE_URL; ?>public/validations/pendingSrnValidation.js"></script>