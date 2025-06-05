<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-compliance-controller.php");
require_once("controller/gstr1-json-repositary-controller.php");
$queryParams = json_decode(base64_decode($_GET['action']));
if ($queryParams === null) {
    redirect("gstr1-concised-view.php");
}

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />


<style>
    section.gstr-1 {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gst-one-filter {
        left: 0;
        top: 0;
    }

    .gst-one-filter a.active {
        background-color: #003060;
        color: #fff;
    }

    .dataTables_scrollBody thead {
        visibility: hidden;
    }

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row {
        display: flex !important;
        align-items: center;
        justify-content: end;
    }

    /* div.dataTables_wrapper {
      overflow: hidden;
    } */

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(1),
    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(3) {
        padding: 10px 20px;
    }

    div.dataTables_wrapper div.dataTables_length select {
        width: 60% !important;
        appearance: none !important;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .dataTables_scroll {
        position: relative;
        margin-bottom: 10px;
    }

    .dataTables_scrollBody::-webkit-scrollbar {
        background-color: transparent;
        width: 0px;
        height: 0px;
        cursor: pointer;
    }

    .dataTables_scrollBody:hover::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dataTables_scrollBody:hover::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
    }


    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 10px;
    }

    div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
        border: 0;
    }

    .dataTables_filter {
        padding-right: 0 !important;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        padding: 0;
        border: 0;
    }

    .dt-top-container {
        display: flex;
        align-items: center;
        padding: 0 20px;
        gap: 20px;
        height: 60px;
    }

    .gst-action-center tr td {
        /* white-space: pre-line !important; */
        text-align: center !important;
    }

    .gst-action-center tr th {
        text-align: center !important;
    }

    .dataTables_length {
        margin-left: 4em;
        display: none;
    }

    div#datatable_filter {
        display: none !important;
    }

    a.btn.add-col.setting-menu.waves-effect.waves-light {
        position: absolute !important;
        display: flex;
        justify-content: space-between;
        top: 10px !important;
    }

    div.dataTables_wrapper div.dataTables_length label {
        margin-bottom: 0;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-left: 20px;
        position: relative;
        top: 0;
    }

    .dataTables_paginate {
        position: relative;
        right: 20px;
        bottom: 20px;
        margin-top: -15px;
    }

    .dt-center-in-div {
        display: block;
        /* order: 3; */
        margin-left: auto;
    }

    .dt-buttons.btn-group.flex-wrap button {
        background-color: #003060 !important;
        border-color: #003060 !important;
        border-radius: 7px !important;
    }

    /* .setting-row .col .btn.setting-menu {
      position: absolute !important;
      right: 255px;
      top: 10px;
    } */

    .dt-buttons.btn-group.flex-wrap {
        gap: 10px;
    }

    .dataTables_scrollBody {
        height: auto !important;
    }


    table.dataTable>thead .sorting:before,
    table.dataTable>thead .sorting:after,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_asc:after,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_desc:after,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_asc_disabled:after,
    table.dataTable>thead .sorting_desc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled:after {

        display: block !important;

    }


    select.fy-dropdown {
        position: absolute;
        max-width: 100px;
        top: 14px;
        left: 255px;
    }

    .daybook-filter-list.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-end;
        position: relative;
        top: 0px;
        left: 18px;
        margin: 15px 0;
        float: right;
    }

    .daybook-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .date-range-input {
        gap: 7px;
    }

    .date-range-input .form-input {
        width: 100%;
    }

    .daybook-tabs {
        flex-direction: row-reverse;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    ul.nav-preview {
        position: absolute;
        top: 15px;
        left: 100px;
        z-index: 9;
    }

    ul.nav-preview li .nav-link,
    ul.nav-preview li .nav-link:hover {
        display: flex;
        align-items: center;
        color: #000 !important;
    }

    ul.nav-preview li .nav-link.active {
        background-color: #fff;
        color: #000;
    }

    .nav-preview-content nav.details .nav-tabs {
        margin-top: 44px;
        background: #003060;
        padding: 5px 3px 0px;
    }

    .nav-preview-content nav.details .nav-tabs button {
        font-weight: 500;
        color: #fff;
    }

    .nav-preview-content nav.details .nav-tabs button.active {
        background: #dbe5ee;
        border: 0;
        color: #000;
    }


    .date-picker {
        width: 260px;
        height: auto;
        max-height: 50px;
        background: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s 0s ease-in-out;
    }

    .date-picker .input {
        width: 100%;
        height: 50px;
        font-size: 0;
        cursor: pointer;
    }

    .date-picker .input .result,
    .date-picker .input button {
        display: inline-block;
        vertical-align: top;
    }

    .date-picker .input .result {
        width: calc(100% - 50px);
        height: 50px;
        line-height: 50px;
        font-size: 16px;
        padding: 0 10px;
        color: grey;
        box-sizing: border-box;
    }

    .date-picker .input button {
        width: 50px;
        height: 50px;
        background-color: #8392A7;
        color: white;
        line-height: 50px;
        border: 0;
        font-size: 18px;
        padding: 0;
    }

    .date-picker .input button:hover {
        background-color: #68768A;
    }

    .date-picker .input button:focus {
        outline: 0;
    }

    .date-picker .calendar {
        position: relative;
        width: 100%;
        background: #fff;
        border-radius: 0px;
        overflow: hidden;
    }

    .date-picker .ui-datepicker-inline {
        position: relative;
        width: 100%;
    }

    .date-picker .ui-datepicker-header {
        height: 100%;
        line-height: 50px;
        background: #8392A7;
        color: #fff;
        margin-bottom: 10px;
    }

    .date-picker .ui-datepicker-prev,
    .date-picker .ui-datepicker-next {
        width: 20px;
        height: 20px;
        text-indent: 9999px;
        border: 2px solid #fff;
        border-radius: 100%;
        cursor: pointer;
        overflow: hidden;
        margin-top: 12px;
    }

    .date-picker .ui-datepicker-prev {
        float: left;
        margin-left: 12px;
    }

    .date-picker .ui-datepicker-prev:after {
        transform: rotate(45deg);
        margin: -43px 0px 0px 8px;
    }

    .date-picker .ui-datepicker-next {
        float: right;
        margin-right: 12px;
    }

    .date-picker .ui-datepicker-next:after {
        transform: rotate(-135deg);
        margin: -43px 0px 0px 6px;
    }

    .date-picker .ui-datepicker-prev:after,
    .date-picker .ui-datepicker-next:after {
        content: "";
        position: absolute;
        display: block;
        width: 4px;
        height: 4px;
        border-left: 2px solid #fff;
        border-bottom: 2px solid #fff;
    }

    .date-picker .ui-datepicker-prev:hover,
    .date-picker .ui-datepicker-next:hover,
    .date-picker .ui-datepicker-prev:hover:after,
    .date-picker .ui-datepicker-next:hover:after {
        border-color: #68768A;
    }

    .date-picker .ui-datepicker-title {
        text-align: center;
    }

    .date-picker .ui-datepicker-calendar {
        width: 100%;
        text-align: center;
    }

    .date-picker .ui-datepicker-calendar thead tr th span {
        display: block;
        width: 100%;
        color: #8392A7;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .date-picker .ui-state-default {
        display: block;
        text-decoration: none;
        color: #b5b5b5;
        line-height: 40px;
        font-size: 12px;
    }

    .date-picker .ui-state-default:hover {
        background: rgba(0, 0, 0, 0.02);
    }

    .date-picker .ui-state-highlight {
        color: #68768A;
    }

    .date-picker .ui-state-active {
        color: #68768A;
        background-color: rgba(131, 146, 167, 0.12);
        font-weight: 600;
    }

    .date-picker .ui-datepicker-unselectable .ui-state-default {
        color: #eee;
        border: 2px solid transparent;
    }

    .date-picker.open {
        max-height: 400px;
    }

    .date-picker.open .input button {
        background: #68768A;
    }


    @media (max-width: 769px) {
        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: absolute;
            top: -39px;
            right: 60px;
        }

        .dt-buttons.btn-group.flex-wrap button {
            max-width: 60px;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: -10px;
        }


    }

    @media (max-width :575px) {
        .dataTables_scrollFoot {
            position: absolute;
            top: 28px;
        }

        .dt-top-container {
            display: flex;
            align-items: baseline;
            padding: 0 20px;
            gap: 20px;
            flex-direction: column-reverse;
            flex-wrap: nowrap;
        }

        .dataTables_length {
            margin-left: 0;
            margin-bottom: 1em;
        }

        select.fy-dropdown {
            position: absolute;
            max-width: 125px;
            top: 155px;
            left: 189px;
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 164px !important;
        }

        .dt-center-in-div {
            margin: 3px auto;
        }

        div.dataTables_filter {
            right: 0;
            margin-top: 0;
            position: relative;
            right: -43px;
        }

        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: relative;
            top: 0;
            right: 0;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: 40px;
        }

        .dataTables_length label {
            font-size: 0;
        }
    }

    @media (max-width: 376px) {
        div.dataTables_wrapper div.dataTables_filter {
            margin-top: 0;
            padding-left: 0 !important;
        }
    }

    .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    table.defaultDataTable {
        width: 100% !important;
    }

    #startDate {
        max-width: 200px;
    }

    .datepicker-bg {
        background-color: #003060;
        color: #fff;
    }

    .gst1-tab-pane .dt-buttons.btn-group {
        position: absolute;
        top: 17px;
        right: 19px;
    }
