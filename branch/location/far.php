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



$pageName =  basename($_SERVER['PHP_SELF'], '.php');

if (!isset($_COOKIE["far"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("far", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}

// export download file name section
$originalFileName = basename($_SERVER['PHP_SELF']);
$fileName = pathinfo($originalFileName, PATHINFO_FILENAME);

$parts = explode("-", $fileName, 2);
$fileNameWithoutExtension = isset($parts[1]) ? $parts[1] : $fileName; // Handle cases where "-" doesn't exist
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = 'export_' . $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = 'download_' . $fileNameWithoutExtension . $currentDateTime;

//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    echo "Session Timeout";
    exit;
}

$templateSalesOrderControllerObj = new TemplateSalesOrderController();

$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'GL Code',
        'slag' => 'gl_code',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'GL Name',
        'slag' => 'gl_name',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Asset Code',
        'slag' => 'itemCode',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Asset Name',
        'slag' => 'itemName',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Asset Description',
        'slag' => 'itemDesc',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Batch No',
        'slag' => 'logRef',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Equip No.',
        'slag' => 'equip_no',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Storage Location',
        'slag' => 'storage_location_name',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Issued to Cost Centre',
        'slag' => 'CostCenter_desc',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'GRN Details',
        'slag' => 'vendorDocumentNo',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Date of Acquisition (GRN Date)',
        'slag' => 'grn_postingDate',
        'icon' => '',
        'dataType' => 'date'
    ],
    [
        'name' => 'Invoice Number',
        'slag' => 'grnPoNumber',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Invoice Date',
        'slag' => 'po_date',
        'icon' => '',
        'dataType' => 'date'
    ],
    [
        'name' => 'Supplier Name',
        'slag' => 'vendorName',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Supplier GSTN',
        'slag' => 'vendorGstin',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Supplier Address',
        'slag' => 'vendorGstinStateName',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Qty',
        'slag' => 'total_qty',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'UOM',
        'slag' => 'uom',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Basic Value',
        'slag' => 'grnSubTotal',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'GST',
        'slag' => 'total_gst',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Total Value',
        'slag' => 'total_with_gst',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Historical Cost',
        'slag' => 'total_value',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Put to Use Date',
        'slag' => 'use_date',
        'icon' => '',
        'dataType' => 'date'
    ],
    [
        'name' => 'Useful Life',
        'slag' => 'asset_life',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Depreciation Rate',
        'slag' => 'dep_rate',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Depreciation Method',
        'slag' => 'method',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Written Down Value',
        'slag' => 'depreciation_value',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Last Written Down Value',
        'slag' => 'depreciation_on_value',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Last Depreciation Run',
        'slag' => 'posting_date',
        'icon' => '',
        'dataType' => 'date'
    ],
    [
        'name' => 'Accumulated Depreciations',
        'slag' => 'total_accu',
        'icon' => '',
        'dataType' => 'number'
    ]
];


?>


