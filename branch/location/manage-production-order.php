<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");

// old page script 

require_once("../../app/v1/functions/branch/func-bom-controller.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../app/v1/functions/branch/func-production-order-controller.php");
require_once("../../app/v1/functions/branch/func-stock-controller.php");

require_once("bom/controller/bom.controller.php");
require_once("bom/controller/mrp.controller.php");
require_once("bom/controller/mrprelease.controller.php");
include_once("bom/controller/consumption.controller.php");
include_once("bom/controller/consumptionbackflash.controller.php");

$productionOrderController = new ProductionOrderController();
$goodsBomController = new GoodsBomController();
$accountingControllerObj = new Accounting();


//  consumption - backflashes

if (isset($_GET["consumption-backflash"]) && isset($_POST["soProdId"])) {

    $consumptionBackFlashControllerObj = new ConsumptionBackFlashController();
    $backFlashObj = $consumptionBackFlashControllerObj->confirmConsumption($_POST);
    swalAlert($backFlashObj["status"], ucfirst($backFlashObj["status"]), $backFlashObj["message"], BASE_URL . "branch/location/manage-production-order.php");
    // console($backFlashObj);
    
}



if (!isset($_COOKIE["cookieProductionOrder"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieProductionOrder", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}

// export download file name section
$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;

//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    echo "Session Timeout";
    exit;
}


$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Production Order',
        'slag' => 'pOrder.porCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Type',
        'slag' => 'goodTypes.goodTypeName',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Code',
        'slag' => 'items.itemCode',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Name',
        'slag' => 'items.itemName',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Ref/SO',
        'slag' => 'pOrder.refNo',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'MRP Code',
        'slag' => 'pOrder.mrp_code',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Quantity',
        'slag' => 'pOrder.qty',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Remain Qty',
        'slag' => 'pOrder.remainQty',
        'icon' => '<ion-icon name="albums-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Require Date',
        'slag' => 'pOrder.expectedDate',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Created Date',
        'slag' => 'pOrder.created_at',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Created By',
        'slag' => 'pOrder.created_by',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Release Status',
        'slag' => 'pOrder.status',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'MRP Status',
        'slag' => 'pOrder.mrp_status',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ]

];

?>
<!-- script for tree table -->
<script src="<?= BASE_URL ?>public/assets/simple-tree-table/dist/jquery-simple-tree-table.js"></script>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<style>
    .global-view-modal .modal-body {
        overflow: auto;
    }
