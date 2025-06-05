<?php

require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;
if (!isset($_COOKIE["cookieQaGoodsList"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieQaGoodsList", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    } else {
        for ($i = 0; $i < 5; $i++) {
            $isChecked = ($i < 5) ? 'checked' : '';
        }
    }
}
$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Item Code',
        'slag' => 'item.itemCode',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Name',
        'slag' => 'item.itemName',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Batch',
        'slag' => 'stocklog.logRef',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Received Qty',
        'slag' => 'received_qty',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Passed Qty',
        'slag' => 'passed_qty',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Rejected Qty',
        'slag' => 'rejected_qty',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Remaining Qty',
        'slag' => 'remaining_qty',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Date',
        'slag' => 'stocklog.bornDate',
        'icon' => '',
        'dataType' => 'date'
    ],
    [
        'name' => 'PO Number',
        'slag' => 'grn.grnPoNumber',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'INV Number',
        'slag' => 'grn.vendorDocumentNo',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Name',
        'slag' => 'grn.vendorName',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor code',
        'slag' => 'grn.vendorCode',
        'icon' => '',
        'dataType' => 'string'
    ]
];



?>


<style>
    .qa-specification p:nth-child(2) {
        position: absolute;
        left: 40%;
        font-weight: 600;
    }

    .qa-item-recieve-block {
        position: relative;
        padding: 10px;
        background-color: #d3d3d3;
        border-radius: 7px;
    }

    .qa-item-recieve-block p {
        position: relative;
    }

    .qa-item-recieve-block-sub-item p::before {
        content: '';
        display: inline-block;
        position: absolute;
        left: 0;
        top: 8px;
        background-color: #fff;
        width: 20px;
        height: 1px;
    }

    .qa-item-recieve-block-sub-item p {
        padding-left: 2rem;
    }

    .qa-checked-item p {
        position: relative;
        padding-left: 5rem;
    }

    .qa-item-recieve-block-sub-item {
        border-left: 1px solid #fff;
    }

    .qa-checked-item p::before {
        content: '';
        display: inline-block;
        position: absolute;
        left: 50px;
        top: -5px;
        background-color: #fff;
        width: 1px;
        height: 26px;
    }

    .qa-checked-item p::after {
        content: '';
        display: inline-block;
        position: absolute;
        left: 50px;
        top: 9px;
        background-color: #fff;
        width: 20px;
        height: 1px;
    }

    .qa-view-header {
        display: flex;
        align-items: flex-start;
        gap: 100px;
        padding-top: 3em;
    }

    button.submit_frm {
        width: 150px;
        float: right;
    }

    .qa-modal-body-acc-btn {
        font-size: 12px !important;
        font-weight: 600;
    }

    .pdf-view {
        width: 100%;
        height: 200px;
        border: 2px dotted #ccc;
        border-radius: 12px;
        margin: 53px 0;
        position: relative;
        box-shadow: -19px 31px 26px -16px #6f6f6f;
        transition-duration: 0.2s;
        display: grid;
        place-items: center;
    }

    .pdf-view:hover {
        box-shadow: -19px 31px 26px -35px #6f6f6f;
    }


    .pdf-view span.float-label {
        font-size: 13px;
        position: absolute;
        top: -10px;
        left: 15px;
        background: #fff;
        padding: 0px 6px;
        font-weight: 600;
    }

    .img-view {
        width: 100%;
        height: auto;
        border: 2px dotted #ccc;
        border-radius: 12px;
        margin: 53px 0;
        position: relative;
        box-shadow: -19px 31px 26px -16px #6f6f6f;
        transition-duration: 0.2s;
    }

    .img-view:hover {
        box-shadow: -19px 31px 26px -35px #6f6f6f;
    }


    .img-view span.float-label {
        font-size: 13px;
        position: absolute;
        top: -10px;
        left: 15px;
        background: #fff;
        padding: 0px 6px;
        font-weight: 600;
    }

    .dotted-border-area.detailRecievedItem {
        position: relative;
        border-width: 1px;
        border-style: solid;
        border-color: #cfcfcf;
        margin: 2rem 0;
        padding: 1rem 1.5rem;
    }

    .dotted-border-area.detailRecievedItem .display-flex-space-between {
        flex-direction: column;
    }

    .detailRecievedItem label {
        position: absolute;
        top: -9px;
        background: #fff;
        padding: 0rem 0.5rem;
        font-weight: 600 !important;
    }
</style>

<!-- <link rel="stylesheet" href="../../public/assets/sales-order.css"> -->

<!-- <link rel="stylesheet" href="../../public/assets/listing.css"> -->
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">

<style>
    #accordionQuality {
        overflow: scroll;
        scrollbar-width: none;
    }

    #accordionQuality .item-status {
        justify-content: flex-start;
    }

    #accordionSpecifications p {
        width: auto;
    }

    .qualityModalTable tr th {
        font-weight: 600 !important;
        color: #000 !important;
        background: #ebebeb !important;
    }

    .global-view-modal .modal-header .left {
        justify-content: center;
    }

    .right-subblock {
        display: flex;
        justify-content: flex-end;
        padding-right: 17px;
        margin-top: 7px;
    }

    .right-subblock .page-list-filer a {
        font-size: 0.75rem;
        background: #e6e6e6;
        padding: 2px 13px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        gap: 9px;
        color: #000;
        position: relative;
    }

    .vitwo-alpha-global .dataTables_wrapper {
        overflow: auto;
        height: calc(100vh - 256px);
    }
</style>

<?php


