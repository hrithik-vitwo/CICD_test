<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

require_once("../../app/v1/functions/branch/func-compliance-controller.php");
require_once("controller/gstr1-view-data.controller.php");
require_once("controller/gstr1-json-data.controller.php");
require_once("controller/gstr1-file.controller.php");


?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">


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
    table.dataTable>thead .sorting::after,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_asc::after,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_desc::after,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_asc_disabled::after,
    table.dataTable>thead .sorting_desc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled::after {

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
        margin-top: 5em;
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

    .date-picker .ui-datepicker-prev::after {
        transform: rotate(45deg);
        margin: -43px 0px 0px 8px;
    }

    .date-picker .ui-datepicker-next {
        float: right;
        margin-right: 12px;
    }

    .date-picker .ui-datepicker-next::after {
        transform: rotate(-135deg);
        margin: -43px 0px 0px 6px;
    }

    .date-picker .ui-datepicker-prev::after,
    .date-picker .ui-datepicker-next::after {
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
    .date-picker .ui-datepicker-prev:hover::after,
    .date-picker .ui-datepicker-next:hover::after {
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


    .proceedToFile {
        display: grid;
        align-items: center;
        place-content: center;
        justify-items: center;
        gap: 17px;
        height: 85%;
    }

    .proceedToFile img {
        max-width: 150px;
        margin: 20px auto;
    }

    .proceedToFile .text {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-direction: column;
    }

    .otp-inputs input {
        width: 30px;
        padding: 5px;
        text-align: center;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= BRANCH_URL ?>gstr1/gst1-report-graphical.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>GSTR1</a></li>
            <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>Filling Pending <?= $branch_gstin_file_frequency != "" ? "(" . date("F, Y", strtotime($gstr1ReturnPeriod)) . " - " . strtoupper($branch_gstin_file_frequency) . ")" : "" ?></a></li>
            <li class="back-button">
                <a href="gst1-preview.php<?= isset($_GET["period"]) ? '?period=' . $_GET["period"] : '' ?>">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
        <!-- <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1</h4> -->
        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="./gst1-preview.php<?= isset($_GET["period"]) ? '?period=' . $_GET["period"] : '' ?>" class="btn"><i class="fas fa-chart-bar mr-2"></i>Preview</a>
                <a href="./gst1-connect-portal.php<?= isset($_GET["period"]) ? '?period=' . $_GET["period"] : '' ?>" class="btn active"><i class="fa fa-list mr-2"></i>Pending Filling</a>
            </div>
        </div>

        <div class="card bg-light">
            <div class="card-header p-3 rounded">
                <h3 class="text-sm text-white mb-0 pl-3">Pending Filling</h3>
            </div>
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-8 col-sm-8 col-sm-8">
                        <h4 class="text-sm font-bold m-4">
                            Connect Portal
                        </h4>
                        <?php
                        require_once(BASE_DIR . "branch/gstr/auth-component-new.php");
                        $authObj = $authGstinPortalObj->checkAuth();
                        if ($authObj["status"] == "success") {
                            redirect(BRANCH_URL . "gstr1/gst1-save-data.php".(isset($_GET["period"]) ? '?period=' . $_GET["period"] : ''));
                        }
                        ?>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-sm-4">
                        <div class="card w-75 ml-auto timeline-card mb-0">
                            <div class="card-body">
                                <div id="content">
                                    <ul class="timeline">
                                        <li class="event progress-success border-color-light">
                                            <h3 class="text-success">Initiation</h3>
                                            <!-- <p>Mr.Guria</p> -->
                                            <!-- <p>16-08-2023</p> -->
                                        </li>
                                        <li class="event">
                                            <h3>Connect</h3>
                                            <!-- <p>Mr.Guria</p> -->
                                            <!-- <p>16-08-2023</p> -->
                                        </li>
                                        <li class="event">
                                            <h3>Save Data</h3>
                                            <!-- <p>Mr.Guria</p> -->
                                            <!-- <p>16-08-2023</p> -->
                                        </li>
                                        <li class="event">
                                            <h3>File & Submit</h3>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?php
require_once("../common/footer.php");
?>