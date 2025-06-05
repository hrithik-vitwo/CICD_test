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
if (!isset($_COOKIE["cookieQaRejected"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieQaRejected", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Name',
        'slag' => 'item.itemName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Batch',
        'slag' => 'stocklog.logRef',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Fail Qty',
        'slag' => 'failQty',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Date',
        'slag' => 'stocklog.bornDate',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'PO Number',
        'slag' => 'grn.grnPoNumber',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'INV Number',
        'slag' => 'grn.vendorDocumentNo',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Name',
        'slag' => 'grn.vendorName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor code',
        'slag' => 'grn.vendorCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
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
                                                <a href="recieved-item.php" class=""><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>All
                                                </a>
                                                <a href="received-items-rejected-list.php" class="filter-link active"><ion-icon name="list-outline" role="img" class="md hydrated" aria-label="list outline"></ion-icon>Rejected
                                                </a>
                                            </div>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()">
                                                <ion-icon name="expand-outline"></ion-icon>
                                            </button>
                                            <button type="button" id="revealList" class="page-list">
                                                <ion-icon name="funnel-outline"></ion-icon>
                                            </button>
                                            <div id="modal-container">
                                                <div class="modal-background">
                                                    <div class="modal">
                                                        <button class="btn-close-modal" is="closeFilterModal">
                                                            <ion-icon name="close-outline"></ion-icon>
                                                        </button>
                                                        <h5>Filter Pages</h5>

                                                        <h5>Search and Export</h5>
                                                        <div class="filter-action filter-mobile-search mobile-page">
                                                            <a type="button" class="btn add-col setting-menu"
                                                                data-toggle="modal" data-target="#myModal1"> <ion-icon
                                                                    name="settings-outline"></ion-icon></a>
                                                            <div class="filter-search">
                                                                <div class="icon-search" data-toggle="modal"
                                                                    data-target="#btnSearchCollpase_modal">
                                                                    <ion-icon name="filter-outline"></ion-icon>
                                                                    Advance Filter
                                                                </div>
                                                            </div>
                                                            <div class="exportgroup mobile-page mobile-export">
                                                                <button class="exceltype btn btn-primary btn-export"
                                                                    type="button">
                                                                    <ion-icon name="download-outline"></ion-icon>
                                                                </button>
                                                                <ul class="export-options">
                                                                    <li>
                                                                        <button class="ion-paginationlistnew">
                                                                            <ion-icon name="list-outline"
                                                                                class="ion-paginationlistnew md hydrated"
                                                                                id="exportAllBtn" role="img"
                                                                                aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button class="ion-fulllistnew">
                                                                            <ion-icon name="list-outline"
                                                                                class="ion-fulllistnew md hydrated"
                                                                                role="img"
                                                                                aria-label="list outline"></ion-icon>Download
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
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
                                                    <select name="" id="QaRejectListLimit" class="custom-select">
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
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieQaRejected"], true) ?? [];

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
                                                                            if ($columnIndex == 0 || $columnIndex === 4) {
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
                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">

                                    <div class="matrix-accordion p-0" id="accordionQuality">
                                        <div class="item-status d-flex gap-4">
                                            <table class="qualityModalTable">
                                                <thead>
                                                    <tr>
                                                        <th>SL.</th>
                                                        <th>Doc No.</th>
                                                        <th>Passed</th>
                                                        <th>Rejected</th>
                                                        <th>Status</th>
                                                        <th>Done By</th>
                                                        <th>Done On</th>
                                                        <th>Retested &amp; Passed</th>
                                                        <th>Remarks</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="rejectListFrmTableBody">

                                                </tbody>
                                            </table>

                                        </div>

                                    </div>


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
                                    <!-- <div class="modal-footer">
                                                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    <button type="button" class="btn btn-primary">Save changes</button>                                                                                                                            </div> -->
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
                <div class="modal-footer"></div>
            </div>
            <!--/.Content-->
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

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookieQaRejected');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/qa/ajax-received-items-rejected-all.php",
                dataType: 'json',
                data: {
                    act: 'QARejectedList',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit
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
                                `<a href="#" class="soModal" data-stocklogid=${value['stocklog.stockLogId']} data-itemCode = ${value['item.itemCode']}  data-id="${value.qa_log_Id}">${value['item.itemCode']}</a>`,
                                value['item.itemName'],
                                value['stocklog.logRef'],
                                value['failQty'],
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
                                            <button class="soModal" data-stocklogid=${value['stocklog.stockLogId']} data-itemCode = ${value['item.itemCode']} data-id="${value.qa_log_Id}">
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
                        $('#yourDataTable_paginate').remove();
                        $('#limitText').remove();
                    }
                }
            });
        }

        fill_datatable();




        $(document).on("click", ".ion-paginationlistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieQaRejected')
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
                url: "ajaxs/qa/ajax-received-items-rejected-all.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieQaRejected'),
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
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#QaRejectListLimit").val();
            //    console.log(limitDisplay);
            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function() {

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

                    if ((columnSlag === 'updated_at' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
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

                fill_datatable(formDatas = formInputs);
                $("#myForm")[0].reset();
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
                        act: 'QaRejected',
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

<!-- datatable and modal portion script ⬆️ -->

<script>
    $(document).ready(function() {

        $(document).on("click", ".soModal", function() {


            $('#viewGlobalModal').modal('show');
            $('.ViewfirstTab').tab('show');
            $(".classic-view").html('');

            let stockLogid = $(this).data('stocklogid');
            let qa_log_Id = $(this).data('id');
            let itemCode = $(this).data('itemcode');
            $('.auditTrail').attr("data-ccode", itemCode);
            $('.relativeHistory').attr('data-stocklogid', stockLogid);

            let globalReceived = '';


            $.ajax({
                type: "GET",
                url: "ajaxs/modals/qa/ajax-received-items-rejected-modal.php",
                dataType: 'json',
                data: {
                    act: "rejectedlistModal",
                    stocklogid: stockLogid
                },
                beforeSend: function() {
                    $('#rejectListFrmTableBody').html('');
                    // let loader = `<div class="load-wrapp" id="globalModalLoader">
                    //             <div class="load-1">
                    //                 <div class="line"></div>
                    //                 <div class="line"></div>
                    //                 <div class="line"></div>
                    //             </div>
                    //           </div>`;

                    // $('#viewGlobalModal .modal-body').append(loader);
                },
                success: function(value) {

                    if (value.status) {
                        var responseObj = value.data;


                        responseObj.forEach(function(one_data) {

                            let row = `
                        <tr>
                            <input type="hidden" value="${one_data.passed}" id="previous_passed_hidden_${one_data.qa_log_Id}">
                            <input type="hidden" value="${one_data.rejected}" id="previous_rejected_hidden_${one_data.qa_log_Id}">
                            <input type="hidden" value="${one_data.sl_no}" id="parent_serial_hidden_${one_data.qa_log_Id}">
                            
                            <td>${one_data.sl_no}</td>
                            <td>${one_data.doc_no}</td>
                            <td id="previous_passed_td_${one_data.qa_log_Id}">${(one_data.passed)}</td>
                            <td id="previous_rejected_td_${one_data.qa_log_Id}">${(one_data.rejected)}</td>
                            <td>${one_data.status}</td>
                            <td>${(one_data.created_by)}</td>
                            <td>${(one_data.created_at)}</td>
                            
                            <td><input type="number" id="input_passed_${one_data.qa_log_Id}" name="passed" class="form-control"></td>
                            <td><textarea rows="1" cols="50" id="input_remarks_${one_data.qa_log_Id}" name="remarks" class="form-control"></textarea></td>
                            <td><button type="button" id="submitid_${one_data.qa_log_Id}" class="btn btn-primary submit_frm">Release</button></td>
                        </tr>
                    `;

                            $('#rejectListFrmTableBody').append(row);

                        });
                        checkRowStatus();
                        // $('#globalModalLoader').remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // $('#globalModalLoader').remove();
                }
            });

            $.ajax({
                type: "GET",
                url: "ajaxs/modals/qa/ajax-received-items-rejected-modal.php",
                dataType: 'json',
                data: {
                    act: "rejectedlistSpecificationModal", // Action name for rejected list specification modal
                    qa_log_Id: qa_log_Id // Passing the QA log ID to fetch specific data
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
                type: "GET",
                url: "ajaxs/modals/qa/ajax-received-items-rejected-modal.php",
                dataType: 'json',
                data: {
                    act: "modalHeader",
                    stocklogid: stockLogid,
                    qa_log_Id: qa_log_Id
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
                        globalReceived = responseObj.itemQty;

                        $('#vendorName').text(responseObj.vendorName);
                        $('#vendorCode').html(responseObj.vendorCode);
                        $('#invNumber').html(responseObj.invNumber);
                        $('#totalreq').html('Total Received : ' + responseObj.itemQty);
                        $('#htmlPassed').html('Passed : ' + responseObj.passedQty);
                        $('#htmlRejected').html('Failed : ' + responseObj.rejectedQty);
                        $('#htmlRemaining').html('Checked Required : ' + responseObj.remainingQty);


                        $(".created_by_trail").html(responseObj.createdBy + "<span class='font-bold text-normal'> on </span>" + responseObj.createdAt);
                        $(".updated_by").html(responseObj.updatedBy + "<span class='font-bold text-normal'> on </span>" + responseObj.updatedAt);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });



        });

        $(document).on("click", ".relativeHistory", function() {
            // alert("called")
            let stocklogid = $(this).data('stocklogid')
            $.ajax({
                type: "POST",
                url: "ajaxs/modals/qa/ajax-received-items-rejected-modal.php",
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
                        <td>${row.status_text}</td>
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



        function checkRowStatus() {
            $('#rejectListFrmTableBody tr').each(function() {
                const row = $(this);
                let hasNumber = false;

                row.find('input[type="number"]').each(function() {
                    if ($(this).val().trim() !== '') {
                        hasNumber = true;
                        return false; // exit inner loop
                    }
                });

                // Enable or disable the submit button in this row
                row.find('.submit_frm').prop('disabled', !hasNumber);
            });
        }

        // Bind to inputs inside each row
        $('#rejectListFrmTableBody').on('input change keyup', 'input[type="number"]', function() {
            checkRowStatus();
        });

        $(document).on("click", ".submit_frm", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];

            console.log(rowNo);
            var passed = $(`#input_passed_${rowNo}`).val();
            var remarks = $(`#input_remarks_${rowNo}`).val();

            var stock_id = $(`#stock_id_${rowNo}`).val();
            let passed_value = (parseFloat(passed) > 0) ? parseFloat(passed) : 0;
            var previous_passed = (parseFloat($(`#previous_passed_hidden_${rowNo}`).val()) > 0) ? parseFloat($(`#previous_passed_hidden_${rowNo}`).val()) : 0;
            var previous_rejected = (parseFloat($(`#previous_rejected_hidden_${rowNo}`).val()) > 0) ? parseFloat($(`#previous_rejected_hidden_${rowNo}`).val()) : 0;
            var parent_serial_hidden = $(`#parent_serial_hidden_${rowNo}`).val();

            var total_passed = (parseFloat($(`#total_passed_qty_${parent_serial_hidden}`).val()) > 0) ? parseFloat($(`#total_passed_qty_${parent_serial_hidden}`).val()) : 0;
            var total_rejected = (parseFloat($(`#total_reject_qty_${parent_serial_hidden}`).val()) > 0) ? parseFloat($(`#total_reject_qty_${parent_serial_hidden}`).val()) : 0;

            var updated_passed_value = previous_passed + passed_value;
            var updated_rejected_value = previous_rejected - passed_value;

            var updated_total_passed_value = total_passed + passed_value;
            var updated_total_reject_value = total_rejected - passed_value;

            if (passed_value > previous_rejected) {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `error`,
                    title: `&nbsp;Please Give Passed Quantity less than Rejected Quantity!`
                });
                console.log("error: " + e.message);
            } else {

                var formData = new FormData();

                formData.append('passed_value', passed_value);
                formData.append('qa_log_id', rowNo);
                formData.append('remarks', remarks);

                $.ajax({
                    url: "ajaxs/qa/ajax-post-reject.php",
                    type: "POST",
                    data: formData,
                    processData: false, // Prevent jQuery from automatically processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    beforeSend: function() {
                        console.log("Mapping...");
                    },
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        if (responseObj["status"] == "success") {
                            let mapData = responseObj["data"];
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


                            //HTML Modification



                            $(`#htmlPassed`).html("Passed : " + decimalQuantity(updated_passed_value));
                            $(`#htmlRejected`).html("Failed : " + decimalQuantity(updated_rejected_value));
                            $(`#previous_passed_hidden_${rowNo}`).val(updated_passed_value);
                            $(`#previous_rejected_hidden_${rowNo}`).val(updated_rejected_value);
                            $(`#previous_passed_td_${rowNo}`).html(decimalQuantity(updated_passed_value));
                            $(`#previous_rejected_td_${rowNo}`).html(decimalQuantity(updated_rejected_value));
                            // $(`#total_passed_qty_${parent_serial_hidden}`).val(updated_total_passed_value);
                            // $(`#total_reject_qty_${parent_serial_hidden}`).val(updated_total_reject_value);



                            if (passed_value == previous_rejected) {
                                $(this).parent().parent().remove();
                            }

                        }

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
                    },
                    complete: function() {
                        // Hide loader modal after request completes
                        $('#rejectListFrmTableBody input[type="number"]').val('');
                        $('#rejectListFrmTableBody textarea').val('');
                        checkRowStatus()
                    }
                });
                if (passed_value == previous_rejected) {
                    $(this).parent().parent().remove();
                }




            }



        });

    });
</script>



<!-- -----fromDate todate input add--- -->


<script>
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

    });
</script>


<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>

<!-- other params isset script portion here  -->

<script>
    function table_settings() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }
    }
</script>


<!-- other portion isset script portion here ⬆️ -->



<script>
    function leaveInput(el) {
        if (el.value.length > 0) {
            if (!el.classList.contains('active')) {
                el.classList.add('active');
            }
        } else {
            if (el.classList.contains('active')) {
                el.classList.remove('active');
            }
        }
    }

    var inputs = document.getElementsByClassName("m-input");
    for (var i = 0; i < inputs.length; i++) {
        var el = inputs[i];
        el.addEventListener("blur", function() {
            leaveInput(this);
        });
    }

    // *** autocomplite select *** //
    wow = new WOW({
        boxClass: 'wow', // default
        animateClass: 'animated', // default
        offset: 0, // default
        mobile: true, // default
        live: true // default
    })
    wow.init();
</script>

<script>
    $(document).ready(function() {


        $(document).on("click", "#btnSearchCollpase", function() {
            sec = document.getElementById("btnSearchCollpase").parentElement;
            coll = sec.getElementsByClassName("collapsible-content")[0];

            if (sec.style.width != '100%') {
                sec.style.width = '100%';
            } else {
                sec.style.width = 'auto';
            }

            if (coll.style.height != 'auto') {
                coll.style.height = 'auto';
            } else {
                coll.style.height = '0px';
            }

            $(this).children().toggleClass("fa-search fa-times");

        });




    });
</script>



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


<script>
    function openFullscreen() {
        var elem = document.getElementById("listTabPan")

        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                /* Safari */
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                /* IE11 */
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                /* Safari */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                /* IE11 */
                document.msExitFullscreen();
            }
        }
    }

    document.addEventListener('fullscreenchange', exitHandler);
    document.addEventListener('webkitfullscreenchange', exitHandler);
    document.addEventListener('MSFullscreenChange', exitHandler);

    function exitHandler() {
        if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) {
            $(".content-wrapper").removeClass("fullscreen-mode");
        } else {
            $(".content-wrapper").addClass("fullscreen-mode");
        }
    }
</script>