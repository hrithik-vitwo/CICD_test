<?php


global $company_id;
global $branch_id;
global $location_id;
global $created_by;
global $updated_by;

global $companyCountry;

$lable = (getLebels($companyCountry)['data']);
$lable = json_decode($lable, true);
// console($lable['fields']['businessTaxID']);
$tdslable = ($lable['source_taxation']);
$abnlable = $lable['fields']['businessTaxID'];
function getStorageLocationListForGrn()
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    global $isQaEnabled;

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

        $goodsHsnObj = queryGet("SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` as goods LEFT JOIN `erp_hsn_code` as hsn ON goods.hsnCode=hsn.hsnCode WHERE goods.company_id='" . $company_id . "' AND goods.itemId='" . $item_id . "'");
        if ($goodsHsnObj["status"] == "success") {

            // return $goodsHsnObj["data"]["itemName"];

            $baseunitmeasure = $goodsHsnObj["data"]["baseUnitMeasure"];

            $getUOM = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $baseunitmeasure . "'");

            if ($getUOM["status"] == "success") {
                return [
                    "itemCode" => $itemCode,
                    "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                    "itemId" => $goodsHsnObj["data"]["itemId"],
                    "itemName" => $goodsHsnObj["data"]["itemName"],
                    "goodsType" => $goodsHsnObj["data"]["goodsType"],
                    "uom" => $getUOM["data"]["uomName"],
                    "type" => $itemType
                ];
            } else {
                return [
                    "itemCode" => $itemCode,
                    "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                    "itemId" => $goodsHsnObj["data"]["itemId"],
                    "itemName" => $goodsHsnObj["data"]["itemName"],
                    "goodsType" => $goodsHsnObj["data"]["goodsType"],
                    "uom" => "",
                    "type" => $itemType
                ];
            }
        } else {
            return [
                "itemCode" => $vendorGoodsCodeObj["data"]["itemCode"],
                "itemHsn" => "",
                "itemId" => "",
                "itemName" => "",
                "goodsType" => "",
                "type" => $itemType
            ];
        }
    } else {
        return [
            "itemCode" => "",
            "itemHsn" => "",
            "itemId" => "",
            "itemName" => "",
            "goodsType" => "",
            "type" => ""
        ];
    }
}



$id = base64_decode($_GET["view"]);

$grnNo = "GRN" . time() . rand(100, 999);


$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];

$processInvoiceObj = queryGet("SELECT * FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE erp_branch_purchase_order.po_number = '" . $id . "' AND erp_branch_purchase_order.company_id='" . $company_id . "' AND erp_branch_purchase_order.branch_id='" . $branch_id . "' AND erp_branch_purchase_order.location_id='" . $location_id . "'", false);
$invoiceDataGet = $processInvoiceObj["data"];
$po_id = $invoiceDataGet["po_id"];
$use_type = $invoiceDataGet["use_type"];
$currency = $invoiceDataGet["currency"];
$curr_name_query = queryGet("SELECT * FROM `erp_currency_type` WHERE currency_id = $currency", false);
$curr_name = $curr_name_query["data"]["currency_name"];
$conversion_rate = $invoiceDataGet["conversion_rate"];
$delivery_date = $invoiceDataGet["delivery_date"];


$po_item = queryGet("SELECT * FROM `erp_branch_purchase_order_items` LEFT JOIN `erp_inventory_items` ON erp_inventory_items.itemId = erp_branch_purchase_order_items.inventory_item_id LEFT JOIN `erp_hsn_code` ON erp_hsn_code.hsnCode = erp_inventory_items.hsnCode WHERE erp_branch_purchase_order_items.po_id = '" . $po_id . "' AND erp_branch_purchase_order_items.remainingQty > 0 AND erp_hsn_code.country_id  = '" . $companyCountry . "' ", true);



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

$invoiceSubTotal = $subtotal ?? 0;
$invoiceTotal = $total ?? 0;
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
$vendorCreditPeriod = $invoiceDataGet["vendor_credit_period"] ?? 0;

$functional_area = $invoiceDataGet["functional_area"];

// console($vendorCreditPeriod);

$totalCGST = 0;
$totalSGST = 0;
$totalIGST = $total_tax == "" ? 0 : $total_tax;


$getStorageLocationListForGrnObj = getStorageLocationListForGrn();
$getCostCenterListForGrnObj = getCostCenterListForGrn();

if ($companyCountry != 103) {
    $b_places = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=$vendorId AND `vendor_business_primary_flag` = 1");
    $b_row = $b_places['data'];
    $vendorGstinStateName = $b_row['vendor_business_state'];

    $venderabn = queryGet("SELECT * FROM `erp_vendor_details` WHERE vendor_id=$vendorId");

    $abn = $venderabn['data'];
    $vendorGstin = $abn['vendor_gstin'];
}
?>

<div id="loaderGRN" class="grn-loader" style="display: none;">
    <img src="<?= BASE_URL ?>public/assets/gif/loadingGRN-data.gif" width="150" alt="">
</div>


