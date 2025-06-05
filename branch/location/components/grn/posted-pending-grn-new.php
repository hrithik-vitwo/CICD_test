<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/common/func-common.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");

global $companyCountry;
$lable = (getLebels($companyCountry)['data']);
$lable = json_decode($lable, true);
$tdslable = ($lable['source_taxation']);
$tcslable = $lable['transaction_taxation'];

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

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
        margin: 40px 0 10px;
    }

    table.grn-table tr td {
        padding: 5px 15px !important;
    }

    table.grn-table tr td input,
    table.grn-table tr td select {
        height: 30px;
        width: auto !important;
    }

    table.grn-table tr td select {
        width: auto;
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
        top: 0;
        left: 0;
        margin: 7px 0;
        display: flex !important;
        align-items: center;
        gap: 7px;
        justify-content: flex-end;
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

    .row.grn-vendor-details .display-flex select {
        font-size: 9px !important;
        background: none;
        border: 0;
        max-width: 120px;
        cursor: pointer;
        height: auto;
        background-color: #0000001a;
        margin: 6px 0px;
    }

    .total-amount-grn-table .card {
        max-width: 500px;
        margin-left: auto;
        border-radius: 7px;
        margin-bottom: 0;
    }

    .grn-table.pending-grn-view table tr.span-error-tr td.bg-transparent {
        background: #fff !important;
    }

    .total-amount-grn-table .card td {
        padding: 10px 15px;
    }

    .select-type {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .select-type div {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .select-type div label {
        margin-bottom: 0;
    }

    form#mapInvoiceItemCodeForm .select2-container {
        width: 100% !important;
    }

    form#changeInvoiceItemCodeForm .select2-container {
        width: 100% !important;
    }

    .filter-list a.active {
        background: #003060;
        color: #fff;
    }




    @media (max-width: 575px) {
        #grnInvoicePreviewIfram {
            display: block;
        }

        div.grn-table {
            margin: 50px 0;
        }

        span.error {
            left: 440px;
        }
    }
</style>
<style>
    .grn-srn-view .row.grn-create .card {
        min-height: auto;
        height: 350px;
        margin-bottom: 0;
    }
</style>


<?php

$grnObj = new GrnController();

$accountingControllerObj = new Accounting();

?>

