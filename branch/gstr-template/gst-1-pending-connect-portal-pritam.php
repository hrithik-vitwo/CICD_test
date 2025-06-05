<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

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
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1</h4>
        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="./gst-1-action-center-preview.php" class="btn active"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <a href="./gst-1-pending-connect-portal.php" class="btn"><i class="fa fa-list mr-2"></i>Pending Filling</a>
            </div>
            <div class="input-group date" id="startDate">
                <span class="input-group-addon input-group-text datepicker-bg"><ion-icon name="calendar"></ion-icon>
                </span>
                <input type="text" class="form-control border px-5" name="startDate" placeholder="dd/mm/yyyy" />
            </div>
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
                    <div class="tab-pane summary fade show active" id="pills-summary" role="tabpanel" aria-labelledby="pills-summary-tab">
                        <table id="datatable" width="100" class="table table-hover defaultDataTable gst-action-center">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Period</th>
                                    <th>Voucher Court</th>
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
                                    <td>B2B Invoices 4A</td>
                                    <td>2</td>
                                    <td class="text-right">750.00</td>
                                    <td class="text-right">67.50</td>
                                    <td class="text-right">0.00</td>
                                    <td></td>
                                    <td class="text-right">135.00</td>
                                    <td class="text-right">885.00</td>
                                    <td class="text-right">885.00</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>B2B Invoices 4A</td>
                                    <td>2</td>
                                    <td class="text-right">750.00</td>
                                    <td class="text-right">67.50</td>
                                    <td class="text-right">0.00</td>
                                    <td></td>
                                    <td class="text-right">135.00</td>
                                    <td class="text-right">885.00</td>
                                    <td class="text-right">885.00</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>B2B Invoices 4A</td>
                                    <td>2</td>
                                    <td class="text-right">750.00</td>
                                    <td class="text-right">67.50</td>
                                    <td class="text-right">0.00</td>
                                    <td></td>
                                    <td class="text-right">135.00</td>
                                    <td class="text-right">885.00</td>
                                    <td class="text-right">885.00</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>B2B Invoices 4A</td>
                                    <td>2</td>
                                    <td class="text-right">750.00</td>
                                    <td class="text-right">67.50</td>
                                    <td class="text-right">0.00</td>
                                    <td></td>
                                    <td class="text-right">135.00</td>
                                    <td class="text-right">885.00</td>
                                    <td class="text-right">885.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane details fade" id="pills-details" role="tabpanel" aria-labelledby="pills-details-tab">
                        <div class="card">
                            <div class="card-body px-0">
                                <nav class="details">
                                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                        <button class="nav-link text-xs active" id="nav-b2b-tab" data-bs-toggle="tab" data-bs-target="#navTabB2b" type="button" role="tab" aria-controls="nav-b2b" aria-selected="true">b2b</button>
                                        <button class="nav-link text-xs" id="nav-b2brepeat-tab" data-bs-toggle="tab" data-bs-target="#navTabB2cl" type="button" role="tab" aria-controls="nav-b2brepeat" aria-selected="false">b2cl</button>
                                        <button class="nav-link text-xs" id="nav-b2cs-tab" data-bs-toggle="tab" data-bs-target="#navTabB2cs" type="button" role="tab" aria-controls="nav-b2cs" aria-selected="false">b2cs</button>
                                        <button class="nav-link text-xs" id="nav-cdnr-tab" data-bs-toggle="tab" data-bs-target="#navTabCdnr" type="button" role="tab" aria-controls="nav-cdnr" aria-selected="false">cdnr</button>
                                        <button class="nav-link text-xs" id="nav-cdhur-tab" data-bs-toggle="tab" data-bs-target="#navTabCdhur" type="button" role="tab" aria-controls="nav-cdhur" aria-selected="false">cdhur</button>
                                        <button class="nav-link text-xs" id="nav-exp-tab" data-bs-toggle="tab" data-bs-target="#navTabExp" type="button" role="tab" aria-controls="nav-exp" aria-selected="false">exp</button>
                                        <button class="nav-link text-xs" id="nav-at-tab" data-bs-toggle="tab" data-bs-target="#navTabAt" type="button" role="tab" aria-controls="nav-at" aria-selected="false">at</button>
                                        <button class="nav-link text-xs" id="nav-atadj-tab" data-bs-toggle="tab" data-bs-target="#navTabAtadj" type="button" role="tab" aria-controls="nav-atadj" aria-selected="false">atadj</button>
                                        <button class="nav-link text-xs" id="nav-exemp-tab" data-bs-toggle="tab" data-bs-target="#navTabExemp" type="button" role="tab" aria-controls="nav-exemp" aria-selected="false">exemp</button>
                                        <button class="nav-link text-xs" id="nav-hsn-tab" data-bs-toggle="tab" data-bs-target="#navTabHsn" type="button" role="tab" aria-controls="nav-hsn" aria-selected="false">hsn</button>
                                        <button class="nav-link text-xs" id="nav-docs-tab" data-bs-toggle="tab" data-bs-target="#navTabDocs" type="button" role="tab" aria-controls="nav-docs" aria-selected="false">docs</button>
                                    </div>
                                </nav>
                                <div class="tab-content pt-2" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="navTabB2b" role="tabpanel" aria-labelledby="nav-b2b-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabB2cl" role="tabpanel" aria-labelledby="nav-b2brepeat-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabB2cs" role="tabpanel" aria-labelledby="nav-b2cs-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabCdnr" role="tabpanel" aria-labelledby="nav-cdnr-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabCdhur" role="tabpanel" aria-labelledby="nav-cdhur-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabExp" role="tabpanel" aria-labelledby="nav-exp-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabAt" role="tabpanel" aria-labelledby="nav-at-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabAtadj" role="tabpanel" aria-labelledby="nav-atadj-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabExemp" role="tabpanel" aria-labelledby="nav-exemp-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabHsn" role="tabpanel" aria-labelledby="nav-hsn-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="navTabDocs" role="tabpanel" aria-labelledby="nav-docs-tab">
                                        <table class="table table-hover">
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
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>10ADFFB56939M23</td>
                                                    <td></td>
                                                    <td>V/23-24/001</td>
                                                    <td>17-08-2023</td>
                                                    <td>590.00</td>
                                                    <td>Regular</td>
                                                    <td></td>
                                                </tr>
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

        <div class="modal" id="myModal2">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Table Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                        <div class="modal-body" style="max-height: 450px;">
                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                            <input type="hidden" name="pageTableName" value="ERP_ACC_JOURNAL" />
                            <div class="modal-body">
                                <div id="dropdownframe"></div>
                                <div id="main2">
                                    <div class="checkAlltd d-flex gap-2 mb-2">
                                        <input type="checkbox" class="grand-checkbox" value="" />
                                        <p class="text-xs font-bold">Check All</p>
                                    </div>
                                    <?php $p = 1; ?>
                                    <table class="colomnTable">
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Sl</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Period</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Voucher Court</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Taxable Amount</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="<?php echo $p; ?>" />
                                                CGST</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?php echo $p; ?>" />
                                                SGST</td>
                                        </tr>

                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="<?php echo $p; ?>" />
                                                IGST</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                                CESS</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="<?php echo $p; ?>" />
                                                Total Tax</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="<?php echo $p; ?>" />
                                                Invoice Amount</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
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


        // DataTable
        var columnSl = 0;
        var table = $("#datatable").DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            buttons: [{
                text: 'Export to JSON',
                action: function(e, dt, button, config) {
                    var data = dt.buttons.exportData();

                    $.fn.dataTable.fileSave(
                        new Blob([JSON.stringify(data)]),
                        'Export.json'
                    );
                }
            }],
            "lengthMenu": [
                [1000, 5000, 10000, -1],
                [1000, 5000, 10000, 'All'],
            ],
            "scrollY": 200,
            "scrollX": true,
            "ordering": false,
        });


    });
</script>



<?php
require_once("../common/footer.php");
?>