<form action="" method="POST" id="addNewGRNForm" enctype="multipart/form-data">
    <div class="row grn-create po-grn-view upload-file">
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="card">
                <div class="card-header">
                    <div class="head">
                        <i class="fa fa-user"></i>
                        <h4>Vendor info </h4>
                    </div>
                </div>
                <div class="card-body" id="customerInfo">

                    <div class="row grn-vendor-details">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <input type="hidden" name="grnCode" value="<?= $grnNo ?>">
                            <input type="hidden" name="id" value="0">
                            <input type="hidden" name="grnType" value="grn">
                            <input type="hidden" name="vendorDocumentFile" value="">
                            <input type="hidden" name="vendorGstinStateName" value="<?= $vendorGstinStateName . '(' . $vendorGstinStateCode . ')'; ?>">
                            <input type="hidden" name="locationGstinStateName" value="<?= $customerGstinStateName . '(' . $customerGstinStateCode . ')' ?>">


                            <div class="display-flex grn-form-input-text flex-direction-row gap-2 mb-4">
                                <i class="fa fa-check"></i>
                                <?php
                                $check_var_sql = queryGet("SELECT * FROM `erp_month_variant` WHERE `month_variant_id`=$admin_variant");
                                $check_var_data = $check_var_sql['data'];

                                $max = $check_var_data['month_end'];
                                $min = $check_var_data['month_start'];
                                ?>
                                <p class="label-bold">Posting Date :</p>
                                <?php
                                $dates = postingDateValidation();
                                $start_date = $dates['start_date'];
                                $end_date = $dates['end_date'];
                                $selected_date = $dates['selected_date'];
                                ?>
                                <input type="date" name="invoicePostingDate" id="invoicePostingDate" value="<?= $selected_date; ?>" class="form-control" min="<?= $start_date ?>" max="<?= $end_date ?>" required>
                            </div>

                            <div id="vendorDocInfo">
                                <div class="po-grn-vendor-details vendorClass_<?= $vendorId ?>">


                                    <div class="row">
                                        <div class="col-12 col-md-12 col-lg-12">
                                            <h2><?= $vendorName ?>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-primary po-vendor-info-btn" data-bs-toggle="modal" data-bs-target="#vendordetailmodal_<?= $vendorId ?>">
                                                        <ion-icon name="information-outline"></ion-icon>
                                                    </button>
                                                    <button type="button" class="btn btn-primary file-input">
                                                        <ion-icon name="cloud-upload-outline"></ion-icon>
                                                        Upload
                                                        <input type="file" name="invoice_file_name[<?= $vendorId ?>]" id="fileInput" class="form-control">
                                                        <!-- <span class="button">Choose</span>--->
                                                        <!-- <span class="label" data-js-label>No file selected</label> -->
                                                    </button>
                                                </div>
                                            </h2>

                                        </div>
                                        <div class="col-12 col-md-12 col-lg-12">
                                            <div class="vendor-blocks">

                                                <div class="doc-detail doc-no">
                                                    <label for=""><ion-icon name="document-text-outline"></ion-icon>Document No</label>
                                                    <input type="text" name="documentNo[<?= $vendorId ?>]" value="" class="form-control" required>
                                                </div>

                                                <div class="doc-detail doc-date">
                                                    <label for=""><ion-icon name="calendar-outline"></ion-icon>Document Date</label>
                                                    <!-- <input type="date" name="documentDate[<?= $vendorId ?>]" value="" class="form-control" required> -->
                                                    <input type="date" id="documentdate_<?= $vendorId ?>" name="documentDate[<?= $vendorId ?>]" value="" class="form-control ddate" required>

                                                    <input type="hidden" id="creditp_<?= $vendorId ?>" value="<?= $vendorCreditPeriod ?>">

                                                </div>

                                                <div class="doc-detail due-date grn-form-input-text">

                                                    <?php

                                                    if ($dueDate == "" && $vendorCreditPeriod != "" && $delivery_date != "") {
                                                        $tempDueDate = date_create($delivery_date);
                                                        date_add($tempDueDate, date_interval_create_from_date_string($vendorCreditPeriod . " days"));
                                                        $dueDate = date_format($tempDueDate, "Y-m-d");
                                                    }
                                                    ?>
                                                    <label for=""> <ion-icon name="time-outline"></ion-icon>Due Date </label>
                                                    <input type="date" id="iv_due_date" name="invoiceDueDate[<?= $vendorId ?>]" value="<?= date("Y-m-d", strtotime($dueDate)); ?>" class="form-control" min="<?= $selected_date ?>" required>
                                                    <p class="text-danger text-xs" id="postdatelabel"></p>
                                                </div>



                                            </div>

                                        </div>

                                    </div>



                                    <div class="po-vendor-area">
                                        <div class="modal fade" id="vendordetailmodal_<?= $vendorId ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Vendor info</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
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
                                                        <?php
                                                        if ($vendorCode != "") {
                                                        ?>
                                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Status :&nbsp;</p>
                                                                <p class="status">Active</p>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php if ($companyCountry == 103) { ?>
                                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN :&nbsp;</p>
                                                                <p> <?= $vendorGstin ?></p>
                                                            </div>
                                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN Status :&nbsp;</p>
                                                                <p id="vendorGstinStatus_<?= $vendorId ?>" class="status">Loding...</p>
                                                            </div>

                                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">State :&nbsp;</p>
                                                                <p><?= $vendorGstinStateName ?>(<?= $vendorGstinStateCode ?>)</p>
                                                            </div>
                                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Customer State :&nbsp;</p>
                                                                <p><?= $customerGstinStateName ?>(<?= $customerGstinStateCode ?>)</p>
                                                            </div>
                                                        <?php } else {


                                                        ?>
                                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold"><?= $abnlable ?> :&nbsp;</p>
                                                                <p> <?= $vendorGstin ?></p>
                                                            </div>


                                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">State :&nbsp;</p>
                                                                <p><?= $vendorGstinStateName ?></p>
                                                            </div>

                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <!-- <div class="row grn-vendor-details">
                                <div class="po-grn-vendor-details vendorClass_142">
                                    <div class="head d-flex border-label">
                                        <h6 class="font-bold text-xs mb-0">Vendor info</h6>
                                        <hr>
                                    </div>
                                    <div class="dotted-border-area">
                                        <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Code :&nbsp;</p>
                                            <p id="invoiceVendorCodeSpan"><?= $vendorCode ?></p>
                                        </div>
                                        <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Name :&nbsp;</p>
                                            <p id="vendorName"><?= $vendorName ?></p>
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
                                        <?php if ($companyCountry == 103) { ?>
                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN :&nbsp;</p>
                                                <p> <?= $vendorGstin ?></p>
                                            </div>
                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GSTIN Status :&nbsp;</p>
                                                <p id="vendorGstinStatus_<?= $vendorCode ?>" class="status">Loding...</p>
                                            </div>


                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">State :&nbsp;</p>
                                                <p><?= $vendorGstinStateName ?>(<?= $vendorGstinStateCode ?>)</p>
                                            </div>
                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Customer State :&nbsp;</p>
                                                <p><?= $customerGstinStateName ?>(<?= $customerGstinStateCode ?>)</p>
                                            </div>
                                        <?php } ?>




                                    </div>
                                </div>
                            </div> -->
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
                        <h4>Doc info</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row grn-vendor-details">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="d-flex justify-content-between align-items-baseline gap-4">
                                <div class="left-div">
                                    <div class="title-head d-flex gap-2 mb-2">
                                        <i class="fa fa-check"></i>
                                        <p class="label-bold">PO Number :</p>
                                    </div>
                                    <div class="customInvoicePoNumberMain" id="customInvoicePoNumberMain">
                                        <input name="invoicePoNumber" type="hidden" id="hiddenInputPO" value="" class="form-control">
                                        <ul id="itemList" class="item-list"></ul>
                                        <div class="d-none add-po-number-area">
                                            <input class="form-control" type="text" id="customInvoicePoNumber" placeholder="enter manual PO here ..." />
                                            <button type="button" class="btn btn-primary" onclick="addItem()">Add</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="right-div">
                                    <div class="display-flex grn-form-input-text">
                                        <button type="button" class="btn btn-primary po-add-btn" data-toggle="modal" data-target="#multiplePOListTable" id="po_list_button">
                                            Select PO
                                            <i class="fa fa-plus pl-2"></i>
                                        </button>
                                        <p class="note">(You can select the multiple PO to process the GRN. Only open PO can be selected.)</p>
                                        <br>
                                        <button type="button" class="btn btn-primary po-add-btn" data-toggle="modal" data-target="#PostedGRNList" id="posted_grn_list_button">
                                            Select Posted GRN
                                            <i class="fa fa-plus pl-2"></i>
                                        </button>
                                        <p class="note">(You can select the posted grn to inventorise the below line item Value.)</p>

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
                                                                <tbody id="child_po_list_table">

                                                                </tbody>
                                                            </table>
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
                                        <div class="modal fade right" id="PostedGRNList" data-bs-keyboard="false" tabindex="-1" aria-labelledby="examplePendingGrnModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">Posted GRN List
                                                        <!-- <button type="button" class="btn btn-primary select-po-btn" id="selectPOBtn" disabled>Select</button> -->
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="multiplePolist">
                                                            <table class="table defaultDataTable grn-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th>GRN Number</th>
                                                                        <th>Invoice Number</th>
                                                                        <th>PO Number</th>
                                                                        <th>Vendor Name</th>
                                                                        <th>Vendor Code</th>
                                                                        <th>GST Number</th>
                                                                        <th>Total Amount</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="posted_grn_list">

                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grn-form-input-text">






                            </div>

                            <div class="display-flex grn-form-input-text flex-direction-row justify-content-between align-items-baseline">
                                <div class="form-input flex-block">
                                    <p class="label-bold d-flex gap-2"><i class="fa fa-check"></i>Functional Area :</p>
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
                                    <div class="display-flex grn-form-input-text mt-2">
                                        <p class="func-note"><span class="mr-2">*</span>Note : Map Functional area with this invoice to get the expense details functional area wise.</p>
                                    </div>
                                </div>
                                <?php
                                $comp_currency = $companyCurrencyData["currency_name"];
                                ?>
                                <div class="currency-conversion-section mt-3 dotted-border-area">
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

                            <ul class="upload-info">
                                <li>
                                    <div class="doc-preview dotted-border-area">
                                        <label for="" class="float-label"><?= $vendorName ?></label>
                                        <span class="label" data-js-label>No File Selected</span>
                                        <button type="button" class="btn btn-transparent preview-btn" id="iframePreview" data-toggle="modal" data-target="#pdfModal">
                                            <ion-icon name="eye-outline"></ion-icon>
                                        </button>
                                    </div>
                                </li>
                            </ul>


                            <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                                        <tbody id="">
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

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grn-table">
        <table class="table-sales-order table defaultDataTable grn-table" id="itemTable">
            <thead>
                <tr>
                    <th>Sl No.</th>
                    <th>Reference No.</th>
                    <th>Vendor Name</th>
                    <th>Vendor Code</th>
                    <th>Item Name</th>
                    <th>Internal Code</th>
                    <th>Item HSN</th>
                    <th>St. Loc / Cost Center</th>
                    <th>Received Qty</th>
                    <th>Unit Price</th>
                    <th>Basic Amount</th>
                    <th>Allocated Cost</th>
                    <?php
                    $getItemTaxRule = getItemTaxRule($companyCountry, $vendorGstinStateCode, $customerGstinStateCode);
                    $data = json_decode($getItemTaxRule['data'], true);
                    if ($companyCountry == 103) { ?>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>IGST</th>
                    <?php } else {

                        if (isset($data['tax']) && is_array($data['tax'])) {
                            foreach ($data['tax'] as $t) {
                                echo "<th>{$t['taxComponentName']}</th>";
                            }
                        }
                    ?>

                    <?php } ?>
                    <th><?= $tdslable ?> %</th>
                    <th>Delete Item</th>
                    <!-- <th>Total Amount</th> -->
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

                $defaultMultiBatchRows = [];
                $resArray = [];

                foreach (array_reverse($po_item_data) as $oneItemObj) {
                    // console($oneItemObj);
                    $oneItemData = $oneItemObj;

                    $itemHSN = "";
                    $itemName = $oneItemData["itemName"] ?? "";
                    $grnItemName = $oneItemData["itemName"] ?? "";
                    $itemQty = helperAmount($oneItemData["remainingQty"]) ?? "0";
                    $itemUnitPrice = $oneItemData["unitPrice"] ?? "0";
                    $invoice_units = $oneItemData["uom"] ?? "";
                    $internalItemuom_id = $oneItemData["baseUnitMeasure"];
                    $goodsType = $oneItemData["goodsType"];
                    $po_item_id = $oneItemData["po_item_id"];

                    $subtotal = $itemUnitPrice * $itemQty;

                    $tax_percentage = $oneItemData["taxPercentage"];

                    $itemTax = ($itemUnitPrice * $itemQty) * $tax_percentage / 100;

                    $Total = ($itemUnitPrice * $itemQty) + $tax_amt;

                    $itemTax = $itemTax == "" ? 0 : $itemTax;
                    $tds = 0;

                    $tds_id = $oneItemData["tds"];
                    $tds_query = queryGet("SELECT `TDSRate` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");
                    $tds = $tds_query["data"]["TDSRate"] ?? 0;

                    $basic_amt = ($itemUnitPrice * $itemQty);

                    $tds_value = $basic_amt * ($tds / 100);
                    if ($companyCountry == 103) {
                        if ($vendorGstinStateCode == $customerGstinStateCode) {
                            $cgst = $itemTax / 2;
                            $sgst = $itemTax / 2;
                            $igst = 0;
                        } else {
                            $cgst = 0;
                            $sgst = 0;
                            $igst = $itemTax == "" ? 0 : $itemTax;
                        }

                        $itemTotalPrice = ($itemUnitPrice * $itemQty) + $cgst + $sgst + $igst - $tds_value;
                    } else {
                        $cgst = 0;
                        $sgst = 0;
                        $igst = 0;
                        $itemTotalPrice = ($itemUnitPrice * $itemQty) + $itemTax - $tds_value;
                    }

                    $internalItemId = "";
                    $internalItemCode = "";
                    $internalItemHsn = "";

                    $internalItemCode = $oneItemData["itemCode"];

                    $internalItemId = $oneItemData["inventory_item_id"];
                    $checkitem = checkItemImpactById($internalItemId);
                    if ($checkitem['status'] != "success") {
                        $resArray[] = ['itemCode' => $internalItemCode, 'message' => $checkitem["message"]];
                        continue;
                    }
                    $internalItemUom = $oneItemData["uom"];


                    // $itemType = $oneItemData["type"];
                    $itemHSN = $oneItemData["hsnCode"];
                    $itemName = $oneItemData["itemName"];


                    array_push($po_ids, $oneItemData["po_item_id"]);

                    if ($itemName == "" || strtolower($itemName) == "cgst" || strtolower($itemName) == "sgst") {
                        continue;
                    }
                    $sl += 1;
                ?>
                    <?php

                    if ($use_type == "servicep") {
                    ?>
                        <tr class="serviceclass" id="grnItemRowTr_<?= $sl ?>">
                        <?php
                    } else {
                        ?>
                        <tr class="goodsclass" id="grnItemRowTr_<?= $sl ?>">
                        <?php
                    }
                        ?>
                        <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                        <input type="hidden" id="internalPoItemId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][PoItemId]" value="<?= $oneItemData["po_item_id"] ?>" />
                        <input type="hidden" class="linePoNumber" id="internalItemPo_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemPurchaseOrder]" value="<?= $id ?>" />
                        <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                        <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                        <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                        <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemQty]" value="<?= helperQuantity($itemQty) ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPriceStatic" id="ItemInvoiceTotalPriceStatic_<?= $sl ?>" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                        <!-- <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                        <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                        <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPrice" id="ItemInvoiceTotalPrice_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTotalPrice]" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemInvoiceTDSValue]" value="<?= $tds_value ?>" />

                        <?php if ($companyCountry == 103) { ?>
                            <input type="hidden" class="ItemInvoiceCGSTClass" id="ItemInvoiceCGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCGST]" value="<?= $cgst ?>" />
                            <input type="hidden" class="ItemInvoiceSGSTClass" id="ItemInvoiceSGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemSGST]" value="<?= $sgst ?>" />
                            <input type="hidden" class="ItemInvoiceIGSTClass" id="ItemInvoiceIGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemIGST]" value="<?= $igst ?>" />
                            <input type="hidden" id="ItemInvoiceCGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCGSTNew]" value="<?= $cgst ?>" />
                            <input type="hidden" id="ItemInvoiceSGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemSGSTNew]" value="<?= $sgst ?>" />
                            <input type="hidden" id="ItemInvoiceIGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemIGSTNew]" value="<?= $igst ?>" />
                            <?php } else {
                            if (isset($data['tax']) && is_array($data['tax'])) {

                                foreach ($data['tax'] as $t) {

                                    $tax_per = $t['taxPercentage'];
                            ?>
                                    <input type="hidden" class="ItemInvoice<?= $t['taxComponentName'] ?>Class" id="ItemInvoice<?= $t['taxComponentName'] ?>_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][item<?= $t['taxComponentName'] ?>]" value="<?= round(($itemTax * $tax_per) / 100, 2); ?>" />
                                    <input type="hidden" id="ItemInvoice<?= $t['taxComponentName'] ?>New_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][item<?= $t['taxComponentName'] ?>New]" value="<?= round(($itemTax * $tax_per) / 100, 2); ?>" />

                                <?php
                                }
                                ?>


                        <?php
                            }
                        } ?>

                        <input type="hidden" id="hiddenTaxValues_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][hiddenTaxValues]" value="">

                        <input type="hidden" id="ItemInvoiceUnits_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnits]" value="<?= $invoice_units ?>" />
                        <input type="hidden" id="ItemInvoiceUOM_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUOM]" value="<?= $internalItemUom ?>" />
                        <input type="hidden" id="itemStockQty_<?= $sl ?>" value="<?= helperQuantity($itemQty) ?>" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemStockQty]">
                        <input type="hidden" id="allocated_array_<?= $sl ?>" value="" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][allocated_array]">
                        <input type="hidden" id="temporary_allocated_array_<?= $sl ?>" value="" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][temporary_allocated_array]">

                        <?php

                        if ($use_type == "servicep") {
                        ?>
                            <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
                        <?php
                        } else {
                        ?>
                            <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemInvoiceGoodsType]" value="goods" />
                        <?php

                        }
                        ?>

                        <input type="hidden" id="ItemInvoiceUOMID_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUOMID]" value="<?= $internalItemuom_id ?>" />
                        <input type="hidden" id="itemVendorName_<?= $sl ?>" value="<?= $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0]['trade_name'] ?>" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][vendorName]">
                        <input type="hidden" id="itemVendorCode_<?= $sl ?>" value="<?= $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0]['vendor_code'] ?>" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][vendorCode]">
                        <input type="hidden" id="itemVendorId_<?= $sl ?>" value="<?= $vendor_id ?>" class="form-control lineVendorId" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][vendor_id]">
                        <input type="hidden" id="" value="pending" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][postedType]">
                        <td><?= $sl ?></td>
                        <td id="grnItemPOTdSpan_<?= $sl ?>">
                            <p class="pre-normal <?= $id ?>"><?= $id ?></p>
                        </td>
                        <td>
                            <?= $BranchPoObj->fetchVendorDetails($poDetail['vendor_id'])['data'][0]['trade_name'] ?>
                        </td>
                        <td>
                            <?= $BranchPoObj->fetchVendorDetails($poDetail['vendor_id'])['data'][0]['vendor_code'] ?>
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

                        <?php
                        if ($use_type == "servicep") {
                            if ($goodsType == 5 || $goodsType == 7) {
                        ?>
                                <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                                    <select class="form-control text-xs costCenterSelect itemCostCenterId_<?= $sl ?>" id="itemCostCenterId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemStorageLocationId]" required>
                                        <option value="">Select Cost Center</option>
                                        <?php
                                        foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                            echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                                        }
                                        ?>
                                        <option value="inventorise_<?= $sl ?>"><strong>Inventorise</strong></option>
                                    </select>


                                    <div class="modal cost-center-modal fade" id="costCenterModal_<?= $sl ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="inventorizeButton">Inventorise Cost</h1>
                                                    <p>Distribution Cost: <span class="text-sm font-bold" id="distribution_cost_<?= $sl ?>">0</span></p>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="inner-section">
                                                        <table>
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>Item Code</th>
                                                                    <th>Item Name</th>
                                                                    <th>Vendor Code</th>
                                                                    <th>Vendor Name</th>
                                                                    <th>Unit Price</th>
                                                                    <th>Quantity</th>
                                                                    <th>Basic Amount</th>
                                                                    <th>Inventorise Amount</th>
                                                                    <th>Allocated Cost</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="costCenterDetailsBody_<?= $sl ?>" id="costCenterId_<?= $sl ?>">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary distributeButtonClass" id="distributeButton_<?= $sl ?>" data-value="0">Distribute Cost</button>
                                                    <button type="button" class="btn btn-primary inventButtonClass" id="inventButton_<?= $sl ?>" data-itemId="<?= $internalItemId ?>" data-allocatedArray="" data-sl="<?= $sl ?>">Corfirm</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            <?php
                            } else { ?>
                                <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                                    <select class="form-control text-xs costCenterSelect itemCostCenterId_<?= $sl ?>" id="itemCostCenterId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemStorageLocationId]" required>
                                        <option value="">Select Cost Center</option>
                                        <?php
                                        foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                            echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                        }
                                        ?>
                                    </select>

                                <?php
                            }
                        } else {
                                ?>

                                <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                                    <select class="form-control text-xs storageLocationSelect" <?php if ($use_type == 'asset') echo "readonly" ?> id="itemStorageLocationId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemStorageLocationId]" required>
                                        <option value="">Select storage location</option>
                                        <?php


                                        $itemId = $internalItemId;
                                        $summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$itemId' AND `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'", false);


                                        // getqaListForGrnObj
                                        if ($summary["data"]["quality_enabled"] == '1') {
                                            foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                                if ($oneRmStorageLocation["storage_location_id"] == $summary["data"]["qa_storage_location"]) {
                                                    echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                                } else {
                                                    echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                                }
                                            }
                                        } else {
                                            if ($goodsType == 5 || $goodsType == 7) {

                                                foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                                    echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                                                }
                                            } else {
                                                foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                                    if ($use_type == "asset") {
                                                        if ($oneRmStorageLocation['storage_location_type'] === "ASSET" && $oneRmStorageLocation['storage_location_name'] == "Asset-FG") {
                                                            echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation['storage_location_name'] . '</option>';
                                                        }
                                                    } else {
                                                        if ($oneRmStorageLocation["storage_location_id"] == $summary["data"]["default_storage_location"]) {
                                                            echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                                        } else {
                                                            echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                                        }
                                                    }
                                                }
                                            }
                                        }


                                        ?>
                                    </select>
                                </td>


                            <?php
                        }
                            ?>

                            <td>
                                <div class="form-input d-flex gap-2">
                                    <input step="any" type="number" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemReceivedQty]" value="<?= helperQuantity($itemQty) ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs received_quantity inputQuantityClass" required>
                                    <input type="hidden" name="poItemId[<?= $po_item_id ?>]" id="grnPoInputQty_<?= $sl ?>" value="0">
                                    <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemRemainQty]" id="grnPoInputRemainQty_<?= $sl ?>" value="0">
                                    <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][poQty]" id="grnPoQty_<?= $sl ?>" value="<?= helperQuantity($itemQty) ?>">
                                    <p class="text-xs"><?= $internalItemUom ?></p>
                                </div>
                            </td>
                            <!-- <td class="text-right" id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= number_format($itemUnitPrice, 2) ?></td> -->
                            <td>
                                <div class="input-group input-group-sm m-0" style="flex-wrap: nowrap;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text spanInvoiceCurrencyName" id="spanInvoiceCurrencyName_<?= $sl ?>"><?= $curr_name ?></span>
                                    </div>
                                    <input type="number" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPriceOtherCurrency]" value="<?= helperAmount($itemUnitPrice) ?>" id="grnItemUnitPriceTdInput_<?= $sl ?>" class="form-control border py-3 text-right itemUnitPrice" required readonly>
                                    <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPricehidden]" value="<?= helperAmount($itemUnitPrice) ?>" id="grnItemUnitPriceTdInputhidden_<?= $sl ?>" class="form-control text-xs itemUnitPricehidden">
                                    <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPrice]" value="<?= helperAmount($itemUnitPrice) ?>" id="grnItemUnitPriceInrhidden_<?= $sl ?>" class="form-control text-xs grnItemUnitPriceInrhidden">
                                </div>
                                <span class="text-small spanUnitPriceINR" id="spanUnitPriceINR_<?= $sl ?>"></span>

                            </td>
                            <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= $curr_name . " : " . helperAmount($itemUnitPrice * $itemQty) ?>
                                <p class="text-small spanBasePriceINR" id="spanBasePriceINR_<?= $sl ?>"></p>
                            </td>
                            <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][allocatedCost]" id="grnItemAllocatedCosthidden_<?= $sl ?>" value="0">
                            <td id="grnItemAllocatedCost_<?= $sl ?>">0</td>
                            <?php if ($companyCountry == 103) { ?>
                                <td class="text-right" id="grnItemInvoiceCGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . helperAmount($cgst) ?>
                                    <span class="text-small spanCgstPriceINR" id="spanCgstPriceINR_<?= $sl ?>"></span>
                                </td>
                                <td class="text-right" id="grnItemInvoiceSGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . helperAmount($sgst) ?>
                                    <span class="text-small spanSgstPriceINR" id="spanSgstPriceINR_<?= $sl ?>"></span>
                                </td>
                                <td class="text-right" id="grnItemInvoiceIGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . helperAmount($igst) ?>
                                    <span class="text-small spanIgstPriceINR" id="spanIgstPriceINR_<?= $sl ?>"></span>
                                </td>
                                <?php } else {
                                if (isset($data['tax']) && is_array($data['tax'])) {
                                    foreach ($data['tax'] as $t) {
                                        $tax_per = $t['taxPercentage'];
                                ?>
                                        <td class="text-right" id="grnItemInvoice<?= $t['taxComponentName'] ?>TdSpan_<?= $sl ?>">
                                            <?= $curr_name . " : " . helperAmount(($itemTax * $tax_per) / 100) ?>
                                            <span class="text-small span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR" id="span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR_<?= $sl ?>"></span>
                                        </td>
                            <?php
                                    }
                                }
                            } ?>
                            <td>
                                <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                    <input type="number" step="any" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTds]" value="<?= $tds ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-xs itemTds inputQuantityClass" required>
                                    <p class="text-xs">%</p>
                                </div>
                            </td>
                            <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= helperAmount($itemTotalPrice) ?> </span>
                            <input type="hidden" value="<?= $tax_percentage ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">

                            <?php
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

                            ?>

                            <input type="hidden" value="<?= $options ?>" id="grnItemAllBins_<?= $sl ?>" class="form-control">

                            <td class="text-right" id="grnItemDeleteTdSpan_<?= $sl ?>">

                                <?php
                                if ($use_type != "servicep") {
                                ?>
                                    <button type="button" class="btn-view btn btn-primary delShedulingBtn" data-toggle="modal" data-storage="<?= $summary["data"]["default_storage_location"] ?>" data-target="#deliveryScheduleModal_<?= $sl ?>" id="grnSettingsButton_<?= $sl ?>">
                                        <i id="statusItemBtn_<?= $internalItemId ?>" class="statusItemBtn fa fa-cog"></i>
                                    </button>
                                <?php
                                }
                                ?>

                                <button title="Delete Item" type="button" id="grnItemDeleteButton_<?= $sl ?>" class="btn btn-sm remove_row" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button>

                                <div class="modal modal-left left-item-modal fade deliveryScheduleModal discountViewModal discountViewModal_<?= $sl ?>" id="deliveryScheduleModal_<?= $sl ?>" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="left_modal">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"><?= $itemName ?></h5>
                                            </div>
                                            <div class="modal-body multiBatchModelViewBody_<?= $sl ?>">
                                                <div class="qty-title d-flex justify-content-between mb-1 mb-3 pb-2 border-bottom">
                                                    <h6 class="modal-title text-xs font-bold">Total Quantity: <span class="totalItemAmountModal" id="totalItemAmountModal_<?= $sl ?>"><?= helperQuantity($itemQty) ?></span></h6>
                                                    <div class="check-box text-left font-bold text-xs">
                                                        <input type="checkbox" class="grnEnableCheckBxClass" value="1" id="grnEnableCheckBx_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][activateBatch]"> Enable check box to insert the manual Batch
                                                        <input type="hidden" name="" id="grnStoreId_<?= $sl ?>" value="<?= $summary["data"]["default_storage_location"] ?? "" ?>">
                                                    </div>
                                                </div>
                                                <p class="note mb-3">
                                                    By default the generated doc (GRN000927) number will be the batch number
                                                </p>
                                                <div class="modal-add-row" id="modal-add-row_<?= $sl ?>">

                                                </div>
                                                <?php
                                                $defaultMultiBatchRows[] = [
                                                    "vendorId" => $vendor_id,
                                                    "sl" => $sl,
                                                    "qty" => $itemQty
                                                ];
                                                ?>

                                            </div>
                                            <div class="modal-footer modal-footer-fixed">
                                                <button type="button" class="btn btn-primary w-100" data-dismiss="modal" id="saveAndClose_<?= $sl ?>">Save & Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                            <td class="text-right" id="grandSubTotalTd" style="background: none;"><?= $curr_name . " : " . helperAmount($totalSubtotal) ?>
                                <span class="text-small spanSubTotalINR" id="spanSubTotalINR"></span>
                            </td>
                        </tr>

                        <?php
                        if ($companyCountry == 103) {


                            $toalTotal = $GrandtoalTotal;
                            $totalCGST = $grandcgst;
                            $totalSGST = $grandsgst;
                            $totalIGST = $grandigst;

                        ?>
                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total CGST</td>
                                <td class="text-right" style="background: none;" id="grandCgstTd"><?= $curr_name . " : " . helperAmount($totalCGST) ?>
                                    <span class="text-small spanCgstGrandINR" id="spanCgstGrandINR"></span>
                                </td>
                            </tr>
                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total SGST</td>
                                <td class="text-right" style="background: none;" id="grandSgstTd"><?= $curr_name . " : " . helperAmount($totalSGST) ?>
                                    <span class="text-small spanSgstGrandINR" id="spanSgstGrandINR"></span>
                                </td>
                            </tr>

                            <tr class="itemTotals">
                                <td colspan="9" style="background: none;">Total IGST</td>
                                <td class="text-right" style="background: none;" id="grandIgstTd"><?= $curr_name . " : " . helperAmount($totalIGST) ?>
                                    <span class="text-small spanIgstGrandINR" id="spanIgstGrandINR"></span>
                                </td>
                            </tr>

                            <?php } else {
                            if (isset($data['tax']) && is_array($data['tax'])) {
                                foreach ($data['tax'] as $t) {
                                    $tax_per = $t['taxPercentage'];
                            ?>

                                    <tr class="itemTotals">
                                        <td colspan="9" style="background: none;">Total <?= $t['taxComponentName'] ?></td>
                                        <td class="text-right" style="background: none;" id="grand<?= ucfirst(strtolower($t['taxComponentName'])) ?>Td"><?= $curr_name . " : " . helperAmount(($total_tax * $tax_per) / 100) ?>
                                            <span class="text-small span<?= ucfirst(strtolower($t['taxComponentName'])) ?>GrandINR" id="span<?= ucfirst(strtolower($t['taxComponentName'])) ?>GrandINR"></span>
                                            <input type="hidden" id="total<?= $t['taxComponentName'] ?>" name="totalInvoice<?= $t['taxComponentName'] ?>" value="<?= ($total_tax * $tax_per) / 100 ?>">

                                        </td>
                                    </tr>

                        <?php
                                }
                            }
                        } ?>
                        <tr class="itemTotals">
                            <td colspan="9" style="background: none;">Total <?= $tdslable; ?></td>
                            <td class="text-right" id="grandTds" style="background: none;">-<?= helperAmount($totalTdsValue) ?></td>
                        </tr>
                        <tr class="itemTotals">
                            <input type="hidden" id="totalCGST" name="totalInvoiceCGST" value="<?= $totalCGST ?>">
                            <input type="hidden" id="hiddenGrandTaxValues" name="hiddenGrandTaxValues" value="">

                            <input type="hidden" id="totalSGST" name="totalInvoiceSGST" value="<?= $totalSGST ?>">
                            <input type="hidden" id="totalIGST" name="totalInvoiceIGST" value="<?= $totalIGST ?>">
                            <input type="hidden" id="totalTDS" name="totalInvoiceTDS" value="<?= $totalTdsValue ?>">
                            <input type="hidden" id="grandSubTotal" name="totalInvoiceSubTotal" value="<?= $totalSubtotal ?>">
                            <input type="hidden" id="grandTotal" name="totalInvoiceTotal" value="<?= $toalTotal ?>">
                            <td colspan="9" style="background: none;">Total Amount</td>
                            <td class="text-right" id="grandTotalTd" style="background: none;"><?= $curr_name . " : " . helperAmount($toalTotal) ?>
                                <span class="text-small spangrandTotalINR" id="spangrandTotalINR"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <input type="hidden" name="poStatus" value="0">
    <input type="hidden" name="addNewGrnFormSubmitBtn" value="formSubmit">
    <button type="submit" id="addNewGrnFormSubmitBtn" disabled value="Submit GRN" class="btn btn-primary float-right mt-3 mb-3">Submit GRN</button>

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