</style>

<style>
  .dataTable thead {
    top: 0 !important;    
  }
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1 Previews <small>(<?= $queryParams->startDate ?> to <?= $queryParams->endDate ?>)</small></h4>
        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="gstr1-preview.php" class="btn active"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <?php if ($queryParams->isFiled == 0) { ?>
                    <a href="gstr1-filling.php?action=<?= base64_encode(json_encode($queryParams)) ?>" class="btn"><i class="fa fa-list mr-2"></i>Pending Filling</a>
                <?php } ?>
            </div>
            <div>
                <a href="gstr1-concised-view.php" class="btn waves-effect waves-light">Back</a>
            </div>
        </div>


        <div>
            <?php
            $period = $queryParams->period;
            $startDate = date("Y-m-d", strtotime($queryParams->startDate));
            $endDate = date("Y-m-d", strtotime($queryParams->endDate));

            $gstr1JsonRepoObj = new Gstr1JsonRepository($period, $startDate, $endDate);
            $jsonObj = $gstr1JsonRepoObj->generate();

            $b2bVourcherChat = 0;
            $b2bTaxableAmount = 0;
            $b2bCGST = 0;
            $b2bSGST = 0;
            $b2bIGST = 0;
            $b2bCESS = 0;
            $b2btotalTax = 0;
            $b2binvAmount = 0;
            //b2cl variables
            $b2clVourcherChat = 0;
            $b2clTaxableAmount = 0;
            $b2clCGST = 0;
            $b2clSGST = 0;
            $b2clIGST = 0;
            $b2clCESS = 0;
            $b2cltotalTax = 0;
            $b2clinvAmount = 0;
            //b2cs variables
            $b2csVourcherChat = 0;
            $b2csTaxableAmount = 0;
            $b2csCGST = 0;
            $b2csSGST = 0;
            $b2csIGST = 0;
            $b2csCESS = 0;
            $b2cstotalTax = 0;
            $b2csinvAmount = 0;
            //hsn variables
            $hsnVourcherChat = 0;
            $hsnTaxableAmount = 0;
            $hsnCGST = 0;
            $hsnSGST = 0;
            $hsnIGST = 0;
            $hsnCESS = 0;
            $hsntotalTax = 0;
            $hsninvAmount = 0;
            //cdnr variables
            $cdnrVourcherChat = 0;
            $crTotalItemtaxableAmt = 0;
            $crTotalItemCGSTAmt = 0;
            $crTotalItemSGSTAmt = 0;
            $crTotalItemIGSTAmt = 0;
            $crTotalItemCessAmt = 0;
            $cdnrtotalTax = 0;
            //cdnur variables
            $cdnurVourcherChat = 0;
            $crurItemtaxableAmt = 0;
            $crurItemCGSTAmt = 0;
            $crurItemSGSTAmt = 0;
            $crurItemIGSTAmt = 0;
            $crurItemCessAmt = 0;
            // exp variable
            $expVourchercount = 0;
            $exptaxableAmt = 0;
            $expItemIGSTAmt = 0;
            $expItemCessAmt = 0;

            ?>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                <ul class="nav nav-pills nav-preview mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-xs" id="pills-summary-tab" data-bs-toggle="pill" data-bs-target="#pills-summary" type="button" role="tab" aria-controls="pills-summary" aria-selected="true"><ion-icon name="document-text-outline" class="mr-2"></ion-icon>Summary</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-xs" id="pills-details-tab" data-bs-toggle="pill" data-bs-target="#pills-details" type="button" role="tab" aria-controls="pills-details" aria-selected="false"><ion-icon name="list-outline" class="mr-2"></ion-icon>Details</button>
                    </li>
                </ul>
                <div class="tab-content nav-preview-content pt-0" id="pills-tabContent">
                    <div class="tab-pane details fade" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab">
                        <div class="card">
                            <div class="card-body px-0">
                                <nav class="details">
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link text-xs active" id="nav-b2b-tab" data-bs-toggle="tab" data-bs-target="#navTabB2b" type="button" role="tab" aria-controls="nav-b2b" aria-selected="true">B2B</button>
                                        <button class="nav-link text-xs" id="nav-b2bcl-tab" data-bs-toggle="tab" data-bs-target="#navTabB2cl" type="button" role="tab" aria-controls="nav-b2bcl" aria-selected="false">B2CL</button>
                                        <button class="nav-link text-xs" id="nav-b2cs-tab" data-bs-toggle="tab" data-bs-target="#navTabB2cs" type="button" role="tab" aria-controls="nav-b2cs" aria-selected="false">B2CS</button>
                                        <button class="nav-link text-xs" id="nav-cdnr-tab" data-bs-toggle="tab" data-bs-target="#navTabCdnr" type="button" role="tab" aria-controls="nav-cdnr" aria-selected="false">CDNR</button>
                                        <button class="nav-link text-xs" id="nav-cdhur-tab" data-bs-toggle="tab" data-bs-target="#navTabCdhur" type="button" role="tab" aria-controls="nav-cdhur" aria-selected="false">CDHUR</button>
                                        <button class="nav-link text-xs" id="nav-exp-tab" data-bs-toggle="tab" data-bs-target="#navTabExp" type="button" role="tab" aria-controls="nav-exp" aria-selected="false">EXP</button>
                                        <!-- <button class="nav-link text-xs" id="nav-at-tab" data-bs-toggle="tab" data-bs-target="#navTabAt" type="button" role="tab" aria-controls="nav-at" aria-selected="false">at</button>
                                        <button class="nav-link text-xs" id="nav-atadj-tab" data-bs-toggle="tab" data-bs-target="#navTabAtadj" type="button" role="tab" aria-controls="nav-atadj" aria-selected="false">atadj</button>
                                        <button class="nav-link text-xs" id="nav-exemp-tab" data-bs-toggle="tab" data-bs-target="#navTabExemp" type="button" role="tab" aria-controls="nav-exemp" aria-selected="false">exemp</button> -->
                                        <button class="nav-link text-xs" id="nav-hsn-tab" data-bs-toggle="tab" data-bs-target="#navTabHsn" type="button" role="tab" aria-controls="nav-hsn" aria-selected="false">HSN</button>
                                        <button class="nav-link text-xs" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#navTabDocs" type="button" role="tab" aria-controls="nav-docs" aria-selected="false">DOCS</button>
                                    </div>
                                </nav>
                                <div class="tab-content pt-2" id="nav-tabContent" style="overflow: auto;">
                                    <div class="tab-pane fade gst1-tab-pane show active" id="navTabB2b" role="tabpanel" aria-labelledby="nav-b2b-tab">
                                        <table id="gstr1b2bTable" class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>GSTIN/UIN of Recipient</th>
                                                    <th>Receiver Name</th>
                                                    <th>Invoice Number</th>
                                                    <th>Invoice date</th>
                                                    <th>Invoice Value</th>
                                                    <th>Place Of Supply</th>
                                                    <th>Reverse Charge</th>
                                                    <th>Applicable % of Tax Rate</th>
                                                    <th>Invoice Type</th>
                                                    <th>E-Commerce GSTIN</th>
                                                    <th>Rate</th>
                                                    <th>Taxable Value</th>
                                                    <th>CGST</th>
                                                    <th>SGST</th>
                                                    <th>IGST</th>
                                                    <th>Cess Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($jsonObj['b2b'] as $invoiceItems) {
                                                    $getReceiverObj = queryGet("SELECT `trade_name` FROM `erp_customer` WHERE customer_gstin='" . $invoiceItems["ctin"] . "'  ")['data'];
                                                    foreach ($invoiceItems['inv'] as $rate => $rateWiseItem) {
                                                        $b2bVourcherChat++;
                                                        $oneInvAndItem = $rateWiseItem[0];
                                                        $rateWiseTaxableVal = 0;
                                                        $b2binvAmount += $rateWiseItem['val'];
                                                        foreach ($rateWiseItem['itms'] as $oneItem) {
                                                            $b2bCGST += $oneItem['itm_det']["camt"];
                                                            $b2bSGST += $oneItem['itm_det']["samt"];
                                                            $b2bIGST += $oneItem['itm_det']["iamt"];
                                                            $b2bCESS += decimalValuePreview($oneItem['itm_det']["csamt"]);
                                                            $b2bTaxableAmount += ($oneItem['itm_det']["txval"]);
                                                            $getReceiverName = $getReceiverObj['trade_name'];
                                                ?>
                                                            <tr>
                                                                <td><?= $invoiceItems["ctin"] ?></td>
                                                                <td><?= $getReceiverName ?></td>
                                                                <td><?= $rateWiseItem["inum"] ?></td>
                                                                <td><?= $rateWiseItem["idt"] ?></td>
                                                                <td class="text-right"><?= decimalValuePreview($rateWiseItem["val"]) ?></td>
                                                                <td class="text-right"><?= getStateDetail($rateWiseItem['pos'])['data']['gstStateName'] ?></td>
                                                                <td class="text-right"><?= $rateWiseItem['rchrg'] ?></td>
                                                                <td><?= $oneItem['itm_det']['rt'] ?></td>
                                                                <td><?= $rateWiseItem['inv_typ'] ?></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-right"><?= decimalValuePreview($oneItem['itm_det']["txval"]) ?></td>
                                                                <td class="text-right"><?= decimalValuePreview($oneItem['itm_det']["camt"]) ?></td>
                                                                <td class="text-right"><?= decimalValuePreview($oneItem['itm_det']["samt"]) ?></td>
                                                                <td class="text-right"><?= decimalValuePreview($oneItem['itm_det']["iamt"]) ?></td>
                                                                <td class="text-right"><?= decimalValuePreview($oneItem['itm_det']['csamt']) ?></td>
                                                            </tr>
                                                <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabB2cl" role="tabpanel" aria-labelledby="nav-b2bcl-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>Supply Type</th>
                                                    <th>Type </th>
                                                    <th>Rate(%)</th>
                                                    <th>Place Of Supply</th>
                                                    <th>Taxable Value</th>
                                                    <th>CGST</th>
                                                    <th>SGST</th>
                                                    <th>Cess Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $b2clVourcherChat = count($jsonObj["b2cl"]);
                                                foreach ($jsonObj["b2cl"] as $invoiceItems) {
                                                    $b2clSGST += $invoiceItems["samt"];
                                                    $b2clCGST += $invoiceItems["camt"];
                                                    $b2clIGST += $invoiceItems["iamt"];
                                                    $b2clCESS += $invoiceItems["csamt"];
                                                    $b2clTaxableAmount += $invoiceItems["txval"];
                                                ?>
                                                    <tr>
                                                        <td><?= $invoiceItems["sply_ty"] ?></td>
                                                        <td><?= $invoiceItems["typ"] ?></td>
                                                        <td><?= $invoiceItems["rt"] ?></td>
                                                        <td><?= getStateDetail($invoiceItems["pos"])['data']['gstStateName'] ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["txval"]) ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["camt"]) ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["samt"]) ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["csamt"]) ?></td>
                                                    </tr>
                                                <?php
                                                }

                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabB2cs" role="tabpanel" aria-labelledby="nav-b2cs-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>Supply Type</th>
                                                    <th>Type </th>
                                                    <th>Rate(%)</th>
                                                    <th>Place Of Supply</th>
                                                    <th>Taxable Value</th>
                                                    <th>CGST</th>
                                                    <th>SGST</th>
                                                    <th>IGST</th>
                                                    <th>Cess Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // console($jsonObj["b2cs"]);
                                                $b2csVourcherChat = count($jsonObj["b2cs"]);
                                                foreach ($jsonObj["b2cs"] as $invoiceItems) {
                                                    $b2csSGST += $invoiceItems["samt"];
                                                    $b2csCGST += $invoiceItems["camt"];
                                                    $b2csIGST += $invoiceItems["iamt"];
                                                    $b2csCESS += $invoiceItems["csamt"];
                                                    $b2csTaxableAmount += $invoiceItems["txval"];
                                                ?>
                                                    <tr>
                                                        <td><?= $invoiceItems["sply_ty"] ?></td>
                                                        <td><?= $invoiceItems["typ"] ?></td>
                                                        <td><?= $invoiceItems["rt"] ?></td>
                                                        <td><?= getStateDetail($invoiceItems["pos"])['data']['gstStateName'] ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["txval"]) ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["camt"]) ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["samt"]) ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["iamt"]) ?></td>
                                                        <td><?= decimalValuePreview($invoiceItems["csamt"]) ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabCdnr" role="tabpanel" aria-labelledby="nav-cdnr-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>GSTIN/UIN of Recipient</th>
                                                    <!-- <th>Receiver Code</th> -->
                                                    <th>CR/DR Number</th>
                                                    <th>CR/DR Date</th>
                                                    <th>Value</th>
                                                    <th>Place Of Supply</th>
                                                    <th>Reverse Charge</th>
                                                    <th>Invoice Type</th>
                                                    <th>Taxable Value</th>
                                                    <th>CGST</th>
                                                    <th>SGST</th>
                                                    <th>IGST</th>
                                                    <th>CESS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($jsonObj['cdnr'] as $crDrNotesRegistered) {
                                                    foreach ($crDrNotesRegistered['nt'] as $oneCrNote) {
                                                        $cdnrVourcherChat++;
                                                        $crItemtaxableAmt = 0;
                                                        $crItemCGSTAmt = 0;
                                                        $crItemSGSTAmt = 0;
                                                        $crItemIGSTAmt = 0;
                                                        $crItemCessAmt = 0;
                                                        foreach ($oneCrNote['itms'] as $onecrItem) {
                                                            $crItemtaxableAmt += $onecrItem['itm_det']['txval'];
                                                            $crItemCGSTAmt += $onecrItem['itm_det']['camt'];
                                                            $crItemSGSTAmt += $onecrItem['itm_det']['samt'];
                                                            $crItemIGSTAmt += $onecrItem['itm_det']['iamt'];
                                                            $crItemCessAmt += $onecrItem['itm_det']['csamt'];
                                                        }
                                                        $crTotalItemtaxableAmt += $crItemtaxableAmt;
                                                        $crTotalItemCGSTAmt += $crItemCGSTAmt;
                                                        $crTotalItemSGSTAmt += $crItemSGSTAmt;
                                                        $crTotalItemIGSTAmt += $crItemIGSTAmt;
                                                        $crTotalItemCessAmt += $crItemCessAmt;
                                                ?>
                                                        <tr>
                                                            <td><?= $crDrNotesRegistered["ctin"] ?></td>
                                                            <td><?= $oneCrNote["nt_num"] ?></td>
                                                            <td><?= $oneCrNote["nt_dt"] ?></td>
                                                            <td><?= decimalValuePreview($oneCrNote["val"]) ?></td>
                                                            <td><?= getStateDetail($oneCrNote["pos"])['data']['gstStateName'] ?></td>
                                                            <td><?= $oneCrNote["rchrg"] ?></td>
                                                            <td><?= $oneCrNote["inv_typ"] ?></td>
                                                            <td><?= decimalValuePreview($crItemtaxableAmt) ?></td>
                                                            <td><?= decimalValuePreview($crItemCGSTAmt) ?></td>
                                                            <td><?= decimalValuePreview($crItemSGSTAmt) ?></td>
                                                            <td><?= decimalValuePreview($crItemIGSTAmt) ?></td>
                                                            <td><?= decimalValuePreview($crItemCessAmt) ?></td>
                                                        </tr>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabCdhur" role="tabpanel" aria-labelledby="nav-cdhur-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>CR/DR Number</th>
                                                    <th>CR/DR Date</th>
                                                    <th>Value</th>
                                                    <th>Place Of Supply</th>
                                                    <th>Reverse Charge</th>
                                                    <th>Invoice Type</th>
                                                    <th>Taxable Value</th>
                                                    <th>CGST</th>
                                                    <th>SGST</th>
                                                    <th>IGST</th>
                                                    <th>CESS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($jsonObj['cdnur'] as $oneCrNoteUr) {
                                                    // foreach ($crDrNotesRegistered[''] as $oneCrNote) {
                                                    $cdnurVourcherChat++;

                                                    foreach ($oneCrNoteUr['itms'] as $onecrItem) {
                                                        $crurItemtaxableAmt += $onecrItem['itm_det']['txval'];
                                                        $crurItemCGSTAmt += $onecrItem['itm_det']['camt'];
                                                        $crurItemSGSTAmt += $onecrItem['itm_det']['samt'];
                                                        $crurItemIGSTAmt += $onecrItem['itm_det']['iamt'];
                                                        $crurItemCessAmt += $onecrItem['itm_det']['csamt'];
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= $oneCrNoteUr['nt_num'] ?></td>
                                                        <td><?= $oneCrNoteUr['nt_dt'] ?></td>
                                                        <td><?= decimalValuePreview($oneCrNoteUr["val"]) ?></td>
                                                        <td><?= getStateDetail($oneCrNoteUr["pos"])['data']['gstStateName'] ?></td>
                                                        <td><?= $oneCrNoteUr["rchrg"] ?></td>
                                                        <td><?= $oneCrNoteUr["inv_typ"] ?></td>
                                                        <td><?= decimalValuePreview($crurItemtaxableAmt) ?></td>
                                                        <td><?= decimalValuePreview($crurItemCGSTAmt) ?></td>
                                                        <td><?= decimalValuePreview($crurItemSGSTAmt) ?></td>
                                                        <td><?= decimalValuePreview($crurItemIGSTAmt) ?></td>
                                                        <td><?= decimalValuePreview($crurItemCessAmt) ?></td>
                                                    </tr>
                                                <?php
                                                    // }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabExp" role="tabpanel" aria-labelledby="nav-exp-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Inv No</th>
                                                    <th>Inv Date</th>
                                                    <th>Invoice Amount</th>
                                                    <th>Total Taxable Amt</th>
                                                    <th>Total IGST</th>
                                                    <th>Total CESS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $exptaxableAmount = 0;
                                                $expTotalIGST = 0;
                                                $expTotalCESS = 0;
                                                foreach ($jsonObj['exp'] as $oneExp) {
                                                    foreach ($oneExp['inv'] as $oneExpInv) {
                                                        $expVourchercount += 1;

                                                        // Reset the sum variables for each invoice
                                                        $expItemtaxableAmt = 0;
                                                        $expItemIGSTAmt = 0;
                                                        $expItemCessAmt = 0;

                                                        // Sum the values for the current invoice
                                                        foreach ($oneExpInv['itms'] as $oneItem) {
                                                            $expItemtaxableAmt += $oneItem['txval'];
                                                            $expItemIGSTAmt += $oneItem['iamt'];
                                                            $expItemCessAmt += $oneItem['csamt'];
                                                            $exptaxableAmount += $expItemtaxableAmt;
                                                            $expTotalIGST += $expItemIGSTAmt;
                                                            $expTotalCESS += $expItemCessAmt;
                                                        }
                                                ?>
                                                        <tr>
                                                            <td><?= $oneExp['exp_typ'] ?></td>
                                                            <td><?= $oneExpInv['inum'] ?></td>
                                                            <td><?= $oneExpInv["idt"] ?></td>
                                                            <td><?= decimalValuePreview($oneExpInv['val']) ?></td>
                                                            <td><?= decimalValuePreview($expItemtaxableAmt) ?></td>
                                                            <td><?= decimalValuePreview($expItemIGSTAmt) ?></td>
                                                            <td><?= decimalValuePreview($expItemCessAmt) ?></td>
                                                        </tr>
                                                <?php
                                                    }
                                                }

                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabAt" role="tabpanel" aria-labelledby="nav-at-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>GSTIN/UIN of Recipient</th>
                                                    <th>Receiver Name</th>
                                                    <th>Invoice Number</th>
                                                    <th>Invoice Date</th>
                                                    <th>Invoice Value</th>
                                                    <th>Place Of Supply</th>
                                                    <th>Reverse Ch</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabAtadj" role="tabpanel" aria-labelledby="nav-atadj-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>GSTIN/UIN of Recipient</th>
                                                    <th>Receiver Name</th>
                                                    <th>Invoice Number</th>
                                                    <th>Invoice Date</th>
                                                    <th>Invoice Value</th>
                                                    <th>Place Of Supply</th>
                                                    <th>Reverse Ch</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabExemp" role="tabpanel" aria-labelledby="nav-exemp-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>GSTIN/UIN of Recipient</th>
                                                    <th>Receiver Name</th>
                                                    <th>Invoice Number</th>
                                                    <th>Invoice Date</th>
                                                    <th>Invoice Value</th>
                                                    <th>Place Of Supply</th>
                                                    <th>Reverse Ch</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabHsn" role="tabpanel" aria-labelledby="nav-hsn-tab">
                                        <table class="table table-hover gstr1-table-export">
                                            <thead>
                                                <tr>
                                                    <th>HSN</th>
                                                    <th>Description</th>
                                                    <th>UQC</th>
                                                    <th>Total Value</th>
                                                    <th>Total Quantity</th>
                                                    <th>Taxable Value</th>
                                                    <th>Rate</th>
                                                    <th>Integrated Tax Amount</th>
                                                    <th>Central Tax Amount</th>
                                                    <th>State/UT Tax Amount</th>
                                                    <th>Cess Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $hsnItemtotal_value = 0;
                                                foreach ($jsonObj['hsn']["data"] as $oneRow) {
                                                    $hsnVourcherChat++;
                                                    $hsnSGST += $oneRow["samt"];
                                                    $hsnCGST += $oneRow["camt"];
                                                    $hsnIGST += $oneRow["iamt"];
                                                    $hsnCESS += $oneRow["csamt"];
                                                    $hsnTaxableAmount += $oneRow["txval"];
                                                    $hsnItemtotal_value = $oneRow["txval"] + $oneRow["iamt"] + $oneRow["camt"] + $oneRow["samt"] + $oneRow["csamt"];
                                                ?>
                                                    <tr>
                                                        <td><?= $oneRow["hsn_sc"] ?></td>
                                                        <td><?= $oneRow["desc"] ?? "" ?></td>
                                                        <td><?= $oneRow["uqc"] ?? "NA" ?></td>
                                                        <td class="text-right"><?= decimalValuePreview($hsnItemtotal_value) ?></td>
                                                        <td><?= $oneRow["qty"] ?? "" ?></td>
                                                        <td class="text-right"><?= decimalValuePreview($oneRow["txval"]) ?></td>
                                                        <td class="text-right"><?= decimalValuePreview($oneRow["rt"]) ?></td>
                                                        <td class="text-right"><?= decimalValuePreview($oneRow["iamt"]) ?></td>
                                                        <td class="text-right"><?= decimalValuePreview($oneRow["camt"]) ?></td>
                                                        <td class="text-right"><?= decimalValuePreview($oneRow["samt"]) ?></td>
                                                        <td class="text-right"><?= decimalValuePreview($oneRow["csamt"]) ?></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade gst1-tab-pane" id="navTabDocs" role="tabpanel" aria-labelledby="nav-docs-tab">
                                        <table class="table table-hover gstr1-table-export" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Sl</th>
                                                    <th rowspan="2">Document Type</th>
                                                    <th colspan="5">Docs</th>
                                                </tr>
                                                <tr>
                                                    <th style="border: 1px solid white">From</th>
                                                    <th style="border: 1px solid white">To</th>
                                                    <th style="border: 1px solid white">Total Doc</th>
                                                    <th style="border: 1px solid white">Cancel Doc</th>
                                                    <th style="border: 1px solid white">Net Issued</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($jsonObj["doc_issue"]["doc_det"] as $oneRowKey => $oneRow) {
                                                ?>
                                                    <tr>
                                                        <td rowspan="<?= count($oneRow['docs']) ?>"><?= $oneRowKey + 1 ?></td>
                                                        <td rowspan="<?= count($oneRow['docs']) ?>"><?= $oneRow["doc_typ"] ?></td>
                                                        <?php
                                                        foreach ($oneRow['docs'] as $onedata) {
                                                        ?>
                                                            <td><?= $onedata["from"] ?></td>
                                                            <td><?= $onedata["to"] ?></td>
                                                            <td><?= $onedata["totnum"] ?></td>
                                                            <td><?= $onedata["cancel"] ?></td>
                                                            <td><?= $onedata["net_issue"] ?></td>
                                                    </tr>
                                                <?php
                                                        }
                                                ?>
                                            <?php
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane summary fade show active" id="pills-summary" role="tabpanel" aria-labelledby="pills-summary-tab">
                        <table id="datatable" width="100" class="table table-hover defaultDataTable gst-action-center">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Period</th>
                                    <th>Voucher Count</th>
                                    <th>Taxable Amount</th>
                                    <th>CGST</th>
                                    <th>SGST</th>
                                    <th>IGST</th>
                                    <th>CESS</th>
                                    <th>Total Tax</th>
                                    <th>Invoice Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>B2B Invoices</td>
                                    <td><?= $b2bVourcherChat  ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2bTaxableAmount) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2bCGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2bSGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2bIGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2bCESS) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2bCGST + $b2bSGST + $b2bIGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2binvAmount) ?></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>B2CL Invoices</td>
                                    <td><?= $b2clVourcherChat  ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2clTaxableAmount) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2clCGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2clSGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2clCGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2clCESS) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2clIGST + $b2clCGST + $b2clCGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2clinvAmount) ?></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>B2CS Invoices</td>
                                    <td><?= $b2csVourcherChat ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2csTaxableAmount) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2csCGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2csSGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2csIGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2csCESS) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2csSGST + $b2csCGST + $b2csIGST + $b2csCESS) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($b2csSGST + $b2csCGST + $b2csIGST + $b2csCESS + $b2csTaxableAmount) ?></td>

                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Credit and Debit Note </td>
                                    <td><?= $cdnrVourcherChat  ?></td>
                                    <td class="text-right"><?= decimalValuePreview($crItemtaxableAmt) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($crItemCGSTAmt) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($crItemIGSTAmt) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($crItemSGSTAmt) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($crItemCessAmt) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($crItemCGSTAmt + $crItemSGSTAmt + $crItemIGSTAmt + $crItemCessAmt) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($crItemCGSTAmt + $crItemSGSTAmt + $crItemIGSTAmt + $crItemCessAmt + $crItemtaxableAmt) ?></td>

                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>CN/DN for Unregistered Taxpayers </td>
                                    <td><?= $cdnurVourcherChat ?></td>
                                    <td class="text-right"><?= $cdnurVourcherChat ?></td>
                                    <td class="text-right"><?= ($crurItemtaxableAmt) ?></td>
                                    <td class="text-right"><?= ($crurItemCGSTAmt) ?></td>
                                    <td class="text-right"><?= ($crurItemSGSTAmt) ?></td>

                                    <td class="text-right"><?= ($crurItemIGSTAmt) ?></td>
                                    <td class="text-right"><?= ($crurItemCessAmt) ?></td>
                                    <td class="text-right">0</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Exports </td>
                                    <td><?= $expVourchercount ?></td>
                                    <td class="text-right"><?= decimalValuePreview($exptaxableAmount)  ?></td>
                                    <td class="text-right"><?= decimalValuePreview(0) ?></td>
                                    <td class="text-right"><?= decimalValuePreview(0) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($expTotalIGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($expTotalCESS) ?></td>
                                    <td class="text-right"><?= decimalValuePreview(0) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($exptaxableAmount + $expTotalIGST + $expTotalCESS) ?></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Hsn summary details</td>
                                    <td><?= $hsnVourcherChat ?></td>
                                    <td class="text-right"><?= decimalValuePreview($hsnTaxableAmount) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($hsnCGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($hsnSGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($hsnIGST) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($hsnCESS) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($hsnCGST + $hsnSGST + $hsnIGST + $hsnCESS) ?></td>
                                    <td class="text-right"><?= decimalValuePreview($hsnCGST + $hsnSGST + $hsnIGST + $hsnCESS + $hsnTaxableAmount) ?></td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function() {
        $(function() {
            $('#startDate').datepicker({
                format: 'dd/mm/yyyy'
            });
        });
    });
</script>
<script>
    $(document).ready(function() {

        $('.gstr1-table-export').each(function() {

            $(this).DataTable({
                dom: 'Bfrtip',
                searching: false,
                ordering: false,
                buttons: [{
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    title: function() {
                        return getActiveTabTitle();
                    }
                }]
            });
        });

        function getActiveTabTitle() {
            const activeTab = $('.nav-link.active');
            return activeTab.data('title') || activeTab.text();
        }
    });
</script>
<script>
    $(document).ready(function() {
        var jsonData = JSON.parse(`<?= json_encode($jsonObj, JSON_PRETTY_PRINT) ?>`);
        console.log(jsonData);
        var columnSl = 0;
        var table = $("#datatable").DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            "ordering": false,
            buttons: [{
                text: 'Export to JSON',
                action: function(e, dt, button, config) {
                    var data = dt.buttons.exportData();
                    $.fn.dataTable.fileSave(
                        new Blob([JSON.stringify(jsonData)]),
                        'GSTR-1 Export <?= $gstr1ReturnPeriod ?>.json'
                    );
                }
            }]
        });
    });
</script>
<?php
require_once("../common/footer.php");
?>