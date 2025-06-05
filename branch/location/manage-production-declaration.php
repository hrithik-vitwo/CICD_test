<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");

require_once("../../app/v1/functions/branch/func-bom-controller.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../app/v1/functions/branch/func-production-order-controller.php");
require_once("../../app/v1/functions/branch/func-stock-controller.php");


require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="<?= BASE_URL ?>public/assets/simple-tree-table/dist/jquery-simple-tree-table.js"></script>



<?php
require_once("bom/controller/bom.controller.php");
require_once("bom/controller/mrp.controller.php");
include_once("bom/controller/consumption.controller.php");

$productionOrderController = new ProductionOrderController();
$goodsBomController = new GoodsBomController();
$accountingControllerObj = new Accounting();


if (isset($_POST['addNewProduction'])) {
    // console($_POST);
    $productionOrder = $productionOrderController->createProduction($_POST);
    swalAlert($productionOrder["status"], ucfirst($productionOrder["status"]), $productionOrder["message"], BASE_URL . "branch/location/manage-production-order.php");
}

if (isset($_GET["consumption-preview"])) {
    require_once("components/production/production-order-barcode-and-consumption.php");
} else {

    $pageName =  basename($_SERVER['PHP_SELF'], '.php');

    if (!isset($_COOKIE["cookiesManageProdDeclair"])) {
        $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
        $settingsCheckbox_concised_view = unserialize($settingsCh);
        if ($settingsCheckbox_concised_view) {
            setcookie("cookiesManageProdDeclair", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
        }
    }

    // export download file name section
    $originalFileName = basename($_SERVER['PHP_SELF']);
    $fileNameWithoutExtension = explode("-", pathinfo($originalFileName, PATHINFO_FILENAME), 2)[1];
    $currentDateTime = date('Y-m-d_H-i-s');
    $newFileName = 'export_' . $fileNameWithoutExtension . '_' . $currentDateTime;
    $newFileNameDownloadall = 'download_' . $fileNameWithoutExtension . $currentDateTime;

    //administratorLocationAuth();
    if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
        echo "Session Timeout";
        exit;
    }

    // $templateSalesOrderControllerObj = new TemplateSalesOrderController();


    $columnMapping = [
        [
            'name' => '#',
            'slag' => 'sl_no',
            'icon' => '',
            'dataType' => 'number'
        ],
        [
            'name' => 'Sub Production Order',
            'slag' => 'pOrder.subProdCode',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'Item Type',
            'slag' => 'goodTypes.goodTypeName',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'Item Code',
            'slag' => 'items.itemCode',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'Item Name',
            'slag' => 'items.itemName',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'Ref/SO',
            'slag' => 'pOrder.prodCode',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'MRP Code',
            'slag' => 'pOrder.mrp_code',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'Quantity',
            'slag' => 'pOrder.prodQty',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'number'
        ],
        [
            'name' => 'Remain Qty',
            'slag' => 'pOrder.remainQty',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'number'
        ],
        [
            'name' => 'Require Date',
            'slag' => 'pOrder.expectedDate',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'date'
        ],
        [
            'name' => 'Work Center',
            'slag' => 'wc.work_center_name',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'Table',
            'slag' => 'table_master.table_name',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'Realease Status',
            'slag' => 'pOrder.status',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'MRP Status',
            'slag' => 'pOrder.mrp_status',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ],
        [
            'name' => 'Created Date',
            'slag' => 'pOrder.created_at',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'date'
        ],
        [
            'name' => 'Created By',
            'slag' => 'pOrder.created_by',
            'icon' => '<ion-icon name="location-outline"></ion-icon>',
            'dataType' => 'string'
        ]

    ];


?>

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
                                                    <h3 class="card-title mb-0">Production Declaration</h3>
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
                                                            <div class="page-list-filer filter-list mobile-page mobile-filter-list">
                                                                <?php include("common/soCommonList.php"); ?>
                                                            </div>
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
                                                                                <ion-icon name="list-outline" class="ion-paginationliststock md hydrated" id="exportAllBtn" role="img" aria-label="list outline"></ion-icon>Export
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <button>
                                                                                <ion-icon name="list-outline" class="ion-fullliststock  md hydrated" role="img" aria-label="list outline"></ion-icon>Download
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
                                                            <button class="ion-paginationliststock">
                                                                <ion-icon name="list-outline" class="ion-paginationliststock md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                            </button>
                                                        </li>
                                                        <li>

                                                            <button class="ion-fullliststock">
                                                                <ion-icon name="list-outline" class="ion-fullliststock md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <!-- <a href="direct-create-invoice.php?sales_order_creation" class="btn btn-create" type="button">
                                                    <ion-icon name="add-outline"></ion-icon>
                                                    Create
                                                </a> -->
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
                                                                                if ($columnIndex === 0 || $column['name'] == 'MRP Status') {
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
                                                                        <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span id="stockQtyNav"></span></p>
                                                                        <p class="info-detail qty"><ion-icon name="albums-outline"></ion-icon><span id="mrpStatusNav"></span></p>
                                                                        <p class="info-detail qty"><ion-icon name="albums-outline"></ion-icon><span id="statusNav"></span></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-body">
                                                                <nav>
                                                                    <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                                        <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                                                                        <button class="nav-link classicview-btn classicview-link" id="nav-declaration-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-declaration" type="button" role="tab" aria-controls="nav-declaration" aria-selected="true"><ion-icon name="print-outline"></ion-icon>Declarations</button>
                                                                        <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                                    </div>
                                                                </nav>
                                                                <div class="tab-content global-tab-content" id="nav-tabContent">

                                                                    <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                                        <div id="prodDecStatusActive">

                                                                            <form action="?consumption-preview" method="post" class="h-100">
                                                                                <input type="hidden" name="soProdId" id="prodId" value="">
                                                                                <input type="hidden" name="soSubProdId" id="subProdId" value="">
                                                                                <input type="hidden" name="soSubProdCode" id="subProdCode" value="">
                                                                                <input type="hidden" name="soProdCode" id="prodCode" value="">
                                                                                <input type="hidden" name="soProdCreatedDate" id="prodCreateDate" value="">
                                                                                <input type="hidden" name="itemCode" id="itemCode" value="">
                                                                                <input type="hidden" name="itemId" id="itemId" value="">
                                                                                <input type="hidden" name="itemUom" id="itemUom" value="">
                                                                                <input type="hidden" name="mrpStatus" id="mrpStatus" value="">
                                                                                <input type="hidden" name="remainQty" id="remainQty" value="">
                                                                                <div class="row p-0 m-0">
                                                                                    <div class="col-md-2"><label for="" class="d-flex gap-1"><input type="checkbox" class="batchCheckBox" value="1" name="activeBatch" id="productionDeclareCheck">Batch Number </label> <input type="text" name="productionDeclareBatch" value="PRODXXXXXXXXX" class="productionDeclareBatch form-control" id="productionDeclareBatch" readonly></div>
                                                                                    <div class="col-md-2"><label for="">Declare Date</label> <input type="date" name="productionDeclareDate" value="<?= date("Y-m-d") ?>" class="productionDeclareDate form-control" id="productionDeclareDate" required></div>
                                                                                    <div class="col-md-2"><label for="">Declare Quantity</label> <input type="number" step="any" min="1" max="" id="productionQuantity" name="productionQuantity" value="" placeholder="eg. " class="productionQuantity form-control" required> <small class="text-danger" id="productionQuantityWarningText"></small></div>
                                                                                    <div class="col-md-2">
                                                                                        <label for=""> Dest. Storage Loc.</label>
                                                                                        <select class="form-control" name="productionDeclareLocation" id="defaultStorageLoc" required>
                                                                                            <option value="">Select Location</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-md-2"><label for="">Remain Quantity</label> <input type="number" value="" class="form-control" id="remainingQty" disabled></div>
                                                                                    <div class="col-md-2"><label for="">Order Quantity</label> <input type="number" value="" class="form-control" id="prodQty" disabled></div>
                                                                                </div>
                                                                                <hr>
                                                                                <div id="productionOrderMrpDetailsDiv">
                                                                                    <!-- Data will be coming from the api -->
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                        <div id="prodDecStatusClosed">
                                                                            <p class='text-center'>This production declaration is closed!</p>
                                                                        </div>



                                                                    </div>
                                                                    <div class="tab-pane declaration-pane fade" id="nav-declaration" role="tabpanel" aria-labelledby="nav-declaration-tab">
                                                                        <div>
                                                                            <table class="table defaultDataTable table-hover">
                                                                                <thead>
                                                                                    <tr class="alert-light">
                                                                                        <th>Sl</th>
                                                                                        <th>Decl. Code</th>
                                                                                        <th>Prod. Code</th>
                                                                                        <th>Sub. Prod. Code</th>
                                                                                        <th>Item Qty.</th>
                                                                                        <th>Date</th>
                                                                                        <th>Status</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="declarationList">

                                                                                </tbody>
                                                                            </table>
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

    <script>

$(document).on("click", "#serach_reset", function(e) {
      e.preventDefault();
      $("#myForm")[0].reset();
      $("#serach_submit").click();
    });

    // Enter to search
    $(document).on("keypress", "#myForm input", function(e) {
      if (e.key === "Enter") {
        $("#serach_submit").click();
        e.preventDefault();
      }
    });
        // let csvContent;
        // let csvContentBypagination;
        let data;
        $(document).ready(function() {
            var indexValues = [];
            var dataTable;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;


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

            function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
                var fdate = "<?php echo $f_date; ?>";
                var to_date = "<?php echo $to_date; ?>";
                var comid = <?php echo $company_id; ?>;
                var locId = <?php echo $location_id; ?>;
                var bId = <?php echo $branch_id; ?>;
                var checkboxSettings = Cookies.get('cookiesManageProdDeclair');
                var notVisibleColArr = [];

                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-manage-prod-declaration.php",
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
                        let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;

                        // Append the new HTML to the modal-body element
                        $('#viewGlobalModal .modal-body').append(loader);
                    },
                    success: function(response) {
                        // console.log(response);
                        // csvContent = response.csvContent;
                        // csvContentBypagination = response.csvContentBypagination;

                        if (response.status) {
                            var responseObj = response.data;
                            $('#yourDataTable_paginate').show();
                            $('#limitText').show();
                            data = responseObj;
                            dataTable.clear().draw();
                            dataTable.columns().visible(false);
                            dataTable.column(-1).visible(true);

                            $.each(responseObj, function(index, value) {

                                let status = '';
                                if (value.status == 9) {
                                    status = `<div class="status-bg status-pending">Open</div>`;
                                } else if (value.status == 13) {
                                    status = `<div class="status-bg status-approved">Release</div>`;
                                } else if (value.status == 10) {
                                    status = `<div class="status-bg status-closed">Closed</div>`;
                                }

                                let mrpStatus = '';
                                if (value.mrpStatus == 'Created' || value.mrpStatus == 'created') {
                                    mrpStatus = `<p class="text-center"><ion-icon name="checkmark-outline"></ion-icon></p>`;
                                } else {
                                    mrpStatus = `<p class="text-center"><ion-icon name="timer-outline"></ion-icon></p>`;
                                }


                                dataTable.row.add([
                                    value.sl_no,
                                    `<a href="#" class="soModal"  data-id="${value.subProdId}" >${value["pOrder.subProdCode"]}</a>`,
                                    value["goodTypes.goodTypeName"],
                                    value["items.itemCode"],
                                    `<p class="pre-normal w-200">${value["items.itemName"]}</p>`,
                                    value["pOrder.prodCode"],
                                    value["pOrder.mrp_code"],
                                    `<p class="text-right">${decimalQuantity(value["pOrder.prodQty"])}</p>`,
                                    `<p class="text-right">${decimalQuantity(value["pOrder.remainQty"])}</p>`,
                                    formatDate(value["pOrder.expectedDate"]),
                                    value["wc.work_center_name"],
                                    value["table_master.table_name"],
                                    status,
                                    mrpStatus,
                                    formatDate(value["pOrder.created_at"]),
                                    value["pOrder.created_by"],
                                    ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                      <li>
                                         <button data-toggle="modal" class="editSobtn" data-id=${value.so_id} data-code="${value.so_no}"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                     </li>
                                    <li>
                                        <button class="soModal" data-toggle="modal" data-id=${value.so_id} data-code="${value.so_no}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                    </li>
                                    </ul>
                                </div>`
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

                        $("#globalModalLoader").remove();
                    },
                    complete: function() {
                        $("#globalModalLoader").remove();

                    },
                });
            }

            fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping);
            $(document).on("click", ".ion-paginationliststock", function(e) {
                $.ajax({
                    type: "POST",
                    url: "../common/exportexcel-new.php",
                    dataType: "json",
                    data: {
                        act: 'paginationlist',
                        data: JSON.stringify(data),
                        coloum: columnMapping,
                        sql_data_checkbox: Cookies.get('cookiesManageProdDeclair')
                    },
                    beforeSend: function() {
                        $('#loaderModal').show();
                        $('.ion-paginationliststock').prop('disabled', true)
                    },

                    success: function(response) {
                        console.log(response);
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
                        $('.ion-paginationliststock').prop('disabled', false);
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
                var limitDisplay = $("#itemsPerPage").val();
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
                        let value13 = $(`#value13_${columnIndex}`).val() ?? "";

                        if (columnSlag === 'delivery_date') {
                            values = value4;
                        } else if (columnSlag === 'pOrder.expectedDate') {
                            values = value9;
                        } else if (columnSlag === 'pOrder.created_at') {
                            values = value13;
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
            });

            $(document).on("click", ".ion-fullliststock", function(e) {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-manage-prod-declaration.php",
                    dataType: "json",
                    data: {
                        act: 'alldata',
                        formDatas: formInputs,
                        coloum: columnMapping,
                        sql_data_checkbox: Cookies.get('cookiesManageProdDeclair')
                    },
                    beforeSend: function() {
                        $('#loaderModal').show();
                        $('.ion-fullliststock').prop('disabled', true)
                    },
                    success: function(response) {
                        console.log(response);
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
                        $('.ion-fullliststock').prop('disabled', false)
                    }
                })

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
                            act: 'manageProdDeclaire',
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
                    inputId = "value_" + columnIndex;
                } else if (columnName === 'Require Date') {
                    inputId = "value9_" + columnIndex;
                } else if (columnName === 'Created Date') {
                    inputId = "value13_" + columnIndex;
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
    <!------------ modal ajax--------- -->
    <script>
        let sub_prod_id
        $(document).on("click", ".soModal", function() {
            $('#viewGlobalModal').modal('show');
            $('.ViewfirstTab').tab('show');
            sub_prod_id = $(this).data('id');
            console.log(sub_prod_id);
            // $('.auditTrail').attr("data-ccode", sub_prod_id);
            let prodQty;
            let remainQty;
            let status;
            let defaultStorageLocationdId;

            $.ajax({
                type: "GET",
                url: "ajaxs/modals/pp/ajax-manage-production-declaration-modal.php",
                dataType: 'json',
                data: {
                    act: "modalData",
                    sub_prod_id
                },
                beforeSend: function() {
                    let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;

                    $('#viewGlobalModal .modal-body').append(loader);
                    $('#itemTableBody').html('');
                    $("#prodDecStatusActive").hide();
                    $("#prodDecStatusClosed").hide();
                },
                success: function(value) {
                    console.log(value);
                    if (value.status) {
                        let responseObj = value.data;
                        let dataObj = responseObj.dataObj;
                        prodQty = dataObj.prodQty;
                        remainQty = dataObj.remainQty;
                        // deault storage location
                        defaultStorageLocationdId = responseObj.defaultStorageLocId;
                        $('.auditTrail').attr("data-ccode", dataObj.subProdCode);
                        $("#itemNameNav").html(dataObj.itemName);
                        $("#itemCodeNav").html(dataObj.itemCode);
                        $("#itemDescNav").html(dataObj.itemDesc);
                        $("#stockQtyNav").html(responseObj.itemStockQty);
                        $("#mrpStatusNav").html(dataObj.mrp_status);
                        $("#statusNav").html(dataObj.status);

                        if (dataObj.mrp_status == 'Created' && dataObj.status != 10) {

                            $("#prodDecStatusActive").show();
                            $("#prodDecStatusClosed").hide();
                            $("#prodId").val(dataObj.prod_id);
                            $("#subProdId").val(dataObj.sub_prod_id);
                            $("#subProdCode").val(dataObj.subProdCode);
                            $("#prodCode").val(dataObj.prodCode);
                            $("#prodCreateDate").val(dataObj.created_at);
                            $("#itemCode").val(dataObj.itemCode);
                            $("#itemName").val(dataObj.itemName);
                            $("#itemUom").val(dataObj.itemUom);
                            $("#productionQuantity").attr("max", dataObj.remainQty);
                            $("#productionQuantity").attr("data-id", dataObj.sub_prod_id);
                            $("#productionQuantity").val(decimalQuantity(dataObj.remainQty));
                            $("#remainingQty").val(decimalQuantity(dataObj.remainQty));
                            $("#prodQty").val(decimalQuantity(dataObj.prodQty));
                            $("#itemId").val(dataObj.itemId);
                            $("#mrpStatus").val(dataObj.mrp_status);
                            $("#remainQty").val(decimalQuantity(dataObj.remainQty));
                            $("#productionDeclareDate").attr("max", responseObj.max);
                            $("#productionDeclareDate").attr("min", responseObj.min);
                            getProductionOrderModalBomHtml();

                        }

                        if (dataObj.status == 10) {
                            $("#prodDecStatusActive").hide();
                            $("#prodDecStatusClosed").show();
                        }

                        $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                        $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

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

            $.ajax({
                type: "GET",
                url: "ajaxs/modals/pp/ajax-manage-production-declaration-modal.php",
                dataType: 'json',
                data: {
                    act: "storageLocation",
                },
                beforeSend: function() {},
                success: function(value) {
                    let response = value.data;
                    let output = [];
                    output.push(`<option value="">Select Location</option>`);
                    $.each(response, function(key, value) {
                        const selected = (value.storage_location_id === defaultStorageLocationdId) ? ' selected' : '';
                        output.push(`<option value="${value.storageLocationTypeSlug}" ${selected}>${value.storage_location_name}</option>`);
                    });
                    $('#defaultStorageLoc').html(output.join(''));
                },
                error: function(error) {
                    console.log(error);
                }
            });

            $.ajax({
                type: "GET",
                url: "ajaxs/modals/pp/ajax-manage-production-declaration-modal.php",
                dataType: 'json',
                data: {
                    act: "declarationList",
                    sub_prod_id
                },
                beforeSend: function() {
                    $("#declarationList").html('');

                },
                success: function(value) {
                    console.log(value);
                    if (value.status) {
                        $("#nav-declaration-tab").show();
                        let decList = value.data;
                        let sl = 0;
                        $.each(decList, function(index, val) {
                            sl = sl + 1;
                            let revAct = '';
                            status = val.status;

                            if (((prodQty != remainQty) || remainQty == 0) && status == 'active') {
                                revAct = `
                            <a style="cursor:pointer" data-id="${val.id}" class="btn btn-sm reverseProdDeclaration waves-effect waves-light" title="Reverse Now">
                                                                                                            <i class="far fa-undo po-list-icon"></i>
                                                                                                        </a>`;
                            }

                            let obj = `<tr>
                                    <td>${sl}</td>
                                    <td>${val.code}</td>
                                    <td>${val.prod_code}</td>
                                    <td>${val.sub_prod_code}</td>
                                    <td>${decimalQuantity(val.quantity)}</td>
                                    <td>${formatDate(val.created_at)}</td>
                                    <td>${capFirstLetter(val.status)}</td>
                                    <td>${revAct}</td>
                                </tr>
                        `;

                            $("#declarationList").append(obj);
                        });
                    } else {
                        $("#nav-declaration-tab").hide();

                    }

                },
                error: function(error) {
                    console.log(error);
                }
            });

        });
    </script>

    <!-- old code start -->
    <script>
        function getProductionOrderModalBomHtml() {
            let productionOrderId = parseInt($("#subProdId").val());
            let productionItemId = parseInt($("#itemId").val());
            let productionOrderMrpStatus = $("#mrpStatus").val();
            let productionOrderRemainQty = parseFloat($("#remainQty").val());
            let productionOrderDeclareQty = parseFloat($("#productionQuantity").val());
            let productionOrderDeclareDate = $("#productionDeclareDate").val();
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
                        act: "productionDeclaration"
                    },
                    beforeSend: function() {
                        $(`#productionOrderMrpDetailsDiv`).html(`<p class="text-warning">Getting Production Order MRP details...</p>`);
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
        $(document).on('click', '.batchCheckBox', function(e) {
            let $this = $(this); // Store the reference to $(this) for later use

            if ($(this).is(':checked')) {
                $(`#productionDeclareBatch`).removeAttr('readOnly');
                $(`#productionDeclareBatch`).val("");
            } else {
                $(`#productionDeclareBatch`).prop("readOnly", true);
                $(`#productionDeclareBatch`).val("PRODXXXXXXXXX");
            }

        })
        $(document).on("change", ".productionDeclareDate", function() {
            getProductionOrderModalBomHtml();
        });
        $(document).on('click', '.reverseProdDeclaration', function(e) {
            e.preventDefault(); // Prevent default click behavior
            var dep_keys = $(this).data('id');
            var $this = $(this); // Store the reference to $(this) for later use
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: 'You want to reverse this?',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Reverse'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        data: {
                            dep_keys: dep_keys,
                            dep_slug: 'reverseProdDeclaration'
                        },
                        url: 'ajaxs/ajax-reverse-post-new.php',
                        beforeSend: function() {
                            // $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                        },
                        success: function(response) {
                           
                            var responseObj = JSON.parse(response);
                            console.log(responseObj);
                            // if (responseObj.status == 'success') {
                            //     $this.parent().parent().find('.listStatus').html('Reverse');
                            //     $this.hide();
                            // } else {
                            //     $this.html('<i class="far fa-undo po-list-icon"></i>');
                            // }
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000
                            });
                            Toast.fire({
                                icon: responseObj.status,
                                title: ' ' + responseObj.message
                            }).then(function() {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });
        $(document).on("keyup", ".productionQuantity", function() {
            let prodId = $(this).data('id');
            let prodQuantity = parseFloat($(this).val());
            let remainingQty = parseFloat($(`#remainingQty`).val());
            prodQuantity = prodQuantity > 0 ? prodQuantity : 0;
            if (prodQuantity > remainingQty) {
                prodQuantity = 0;
                $(this).val(prodQuantity);
                $(`#productionQuantityWarningText`).html("Declare qty can't be greater than remaining");
            } else {
                $(`#productionQuantityWarningText`).html("");
            }
            $(`.productionOrderBomItemTrList_${prodId}`).each(function() {
                let totalConsumptionPerUnit = helperQuantity($(this).find(".totalConsumptionPerUnit").html());
                let totalConsumption = helperQuantity($(this).find(".totalConsumption").html());
                let totalAvailableStock = helperQuantity($(this).find(".totalAvailableStock").html());
                //$(this).find(".totalConsumption").html((totalConsumptionPerUnit * prodQuantity).toFixed(2));
                $(this).find(".totalConsumption").html(helperQuantity(totalConsumptionPerUnit * prodQuantity));
            });
        });
    </script>
    <!-- old code end -->
<?php

} ?>