</div>


<script>
    var countVendor = 1;
    var serial_number = <?= json_encode($sl) ?>;

    function inputValuegrn(number) {
        if (number !== null && number !== "") {
            number = number ?? 0;
            let num = parseFloat(number);
            if (isNaN(num)) {
                return number;
            }
            let base = <?= $decimalValue ?>;
            let factor = Math.pow(10, base);
            let truncated = Math.trunc(num * factor) / factor; // Truncate instead of rounding

            // Ensure correct decimal places using toFixed()
            return truncated.toFixed(base);
        }
        return "";
    }



    function inputQuantity(number) {
        if (number !== null && number !== "") {
            number = number ?? 0;
            let num = parseFloat(number);
            if (isNaN(num)) {
                return number;
            }
            let base = <?= $decimalQuantity ?>;
            let factor = Math.pow(10, base);
            let truncated = Math.trunc(num * factor) / factor; // Truncate instead of rounding
            return truncated.toString().replace(/,/g, ''); // Ensure no commas
        }
        return "";
    }

    $(document).ready(function() {

        $("#addNewGRNForm").on("submit", function(e) {

            e.preventDefault(); // Prevent default form submission
            // alert("oo");
            // let inputqtyvalue = inputQuantity($('.multiQuantity').val());
            // $('.multiQuantity').val(inputqtyvalue);

            // let delqtyvalue = inputQuantity($('.multiQuantity').val());
            // $('.multiQuantity').val(delqtyvalue);

            // let itemUnitPrice = inputValue($('.itemUnitPrice').val());
            // $('.itemUnitPrice').val(itemUnitPrice);

            this.submit();
        });
        $('#fileInput').change(function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    // Display PDF preview in modal body
                    $('#previewModalContainer').html('<embed src="' + e.target.result + '" width="100%" height="100%">');
                };
                reader.readAsDataURL(file);
            } else {

            }
        });
    });
    $(document).on("input keyup paste blur", ".inputQuantityClass", function() {
        let val = $(this).val();
        let base = <?= $decimalQuantity ?>;
        // Allow only numbers and one decimal point
        if (val.includes(".")) {
            let parts = val.split(".");
            if (parts[1].length > base) {
                $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
            }
        }
    });

    $(document).on("input keyup paste blur", ".inputAmountClass", function() {
        let val = $(this).val();
        let base = <?= $decimalValue ?>;
        // Allow only numbers and one decimal point
        if (val.includes(".")) {
            let parts = val.split(".");
            if (parts[1].length > base) {
                $(this).val(parts[0] + "." + parts[1].substring(0, base)); // Restrict extra decimals
            }
        }
    });
