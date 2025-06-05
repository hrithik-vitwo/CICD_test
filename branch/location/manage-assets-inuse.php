<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");

require_once("../common/sidebar.php");
require_once("../common/pagination.php");
// administratorLocationAuth();
// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../../app/v1/functions/common/templates/template-sales-order.controller.php");




if (!isset($_COOKIE["cookieAssetsInuse"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieAssetsInuse", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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

$templateSalesOrderControllerObj = new TemplateSalesOrderController();

$columnMapping = [
    [
        'name' => '<input type="checkbox" id="checkAll" name="checkall">',
        'slag' => 'checkbox',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Item Code',
        'slag' => 'itemCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Name',
        'slag' => 'itemName',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Cost Center',
        'slag' => 'cost_center',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Base UOM',
        'slag' => 'uom',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Put to use date',
        'slag' => 'use_date',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Historical Qty',
        'slag' => 'qty',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Historical Price',
        'slag' => 'rate',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Historical Total Price',
        'slag' => 'total_value',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Depriciated Price(latest price)',
        'slag' => 'depreciated_asset_value',
        'icon' => '<ion-icon name="albums-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Status',
        'slag' => 'items.status',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
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
<div class="content-wrapper report-wrapper is-asset-inuse vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">Asset In Use</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <div class="page-list-filer filter-list">
                                            </div>
                                            <?php require_once("components/mm/assets-tabs.php"); ?>
                                            <div class="col-lg-2 col-md-2 col-2 col-sm-2">
                                                <div class="form-input my-2">
                                                    <input type="date" name="useDate" id="useDate" class="form-control">
                                                </div>
                                            </div>
                                            <!-- <a href="#" class="btn btn-primary ">Change</a> -->
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
                                                                            <ion-icon name="list-outline" class="ion-fulllistAssetsUnuse md hydrated" id="exportAllBtn" role="img" aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline" class="ion-paginationlistAssetsUnuse md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <button class="btn btn-primary save-close-btn btn-xs float-right" id="depreciate" value="add_post" data-toggle="dep_modal">Depreciate</button>

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
                                                        <button class="ion-paginationlistAssetsUnuse">
                                                            <ion-icon name="list-outline" class="ion-paginationlistAssetsUnuse md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistAssetsUnuse">
                                                            <ion-icon name="list-outline" class="ion-fulllistAssetsUnuse md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a class="btn btn-create" id="depreciate" type="button">

                                                Depreciate
                                            </a>

                                            <table id="dataTable_detailed_view1" class="table table-hover table-nowrap stock-new-table transactional-book-table">

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

                                                                                //  checkbox column 
                                                                                // if ($index === ) {
                                                                                //     continue;
                                                                                // }

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
                                                                            if ($columnIndex === 0 || $columnIndex === 1 || $columnIndex === 4 || $columnIndex === 5) {
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
                                            <div class="modal right fade goods-item-modal global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="top-details">
                                                                <div class="left">
                                                                    <p class="info-detail amount" id="amounts">
                                                                        <ion-icon name="wallet-outline"></ion-icon>
                                                                        <span class="amount-value" id="amount"> </span>
                                                                    </p>
                                                                    <span class="amount-in-words" id="amount-words"></span>
                                                                    <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="po-numbers"> </span></p>
                                                                </div>
                                                                <div class="right">
                                                                    <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span id="cus_name"></span></p>
                                                                    <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <nav>
                                                                <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                                    <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                                                                    <button class="nav-link dephistory-btn dephistory-link" id="nav-dephistory-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-dephistory" type="button" role="tab" aria-controls="nav-dephistory" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Depreciation History</button>
                                                                    <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                                </div>
                                                            </nav>
                                                            <div class="tab-content global-tab-content" id="nav-tabContent">

                                                                <div class="tab-pane fade show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                                    <!-- <div class="d-flex nav-overview-tabs">

                                                                    </div> -->
                                                                    <div class="row" id="assetItem">
                                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                                                            <div class="view-block details-block">
                                                                                <div class="items-table">
                                                                                    <h6>Basic Details</h6>
                                                                                    <div class="item-details">
                                                                                        <div class="form-input">
                                                                                            <label for="">Asset Name</label>
                                                                                            <p><span id="itemName"></span></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Asset Description</label>
                                                                                            <p><span id="itemDesc"></span></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">HSN</label>
                                                                                            <p><span id="hsnCode"></span></p>
                                                                                            <p class="note"><span id="hsnDesc"></span></p>
                                                                                        </div>
                                                                                        <div class="d-flex justify-content-between mb-3">
                                                                                            <div class="form-input">
                                                                                                <label for="">Moving Weighted Price</label>
                                                                                                <p><span id="movWeightPrice"></span></p>
                                                                                            </div>
                                                                                            <div class="d-flex uom-flex">
                                                                                                <div class="form-input mt-0">
                                                                                                    <label for="">Base UOM</label>
                                                                                                    <p><span id="baseUom"></span></p>
                                                                                                </div>
                                                                                                <div class="form-input mt-0">
                                                                                                    <label for="">Alternate UOM</label>
                                                                                                    <p> <span id="altUom"></span></p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="view-block group-block">
                                                                                <div class="items-table">
                                                                                    <h6>Classification </h6>
                                                                                    <div class="item-details">
                                                                                        <div class="row">
                                                                                            <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-input">
                                                                                                    <label for="">Asset Type</label>
                                                                                                    <p><span id="itemType"></span></p>
                                                                                                </div>

                                                                                                <div class="form-input mt-5">
                                                                                                    <p class="note">Note : <span id="groupNote"></span></p>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-6 col-lg-6 col-md-6 col-sm-12">
                                                                                                <div class="form-input">
                                                                                                    <label for="">Asset Classification</label>
                                                                                                    <p id="assetClassification"></p>
                                                                                                </div>

                                                                                                <div class="form-input">
                                                                                                    <label for="">Gl Code</label>
                                                                                                    <p id="assetGlCode"></p>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="view-block specification-block">
                                                                                <div class="items-table">
                                                                                    <h6>Specification Details</h6>
                                                                                    <div class="spec-details" id="specificationDiv">
                                                                                        <div class="form-input">
                                                                                            <label for="">Net Weight</label>
                                                                                            <p><span id="netWeightSpec"></span> </p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Gross Weight</label>
                                                                                            <p><span id="grossWeightSpec"></span> </p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Height</label>
                                                                                            <p><span id="heightSpec"></span> </p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Width</label>
                                                                                            <p><span id="widthSpec"></span> </p>

                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Length</label>
                                                                                            <p><span id="lengthSpec"></span> </p>

                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Volume in CM3</label>
                                                                                            <p><span id="volumenCmSpec"></span></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Volume in M3</label>
                                                                                            <p><span id="volumenMSpec"></span></p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                                                            <div class="view-block item-image-block">
                                                                                <div class="items-table">
                                                                                    <h6>Asset Images</h6>
                                                                                    <div class="goods-img">
                                                                                        <div class="img-block" id="itemImages">

                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="view-block storage-block">
                                                                                <div class="items-table">
                                                                                    <h6>Storage Details</h6>
                                                                                    <div class="storage-details">
                                                                                        <div class="form-input">
                                                                                            <label for="storageControl">Storage Control</label>
                                                                                            <p id="storageControl"></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="maxStoragePeriod">Max Storage Period</label>
                                                                                            <p id="maxStoragePeriod"></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="defaultStorageLocation">Default Storage Location</label>
                                                                                            <p id="defaultStorageLocation"></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="minimumRemainSelfLife">Minimum Remain Self life</label>
                                                                                            <p id="minimumRemainSelfLife"></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="minTimeUnit">Min Time Unit</label>
                                                                                            <p id="minTimeUnit"></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="maxTimeUnit">Max Time Unit</label>
                                                                                            <p id="maxTimeUnit"></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="minimumStock">Minimum Stock</label>
                                                                                            <p id="minimumStock"></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="maximumUnit">Maximum Unit</label>
                                                                                            <p id="maximumUnit"></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="qaStorageLocation">QA Storage Location</label>
                                                                                            <p id="qaStorageLocation"></p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="view-block price-discount-block">
                                                                                <div class="items-table">
                                                                                    <h6>Pricing and Discount</h6>
                                                                                    <div class="storage-details">
                                                                                        <div class="form-input">
                                                                                            <label for="">Default MRP</label>
                                                                                            <p><span id="defMrp"></span></p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Default Discount (%)</label>
                                                                                            <p><span id="defDiscount"></span></p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="view-block specification-block">
                                                                                <div class="items-table">
                                                                                    <h6>Technical Specification Details</h6>
                                                                                    <div id="techSpecification"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="tab-pane dephistory-pane fade" id="nav-dephistory" role="tabpanel" aria-labelledby="nav-dephistory-tab">
                                                                    <table>
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Dep. Date</th>
                                                                                <th>Dep. Code</th>
                                                                                <th>Historical Price</th>
                                                                                <th>Dep On Value</th>
                                                                                <th>Depreciatiion Value</th>
                                                                                <th>Depreciated Value</th>
                                                                                <th>Status</th>
                                                                                <th>Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="depHistTbody"></tbody>
                                                                    </table>
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

                                            <!-----add bulk dep modal start --->
                                            <div class="modal fade asset-depriciate-modal" id="dep_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4>Run Depreciation</h4>
                                                        </div>

                                                        <div class="modal-body p-0" id="dep_modal_body">
                                                            <div class="deep-modal-content" id="dep_modal_content">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="modal fade asset-depriciate-modal" id="dep_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                <div class="modal-dialog modal-full-height modal-notify modal-success" style="max-width:80% !important;" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4>Run Depreciation</h4>
                                                        </div>

                                                        <div class="modal-body p-0" id="dep_modal1_body">
                                                            <div class="deep-modal-content" style="overflow:auto !important;" id="">
                                                                <div id="dep_modal1_content"></div>


                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!----- add bulk dep modal end -->

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
        $('th').removeClass('draggable');
        $('#dataTable_detailed_view').off('mousemove');

    });
</script>
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
    $('#checkAll').change(function() {
        $('.checking').prop('checked', $(this).prop('checked'));
    });

    let csvContent;
    let csvContentBypagination;

    $(document).ready(function() {
        var indexValues = [];
        var dataTable;
        var columnMapping = <?php echo json_encode($columnMapping); ?>;


        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view1").DataTable({
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
        $('#dataTable_detailed_view1 thead tr').append('<th>Action</th>');

        initializeDataTable();


        var allData;
        var dataPaginate;

        function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var checkboxSettings = Cookies.get('cookieAssetsInuse');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-assets-inuse.php",
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
                    csvContent = response.csvContent;
                    csvContentBypagination = response.csvContentBypagination;

                    if (response.status) {
                        var responseObj = response.data;
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);
                        dataTable.column(length - 2).visible(true);

                        $.each(responseObj, function(index, value) {

                            let status = ``;
                            if (value['items.status'] == 'active') {
                                status = `<p class='status-bg status-open'>Active</p>`;
                            } else if (value['items.status'] == 'deleted') {
                                status = `<p class='status-bg status-closed'>Deleted</p>`;
                            } else {
                                status = `<p class='status-bg status-open'>${value['items.status']}</p>`;
                            }


                            let checkBox = `<input type="checkbox" class="checking" name="check_dep" id="check_dep" value=${value.use_asset_id}>`;
                            dataTable.row.add([
                                checkBox,
                                value.sl_no,
                                `<a class="soModal" href="#" data-id="${value.itemId}" data-code="${value.asset_code}" data-asset_use_id="${value.use_asset_id}">${value.itemCode}</a>`,
                                value.itemName,
                                value.cost_center,
                                value.uom,
                                value.use_date,
                                value.qty,
                                value.rate,
                                value.total_value,
                                value.depreciated_asset_value,
                                status,
                                `<div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                        <li>
                                            <button class="soModal" data-toggle="modal" data-id=${value.itemId} data-code="${value.asset_code}" data-asset_use_id="${value.use_asset_id}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
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
                        $('#yourDataTable_paginate').remove();
                        $('#limitText').remove();
                    }
                    $("#globalModalLoader").remove();
                },
                complete: function() {
                    $("#globalModalLoader").remove();

                },
            });
        }

        fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping);

        $(document).on("click", ".ion-paginationlistAssetsUnuse", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieAssetsInuse')
                },
                beforeSend: function() {
                    $('#loaderModal').show();
                    $('.ion-paginationlistAssetsUnuse').prop('disabled', true)
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
                    $('.ion-paginationlistAssetsUnuse').prop('disabled', false);
                }
            })

        });

        $(document).on("click", ".ion-fulllistAssetsUnuse", function(e) {
            let fromDate = "<?= $fromDate ?>"; // For Date Filter
            let toDate = "<?= $toDate ?>"; // For Date Filter  
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-assets-inuse.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieAssetsInuse'),
                    formDatas: formInputs,
                    // fromDate,
                    // toDate
                },
                beforeSend: function() {
                    $('#loaderModal').show();
                    $('.ion-fulllistAssetsUnuse').prop('disabled', true)
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
                    $('.ion-fulllistAssetsUnuse').prop('disabled', false)
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

                    if (columnSlag === 'delivery_date') {
                        values = value4;
                    } else if (columnSlag === 'so_date') {
                        values = value2;
                    } else if (columnSlag.trim() === 'use_date') {
                        values = value3;
                    }

                    if ((columnSlag === 'delivery_date' || columnSlag === 'so_date' || columnSlag.trim() === 'use_date') && operatorName == "BETWEEN") {
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
                        act: 'assetsInuse',
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
        let today = new Date();
        let formattedDate = today.toISOString().split('T')[0];

        // Set the default value of the date input to today
        $("#useDate").val(formattedDate);
            $(document).on("change", ".selectOperator", function() {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Delivery Date') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'SO Date') {
                inputId = "value2_" + columnIndex;
            } else if (columnName.trim() === 'Put to use date') {
                inputId = "value3_" + columnIndex;
            }

            if ((columnName === 'Delivery Date' || columnName === 'SO Date' || columnName.trim() === 'Put to use date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
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


<!------------ modal ajax----------->
<script>
    $(document).on("click", ".soModal", function() {
        let itemId = $(this).data('id');
        let code = $(this).data('code');
        let asset_use_id = $(this).data("asset_use_id");
        $('.auditTrail').attr("data-ccode", code);
        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        console.log(itemId);

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
            dataType: 'json',
            data: {
                act: 'modalData',
                itemId
            },
            beforeSend: function() {
                $('#itemTableBody').html('');
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
            success: function(value) {
                let responseObj = value.data;
                let dataObj = value.data.dataObj;

                // top nav
                $("#amount").html(dataObj.itemName);
                $("#po-numbers").html(dataObj.itemCode);
                $("#default_address").html(dataObj.itemDesc);
                $("#cus_name").html(responseObj.classification.glName);

                if (responseObj.type == 'asset') {
                    $('#otherItem').show();
                    $('#serviceItem').hide();

                    //Item Basic Details
                    $("#itemName").html(dataObj.itemName);
                    $("#itemDesc").html(dataObj.itemDesc);
                    $("#hsnCode").html(dataObj.hsnCode);
                    $("#hsnDesc").html(dataObj.hsnDesc);
                    $("#movWeightPrice").html(responseObj.movWeightPrice);
                    $("#baseUom").html(responseObj.baseUnitMeasure);
                    $("#altUom").html(responseObj.issueUnitMeasure);

                    //Images from item
                    if (responseObj.images.length > 0) {
                        $("#itemImages").html('');
                        $.each(responseObj.images, function(index, val) {
                            let imgUrl = `<?= COMP_STORAGE_URL ?>/others/${val}`;
                            // console.log(imgUrl);
                            let obj = `<div class="imgs">                       
                                            <img src="${imgUrl}" alt="">
                                    </div>`;
                            $("#itemImages").append(obj);
                        });
                    } else {
                        let obj = `<div class="imgs"><p>No Images Found </p></div>`
                        $("#itemImages").html(obj);
                    }

                    //Group
                    $("#itemType").html(responseObj.classification.glName);
                    //Item  Asset classification
                    $("#assetClassification").html(responseObj.assetClass);
                    // Item Gl Code
                    $("#assetGlCode").html(responseObj.assetGlCode);



                    //Specification Details
                    if (dataObj.netWeight != '' || dataObj.grossWeight != '' || dataObj.height != '' || dataObj.width != '' || dataObj.length != '' || dataObj.volumeCubeCm != '' || dataObj.volume != '') {
                        if (dataObj.netWeight != '') {
                            $('#netWeightSpec').html(`${dataObj.netWeight} ${dataObj.weight_unit}`);
                        }
                        if (dataObj.grossWeight != '') {
                            $('#grossWeightSpec').html(`${dataObj.grossWeight}  ${dataObj.weight_unit}`);
                        }
                        if (dataObj.height != '') {
                            $('#heightSpec').html(`${dataObj.height} ${dataObj.measuring_unit}`);
                        }
                        if (dataObj.width != '') {
                            $('#widthSpec').html(`${dataObj.width} ${dataObj.measuring_unit}`);
                        }
                        if (dataObj.length != '') {
                            $('#lengthSpec').html(`${dataObj.length} ${dataObj.measuring_unit}`);
                        }
                        if (dataObj.volumeCubeCm != '') {
                            $('#volumenCmSpec').html(`${dataObj.volumeCubeCm}`);
                        }
                        if (dataObj.volume != '') {
                            $('#volumenMSpec').html(`${dataObj.volume}`);
                        }
                    } else {
                        $("#specificationDiv").html('');
                    }

                    //Tech Specifications
                    $('#techSpecification').html('');
                    let techSpecification = responseObj.techSpecification;
                    if (techSpecification.length > 0 && (techSpecification[0].specification != '' && techSpecification[0].specification_detail != '')) {
                        $.each(techSpecification, function(index, val) {
                            if (val.specification != '' && val.specification_detail != '') {
                                let obj = `<div class="spec-details">
                                                                                        <div class="form-input">
                                                                                            <label for="">Specifiaction</label>
                                                                                            <p>${val.specification}</p>
                                                                                        </div>
                                                                                        <div class="form-input">
                                                                                            <label for="">Description</label>
                                                                                            <p>${val.specification_detail}</p>
                                                                                        </div>
                                                                                    </div>`;
                                $('#techSpecification').append(obj);
                            }

                        });
                    }

                    //Default mrp and discount
                    $('#defMrp').html(`${responseObj.companyCurrency} ${(responseObj.itemPrice)}`);
                    $('#defDiscount').html(`${responseObj.itemMaxDiscount} %`);

                    //Storage details
                    let storageDetails = responseObj.storageDetails;
                    let summaryData = responseObj.summaryData;

                    $('#storageControl').html(storageDetails.storageControl);
                    $("#maxStoragePeriod").html(storageDetails.maxStoragePeriod);
                    $("#minimumRemainSelfLife").html(storageDetails.minRemainSelfLife);
                    $("#minTimeUnit").html(storageDetails.minRemainSelfLifeTimeUnit);
                    $("#maxTimeUnit").html(storageDetails.maxStoragePeriodTimeUnit);
                    $("#minimumStock").html(decimalQuantity(summaryData.min_stock));
                    $("#maximumUnit").html(decimalQuantity(summaryData.max_stock));
                    $("#defaultStorageLocation").html(responseObj.defaultStorageLocationName);
                    $("#qaStorageLocation").html(responseObj.qaStorageLocationName);

                    // end of other
                }
                // trail part
                $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

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
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
            dataType: "json",
            data: {
                act: 'depHistory',
                asset_use_id
            },
            beforeSend: function() {
                $("#depHistTbody").html('');
            },
            success: function(value) {
                console.log(value);
                if (value.status == 'success') {
                    let responseObj = value.data;
                    let output = [];
                    $.each(responseObj, function(index, val) {
                        let act = "";
                        if (val.status == 'active') {
                            act = `<button data-id="${val.asset_depreciation_id}" class="btn btn-sm reverseDepreciation"><i class="far fa-undo po-list-icon"></i></button>`;
                        }
                        output.push(`
                            <tr>
                                <td>${(formatDate(val.posting_date))}</td>
                                <td>${val.depreciation_code}</td>
                                <td>${decimalAmount(val.asset_value)}</td>
                                <td>${decimalAmount(val.depreciation_on_value)}</td>
                                <td>${decimalAmount(val.depreciated_value)}</td>
                                <td>${decimalAmount(val.depreciation_value)}</td>
                                <td>${val.status}</td>
                                <td>${act}</td>
                            </tr>                        
                        `);
                    });
                    $('#depHistTbody').append(output.join(''));
                } else {
                    let obj = `<tr><td colspan="5"><p class="text-center">No Data Found</p> </td></tr>  `;
                    $('#depHistTbody').append(obj);
                }
            },
        });

    });
    $(document).on("click", "#depreciate", function() {


        if ($('input[name="check_dep"]:checked').length > 0) {




            let boxArray = [];
            $("input:checkbox[name=check_dep]:checked").each(function() {
                boxArray.push($(this).val());
            });

            console.log(boxArray)
            // alert(dep_keys);
            let post_date = $("#useDate").val();
            console.log(post_date);
            let currentDate = new Date();
            let formattedDate = currentDate.toISOString().split('T')[0];
            // Get the current year
            let currentYear = currentDate.getFullYear();

            // Get the current month (Note: months are 0-indexed in JavaScript, so January is 0, February is 1, etc.)
            let currentMonth = currentDate.getMonth() + 1;


            $.ajax({
                type: "GET",
                url: `ajaxs/items/ajax-depreciation.php`,
                data: {
                    dep_keys: boxArray,
                    currentYear: currentYear,
                    currentMonth: currentMonth,
                    postingdate: post_date,
                },
                beforeSend: function() {
                    $("#dep_modal").modal("hide");
                    $("#glCode").html(`<option value="">Loading...</option>`);
                    $("body").append('<div id="loader-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;"><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""></div>');


                },
                success: function(response) {
                    if (boxArray.length === 1) {
                        $("#loader-overlay").remove();
                        $("#dep_modal_content").html("");
                        $("#dep_modal_content").append(response);
                        $("#dep_modal").modal("show");
                    } else {
                        $("#loader-overlay").remove();
                        $("#dep_modal1_content").html("");

                        $("#dep_modal1").modal("show");
                        $("#dep_modal1_content").append(response);
                    }

                }

            });
        } else {
            alert("No checkbox is checked.");
        }

    });
    $(document).on("submit", "#depreciationForm", function(e) {
        let depreciationForm = $("#depreciationForm");

        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "ajaxs/modals/mm/ajax-manage-asset-modal.php",
            dataType: "json",
            data: depreciationForm.serialize(),
            beforeSend: function() {
                $("body").append('<div id="loader-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; display: flex; justify-content: center; align-items: center;"><img src="<?= BASE_URL ?>public/assets/gif/loading-data.gif" width="150" alt=""></div>');

            },
            success: function(response) {
                console.log(response);
                if (response.status == "success") {
                    $("#loader-overlay").remove();
                    Swal.fire({
                        icon: response.status,
                        title: response.message,
                        timer: 3000,
                        showConfirmButton: false,
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(error) {
                console.log(error);
            },
        });
    });
    $(document).on("click", ".reverseDepreciation", function(e) {
        e.preventDefault();
        let dep_keys = $(this).data('id');
        let $this = $(this);

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
                        dep_slug: 'reverseDepreciation'
                    },
                    url: 'ajaxs/ajax-reverse-post.php',
                    beforeSend: function() {
                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        let responseObj = JSON.parse(response);
                        console.log(responseObj);

                        if (responseObj.status == 'success') {
                            $this.parent().parent().find('.reverseDepreStatus').html('reverse');
                            $this.parent('.reverseDepreciationDiv').html('');
                        } else {
                            $this.html('<i class="far fa-undo po-list-icon"></i>');
                        }

                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4000
                        });
                        Toast.fire({
                            icon: responseObj.status,
                            title: '&nbsp;' + responseObj.message
                        }).then(function() {
                            location.reload();
                        });
                    }
                });
            }
        });
    });
</script>