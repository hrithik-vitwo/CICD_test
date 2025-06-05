<?php
require_once("../../../app/v1/connection-branch-admin.php");

// if (!isset($_COOKIE["cookieTableStockReport"])) {
//     $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
//     $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
//     $settingsCheckbox_concised_view = unserialize($settingsCh);
//     if (settingsCheckbox_concised_view) {
//         setcookie("cookieTableStockReport", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
//     } else {
//         for ($i = 0; $i < 5; $i++) {
//             $isChecked = ($i < 5) ? 'checked' : '';
//         }
//     }
// }


$pageName =  basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    echo "Session Timeout";
    exit;
}
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");

// Add Functions
require_once("../../../app/v1/functions/branch/func-customers.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");




$columnMapping = [
    [
        'name' => 'SL_NO',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Location',
        'slag' => 'loc.othersLocation_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Document No',
        'slag' => 'LOG.refNumber',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Item Group',
        'slag' => 'grp.goodGroupName',
        'icon' => '<ion-icon name="albums-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Item Code',
        'slag' => 'items.itemCode',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Item Name',
        'slag' => 'itemName',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Storage Location',
        'slag' => 'str_loc.storage_location_name',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Party Code',
        'slag' => 'grn.vendorCode__customer.customer_code',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Party Name',
        'slag' => 'grn.vendorName__customer.trade_name',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Batch No',
        'slag' => 'LOG.logRef',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Date',
        'slag' => 'date',
        'icon' => '<ion-icon name="calendar-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Mvt Type ',
        'slag' => 'LOG.refActivityName',
        'icon' => '<ion-icon name="walk-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Qty',
        'slag' => 'LOG.itemQty',
        'icon' => '<ion-icon name="keypad-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'UOM',
        'slag' => 'uom',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Value',
        'slag' => 'LOG.itemPrice',
        'icon' => '<ion-icon name="wallet-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Currency',
        'slag' => 'currency',
        'icon' => '<ion-icon name="wallet-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Action',
        'slag' => 'action',
        'icon' => '<ion-icon name="wallet-outline"></ion-icon>',
        'dataType' => 'img'
    ],

];

?>

<!-- <link rel="stylesheet" href="../../../public/assets/new_listing.css"> -->
<!-- <link rel="stylesheet" href="../../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../../public/assets/stock-report-new.css">