<div class="grn-srn-view">

    <section class="content">

        <div class="container-fluid">

            <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="<?= LOCATION_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

                <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage GRN</a></li>

                <li class="breadcrumb-item active">

                    <a href="" class="text-dark">View</a>

                </li>

                <li class="back-button">

                    <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

                        <i class="fa fa-reply po-list-icon"></i>

                    </a>

                </li>

            </ol>

        </div>

        <div class="container-fluid">

            <?php



            if (isset($_POST["ivPostingFormSubmitBtn"])) {



                $ivPostingData = json_decode(base64_decode($_POST["ivPostingGrnData"]), true);

                // console($_POST);
                // echo '<br>---------------------ivPostingData-------------------';
                // console($ivPostingData);



                $grnPostingJournalId = $ivPostingData["grnDetails"]["grnPostingJournalId"];
                $grnId = $ivPostingData["grnDetails"]["grnId"];



                // $grIrItemsObj = queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $grnPostingJournalId, true);

                // $grIrItems = [];

                // foreach ($grIrItemsObj["data"] as $grIrItem) {

                //     $grIrItems[] = $grIrItem["credit_amount"];
                // }

                // echo '<br>---------------------grIrItems-------------------';
                // console($_POST['grnItemList']);
                // exit;


                $vendorDetailsObj = queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=' . $_POST["vendorId"]);

                $vendorParentGlId = $vendorDetailsObj["data"]["parentGlId"] ?? 0;

                $tdsDetails = [];

                $postingDate = $_POST['invoicePostingDate'] ?? date("Y-m-d");
                $extra_remark = $POST['extra_remark'] ?? '';


                $symbol = $_POST["adjust_symbol"];
                $roundOffValue = $_POST["roundOffGL"];
                if ($symbol == "add") {
                    $roundOffGL = $roundOffValue;
                } else {
                    $roundOffGL = $roundOffValue * -1;
                }


                $ivPostingInputData = [

                    "BasicDetails" => [

                        "documentNo" => $_POST["documentNo"], // Invoice Doc Number

                        "documentDate" => $_POST["documentDate"], // Invoice number

                        "postingDate" => $postingDate, // current date

                        "grnJournalId" => $grnPostingJournalId,

                        "reference" => $_POST["grnCode"], // grn code

                        "remarks" => "Invoice Posting - " . $_POST["grnCode"] . " " . $extra_remark,

                        "journalEntryReference" => "Purchase"

                    ],

                    "vendorDetails" => [

                        "vendorId" => $_POST["vendorId"],

                        "vendorName" => $_POST["vendorName"],

                        "vendorCode" => $_POST["vendorCode"],

                        "parentGlId" => $vendorParentGlId

                    ],

                    "grIrItems" => $_POST['grnItemList'],

                    "taxDetails" => [

                        "cgst" => $_POST["totalInvoiceCGST"],

                        "sgst" => $_POST["totalInvoiceSGST"],

                        "igst" => $_POST["totalInvoiceIGST"]

                    ],

                    "tdsDetails" => $tdsDetails,
                    "roundOffValue" => $roundOffGL

                ];


                $createInvObj = $grnObj->createInvoice($_POST);


                if ($createInvObj["status"] == "success") {

                    //console($ivPostingInputData);
                    $ivPostingObj = $accountingControllerObj->grnIvAccountingPosting($ivPostingInputData, "grniv", $createInvObj['grnIVId']);
                    $queryObj = queryUpdate('UPDATE `erp_grninvoice` SET `ivPostingJournalId`=' . $ivPostingObj["journalId"] . ' WHERE `grnIVId`=' . $createInvObj['grnIVId']);


                    swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"], BASE_URL . "branch/location/manage-vendor-invoice.php");
                } else {
                    swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"]);
                }

                // console($createInvObj);
                // console($createInvObj);

                // exit();

            }







            $getGrnDetailsObj = $grnObj->getGrnDetails($_GET["view"]);

            if ($getGrnDetailsObj["numRows"] == 1) {

                //console($getGrnDetailsObj);

                $grnDetails = $getGrnDetailsObj["data"];
                

                $grnItemDetailsObj = $grnObj->getGrnItemDetails($_GET["view"]);

                global $branch_id;

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

                //console($grnItemDetailsObj);

            ?>

                <form action="" method="POST" id="addNewGRNForm">

                    <input type="hidden" name="ivPostingGrnId" value="<?= $grnDetails["grnId"] ?>">

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

                                            <input type="hidden" name="grnCode" value="<?= $grnDetails["grnCode"] ?? "" ?>">
                                            <input type="hidden" name="documentNo" value="<?= $grnDetails["vendorDocumentNo"] ?? "" ?>">
                                            <input type="hidden" name="documentDate" value="<?= $grnDetails["vendorDocumentDate"] ?? "" ?>">
                                            <input type="hidden" name="vendorDocumentFile" value="<?= $grnDetails["vendorDocumentFile"] ?? "" ?>">
                                            <input type="hidden" name="vendorGstinStateName" value="<?= $grnDetails["vendorGstinStateName"] ?? "" . '(' . substr($grnDetails["vendorGstin"], 0) . ')'; ?>">
                                            <input type="hidden" name="locationGstinStateName" value="<?= $grnDetails["locationGstinStateName"] ?? "" . '(' . substr($loginBranchGstin, 0) . ')' ?>">
                                            <input type="hidden" name="invoicePostingDate" value="<?= $grnDetails["postingDate"] ?>" required>
                                            <input type="hidden" name="invoiceDueDate" value="<?= $grnDetails["dueDate"] ?>" required>
                                            <input type="hidden" name="invoicePoNumber" value="<?= $grnDetails["grnPoNumber"] ?>">
                                            <input type="hidden" name="grnType" value="grn">
                                            <input type="hidden" name="vendorCode" id="invoiceVendorCodeInput" value="<?= $grnDetails["vendorCode"] ?>" class="form-control" />
                                            <input type="hidden" name="vendorId" id="invoiceVendorIdInput" value="<?= $grnDetails["vendorId"] ?>" class="form-control" />
                                            <input type="hidden" name="vendorName" value="<?= $grnDetails["vendorName"] ?>" class="form-control" />
                                            <input type="hidden" name="vendorGstin" value="<?= $grnDetails["vendorGstin"] ?>" class="form-control" />
                                            <input type="hidden" name="totalInvoiceSubTotal" value="<?= $grnDetails["grnSubTotal"] ?>">
                                            <input type="hidden" name="totalInvoiceTotal" id="totalInvoiceTotal" value="<?= $grnDetails["grnTotalAmount"] ?>">
                                            <input type="hidden" name="grnApprovedStatus" value="<?= $grnDetails["grnApprovedStatus"] ?>">
                                            <input type="hidden" name="id" value="<?= $grnDetails["grnId"] ?>">



                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">GRN No :&nbsp;</p>

                                                <p><?= $grnDetails["grnCode"] ?></p>

                                            </div>

                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document No :&nbsp;</p>

                                                <p><?= $grnDetails["vendorDocumentNo"] ?></p>

                                            </div>

                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Document Date :&nbsp;</p>
                                                <?php
                                                $originalvendorDocumentDate = $grnDetails["vendorDocumentDate"];
                                                $vendorDocumentDatetimestamp = strtotime($originalvendorDocumentDate);

                                                ?>
                                                <p><?= date("d-m-Y", $vendorDocumentDatetimestamp) ?></p>

                                            </div>

                                            <div class="display-flex grn-form-input-text">

                                                <i class="fa fa-check"></i>

                                                &nbsp;

                                                <p class="label-bold">Posting Date :</p>

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

                                                <p><input type="date" id="invoicePostingDateId" name="invoicePostingDate" class="form-control" value="<?= $grnDetails["postingDate"] ?>" min="<?= $min ?>" $max="<?= $max ?>" required></p>
                                                <p class="text-danger text-xs" id="postdatelabel"></p>

                                            </div>

                                            <div class="display-flex grn-form-input-text">

                                                <i class="fa fa-check"></i>

                                                &nbsp;

                                                <?php
                                                $originaldueDate = $grnDetails["dueDate"];
                                                $dueDatetimestamp = strtotime($originaldueDate);

                                                ?>

                                                <p class="label-bold">Due Date :</p>

                                                &nbsp;

                                                <p><?= date('d-m-Y', $dueDatetimestamp) ?></p>

                                            </div>

                                            <?php

                                            if ($grnDetails["grnPoNumber"] != "") {

                                            ?>

                                                <div class="display-flex grn-form-input-text">

                                                    <i class="fa fa-check"></i>

                                                    &nbsp;

                                                    <p class="label-bold">PO Number :</p>

                                                    &nbsp;

                                                    <p><?= $grnDetails["grnPoNumber"] ?></p>

                                                </div>

                                            <?php

                                            }

                                            ?>
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

                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Code :&nbsp;</p>

                                                <p id="invoiceVendorCodeSpan"><?= $grnDetails["vendorCode"] ?? "" ?></p>

                                            </div>

                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">Name :&nbsp;</p>

                                                <p><?= $grnDetails["vendorName"] ?? "" ?></p>

                                            </div>

                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold"><?=$lable['fields']['businessTaxID']?> :&nbsp;</p>

                                                <p> <?= $grnDetails["vendorGstin"] ?? "" ?></p>

                                            </div>

                                            <div class="display-flex"><i class="fa fa-check"></i>&nbsp;<p class="label-bold">State :&nbsp;</p>

                                                <p><?= $grnDetails["vendorGstinStateName"] ?? "" ?></p>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">

                            <div class="row">

                                <div class="card card-tabs">

                                    <div class="card-header">

                                        <div class="head">
                                            <i class="fa fa-file"></i>
                                            <h4>Uploaded Bill</h4>
                                        </div>

                                    </div>

                                    <div class="card-body">

                                        <div class="tab-content tab-col" id="custom-tabs-three-tabContent">

                                            <div class="tab-pane fade show active iframe-preview-btn" id="uploaded-invoice-preview-div" role="tabpanel" aria-labelledby="uploaded-invoice-preview-div-tab">

                                                <?php
                                                if (isset($grnDetails["vendorDocumentFile"]) && $grnDetails["vendorDocumentFile"] != "" && $grnDetails["vendorDocumentFile"] != NULL) {

                                                    if (file_exists(COMP_STORAGE_URL . "/grn-invoice/" . $grnDetails["vendorDocumentFile"])) {

                                                ?>

                                                        <iframe src='<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $grnDetails["vendorDocumentFile"] ?? "" ?>' id="grnInvoicePreviewIfram" width="100%" height="220">

                                                            <p>This browser does not support PDF!</p>

                                                        </iframe>

                                                        <div class="preview-btn-space">

                                                            <button type="button" class="btn btn-primary preview-btn" id="iframePreview" data-toggle="modal" data-target="#exampleModalCenter">

                                                                Preview

                                                            </button>

                                                        </div>

                                                <?php

                                                    } else {

                                                        echo "Bill file not found";
                                                    }
                                                } else {

                                                    echo "Bill file not found";
                                                }

                                                ?>



                                                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

                                                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">

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

                                    <th width="10%">Sl No.</th>

                                    <th>Item Name</th>

                                    <th width="10%">Internal Code</th>

                                    <th width="10%">Item HSN</th>

                                    <th width="10%">St. Loc.</th>

                                    <th width="10%">Derived Qty</th>

                                    <th width="10%">Invoice Qty</th>

                                    <th width="10%">Received Qty</th>

                                    <th width="10%">Unit Price</th>

                                    <th width="10%">Basic Amount</th>
                                    <?php if ($companyCountry == 103) { ?>
                                        <th width="10%">CGST</th>
                                        <th width="10%">SGST</th>
                                        <th width="10%">IGST</th>
                                    <?php } else {
                                        // $getItemTaxRule = getItemTaxRule($companyCountry, $vendorGstinStateCode, $customerGstinStateCode);
                                        $taxComponents = json_decode($grnDetails['taxComponents'], true);
                                    ?>
                                        <?php
                                        foreach ($taxComponents as $t) {
                                            echo "<th width='10%'>{$t['gstType']}</th>";
                                        }
                                        ?>

                                    <?php } ?>
                                    <th width="50%"><?= $tdslable ?></th>

                                    <!-- <th width="10%">Total Amount</th> -->

                                </tr>

                            </thead>

                            <tbody id="itemsTable">

                                <?php



                                $formEncodedData = base64_encode(json_encode([

                                    "grnDetails" => $grnDetails,

                                    "grnItemDetails" => $grnItemDetailsObj["data"]

                                ]));


                                // console($grnItemDetailsObj["data"]);

                                echo '<input type="hidden" name="ivPostingGrnData" value="' . $formEncodedData . '" />';







                                $sl = 0;
                                $total_tds = 0;

                                foreach ($grnItemDetailsObj["data"] as $oneItemDetails) {

                                    $sl += 1;

                                    $basic_amount = $oneItemDetails["unitPrice"] * $oneItemDetails["receivedQty"];
                                    $tds = $oneItemDetails["tds"] ?? 0;
                                    $tds_value = $basic_amount * $tds / 100;
                                    $total_tds += $tds_value;

                                    $total_amount = $basic_amount + $oneItemDetails["cgst"] + $oneItemDetails["sgst"] + $oneItemDetails["igst"] - $tds_value;

                                ?>

                                    <tr id="grnItemRowTr_<?= $sl ?>">

                                        <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemId]" value="<?= $oneItemDetails["goodId"] ?>" />
                                        <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCode]" value="<?= $oneItemDetails["goodCode"] ?>" />
                                        <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemHsn]" value="<?= $oneItemDetails["goodHsn"] ?>" />
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemName]" value="<?= $oneItemDetails["goodName"] ?>" />
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemQty]" value="<?= $oneItemDetails["itemStocksQty"] ?>" />
                                        <input type="hidden" class="ItemInvoiceTotalPrice" name="grnItemList[<?= $sl ?>][baseAmount]" id="grnItemBaseInput_<?= $sl ?>" value="<?= $oneItemDetails["unitPrice"] * $oneItemDetails["receivedQty"] ?>" />
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $oneItemDetails["unitPrice"] ?>" />
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemTotalPrice]" value="<?= $oneItemDetails["totalAmount"] ?>" />
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemStockQty]" value="<?= $oneItemDetails["itemStocksQty"] ?>" class="form-control text-xs w-50" \>
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemReceivedQty]" value="<?= $oneItemDetails["receivedQty"] ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs">
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" value="<?= $oneItemDetails["itemStorageLocation"] ?>" />
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemTotalTDS]" id="grnItemTDSValue_<?= $sl ?>" value="<?= $tds_value ?>" class="ItemTotalTds" />
                                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemUOM]" id="grnItemUom_<?= $sl ?>" value="<?= $oneItemDetails["itemUOM"] ?>" />





                                        <td><?= $sl ?></td>

                                        <td>
                                            <p style="white-space: pre-wrap;"><?= ucfirst($oneItemDetails["goodName"]) ?></p>
                                        </td>

                                        <td><?= $oneItemDetails["goodCode"] ?></td>

                                        <td><?= $oneItemDetails["goodHsn"] ?></td>

                                        <td><?= $oneItemDetails["storage_location_code"] ?></td>

                                        <td><?= $oneItemDetails["itemStocksQty"] . " " . $oneItemDetails["itemUOM"] ?></td>

                                        <td><?= $oneItemDetails["goodQty"] ?></td>

                                        <td><?= $oneItemDetails["receivedQty"] ?></td>

                                        <td class="text-right"><?= number_format($oneItemDetails["unitPrice"], 2) ?></td>

                                        <td class="text-right" id="grnItemBaseUnit_<?= $sl ?>"><?= number_format(($oneItemDetails["unitPrice"] * $oneItemDetails["receivedQty"]), 2) ?></td>

                                        <?php if ($companyCountry == 103) { ?>
                                            <td class="text-right">

                                                <div class="form-input">
                                                    <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemCGST]" value="<?= number_format($oneItemDetails["cgst"], 2, '.', '') ?>" id="grnItemUnitCgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemCgst">
                                                </div>
                                            </td>

                                            <td class="text-right">
                                                <div class="form-input">
                                                    <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemSGST]" value="<?= number_format($oneItemDetails["sgst"], 2, '.', '') ?>" id="grnItemUnitSgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemSgst">
                                                </div>
                                            </td>

                                            <td class="text-right">
                                                <div class="form-input">
                                                    <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemIGST]" value="<?= number_format($oneItemDetails["igst"], 2, '.', '') ?>" id="grnItemUnitIgstInput_<?= $sl ?>" class="form-control text-xs w-auto itemIgst">
                                                </div>
                                            </td>
                                            <?php } else {

                                            $taxComponents1 = json_decode($oneItemDetails['taxComponents'], true);
                                            foreach ($taxComponents1 as $t) { ?>
                                                <td class="text-right">
                                                    <div class="form-input">
                                                        <input type="number" step="any" name="grnItemList[<?= $sl ?>][item<?= (($t['gstType'])) ?>]" value="<?= decimalValue($t["taxAmount"]) ?>" id="grnItemUnit<?= ucfirst(strtolower($t['gstType'])) ?>Input_<?= $sl ?>" class="form-control text-xs w-auto dynamic item<?= ucfirst(strtolower($t['gstType'])) ?>">
                                                    </div>
                                                </td>
                                        <?php
                                            }
                                        } ?>


                                        <td class="text-right">
                                            <div class="form-input d-flex gap-2">
                                                <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemTDS]" value="<?= $oneItemDetails["tds"] ?>" id="grnItemUnitTdsInput_<?= $sl ?>" class="form-control text-xs w-auto itemTds">
                                                <p class="text-xs">%</p>
                                            </div>
                                        </td>

                                        <span style="display: none" class="text-right" id="grnItemUnitTDTotal_<?= $sl ?>"><?= number_format($total_amount, 2) ?></span>

                                    </tr>

                                <?php

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

                                            <td class="text-right" style="background: none;"><?= number_format($grnDetails["grnSubTotal"], 2) ?></td>

                                        </tr>

                                        <?php if ($companyCountry == 103) { ?>
                                            <tr class="itemTotals">

                                                <td colspan="9" style="background: none;">Total CGST</td>

                                                <td class="text-right" style="background: none;">
                                                    <input type="number" step="any" name="totalInvoiceCGST" value="<?= number_format($grnDetails["grnTotalCgst"], 2, '.', '') ?>" id="totalInvoiceCGST" class="form-control text-xs itemUnitPrice">
                                                </td>

                                            </tr>

                                            <tr class="itemTotals">

                                                <td colspan="9" style="background: none;">Total SGST</td>

                                                <td class="text-right" style="background: none;">
                                                    <input type="number" step="any" name="totalInvoiceSGST" value="<?= number_format($grnDetails["grnTotalSgst"], 2, '.', '') ?? 0 ?>" id="totalInvoiceSGST" class="form-control text-xs itemUnitPrice">
                                                </td>

                                            </tr>

                                            <tr class="itemTotals">

                                                <td colspan="9" style="background: none;">Total IGST</td>

                                                <td class="text-right" style="background: none;">
                                                    <input type="number" step="any" name="totalInvoiceIGST" value="<?= number_format($grnDetails["grnTotalIgst"], 2, '.', '') ?? 0 ?>" id="totalInvoiceIGST" class="form-control text-xs itemUnitPrice">
                                                </td>

                                            </tr>
                                        <?php } else { ?>

                                        <?php } ?>

                                        <tr class="itemTotals">

                                            <td colspan="9" style="background: none;">Total <?= $tdslable ?></td>

                                            <td class="text-right" style="background: none;">
                                                <input type="number" step="any" name="totalInvoiceTDS" value="<?= number_format($total_tds, 2, '.', '') ?>" id="totalInvoiceTDS" class="form-control text-xs itemUnitPrice">
                                            </td>

                                        </tr>
                                        <tr class="itemTotals">

                                            <td colspan="9" style="background: none;">Total <?= $tcslable ?></td>

                                            <td class="text-right" style="background: none;">
                                                <input type="number" step="any" name="totalInvoiceTDS" value="<?= number_format($total_tds, 2, '.', '') ?>" id="totalInvoiceTDS" class="form-control text-xs itemUnitPrice">
                                            </td>

                                        </tr>


                                        <tr class="itemTotals">

                                            <td colspan="9" class="font-bold" style="background: none; border: 0;">Total Amount</td>

                                            <td class="text-right font-bold" id="tdGrandTotal" style="background: none; border: 0;s"><?= number_format($grnDetails["grnTotalAmount"], 2) ?? 0 ?></td>

                                        </tr>

                                        <tr class="itemTotals">

                                            <td colspan="9" class="font-bold" style="background: none; border: 0;">Round - Off</td>

                                            <td class="text-right font-bold" id="" style="background: none; border: 0;">
                                                <div class="adjust-currency d-flex gap-3 justify-content-end">
                                                    <!-- <select id="round_sign" name="adjust_symbol" class="form-control text-center">
                                                        <option value="add">+</option>
                                                        <option value="sub">-</option>
                                                    </select> -->
                                                    <input type="number" step="any" name="roundOffGL" id="round_value" value="<?= $roundOffValue; ?>" class="form-control text-center">
                                                </div>

                                            </td>

                                        </tr>

                                        <tr class="itemTotals">

                                            <td colspan="9" class="font-bold" style="background: none; border: 0;">Adjusted Amount</td>

                                            <td class="text-right text-success font-bold" id="tdAdjustedTotal" style="background: none; border: 0;"><?= number_format($grnDetails["grnTotalAmount"], 2) ?? 0 ?></td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </div>



                        <?php

                        if ($getGrnDetailsObj["data"]["iv_status"] != 1) {

                        ?>



                        <?php

                        }

                        ?>



                </form>

            <?php



            } else {



                console($getGrnDetailsObj);
            }



            ?>



        </div>

    </section>

