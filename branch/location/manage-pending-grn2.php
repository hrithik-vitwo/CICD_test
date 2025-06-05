<?php
require_once("../../app/v1/connection-branch-admin.php");
administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

require_once("../../app/v1/functions/branch/func-grn-controller.php");
include_once("../../app/v1/connection-branch-admin.php");
include("../../app/v1/functions/branch/func-bills-controller.php");
include("../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");

$BranchPoObj = new BranchPo();
?>
<style>
    .content-wrapper {
        height: auto !important;
    }

    .row.grn-create .card ul {
        padding-left: 0;
    }

    .quick-registration-vendor {
        overflow: auto;
    }

    div.grn-table {
        padding: 40px 0;
    }

    table.grn-table tr td {
        padding: 18px 13px !important;
    }

    table.grn-table tr td input,
    table.grn-table tr td select {
        height: 30px;
    }

    table.grn-table tr td select {
        width: 132px;
    }

    .derived-qty-info {
        display: inline-block;
    }

    .derived-qty-info p {

        white-space: pre-line;
        top: -53px !important;

    }

    .derived-qty-info::before {
        content: '!' !important;
    }

    span.error {
        position: relative;
        display: block;
        text-align: center;
        top: -28px;
        left: -97px;
        margin: 10px 0;
        color: orange !important;
    }

    span.calculate-error {
        left: 0;
    }

    table.grn-table tr.span-error-tr td {
        background-color: transparent !important;
        height: 0;
        padding: 0 !important;
    }

    .invoice-iframe .modal-dialog {
        max-width: 705px;
    }

    .modal-open {
        overflow: auto !important;
    }

    @media (max-width: 575px) {
        #grnInvoicePreviewIfram {
            display: block;
        }

        div.grn-table {
            padding: 50px 0;
        }

        span.error {
            left: 440px;
        }
    }