<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-stock-new vitwo-alpha-global">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <?php
            // $cookieTableStockReport = $_COOKIE["cookieTableStockReport"];
            // console(["cookieTableStockReport" => $cookieTableStockReport]);

            ?>
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 p-0">
                    <div class="card card-tabs reports-card">
                        <div class="card-body">
                            <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!-- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space" style="width:100%">
                                        <div class="left-block">
                                            <div class="label-select">
                                                <h3 class="card-title mb-0">Stock Report</h3>
                                            </div>
                                        </div>
                                        <div class="right-block">
                                            <div class="page-list-filer filter-list">
                                                <a href="#" class="active filter-link"><ion-icon name="list-outline"></ion-icon>All List</a>
                                                <a href="#" class="filter-link"><ion-icon name="list-outline"></ion-icon>Demo List</a>
                                                <a href="#" class="filter-link"><ion-icon name="list-outline"></ion-icon>Sub Demo List</a>
                                                <a href="#" class="filter-link"><ion-icon name="list-outline"></ion-icon>Sub Demo List 2</a>
                                                <a href="#" class="filter-link"><ion-icon name="list-outline"></ion-icon>Sub Demo List 3</a>
                                            </div>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()">
                                                <ion-icon name="expand-outline"></ion-icon>
                                            </button>


                                            <!-- <div id="two" class="button">Revealing</div> -->
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
                                                            <a href="#" class="active filter-link"><ion-icon name="list-outline"></ion-icon>All List</a>
                                                            <a href="#" class="filter-link"><ion-icon name="list-outline"></ion-icon>Demo List</a>
                                                            <a href="#" class="filter-link"><ion-icon name="list-outline"></ion-icon>Sub Demo List</a>
                                                            <a href="#" class="filter-link"><ion-icon name="list-outline"></ion-icon>Sub Demo List 2</a>
                                                            <a href="#" class="filter-link"><ion-icon name="list-outline"></ion-icon>Sub Demo List 3</a>
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
                                                                            <ion-icon name="list-outline" class="ion-fulllist md hydrated" role="img" aria-label="list outline"></ion-icon>Export Full List
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline" class="ion-paginationlist md hydrated" role="img" aria-label="list outline"></ion-icon>Export By Pagination
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <a href="#" class="btn btn-create  mobile-page mobile-create" type="button">
                                                                <ion-icon name="add-outline"></ion-icon>
                                                                Create
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <!-- Search END -->
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
                                                        <button>
                                                            <ion-icon name="list-outline" class="ion-fulllist md hydrated" role="img" aria-label="list outline"></ion-icon>Export Full List
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button>
                                                            <ion-icon name="list-outline" class="ion-paginationlist md hydrated" role="img" aria-label="list outline"></ion-icon>Export By Pagination
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="#" class="btn btn-create" type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a>

                                            <!-- main table start -->

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

                                            <!-- customm pagination -->

                                            <div class="row custom-table-footer">
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div id="limitText" class="limit-text">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-12">
                                                    <div id="yourDataTable_paginate">

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- main table finish -->


                                            <!---------------------------------deialed View Table settings Model Start--------------------------------->
                                            <div class="modal chkmodal manage-column-setting-modal" id="myModal1">
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
                                                                                $checkedText = in_array($column['slag'], $cookieTableStockReport) ? "checked" : "";
                                                                            ?>

                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px">

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
                                                                <button type="submit" id="check-box-submt" data-dismiss="chkmodal" name="check-box-submit" class="btn btn-primary">Save</button>
                                                                <button type="button" class="btn btn-danger">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!---------------------------------Table Model End--------------------------------->

                                            <div class="modal  list-filter-search-modal advance-filter-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                                                                        $operators = ["LIKE", "NOT LIKE", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex  => $column) {
                                                                            if ($columnIndex === 0) {
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
                                                                                        foreach ($operators as $operator) {
                                                                                        ?>
                                                                                            <option value="<?= $operator ?>"><?= $operator ?></option>
                                                                                        <?php
                                                                                        }
                                                                                        ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td id="td_<?= $columnIndex ?>" class="date-flex">
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
                                                                <button type="submit" id="serach_submit" class="btn btn-primary" data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
                                                    </div>
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
                                                                <p class="info-detail amount"><ion-icon name="wallet-outline"></ion-icon>₹8800</p>
                                                                <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon>PO2310019</p>
                                                                <p class="info-detail ref-number"><ion-icon name="information-outline"></ion-icon>Ref : 12345</p>
                                                            </div>
                                                            <div class="right">
                                                                <p class="info-detail name"><ion-icon name="business-outline"></ion-icon>M/s Gujarat Cooperative Milk Marketing Federation Limited</p>
                                                                <p class="info-detail qty"><ion-icon name="albums-outline"></ion-icon>10 items</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <nav>
                                                            <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                                <button class="nav-link active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                                                                <button class="nav-link" id="nav-transaction-tab" data-bs-toggle="tab" data-bs-target="#nav-transaction" type="button" role="tab" aria-controls="nav-transaction" aria-selected="false"><ion-icon name="repeat-outline"></ion-icon>Transactional</button>
                                                                <button class="nav-link" id="nav-mail-tab" data-bs-toggle="tab" data-bs-target="#nav-mail" type="button" role="tab" aria-controls="nav-mail" aria-selected="false"><ion-icon name="mail-outline"></ion-icon>Mails</button>
                                                                <button class="nav-link" id="nav-statement-tab" data-bs-toggle="tab" data-bs-target="#nav-statement" type="button" role="tab" aria-controls="nav-statement" aria-selected="false"><ion-icon name="document-text-outline"></ion-icon>Statement</button>
                                                                <button class="nav-link" id="nav-compliance-tab" data-bs-toggle="tab" data-bs-target="#nav-compliance" type="button" role="tab" aria-controls="nav-compliance" aria-selected="false"><ion-icon name="analytics-outline"></ion-icon>Compliance Status</button>
                                                                <button class="nav-link" id="nav-reconciliation-tab" data-bs-toggle="tab" data-bs-target="#nav-reconciliation" type="button" role="tab" aria-controls="nav-reconciliation" aria-selected="false"><ion-icon name="settings-outline"></ion-icon>Reconciliation</button>
                                                                <button class="nav-link" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                            </div>
                                                            <div id="more-dropdown" class="more-dropdown">
                                                                <!-- Dropdown menu for additional tabs -->
                                                            </div>
                                                        </nav>

                                                        <div class="tab-content global-tab-content" id="nav-tabContent">
                                                            <div class="tab-pane fade show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                                <div class="d-flex">
                                                                    <a href="#" class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create Invoice</a>
                                                                    <a href="#" class="btn btn-danger"><ion-icon name="close-outline"></ion-icon>Close Invoice</a>
                                                                </div>

                                                                <div class="items-view">
                                                                    <h5 class="title">Details View</h5>
                                                                    <hr>
                                                                    <div class="card">
                                                                        <div class="card-body">
                                                                            <div class="row-section row-first">
                                                                                <div class="left-info">
                                                                                    <ion-icon name="cube-outline"></ion-icon>
                                                                                    <div class="item-info">
                                                                                        <p class="code">33000033</p>
                                                                                        <p class="name">Rider</p>
                                                                                        <p class="desc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta laudantium ut voluptatum nisi id reiciendis.</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="right-info">
                                                                                    <div class="item-info">
                                                                                        <p class="code">2 hours</p>
                                                                                        <p class="name">INR 200.00</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row-section row-tax">
                                                                                <div class="left-info">
                                                                                    <div class="item-info">
                                                                                        <p>Sub Total</p>
                                                                                        <p>Total Tax</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="right-info">
                                                                                    <div class="item-info">
                                                                                        <p>INR 200.00</p>
                                                                                        <p>INR 108.00 (18%)</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <hr>
                                                                            <div class="row-section row-total-amount">
                                                                                <div class="left-info">
                                                                                    <div class="item-info">
                                                                                        <p class="total">Total Amount</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="right-info">
                                                                                    <div class="item-info">
                                                                                        <p class="amount">INR 708.00</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="chart-view">
                                                                    <h5 class="title">Graph View</h5>
                                                                    <hr>
                                                                    <div id="chartDivCombinedColumnAndLineChart" class="chartContainer"></div>
                                                                </div>
                                                                <div class="info-view">
                                                                    <h5 class="title">Details View</h5>
                                                                    <hr>
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                            <div class="accordion view-modal-accordion" id="accordionExample">
                                                                                <div class="accordion-item">
                                                                                    <h2 class="accordion-header" id="headingOne">
                                                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseOne">
                                                                                            <ion-icon name="information-outline"></ion-icon> Basic Details
                                                                                        </button>
                                                                                    </h2>
                                                                                    <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                                                        <div class="accordion-body">
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Code</label>
                                                                                                <p>: AD525200</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Name</label>
                                                                                                <p>: Rider</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>HSN/SAC</label>
                                                                                                <p>: 98789</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Quantity</label>
                                                                                                <p>: 3</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>UOM</label>
                                                                                                <p>: Hour</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Rate</label>
                                                                                                <p>: 200.00</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Taxable Amount</label>
                                                                                                <p>: 600.00</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Total Amount</label>
                                                                                                <p>: 708.00</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                            <div class="accordion view-modal-accordion" id="accordionExample">
                                                                                <div class="accordion-item">
                                                                                    <h2 class="accordion-header" id="headingOne">
                                                                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAddress" aria-expanded="true" aria-controls="collapseOne">
                                                                                            <ion-icon name="information-outline"></ion-icon> Address Details
                                                                                        </button>
                                                                                    </h2>
                                                                                    <div id="collapseAddress" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                                                        <div class="accordion-body">
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>State</label>
                                                                                                <p>: Uttarakhand</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>City</label>
                                                                                                <p>: Selaqui</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>District</label>
                                                                                                <p>: Dehradun</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Location</label>
                                                                                                <p>: Selaqui</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Building Number</label>
                                                                                                <p>: Plot No-11 and 12, Khasra No-1045/2</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Flat Number</label>
                                                                                                <p>: Pargana Pachwa Doon</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Street Name</label>
                                                                                                <p>: Twin Industrial Area Mouza Camp Road</p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>PIN Code</label>
                                                                                                <p>: 246541</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane transaction-tab-pane fade" id="nav-transaction" role="tabpanel" aria-labelledby="nav-transaction-tab">
                                                                <div class="inner-content">
                                                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link active" id="pills-invoicesinner-tab" data-bs-toggle="pill" data-bs-target="#pills-invoicesinner" type="button" role="tab" aria-controls="pills-invoicesinner" aria-selected="true"><ion-icon name="receipt-outline"></ion-icon> Invoices</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-collectioninner-tab" data-bs-toggle="pill" data-bs-target="#pills-collectioninner" type="button" role="tab" aria-controls="pills-collectioninner" aria-selected="false"><ion-icon name="podium-outline"></ion-icon>Collection</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-estimatesinner-tab" data-bs-toggle="pill" data-bs-target="#pills-estimatesinner" type="button" role="tab" aria-controls="pills-estimatesinner" aria-selected="false"><ion-icon name="ticket-outline"></ion-icon>Estimates</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-salesorderinner-tab" data-bs-toggle="pill" data-bs-target="#pills-salesorderinner" type="button" role="tab" aria-controls="pills-salesorderinner" aria-selected="false"><ion-icon name="pricetags-outline"></ion-icon>Sales Order</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-journalinner-tab" data-bs-toggle="pill" data-bs-target="#pills-journalinner" type="button" role="tab" aria-controls="pills-journalinner" aria-selected="false"><ion-icon name="id-card-outline"></ion-icon>Journals</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link" id="pills-creditnotesinner-tab" data-bs-toggle="pill" data-bs-target="#pills-creditnotesinner" type="button" role="tab" aria-controls="pills-creditnotesinner" aria-selected="false"><ion-icon name="document-text-outline"></ion-icon>Credit Notes</button>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="tab-content" id="pills-tabContent">
                                                                        <div class="tab-pane fade show active" id="pills-invoicesinner" role="tabpanel" aria-labelledby="pills-invoicesinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-collectioninner" role="tabpanel" aria-labelledby="pills-collectioninner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-estimatesinner" role="tabpanel" aria-labelledby="pills-estimatesinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-salesorderinner" role="tabpanel" aria-labelledby="pills-salesorderinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-journalinner" role="tabpanel" aria-labelledby="pills-journalinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                        <div class="tab-pane fade" id="pills-creditnotesinner" role="tabpanel" aria-labelledby="pills-creditnotesinner-tab">
                                                                            <div class="list-block">
                                                                                <div class="head">
                                                                                    <h4>Invoices</h4>
                                                                                    <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                </div>
                                                                                <table class="exportTable">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Icon</th>
                                                                                            <th>Invoice Number</th>
                                                                                            <th>Amount</th>
                                                                                            <th>Date</th>
                                                                                            <th>Due in(day/s)</th>
                                                                                            <th>Status</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon warning">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-today">Due in Today</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon success">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-front">Due in 44 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status view" title="2024-03-14 17:01:09">Viewed<ion-icon name="checkmark-done-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="user-icon">
                                                                                                    <ion-icon name="person-outline"></ion-icon>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>INV-0000000410</td>
                                                                                            <td>INR 420.00</td>
                                                                                            <td>2024-03-14</td>
                                                                                            <td>
                                                                                                <p class="duein days-back">Out of 2 days</p>
                                                                                            </td>
                                                                                            <td>
                                                                                                <p class="status-delivery-status sent" title="2024-03-14 17:01:09">Sent<ion-icon name="checkmark-outline"></ion-icon></p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane mail-tab-pane fade" id="nav-mail" role="tabpanel" aria-labelledby="nav-mail-tab">
                                                                <div class="inner-content">
                                                                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                                                        <li class="nav-item" role="presentation">
                                                                            <button class="nav-link active" id="pills-mailInbox-tab" data-bs-toggle="pill" data-bs-target="#pills-mailInbox" type="button" role="tab" aria-controls="pills-mailInbox" aria-selected="true"><ion-icon name="mail-outline"></ion-icon>Inbox</button>
                                                                        </li>
                                                                        <li class="nav-item" role="presentation">
                                                                            <div class="float-reminder-btn">
                                                                                <button class="nav-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                                                                                    <ion-icon name="notifications-outline"></ion-icon>
                                                                                    Reminder
                                                                                </button>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                    <div class="tab-content" id="pills-tabContent">
                                                                        <div class="tab-pane fade show active" id="pills-mailInbox" role="tabpanel" aria-labelledby="pills-mailInbox-tab">
                                                                            <div class="inbox-blocks">
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block read-mail">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block read-mail">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block read-mail">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                                <a href="">
                                                                                    <div class="mail-block read-mail">
                                                                                        <div class="left-detail">
                                                                                            <p class="sender-mail">Lorem Ipsum</p>
                                                                                        </div>
                                                                                        <div class="subject-detail">
                                                                                            <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Adipisci eveniet distinctio vel obcaecati at, nostrum natus soluta asperiores maiores quidem.</p>
                                                                                        </div>
                                                                                        <div class="right-detail">
                                                                                            <p class="time-date">2:10 PM</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </a>
                                                                            </div>
                                                                            <div class="offcanvas offcanvas-end reminder-offcanvas" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                                                                                <div class="offcanvas-header">
                                                                                    <h5 id="offcanvasRightLabel"><ion-icon name="notifications-outline"></ion-icon>Reminder</h5>
                                                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
                                                                                        <ion-icon name="close-outline"></ion-icon>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="offcanvas-body">
                                                                                    <div class="row">
                                                                                        <div class="col-12 col-md-3">
                                                                                            <div class="form-input">
                                                                                                <label for="">Days</label>
                                                                                                <input type="text" class="form-control" name="reminerDays">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12 col-md-7">
                                                                                            <div class="form-input">
                                                                                                <label for="">Operator</label>
                                                                                                <select name="" id="" class="form-control">
                                                                                                    <option value="0">Post of Invoice Date</option>
                                                                                                    <option value="1">Post of Due Date</option>
                                                                                                    <option value="2">Early of Invoice Date</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12 col-md-2">
                                                                                            <button class="btn btn-primary add-mail-reminder">
                                                                                                <ion-icon name="add-outline"></ion-icon>
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="offcanvas-footer">
                                                                                    <button class="btn btn-primary">
                                                                                        <ion-icon name="send-outline"></ion-icon>
                                                                                        Sent
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- <div class="tab-pane fade" id="pills-manualInbox" role="tabpanel" aria-labelledby="pills-manualInbox-tab">
                                                        
                                                    </div> -->
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane statement-tab-pane fade" id="nav-statement" role="tabpanel" aria-labelledby="nav-statement-tab">
                                                                <div class="inner-content">
                                                                    <div class="row select-date">
                                                                        <div class="col-12 col-md-3">
                                                                            <div class="form-input">
                                                                                <select name="" id="" class="form-control">
                                                                                    <option value="">Select</option>
                                                                                    <option value="">This Month</option>
                                                                                    <option value="">Custom</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-9">
                                                                            <div class="date-fields">
                                                                                <div class="form-inline">
                                                                                    <label for="">From</label>
                                                                                    <input type="date" class="form-control">
                                                                                </div>
                                                                                <div class="form-inline">
                                                                                    <label for="">To</label>
                                                                                    <input type="date" class="form-control">
                                                                                </div>
                                                                                <button class="btn btn-primary">
                                                                                    <ion-icon name="arrow-forward-outline"></ion-icon>
                                                                                    Apply
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row statement-details">
                                                                        <div class="col-12 col-md-6 col">
                                                                            <div class="title-block">
                                                                                <h6><ion-icon name="document-text-outline"></ion-icon>STATEMENT OF ACCOUNT</h6>
                                                                                <p>2023-01-01 To 2024-03-19</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-6">
                                                                            <div class="amount-block">
                                                                                <h6><ion-icon name="card-outline"></ion-icon>Acount Summary</h6>
                                                                                <div class="info">
                                                                                    <label for=""><ion-icon name="lock-open-outline"></ion-icon>Opening Balance</label>
                                                                                    <p>RS 0</p>
                                                                                </div>
                                                                                <div class="info">
                                                                                    <label for=""><ion-icon name="wallet-outline"></ion-icon>Billed Amount</label>
                                                                                    <p>RS 0</p>
                                                                                </div>
                                                                                <div class="info">
                                                                                    <label for=""><ion-icon name="wallet-outline"></ion-icon>Amount Paid</label>
                                                                                    <p>RS 0</p>
                                                                                </div>
                                                                                <hr>
                                                                                <div class="total-info info">
                                                                                    <label for=""><ion-icon name="wallet-outline"></ion-icon>Amount Paid</label>
                                                                                    <p>RS 0</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-md-12">
                                                                            <table class="statement-table">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Date</th>
                                                                                        <th>Transaction</th>
                                                                                        <th>Details</th>
                                                                                        <th>Invoice Amount</th>
                                                                                        <th>Payment</th>
                                                                                        <th>Balance</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>2023-01-01 To 2024-03-19</td>
                                                                                        <td>Opening Balance</td>
                                                                                        <td>Lorem ipsum dolor ...</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                        <td>Cash</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                </tbody>
                                                                                <tfoot>
                                                                                    <tr class="balnce-due">
                                                                                        <td colspan="5">Balance Due</td>
                                                                                        <td class="text-right">450.00</td>
                                                                                    </tr>
                                                                                </tfoot>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane compliance-tab-pane fade" id="nav-compliance" role="tabpanel" aria-labelledby="nav-compliance-tab">
                                                                <div class="inner-content">
                                                                    <div class="list-block">
                                                                        <div class="gst-list gst-one-tab">
                                                                            <div class="head">
                                                                                <h4><ion-icon name="document-text-outline"></ion-icon>GST Filed Status For GSTR1</h4>
                                                                            </div>
                                                                            <table>
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Financial Year</th>
                                                                                        <th>Tax Period</th>
                                                                                        <th>Date of Filing</th>
                                                                                        <th>Status</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-non-filed"><ion-icon name="close-outline"></ion-icon>Not Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>

                                                                        <div class="gst-list gst-three-tab">
                                                                            <div class="head">
                                                                                <h4><ion-icon name="document-text-outline"></ion-icon>GST Filed Status For GSTR3B</h4>
                                                                            </div>
                                                                            <table>
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Financial Year</th>
                                                                                        <th>Tax Period</th>
                                                                                        <th>Date of Filing</th>
                                                                                        <th>Status</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-filed"><ion-icon name="checkmark-outline"></ion-icon>Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <tr>
                                                                                        <td>
                                                                                            2023-24
                                                                                        </td>
                                                                                        <td>March</td>
                                                                                        <td>18-04-2023</td>
                                                                                        <td>
                                                                                            <p class="gst-non-filed"><ion-icon name="close-outline"></ion-icon>Not Filed</p>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane recon-tab-pane fade" id="nav-reconciliation" role="tabpanel" aria-labelledby="nav-reconciliation-tab">
                                                                <div class="inner-content">
                                                                    <div class="date-fields">
                                                                        <div class="form-inline">
                                                                            <label for="">From</label>
                                                                            <input type="date" class="form-control">
                                                                        </div>
                                                                        <div class="form-inline">
                                                                            <label for="">To</label>
                                                                            <input type="date" class="form-control">
                                                                        </div>
                                                                        <button class="btn btn-primary waves-effect waves-light">
                                                                            <ion-icon name="arrow-forward-outline" role="img" class="md hydrated" aria-label="arrow forward outline"></ion-icon>
                                                                            Apply
                                                                        </button>
                                                                    </div>
                                                                    <form action="">
                                                                        <div class="recon-amount-section">
                                                                            <div class="tranasction total-transaction">
                                                                                <div class="form-inline">
                                                                                    <ion-icon name="repeat-outline"></ion-icon>
                                                                                    <div class="form-input">
                                                                                        <label for="">Total Transaction</label>
                                                                                        <p class="amount">20000.00</p>
                                                                                    </div>
                                                                                </div>

                                                                            </div>
                                                                            <div class="tranasction total-reconcile">
                                                                                <div class="form-inline">
                                                                                    <ion-icon name="repeat-outline"></ion-icon>
                                                                                    <div class="form-input">
                                                                                        <label for="">Total Reconciled</label>
                                                                                        <p class="amount">10000.00</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tranasction pending-transaction">
                                                                                <div class="form-inline">
                                                                                    <ion-icon name="time-outline"></ion-icon>
                                                                                    <div class="form-input">
                                                                                        <label for="">Pending</label>
                                                                                        <p class="amount">10000.00</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="reconcile-btn">
                                                                            <button class="btn btn-primary">Proceed for Reconciliation</button>
                                                                        </div>
                                                                    </form>
                                                                    <p class="recon-note">All valuesare in <b>INR</b></p>
                                                                </div>
                                                            </div>
                                                            <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                                                                <div class="inner-content">
                                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> Sonie Kushwaha <span class="font-bold text-normal"> on </span> 26-02-2024 16:35:10</p>
                                                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> Sonie Kushwaha <span class="font-bold text-normal"> on </span> 26-02-2024 16:35:10</p>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContentCustomer52400022">
                                                                        <ol class="timeline">

                                                                            <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLineCustomer" type="button" data-toggle="modal" data-id="2324" data-ccode="52400022" data-target="#innerModal">
                                                                                <span class="timeline-item-icon | filled-icon"><img src="https://www.devalpha.vitwo.ai/public/storage/audittrail/ADD.png" width="25" height="25"></span>
                                                                                <span class="step-count">3</span>
                                                                                <div class="new-comment font-bold">
                                                                                    <p>Sonie Kushwaha </p>
                                                                                    <ul class="ml-3 pl-0">
                                                                                        <li style="list-style: disc; color: #a7a7a7;">26-02-2024 16:35:12</li>
                                                                                    </ul>
                                                                                    <p></p>
                                                                                </div>
                                                                            </li>
                                                                            <p class="mt-0 mb-5 ml-5">New Customer added</p>
                                                                            <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLineCustomer" type="button" data-toggle="modal" data-id="1891" data-ccode="PO2402011" data-target="#innerModal">
                                                                                <span class="timeline-item-icon | filled-icon"><img src="https://www.devalpha.vitwo.ai/public/storage/audittrail/ADD.png" width="25" height="25"></span>
                                                                                <span class="step-count">2</span>
                                                                                <div class="new-comment font-bold">
                                                                                    <p>Sonie Kushwaha </p>
                                                                                    <ul class="ml-3 pl-0">
                                                                                        <li style="list-style: disc; color: #a7a7a7;">02-02-2024 14:57:02</li>
                                                                                    </ul>
                                                                                    <p></p>
                                                                                </div>
                                                                            </li>
                                                                            <p class="mt-0 mb-5 ml-5"> PO Created</p>
                                                                            <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLineCustomer" type="button" data-toggle="modal" data-id="1645" data-ccode="62400017" data-target="#innerModal">
                                                                                <span class="timeline-item-icon | filled-icon"><img src="https://www.devalpha.vitwo.ai/public/storage/audittrail/ADD.png" width="25" height="25"></span>
                                                                                <span class="step-count">1</span>
                                                                                <div class="new-comment font-bold">
                                                                                    <p>Anurag </p>
                                                                                    <ul class="ml-3 pl-0">
                                                                                        <li style="list-style: disc; color: #a7a7a7;">25-01-2024 18:04:35</li>
                                                                                    </ul>
                                                                                    <p></p>
                                                                                </div>
                                                                            </li>
                                                                            <p class="mt-0 mb-5 ml-5">New Vendor Add</p>


                                                                        </ol>

                                                                    </div>
                                                                    <div class="modal fade right audit-history-modal" id="innerModal" role="dialog" aria-labelledby="innerModalLabel" aria-modal="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content auditTrailBodyContentLineDiv">
                                                                                <div class="modal-header">
                                                                                    <div class="head-audit">
                                                                                        <p>New Customer added</p>
                                                                                    </div>
                                                                                    <div class="head-audit">
                                                                                        <p>Sonie Kushwaha</p>
                                                                                        <p>26-02-2024 16:35:12</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-body p-0">
                                                                                    <div class="free-space-bg">
                                                                                        <div class="color-define-text">
                                                                                            <p class="update"><span></span> Record Updated </p>
                                                                                            <p class="all"><span></span> New Added </p>
                                                                                        </div>
                                                                                        <ul class="nav nav-tabs pb-0" id="myTab" role="tablist">
                                                                                            <li class="nav-item">
                                                                                                <a class="nav-link active" id="concise-tab" data-toggle="tab" href="#consize" role="tab" aria-controls="concise" aria-selected="true"><i class="fa fa-th-large mr-2" aria-hidden="true"></i> Concised View</a>
                                                                                            </li>

                                                                                            <li class="nav-item">
                                                                                                <a class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false"><i class="fa fa-list mr-2" aria-hidden="true"></i>Detailed View</a>
                                                                                            </li>
                                                                                        </ul>
                                                                                    </div>
                                                                                    <div class="tab-content pt-0" id="myTabContent">
                                                                                        <div class="tab-pane fade show active" id="consize" role="tabpanel" aria-labelledby="consize-tab"></div>
                                                                                        <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                                                                                            <div class="dotted-box">
                                                                                                <p class="overlap-title">Customer Detail</p>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer code</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>52400022</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer pan</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>AAAFX0050D</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer gstin</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>29AAAFX0050D1ZL</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Trade name</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>SESHADRI AND COMPANY</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer currency</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>2</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer credit period</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>50</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Constitution of business</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>Partnership</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised person name</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>Sonie</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised person designation</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>Tester</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised person phone</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>7489761502</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised alt phone</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p></p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised person email</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>ksoni@vitwo.in</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer authorised alt email</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p></p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer visible to all</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>Yes</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer created by</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>6|location</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer updated by</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>6|location</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="box-content">
                                                                                                    <p>Customer status</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>active</p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="dotted-box">
                                                                                                <p class="overlap-title">Customer Address</p>
                                                                                                <div class="dotted-box">
                                                                                                    <p class="overlap-title">Bengaluru Urban (560003)</p>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address primary flag</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>1</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address building no</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>15/2</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address flat no</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p></p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address street name</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>13TH CROSS, 11MAIN</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address pin code</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>560003</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address location</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>MALLESWARAM</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address city</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>MALLESWARAM</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address district</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>Bengaluru Urban</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address state</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>Karnataka</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address created by</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>6|location</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="box-content">
                                                                                                        <p>Customer address updated by</p>
                                                                                                        <div class="existing-cross-data">
                                                                                                            <p>6|location</p>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="dotted-box">
                                                                                                <p class="overlap-title">Mail-Send</p>
                                                                                                <div class="box-content">
                                                                                                    <p>Send status</p>
                                                                                                    <div class="existing-cross-data">
                                                                                                        <p>success</p>
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
    </section>
    <!-- /.content -->
</div>

<?php
require_once("../../common/footer.php");

?>


<script src="../public/assets/core.js"></script>
<script src="../public/assets/charts.js"></script>
<script src="../public/assets/animated.js"></script>
<script src="../public/assets/forceDirected.js"></script>
<script src="../public/assets/sunburst.js"></script>



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


    // $("button.global-filter-list").click(function() {
    //     var buttonId = $(this).attr("id");
    //     $("#filter-container").removeAttr("class").toggleClass(buttonId);
    //     $(".mobile-transform-card").addClass("modal-active");
    // });

    // $("#filter-container").click(function() {
    //     $(this).toggleClass("out");
    //     $(".mobile-transform-card").removeClass("modal-active");
    // });
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
    $(".page-list-filer a").click(function() {
        $(this).toggleClass("active");
    });
</script>

<script>
    $(document).ready(function() {
        $('.exportTable').DataTable({
            dom: 'Bfrtip',
            "lengthMenu": false,
            "ordering": false,
            "searching": false,
            info: false,
            buttons: [{
                extend: 'excel',
                text: '<ion-icon name="download-outline" class="ion-excel"></ion-icon> Excel',
                filename: 'Invoice-List'
            }],
            "bPaginate": false
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#dropBtn").on("click", function(e) {
            e.stopPropagation(); // Stop the event from propagating to the document
            console.log("clickedddd");
            $("#filterDropdown .dropdown-content").addClass("active");
            $("#filterDropdown").addClass("active");
        });

        $(document).on("click", function() {
            $("#filterDropdown .dropdown-content").removeClass("active");
            $("#filterDropdown").removeClass("active");
        });

        // Close the dropdown when clicking inside it
        $("#filterDropdown .dropdown-content").on("click", function(e) {
            e.stopPropagation(); // Prevent the event from reaching the document
        });

        // $(window).resize(function() {
        //     if ($(window).width() > 768) {
        //         $("#filterDropdown .dropdown-content").hide();
        //     }
        // });
    });
</script>

<script>
    // ====================================== Combined bullet/column and line graphs with multiple value axes ======================================//
    am4core.ready(function() {

        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("chartDivCombinedColumnAndLineChart", am4charts.XYChart);
        chart.logo.disabled = true;

        // Add data
        chart.data = [{
            "date": "2013-01-16",
            "market1": 71,
            "market2": 75,
            "sales1": 5,
            "sales2": 8
        }, {
            "date": "2013-01-17",
            "market1": 74,
            "market2": 78,
            "sales1": 4,
            "sales2": 6
        }, {
            "date": "2013-01-18",
            "market1": 78,
            "market2": 88,
            "sales1": 5,
            "sales2": 2
        }, {
            "date": "2013-01-19",
            "market1": 85,
            "market2": 89,
            "sales1": 8,
            "sales2": 9
        }, {
            "date": "2013-01-20",
            "market1": 82,
            "market2": 89,
            "sales1": 9,
            "sales2": 6
        }, {
            "date": "2013-01-21",
            "market1": 83,
            "market2": 85,
            "sales1": 3,
            "sales2": 5
        }, {
            "date": "2013-01-22",
            "market1": 88,
            "market2": 92,
            "sales1": 5,
            "sales2": 7
        }, {
            "date": "2013-01-23",
            "market1": 85,
            "market2": 90,
            "sales1": 7,
            "sales2": 6
        }, {
            "date": "2013-01-24",
            "market1": 85,
            "market2": 91,
            "sales1": 9,
            "sales2": 5
        }, {
            "date": "2013-01-25",
            "market1": 80,
            "market2": 84,
            "sales1": 5,
            "sales2": 8
        }, {
            "date": "2013-01-26",
            "market1": 87,
            "market2": 92,
            "sales1": 4,
            "sales2": 8
        }, {
            "date": "2013-01-27",
            "market1": 84,
            "market2": 87,
            "sales1": 3,
            "sales2": 4
        }, {
            "date": "2013-01-28",
            "market1": 83,
            "market2": 88,
            "sales1": 5,
            "sales2": 7
        }, {
            "date": "2013-01-29",
            "market1": 84,
            "market2": 87,
            "sales1": 5,
            "sales2": 8
        }, {
            "date": "2013-01-30",
            "market1": 81,
            "market2": 85,
            "sales1": 4,
            "sales2": 7
        }];

        // Create axes
        var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
        //dateAxis.renderer.grid.template.location = 0;
        //dateAxis.renderer.minGridDistance = 30;

        var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis1.title.text = "Sales";

        var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis2.title.text = "Market Days";
        valueAxis2.renderer.opposite = true;
        valueAxis2.renderer.grid.template.disabled = true;

        // Create series
        var series1 = chart.series.push(new am4charts.ColumnSeries());
        series1.dataFields.valueY = "sales1";
        series1.dataFields.dateX = "date";
        series1.yAxis = valueAxis1;
        series1.name = "Target Sales";
        series1.tooltipText = "{name}\n[bold font-size: 20]${valueY}M[/]";
        series1.fill = chart.colors.getIndex(0);
        series1.strokeWidth = 0;
        series1.clustered = false;
        series1.columns.template.width = am4core.percent(40);

        var series2 = chart.series.push(new am4charts.ColumnSeries());
        series2.dataFields.valueY = "sales2";
        series2.dataFields.dateX = "date";
        series2.yAxis = valueAxis1;
        series2.name = "Actual Sales";
        series2.tooltipText = "{name}\n[bold font-size: 20]${valueY}M[/]";
        series2.fill = chart.colors.getIndex(0).lighten(0.5);
        series2.strokeWidth = 0;
        series2.clustered = false;
        series2.toBack();

        var series3 = chart.series.push(new am4charts.LineSeries());
        series3.dataFields.valueY = "market1";
        series3.dataFields.dateX = "date";
        series3.name = "Market Days";
        series3.strokeWidth = 2;
        series3.tensionX = 0.7;
        series3.yAxis = valueAxis2;
        series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

        var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
        bullet3.circle.radius = 3;
        bullet3.circle.strokeWidth = 2;
        bullet3.circle.fill = am4core.color("#fff");

        var series4 = chart.series.push(new am4charts.LineSeries());
        series4.dataFields.valueY = "market2";
        series4.dataFields.dateX = "date";
        series4.name = "Market Days ALL";
        series4.strokeWidth = 2;
        series4.tensionX = 0.7;
        series4.yAxis = valueAxis2;
        series4.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
        series4.stroke = chart.colors.getIndex(0).lighten(0.5);
        series4.strokeDasharray = "3,3";

        var bullet4 = series4.bullets.push(new am4charts.CircleBullet());
        bullet4.circle.radius = 3;
        bullet4.circle.strokeWidth = 2;
        bullet4.circle.fill = am4core.color("#fff");

        // Add cursor
        chart.cursor = new am4charts.XYCursor();

        // Add legend
        chart.legend = new am4charts.Legend();
        chart.legend.position = "top";

        // Add scrollbar
        chart.scrollbarX = new am4charts.XYChartScrollbar();
        chart.scrollbarX.series.push(series1);
        chart.scrollbarX.series.push(series3);
        chart.scrollbarX.parent = chart.bottomAxesContainer;

    });
    // ++++++++++++++++++++++++++++++++++++++ Combined bullet/column and line graphs with multiple value axes ++++++++++++++++++++++++++++++++++++++
</script>

<script>
    const table = new DataTable('#example', {
        ajax: '../php/staff.php',
        columns: [{
                data: null,
                render: (data) => data.first_name + ' ' + data.last_name
            },
            {
                data: 'position'
            },
            {
                data: 'office'
            },
            {
                data: 'extn'
            },
            {
                data: 'start_date'
            },
            {
                data: 'salary',
                render: DataTable.render.number(null, null, 0, '$')
            }
        ],
        colReorder: true,

    });
</script>

<script>
    var csvContent;
    $(document).ready(function() {
        var indexValues = [];
        var dataTable;

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

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookieTableStockReport');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "../ajaxs/ajax-stock-report.php",
                dataType: 'json',
                data: {
                    act: 'detailed_view',
                    fdate: fdate,
                    to_date: to_date,
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
                    /// console.log(response);
                    if (response.status) {
                        var responseObj = response.data;
                        csvContent = response.csvContent;
                        dataTable.clear().draw();
                        $.each(responseObj, function(index, value) {
                            dataTable.row.add([
                                value.sl_no,
                                value.loc,
                                value.doc_no,
                                value.itemGrp,
                                value.itemcode,
                                `<p class='item-name pre-normal'>${value.itemName}</p>`,
                                value.storage_loc,
                                value.party_code,
                                value.party_name,
                                value.logRef,
                                value.date,
                                `<p class='movement-type stockReportMovementType-${value.movement_type.toLowerCase()}'>${value.movement_type}</p>`,
                                value.qty,
                                value.uom,
                                value.value,
                                `<p class='currency-type stockReportMovementType-${value.currency.toLowerCase()}'>${value.currency}</p>`,
                                `<div class="dropout">
                    <button class="more">
                         <span></span>
                         <span></span>
                         <span></span>
                    </button>
                    <ul>
                        <li>
                            <button data-toggle="modal" data-target="#editModal"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                        </li>
                        <li>
                            <button data-toggle="modal" data-target="#deleteModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                        </li>
                        <li>
                            <button data-toggle="modal" data-target="#viewGlobalModal"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                        </li>
                        <li>
                            <button><ion-icon name="repeat-outline"></ion-icon>Reverse</button>
                        </li>
                    </ul>
                </div>`
                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        // Determine the active page
                        var activePage = response.activePage;

                        // Add active class to the active page number element
                        $('#yourDataTable_paginate #pagination a').each(function() {
                            if ($(this).text().trim() === activePage) {
                                $(this).addClass('active');
                            } else {
                                $(this).removeClass('active');
                            }
                        });

                        if (checkboxSettings === '' || checkboxSettings === null) {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            console.log("Fiveeeeeeee");
                        } else {
                            var checkedColumns = JSON.parse(checkboxSettings);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                var columnVal = $(this).val();
                                if (checkedColumns.includes(columnVal)) {
                                    $(this).prop("checked", true);
                                } else {
                                    notVisibleColArr.push(index);
                                }
                            });
                            console.log("notVisibleColArr index:", notVisibleColArr);
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }
                        }
                    } else {
                        $("#detailed_tbody").html(`<td colspan=17 class='else-td not-found-td text-center'><img src="../../../public/assets/gif/no-transaction.gif" width="150" alt=""><p>No Data Found</p></td>`);
                    }
                }

            });
        }

        fill_datatable();

        function downloadCSV() {
            var blob = new Blob([csvContent], {
                type: 'text/csv'
            });

            var url = URL.createObjectURL(blob);
            var link = document.createElement('a');
            link.href = url;
            link.download = 'exported_data.csv';
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

          
        $(document).on("click", ".ion-fulllist", function() {
            downloadCSV();
        });


        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);

        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a ", function(e) {
            e.preventDefault();
            $(this).toggleClass("active")
            var page_id = $(this).attr('id');
            var limitDisplay = $(".custom-select").val();

            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function() {

            $(document).on("click", "#serach_submit", function(event) {

                // $("#myForm").submit(function(event) {
                event.preventDefault();

                $(".selectOperator").each(function() {
                    let columnIndex = ($(this).attr("id")).split("_")[1];
                    let columnSlag = $(`#columnSlag_${columnIndex}`).val();
                    let operatorName = $(`#selectOperator_${columnIndex}`).val();
                    let value = $(`#value_${columnIndex}`).val() ?? "";
                    let value2 = $(`#value2_${columnIndex}`).val() ?? "";

                    if (columnSlag == "date" && operatorName == "BETWEEN") {
                        formInputs[columnSlag] = {
                            operatorName,
                            value: {
                                fromDate: value,
                                toDate: value2
                            }
                        };
                    } else {
                        formInputs[columnSlag] = {
                            operatorName,
                            value
                        };
                    }
                });

                $("#btnSearchCollpase_modal").hide();
                console.log("FormInputs:", formInputs);
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
                console.log(columnVal);

                var index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                console.log(index);
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
            $("#myModal1").modal().hide();
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

            console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "../ajaxs/ajax-save-cookies.php",
                    data: fromData,
                    success: function(response) {
                        console.log(response);
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
    document.querySelector('table.stock-new-table').onclick = ({
        target
    }) => {
        if (!target.classList.contains('more')) return
        document.querySelectorAll('.dropout.active').forEach(
            (d) => d !== target.parentElement && d.classList.remove('active')
        )
        target.parentElement.classList.toggle('active')
    }

    window.onclick = function(event) {
        if (!event.target.closest('table.stock-new-table')) {
            document.querySelectorAll('.dropout.active').forEach(function(dropout) {
                dropout.classList.remove('active');
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.exportgroup').forEach(function(exportgroup) {
            exportgroup.querySelector('.exceltype').addEventListener('click', function() {
                exportgroup.classList.toggle('active');
            });
        });

        window.addEventListener('click', function(event) {
            if (!event.target.closest('.exportgroup')) {
                document.querySelectorAll('.exportgroup.active').forEach(function(exportgroup) {
                    exportgroup.classList.remove('active');
                });
            }
        });
    });
</script>




<!-- ----------------- -->
<script>
    $(document).ready(function() {
        $(document).on("change", ".selectOperator", function() {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html();
            let inputContainer = $(`#td_${columnIndex}`);

            if (columnName === 'Date' && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="value2_${(columnIndex)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#value2_${columnIndex}`).remove();

            }
            console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
        });

    });
</script>

<script>
    $(document).ready(function() {
        $('.select2')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
            });
        /**************************************************************/
        $('.select4')
            .select4()
            .on('select4:open', () => {
                $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
            });
    });
</script>

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
    $(function() {
        $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            },
            function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
    });
</script>


<!-- CHANGES -->
<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
    $('#fYDropdown').change(function() {
        var title = $(this).val();
        if (title == "customrange") {
            $("#drop_val").val('customrange');
            $("#from_date").val('');
            $("#to_date").val('');
            $("#from_date").focus();
        } else {
            let start = $(this).find(':selected').data('start');
            let end = $(this).find(':selected').data('end');
            //alert(start);
            $("#from_date").val(start);
            $("#to_date").val(end);
            $("#drop_val").val('fYDropdown');
            $("#drop_id").val(title);
            $('#date_form').submit();
        }
    });

    $('#quickDropdown').change(function() {
        var days = $(this).val();
        var today = new Date();
        var seven_days_ago = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));

        var end = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        var start = seven_days_ago.getFullYear() + '-' + ('0' + (seven_days_ago.getMonth() + 1)).slice(-2) + '-' + ('0' + seven_days_ago.getDate()).slice(-2);

        // alert(start);
        // alert(end);
        $("#from_date").val(start);
        $("#to_date").val(end);
        $("#drop_val").val('quickDrop');
        $("#drop_id").val(days);

        $('#date_form').submit();
    });

    function compare_date() {
        let fromDate = $("#from_date").val();
        let toDate = $("#to_date").val();

        const date1 = new Date(fromDate);
        const date2 = new Date(toDate);
        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (fromDate && toDate) {
            if (diffDays > 366) {
                document.getElementById("rangeid").disabled = true;
                $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">Date Range can not be greater than 1 year</p>`);
            } else {
                $(".customRangeCla").html('');
                document.getElementById("rangeid").disabled = false;

                if (toDate < fromDate) {
                    $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">From Date can not be greater than To Date</p>`);
                    document.getElementById("rangeid").disabled = true;

                } else {
                    $(".customRangeCla").html('');
                    document.getElementById("rangeid").disabled = false;
                }
            }
        }
    }

    $("#to_date").keyup(function() {
        compare_date();
    });

    $("#from_date").change(function() {
        compare_date();
    });

    $("#to_date").change(function() {
        compare_date();
    });
</script>