<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

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
                                                <h3 class="card-title mb-0">Fixed Asset Register</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <div class="page-list-filer filter-list">
                                            </div>
                                            <?php require_once("components/mm/assets-tabs.php"); ?>

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
                                                                        <button class="ion-paginationlist">
                                                                            <ion-icon name="list-outline" class="ion-fulllist-asset md hydrated" id="exportAllBtn" role="img" aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button class="ion-paginationlist-asset">
                                                                            <ion-icon name="list-outline" class="ion-paginationlist-asset md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <!-- <button class="btn btn-primary save-close-btn btn-xs float-right" id="depreciate" value="add_post" data-toggle="dep_modal">Depreciate</button> -->

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
                                            <!-- <a class="btn btn-create" id="depreciate" type="button">

                                                Depreciate
                                            </a> -->

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
                                                                                if ($index === 0) {
                                                                                    continue;
                                                                                }

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
                                                                            if ($columnIndex === 0 || $columnIndex === 20 || $columnIndex === 21|| $columnIndex === 5) {
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

    // let csvContent;
    let data;
    // let csvContentBypagination;

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

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var checkboxSettings = Cookies.get('far');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-assets-far.php",
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
                    // console.log(response);
                    // csvContent = response.csvContent;
                    // csvContentBypagination = response.csvContentBypagination;
                    // sql = response.sqllist;
                    // data=response.data;

                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();
                        data=responseObj;
                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);
                        dataTable.column(length - 2).visible(true);

                        $.each(responseObj, function(index, value) {

                            let status = ``;
                            if (value.status == 'active') {
                                status = `<p class='status-bg status-open'>Active</p>`;
                            } else if (value.status == 'deleted') {
                                status = `<p class='status-bg status-closed'>Deleted</p>`;
                            } else {
                                status = `<p class='status-bg status-open'>${value.status}</p>`;
                            }
                            dataTable.row.add([
                                value.sl_no,
                                value.gl_code,
                                value.gl_name,
                                value.itemCode,
                                value.itemName,
                                value.itemDesc,
                                value.logRef,
                                value.equip_no,
                                value.storage_location_name,
                                value.CostCenter_desc,
                                value.vendorDocumentNo,
                                formatDate(value.grn_postingDate),
                                value.grnPoNumber,
                                formatDate(value.po_date),
                                value.vendorName,
                                value.vendorGstin,
                                value.vendorGstinStateName,
                                decimalQuantity(value.total_qty),
                                value.uom,
                                decimalAmount(value.grnSubTotal),
                                decimalAmount(value.total_gst),
                                decimalAmount(value.total_with_gst),
                                decimalAmount(value.total_value),
                                formatDate(value.use_date),
                                value.asset_life,
                                value.dep_rate,
                                value.method,
                                value.depreciation_value,
                                value.depreciation_on_value,
                                formatDate(value.posting_date),
                                value.total_accu,

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
                    sql_data_checkbox: Cookies.get('far')
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

                    if (columnSlag === 'grn_postingDate') {
                        values = value4;
                    } else if (columnSlag === 'po_date') {
                        values = value2;
                    } else if (columnSlag === 'use_date') {
                        values = value3;
                    }

                    if ((columnSlag === 'grn_postingDate' || columnSlag === 'po_date' || columnSlag === 'use_date') && operatorName == "BETWEEN") {
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
                url: "ajaxs/ajax-manage-assets-far.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    formDatas: formInputs,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('far')
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
                        act: 'far',
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
            if (columnName === 'Date of Acquisition (GRN Date)') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'Invoice Date') {
                inputId = "value2_" + columnIndex;
            } else if (columnName === 'Put to Use Date') {
                inputId = "value3_" + columnIndex;
            }

            if ((columnName === 'Date of Acquisition (GRN Date)' || columnName === 'Invoice Date' || columnName === 'Put to Use Date') && operatorName === 'BETWEEN') {
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


<script>
    var columnMapping = <?php echo json_encode($columnMapping); ?>;
    $(document).on("click", ".ion-fulllist-asset", function(e) {
        $.ajax({
            type: "POST",
            url: "ajaxs/ajax-manage-assets-far.php",
            dataType: "json",
            data: {
                act: 'alldata',
                sql: sql,
                coloum: columnMapping,
                sql_data_checkbox: Cookies.get('far')
            },
            beforeSend: function() {
                $('#loaderModal').show();
                $('.ion-fulllist-asset').prop('disabled', true)
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
                $('.ion-fulllist-asset').prop('disabled', false)
            }
        })

    });
    $(document).on("click", ".ion-paginationlist-asset", function(e) {
        $.ajax({
            type: "POST",
            url: "../common/exportexcel-new.php",
            dataType: "json",
            data: {
                act: 'paginationlist',
                data: JSON.stringify(data),
                coloum: columnMapping,
                sql_data_checkbox: Cookies.get('far')
            },
            beforeSend: function() {
                $('#loaderModal').show();
                $('.ion-paginationlist-asset').prop('disabled', true)
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
                $('.ion-paginationlist-asset').prop('disabled', false);
            }
        })

    });
</script>