</div>





<script>
    $(document).ready(function() {

        let total_value = 0;
        var sign = "add";
        let roudoff = 0.0;

        function roundofftotal(total_value, sign, roudoff) {
            let final_value = 0;
            if (sign === "add") {
                final_value = total_value + roudoff;
            } else {
                final_value = total_value - roudoff;
            }

            $("#totalInvoiceTotal").val(final_value.toFixed(2));
            $("#tdAdjustedTotal").html(final_value.toFixed(2));

        }

        $(document).on("blur", "#round_value", function() {
            let roundValue = parseFloat($(this).val());
            let total_value = parseFloat($("#totalInvoiceTotal").val());
            var sign = $('#round_sign').val();
            roundofftotal(total_value, sign, roundValue);
        });


        $(document).on("keyup", ".itemCgst", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });
        $(document).on("keyup", ".itemSgst", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        $(document).on("keyup", ".itemIgst", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        $(document).on("keyup", ".itemTds", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });


        function calculateOneItemAmounts(rowNo) {
            let basicPrice = (parseFloat($(`#grnItemBaseInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemBaseInput_${rowNo}`).val()) : 0;
            let cgst = (parseFloat($(`#grnItemUnitCgstInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitCgstInput_${rowNo}`).val()) : 0;
            let sgst = (parseFloat($(`#grnItemUnitSgstInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitSgstInput_${rowNo}`).val()) : 0;
            let igst = (parseFloat($(`#grnItemUnitIgstInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitIgstInput_${rowNo}`).val()) : 0;
            let tds = (parseFloat($(`#grnItemUnitTdsInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitTdsInput_${rowNo}`).val()) : 0;

            let tds_value = basicPrice * (tds / 100);

            let totalItemPrice = basicPrice + cgst + sgst + igst - tds_value;

            console.log(totalItemPrice, cgst, sgst, igst, tds_value);

            $(`#grnItemUnitTDTotal_${rowNo}`).html(totalItemPrice.toFixed(2));
            $(`#grnItemTDSValue_${rowNo}`).val(tds_value.toFixed(2));



            calculateGrandTotalAmount();
        }

        function calculateGrandTotalAmount() {
            let totalAmount = 0;
            let grandSubTotalAmt = 0;
            let TotalCGSt = 0;
            let TotalSGSt = 0;
            let TotalIGSt = 0;
            let TotalTds = 0;


            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemCgst").each(function() {
                TotalCGSt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemSgst").each(function() {
                TotalSGSt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemIgst").each(function() {
                TotalIGSt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".ItemTotalTds").each(function() {
                TotalTds += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(`#totalInvoiceCGST`).val(TotalCGSt);
            $(`#totalInvoiceSGST`).val(TotalSGSt);
            $(`#totalInvoiceIGST`).val(TotalIGSt);
            $(`#totalInvoiceTDS`).val(TotalTds);

            let ToTalcgst = (parseFloat($(`#totalInvoiceCGST`).val()) > 0) ? parseFloat($(`#totalInvoiceCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalInvoiceSGST`).val()) > 0) ? parseFloat($(`#totalInvoiceSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalInvoiceIGST`).val()) > 0) ? parseFloat($(`#totalInvoiceIGST`).val()) : 0;
            let ToTalinvTds = (parseFloat($(`#totalInvoiceTDS`).val()) > 0) ? parseFloat($(`#totalInvoiceTDS`).val()) : 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - ToTalinvTds;


            $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
            $("#tdGrandTotal").html(totalAmount.toFixed(2));
        }

        $(document).on("keyup", "#totalInvoiceCGST", function() {
            let grandSubTotalAmt = 0;
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            let ToTalcgst = (parseFloat($(`#totalInvoiceCGST`).val()) > 0) ? parseFloat($(`#totalInvoiceCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalInvoiceSGST`).val()) > 0) ? parseFloat($(`#totalInvoiceSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalInvoiceIGST`).val()) > 0) ? parseFloat($(`#totalInvoiceIGST`).val()) : 0;
            let ToTalinvTds = (parseFloat($(`#totalInvoiceTDS`).val()) > 0) ? parseFloat($(`#totalInvoiceTDS`).val()) : 0;

            let totalAmount = 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - ToTalinvTds;

            $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
            $("#tdGrandTotal").html(totalAmount.toFixed(2));

        });

        $(document).on("keyup", "#totalInvoiceSGST", function() {
            let grandSubTotalAmt = 0;
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            let ToTalcgst = (parseFloat($(`#totalInvoiceCGST`).val()) > 0) ? parseFloat($(`#totalInvoiceCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalInvoiceSGST`).val()) > 0) ? parseFloat($(`#totalInvoiceSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalInvoiceIGST`).val()) > 0) ? parseFloat($(`#totalInvoiceIGST`).val()) : 0;
            let ToTalinvTds = (parseFloat($(`#totalInvoiceTDS`).val()) > 0) ? parseFloat($(`#totalInvoiceTDS`).val()) : 0;

            let totalAmount = 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - ToTalinvTds;

            $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
            $("#tdGrandTotal").html(totalAmount.toFixed(2));
        });

        $(document).on("keyup", "#totalInvoiceIGST", function() {
            let grandSubTotalAmt = 0;
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            let ToTalcgst = (parseFloat($(`#totalInvoiceCGST`).val()) > 0) ? parseFloat($(`#totalInvoiceCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalInvoiceSGST`).val()) > 0) ? parseFloat($(`#totalInvoiceSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalInvoiceIGST`).val()) > 0) ? parseFloat($(`#totalInvoiceIGST`).val()) : 0;
            let ToTalinvTds = (parseFloat($(`#totalInvoiceTDS`).val()) > 0) ? parseFloat($(`#totalInvoiceTDS`).val()) : 0;

            let totalAmount = 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - ToTalinvTds;

            $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
            $("#tdGrandTotal").html(totalAmount.toFixed(2));

        });





    });


    $(function() {

        $('#iframePreview').click(function() {

            if (!$('#iframe').length) {

                $('#iframeHolder').html('<iframe src="<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $grnDetails["vendorDocumentFile"]  ?? "" ?>" id="grnInvoicePreviewIfram" width="100%" height="100%" <p>This browser does not support PDF!</p></iframe>');

            }

        });

    });
</script>