$keywd = '';
if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
    $keywd = $_REQUEST['keyword'];
} else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
    $keywd = $_REQUEST['keyword2'];
}
?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper report-wrapper vitwo-alpha-global">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <?php ?>
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 p-0">
                    <div class="card card-tabs reports-card">
                        <div class="card-body">
                            <div class="row filter-serach-row m-0">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="row table-header-item">
                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space"
                                        style="width:100%">
                                        <div class="left-block">
                                            <div class="label-select">
                                                <h3 class="card-title mb-0">Quality Analysis
                                                </h3>
                                            </div>
                                        </div>


                                        <div class="right-block">
                                            <div class="page-list-filer filter-list">
                                                <a href="recieved-item.php" class="filter-link active"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>All
                                                </a>
                                                <a href="received-items-rejected-list.php" class=""><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Rejected
                                                </a>
                                            </div>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()">
                                                <ion-icon name="expand-outline"></ion-icon>
                                            </button>
                                            <button type="button" id="revealList" class="page-list">
                                                <ion-icon name="funnel-outline"></ion-icon>
                                            </button>

                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>

                            <div class="right-subblock">
                                <div class="page-list-filer filter-list">
                                    <a href="" class="filter-link subGoodsLink" name="FG"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>FG
                                    </a>
                                    <a href="" class="filter-link subGoodsLink" name="RM"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>RM
                                    </a>
                                    <a href="" class="filter-link subGoodsLink" name="SFG"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>SFG
                                </div>
                            </div>

                            <div class="card card-tabs mobile-transform-card mb-0" style="border-radius: 20px;">
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane dataTableTemplate dataTable_stock fade show active"
                                            id="listTabPan" role="tabpanel" aria-labelledby="listTab"
                                            style="background: #fff; border-radius: 20px;">
                                            <div class="length-row mobile-legth-row">
                                                <span>Show</span>
                                                <select name="" id="" class="custom-select" value="25">
                                                    <option value="10">10</option>
                                                    <option value="25" selected="selected">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                    <option value="200">200</option>
                                                    <option value="250">250</option>
                                                </select>
                                                <span>Entries</span>
                                            </div>
                                            <div class="filter-action">
                                                <a type="button" class="btn add-col setting-menu" data-toggle="modal"
                                                    data-target="#myModal1"> <ion-icon
                                                        name="settings-outline"></ion-icon> Manage Column</a>
                                                <div class="length-row">
                                                    <span>Show</span>
                                                    <select name="" id="Qarm_ListLimit" class="custom-select">
                                                        <option value="10">10</option>
                                                        <option value="25" selected="selected">25</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                        <option value="200">200</option>
                                                        <option value="250">250</option>
                                                    </select>
                                                    <span>Entries</span>
                                                </div>
                                                <div class="filter-search">
                                                    <div class="icon-search" data-toggle="modal"
                                                        data-target="#btnSearchCollpase_modal">
                                                        <p>Advance Search</p>
                                                        <ion-icon name="filter-outline"></ion-icon>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="exportgroup">
                                                <button class="exceltype btn btn-primary btn-export" type="button">
                                                    <ion-icon name="download-outline"></ion-icon>
                                                    Export
                                                </button>
                                                <ul class="export-options">
                                                    <li>
                                                        <button class="ion-paginationlistnew">
                                                            <ion-icon name="list-outline"
                                                                class="ion-paginationlistnew md hydrated"
                                                                role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistnew">
                                                            <ion-icon name="list-outline"
                                                                class="ion-fulllistnew md hydrated"
                                                                role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- <a href="manage-discount-variation-actions.php?create" class="btn btn-create mobile-page mobile-create"
                                                type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a> -->


                                            <table id="dataTable_detailed_view"
                                                class="table table-hover table-nowrap stock-new-table transactional-book-table">

                                                <thead>
                                                    <tr>
                                                        <?php
                                                        foreach ($columnMapping as $index => $column) {
                                                        ?>
                                                            <th data-value="<?= $index ?>"><?= $column['name'] ?></th>
                                                        <?php
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody id="detailed_tbody">
                                                </tbody>
                                            </table>
                                            <div class="row custom-table-footer">
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div id="limitText" class="limit-text">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div id="yourDataTable_paginate">
                                                        <div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <!---------------------------------deialed View Table settings Model Start--------------------------------->
                                            <div class="modal manage-column-setting-modal" id="myModal1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title text-sm">Detailed View Column
                                                                Settings</h4>
                                                            <button type="button" class="close"
                                                                data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form name="table_settings_detailed_view" method="POST"
                                                            action="<?php $_SERVER['PHP_SELF']; ?>">
                                                            <div class="modal-body" style="max-height: 450px;">
                                                                <!-- <h4 class="modal-title">Detailed View Column Settings</h4> -->
                                                                <input type="hidden" id="tablename" name="tablename"
                                                                    value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                                <input type="hidden" id="pageTableName"
                                                                    name="pageTableName"
                                                                    value="ERP_TEST_<?= $pageName ?>" />
                                                                <div class="modal-body">
                                                                    <div id="dropdownframe"></div>
                                                                    <div id="main2">
                                                                        <div class="checkAlltd d-flex gap-2 mb-3 pl-2">
                                                                            <input type="checkbox"
                                                                                class="grand-checkbox" value="" />
                                                                            <p class="text-xs font-bold">Check All</p>
                                                                        </div>

                                                                        <table class="colomnTable">
                                                                            <?php
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieQaGoodsList"], true) ?? [];

                                                                            foreach ($columnMapping as $index => $column) {

                                                                            ?>
                                                                                <tr>
                                                                                    <td valign="top">

                                                                                        <input type="checkbox"
                                                                                            class="settingsCheckbox_detailed"
                                                                                            name="settingsCheckbox[]"
                                                                                            id="settingsCheckbox_detailed_view[]"
                                                                                            value='<?= $column['slag'] ?>'>
                                                                                        <?= $column['name'] ?>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php
                                                                            }
                                                                            ?>

                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="submit" id="check-box-submt"
                                                                    name="check-box-submit" data-dismiss="modal"
                                                                    class="btn btn-primary">Save</button>
                                                                <button type="button" class="btn btn-danger"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!---------------------------------Table Model End--------------------------------->

                                            <div class="modal " id="btnSearchCollpase_modal" tabindex="-1" role="dialog"
                                                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-sm" id="exampleModalLongTitle">
                                                                Advanced Filter</h5>
                                                        </div>
                                                        <form id="myForm" method="post" action="">
                                                            <div class="modal-body">

                                                                <table>
                                                                    <tbody>
                                                                        <?php
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "<", ">", ">=", "<=", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex => $column) {
                                                                            if ($columnIndex === 0 || $columnIndex == 4 || $columnIndex == 5 || $columnIndex == 6 || $columnIndex == 7) {
                                                                                continue;
                                                                            } ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <div
                                                                                        class="icon-filter d-flex align-items-center gap-2">
                                                                                        <?= $column['icon'] ?>
                                                                                        <p
                                                                                            id="columnName_<?= $columnIndex ?>">
                                                                                            <?= $column['name'] ?>
                                                                                        </p>
                                                                                        <input type="hidden"
                                                                                            id="columnSlag_<?= $columnIndex ?>"
                                                                                            value="<?= $column['slag'] ?>">
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <select
                                                                                        class="form-control selectOperator"
                                                                                        id="selectOperator_<?= $columnIndex ?>"
                                                                                        name="operator[]" val="">
                                                                                        <?php
                                                                                        if (($column['dataType'] === 'date')) {
                                                                                            $operator = array_slice($operators, -3, 3);
                                                                                            foreach ($operator as $oper) {
                                                                                        ?>
                                                                                                <option value="<?= $oper ?>">
                                                                                                    <?= $oper ?>
                                                                                                </option>
                                                                                            <?php
                                                                                            }
                                                                                        } elseif ($column['dataType'] === 'number') {
                                                                                            $operator = array_slice($operators, 2, 6);
                                                                                            foreach ($operator as $oper) {
                                                                                            ?>
                                                                                                <option value="<?= $oper ?>">
                                                                                                    <?= $oper ?>
                                                                                                </option>
                                                                                                <?php

                                                                                            }
                                                                                        } else {
                                                                                            $operator = array_slice($operators, 0, 2);
                                                                                            foreach ($operator as $oper) {
                                                                                                if ($oper === 'CONTAINS') {
                                                                                                ?>
                                                                                                    <option value="LIKE">
                                                                                                        <?= $oper ?>
                                                                                                    </option>
                                                                                                <?php
                                                                                                } else { ?>

                                                                                                    <option value="NOT LIKE">
                                                                                                        <?= $oper ?>
                                                                                                    </option>

                                                                                        <?php
                                                                                                }
                                                                                            }
                                                                                        } ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td id="td_<?= $columnIndex ?>">
                                                                                    <input
                                                                                        type="<?= ($column['dataType'] === 'date') ? 'date' : 'input' ?>"
                                                                                        data-operator-val="" name="value[]"
                                                                                        class="fld form-control m-input"
                                                                                        id="value_<?= $columnIndex ?>"
                                                                                        placeholder="Enter Keyword"
                                                                                        value="">
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" id="serach_reset"
                                                                    class="btn btn-primary">Reset</button>
                                                                <button type="submit" id="serach_submit"
                                                                    class="btn btn-primary"
                                                                    data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>






                                            <!-- edit modal start  -->


                                            <!-- edit modal end -->

                                            <!-- Global View start-->



                                            <!-- Global View end -->

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <div class="modal fade right global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
            <!--Content-->
            <div class="modal-content">
                <!--Header-->
                <div class="modal-header">
                    <div class="top-details">
                        <div class="left">
                            <p class="info-detail amount"><ion-icon
                                    name="business-outline"></ion-icon><span
                                    id="vendorName"></span></p>
                            <p class="info-detail po-number"><ion-icon
                                    name="information-outline"></ion-icon><span
                                    id="vendorCode"></span></p>
                            <p class="info-detail po-number"><ion-icon
                                    name="information-outline"></ion-icon><span
                                    id="invNumber"></span></p>
                        </div>
                        <div class="right">
                            <div class="qa-item-recieve-block">
                                <p class="text-sm my-2 font-bold" id="totalreq"> </p>
                                <div class="qa-item-recieve-block-sub-item">
                                    <p class="text-sm my-2 font-bold" id="totalChecked">Checked :</p>
                                    <div class="qa-checked-item">
                                        <p class="text-xs my-2" id="htmlPassed"> </p>
                                        <p class="text-xs my-2" id="htmlRejected"> </p>
                                    </div>
                                    <p class="text-xs my-2 font-bold" id="htmlRemaining"> </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!--Body-->
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                            <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Quality Check</button>
                            <button class="nav-link relativeHistory" id="nav-relativehistory-tab"
                                data-bs-toggle="tab" data-bs-target="#nav-relativehistory" data-stocklogid=""
                                type="button" role="tab"
                                aria-controls="nav-relativehistory"
                                aria-selected="false"><ion-icon
                                    name="document-text-outline"></ion-icon>Relative History</button>
                            <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                        </div>
                    </nav>
                    <div class="tab-content global-tab-content" id="nav-tabContent">

                        <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                            <div class="d-flex nav-overview-tabs">

                            </div>


                            <div class="row">
                                <div class="py-2">
                                    <button type="button" id="" class="btn btn-primary submit_frm">SUBMIT</button>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">

                                    <div class="row">
                                        <!-- <div class="col-12 col-lg-12 col-md-12 col-sm-12"> -->
                                        <!-- <div class=""> -->
                                        <div class="row orders-table">
                                            <!-- <div class="col-lg-12 col-md-8 col-sm-12 col-8"> -->
                                            <div class="items-table">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            <th>Item Code</th>
                                                            <th>Item Name</th>
                                                            <th>Type</th>
                                                            <th>Avalability Check</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="itemTableSpecification">


                                                    </tbody>
                                                </table>
                                                <!-- <div class="col-lg-12 col-md-12 col-sm-12 col-12"> -->
                                                <div class="items-table">
                                                    <h4>Specifications</h4>
                                                    <table>
                                                        <thead>
                                                            <tr>
                                                                <th>Item Description</th>
                                                                <th>Net Weight</th>
                                                                <th>Gross Weight</th>
                                                                <th>Volume</th>
                                                                <th>Volume Cube Cm</th>
                                                                <th>Height</th>
                                                                <th>Width</th>
                                                                <th>Length</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="itemSpecificationsDatatable">

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!-- </div> -->
                                            </div>
                                            <!-- </div> -->

                                        </div>
                                        <!-- </div> -->
                                        <!-- </div> -->
                                    </div>

                                    <div class="accordion item-classification accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                        <div class="accordion-item">

                                            <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button btn btn-primary qa-modal-body-acc-btn waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#itemClassification" aria-expanded="true" aria-controls="flush-collapseOne">
                                                    Select Item Status :
                                                </button>
                                            </h2>
                                            <input type="hidden" id="stock_id" name="" value="">
                                            <input type="hidden" id="remain_qty" name="" value="">
                                            <input type="hidden" id="received_qty" name="" value="">
                                            <input type="hidden" id="total_passed_qty" name="" value="">
                                            <input type="hidden" id="total_reject_qty" name="" value="">
                                            <div class="item-status d-flex gap-4 qaSts">

                                                <label class="status-common status-reserve d-flex gap-2 radio-button-label">
                                                    <input type="radio" checked="" id="input1" name="status_radio" value="0">
                                                    <span class="text-xs">Todo</span>
                                                </label>
                                                <label class="status-common status-cip d-flex gap-2 radio-button-label">
                                                    <input type="radio" name="status_radio" value="1">
                                                    <span class="text-xs">Check In Progress</span>
                                                </label>
                                                <label class="status-common status-release d-flex gap-2 radio-button-label">
                                                    <input type="radio" name="status_radio" value="2">
                                                    <span class="text-xs">Done</span>
                                                </label>

                                                <label class="status-common status-release d-flex gap-2 radio-button-label">
                                                    <input type="number" id="input_passed" name="passed" class="form-control">
                                                    <span class="text-xs">Passed</span>
                                                </label>

                                                <label class="status-common status-release d-flex gap-2 radio-button-label">
                                                    <input type="number" id="input_reject" name="rejected" class="form-control">
                                                    <span class="text-xs">Rejected</span>
                                                </label>


                                            </div>

                                        </div>
                                    </div>





                                    <div class="accordion item-classification accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button btn btn-primary qa-modal-body-acc-btn waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#pdfUploadItem" aria-expanded="true" aria-controls="flush-collapseOne">
                                                    PDF Upload
                                                </button>
                                            </h2>
                                            <div id="pdfUploadItem" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#itemClassification">
                                                <div class="accordion-body p-0">
                                                    <div class="card bg-transparent">
                                                        <div class="card-body p-2">
                                                            <div class="upload-section">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="container">
                                                                            <div class="row">
                                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                                    <input id="pdf_file_1" type="file" accept=".pdf" class="pdf-upload-input">
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
                                    </div>
                                    <div class="accordion item-classification accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button btn btn-primary qa-modal-body-acc-btn waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#imgUrlUploadItem" aria-expanded="true" aria-controls="flush-collapseOne">
                                                    Multiple Image URL Upload
                                                </button>
                                            </h2>
                                            <div id="imgUrlUploadItem" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#itemClassification">
                                                <div class="accordion-body p-0">
                                                    <div class="card bg-transparent border border-rounded-3">
                                                        <div class="card-body py-3">
                                                            <div class="upload-section">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="container">
                                                                            <div class="row">
                                                                                <div class="col-lg-4 col-md-4 col-sm-4 col-12 imgUp">
                                                                                    <!-- <div class="imagePreview"></div> -->
                                                                                    <input type="text" class="form-control my-2 all-link" placeholder="upload image link">
                                                                                    <!-- <label class="btn btn-primary">
                                                                                                                                                    Upload
                                                                                                                                                    <input type="file" class="uploadFile img" accept="image/*" value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
                                                                                                                                                </label> -->
                                                                                </div>
                                                                                <i class="fa fa-plus imgAdd"></i>
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
                                </div>

                            </div>

                        </div>

                        <div class="tab-pane fade" id="nav-relativehistory" role="tabpanel" aria-labelledby="nav-relativehistory-tab">
                            <table class="table table-hover qualityModalTable">
                                <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Doc No.</th>
                                        <th>Passed</th>
                                        <th>Rejected</th>
                                        <th>Status</th>
                                        <th>Done By</th>
                                        <th>Done On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="relativeHistoryviewTableData">

                                </tbody>
                            </table>
                        </div>
                        <div class="modal fade customer-modal" id="detailedHistoryModal" tabindex="-1" aria-labelledby="detailedHistoryLabel" aria-hidden="true">
                            <div class="modal-dialog w-25" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="pdf-view">
                                            <span class="float-label">PDF View</span>
                                            <p></p>
                                        </div>
                                        <div class="img-view">
                                            <span class="float-label">Image View</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                            <div class="inner-content">
                                <div class="audit-head-section mb-3 mt-3 ">
                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span></p>
                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"></span></p>
                                </div>
                                <hr>
                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent">


                                </div>
                                <div class="modal fade right audit-history-modal" id="innerModal" role="dialog" aria-labelledby="innerModalLabel" aria-modal="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content auditTrailBodyContentLineDiv">

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

<!-- <div id="loaderModal" class="modal" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Downloading, please wait...</p>
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div> -->
</div>
<!-- /.row -->
<!-- /.content -->


<!-- /.Content Wrapper. Contains page content -->
<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo $_REQUEST['pageNo'];
                                                } ?>">