</style>
<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <?php  ?>
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
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space" style="width:100%">
                                        <div class="left-block">
                                            <div class="label-select">
                                                <h3 class="card-title mb-0">Production Order</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">

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
                                                            <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal1"> <ion-icon name="settings-outline"></ion-icon></a>
                                                            <div class="filter-search">
                                                                <div class="icon-search" data-toggle="modal" data-target="#btnSearchCollpase_modal">
                                                                    <ion-icon name="filter-outline"></ion-icon>
                                                                    Advance Filter
                                                                </div>
                                                            </div>
                                                            <div class="exportgroup mobile-page mobile-export">
                                                                <button class="exceltype btn btn-primary btn-export" type="button">
                                                                    <ion-icon name="download-outline"></ion-icon>
                                                                </button>
                                                                <ul class="export-options">
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline" class="ion-fulllistProductionOrder md hydrated" id="exportAllBtn" role="img" aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline" class="ion-paginationlistProductionOrder md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <!-- <a href="#" class="btn btn-create mobile-page mobile-create" type="button">
                                                                <ion-icon name="add-outline"></ion-icon>
                                                                Create
                                                            </a> -->
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
                                        <div class="tab-pane dataTableTemplate dataTable_stock fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
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
                                                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal1"> <ion-icon name="settings-outline"></ion-icon> Manage Column</a>
                                                <div class="length-row">
                                                    <span>Show</span>
                                                    <select name="" id="itemsPerPage" class="custom-select">
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
                                                    <div class="icon-search" data-toggle="modal" data-target="#btnSearchCollpase_modal">
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
                                                        <button class="ion-paginationlistProductionOrder">
                                                            <ion-icon name="list-outline" class="ion-paginationlistProductionOrder md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistProductionOrder">
                                                            <ion-icon name="list-outline" class="ion-fulllistProductionOrder md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="<?= BRANCH_URL ?>location/production-order-actions.php?create" class="btn btn-create" type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a>
                                            <table id="dataTable_detailed_view" class="table table-hover table-nowrap stock-new-table transactional-book-table">

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
                                                            <h4 class="modal-title text-sm">Detailed View Column Settings</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>
                                                        <form name="table_settings_detailed_view" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
                                                            <div class="modal-body" style="max-height: 450px;">
                                                                <!-- <h4 class="modal-title">Detailed View Column Settings</h4> -->
                                                                <input type="hidden" id="tablename" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                                <input type="hidden" id="pageTableName" name="pageTableName" value="ERP_TEST_<?= $pageName ?>" />
                                                                <div class="modal-body">
                                                                    <div id="dropdownframe"></div>
                                                                    <div id="main2">
                                                                        <div class="checkAlltd d-flex gap-2 mb-3 pl-2">
                                                                            <input type="checkbox" class="grand-checkbox" value="" />
                                                                            <p class="text-xs font-bold">Check All</p>
                                                                        </div>

                                                                        <table class="colomnTable">
                                                                            <?php
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieTableStockReport"], true) ?? [];

                                                                            foreach ($columnMapping as $index => $column) {

                                                                            ?>
                                                                                <tr>
                                                                                    <td valign="top">

                                                                                        <input type="checkbox" class="settingsCheckbox_detailed" name="settingsCheckbox[]" id="settingsCheckbox_detailed_view[]" value='<?= $column['slag'] ?>'>
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
                                                                <button type="submit" id="check-box-submt" name="check-box-submit" data-dismiss="modal" class="btn btn-primary">Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!---------------------------------Table Model End--------------------------------->

                                            <div class="modal " id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-sm" id="exampleModalLongTitle">Advanced Filter</h5>
                                                        </div>
                                                        <form id="myForm" method="post" action="">
                                                            <div class="modal-body">

                                                                <table>
                                                                    <tbody>
                                                                        <?php
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "<", ">", ">=", "<=", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex  => $column) {
                                                                            if ($columnIndex === 0 || $columnIndex == 13) {
                                                                                continue;
                                                                            } ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="icon-filter d-flex align-items-center gap-2">
                                                                                        <?= $column['icon'] ?>
                                                                                        <p id="columnName_<?= $columnIndex ?>"><?= $column['name'] ?></p>
                                                                                        <input type="hidden" id="columnSlag_<?= $columnIndex ?>" value="<?= $column['slag'] ?>">
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <select class="form-control selectOperator" id="selectOperator_<?= $columnIndex ?>" name="operator[]" val="">
                                                                                        <?php
                                                                                        if (($column['dataType'] === 'date')) {
                                                                                            $operator = array_slice($operators, -3, 3);
                                                                                            foreach ($operator as $oper) {
                                                                                        ?>
                                                                                                <option value="<?= $oper ?>"><?= $oper ?></option>
                                                                                            <?php
                                                                                            }
                                                                                        } elseif ($column['dataType'] === 'number') {
                                                                                            $operator = array_slice($operators, 2, 6);
                                                                                            foreach ($operator as $oper) {
                                                                                            ?>
                                                                                                <option value="<?= $oper ?>"><?= $oper ?></option>
                                                                                                <?php

                                                                                            }
                                                                                        } else {
                                                                                            $operator = array_slice($operators, 0, 2);
                                                                                            foreach ($operator as $oper) {
                                                                                                if ($oper === 'CONTAINS') {
                                                                                                ?>
                                                                                                    <option value="LIKE"><?= $oper ?></option>
                                                                                                <?php
                                                                                                } else { ?>

                                                                                                    <option value="NOT LIKE"><?= $oper ?></option>

                                                                                        <?php
                                                                                                }
                                                                                            }
                                                                                        } ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td id="td_<?= $columnIndex ?>">
                                                                                    <input type="<?= ($column['dataType'] === 'date') ? 'date' : 'input' ?>" data-operator-val="" name="value[]" class="fld form-control m-input" id="value_<?= $columnIndex ?>" placeholder="Enter Keyword" value="">
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" id="serach_reset" class="btn btn-primary" data-dismiss="modal">Reset</button>
                                                                <button type="submit" id="serach_submit" class="btn btn-primary" data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Global View start-->

                                            <div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="top-details">
                                                                <div class="left">
                                                                    <p class="info-detail amount"><ion-icon name="wallet-outline"></ion-icon><span id="itemCodeNav"></span></p>
                                                                    <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="itemNameNav"></span></p>
                                                                    <p class="info-detail ref-number"><ion-icon name="information-outline"></ion-icon><span id="itemDescNav"></span></p>
                                                                </div>
                                                                <div class="right">
                                                                    <p class="info-detail name"><ion-icon name="business-outline"></ion-icon>Stock as on <span id="" class="text-xs">(<?= formatDateORDateTime(date('d-m-Y')) ?>)</span> <span id="stockQtyNav"></span></p>
                                                                    <p class="info-detail qty"><ion-icon name="albums-outline"></ion-icon>MRP Status : <span id="mrpStatusNav"></span></p>
                                                                    <p class="info-detail qty"><ion-icon name="albums-outline"></ion-icon>UOM :<span id="statusNav"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <nav>
                                                                <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                                    <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                                                                    <button class="nav-link treeTable" id="nav-classicview-tab" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="print-outline"></ion-icon>Tree Table</button>
                                                                    <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                                </div>
                                                            </nav>
                                                            <div class="tab-content global-tab-content" id="nav-tabContent">

                                                                <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                                    <div class="d-flex nav-overview-tabs">
                                                                        <div class="action-btns display-flex-gap create-delivery-btn-sales" id="navBtn"></div>
                                                                    </div>

                                                                    <div class="text-left">
                                                                        <form action="?consumption-backflash" method="post" class="h-100">
                                                                            <div class="row p-0 m-0">
                                                                                <div class="col-md-3">Back Flash Declare Date: <input type="date" name="productionDeclareDate" value="<?= date("Y-m-d") ?>" class="productionDeclareDate form-control" id="productionDeclareDate" required></div>
                                                                                <div class="col-md-3">Back Flash Declare Quantity: <input type="number" step=".01" min="0.1" max="" id="productionQuantity" name="productionQuantity" value="" placeholder="eg. " class="productionQuantity form-control" required> <small class="text-danger" id="productionQuantityWarningText"></small></div>
                                                                                <div class="col-md-3">Remain Quantity: <input type="number" class="form-control" id="remainingQty" disabled></div>
                                                                                <div class="col-md-3">Order Quantity: <input type="number" id="orderQty" class="form-control" disabled></div>
                                                                                <input type="hidden" name="soProdId" id="soProdId">
                                                                                <input type="hidden" name="soProdCode" id="soProdCode">
                                                                                <input type="hidden" name="soProdRefNo" id="soProdRefNo">
                                                                                <input type="hidden" name="soProdCreatedDate" id="soProdCreatedDate">
                                                                                <input type="hidden" name="itemId" id="itemId">
                                                                                <input type="hidden" name="mrpStatus" id="mrpStatus">
                                                                            </div>
                                                                            <hr>
                                                                            <div id="productionOrderMrpDetailsDiv">
                                                                                <!-- Data will be coming from the api -->
                                                                            </div>
                                                                        </form>
                                                                    </div>

                                                                </div>
                                                                <div class="tab-pane classicview-pane fade" id="nav-classicview" role="tabpanel" aria-labelledby="nav-classicview-tab">
                                                                    <div class="text-left">
                                                                        <div class="d-flex">
                                                                            <span class="h5 font-weight-bold">MRP Preview</span>
                                                                            <div class="d-flex ml-auto action-btns-production">
                                                                                <div class="btn-group btn-group-toggle col-2 pr-0" data-toggle="buttons">
                                                                                    <label class="btn btn-secondary active waves-effect waves-light">
                                                                                        <input type="radio" class="expand_collapse" id="collapser" name="expand_collapse" value="collapse" autocomplete="off">Collapse
                                                                                    </label>
                                                                                    <label class="btn btn-secondary waves-effect waves-light">
                                                                                        <input type="radio" class="expand_collapse" id="expander" name="expand_collapse" value="expand" autocomplete="off" checked="">Expand
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                                                <table id="basic" class="table">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Material Details</th>
                                                                                            <th>Item Code</th>
                                                                                            <th>Progress</th>
                                                                                            <th>Required Quantity</th>
                                                                                            <th>Produced Quantity</th>
                                                                                            <th>Remaining Quantity</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody id="treeTableBody">
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                                                                    <div class="inner-content">
                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span></p>
                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"> </span></p>
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
                                                </div>
                                            </div>
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
</div>
</section>
<!-- /.content -->
</div>

<?php
require_once("../common/footer2.php");
?>

<!-----------mobile filter list------------>


<script>
    $(document).ready(function() {
        $("button.page-list").click(function() {
            let buttonId = $(this).attr("id");
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

<script>

$(document).on("click", "#serach_reset", function(e) {
      e.preventDefault();
      $("#myForm")[0].reset();
      $("#serach_submit").click();
    });

    // Enter to search
    $(document).on("keypress", "#myForm input", function(e) {
      if (e.key === "Enter") {
          e.preventDefault();
        $("#serach_submit").click();
      
      }
    });
    let csvContent;
    let csvContentBypagination;

    $(document).ready(function() {
        let indexValues = [];
        let dataTable;
        let columnMapping = <?php echo json_encode($columnMapping); ?>;


        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function(settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [],
                // select: true,
                "bPaginate": false,

            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        var allData;
        var dataPaginate;


        function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
            let fdate = "<?php echo $f_date; ?>";
            let to_date = "<?php echo $to_date; ?>";
            let comid = <?php echo $company_id; ?>;
            let locId = <?php echo $location_id; ?>;
            let bId = <?php echo $branch_id; ?>;
            let checkboxSettings = Cookies.get('cookieProductionOrder');
            let notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-production-order.php",
                dataType: 'json',
                data: {
                    act: 'tdata',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit,
                    columnMapping: columnMapping
                },
                beforeSend: function() {
                    $("#detailed_tbody").html(`<td colspan=17 class='else-td loading-td text-center'><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""><p>Data Loading ....</p></td>`);
                },
                success: function(response) {
                    console.log(response);
                    // csvContent = response.csvContent;
                    // csvContentBypagination = response.csvContentBypagination;

                    if (response.status) {
                        let responseObj = response.data;
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);

                        $.each(responseObj, function(index, value) {

                            let status = '';
                            if (value['pOrder.status'] == 'open') {
                                status = `<div class="status-bg status-pending">Open</div>`;
                            } else if (value['pOrder.status'] == 'Released Order') {
                                status = `<div class="status-bg status-approved">Release</div>`;
                            } else if (value['pOrder.status'] == 'closed') {
                                status = `<div class="status-bg status-closed">Closed</div>`;
                            }

                            let mrpStatus = '';
                            if (value['pOrder.mrp_status'] == 'Created' || value['pOrder.mrp_status'] == 'created') {
                                mrpStatus = `<p class="text-center"><ion-icon name="checkmark-outline"></ion-icon></p>`;
                            } else {
                                mrpStatus = `<p class="text-center"><ion-icon name="timer-outline"></ion-icon></p>`;
                            }

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.prodId}" data-toggle="modal" data-target="#viewGlobalModal">${value['pOrder.porCode']}</a>`,
                                value['goodTypes.goodTypeName'],
                                value['items.itemCode'],
                                `<p class="pre-normal w-200">${value['items.itemName']}</p>`,
                                value['pOrder.refNo'],
                                value['pOrder.mrp_code'],
                                value['pOrder.qty'],
                                value['pOrder.remainQty'],
                                value['pOrder.expectedDate'],
                                value['pOrder.created_at'],
                                value['pOrder.created_by'],
                                status,
                                mrpStatus,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>                                    
                                        <li>
                                            <button class="deleteSoBtn" data-toggle="modal"  data-id="${value.prodId}" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                        </li>
                                        
                                        <li>
                                            <button class="soModal" data-toggle="modal" data-id=${value.prodId}><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                        </li>
                                    </ul>
                                </div>`
                            ]).draw(false);

                            // <li>
                            //             <button data-toggle="modal" data-target="#editModal"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                            //         </li>
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            let checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function(index) {
                                let columnVal = $(this).val();
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

                            // console.log('Cookie value:', checkboxSettings);

                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });

                            // console.log('Cookie is blank.');
                        }
                    } else {

                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                        $('#yourDataTable_paginate').hide();
                        $('#limitText').hide();
                    }


                }
            });
        }

        fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping);


        $(document).on("click", ".ion-paginationlistProductionOrder", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieProductionOrder')
                },
                beforeSend:function(){
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-paginationlistProductionOrder').prop('disabled', true)
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
                    $('.ion-paginationlistProductionOrder').prop('disabled', false)
                }
            })

        });
        $(document).on("click", ".ion-fulllistProductionOrder", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-production-order.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieProductionOrder'),
                    formDatas: formInputs
                },

                beforeSend:function(){
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-fulllistProductionOrder').prop('disabled', true)
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
                    $('.ion-fulllistProductionOrder').prop('disabled', false);
                }
            })

        });


        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            let maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function(e) {
            e.preventDefault();
            let page_id = $(this).attr('id');
            let limitDisplay = $("#itemsPerPage").val();
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
                    let value9 = $(`#value9_${columnIndex}`).val() ?? "";
                    let value10 = $(`#value10_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'delivery_date') {
                        values = value4;
                    } else if (columnSlag === 'pOrder.expectedDate') {
                        values = value9;
                    } else if (columnSlag === 'pOrder.created_at') {
                        values = value10;
                    }

                    if ((columnSlag === 'delivery_date' || columnSlag === 'pOrder.expectedDate' || columnSlag === 'pOrder.created_at') && operatorName == "BETWEEN") {
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
                $(".m-input2").remove();
            });

            $(document).on("click", "#serach_reset", function(e) {
                e.preventDefault();
                $("#myForm")[0].reset();
                $("#serach_submit").click();
            });

            $(document).on("keypress", "#myForm input", function(e) {
                if (e.key === "Enter") {
                    $("#serach_submit").click();
                    e.preventDefault();
                }
            });
        });

        // -------------checkbox----------------------

        $(document).ready(function() {
            let columnMapping = <?php echo json_encode($columnMapping); ?>;

            let indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                let column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function() {
                let columnVal = $(this).val();
                // console.log(columnVal);

                let index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                // console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function() {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function() {
                    let columnVal = $(this).val();
                    // console.log(columnVal);
                    let index = columnMapping.findIndex(function(column) {
                        return column.slag === columnVal;
                    });
                    if ($(this).is(':checked')) {
                        indexValues.push(index);
                    } else {
                        let removeIndex = indexValues.indexOf(index);
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
            let tablename = $("#tablename").val();
            let pageTableName = $("#pageTableName").val();
            let settingsCheckbox = [];
            let fromData = {};
            $(".settingsCheckbox_detailed").each(function() {
                if ($(this).prop('checked')) {
                    let chkBox = $(this).val();
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
                        act: 'manageProductionOrder',
                        fromData: fromData
                    },
                    success: function(response) {
                        // console.log(response);
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

<!-- -----fromDate todate input add--- -->
<script>
    $(document).ready(function() {
        $(document).on("change", ".selectOperator", function() {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Delivery Date') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'Require Date') {
                inputId = "value9_" + columnIndex;
            } else if (columnName === 'Created Date') {
                inputId = "value10_" + columnIndex;
            }

            if ((columnName === 'Delivery Date' || columnName === 'Require Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input m-input2" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
        });

    });
</script>

<script>
    function openFullscreen() {
        let elem = document.getElementById("listTabPan")

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
<!------------ modal ajax--------- -->

<script>
    let prodId
    $(document).on("click", ".soModal", function() {

        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        prodId = $(this).data('id');
        console.log(prodId);

        $(".treeTable").attr("data-ccode", prodId);
        // $('.auditTrail').attr("data-ccode", prodId);


        $.ajax({
            type: "GET",
            url: "ajaxs/modals/pp/ajax-manage-production-order-modal.php",
            dataType: 'json',
            data: {
                act: "modalData",
                prodId
            },
            beforeSend: function() {
                $("#productionOrderMrpDetailsDiv").html('');
                $("#navBtn").html('');

                let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;
                $('#viewGlobalModal .modal-body').append(loader);

            },
            success: function(value) {

                if (value.status) {
                    let responseObj = value.data;
                    let dataObj = responseObj.dataObj;

                    // nav 
                    $("#itemNameNav").html(dataObj.itemName);
                    $("#itemCodeNav").html(dataObj.itemCode);
                    $("#itemDescNav").html(`Expected Date:- ${formatDate(dataObj.expectedDate)} || Validity Date:- ${formatDate(dataObj.validityperiod)??"-"}`);
                    // setTitleAttributeById('itemDescNav', dataObj.itemDesc);
                    $('.auditTrail').attr("data-ccode", dataObj.porCode);
                    $("#stockQtyNav").html(decimalQuantity(responseObj.itemStockQty));
                    $("#mrpStatusNav").html(dataObj.mrp_status);
                    $("#statusNav").html(dataObj.uom);

                    if (dataObj.status != "13") {
                        let releaseBtn = `<button class="btn btn-success" id="releaseOrderBtn" data-id="${btoa(dataObj.so_por_id)}">Release Order</button>`;
                        let obj = `<button class="btn btn-warning" id="runMrpBtn" data-id="${btoa(dataObj.so_por_id)}">Run MRP</button>`;
                        $("#navBtn").html(releaseBtn + obj);
                    }

                    // head part
                    $("#productionQuantity").attr("max", decimalQuantity(dataObj.remainQty));
                    $("#productionQuantity").attr("data-id", prodId);
                    $("#productionQuantity").val(decimalQuantity(dataObj.remainQty));
                    $("#remainingQty").val(decimalQuantity(dataObj.remainQty));
                    $("#orderQty").val(decimalQuantity(dataObj.qty));
                    $("#itemId").val(dataObj.itemId);
                    $("#mrpStatus").val(dataObj.mrp_status);
                    $("#soProdId").val(dataObj.so_por_id);
                    $("#soProdCode").val(dataObj.porCode);
                    $("#soProdRefNo").val(dataObj.refNo);
                    $("#soProdCreatedDate").val(dataObj.created_at);
                    $("#productionDeclareDate").attr("max", responseObj.max);
                    $("#productionDeclareDate").attr("min", responseObj.min);

                    $("#productionDeclareDate").attr("expdate", responseObj.dataObj.expectedDate);
                    $("#productionDeclareDate").attr("valdate", responseObj.dataObj.validityperiod);

                    // trail 
                    $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                    $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);


                    let expDate = new Date(responseObj.dataObj.expectedDate);
                    // let valDate = new Date(responseObj.dataObj.validityperiod);
                    let valDate = responseObj.dataObj.validityperiod ? new Date(responseObj.dataObj.validityperiod) : null;

                    let currentDate = new Date();

                    // if (currentDate >= expDate && currentDate <= valDate) {
                    if (currentDate >= expDate && (valDate === null || currentDate <= valDate)) {
                        getProductionOrderModalBomHtml(prodId);
                    } else {
                        $(`#productionOrderMrpDetailsDiv`).html("");
                    }

                } else {
                    console.log(value);
                }
                $("#globalModalLoader").remove();
            },
            complete: function() {
                $("#globalModalLoader").remove();

            },
            error: function(error) {
                console.log(error);
            }
        });

        // getProductionOrderModalBomHtml(prodId);

    });
    $(document).on('click', "#runMrpBtn", function() {
        let soPrId = $(this).data('id');
        let url = `<?= BRANCH_URL ?>location/production-order-actions.php?run-mrp=${soPrId}`;
        window.location.href = url;
    })
    $(document).on('click', "#releaseOrderBtn", function() {
        let soPrId = $(this).data('id');
        let url = `<?= BRANCH_URL ?>location/manage-production-order.php`;

        $.ajax({
            type: "GET",
            url: `<?= BASE_URL ?>branch/location/ajaxs/production/ajax-production-order-without-mrp.php`,
            dataType: 'json',
            data: {
                prodId: soPrId
            },
            beforeSend: function() {},
            success: function(response) {
                console.log(response)
                Swal.fire({
                    icon: response.status,
                    title: response.message,
                    timer: 1000,
                    showConfirmButton: false,
                })
                .then((result) => {
                    window.location.href = url;
                });
            }
        })

    })
</script>



<!-- old page script  start -->

<script>
    function getProductionOrderModalBomHtml(prodId = 0) {
        let productionOrderId = prodId;
        let productionItemId = $("#itemId").val();
        let productionOrderMrpStatus = $("#mrpStatus").val();
        let productionOrderRemainQty = $("#remainingQty").val();
        let productionOrderDeclareDate = $("#productionDeclareDate").val();
        let productionOrderDeclareQty = $("#productionQuantity").val();

        if (productionOrderMrpStatus == "Created") {

            $.ajax({
                type: "GET",
                url: `<?= BASE_URL ?>branch/location/ajaxs/production/ajax-production-order-bom-item-and-stocks.php`,
                data: {
                    productionOrderId,
                    productionItemId,
                    productionOrderMrpStatus,
                    productionOrderRemainQty,
                    productionOrderDeclareQty,
                    productionOrderDeclareDate,
                    act: "productionOrder"
                },
                beforeSend: function() {
                    $(`#productionOrderMrpDetailsDiv`).html("Getting Production Order MRP details...");
                },
                success: function(response) {
                    $(`#productionOrderMrpDetailsDiv`).html(response);
                },
                error: function(jqXHR, textStatus, errorTh) {
                    $(`#productionOrderMrpDetailsDiv`).html("Something went wrong, please try again!");
                    console.log("Something went wrong, please try again!", textStatus, jqXHR.status, errorTh);
                },
                complete: function(jqXHR, textStatus, errorTh) {
                    console.log("Completed the production order mrp details api call", textStatus, jqXHR.status);
                }
            });
        }

    }
    // Initialize TreeTable for all modals
    initTreeTable();

    // Click event for treeTable
    $(document).on('click', '.treeTable', function() {
        console.log("tree clicked");
        let ccode = $(this).data('ccode');
        console.log(ccode);
        $.ajax({
            url: `bom/ajax/get-prod-tree-view.php?production-order-id=${ccode}`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Render the tree when the data is successfully fetched
                console.log("Calling render function!");
                console.log(data);
                // Identify the modal based on the clicked link
                let modalId = `#treeTable${ccode}`;
                let productionOrderDetails = data[0];
                // console.log(productionOrderDetails['qty']);
                let treeTableBodyHtml = renderTreeTable(data, "0_0");
                const totalQuantity = productionOrderDetails['qty'];
                const remainQty = productionOrderDetails['remainingQty'];
                const chartValue = (remainQty / totalQuantity) * 100;
                // Set data in the specific modal
                $(`#treeTableBody`).html(`${treeTableBodyHtml}`);

                initTreeTable(modalId);
            },
            error: function(error) {
                console.error('Error fetching data:', error);
            }
        });
        // Prevent the default action of the anchor tag
        // e.preventDefault();
    });

    function initTreeTable(modalId) {
        $(`${modalId} #basic`).simpleTreeTable({
            expander: $(`${modalId} #expander`),
            collapser: $(`${modalId} #collapser`),
            store: 'session',
            storeKey: 'simple-tree-table-basic'
        });
    }

    function renderTreeTable(nodes, parentNodeId = null) {
        let html = '';
        //  console.log(nodes);
        nodes.forEach(function(node, index) {
            let nodeId = `${node.so_por_id}_${node.itemId}`;
            const totalQuantity = node.qty;
            const remainQty = node.remainingQty;
            const producedQty = totalQuantity - remainQty;
            const chartValue = (producedQty / totalQuantity) * 100;
            const progressBarId = `progressBar_${nodeId}`;

            html += `
                    <tr data-node-id="${nodeId}" ${ parentNodeId ? `data-node-pid="${parentNodeId}"` : ""}>
                        <td><span class="pre-normal">${node.itemName}</span></td>
                        <td><span class="pre-normal">${node.itemCode}</span></td>
                        <td>${chartValue}%
                        <div class="progress">
                        <div id="${progressBarId}" class="progress-bar" role="progressbar" aria-valuenow="${chartValue}" aria-valuemin="0" aria-valuemax="100" style="width:${chartValue}%">${chartValue}%</div>
                    </div>
                        </td>
                        <td class="text-right">${decimalQuantity(node.qty)}</td>
                        <td class="text-right">${decimalQuantity(node.qty-node.remainingQty)}</td>
                        <td class="text-right">${decimalQuantity(node.remainingQty)}</td>
                    </tr>`;
            if (node.childrens) {
                html += renderTreeTable(node.childrens, nodeId);
            }
            setTimeout(() => {
                nodes.forEach(function(node) {
                    const nodeId = `${node.so_por_id}_${node.itemId}`;
                    const progressBarId = `progressBar_${nodeId}`;
                    const progressBar = document.getElementById(progressBarId);

                    if (progressBar) {
                        progressBar.style.width = `${progressBar.getAttribute("aria-valuenow")}%`;
                    }
                });
            }, 0);

        });
        return html;
    }


    $(document).ready(function() {

        $(document).on('click', ".multipleMrpCheckBox", function() {
            let total = $('input[name="multipleMrp[]"]:checked').length;
            if (total > 0) {
                let productionOrderIds = [];
                $('input[name="multipleMrp[]"]:checked').each(function() {
                    productionOrderIds.push(parseInt($(this).val()));
                });
                let productionOrderIdsStr = productionOrderIds.join(",");
                $("#multipleMrpRunSpan").html(`<a href="manage-production-order.php?run-multi-mrp=${btoa(productionOrderIdsStr)}" id="multipleMrpRunBtn" class="btn btn-sm btn-primary">Run Multiple MRP</a>`);
            } else {
                $("#multipleMrpRunSpan").html('');
            }
        });

        $(document).on("keyup", ".productionQuantity", function() {
            // let prodId = ($(this).attr("id")).split("_")[1];
            let prodId = $(this).data('id');
            let prodQuantity = parseFloat($(this).val());
            let remainingQty = parseFloat($('#remainingQty').val());
            console.log(`${prodId} ${prodQuantity} ${remainingQty}`);
            prodQuantity = prodQuantity > 0 ? prodQuantity : 0;
            if (prodQuantity > remainingQty) {
                prodQuantity = 0;
                $(this).val(prodQuantity);
                $(`#productionQuantityWarningText`).html("Declare qty can't be greater than remaining");
            } else {
                $(`#productionQuantityWarningText`).html("");
            }
            $(`.productionOrderBomItemTrList_${prodId}`).each(function() {
                let totalConsumptionPerUnit = parseFloat($(this).find(".totalConsumptionPerUnit").html());
                let totalConsumption = parseFloat($(this).find(".totalConsumption").html());
                let totalAvailableStock = parseFloat($(this).find(".totalAvailableStock").html());
                $(this).find(".totalConsumption").html((totalConsumptionPerUnit * prodQuantity).toFixed(2));
            });
        });

        $(document).on("change", ".availableQuantity", function() {
            let randomRowNum = ($(this).attr("id")).split("_")[1];
            let storageLocationName = $(this).find(':selected').data('storagelocation');
            $(`#availableQuantityLocationName_${randomRowNum}`).val(storageLocationName);
            console.log(storageLocationName);
        });

        $(document).on("click", ".productionOrderDetailsModalBtn", function() {
            let prodId = ($(this).attr("id")).split("_")[1];
            getProductionOrderModalBomHtml(prodId);
        });

        $(document).on("change", ".productionDeclareDate", function() {
            let prodId = ($(this).attr("id")).split("_")[1];
            let value = $(this).val();

            let givenDate = new Date($(this).val());
            let expDate = new Date($(this).attr("expdate"));
            // let valDate = new Date($(this).attr("valdate"));
            /* For Sfg Modify this Condition */
            let valDate = $(this).attr("valdate") ? new Date($(this).attr("valdate")) : null;

            if (givenDate >= expDate && (valDate === null || givenDate <= valDate)) {
                getProductionOrderModalBomHtml(prodId);
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Invalid date provided. Please check the production order date and validity date.",
                    timer: 3000,
                    showConfirmButton: false,
                }).then(() => {
                    $(`#productionOrderMrpDetailsDiv`).html("");
                })
            }
        });
    });
</script>

<!-- old page script  end -->