</style>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<div class="content-wrapper pl-3 pr-3 mb-5" style="height: auto !important;">
    <?php
    $grnObj = new GrnController();
    //console($_POST);

    if (isset($_POST["vendorCode"]) && $_POST["vendorCode"] != "") {
        $createGrnObj = $grnObj->createGrn($_POST);

        if ($createGrnObj["status"] == "success") {
            swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"], BASE_URL . "branch/location/manage-grn.php");
        } else {
            swalAlert($createGrnObj["status"], ucfirst($createGrnObj["status"]), $createGrnObj["message"]);
        }

        //console("Hello grn process");
        //console($createGrnObj);

    }

    if (isset($_GET["view"]) && $_GET["view"] != "") {

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

            $vendorGoodsCodeObj = queryGet("SELECT `itemId`,`itemCode`,`itemType` FROM `" . ERP_VENDOR_ITEM_MAP . "` WHERE `companyId`='" . $company_id . "' AND `vendorCode`='" . $vendorCode . "' AND `itemTitle`='" . strip_tags($vendorItemTitle) . "' ORDER BY `vendorItemMapId` DESC LIMIT 1");
            if ($vendorGoodsCodeObj["status"] == "success") {
                $itemCode = $vendorGoodsCodeObj["data"]["itemCode"];
                $itemType = $vendorGoodsCodeObj["data"]["itemType"];
                $item_id = $vendorGoodsCodeObj["data"]["itemId"];

                // console($item_id);

                // return $vendorItemTitle;


                $goodsHsnObj = queryGet("SELECT `itemId`, `itemName`, `hsnCode`,`baseUnitMeasure` FROM `" . ERP_INVENTORY_ITEMS . "` WHERE `company_id`='" . $company_id . "' AND `itemId`='" . $item_id . "'");
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
                            "uom" => $getUOM["data"]["uomName"],
                            "type" => $itemType
                        ];
                    } else {
                        return [
                            "itemCode" => $itemCode,
                            "itemHsn" => $goodsHsnObj["data"]["hsnCode"],
                            "itemId" => $goodsHsnObj["data"]["itemId"],
                            "itemName" => $goodsHsnObj["data"]["itemName"],
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
        $grnNo = "GRN" . time() . rand(100, 999);

        $InvoiceObj = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `grn_mul_id` = '" . $id . "'", false);
        $InvoiceData = $InvoiceObj["data"];


        if($InvoiceData["vendor_code"] == "" && $InvoiceData["gst_no"] != "")
        {
            $Vendorgst = $InvoiceData["gst_no"];
            $checkGstSql = queryGet("SELECT * FROM `erp_vendor_details` WHERE `company_id` = '" . $company_id . "' AND `company_branch_id`='".$branch_id."' AND `location_id`='".$location_id."' AND `vendor_gstin`='".$Vendorgst."'", false);

            if($checkGstSql["status"] == "success")
            {
                $checkGst = $checkGstSql["data"];
                $v_id = $checkGst["vendor_id"];
                $v_code = $checkGst["vendor_code"];
                $update = queryUpdate("UPDATE `erp_grn_multiple` SET `vendor_id`='" . $v_id . "', `vendor_code`='".$v_code."' WHERE `grn_mul_id`='" . $id."'");
            }
        }


        $processInvoiceObj = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `grn_mul_id` = '" . $id . "'", false);
        $invoiceDataGet = $processInvoiceObj["data"];

        $invoice_data_json = unserialize($invoiceDataGet["grn_read_json"]);
        // console($invoice_data_json);
        $invoiceData = $invoice_data_json["data"];
        $invoiceFile = $invoiceDataGet["uploaded_file_name"];

        $documentNo = $invoiceData["InvoiceId"] ?? "";
        $documentDate = $invoiceData["InvoiceDate"] ?? "";
        $dueDate = $invoiceData["DueDate"] ?? "";

        $invoiceTotal = $invoiceData["InvoiceTotal"] ?? 0;
        $invoiceSubTotal = $invoiceData["SubTotal"] ?? 0;
        $invoiceTaxTotal = $invoiceData["TotalTax"] ?? 0;

        $customerName = $invoiceData["CustomerName"] ?? "";
        $customerPurchaseOrder = $invoiceDataGet["po_no"] ?? "";

        $customerGstin = $invoiceData["CustomerTaxId"] ?? $invoiceDataGet["branch_gst_no"];
        $vendorGstin = $invoiceData["VendorTaxId"] ?? "";

        $customerGstinStateCode = substr($customerGstin, 0, 2);
        $vendorGstinStateCode = substr($vendorGstin, 0, 2);


        $vendorAddress = $invoiceData["VendorAddress"] ?? "";
        $vendorAddressRecipient = $invoiceData["VendorAddressRecipient"] ?? "";

        $vendorGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $vendorGstinStateCode)["data"]["gstStateName"] ?? "";
        $customerGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $customerGstinStateCode)["data"]["gstStateName"] ?? "";

        $vendorPan = substr($vendorGstin, 2, 10);

        $vendorCode = $invoiceDataGet["vendor_code"];
        $vendorId = $invoiceDataGet["vendor_id"];
        $vendorName = $invoiceDataGet["vendor_name"] ?? "";
        $vendorCreditPeriod = $invoiceDataGet["vendor_credit_period"];

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
                                    <input type="hidden" name="documentNo" value="<?= $documentNo ?>">
                                    <input type="hidden" name="documentDate" value="<?= $documentDate ?>">
                                    <input type="hidden" name="vendorDocumentFile" value="<?= $invoiceFile ?>">
                                    <input type="hidden" name="vendorGstinStateName" value="<?= $vendorGstinStateName . '(' . $vendorGstinStateCode . ')'; ?>">
                                    <input type="hidden" name="locationGstinStateName" value="<?= $customerGstinStateName . '(' . $customerGstinStateCode . ')' ?>">
                                    <!-- <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GRN No :&nbsp;</p>
                                        <p> <?= $grnNo ?></p>
                                    </div> -->
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document No :&nbsp;</p>
                                        <p><?= $documentNo ?></p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document Date :&nbsp;</p>
                                        <p><?= date("d-m-Y", strtotime($documentDate)) ?></p>
                                    </div>
                                    <div class="display-flex grn-form-input-text">
                                        <i class="fa fa-check"></i>
                                        &nbsp;
                                        <p class="label-bold">Posting Date :</p>
                                        &nbsp;
                                        <input type="date" name="invoicePostingDate" value="<?= date("Y-m-d"); ?>" class="form-control" required>
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
                                        <input type="text" name="invoicePoNumber" id = "invoicePoNumber" value="<?= $customerPurchaseOrder ?>" class="form-control">
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
                                    <input type="hidden" name="vendorName" value="<?= $vendorName ?>" class="form-control" />
                                    <input type="hidden" name="vendorGstin" value="<?= $vendorGstin ?>" class="form-control" />
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Code :&nbsp;</p>
                                        <p id="invoiceVendorCodeSpan"><?= $vendorCode ?></p>
                                    </div>
                                    <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Name :&nbsp;</p>
                                        <p><?= $vendorName ?></p>
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
                                if($vendorCode == "")
                                {
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

                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content tab-col" id="custom-tabs-three-tabContent">
                                <div class="tab-pane fade show active iframe-preview-btn" id="uploaded-invoice-preview-div" role="tabpanel" aria-labelledby="invoice-po-div-tab">
                                    <iframe src='../bills/<?= $invoiceFile ?>#view=fitH' id="grnInvoicePreviewIfram" width="100%" height="220"></iframe>
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
                                    $sql_list = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE company_id='" . $company_id . "' AND company_branch_id='".$branch_id."' AND location_id='".$location_id."' AND vendor_status='active' ORDER BY vendor_id DESC";
                                    $qry_list = queryGet($sql_list,true);
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

                                            <table class="table-sales-order table defaultDataTable grn-table">
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
                                                            <a class="btn btn-sm btn-xs btn-secondary ml-2 vendorListClass" data-id="<?= $eachvendor["vendor_id"] ?>" data-name = "<?= $eachvendor["trade_name"] ?>" data-code = "<?= $eachvendor["vendor_code"] ?>" data-toggle="modal" data-target="#vendor_confirmation">Map Vendor</i></a>
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
                                if ($customerPurchaseOrder != "") {

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
                                                            <th class="text-center" colspan = 2>Quantity</th>
                                                            <th class="text-center" colspan = 2>Price</th>
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
                                                            <td><?= $itemName ?></td>
                                                            <td><?= $internalItemCode ?></td>
                                                            <?php
                                                            $quantity = "";
                                                            foreach($poItemsList as $poItem)
                                                            {
                                                                if($poItem["itemName"] == $itemName)
                                                                {
                                                                    $quantity = $poItem["qty"];
                                                                    break;
                                                                }
                                                                else
                                                                {
                                                                    $quantity = "";
                                                                    continue;
                                                                }
                                                            }
                                                            ?>
                                                            <td><?= $quantity ?></td>
                                                            <td><?= $oneItemObj["Quantity"] ?></td>

                                                            <?php
                                                            $price = "";
                                                            foreach($poItemsList as $poItem)
                                                            {
                                                                if($poItem["itemName"] == $itemName)
                                                                {
                                                                    $price = $poItem["unitPrice"];
                                                                    break;
                                                                }
                                                                else
                                                                {
                                                                    $price = "";
                                                                    continue;
                                                                }
                                                            }
                                                            ?>
                                                            <td><?= $price ?></td>
                                                            <td><?= $oneItemObj["UnitPrice"] ?></td>
                                                            <?php
                                                                $match = "Mismatched";
                                                                foreach($poItemsList as $poItem)
                                                                {
                                                                    if($poItem["itemName"] == $itemName)
                                                                    {
                                                                        if($oneItemObj["UnitPrice"] == $poItem["unitPrice"] && $oneItemObj["Quantity"] == $poItem["qty"])
                                                                        {
                                                                            $match = "Matched";
                                                                            break;
                                                                        }
                                                                        else
                                                                        {
                                                                            // $match = "";
                                                                            continue;
                                                                        }
                                                                    }
                                                                    else
                                                                    {
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
                                <?php
                                }
                                $poDetailsObj = queryGet('SELECT * FROM `erp_branch_purchase_order` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `po_status`="9" AND `vendor_id`="'.$vendorId.'"', true);
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
                                                <tbody>
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
                <table class="table-sales-order table defaultDataTable grn-table">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Item Name</th>
                            <th>Internal Code</th>
                            <th>Item HSN</th>
                            <th>St. Loc.</th>
                            <th>Derived Qty
                                <div class="help-tip derived-qty-info">
                                    <p>This Quantity will be reflected in your stock</p>
                                </div>
                            </th>
                            <th>Invoiced Qty</th>
                            <th>Received Qty</th>
                            <th>Unit Price</th>
                            <th>Basic Amount</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>Total Amount</th>
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
                        foreach ($invoiceData["Items"] as $oneItemObj) {

                            $oneItemData = $oneItemObj;

                            $itemHSN = "";
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

                            if ($vendorGstinStateCode == $customerGstinStateCode) {
                                $itemTotalPrice = ($itemUnitPrice * $itemQty) + $cgst + $sgst;
                            } else {
                                $itemTotalPrice = ($itemUnitPrice * $itemQty) + $igst;
                            }


                            $internalItemId = "";
                            $internalItemCode = "";
                            $internalItemHsn = "";
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
                            // $itemHSN = $oneItemData["ProductCode"] ?? $itemHSN;

                            //Check for mapped Item
                            if ($internalItemCode == "") {
                                $itemHSN = $oneItemData["ProductCode"];
                                $itemName = $oneItemData["Description"] ?? "";
                            }


                            if ($itemName == "" || strtolower($itemName) == "cgst" || strtolower($itemName) == "sgst") {
                                continue;
                            }
                            $sl += 1;
                        ?>

                            <tr id="grnItemRowTr_<?= $sl ?>">
                                <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                                <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                                <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                                <input type="hidden" id = "grnItemQty_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemQty]" value="<?= $itemQty ?>" />
                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                                <!-- <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                                <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                                <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                                <input type="hidden" class="ItemInvoiceTotalPrice" id="ItemInvoiceTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemTotalPrice]" value="<?= $itemUnitPrice * $itemQty ?>" />
                                <input type="hidden" id="ItemInvoiceCGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCGST]" value="<?= $cgst ?>" />
                                <input type="hidden" id="ItemInvoiceSGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemSGST]" value="<?= $sgst ?>" />
                                <input type="hidden" id="ItemInvoiceIGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemIGST]" value="<?= $igst ?>" />
                                <input type="hidden" id="ItemInvoiceUnits_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUnits]" value="<?= $invoice_units ?>" />


                                <td><?= $sl ?></td>
                                <td id="grnItemNameTdSpan_<?= $sl ?>"><?= $itemName ?></td>
                                <td class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                                    <?php
                                    if($postStatus != 0)
                                    {
                                        echo $internalItemCode;
                                    }
                                    else
                                    {
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
                                <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                                    <?php if ($itemType == "goods") {

                                        // $checkforposted = 

                                    ?>
                                        <select class="form-control text-xs" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" required>
                                            <option value="">Select storage location</option>
                                            <?php
                                            foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                                echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    <?php


                                    } ?>
                                </td>
                                <td id="grnItemStkQtyTdSpan_<?= $sl ?>">
                                    <?php if ($itemType == "goods") {  ?>
                                        <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                            <input step="0.01" type="number" value="<?= $itemQty ?>" class="form-control text-xs w-50" name="grnItemList[<?= $sl ?>][itemStockQty]">
                                            <p class="text-xs" id="grnItemUOM_<?= $sl ?>"><?= $internalItemUom ?></p>
                                        </div>
                                    <?php } ?>
                                </td>
                                <td id="grnItemInvoiceQtyTdSpan_<?= $sl ?>"><?= $itemQty . " " . $invoice_units ?> </td>
                                <td>
                                    <div class="form-input">
                                        <input step="0.01" type="number" name="grnItemList[<?= $sl ?>][itemReceivedQty]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs received_quantity" required>
                                    </div>
                                </td>
                                <!-- <td class="text-right" id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= number_format($itemUnitPrice, 2) ?></td> -->
                                <td>
                                    <div class="form-input">
                                        <input step="0.01" type="number" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceTdInput_<?= $sl ?>" class="form-control text-xs itemUnitPrice" required>
                                    </div>
                                </td>
                                <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= number_format($itemUnitPrice * $itemQty, 2) ?></td>
                                <td class="text-right" id="grnItemInvoiceCGSTTdSpan_<?= $sl ?>"><?= number_format($cgst, 2) ?></td>
                                <td class="text-right" id="grnItemInvoiceSGSTTdSpan_<?= $sl ?>"><?= number_format($sgst, 2) ?></td>
                                <td class="text-right" id="grnItemInvoiceIGSTTdSpan_<?= $sl ?>"><?= number_format($igst, 2) ?></td>
                                <td class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </td>
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
                                <span class='error' id= 'grnItemMessage_<?= $sl ?>'>
                                    <?php if (strtolower($invoice_units) != strtolower($internalItemUom)) echo "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>stock keeping unit and invoice driven unit is different"; ?>
                                </span>
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
                            $grandcgst += $sgst;
                            $grandigst += $igst;
                        }
                        ?>
                        <tr class="itemTotals">
                            <td colspan="9" class="text-right" style="background: none; border: 0;">Sub Total</td>
                            <td class="text-right" id="grandSubTotalTd" style="background: none; border: 0;"><?= number_format($totalSubtotal, 2) ?></td>
                        </tr>

                        <?php

                        if ($totalCGST == 0 && $totalSGST == 0 && $totalIGST == 0) {
                            $toalTotal = $GrandtoalTotal;
                            $totalCGST = $cgst;
                            $totalSGST = $sgst;
                            $totalIGST = $igst;
                        } else {
                            $toalTotal = $totalSubtotal + $totalCGST + $totalSGST + $totalIGST;
                        }


                        if ($vendorGstinStateCode == $customerGstinStateCode) {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="9" class="text-right" style="background: none; border: 0;">Total CGST</td>
                                <td class="text-right" style="background: none; border: 0;"><?= number_format($totalCGST, 2) ?></td>
                            </tr>
                            <tr class="itemTotals">
                                <td colspan="9" class="text-right" style="background: none; border: 0;">Total SGST</td>
                                <td class="text-right" style="background: none; border: 0;"><?= number_format($totalSGST, 2) ?></td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr class="itemTotals">
                                <td colspan="9" class="text-right" style="background: none; border: 0;">Total IGST</td>
                                <td class="text-right" style="background: none; border: 0;"><?= number_format($totalIGST, 2) ?></td>
                            </tr>
                        <?php
                        }

                        ?>
                        <tr class="itemTotals">
                            <input type="hidden" id="totalCGST" name="totalInvoiceCGST" value="<?= $totalCGST ?>">
                            <input type="hidden" id="totalSGST" name="totalInvoiceSGST" value="<?= $totalSGST ?>">
                            <input type="hidden" id="totalIGST" name="totalInvoiceIGST" value="<?= $totalIGST ?>">
                            <input type="hidden" id="grandTotal" name="totalInvoiceSubTotal" value="<?= $totalSubtotal ?>">
                            <input type="hidden" id="grandSubTotal" name="totalInvoiceTotal" value="<?= $toalTotal ?>">
                            <td colspan="9" class="text-right" style="background: none; border: 0;">Total Amount</td>
                            <td class="text-right" id="grandTotalTd" style="background: none; border: 0;"><?= number_format($toalTotal, 2) ?></td>
                        </tr>
                    </tbody>
                </table>



            </div>

            <?php
            if ($isPoAndGrnInvoiceMatched) {

                if ($postStatus == 0) {
            ?>
                    <input type="hidden" name="addNewGrnFormSubmitBtn" value="formSubmit">
                    <button type="submit" id="addNewGrnFormSubmitBtn" value="Submit GRN" class="btn btn-primary float-right mt-3 mb-3">Submit GRN</button>
                <?php
                }
            } else {
                ?>
                <input type="hidden" name="addNewGrnFormSubmitDraftBtn" value="formSubmit">
                <button type="submit" id="addNewGrnFormSubmitBtn" value="Submit GRN as Draft" class="btn btn-primary float-right mt-3 mb-3">Submit GRN as Draft</button>
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
                        <span class="text-muted">The vendro is being added with the basic details. Account, POC, Other details need to be added latter.</span>
                        <form action="" method="post" id="vendorQuickAddForm">
                            <input type="hidden" name="pendingGrnId" value="<?= $_GET["view"] ?>">
                            <label for="">Vendor Name</label>
                            <input type="text" name="vendorName" value="<?= $vendorName ?>" class="form-control" required>
                            <label for="">Vendor Gstin</label>
                            <input type="text" name="vendorGstin" value="<?= $vendorGstin ?>" class="form-control" required>
                            <label for="">Vendor Pan</label>
                            <input type="text" name="vendorPan" value="<?= $vendorPan ?>" class="form-control" required>
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
                            </div>
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
                        <h5 class="modal-title">Map Item</h5>
                        <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="mapInvoiceItemCodeForm">
                            <input type="hidden" name="modalItemSlNo" id="modalItemSlNo" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mapInvoiceItemTypeRadioBtn" id="mapInvoiceItemTypeGoods" value="goods" checked>
                                <label class="form-check-label" for="mapInvoiceItemTypeGoods">
                                    Goods
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mapInvoiceItemTypeRadioBtn" id="mapInvoiceItemTypeService" value="service">
                                <label class="form-check-label" for="mapInvoiceItemTypeService">
                                    Services
                                </label>
                            </div>
                            <small class="text-muted mt-2">Item Description</small>
                            <textarea name="modalItemDescription" id="modalItemDescription" cols="1" rows="3" class="form-control" readonly></textarea>
                            <input type="hidden" name="modalItemQtyMap" id="modalItemQtyMap">
                            <small class="text-muted mt-3">Select Item Code</small>
                            <select class="form-control" name="modalItemCode" id="modalItemCodeDropDown" required>
                                <?php
                                $goodsController = new GoodsController();
                                $rmGoodsObj = $goodsController->getAllRMGoods();
                                if ($rmGoodsObj["status"] == "success") {
                                    echo '<option value="" data-hsncode="" data-itemtitle="">Select Item</option>';
                                    foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                                ?>
                                        <option value="<?= $oneRmGoods["itemCode"]; ?>" data-name=<?= $oneRmGoods["itemName"]; ?> data-uom="<?= $oneRmGoods["uomName"]; ?>" data-itemid="<?= $oneRmGoods["itemId"]; ?>" data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>" data-itemtitle="<?= $oneRmGoods["itemName"]; ?>"><?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["itemDesc"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <br>
                            <button type="submit" name="mapItemCodeFormSubmitBtn" class="btn btn-primary btnstyle mt-2">Map Code</button>
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
                        <h5 class="modal-title" style="color:white;">Change Code</h5>
                        <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="changeInvoiceItemCodeForm">
                            <input type="hidden" name="modalItemSlNoChange" id="modalItemSlNoChange" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mapInvoiceItemTypeChangeRadioBtn" id="changeInvoiceItemTypeGoods" value="goods" checked>
                                <label class="form-check-label" for="changeInvoiceItemTypeGoods">
                                    Goods
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mapInvoiceItemTypeChangeRadioBtn" value="service" id="changeInvoiceItemTypeService">
                                <label class="form-check-label" for="changeInvoiceItemTypeService">
                                    Services
                                </label>
                            </div>
                            <small class="text-muted mt-2">Item Description</small>
                            <textarea name="modalItemDescriptionChange" id="modalItemDescriptionChange" cols="1" rows="3" class="form-control" readonly></textarea>
                            <input type="hidden" name="modalItemQtyChange" id="modalItemQtyChange">
                            <small class="text-muted mt-3">Select Item Code</small>
                            <select class="form-control" name="modalItemCodeChange" id="modalItemCodeDropDownChange" required>
                                <?php
                                $goodsController = new GoodsController();
                                $rmGoodsObj = $goodsController->getAllRMGoods();
                                if ($rmGoodsObj["status"] == "success") {
                                    echo '<option value="" data-hsncode="" data-itemtitle="">Select Item</option>';
                                    foreach ($rmGoodsObj["data"] as $oneRmGoods) {
                                ?>
                                        <option value="<?= $oneRmGoods["itemCode"]; ?>" data-name=<?= $oneRmGoods["itemName"]; ?> data-uom="<?= $oneRmGoods["uomName"]; ?>" data-itemid="<?= $oneRmGoods["itemId"]; ?>" data-hsncode="<?= $oneRmGoods["hsnCode"]; ?>" data-itemtitle="<?= $oneRmGoods["itemName"]; ?>"><?= $oneRmGoods["itemCode"]; ?> | <?= $oneRmGoods["itemName"]; ?> | <?= $oneRmGoods["itemDesc"]; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                            <button type="submit" name="mapItemCodeFormSubmitBtn" class="btn btn-primary btnstyle mt-2">Change Code</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal end -->


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
                                    <span id = "confirmation_id">Are You sure ?</span>
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
        console.log("hello there!");
        var type = "goods";
        var obj = <?= json_encode($getStorageLocationListForGrnObj) ?>;
        var id = <?= json_encode($id) ?>;


        $(document).on("keyup", ".itemUnitPrice", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });



        $(document).on("keyup", ".received_quantity", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        function calculateOneItemAmounts(rowNo) {
            let itemQty = (parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemReceivedQtyTdInput_${rowNo}`).val()) : 0;
            let itemUnitPrice = (parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitPriceTdInput_${rowNo}`).val()) : 0;
            let cgst = (parseFloat($(`#ItemInvoiceCGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceCGST_${rowNo}`).val()) : 0;
            let sgst = (parseFloat($(`#ItemInvoiceSGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceSGST_${rowNo}`).val()) : 0;
            let igst = (parseFloat($(`#ItemInvoiceIGST_${rowNo}`).val()) > 0) ? parseFloat($(`#ItemInvoiceIGST_${rowNo}`).val()) : 0;


            let basicPrice = itemUnitPrice * itemQty;
            let totalItemPrice = basicPrice + cgst + sgst + igst;

            console.log(totalItemPrice, cgst, sgst, igst);

            $(`#grnItemInvoiceTotalPriceTdSpan_${rowNo}`).html(totalItemPrice.toFixed(2));
            $(`#grnItemInvoiceBaseAmtTdSpan_${rowNo}`).html(basicPrice.toFixed(2));
            $(`#ItemInvoiceTotalPrice_${rowNo}`).val(basicPrice.toFixed(2));
            $(`#ItemInvoiceGrandTotalPrice_${rowNo}`).val(totalItemPrice.toFixed(2));


            calculateGrandTotalAmount();
        }


        function calculateGrandTotalAmount() {
            let totalAmount = 0;
            let grandSubTotalAmt = 0;
            // $(".ItemInvoiceGrandTotalPrice").each(function() {
            //     totalAmount += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            // });
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            // console.log("Grand = ", totalAmount, totalTaxAmount, totalDiscountAmount);
            // let grandSubTotalAmt = totalAmount - totalTaxAmount - totalDiscountAmount;
            let ToTalcgst = (parseFloat($(`#totalCGST`).val()) > 0) ? parseFloat($(`#totalCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalSGST`).val()) > 0) ? parseFloat($(`#totalSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalIGST`).val()) > 0) ? parseFloat($(`#totalIGST`).val()) : 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst;

            $("#grandSubTotalTd").html(grandSubTotalAmt.toFixed(2));
            $("#grandSubTotal").val(grandSubTotalAmt.toFixed(2));
            $("#grandTotalTd").html(totalAmount.toFixed(2));
            $("#grandTotal").val(totalAmount.toFixed(2));
        }



        $("#modalItemCodeDropDown").select2({
            dropdownParent: $("#mapInvoiceItemCode")
        });

        //$("#modalItemCodeDropDown").select2();

        let vendorCode = `<?= $vendorCode ?>`;
        let vendorId = `<?= $vendorId ?>`;
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
            $("#invoicePoNumber").val(passedCode);
            console.log(passedCode);

            $.ajax({
                url: "ajaxs/po/ajax-update-po-grn.php?grn="+id+"&po="+passedCode,
                type: "GET",
                beforeSend: function() {},
                success: function(response) {
                    let responseObj = JSON.parse(response);
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
            $("#confirmation_id").html("Are you sure you want to map this invoice with "+name+" ??");
            $("#vendorYes").val(id);

        });


        $("#vendorYes").click(function() {
            var vendor_id = $(this).val();
            $.ajax({
                url: "ajaxs/vendor/ajax-update-vendor-grn.php?grn="+id+"&vendor="+vendor_id,
                type: "GET",
                beforeSend: function() {},
                success: function(response) {
                    let responseObj = JSON.parse(response);
                    console.log(responseObj);

                    $("#invoiceVendorCodeInput").val(responseObj["code"]);
                        $("#invoiceVendorIdInput").val(vendor_id);
                        $("#invoiceVendorCodeSpan").html(responseObj["code"]);
                        $("#dialogForVendorQuickAddCloseBtn").click();

                        $("#uploaded-invoice-preview-div-tab").click();
                        $("#vendor-quick-registration-div").remove();
                        $("#vendor-quick-registration-div-tab").remove();
                        $("#vendor_list_tab").remove();
                        $("#vendor_confirmation").hide();

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

        $(".openModalMapInvoiceItemCode").click(function() {
            let itemSlNo = $(this).data("itemrownum");
            let itemDescription = ($(`#ItemGRNName_${itemSlNo}`).val()).trim();
            let itemQty = $(`#grnItemQty_${itemSlNo}`).val();
            console.log(itemQty);
            $("#modalItemQtyMap").val(itemQty);
            $("#modalItemDescription").val(itemDescription);
            $("#modalItemSlNo").val(itemSlNo);
            $('#modalItemCodeDropDown').prop('selectedIndex', 0);
        });

        $(".openModalMapInvoiceItemCodeChange").click(function() {
            let itemSlNo = $(this).data("itemrownum");
            let itemDescription = ($(`#ItemGRNName_${itemSlNo}`).val()).trim();
            let itemQty = $(`#grnItemQty_${itemSlNo}`).val();
            console.log(itemQty);
            $("#modalItemQtyChange").val(itemQty);
            $("#modalItemDescriptionChange").val(itemDescription);
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
                let itemName = $("#modalItemCodeDropDown").find(':selected').data("name");
                let itemTitle = ($("#modalItemDescription").val()).trim();
                let itemQty = $("#modalItemQtyMap").val();
                let itemType = type;
                let taskType = "map";
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
                            $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"]+" "+"<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='"+itemSlNo+"' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a>");
                            $(`#internalItemId_${itemSlNo}`).val(mapData["itemId"]);
                            $(`#internalItemCode_${itemSlNo}`).val(mapData["itemCode"]);
                            $(`#grnItemHSNTdSpan_${itemSlNo}`).html(mapData["itemHSN"]);
                            $(`#internalItemHsn_${itemSlNo}`).val(mapData["itemHSN"]);
                            $(`#grnItemUOM_${itemSlNo}`).html(mapData["itemUom"]);
                            $(`#grnItemNameTdSpan_${itemSlNo}`).html(itemName);
                            var itemInvoiceUnits = $(`#ItemInvoiceUnits_${itemSlNo}`).val();
                            var InternalItemUom = mapData["itemUom"];

                            if(itemInvoiceUnits.toLowerCase() == InternalItemUom.toLowerCase())
                            {
                                $(`#grnItemMessage_${itemSlNo}`).html("");
                            }
                            else
                            {
                                $(`#grnItemMessage_${itemSlNo}`).html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>stock keeping unit and invoice driven unit is different");
                            }

                            $(`#mapInvoiceItemCode`).hide();

                            var storageLoc = "";
                            var derivedQty = "";
                            if (itemType == "goods") {
                                storageLoc += "<select class='form-control text-xs' name='grnItemList[" + itemSlNo + "][itemStorageLocationId]' required><option value=''>Select storage location</option>";

                                var objects = obj.data;

                                for (let i = 0; i < objects.length; i++) {
                                    storageLoc += "<option value='" + objects[i].storage_location_id + "'>" + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                }

                                storageLoc += "</select>";

                                $(`#grnItemStrgLocTdSpan_${itemSlNo}`).html(storageLoc);
                                // $(`#grnItemStkQtyTdSpan_${itemSlNo}`).css("display", "block");

                                derivedQty += "<div class='form-input d-flex' style='align-items: center; gap: 7px;'><input type='number' value='"+itemQty+"' class='form-control text-xs w-50' name='grnItemList["+itemSlNo+"][itemStockQty]'><p class='text-xs' id='grnItemUOM_"+itemSlNo+"'>"+itemUOM+"</p></div>";
                                $(`#grnItemStkQtyTdSpan_${itemSlNo}`).html(derivedQty);


                                console.log(storageLoc);

                            } else {
                                $(`#grnItemStrgLocTdSpan_${itemSlNo}`).html("<p></p>");
                                // $(`#grnItemStkQtyTdSpan_${itemSlNo}`).css("display", "none");
                            }

                        }
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
                console.log("maping item code");
                let itemSlNo = $("#modalItemSlNoChange").val();
                let itemCode = $("#modalItemCodeDropDownChange").val();
                let itemId = $("#modalItemCodeDropDownChange").find(':selected').data("itemid");
                let itemHSN = $("#modalItemCodeDropDownChange").find(':selected').data("hsncode");
                let itemUOM = $("#modalItemCodeDropDownChange").find(':selected').data("uom");
                let itemTitle = ($("#modalItemDescriptionChange").val()).trim();
                let itemName = $("#modalItemCodeDropDownChange").find(':selected').data("name");
                let itemQty = $("#modalItemQtyChange").val();
                console.log(itemQty);
                let itemType = type;
                let taskType = "change";

                console.log(itemId);

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
                            $(`#grnItemCodeTdSpan_${itemSlNo}`).html(mapData["itemCode"]+" "+"<a class='btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange' data-itemrownum='"+itemSlNo+"' data-toggle='modal' data-target='#mapInvoiceItemCodeChange'><i class='fas fa-pencil-alt'></i></a>");
                            $(`#internalItemId_${itemSlNo}`).val(mapData["itemId"]);
                            $(`#internalItemCode_${itemSlNo}`).val(mapData["itemCode"]);
                            $(`#grnItemHSNTdSpan_${itemSlNo}`).html(mapData["itemHSN"]);
                            $(`#internalItemHsn_${itemSlNo}`).val(mapData["itemHSN"]);
                            $(`#grnItemUOM_${itemSlNo}`).html(mapData["itemUom"]);
                            $(`#grnItemNameTdSpan_${itemSlNo}`).html(itemName);
                            var itemInvoiceUnits = $(`#ItemInvoiceUnits_${itemSlNo}`).val();
                            var InternalItemUom = mapData["itemUom"];
                            if(itemInvoiceUnits.toLowerCase() == InternalItemUom.toLowerCase())
                            {
                                $(`#grnItemMessage_${itemSlNo}`).html("");
                            }
                            else
                            {
                                $(`#grnItemMessage_${itemSlNo}`).html("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>stock keeping unit and invoice driven unit is different");
                            }
                            $(`#mapInvoiceItemCodeChange`).hide();

                            console.log(itemType);

                            var storageLoc = "";
                            var derivedQty = "";
                            if (itemType == "goods") {
                                storageLoc += "<select class='form-control text-xs' name='grnItemList[" + itemSlNo + "][itemStorageLocationId]' required><option value=''>Select storage location</option>";

                                var objects = obj.data;

                                for (let i = 0; i < objects.length; i++) {
                                    storageLoc += "<option value='" + objects[i].storage_location_id + "'>" + objects[i].storage_location_code + " | " + objects[i].storage_location_name + "</option>";
                                }

                                storageLoc += "</select>";

                                $(`#grnItemStrgLocTdSpan_${itemSlNo}`).html(storageLoc);
                                // $(`#grnItemStkQtyTdSpan_${itemSlNo}`).css("display", "block");
                                derivedQty += "<div class='form-input d-flex' style='align-items: center; gap: 7px;'><input type='number' value='"+itemQty+"' class='form-control text-xs w-50' name='grnItemList["+itemSlNo+"][itemStockQty]'><p class='text-xs' id='grnItemUOM_"+itemSlNo+"'>"+itemUOM+"</p></div>";
                                $(`#grnItemStkQtyTdSpan_${itemSlNo}`).html(derivedQty);


                                console.log(storageLoc);

                            } else {
                                $(`#grnItemStrgLocTdSpan_${itemSlNo}`).html("<p></p>");
                                // $(`#grnItemStkQtyTdSpan_${itemSlNo}`).css("display", "none");
                            }

                        }
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
                    $('#iframeHolder').html('<iframe src="../bills/<?= $invoiceFile ?>" id="grnInvoicePreviewIfram" width="100%" height="100%"></iframe>');
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



    });
</script>

<?php
    } else if (isset($_GET["posting"])) {
        require_once("components/grn/posted-grn.php");
    } else {
        require_once("components/grn/pending-grn.php");
    }
    require_once("../common/footer.php");
?>