</form>
<!-- End Pegination from------->


<?php

require_once("../common/footer2.php");
?>


<script>
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function(event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function() {
        form.submit();
    });
</script>








<!-----------mobile filter list------------>


<script>
    $(document).ready(function() {
        $("button.page-list").click(function() {
            var buttonId = $(this).attr("id");
            $("#modal-container").removeAttr("class").addClass(buttonId);
            $(".mobile-transform-card").addClass("modal-active");
        });

        $(".btn-close-modal").click(function() {
            $("#modal-container").toggleClass("out");
            $(".mobile-transform-card").removeClass("modal-active");
        });
    })
</script>


<!-- modal view responsive more tabs -->

<script>
    $(document).ready(function() {
        // Adjust tabs based on window size
        adjustTabs();

        // Listen for window resize event
        $(window).resize(function() {
            adjustTabs();
        });
    });

    function adjustTabs() {
        var navTabs = $("#nav-tab");
        var moreDropdown = $("#more-dropdown");

        // Reset nav tabs
        navTabs.children().show();
        moreDropdown.empty();

        // Check if tabs overflow the container
        var visibleTabs = 7; // Number of visible tabs
        if ($(window).width() < 576) { // Adjust for mobile devices
            visibleTabs = 3; // Display only one tab on mobile
        } else if ($(window).width() > 576) {
            visibleTabs = 7;
        } else {
            visibleTabs = 7;
        }


        var hiddenTabs = navTabs.children(":gt(" + (visibleTabs) + ")");

        hiddenTabs.hide().appendTo(moreDropdown);

        // If there are hidden tabs, show the "More" dropdown
        if (hiddenTabs.length > 0) {
            moreDropdown.show();
        } else {
            moreDropdown.hide();
        }
    }