</script>


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
    var inputs = document.querySelectorAll(".upload-file");

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


    let multipleBatchRowNo = 0;
    let multipleBatchRowNumber = 0;



    function addGrnItemMultipleBatch(batchVendorId, slNumber, qty = 0, isFirstRow = false, bins = null) {
        multipleBatchRowNo += 1;

        let checkboxEnable = document.getElementById(`grnEnableCheckBx_${slNumber}`).checked;

        var batchHtml = `
            <div class="modal-add-row">
                <div class="row manual-grn-plus-modal modal-cog-right${isFirstRow ? ' dotted-border-area mx-1' : ''}">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="form-input">
                            <label>Batch Number</label>
                            <input type="text" name="grnItemList[${batchVendorId}][${slNumber}][multipleBatch][${multipleBatchRowNo}][batchNumber]" class="form-control multiBatch multiBatchRowNumber_${slNumber}" data-itemid="${slNumber}" value="${!checkboxEnable ? 'GRNXXXXXXXXX' : ''}" id="multiDeliveryDate_${multipleBatchRowNo}" placeholder="Batch Number" ${checkboxEnable ? '' : 'readonly'}>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="form-input">
                            <label>Quantity</label>
                            <input type="number" name="grnItemList[${batchVendorId}][${slNumber}][multipleBatch][${multipleBatchRowNo}][quantity]" class="form-control multiQuantity multiBatchRowQuantity_${slNumber}" data-itemid="${slNumber}" id="multiQuantity_${multipleBatchRowNo}" placeholder="quantity" value="${qty}" ${isFirstRow ? 'readonly':''}>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="form-input">
                            <label>Bin</label>
                            <select class="form-control multiBatchOptions_${slNumber}" id="" name="grnItemList[${batchVendorId}][${slNumber}][multipleBatch][${multipleBatchRowNo}][bin]">
                                <option value="0">Select Bin</option>` + bins + `</select>
                    </div>
                    </div>
                    ${isFirstRow ? (`
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <a style="cursor: pointer" class="btn btn-primary addQtyBtn addQtyBtnMultiOptions_${slNumber}" data-optiondata="" id="addQtyBtn_${slNumber}_${batchVendorId}">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>`) : (`
                    <div class="col-lg-1 col-md-1 col-sm-1 dlt-popup deleteQtyBtnMultiOptions_${slNumber}" data-rowno="${multipleBatchRowNo}" id="deleteQtyBtn_${slNumber}_${batchVendorId}">
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

    function addGrnItemMultipleBatchNew(batchVendorId, slNumber, qty = 0, isFirstRow = false, bins = null) {
        multipleBatchRowNumber += 1;

        var batchHtml = `
            <div class="modal-add-row">
                <div class="row manual-grn-plus-modal modal-cog-right${isFirstRow ? ' dotted-border-area mx-1' : ''}">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="form-input">
                            <label>Batch Number</label>
                            <input type="text" name="grnItemList[${batchVendorId}][${slNumber}][multipleBatch][${multipleBatchRowNumber}][batchNumber]" class="form-control multiBatch multiBatchRowNumber_${slNumber}" data-itemid="${slNumber}" value="GRNXXXXXXXXX" id="multiDeliveryDate_${multipleBatchRowNumber}" placeholder="Batch Number" readonly>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="form-input">
                            <label>Quantity</label>
                            <input type="number" name="grnItemList[${batchVendorId}][${slNumber}][multipleBatch][${multipleBatchRowNumber}][quantity]" class="form-control multiQuantity multiBatchRowQuantity_${slNumber}" data-itemid="${slNumber}" id="multiQuantity_${multipleBatchRowNumber}" placeholder="quantity" value="${qty}" ${isFirstRow ? 'readonly':''}>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <div class="form-input">
                            <label>Bin</label>
                            <select class="form-control multiBatchOptions_${slNumber}" id="" name="grnItemList[${batchVendorId}][${slNumber}][multipleBatch][${multipleBatchRowNumber}][bin]">
                                <option value="0">Select Bin</option>` + bins + `</select>
                    </div>
                    </div>
                    ${isFirstRow ? (`
                    <div class="col-lg-1 col-md-1 col-sm-1">
                        <a style="cursor: pointer" class="btn btn-primary addQtyBtn addQtyBtnMultiOptions_${slNumber}" data-optiondata="" id="addQtyBtn_${slNumber}_${batchVendorId}">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>`) : (`
                    <div class="col-lg-1 col-md-1 col-sm-1 dlt-popup deleteQtyBtnMultiOptions_${slNumber}" data-rowno="${multipleBatchRowNumber}" id="deleteQtyBtn_${slNumber}_${batchVendorId}">
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
        var type = "goods";
        var obj = <?= json_encode($getStorageLocationListForGrnObj) ?>;
        var id = <?= json_encode($id) ?>;
        var inv_date = <?= json_encode($documentDate) ?>;
        var company_currency = <?= json_encode($comp_currency)  ?>;
        var curr_name = <?= json_encode($curr_name)  ?>;
        var curr_id = <?= json_encode($currency)  ?>;
        var conversion_rate = <?= json_encode($conversion_rate)  ?>;

        var po_id = <?= json_encode($po_id) ?>;
        var newInputArray = [];
        var vendor_id = <?= json_encode($vendorId) ?>;
        var vendor_id_array = [];
        var po_id_array = [];
        var currency_loaded = 0;
        var po_loaded = 0;
        var posted_loader = 0;

        $("#allocate_id").hide();

        $('#invoicePostingDate').on('change', function() {
            let selectedDate = $(this).val();
            let creditPeriod = <?= json_encode($vendorCreditPeriod); ?>;
            // alert(creditPeriod);
            if (selectedDate && !isNaN(creditPeriod)) {
                let tempDueDate = new Date(selectedDate);
                tempDueDate.setDate(tempDueDate.getDate() + parseInt(creditPeriod, 10));
                let dueDate = tempDueDate.toISOString().split('T')[0];
                $('#iv_due_date').val(dueDate);
                $('#iv_due_date').attr('min', selectedDate);

            } else {
                console.error("Invalid date or credit period");
            }
        });

        vendor_id_array.push(vendor_id);
        po_id_array.push(id);


        //due date base on document date
        $(document).on('change', '.ddate', function() {
            var vendorIdMatch = $(this).attr('name').match(/\d+/);
            if (!vendorIdMatch) return; // Prevent errors if vendorId is not found

            var vendorId = vendorIdMatch[0];
            var documentDate = $(this).val(); // Get document date
            // var creditPeriod = $('#creditp_' + vendorId).val(); // Get credit period
            var creditPeriod = $('#creditp_' + vendorId).val().trim(); // Get credit period & trim spaces
            creditPeriod = creditPeriod ? parseInt(creditPeriod) || 0 : 0;

            if (documentDate) {
                var dueDate = new Date(documentDate);
                if (isNaN(dueDate.getTime())) return; // Prevent invalid date errors

                dueDate.setDate(dueDate.getDate() + parseInt(creditPeriod)); // Add credit period days

                var dueDateFormatted = dueDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD

                $('input[name="invoiceDueDate[' + vendorId + ']"]').val(dueDateFormatted); // Update due date field
            }
        });

        //GRN Loading...

        function multiplePo(poNumber, type, serial_number, listType) {

            if (type == "add") {

                // alert(newInputArray);

                $.ajax({
                    url: "ajaxs/grn/ajax-grn-po-items-new.php?po=" + poNumber + "&serial_number=" + serial_number + "&listtype=" + listType,
                    type: "GET",
                    beforeSend: function() {
                        console.log("Adding new items...");
                        $("#loaderGRN").show();
                    },
                    success: function(responseData) {
                        let totalRows=0;
                        let newRows = $(responseData);
                        if (listType === "service") {
                            totalRows = newRows.filter('tr.serviceclass').length;

                        } else {
                            totalRows = newRows.filter('tr.goodsclass').length;

                        }



                        let uniqueClass = "po-row-" + poNumber;
                        newRows.filter("tr").addClass(uniqueClass);
                        $("#itemsTable").append(newRows);
                        updateSerialNumber(totalRows);
                        currency_change(curr_name);
                        po_id_array.push(poNumber);
                        setTimeout(function() {
                            $("#loaderGRN").hide();
                        }, 1000);
                        // serial_number = 7;
                    }
                });

                $.ajax({
                    url: "ajaxs/grn/ajax-grn-vendor-details.php?po=" + poNumber,
                    type: "GET",
                    beforeSend: function() {
                        $("#loaderGRN").show();
                    },
                    success: function(responseData) {

                        var vendorIdajax = $(responseData).find("#invoiceVendorIdInput").val();
                        if (vendor_id_array.includes(vendorIdajax)) {

                        } else {
                            vendor_id_array.push(vendorIdajax);
                            $("#vendorDocInfo").append(responseData);
                        }
                        setTimeout(function() {
                            $("#loaderGRN").hide();
                        }, 1000);
                    }
                });

                $.ajax({
                    url: "ajaxs/grn/ajax-grn-file-details.php?po=" + poNumber,
                    type: "GET",
                    beforeSend: function() {},
                    success: function(responseData) {

                        var uniqueClass = 'unique-class-' + poNumber;
                        var responseHTML = $('<div>').html(responseData);
                        var ulElement = responseHTML.find('ul.upload-info').addClass(uniqueClass);

                        $("#uploaded-invoice-preview-div").append(ulElement);
                        setTimeout(function() {

                        }, 1000);
                    }
                });




            } else {
                removePO(poNumber);
                currency_change(curr_name);
            }
        }

        function removePO(poNumber) {



            let input = document.getElementById("hiddenInputPO");
            let arr = JSON.parse(input.value);
            arr = arr.filter(val => val !== poNumber);
            input.value = JSON.stringify(arr);
            document.querySelector(`#itemList li[class="${poNumber}"]`)?.remove();
            let uniqc = "po-row-" + poNumber;
            $("." + uniqc).remove();
            let uniqueClass = 'unique-class-' + poNumber;
            $("." + uniqueClass).remove();
            document.querySelectorAll('.po-input_checkbox').forEach(function(checkbox) {
                if (checkbox.value === poNumber) {
                    checkbox.checked = false;
                }
            });
            deleteVendor(poNumber);
            removeFromArray(poNumber);

        }


        //GRN Loading...

        function multipleGrnList(grnNumber, type, serial_number, listType) {


            if (type == "add") {

                $.ajax({
                    url: "ajaxs/grn/ajax-posted-grn-items-new.php?grn=" + grnNumber + "&serial_number=" + serial_number + "&listtype=" + listType,
                    type: "GET",
                    beforeSend: function() {
                        console.log("Adding new items...");
                        $("#loaderGRN").show();
                    },
                    success: function(responseData) {
                        let newRows = $(responseData);
                        let totalRows = newRows.filter('tr.goodsclass').length;



                        // Generate a base class name (optional: add timestamp or counter for uniqueness)
                        let uniqueClass = "po-row-" + grnNumber; // e.g., po-row-1715583472000

                        // Add the unique class to each <tr> in the response
                        newRows.filter("tr").addClass(uniqueClass);
                        $("#itemsTable").append(newRows);
                        updateSerialNumber(totalRows);

                        // currency_change(curr_name);

                        // console.log(responseData);
                        setTimeout(function() {
                            $("#loaderGRN").hide();
                        }, 5000);
                    }
                });

            } else {
                //remove lines
                // $("." + grnNumber).parent().parent().remove();
                removePO(grnNumber);
                currency_change(curr_name);
            }

        }


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



        $(document).on("keyup", ".itemUnitPrice", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        $(document).on("keyup", ".itemTds", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        $(document).on("keyup", ".received_quantity", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);

            let $input = $(`#grnItemReceivedQtyTdInput_${rowNo}`);
            let receivedQuantityValue = parseFloat($input.val());
            let defaultValue = parseFloat($input.attr('value'));

            var binlistHtml = $(`#grnItemAllBins_${rowNo}`).val();
            var vendorIdValue = $(`#itemVendorId_${rowNo}`).val();

            addGrnItemMultipleBatchNew(vendorIdValue, rowNo, receivedQuantityValue, true, binlistHtml);
            $(`#totalItemAmountModal_${rowNo}`).html(receivedQuantityValue);


            if (receivedQuantityValue > defaultValue) {
                $input.val(defaultValue);
                $(`.grnItemReceivedQtyTdInput_${rowNo}`).remove();
                $(`#grnItemReceivedQtyTdInput_${rowNo}`)
                    .parent()
                    .append(
                        `<span class="error grnItemReceivedQtyTdInput_${rowNo}" style="top:22px; left:-138px">Value can't be grater than default value</span>`
                    );
            } else {
                $(`.grnItemReceivedQtyTdInput_${rowNo}`).remove();
            }
        });







        function calculateOneItemAmounts(rowNo) {

            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;
            let tds = (parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) : 0;
            let poQty = (parseFloat($(`#grnPoQty_${rowNo}`).val()) > 0) ? parseFloat($(`#grnPoQty_${rowNo}`).val()) : 0;
            var basicPrice = itemUnitPrice * itemQty;
            var tax = (parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) : 0;
            var taxArray = [];
            var curr_name = $("#selectCurrency").find(':selected').data("currname");


            <?php if ($companyCountry == 103) { ?>
                let cgst = (parseFloat($(`#ItemInvoiceCGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceCGSTNew_${rowNo}`).val()) : 0;
                let sgst = (parseFloat($(`#ItemInvoiceSGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceSGSTNew_${rowNo}`).val()) : 0;
                let igst = (parseFloat($(`#ItemInvoiceIGSTNew_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceIGSTNew_${rowNo}`).val()) : 0;
                let itemStaticPrice = (parseFloat($(`#ItemInvoiceTotalPriceStatic_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceTotalPriceStatic_${rowNo}`).val()) : 0;
                var currency_rate_value = $(`#currency_conversion_rate`).val();


                let cgstPercent = (cgst / itemStaticPrice) * 100;
                let sgstPercent = (sgst / itemStaticPrice) * 100;
                let igstPercent = (igst / itemStaticPrice) * 100;

                let cgst_value = basicPrice * (cgstPercent / 100);
                let sgst_value = basicPrice * (sgstPercent / 100);
                let igst_value = basicPrice * (igstPercent / 100);
                console.log(cgst_value);
                console.log(sgst_value);
                console.log(igst_value);
                console.log(igstPercent);
                console.log(sgstPercent);
                console.log(cgstPercent);
                console.log(currency_rate_value);
                $(`#ItemInvoiceCGST_${rowNo}`).val((cgst_value / currency_rate_value).toFixed(2));
                $(`#ItemInvoiceSGST_${rowNo}`).val((sgst_value / currency_rate_value).toFixed(2));
                $(`#ItemInvoiceIGST_${rowNo}`).val((igst_value / currency_rate_value).toFixed(2));
                let totalItemPrice = basicPrice + cgst_value + sgst_value + igst_value;
                $(`#grnItemInvoiceCGSTTdSpan_${rowNo}`).html(`${curr_name}: ${inputValue(cgst_value)}` + '<p class="text-small spanCgstPriceINR" id="spanCgstPriceINR_' + rowNo + '"></p>');
                $(`#grnItemInvoiceSGSTTdSpan_${rowNo}`).html(`${curr_name}: ${inputValue(sgst_value)}` + '<p class="text-small spanSgstPriceINR" id="spanSgstPriceINR_' + rowNo + '"></p>');
                $(`#grnItemInvoiceIGSTTdSpan_${rowNo}`).html(`${curr_name}: ${inputValue(igst_value)}` + '<p class="text-small spanIgstPriceINR" id="spanIgstPriceINR_' + rowNo + '"></p>');

                if (curr_name != company_currency) {
                    $(`#spanCgstPriceINR_${rowNo}`).html(`${company_currency}: ${(cgst_value / currency_rate_value).toFixed(2)}`);
                    $(`#spanSgstPriceINR_${rowNo}`).html(`${company_currency}: ${(sgst_value / currency_rate_value).toFixed(2)}`);
                    $(`#spanIgstPriceINR_${rowNo}`).html(`${company_currency}: ${(igst_value / currency_rate_value).toFixed(2)}`);
                }
                if (igst > 0) {
                    taxArray.push({
                        gstType: "IGST",
                        taxPercentage: "100",
                        taxAmount: igst_value.toFixed(2)
                    });
                } else if (cgst > 0 && sgst > 0) {
                    taxArray.push({
                        gstType: "CGST",
                        taxPercentage: "50",
                        taxAmount: cgst_value.toFixed(2)
                    });
                    taxArray.push({
                        gstType: "SGST",
                        taxPercentage: "50",
                        taxAmount: sgst_value.toFixed(2)
                    });
                }
                document.getElementById(`hiddenTaxValues_${rowNo}`).value = JSON.stringify(taxArray);

                <?php

            } else {


                if (isset($data['tax']) && is_array($data['tax'])) {
                    foreach ($data['tax'] as $t) {
                ?>
                        var currency_rate_value1 = $(`#currency_conversion_rate`).val();
                        var itemStaticPrice2 = (parseFloat($(`#ItemInvoiceTotalPriceStatic_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceTotalPriceStatic_${rowNo}`).val()) : 0;


                        var curr_name1 = $("#selectCurrency").find(':selected').data("currname");
                        var company_currency1 = <?= json_encode($comp_currency)  ?>;

                        var cgst2 = (parseFloat($(`#ItemInvoice<?= $t['taxComponentName'] ?>New_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoice<?= $t['taxComponentName'] ?>New_${rowNo}`).val()) : 0;
                        var cgstPercent2 = (cgst2 / itemStaticPrice2) * 100;
                        var cgst_value2 = basicPrice * (cgstPercent2 / 100);
                        $(`#grnItemInvoice<?= $t['taxComponentName'] ?>TdSpan_${rowNo}`).html(`${curr_name1}: ${(cgst_value2).toFixed(2)}` + '<p class="text-small span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR" id="span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR_' + rowNo + '"></p>');

                        if (curr_name1 != company_currency1) {

                            $(`#span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR_${rowNo}`).html(`${curr_name1}: ${(cgst_value2 / currency_rate_value1).toFixed(2)}`);
                        }
                        $(`#ItemInvoice<?= $t['taxComponentName'] ?>_${rowNo}`).val((cgst_value2 / currency_rate_value1).toFixed(2));
                        totalItemPrice = basicPrice + cgst_value2;
                        taxArray.push({
                            gstType: "<?= $t['taxComponentName'] ?>",
                            taxPercentage: "<?= $t['taxPercentage'] ?>",
                            taxAmount: cgst_value2.toFixed(2)
                        });
                    <?php
                    }
                    ?>
                    document.getElementById(`hiddenTaxValues_${rowNo}`).value = JSON.stringify(taxArray);
            <?php
                }
            } ?>
            let tds_value = basicPrice * (tds / 100);

            totalItemPrice = totalItemPrice - tds_value;


            let tax_value = basicPrice + (basicPrice * tax / 100);

            // console.log(itemUnitPrice, itemQty, basicPrice, totalItemPrice, cgst, sgst, igst);

            var curr_name = $("#selectCurrency").find(':selected').data("currname");
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            $(`#grnItemInvoiceTotalPriceTdSpan_${rowNo}`).html(inputValue(totalItemPrice));
            $(`#grnItemInvoiceBaseAmtTdSpan_${rowNo}`).html(`${curr_name}: ${inputValuegrn(basicPrice)}` + '<p class="text-small spanBasePriceINR" id="spanBasePriceINR_' + rowNo + '"></p>');
            $(`#ItemInvoiceTotalPrice_${rowNo}`).val(inputValue(basicPrice / currency_rate_value));
            $(`#ItemInvoiceGrandTotalPrice_${rowNo}`).val(inputValue(totalItemPrice / currency_rate_value));
            $(`#grnItemInternalTaxValue_${rowNo}`).val(inputValue(tax_value / currency_rate_value));
            $(`#ItemInvoiceTDSValue_${rowNo}`).val(inputValue(tds_value / currency_rate_value));




            $(`#spanInvoiceCurrencyName_${rowNo}`).html(`${curr_name}`);
            $(`#grnItemUnitPriceInrhidden_${rowNo}`).val(`${(itemUnitPrice / currency_rate_value).toFixed(2)}`);


            if (curr_name != company_currency) {
                $(`#spanUnitPriceINR_${rowNo}`).html(`${company_currency}: ${(itemUnitPrice / currency_rate_value).toFixed(2)}`);
                $(`#spanBasePriceINR_${rowNo}`).html(`${company_currency}: ${(basicPrice / currency_rate_value).toFixed(2)}`);

            }


            $(`#grnPoInputQty_${rowNo}`).val(poQty - itemQty);
            $(`#grnPoInputRemainQty_${rowNo}`).val(poQty - itemQty);

            calculateGrandTotalAmount();
        }


        function calculateGrandTotalAmount() {

            let totalAmount = 0;
            let grandSubTotalAmt = 0;
            let totalTds = 0;
            var totalgst = 0;
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            var curr_name = $("#selectCurrency").find(':selected').data("currname");

            // $(".ItemInvoiceGrandTotalPrice").each(function() {
            //     totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // });
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            // console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
            // let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;
            // let ToTalcgst = (parseFloat($(`#totalCGST`).val()) > 0) ? parseFloat($(`#totalCGST`).val()) : 0;
            // let ToTalsgst = (parseFloat($(`#totalSGST`).val()) > 0) ? parseFloat($(`#totalSGST`).val()) : 0;
            // let ToTaligst = (parseFloat($(`#totalIGST`).val()) > 0) ? parseFloat($(`#totalIGST`).val()) : 0;

            let ToTalcgst = 0;
            let ToTalsgst = 0;
            let ToTaligst = 0;
            var taxArray2 = [];
            $(".ItemInvoiceTDSValue").each(function() {
                totalTds += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            <?php if ($companyCountry == 103) { ?>
                $(".ItemInvoiceCGSTClass").each(function() {
                    ToTalcgst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                });
                $(".ItemInvoiceSGSTClass").each(function() {
                    ToTalsgst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                });
                $(".ItemInvoiceIGSTClass").each(function() {
                    ToTaligst += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
                });

                $("#grandCgstTd").html(`${curr_name}: ${inputValue(ToTalcgst * currency_rate_value)}` + '<p class="text-small spanCgstGrandINR" id="spanCgstGrandINR"></p>');
                $("#totalCGST").val((ToTalcgst).toFixed(2));
                $("#grandSgstTd").html(`${curr_name}: ${inputValue(ToTalsgst * currency_rate_value)}` + '<p class="text-small spanSgstGrandINR" id="spanSgstGrandINR"></p>');
                $("#totalSGST").val((ToTalsgst).toFixed(2));
                $("#grandIgstTd").html(`${curr_name}: ${inputValue(ToTaligst * currency_rate_value)}` + '<p class="text-small spanIgstGrandINR" id="spanIgstGrandINR"></p>');
                $("#totalIGST").val(inputValue(ToTaligst));
                totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - totalTds;


                taxArray2.push({
                    gstType: "CGST",
                    taxPercentage: "50",
                    taxAmount: inputValue(ToTalcgst)
                });
                taxArray2.push({
                    gstType: "SGST",
                    taxPercentage: "50",
                    taxAmount: inputValue(ToTalsgst)
                });

                taxArray2.push({
                    gstType: "IGST",
                    taxPercentage: "100",
                    taxAmount: inputValue(ToTaligst)
                });

                <?php

            } else {

                if (isset($data['tax']) && is_array($data['tax'])) {
                    foreach ($data['tax'] as $t) {
                ?>
                        var currency_rate_value1 = $(`#currency_conversion_rate`).val();

                        var curr_name1 = $("#selectCurrency").find(':selected').data("currname");

                        ToTalg = 0;
                        $(".ItemInvoice<?= $t['taxComponentName'] ?>Class").each(function() {
                            ToTalg += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;

                        });
                        totalgst += ToTalg;
                        $("#grand<?= ucfirst(strtolower($t['taxComponentName'])) ?>Td").html(`${curr_name1}: ${(ToTalg * currency_rate_value1).toFixed(2)}` + '<p class="text-small span<?= ucfirst(strtolower($t['taxComponentName'])) ?>GrandINR" id="span<?= ucfirst(strtolower($t['taxComponentName'])) ?>GrandINR"></p>');

                        $("#total<?= $t['taxComponentName'] ?>").val((ToTalg).toFixed(2));
                        totalAmount = grandSubTotalAmt + totalgst - totalTds;
                        taxArray2.push({
                            gstType: "<?= $t['taxComponentName'] ?>",
                            taxPercentage: "<?= $t['taxPercentage'] ?>",
                            taxAmount: ToTalg.toFixed(2)
                        });

            <?php
                    }
                }
            } ?>
            document.getElementById(`hiddenGrandTaxValues`).value = JSON.stringify(taxArray2);

            // totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - totalTds;
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            var curr_name = $("#selectCurrency").find(':selected').data("currname");

            $("#grandSubTotalTd").html(`${curr_name}: ${inputValue(grandSubTotalAmt * currency_rate_value)}` + '<p class="text-small spanSubTotalINR" id="spanSubTotalINR"></p>');
            $("#grandSubTotal").val(inputValue(grandSubTotalAmt));
            $("#grandTotalTd").html(`${curr_name}: ${inputValue(totalAmount * currency_rate_value)}` + '<p class="text-small spangrandTotalINR" id="spangrandTotalINR"></p>');
            $("#grandTotal").val(inputValue(totalAmount));
            $("#grandTds").html(`${curr_name}: -${inputValue(totalTds * currency_rate_value)}` + '<p class="text-small spangrandTDSINR" id="spangrandTDSINR"></p>');
            $("#totalTDS").val(inputValue(totalTds));




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

            var lineVendorId = $(`#itemVendorId_${rowNo}`).val();

            var allVendorIdArray = [];

            $(".lineVendorId").each(function() {
                allVendorIdArray.push($(this).val());
            });

            var count = $.grep(allVendorIdArray, function(elem) {
                return elem == lineVendorId;
            }).length;

            if (count <= 1) {
                vendor_id_array.pop(lineVendorId);

                $('.vendorClass_' + lineVendorId).remove();
            }


            var linePoNumber = $(`#internalItemPo_${rowNo}`).val();
            var linePoNumberArray = [];

            $(".linePoNumber").each(function() {
                linePoNumberArray.push($(this).val());
            });


            var poCount = $.grep(linePoNumberArray, function(poElem) {
                return poElem == linePoNumber;
            }).length;

            if (poCount <= 1) {
                newInputArray.pop(linePoNumber);
                $("." + linePoNumber).remove();
                // alert(poCount);
            }

            console.log(newInputArray, newInputArray.length, linePoNumber);

            var mainlineId = rowNo;
            let mainItemId = $(`#internalItemId_${mainlineId}`).val();
            var arrayOfDistributedElement = $(`#allocated_array_${mainlineId}`).val();

            var allocatedArrayFromHtml;

            if (arrayOfDistributedElement === null || arrayOfDistributedElement === undefined || arrayOfDistributedElement === '') {
                allocatedArrayFromHtml = []; // Initialize an empty array to store selected items
            } else {
                allocatedArrayFromHtml = typeof(arrayOfDistributedElement) !== 'string' ? arrayOfDistributedElement : JSON.parse(arrayOfDistributedElement);
            }

            console.log("BeforeallocatedArrayFromHtml:", allocatedArrayFromHtml);

            var isPresent = false;
            $.each(allocatedArrayFromHtml, function(index, item) {
                if (item.formItemId == mainItemId) {
                    isPresent = true;
                    return false;
                }
            });

            if (isPresent) {
                for (let i = allocatedArrayFromHtml.length - 1; i >= 0; i--) {
                    if (allocatedArrayFromHtml[i].formItemId == mainItemId) {

                        var to_item_line_id = allocatedArrayFromHtml[i].itemLineId;
                        var allocated_cost = allocatedArrayFromHtml[i].allocatedCost;

                        var pre_allocated_cost = $(`#grnItemAllocatedCost_${to_item_line_id}`).html();

                        console.log("pre_allocated_cost", pre_allocated_cost);
                        var currency_rate_value = $(`#currency_conversion_rate`).val();

                        var actual_cost = pre_allocated_cost - (allocated_cost * currency_rate_value);

                        $(`#grnItemAllocatedCost_${to_item_line_id}`).html(actual_cost.toFixed(2));

                        $(`#grnItemAllocatedCosthidden_${to_item_line_id}`).val(actual_cost / currency_rate_value);

                        allocatedArrayFromHtml.splice(i, 1);
                    }
                }

                $(`#allocated_array_${mainlineId}`).val(JSON.stringify(allocatedArrayFromHtml));
                $(`#temporary_allocated_array_${mainlineId}`).val('');
            }
            console.log("AfterallocatedArrayFromHtml:", allocatedArrayFromHtml);


            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;
            let cgst = (parseFloat($(`#ItemInvoiceCGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceCGST_${rowNo}`).val()) : 0;
            let sgst = (parseFloat($(`#ItemInvoiceSGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceSGST_${rowNo}`).val()) : 0;
            let igst = (parseFloat($(`#ItemInvoiceIGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceIGST_${rowNo}`).val()) : 0;
            let tax = (parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemInternalTax_${rowNo}`).val()) : 0;
            let tds = (parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemTdsTdInput_${rowNo}`).val()) : 0;

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

            var curr_name = $("#selectCurrency").find(':selected').data("currname");
            var currency_rate_value = $(`#currency_conversion_rate`).val();

            $("#grandSubTotalTd").html(`${curr_name}: ${inputValue(grandSubTotalAmt * currency_rate_value)}` + '<p class="text-small spanSubTotalINR" id="spanSubTotalINR"></p>');
            $("#grandSubTotal").val(inputValue(grandSubTotalAmt));

            $("#grandTotalTd").html(`${curr_name}: ${inputValue(totalAmount * currency_rate_value)}` + '<p class="text-small spangrandTotalINR" id="spangrandTotalINR"></p>');
            $("#grandTotal").val(inputValue(totalAmount));

            $("#grandCgstTd").html(`${curr_name}: ${inputValue(cgstDeduct * currency_rate_value)}` + '<p class="text-small spanCgstGrandINR" id="spanCgstGrandINR"></p>');
            $("#grandSgstTd").html(`${curr_name}: ${inputValue(sgstDeduct * currency_rate_value)}` + '<p class="text-small spanSgstGrandINR" id="spanSgstGrandINR"></p>');
            $("#grandIgstTd").html(`${curr_name}: ${inputValue(igstDeduct * currency_rate_value)}` + '<p class="text-small spanIgstGrandINR" id="spanIgstGrandINR"></p>');

            $("#totalCGST").val(inputValue(cgstDeduct));
            $("#totalSGST").val(inputValue(sgstDeduct));
            $("#totalIGST").val(inputValue(igstDeduct));

            $("#grandTds").html("-" + `${curr_name}: ${(tdsDeduct * currency_rate_value).toFixed(2)}` + '<p class="text-small spangrandTDSINR" id="spangrandTDSINR"></p>');
            $("#totalTDS").val((tdsDeduct).toFixed(2));

            var currency_rate_value = $(`#currency_conversion_rate`).val();

            if (company_currency != curr_name) {
                $(`#spanSubTotalINR`).html(`${company_currency}: ${inputValue(grandSubTotalAmt)}`);
                $(`#spangrandTotalINR`).html(`${company_currency}: ${inputValue(totalAmount)}`);
                $(`#spangrandTDSINR`).html(`${company_currency}: ${inputValue(tdsDeduct)}`);
                $(`#spanCgstGrandINR`).html(`${company_currency}: ${inputQuantity(cgstDeduct)}`);
                $(`#spanSgstGrandINR`).html(`${company_currency}: ${inputValue(sgstDeduct)}`);
                $(`#spanIgstGrandINR`).html(`${company_currency}: ${inputValue(igstDeduct)}`);
            }
            calculateGrandTotalAmount();

        });


        $("#modalItemCodeDropDown").select2({
            dropdownParent: $("#mapInvoiceItemCode")
        });

        //$("#modalItemCodeDropDown").select2();

        let vendorCode = `<?= $vendorCode ?>`;
        let vendorId = `<?= $vendorId ?>`;

        function loadPoList() {
            $.ajax({
                url: "ajaxs/grn/ajax-fetch-multiple-po.php?vendor_id=" + vendorId + "&currency=" + curr_id + "&except_po=" + id + "&exceptparentpo=" + po_id,
                type: "GET",
                beforeSend: function() {
                    $("#open_po_list_table").html('Loading.....');
                },
                success: function(responseData) {
                    po_loaded = 1;
                    var responseObj = JSON.parse(responseData);
                    console.log(responseData);
                    $("#open_po_list_table").html(responseObj);
                }
            });
        }



        $("#po_list_button").click(function() {
            if (po_loaded == 0) {
                loadPoList();
            }

        });

        function loadPostedGrnList() {
            $.ajax({
                url: "ajaxs/grn/ajax-posted-grn.php",
                type: "GET",
                beforeSend: function() {
                    $("#posted_grn_list").html("Loading....");
                },
                success: function(responseData) {
                    posted_loader = 1;
                    var responseObj = JSON.parse(responseData);
                    // console.log(responseData);
                    $("#posted_grn_list").html(responseObj);
                }
            });
        }

        $("#posted_grn_list_button").click(function() {
            if (posted_loader == 0) {
                loadPostedGrnList();
            }
        })


        function loadChildPoList() {
            $.ajax({
                url: "ajaxs/grn/ajax-fetch-child-po.php?vendor_id=" + vendorId + "&currency=" + curr_id + "&po=" + po_id,
                type: "GET",
                beforeSend: function() {
                    $("#child_po_list_table").html('Loading.....');
                },
                success: function(responseData) {

                    var responseObj = JSON.parse(responseData);
                    // console.log(responseData);
                    $("#child_po_list_table").html(responseObj);
                }
            });
        }

        loadChildPoList();

        // $("#posted_grn_list_button").click(function() {
        //     // alert("Hello");


        // });



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

        $("#refresh_po_match").click(function() {
            alert("Hello");
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


        <?php
        if ($vendorGstin != "") {
        ?>
            $.ajax({
                url: '<?= BASE_URL ?>/branch/location/ajaxs/ajax-gst-details.php?gstin=<?= $vendorGstin ?>',
                type: 'GET',
                beforeSend: function() {

                    // <div id="vendorGstinStatusDiv"><p class="status">Active</p></div>

                    $(`#vendorGstinStatus_${vendor_id}`).html(`Loding...`);
                    // $(`#vendorGstinStatus_${vendorCode}`).html(`Loding...`);
                },
                success: function(responseData) {
                    responseObj = JSON.parse(responseData);
                    let gstinStatus = responseObj["data"]["sts"] ?? "Inactive";
                    $(`#vendorGstinStatus_${vendor_id}`).html(`${gstinStatus}`);
                    // $(`#vendorGstinStatus_${vendorCode}`).html(`${gstinStatus}`);


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

                        $('#addNewGrnFormSubmitBtn').prop('disabled', false);
                        var responseObj = JSON.parse(responseData);
                        console.log("Response of change currency func:", responseObj);

                        for (elem of $(".itemUnitPricehidden")) {
                            let rowNo = ($(elem).attr("id")).split("_")[1];
                            let newVal = ($(elem).val()) * conversion_rate;
                            $(`#grnItemUnitPriceTdInput_${rowNo}`).val(newVal);
                            $(`#currency_conversion_rate`).val(responseObj);
                            calculateOneItemAmounts(rowNo);

                        };
                    }
                });
            } else {

                for (elem of $(".itemUnitPricehidden")) {
                    let rowNo = ($(elem).attr("id")).split("_")[1];
                    let newVal = ($(elem).val()) * conversion_rate;
                    $(`#grnItemUnitPriceTdInput_${rowNo}`).val(newVal);
                    // $(`#currency_conversion_rate`).val(responseObj);
                    calculateOneItemAmounts(rowNo);

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



        newInputArray.push(id);

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
                    li.classList.add(item);
                    ul.appendChild(li);
                }
            });
        }

        function addItem() {
            var inputValue = document.getElementById("customInvoicePoNumber").value;
            if (!inputValue.trim()) {
                return;
            }

            // multiplePo(inputValue, "add");
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
                multiplePo(this.parentNode.textContent.trim(), "sub", serial_number, "material");
                removeFromArray(this.parentNode.textContent.trim());
                showCombinedArray();
                //Call Ajax to get vendor ID
                deleteVendor(this.parentNode.textContent.trim());

            };
            return deleteButton;
        }

        function removeFromArray(item) {
            var index = newInputArray.indexOf(item);
            if (index !== -1) {
                newInputArray.splice(index, 1);
            }
        }


        function deleteVendor(po) {
            let vendorSet = new Set();
            $(".lineVendorId").each(function() {
                vendorSet.add($(this).val());
            });

            $.ajax({
                url: "ajaxs/grn/ajax-fetch-vendor-from-po.php?po=" + po,
                type: "GET",
                beforeSend: function() {},
                success: function(response) {
                    let vendorID = JSON.parse(response); // assuming response is a single vendor ID (e.g., "123")

                    // If vendorID is not in the list of values (v), remove elements with class vendorClass_vendorID
                    if (!vendorSet.has(vendorID)) {
                        countVendor--;

                        $('.vendorClass_' + vendorID).remove();
                        const index = vendor_id_array.indexOf(vendorID);
                        if (index !== -1) {
                            vendor_id_array.splice(index, 1);
                        }
                    }
                },
                error: function(e) {
                    console.log("error: " + e.message);
                }
            });
        }

        showCombinedArray();



        $(document).on('keydown', 'input', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent default behavior
            }
        });

        function handleCheckboxChange(checkbox) {
            if (checkbox.checked) {
                var poNumber = checkbox.value;

                var listType = checkbox.dataset.type;
                var originType = checkbox.dataset.origin;
                if (originType == "grn") {
                    $.ajax({
                        url: "ajaxs/grn/ajax-check-item-movement.php?grn=" + poNumber,
                        type: "GET",
                        beforeSend: function() {
                            console.log("Checking Posted GRN.....");
                            $("#loaderGRN").show();
                        },
                        success: function(responseData) {
                            $("#loaderGRN").hide();
                            console.log((responseData));
                            if (responseData == "true") {
                                ss = true;
                                Swal.fire({
                                    icon: "warning",
                                    title: "Cannot Inventorize this GRN Item",
                                    text: "This GRN item has already been used in another activity.",
                                    showConfirmButton: true,
                                    confirmButtonText: "OK"
                                });
                                checkbox.checked = false;
                            } else {
                                addItemToList(poNumber);
                                multipleGrnList(poNumber, "add", serial_number, "grn");
                            }
                        }
                    });

                } else {
                    $.ajax({
                        url: "ajaxs/grn/ajax-grn-vendor-details.php?po=" + poNumber,
                        type: "GET",
                        beforeSend: function() {
                            // $("#loaderGRN").show();
                        },
                        success: function(responseData) {
                            var vendorIdajax = $(responseData).find("#invoiceVendorIdInput").val();
                            let isExistingVendor = vendor_id_array.includes(vendorIdajax);

                            // Check if vendor exists or if new vendor can be added
                            if (!isExistingVendor && countVendor >= 5) {
                                Swal.fire({
                                    icon: "warning",
                                    title: "Vendor Limit Reached",
                                    text: "Only 5 different vendors are allowed per GRN",
                                    showConfirmButton: true,
                                    confirmButtonText: "OK"
                                });
                                checkbox.checked = false;
                            } else {
                                countVendor++;
                                addItemToList(poNumber);
                                multiplePo(poNumber, "add", serial_number, listType);
                            }
                        }
                    });

                }

            } else {
                var poNumber = checkbox.value;
                var listType = checkbox.dataset.type;
                var originType = checkbox.dataset.origin;
                if (originType == "grn") {
                    multipleGrnList(poNumber, "sub", serial_number, "grn");
                } else {
                    multiplePo(poNumber, "sub", serial_number, listType);
                }
                // $(`.${poNumber}`).remove();

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
                li.classList.add(item);
                ul.appendChild(li);

                newInputArray.push(item);
                showCombinedArray();
            }
        }

        let DISTRIBUTED_MASTER_DATA = {};

        $(document).on("change", ".costCenterSelect", function() {
            let selectedOption = $(this).val();
            let mainlineId = $(this).attr("id").split("_")[1];
            let mainItemId = $(`#internalItemId_${mainlineId}`).val();
            let received_qty = $(`#grnItemReceivedQtyTdInput_${mainlineId}`).val();
            let unitPrice = $(`#grnItemUnitPriceTdInput_${mainlineId}`).val();
            let allocatedArrayValue = $(`#allocated_array_${mainlineId}`).val();
            let basicAMt = received_qty * unitPrice;

            $(`#distribution_cost_${mainlineId}`).html("Distribution Cost : " + basicAMt);
            $(`#distributeButton_${mainlineId}`).attr('data-value', basicAMt);
            $(`#inventButton_${mainlineId}`).attr('data-itemId', mainItemId);
            $(`#inventButton_${mainlineId}`).attr('data-allocatedArray', allocatedArrayValue);
            $(`#inventButton_${mainlineId}`).attr('data-sl', mainlineId);


            let modalCostCenter = $(`#costCenterModal_${mainlineId}`);
            let editButton = $("#modalValueEdit");

            $(".btn-close").click(function() {
                modalCostCenter.hide();
            });

            $(window).click(function(event) {
                if (event.target == modalCostCenter[0]) {
                    modalCostCenter.show();
                }
            });

            if (selectedOption == "inventorise_" + mainlineId) {
                modalCostCenter.addClass("show");
                modalCostCenter.css("display", "block");
                modalCostCenter.css("backdrop-filter", "brightness(0.8) blur(3px)");

                let tablebodydata = '';
                $('.goodsclass').each(function() {
                    let lineId = $(this).attr("id").split("_")[1];
                    // console.log(lineId);
                    let internalItemCode = $(this).find(`#internalItemCode_${lineId}`).val();
                    let internalItemName = $(this).find(`#internalItemName_${lineId}`).val();
                    let internalVendorCode = $(this).find(`#itemVendorCode_${lineId}`).val();
                    let internalVendorName = $(this).find(`#itemVendorName_${lineId}`).val();
                    let internalUnitPrice = $(this).find(`#grnItemUnitPriceTdInput_${lineId}`).val();
                    let internalQuantity = $(this).find(`#grnItemReceivedQtyTdInput_${lineId}`).val();
                    let internalBasic = internalUnitPrice * internalQuantity;
                    let allocated_cost_modal = $(`#grnItemAllocatedCost_${lineId}`).html();

                    console.log(allocated_cost_modal);

                    tablebodydata += `<tr>
                        <td><input type="checkbox" id="check_box_${lineId}" name="check_box" class="checkbx_${mainlineId}" value="${mainlineId}_${lineId}"></td>
                        <td>${internalItemCode}</td>
                        <td>${internalItemName}</td>
                        <td>${internalVendorCode}</td>
                        <td>${internalVendorName}</td>
                        <td>${internalUnitPrice}</td>
                        <td id="qtyAllocate_${mainlineId}_${lineId}">${internalQuantity}</td>
                        <td id="basicAmtAllocate_${mainlineId}_${lineId}">${internalBasic}</td>
                        <td><input id="invtAmt_${mainlineId}_${lineId}" class="form-control invtAmtClass" type="text" value="0" readonly></td>
                        <td id="allocatedCost_${mainlineId}_${lineId}">${allocated_cost_modal}</td>
                    </tr>`;
                    // console.log(tablebodydata);
                });

                // Append the cloned row to the target table
                $(`#costCenterId_${mainlineId}`).html('');
                $(`#costCenterId_${mainlineId}`).html(tablebodydata);

                // Show the edit button
                editButton.show();
            } else {
                modalCostCenter.removeClass("show");
                modalCostCenter.css("display", "none");

                // Hide the edit button
                editButton.hide();

                var arrayOfDistributedElement = $(`#allocated_array_${mainlineId}`).val();
                var allocatedArrayFromHtml;

                if (arrayOfDistributedElement === null || arrayOfDistributedElement === undefined || arrayOfDistributedElement === '') {
                    allocatedArrayFromHtml = []; // Initialize an empty array to store selected items
                } else {
                    allocatedArrayFromHtml = typeof(arrayOfDistributedElement) !== 'string' ? arrayOfDistributedElement : JSON.parse(arrayOfDistributedElement);
                }

                console.log("BeforeallocatedArrayFromHtml:", allocatedArrayFromHtml);

                var isPresent = false;
                $.each(allocatedArrayFromHtml, function(index, item) {
                    if (item.formItemId == mainItemId) {
                        isPresent = true;
                        return false;
                    }
                });

                if (isPresent) {
                    for (let i = allocatedArrayFromHtml.length - 1; i >= 0; i--) {
                        if (allocatedArrayFromHtml[i].formItemId == mainItemId) {

                            var to_item_line_id = allocatedArrayFromHtml[i].itemLineId;
                            var allocated_cost = allocatedArrayFromHtml[i].allocatedCost;

                            var pre_allocated_cost = $(`#grnItemAllocatedCost_${to_item_line_id}`).html();

                            console.log("pre_allocated_cost", pre_allocated_cost);

                            var currency_rate_value = $(`#currency_conversion_rate`).val();

                            var actual_cost = pre_allocated_cost - (allocated_cost * currency_rate_value);

                            $(`#grnItemAllocatedCost_${to_item_line_id}`).html(actual_cost.toFixed(2));

                            $(`#grnItemAllocatedCosthidden_${to_item_line_id}`).val(actual_cost / currency_rate_value);

                            allocatedArrayFromHtml.splice(i, 1);
                        }
                    }

                    $(`#allocated_array_${mainlineId}`).val(JSON.stringify(allocatedArrayFromHtml));
                    $(`#temporary_allocated_array_${mainlineId}`).val('');
                }
                console.log("AfterallocatedArrayFromHtml:", allocatedArrayFromHtml);

            }
        });

        // Handle click event of the edit button to open the modal
        $("#modalValueEdit").click(function() {
            var modalCostCenter = $("#costCenterModal");
            modalCostCenter.addClass("show");
            modalCostCenter.css("display", "block");
            modalCostCenter.css("backdrop-filter", "brightness(0.8) blur(3px)");
        });


        function matchFormAndToItemIds(array1, array2) {
            // Iterate through array1
            for (let obj1 of array1) {
                // Check if there's a corresponding object in array2
                let match = array2.find(obj2 => obj1.formItemId === obj2.formItemId && obj1.toItemId === obj2.toItemId);
                // If a match is found, return true
                if (match) {
                    return true;
                }
            }
            // If no match is found, return false
            return false;
        }

        $(document).on("click", ".distributeButtonClass", function() {
            var buttonId = $(this).attr("id").split("_")[1];
            var fromItemIdValue = $(`#inventButton_${buttonId}`).attr('data-itemId');
            var allocatedArrayFromHtml = $(`#inventButton_${buttonId}`).attr('data-allocatedArray');
            var temporaryAllocatedArrayFromHtml = $(`#temporary_allocated_array_${buttonId}`).val();
            var sl = $(`#inventButton_${buttonId}`).attr('data-sl');

            if (temporaryAllocatedArrayFromHtml === null || temporaryAllocatedArrayFromHtml === undefined || temporaryAllocatedArrayFromHtml === '') {
                var temporaryAllocatedArrayFromHtml = []; // Initialize an empty array to store selected items
            }

            if (allocatedArrayFromHtml === null || allocatedArrayFromHtml === undefined || allocatedArrayFromHtml === '') {
                var allocatedArrayFromHtml = []; // Initialize an empty array to store selected items
            }

            // console.log(temporaryAllocatedArrayFromHtml);

            var distibuteSelectedItems = []; // Initialize an empty array to store selected items

            $("input:checkbox[class=checkbx_" + buttonId + "]:checked").each(function() {
                var id = $(this).val().split("_")[1]; // Get the ID of the checked item
                var internalItemCode = $(`#internalItemCode_${id}`).val(); // Get other relevant data of the checked item
                var internalItemName = $(`#internalItemName_${id}`).val();
                var internalVendorCode = $(`#itemVendorCode_${id}`).val();
                var internalVendorName = $(`#itemVendorName_${id}`).val();
                var internalUnitPrice = $(`#grnItemUnitPriceTdInput_${id}`).val();
                var internalQuantity = $(`#grnItemReceivedQtyTdInput_${id}`).val();
                var InventoriseAmount = $(`#invtAmt_${buttonId}_${id}`).val();
                var toItemIdValue = $(`#internalItemId_${id}`).val();
                var toVendorIdValue = $(`#itemVendorId_${id}`).val();
                var currency_rate_value = $(`#currency_conversion_rate`).val();
                var allocated_cost_inr = InventoriseAmount / currency_rate_value;

                // Push the relevant data of the checked item to the selectedItems array
                distibuteSelectedItems.push({
                    itemLineId: id,
                    formItemId: fromItemIdValue,
                    toItemId: toItemIdValue,
                    toVendorId: toVendorIdValue,
                    allocatedCost: allocated_cost_inr
                });
            });

            try {
                var parsedArray = typeof(allocatedArrayFromHtml) != 'string' ? allocatedArrayFromHtml : JSON.parse(allocatedArrayFromHtml);

                var parsedTemporaryArray = typeof(temporaryAllocatedArrayFromHtml) != 'string' ? temporaryAllocatedArrayFromHtml : JSON.parse(temporaryAllocatedArrayFromHtml);

                var itemStatus = matchFormAndToItemIds(distibuteSelectedItems, parsedArray);
                var temporaryItemStatus = matchFormAndToItemIds(distibuteSelectedItems, parsedTemporaryArray);

            } catch (error) {
                console.log("Error parsing JSON:", error);
            }

            if (!itemStatus && !temporaryItemStatus) {
                let freightprice = (parseFloat($(this).attr('data-value')) > 0) ? parseFloat($(this).attr('data-value')) : 0;
                let total_base = 0;
                $("input:checkbox[class=checkbx_" + buttonId + "]:checked").each(function() {
                    var id = $(this).val().split("_")[1];
                    let basic = (parseFloat($(`#basicAmtAllocate_${buttonId}_${id}`).html()) > 0) ? parseFloat($(`#basicAmtAllocate_${buttonId}_${id}`).html()) : 0;
                    total_base += basic;
                });

                $("input:checkbox[class=checkbx_" + buttonId + "]:checked").each(function() {
                    var id = $(this).val().split("_")[1];
                    let basic_each = (parseFloat($(`#basicAmtAllocate_${buttonId}_${id}`).html()) > 0) ? parseFloat($(`#basicAmtAllocate_${buttonId}_${id}`).html()) : 0;
                    let qty = (parseFloat($(`#qtyAllocate_${buttonId}_${id}`).html()) > 0) ? parseFloat($(`#qtyAllocate_${buttonId}_${id}`).html()) : 0;
                    let x = (basic_each * freightprice) / total_base;
                    let base_after_freight = basic_each + x;
                    let item_rate = base_after_freight / qty;

                    let invAmt = (parseFloat($(`#invtAmt_${buttonId}_${id}`).val()) > 0) ? parseFloat($(`#invtAmt_${buttonId}_${id}`).val()) : 0;

                    // console.log(id);

                    $(`#invtAmt_${buttonId}_${id}`).val((invAmt + x).toFixed(2));
                    // $(`#invtAmt_${id}`).val(item_rate);

                    let prev_alloc_cost = (parseFloat($(`#allocatedCost_${buttonId}_${id}`).html()) > 0) ? parseFloat($(`#allocatedCost_${buttonId}_${id}`).html()) : 0;

                    parseFloat($(`#allocatedCost_${buttonId}_${id}`).html(prev_alloc_cost + x));

                });

                $(`#temporary_allocated_array_${buttonId}`).val(JSON.stringify(distibuteSelectedItems));

            } else {
                alert("Already allocated");
            }

        });



        $(document).on("click", ".inventButtonClass", function() {

            var buttonId = $(this).attr("id").split("_")[1];
            var fromItemIdValue = $(`#inventButton_${buttonId}`).attr('data-itemId');
            var allocatedArrayFromHtml = $(`#inventButton_${buttonId}`).attr('data-allocatedArray');

            if (allocatedArrayFromHtml === null || allocatedArrayFromHtml === undefined || allocatedArrayFromHtml === '') {
                var confirmSelectedItems = [];
            } else {
                var confirmSelectedItems = JSON.parse(allocatedArrayFromHtml);
            }

            var tempconfirmSelectedItems = [];

            $("input:checkbox[class=checkbx_" + buttonId + "]:checked").each(function() {
                var id = $(this).val().split("_")[1]; // Get the ID of the checked item
                var internalItemCode = $(`#internalItemCode_${id}`).val(); // Get other relevant data of the checked item
                var internalItemName = $(`#internalItemName_${id}`).val();
                var internalVendorCode = $(`#itemVendorCode_${id}`).val();
                var internalVendorName = $(`#itemVendorName_${id}`).val();
                var internalUnitPrice = $(`#grnItemUnitPriceTdInput_${id}`).val();
                var internalQuantity = $(`#grnItemReceivedQtyTdInput_${id}`).val();
                var InventoriseAmount = $(`#invtAmt_${buttonId}_${id}`).val();
                var toItemIdValue = $(`#internalItemId_${id}`).val();
                var toVendorIdValue = $(`#itemVendorId_${id}`).val();
                var currency_rate_value = $(`#currency_conversion_rate`).val();
                var allocated_cost_inr = InventoriseAmount / currency_rate_value;

                // Push the relevant data of the checked item to the selectedItems array
                confirmSelectedItems.push({
                    itemLineId: id,
                    formItemId: fromItemIdValue,
                    toItemId: toItemIdValue,
                    toVendorId: toVendorIdValue,
                    allocatedCost: allocated_cost_inr
                });

                tempconfirmSelectedItems.push({
                    itemLineId: id,
                    formItemId: fromItemIdValue,
                    toItemId: toItemIdValue,
                    toVendorId: toVendorIdValue,
                    allocatedCost: allocated_cost_inr
                });
            });

            if (allocatedArrayFromHtml === null || allocatedArrayFromHtml === undefined || allocatedArrayFromHtml === '') {
                var allocatedArrayFromHtml = []; // Initialize an empty array to store selected items
            }

            try {
                var parsedConfirmArray = typeof(allocatedArrayFromHtml) != 'string' ? allocatedArrayFromHtml : JSON.parse(allocatedArrayFromHtml);

                var itemConfirmStatus = matchFormAndToItemIds(tempconfirmSelectedItems, parsedConfirmArray);

            } catch (error) {
                console.log("Error parsing JSON:", error);
            }

            console.log(itemConfirmStatus);

            if (!itemConfirmStatus) {
                //Push to particular Item
                $(`#allocated_array_${buttonId}`).val(JSON.stringify(confirmSelectedItems));


                // You can further process this array as per your requirement

                // Existing functionality to update allocated costs
                $("input:checkbox[class=checkbx_" + buttonId + "]:checked").each(function() {
                    var id = $(this).val().split("_")[1];

                    let allocated_cost_modal = (parseFloat($(`#allocatedCost_${buttonId}_${id}`).html()) > 0) ? parseFloat($(`#allocatedCost_${buttonId}_${id}`).html()) : 0;

                    var curr_name = $("#selectCurrency").find(':selected').data("currname");
                    var currency_rate_value = $(`#currency_conversion_rate`).val();

                    $(`#grnItemAllocatedCost_${id}`).html(`${(allocated_cost_modal).toFixed(2)}`);
                    $(`#grnItemAllocatedCosthidden_${id}`).val(allocated_cost_modal / currency_rate_value);
                });



                let modalCostCenter = $(`#costCenterModal_${buttonId}`);
                modalCostCenter.removeClass("show");
                modalCostCenter.css("display", "none");
                modalCostCenter.hide();

                // console.log("call");

            } else {
                alert("Already allocated");
            }

            $(`.checkbx_${buttonId}`).prop('checked', false);

        });


        $(document).on("keyup", ".invtAmtClass", function() {

            let id = $(this).attr("id").split("_")[1];
            let value = $(`#invtAmt_${id}`).val();

            parseFloat($(`#allocatedCost_${id}`).html(value));

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

        $(document).on("keyup", ".multiQuantity", function() {
            let rowNo = $(this).data("itemid");

            console.warn("Recalculating the batch quantity!", rowNo);
            calculateAndCheckBatchQuantity(rowNo, $(this));
        });


        let defaultMultiBatchRows = JSON.parse(`<?= json_encode($defaultMultiBatchRows, true) ?>`);
        defaultMultiBatchRows.map(function(item) {
            var binlistHtml = $(`#grnItemAllBins_${item.sl}`).val();
            addGrnItemMultipleBatch(item.vendorId, item.sl, item.qty, true, binlistHtml);
        });

        $(document).on("click", ".addQtyBtn", function() {
            let id = $(this).attr("id").split("_")[1];
            let batchVendorId = $(this).attr("id").split("_")[2];
            console.log("Appending new row!!!!", id);

            var binlistHtml = $(`#grnItemAllBins_${id}`).val();

            addGrnItemMultipleBatch(batchVendorId, id, 0, false, binlistHtml);

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

    });

    function updateSerialNumber(newnumber) {
        serial_number = serial_number + newnumber;
        console.log(serial_number); // Prints 2, then 3, etc., on subsequent calls
    }
</script>

<script src="<?= BASE_URL; ?>public/validations/pendingGrnValidation.js"></script>