</script>


<!-- datatable and modal script portion  -->

<script>
    $(document).ready(function() {
        var indexValues = [];
        var dataTable;
        let columnMapping = <?php echo json_encode($columnMapping); ?>
        // let dataPaginate;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"billList_wrapper"t><ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function(settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [{
                    extend: 'collection',
                    text: '<ion-icon name="download-outline"></ion-icon> Export',
                    buttons: [{
                        extend: 'csv',
                        text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
                    }]
                }],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        var allData;
        var dataPaginate;


        // function full_datatable() {
        //     let fromDate = "<?= $fromDate ?>"; // For Date Filter
        //     let toDate = "<?= $toDate ?>"; // For Date Filter        
        //     let comid = <?= $company_id ?>;
        //     let locId = <?= $location_id ?>;
        //     let bId = <?= $branch_id ?>;

        //     $.ajax({
        //         type: "POST",
        //         url: "ajaxs/discount/ajax-manage-discount-variation-all.php",
        //         dataType: 'json',
        //         data: {
        //             act: 'alldata',
        //         },
        //         beforeSend: function () {

        //         },
        //         success: function (response) {
        //             // all_data = response.all_data;
        //             allData = response.all_data;


        //         },
        //     });
        // };
        // full_datatable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '', typeGoods = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookieQaGoodsList');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/qa/ajax-received-items-qa-all.php",
                dataType: 'json',
                data: {
                    act: 'QA_AllList',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit,
                    typeGoods: typeGoods
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function(response) {
                    console.log(response);
                    // alert(response)

                    if (response.status) {
                        var responseObj = response.data;
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        $.each(responseObj, function(index, value) {
                            //  $('#item_id').val(value.itemId);

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal" data-itemCode=${value['item.itemCode']} data-stocklogid=${value['stocklog.stockLogId']} >${value['item.itemCode']}</a>`,
                                value['item.itemName'],
                                value['stocklog.logRef'],
                                value['received_qty'],
                                value['passed_qty'],
                                value['rejected_qty'],
                                value['remaining_qty'],
                                value['stocklog.bornDate'],
                                value['grn.grnPoNumber'],
                                value['grn.vendorDocumentNo'],
                                value['grn.vendorName'],
                                value['grn.vendorCode'],

                                ` <div class="dropout">
                                     <button class="more">
                                          <span></span>
                                          <span></span>
                                          <span></span>
                                     </button>
                                     <ul>
                                        <li>
                                            <button class="soModal" data-itemCode=${value['item.itemCode']} data-stocklogid=${value['stocklog.stockLogId']}>
                                                <ion-icon name="create-outline" class="ion-view"></ion-icon>
                                                view
                                            </button>
                                        </li>
                                     </ul>
                                   
                                 </div>`,
                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            var checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function(index) {
                                var columnVal = $(this).val();
                                if (checkedColumns.includes(columnVal)) {
                                    $(this).prop("checked", true);
                                    dataTable.column(index).visible(true);

                                } else {
                                    notVisibleColArr.push(index);
                                }
                            });
                            // console.log("notVisibleColArr index:", notVisibleColArr);
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }


                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').hide();
                        $('#limitText').hide();
                    }
                }
            });
        }

        fill_datatable();

        let currentTypeGoods = '';

        $('.filter-link').on('click', function(e) {
            e.preventDefault();

            $('.filter-link').removeClass('active');

            $(this).addClass('active');

            currentTypeGoods = $(this).attr('name') || '';

            fill_datatable(formInputs, '', '', currentTypeGoods);
        });


        $(document).on("click", ".ion-paginationlistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieQaGoodsList')
                },
                beforeSend: function() {
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-paginationlistnew').prop('disabled', true)
                },

                success: function(response) {
                    var blob = new Blob([response.csvContentpage], {
                        type: 'text/csv'
                    });

                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = '<?= $newFileName ?>';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);


                },
                complete: function() {
                    // Hide loader modal after request completes
                    $('#loaderModal').hide();
                    $('.ion-paginationlistnew').prop('disabled', false)
                }
            })

        });
        $(document).on("click", ".ion-fulllistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/qa/ajax-received-items-qa-all.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieQaGoodsList'),
                    formDatas: formInputs
                },

                beforeSend: function() {
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-fulllistnew').prop('disabled', true)
                },
                success: function(response) {
                    var blob = new Blob([response.csvContentall], {
                        type: 'text/csv'
                    });

                    var url = URL.createObjectURL(blob);
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = '<?= $newFileNameDownloadall ?>';
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);


                },
                complete: function() {
                    // Hide loader modal after request completes
                    $('#loaderModal').hide();
                    $('.ion-fulllistnew').prop('disabled', false);
                }
            })

        });



        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit, currentTypeGoods);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#Qarm_ListLimit").val();
            //    console.log(limitDisplay);
            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay, currentTypeGoods);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function() {

            $(document).on("change", ".selectOperator", function() {
                let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
                let operatorName = $(this).val();
                let columnName = $(`#columnName_${columnIndex}`).html().trim();
                let inputContainer = $(`#td_${columnIndex}`);
                let inputId;
                if (columnName === 'Date') {
                    inputId = "value4_" + columnIndex;
                } else if (columnName === 'Valid From') {
                    inputId = "value2_" + columnIndex;
                } else if (columnName === 'Valid Upto') {
                    inputId = "value3_" + columnIndex;
                }

                if ((columnName === 'Date' || columnName === 'Valid From' || columnName === 'Valid Upto') && operatorName === 'BETWEEN') {
                    inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
                } else {
                    $(`#${inputId}`).remove();
                }
                // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
            });

            $(document).on("click", "#serach_submit", function(event) {
                event.preventDefault();
                let values;
                $(".selectOperator").each(function() {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";
                    let value3 = $(`#value3_${columnIndex}`).val() ?? "";
                    let value4 = $(`#value4_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'stocklog.bornDate') {
                        values = value4;
                    } else if (columnSlag === 'valid_from') {
                        values = value2;
                    } else if (columnSlag === 'valid_to') {
                        values = value3;
                    }

                    if ((columnSlag === 'updated_at' || columnSlag === 'created_at' || columnSlag === 'stocklog.bornDate') && operatorName == "BETWEEN") {
                        formInputs[columnSlag] = {
                            operatorName,
                            value: {
                                fromDate: value,
                                toDate: values
                            }
                        };
                    } else {
                        formInputs[columnSlag] = {
                            operatorName,
                            value
                        };
                    }
                });

                $('#btnSearchCollpase_modal').modal('hide');
                // console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs, '', '', currentTypeGoods);
                // $("#myForm")[0].reset();
            });



        });


        // -------------checkbox----------------------

        $(document).ready(function() {
            var columnMapping = <?php echo json_encode($columnMapping); ?>;

            var indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                var column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function() {
                var columnVal = $(this).val();
                // console.log(columnVal);

                var index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                // console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function() {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function() {
                    var columnVal = $(this).val();
                    // console.log(columnVal);
                    var index = columnMapping.findIndex(function(column) {
                        return column.slag === columnVal;
                    });
                    if ($(this).is(':checked')) {
                        indexValues.push(index);
                    } else {
                        var removeIndex = indexValues.indexOf(index);
                        if (removeIndex !== -1) {
                            indexValues.splice(removeIndex, 1);
                        }
                    }
                    toggleColumnVisibility(index, this);
                });
            });

        });

    });

    //    -------------- save cookies--------------------

    $(document).ready(function() {
        $(document).on("click", "#check-box-submt", function(event) {
            // console.log("Hiiiii");
            event.preventDefault();
            // $("#myModal1").modal().hide();
            $('#btnSearchCollpase_modal').modal('hide');
            var tablename = $("#tablename").val();
            var pageTableName = $("#pageTableName").val();
            var settingsCheckbox = [];
            var fromData = {};
            $(".settingsCheckbox_detailed").each(function() {
                if ($(this).prop('checked')) {
                    var chkBox = $(this).val();
                    settingsCheckbox.push(chkBox);
                    fromData = {
                        tablename,
                        pageTableName,
                        settingsCheckbox
                    };
                }
            });

            // console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'QaGoods_List',
                        fromData: fromData
                    },
                    success: function(response) {
                        console.log(response);
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        })
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });



    });
</script>

<script>
    $(document).ready(function() {
       
    
    $(document).on("click", ".soModal", function() {


        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        $(".classic-view").html('');

        let stockLogid = $(this).data('stocklogid');
        let itemCode = $(this).data('itemcode');
        $('.auditTrail').attr("data-ccode", itemCode);
        $('.relativeHistory').attr('data-stocklogid', stockLogid);



        $.ajax({
            type: "GET",
            url: "ajaxs/modals/qa/ajax-received-items-qa-modal.php",
            dataType: 'json',
            data: {
                act: "modalHeader",
                stocklogid: stockLogid,
            },
            beforeSend: function() {
                // Clear the previous text before new data loads
                $('#vendorName').text('');
                $('#vendorCode').text('');
                $('#invNumber').text('');
                $('#totalreq').text('');
                $('#htmlPassed').text('');
                $('#htmlRejected').text('');
                $('#htmlRemaining').text('');


            },
            success: function(value) {
                if (value.status) {
                    var responseObj = value.data[0];

                    // Fill with the new values
                    $('#vendorName').text(responseObj.vendorName);
                    $('#vendorCode').html(responseObj.vendorCode);
                    $('#invNumber').html(responseObj.invNumber);
                    $('#totalreq').html('Total Received : ' + responseObj.itemQty);
                    $('#htmlPassed').html('Passed : ' + responseObj.passedQty);
                    $('#htmlRejected').html('Failed : ' + responseObj.rejectedQty);
                    $('#htmlRemaining').html('Checked Required : ' + responseObj.remainingQty);

                    // Append the hidden input values
                    $('#remain_qty').val(responseObj.remainingQty);
                    $('#received_qty').val(responseObj.itemQty);
                    $('#total_passed_qty').val(responseObj.passedQty);
                    $('#total_reject_qty').val(responseObj.rejectedQty);
                    $('#stock_id').val(stockLogid);

                    $(".created_by_trail").html(responseObj.createdBy + "<span class='font-bold text-normal'> on </span>" + responseObj.createdAt);
                    $(".updated_by").html(responseObj.updatedBy + "<span class='font-bold text-normal'> on </span>" + responseObj.updatedAt);

                }
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });


        $.ajax({
            type: "GET",
            url: "ajaxs/modals/qa/ajax-received-items-qa-modal.php",
            dataType: 'json',
            data: {
                act: "rejectedlistSpecificationModal", // Action name for rejected list specification modal
                stockLogid: stockLogid // Passing the QA log ID to fetch specific data
            },
            beforeSend: function() {
                // Clear previous data and show loading animation
                $('#itemTableSpecification').html('');
                $('#itemSpecificationsDatatable').html('');
            },
            success: function(value) {

                if (value.status) {
                    var responseObj = value.data;

                    // Loop through the dynamic list of specifications (item only)
                    responseObj.dynamic_listSpecification.forEach(function(item) {
                        let itemRow = `
                    <tr>
                        <td>${item.itemDesc}</td>
                        <td>${item.netWeight}</td>
                        <td>${item.grossWeight}</td>
                        <td>${item.volume}</td>
                        <td>${item.volumeCubeCm}</td>
                        <td>${item.height}</td>
                        <td>${item.width}</td>
                        <td>${item.length}</td>
                    </tr>
                `;
                        $('#itemSpecificationsDatatable').append(itemRow);
                    });

                    // Loop through item details (item general details)
                    responseObj.onlyItemDetails.forEach(function(itemDetail) {
                        console.log(itemDetail)
                        let specificationRow = `
                    <tr>
                        <td>${itemDetail.itemCode}</td>
                        <td>${itemDetail.itemName}</td>
                        <td>${itemDetail.availabilityCheck}</td>
                        <td>${itemDetail.status}</td>
                    </tr>
                `;
                        $('#itemTableSpecification').append(specificationRow);
                    });

                    // Remove the loader after data is appended
                    // $('#globalModalLoader').remove();
                }
            },
            error: function(xhr, status, error) {
                console.error(error);
                // $('#globalModalLoader').remove();
            }
        });

        $.ajax({
            url: "ajaxs/modals/qa/ajax-received-items-qa-modal.php",
            method: "GET",
            dataType: "json",
            data: {
                act: "formStatusradio",
                stocklogid: stockLogid
            },
            beforeSend() {
                $('input[name="status_radio"]').prop('checked', false);
            },
            success(res) {
                if (res.status) {
                    const row = res.data[0];
                    console.log(row)

                    $(`input[name="status_radio"][value="${row.form_status}"]`).prop("checked", true);
                    $('#detailedHistoryModal').modal('show');
                }

            }
        });

    })

    $(document).on("click", ".relativeHistory", function() {
        // alert("called")
        let stocklogid = $(this).data('stocklogid')
        $.ajax({
            type: "POST",
            url: "ajaxs/modals/qa/ajax-received-items-qa-modal.php",
            data: {
                act: 'relativeHistorytable',
                stocklogid: stocklogid // <-- pass your stocklogid dynamically
            },
            dataType: "json",
            beforeSend: function() {
                $("#relativeHistoryviewTableData").html('');
            },
            success: function(response) {
                if (response.status) {
                    let tableBody = '';

                    response.data.forEach(function(row) {
                        tableBody += `
                    <tr>
                        <td>${row.sl_no}</td>
                        <td>${row.doc_no}</td>
                        <td>${row.passed}</td>
                        <td>${row.rejected}</td>
                        <td>${row.status}</td>
                        <td>${row.created_by}</td>
                        <td>${row.created_at}</td>
                        <td>
                            <a type="button" class="btn btn-transparent waves-effect waves-light relativeHistoryModal" data-qaid="${row.qa_log_Id}" data-toggle="modal" data-target="#detailedHistoryModal">
                                <i class="fa fa-eye po-list-icon"></i>
                            </a>
                        </td>
                    </tr>
                `;
                    });

                    $('#relativeHistoryviewTableData').html(tableBody); // Replace with your <tbody> id
                } else {
                    $('#relativeHistoryviewTableData').html('<tr><td colspan="8" class="text-center">No Data Found</td></tr>');
                }
            },
            error: function() {
                alert('Something went wrong.');
            }
        });
    });

    $(document).on("click", ".relativeHistoryModal", function() {
        let qaid = $(this).data("qaid"); // you can change to 'qaid' if needed
        $("#detailedHistoryModal").modal("show");

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/qa/ajax-received-items-rejected-modal.php",
            data: {
                act: 'relativeHistoryModal',
                qa_log_Id: qaid
            },
            dataType: "json",
            beforeSend: function() {
                $(".pdf-view p").html('');
                $(".img-view").html('<span class="float-label">Image View</span>');
            },
            success: function(response) {
                if (response.status && response.data.length > 0) {
                    let pdfData = '';
                    let imgData = '';

                    response.data.forEach(function(item) {
                        if (item.qa_file) {
                            pdfData += `<p>${item.qa_file}</p>`;
                        }

                        if (item.links && item.links.length > 0) {
                            item.links.forEach(function(link) {
                                imgData += `<p>${link}</p>`;
                            });
                        }
                    });

                    $(".pdf-view p").html(pdfData);
                    $(".img-view").append(imgData);

                } else {
                    $(".pdf-view p").html('No PDF Found');
                    // $(".img-view").html('<span class="float-label">Image View</span><p>No Images</p>');
                    $("#detailedHistoryModal").modal("show");
                }
            }
        });
    });

    $(".imgAdd").click(function() {
        $(this)
            .closest(".row")
            .find(".imgAdd")
            .before(
                '<div class="col-lg-4 col-md-4 col-sm-4 col-12 imgUp"><input type="text" class="form-control my-2 all-link" placeholder="upload image link"><i class="fa fa-times del"></i></div>'
            );
    });
    $(document).on("click", "i.del", function() {
        $(this).parent().remove();
    });
    $(function() {
        $(document).on("change", ".uploadFile", function() {
            var uploadFile = $(this);
            var files = !!this.files ? this.files : [];
            if (!files.length || !window.FileReader) return;

            if (/^image/.test(files[0].type)) {
                // only image file
                var reader = new FileReader();
                reader.readAsDataURL(files[0]);

                reader.onloadend = function() {

                    uploadFile
                        .closest(".imgUp")
                        .find(".imagePreview")
                        .css("background-image", "url(" + this.result + ")");
                };
            }
        });
    });


    function checkFormStatus() {
            const radioSelected = $('input[name="status_radio"]:checked').length > 0;
            const passedVal = $('#input_passed').val();
            const rejectVal = $('#input_reject').val();
            const hasNumberValue = passedVal !== '' || rejectVal !== '';

            if (radioSelected && hasNumberValue) {
                $('.submit_frm').prop('disabled', false);
            } else {
                $('.submit_frm').prop('disabled', true);
            }
        }

        // Run check on page load
        checkFormStatus();

        // Bind events
        $('input[name="status_radio"], #input_passed, #input_reject').on('change keyup input', function() {
            checkFormStatus();
        });


    function clearItemStatus() {
        // Clear radio inputs
        $('div.item-status input[type="radio"]').prop('checked', false);

        // Clear number inputs
        $('div.item-status input[type="number"]').val('');
    }

    $(document).on("click", ".submit_frm", function() {
        var status = $('input[name="status_radio"]:checked').val();
        var passed = Number($("#input_passed").val()) ?? 0;
        var reject = Number($("#input_reject").val()) ?? 0;
        var stock_id = $("#stock_id").val();
        var remain_qty = (parseFloat($("#remain_qty").val()) > 0) ? parseFloat($("#remain_qty").val()) : 0;
        var received_qty = $("#received_qty").val();
        var previous_passed = (parseFloat($("#total_passed_qty").val()) > 0) ? parseFloat($("#total_passed_qty").val()) : 0;
        var previous_rejected = (parseFloat($("#total_reject_qty").val()) > 0) ? parseFloat($("#total_reject_qty").val()) : 0;

        const all_link = [];
        $(".all-link").each(function() {
            var value = $(this).val();
            if (value.trim() !== '') {
                all_link.push(value);
            }
        });

        if ((parseFloat(passed) + parseFloat(reject)) > remain_qty) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please provide Passed and Rejected Quantity less than Remaining Quantity!',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            var formData = new FormData();
            var selectedFile = $("#pdf_file_1")[0].files[0];

            if (selectedFile) {
                formData.append('file', selectedFile);
            }

            formData.append('status', status);
            formData.append('stock_id', stock_id);
            formData.append('passed_value', parseFloat(passed) || 0);
            formData.append('reject_value', parseFloat(reject) || 0);
            formData.append('all_link', all_link);
            formData.append('received_qty', received_qty);

            $.ajax({
                url: "ajaxs/qa/ajax-post-qa.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    console.log("Processing...");
                },
                success: function(response) {
                    let responseObj = JSON.parse(response);
                    console.log(responseObj);
                    if (responseObj["status"] === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: responseObj["message"],
                            timer: 3000,
                            showConfirmButton: false
                        });


                        var current_total_remaining = remain_qty - (parseFloat(passed) + parseFloat(reject));
                        var current_total_passed = previous_passed + parseFloat(passed);
                        var current_total_reject = previous_rejected + parseFloat(reject);


                        $("#total_passed_qty").val(current_total_passed);
                        $("#total_reject_qty").val(current_total_reject);
                        $("#remain_qty").val(current_total_remaining);
                        $("#htmlPassed").html("Passed : " + decimalQuantity(current_total_passed));
                        $("#htmlRejected").html("Failed : " + decimalQuantity(current_total_reject));
                        $("#htmlRemaining").html("Checked Required : " + decimalQuantity(current_total_remaining));
                    }
                },
                error: function(e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Mapping failed, please try again!',
                        timer: 3000,
                        showConfirmButton: false
                    });
                    console.log("Error: " + e.message);
                },
                complete: function() {
                    clearItemStatus(); 
                    checkFormStatus(); // Recheck the form status after submission

                    // Hide loader modal after request completes
                    // $("#viewGlobalModal").modal("hide");
                }
            });
        }
    });
});
</script>