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
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("boq/controller/boq.controller.php");



// job order action testing
$BranchSoObj = new BranchSo();

// if (isset($_POST['jobOrderApprovalSubmitBtn'])) {
//     // console($_POST);
//     // exit();
//     $jobOrderCompletionConfirmationObj = $BranchSoObj->jobOrderCompletionConfirmation($_POST);

//     if ($jobOrderCompletionConfirmationObj['status'] == "success") {
//         // swalAlert($jobOrderCompletionConfirmationObj["status"], "KJGHFTYF55552000", $jobOrderCompletionConfirmationObj["message"], $_SERVER['PHP_SELF']);
//         swalAlert($jobOrderCompletionConfirmationObj["status"], $jobOrderCompletionConfirmationObj["message"], $_SERVER['PHP_SELF']);
//     } else {
//         swalAlert($jobOrderCompletionConfirmationObj["status"], $jobOrderCompletionConfirmationObj["message"]);
//     }
// }

$pageName =  basename($_SERVER['PHP_SELF'], '.php');
if (!isset($_COOKIE["cookiePendingJob"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiePendingJob", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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

$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'SO Number',
        'slag' => 'so_number',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer PO',
        'slag' => 'customer_po_no',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    // [
    //     'name' => 'SO Date',
    //     'slag' => 'so_date',
    //     'icon' => '<ion-icon name="document-outline"></ion-icon>',
    //     'dataType' => 'date'
    // ],
    // [
    //     'name' => 'Created Date',
    //     'slag' => 'created_at',
    //     'icon' => '<ion-icon name="document-outline"></ion-icon>',
    //     'dataType' => 'date'
    // ],
    [
        'name' => 'Delivery Date',
        'slag' => 'delivery_date',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Customer Code',
        'slag' => 'cust.customer_code',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Name',
        'slag' => 'cust.trade_name',
        'icon' => '<ion-icon name="albums-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Type',
        'slag' => 'goodsType',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Total Amount',
        'slag' => 'so.totalAmount',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Total Item',
        'slag' => 'so.totalItems',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'JO Status',
        'slag' => 'so.jobOrderApprovalStatus',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'approvalStatus',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ]

];

?>


<!-- <link rel="stylesheet" href="../../../public/assets/new_listing.css"> -->
<!-- <link rel="stylesheet" href="../../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<style>
       .global-view-modal .modal-body {
            overflow: auto;
        }
    </style>
<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-stock-new is-sales-orders vitwo-alpha-global">

    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

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
                                                <h3 class="card-title mb-0">Pending Job Order</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <div class="page-list-filer filter-list">
                                                <?php require_once("common/soCommonList.php"); ?>
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
                                                            <a href="#" class="btn btn-create mobile-page mobile-create" type="button">
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
                                            <a href="direct-create-invoice.php?sales_order_creation" class="btn btn-create" type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a>
                                            <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
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

                                        </div>
                                    </div>

                                    <!-- Global View start-->

                                    <div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
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
                                                            <button class="nav-link ViewActionTab" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-action" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Action</button>
                                                            <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                        </div>
                                                    </nav>
                                                    <div class="tab-content global-tab-content" id="nav-tabContent">
                                                        <!-- overview  div -->
                                                        <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                            <div class="d-flex nav-overview-tabs">
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                                                    <div class="items-table">
                                                                        <h4>Customer Details</h4>
                                                                        <div class="customer-details">
                                                                            <div class="name-code">
                                                                                <div class="details name">
                                                                                    <p id="custName"></p>
                                                                                </div>
                                                                                <div class="details code">
                                                                                    <p id="custCode"></p>
                                                                                </div>
                                                                            </div>

                                                                            <div class="details gstin">
                                                                                <label for="">GSTIN</label>
                                                                                <p id="custgst"></p>
                                                                            </div>
                                                                            <div class="details pan">
                                                                                <label for="">PAN</label>
                                                                                <p id="custpan"></p>
                                                                            </div>

                                                                            <div class="address-contact">
                                                                                <div class="address-customer">
                                                                                    <div class="details">
                                                                                        <label for="">Billing Address</label>
                                                                                        <p class="pre-normal" id="custBillAdd"></p>
                                                                                    </div>
                                                                                    <div class="details">
                                                                                        <label for="">Shiping Address</label>
                                                                                        <p class="pre-normal" id="custShipAdd"></p>
                                                                                    </div>
                                                                                    <div class="details">
                                                                                        <label for="">Place of Supply</label>
                                                                                        <p><span id="stCode"></span> || <span id="stName"></span></p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="contact-customer">
                                                                                    <div class="details dotted-border-area">
                                                                                        <label for="">Contacts</label>
                                                                                        <p> <ion-icon name="mail-outline"></ion-icon> <span id="custEmail"></span></p>
                                                                                        <p> <ion-icon name="call-outline"></ion-icon><span id="custPhone"></span></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="items-table">
                                                                        <h4>Other Details</h4>
                                                                        <div class="other-info">
                                                                            <div class="details">
                                                                                <label for="">Posting Date</label>
                                                                                <p id="postingDate"></p>
                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Posting Time</label>
                                                                                <p id="postingTime"></p>

                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Delivery Date</label>
                                                                                <p id="deliveryDate"></p>

                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Validity Till</label>
                                                                                <p id="validityPeriod"></p>
                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Customer Order Number</label>
                                                                                <p id="custOrderNo"></p>
                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Credit Period</label>
                                                                                <p id="creditPeriod"></p>
                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Sales Person</label>
                                                                                <p id="salesPerson"></p>
                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Functional Area</label>
                                                                                <p id="funcArea"></p>
                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Compliance Invoice Type</label>
                                                                                <p id="comInvType"></p>
                                                                            </div>
                                                                            <div class="details">
                                                                                <label for="">Reference Document Link</label>
                                                                                <p>: <a href="#">Doc</a></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                                                    <div class="items-view items-calculation" id="item-div-main">
                                                                        <div class="card item-cards">
                                                                            <div class="card-body">
                                                                                <div class="row-section row-first">
                                                                                    <div class="left-info">
                                                                                        <ion-icon name="cube-outline"></ion-icon>
                                                                                        <div class="item-info">
                                                                                            <p class="code" id="cardSoNo"></p>
                                                                                            <p class="name" id="cardCustPo"></p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="right-info">
                                                                                        <div class="item-info">
                                                                                            <p class="code" id="totalItem"></p>
                                                                                            <!-- <p class="name" id="subTotal_inr"></p> -->
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row-section row-tax">
                                                                                    <div class="left-info">
                                                                                        <div class="item-info">
                                                                                            <p>Sub Total</p>
                                                                                            <p>Total Discount</p>
                                                                                            <p>Taxable Amount</p>
                                                                                            <p id="igstP">IGST</p>
                                                                                            <div id="csgst" style="display: none;">
                                                                                                <p>CGST</p>
                                                                                                <p>SGST</p>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="right-info">
                                                                                        <div class="item-info">
                                                                                            <p id="sub_total"></p>
                                                                                            <p id="totalDis"></p>
                                                                                            <p id="taxableAmt"></p>
                                                                                            <p id="igst"></p>
                                                                                            <div id="csgstVal">
                                                                                                <p id="cgstVal"></p>
                                                                                                <p id="sgstVal"></p>
                                                                                            </div>

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
                                                                                            <p class="amount" id="total_amount"></p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="del_status">
                                                                                </div>
                                                                            </div>
                                                                            <div class="text" id="jobInputBtn">

                                                                            </div>
                                                                            <div class="items-table">
                                                                                <div class="details">
                                                                                    <label for="">Remarks</label>
                                                                                    <p id="remark"></p>
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row orders-table">
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                    <div class="items-table">
                                                                        <h4>Item Details</h4>
                                                                        <div class="multiple-item-table">
                                                                            <div class="row head-state-table">
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Code</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Name</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">HSN</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Stock</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Qty</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Currency</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Unit Price</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Base Amount</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Discount</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Taxable Amount</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">GST(%)</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">GST Amount(<span id="currencyHead"></span>)</div>
                                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Total Amount</div>
                                                                            </div>

                                                                            <div id="itemTableBody">

                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- nav action div -->
                                                        <div class="tab-pane fade transactional-data-tabpane" id="nav-action" role="tabpanel" aria-labelledby="nav-action-tab">
                                                            <form id="jobForm" action="" method="post">
                                                                <div class="d-flex nav-overview-tabs" id="actionNavbar"></div>
                                                                <div id="allActionJob"></div>
                                                            </form>
                                                        </div>
                                                        <!-- nav trail div -->
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

                                                    <div class="modal-footer"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Global View end -->
                                </div>
                            </div>
                        </div>
    </section>
    <!-- /.content -->
</div>
<div id="loaderModal" class="modal" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p>Downloading, please wait...</p>
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>
</div>
<?php
require_once("../common/footer2.php");
?>

<script>
     $(document).on("click", "#serach_reset", function(e) {
      e.preventDefault();
      $("#myForm")[0].reset();
      $("#serach_submit").click();
    });
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
    // let csvContent;
    // let csvContentBypagination;
    let data;
    var columnMapping = <?php echo json_encode($columnMapping); ?>;

    $(document).ready(function() {
         $('.filter-link[data-name="pendingJobs"]').addClass('active');
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
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var checkboxSettings = Cookies.get('cookiePendingJob');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-job-order-pending.php",
                dataType: 'json',
                data: {
                    act: 'detailed_view',
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
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();
                        data=responseObj;
                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        var prevSoNo = null;

                        $.each(responseObj, function(index, value) {
                            let approvalStatus = '';
                            if (value.approvalStatus == "open") {
                                approvalStatus = `<p class='status-bg status-open'>Open</p>`;
                            } else if (value.approvalStatus == "pending") {
                                approvalStatus = `<p class='status-bg status-pending'>Pending</p>`;
                            } else if (value.approvalStatus == "closed") {
                                approvalStatus = `<p class='status-bg status-closed'>Closed</p>`;
                            }

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.soId}" data-toggle="modal" data-target="#viewGlobalModal">${value.so_number}</a>`,
                                value.customer_po_no,
                                // formatDate(value.so_date),
                                // formatDate(value.created_at),
                                value.delivery_date,
                                value["cust.customer_code"],
                                value["cust.trade_name"],
                                value.goodsType_page,
                                value["so.totalAmount"],
                                value["so.totalItems"],
                                value.jobOrderApprovalStatus_page,
                                approvalStatus,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>                                        
                                        <li>
                                            <button class="soModal"  data-id="${value.soId}" data-toggle="modal" data-target="#viewGlobalModal"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                        </li>
                                        <li>
                                        <button class="deleteSoBtn" data-toggle="modal" data-target="#" data-id="${value.soId}"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button> 
                                        </li>                                        
                                    </ul>
                                </div>`
                            ]).draw(false);

                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (!checkboxSettings) {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);
                                }
                            });

                            console.log('Cookie is blank.');
                        } else {
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

        $(document).on("click", ".ion-paginationliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(data),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiePendingJob')
                },
                beforeSend:function(){
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

        $(document).on("click", "#pagination a ", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#itemsPerPage").val();

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
                        values = value3;
                    } else if (columnSlag === 'so_date') {
                        values = value2;
                    } else if (columnSlag === 'created_at') {
                        values = value4;
                    }

                    if ((columnSlag === 'delivery_date' || columnSlag === 'so_date' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
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

            $(document).on("keypress", "#myForm input", function(e) {
                if (e.key === "Enter") {
                    $("#serach_submit").click();
                    e.preventDefault();
                }
            });
        });


        $(document).on("click", ".ion-fullliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-job-order-pending.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    formDatas: formInputs,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiePendingJob')
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
                    dataType: "json",
                    data: {
                        act: 'pendingJob',
                        fromData: fromData,
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
                inputId = "value3_" + columnIndex;
            } else if (columnName === 'SO Date') {
                inputId = "value2_" + columnIndex;
            } else if (columnName === 'Created Date') {
                inputId = "value4_" + columnIndex;
            }

            if ((columnName === 'Delivery Date' || columnName === 'SO Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input m-input2" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
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
    $(document).on("click", ".soModal", function() {
        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        let soId = $(this).data('id');
        // $('.auditTrail').attr("data-ccode", soId);

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/so/ajax-manage-job-order-pending-modal.php",
            dataType: 'json',
            data: {
                act: "modalData",
                soId: soId
            },
            beforeSend: function() {
                $("#itemTableBody").html('');
                $("#allActionJob").html('');
                $(".jobWarning").hide();
                $('.auditTrailBodyContent').html('');
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
                // console.log(value);
                if (value.status) {
                    let responseObj = value.data;
                    let itemsObj = responseObj.itemDetails;
                    $('.auditTrail').attr("data-ccode", responseObj.dataObj.so_number);
                    $('.ViewfirstTab').tab('show');
                    let delivery_qty = [];
                    let deliveryStatus = [];
                    let del_date = [];

                    $.each(itemsObj, function(index, item) {
                        delivery_qty.push(item.del_qty);
                        deliveryStatus.push(item.deliveryStatus);
                        del_date.push(item.delivery_date);
                    });

                    let dataObj = responseObj.dataObj;
                    // nav part
                    $(".left #amount").html(dataObj.currency_name + " " + decimalAmount(dataObj.totalAmount));
                    $("#default_address").html(responseObj.customer_address);
                    $("#amount-words").html("(" + responseObj.currecy_name_words + ")");
                    $("#po-numbers").html(dataObj.so_number);
                    $(".right #cus_name").html(dataObj.trade_name);
                    $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                    $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

                    $(".nav-overview-tabs").html(responseObj.navbar);
                    $("#custName").html(dataObj.trade_name);
                    $("#custCode").html(dataObj.customer_code);
                    $("#custBillAdd").html(dataObj.billingAddress);
                    $("#custShipAdd").html(dataObj.shippingAddress);
                    $("#stCode").html(dataObj.placeOfSupply);
                    $("#stName").html(responseObj.placeOfsupply);
                    $("#custEmail").html(dataObj.customer_authorised_person_email);
                    $("#custPhone").html(dataObj.customer_authorised_person_phone);
                    $("#postingDate").html(` : ` + formatDate(dataObj.so_date));
                    $("#postingTime").html(` : ` + dataObj.soPostingTime);
                    $("#deliveryDate").html(` : ` + formatDate(dataObj.delivery_date));
                    $("#validityPeriod").html(` : ` + formatDate(dataObj.validityperiod));
                    $("#custOrderNo").html(` : ` + dataObj.customer_po_no);
                    $("#creditPeriod").html(` : ` + dataObj.credit_period);
                    $("#salesPerson").html(` : ` + dataObj.kamName);
                    $("#funcArea").html(` : ` + dataObj.functionalities_name);
                    $("#comInvType").html(` : ` + dataObj.complianceInvoiceType);
                    $("#custgst").html(dataObj.customer_gstin);
                    $("#custpan").html(dataObj.customer_pan);

                    let taxableAmt = 0;
                    let igst = 0;
                    let cgst = 0;
                    let sgst = 0;
                    let subTotal = 0;
                    let totalTax = responseObj.dataObj.totalTax;
                    let disCount = responseObj.dataObj.totalDiscount;
                    let totalAmt = responseObj.dataObj.totalAmount;

                    if (disCount == 0 && totalTax != 0) {
                        subTotal = totalAmt - totalTax;
                        taxableAmt = subTotal;
                    } else {
                        subTotal = totalAmt - disCount;
                        taxableAmt = subTotal - disCount;
                    }

                    if (responseObj.dataObj.igst == 0) {
                        cgst = totalTax / 2;
                        sgst = totalTax / 2;
                    } else {
                        igst = responseObj.dataObj.igst;
                    }

                    // card details section
                    $("#cardSoNo").html(responseObj.dataObj.so_number);
                    $("#cardCustPo").html(responseObj.dataObj.customer_po_no);
                    $("#totalItem").html(decimalQuantity(responseObj.dataObj.totalItems) + " " + "Items");
                    $("#sub_total").html(responseObj.dataObj.currency_name + " " + decimalAmount(subTotal));
                    $("#totalDis").html(responseObj.dataObj.currency_name + " " + decimalAmount(disCount));
                    $("#taxableAmt").html(responseObj.dataObj.currency_name + " " + decimalAmount(taxableAmt));
                    $("#total_amount").html(responseObj.dataObj.currency_name + " " + decimalAmount(responseObj.dataObj.totalAmount));
                    $("#remark").html(responseObj.dataObj.remarks);

                    if (responseObj.dataObj.igst == 0) {
                        $("#csgst").css("display", "block");
                        $("#igstP").hide();
                        $("#igst").hide();
                        $("#cgstVal").html(responseObj.dataObj.currency_name + " " + decimalAmount(cgst));
                        $("#sgstVal").html(responseObj.dataObj.currency_name + " " + decimalAmount(sgst));
                    } else {
                        $("#igst").html(responseObj.dataObj.currency_name + " " + decimalAmount(igst));
                    }
                    // $("#jobInputBtn").html(responseObj.text_xs);
                    let tableData = ``;
                    let itemTableData = ``;
                    $.each(itemsObj, function(index, val) {
                        itemTableData = ` <div class="row body-state-table">
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.itemCode}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${val.itemName}">${val.itemName}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.hsnCode}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.stock}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${decimalQuantity(val.qty)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.currency}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${val.currency} ${decimalAmount(val.unitPrice)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${val.currency} ${decimalAmount(val.subTotal)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${val.currency} ${decimalAmount(val.total_discount)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${val.currency} ${decimalAmount(val.taxAbleAmount)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${decimalQuantity(val.tax)}%</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${val.currency} ${decimalAmount(val.gstAmount)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${val.currency} ${decimalAmount(val.itemTotalAmount)}</div>
                                                            </div>
                                                            `;
                        $("#currencyHead").html(val.currency)
                        $("#itemTableBody").append(itemTableData);
                    });
                    // overview end here --------------------------------

                    // ------------------------------Action part start from here --------------------------------
                    let navBtn = ``;
                    if (dataObj.approvalStatus == 9 || dataObj.approvalStatus == 11) {
                        if (dataObj.goodsType == "material" || dataObj.goodsType == "both") {
                            navBtn = `
                            <a href="delivery-actions.php?create-sales-order-delivery=${btoa(soId)}" class="btn-primary text-xs text-light deliveryCreationBtn pl-2 pr-2"><i class="fa fa-plus mr-2"></i>Create Delivery</a>
                            <a title="Create Invoice" href="direct-create-invoice.php?so_to_invoice=${btoa(soId)}" class="btn-primary text-xs text-light deliveryCreationBtn pl-2 pr-2 ml-2"><i class="fa fa-plus mr-2"></i>Create Invoice</a>`;
                        } else if (dataObj.goodsType == "project") {
                            navBtn = `<button type="submit" class="btn btn-success approvalTab " name="jobOrderApprovalSubmitBtn"><ion-icon name="arrow-forward-outline"></ion-icon>Job Done</button>`;
                        } else {
                            navBtn = `<a title="Create Invoice" href="direct-create-invoice.php?so_to_invoice=${btoa(soId)}" class="btn-primary text-xs text-light deliveryCreationBtn pl-2 pr-2 ml-2"><i class="fa fa-plus mr-2"></i>Create Invoice</a>`;
                        }
                    }

                    $("#actionNavbar").html(navBtn);
                    $('.approvalTab').prop("disabled", true);

                    let actionitemData = '';
                    let boqDetailObj = responseObj.boqDetailObj;
                    // console.log(boqDetailObj);
                    if (boqDetailObj.length > 0) {
                        let formObj = `<input type="hidden" name="soDetails[soId]" value="${soId}">
                                      <input type="hidden" name="soDetails[so_number]" value="${dataObj.so_number}">`;
                        $("#allActionJob").append(formObj);
                        // main loop for displaying boq detail 
                        $.each(boqDetailObj, function(index, boq) {
                            let jobInputDiv=``;
                            
                            if(boq.remainingQty==0){
                                jobInputDiv=`
                                    <input type="number" step="any" class="form-control"  placeholder="Order Closed" disabled>`;
                            }else{
                                jobInputDiv=`<div> 
                                               <input type="number" step="any" class="form-control enterJob" name="modalListItem[${index}][completionPercentage]"  data-maxvalue="${boq.remainingQty}" data-index=${index} placeholder="Enter done jobs qty">
                                                <p class="text-danger jobWarning" id="jobWarningMessage_${index}"></p>
                                            </div>`;
                            }

                            let actionitemData = `
                            <div class="navactiondiv">
                                <div class="row">
                                    <div class="col-8 col-lg-8 col-md-8 col-sm-12">
                                        <div class="line-border-area">
                                            <div class="d-flex justify-content-between item-details">
                                                <p>${boq.itemCode}</p>
                                                <p>${boq.itemName}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4 col-lg-4 col-md-4 col-sm-12">
                                        <p class="note">
                                            Note: ${boq.itemRemarks}
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8 col-lg-8 col-md-8 col-sm-12">
                                        <div class="line-border-area calc-qty">
                                            <div class="form-input">
                                                <label for=""><ion-icon name="layers-outline"></ion-icon>Total Order Qty. </label>
                                                <p>${decimalQuantity(boq.qty)}</p>
                                            </div>
                                            <div class="form-input">
                                                <label for=""><ion-icon name="checkmark-circle-outline"></ion-icon>Completed Jobs</label>
                                                <p>${decimalQuantity(boq.completion_value)}</p>
                                            </div>
                                            <div class="form-input">
                                                <label for=""><ion-icon name="list-outline"></ion-icon>To-Do </label>
                                                <p>${decimalQuantity(boq.remainingQty)}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4 col-lg-4 col-md-4 col-sm-12">
                                        <div class="input-qty">
                                            <label for="">Enter your Qty. of done jobs</label>
                                            <div class="d-flex">
                                                <input type="hidden" name="modalListItem[${index}][so_item_id]" class="soItemId"  value="${boq.so_item_id}">
                                                <input type="hidden" name="modalListItem[${index}][inventory_item_id]" class="fetchItemCode" value="${boq.inventory_item_id}">
                                                <input type="hidden" name="modalListItem[${index}][itemCode]" class="fetchItemCode" value="${boq.itemCode}">
                                                <input type="hidden" name="modalListItem[${index}][invStatus]" class="invStatus"    value="${boq.invStatus}">
                                                <input step="any" type="hidden" name="modalListItem[${index}][itemQty]" class="form-control text-right itemQty" value="${boq.qty}">
                                                <input step="any" type="hidden" name="modalListItem[${index}][completion_value]" class="form-control text-right completion_value"  value="${boq.completion_value}">
                                                <input step="any" type="hidden" name="modalListItem[${index}][remainingQtyHidden]" class="form-control text-right remainingQtyHidden"  value="${boq.remainingQty}">
                                                ${jobInputDiv}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-lg-12 col-md-12 col-sm-12">
                                        <div class="line-border-area">
                                            <div class="row orders-table">
                                                <div class="col-lg-8 col-md-8 col-sm-12 col-8">
                                                    <div class="items-table">
                                                        <h4>View BOQ </h4>
                                                        <p>Service Item</p>
                                                        <table>
                                                            <thead>
                                                                <tr>
                                                                    <th>Code</th>
                                                                    <th>Title</th>
                                                                    <th>Consumption</th>
                                                                    <th>Extra(%)</th>
                                                                    <th>UOM</th>
                                                                    <th>Rate</th>
                                                                    <th>Amount</th>
                                                                    <th>Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="boqTable" id="boqServiceTable-${index}">
                                                            </tbody>
                                                        </table>
                                                        <p>Goods Item</p>
                                                        <table>
                                                            <thead>
                                                                <tr>
                                                                    <th>Code</th>
                                                                    <th>Title</th>
                                                                    <th>Consumption</th>
                                                                    <th>Extra(%)</th>
                                                                    <th>UOM</th>
                                                                    <th>Rate</th>
                                                                    <th>Amount</th>
                                                                    <th>Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="boqTable" id="boqGoodTable-${index}">
                                                            </tbody>
                                                        </table>
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                            <div class="items-table">
                                                                <h4>Activities</h4>
                                                                <p>Hourly Deployment</p>
                                                                <table>
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Cost center</th>
                                                                            <th>Code</th>
                                                                            <th>Head Name</th>
                                                                            <th>Consumption</th>
                                                                            <th>Extra(%)</th>
                                                                            <th>UOM</th>
                                                                            <th>Rate</th>
                                                                            <th>Amount</th>
                                                                            <th>Remarks</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="boqTable" id="boqHdTable-${index}">
                                                                    </tbody>
                                                                </table>
                                                                <p>Other Heads</p>
                                                                <table>
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Cost center</th>
                                                                            <th>Code</th>
                                                                            <th>Head Name</th>
                                                                            <th>Consumption</th>
                                                                            <th>Extra(%)</th>
                                                                            <th>UOM</th>
                                                                            <th>Rate</th>
                                                                            <th>Amount</th>
                                                                            <th>Remarks</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="boqTable" id="boqOtherHeadTable-${index}">
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-12 col-4">
                                                    <p class="note">
                                                        Note: After submission, this will be available under "Done Jobs" to make the invoice.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                            $("#allActionJob").append(actionitemData);
                            // Append service items
                            $.each(boq.boqDetail.boq_service_data, function(i, val) {
                                let serviceItem = `
                                <tr>
                                    <td>${val.itemCode}</td>
                                    <td>${trimString(val.itemName,20)}</td>
                                    <td>${decimalQuantity(val.consumption)}</td>
                                    <td>${decimalQuantity(val.extra)}</td>
                                    <td>${val.uom}</td>
                                    <td>${decimalAmount(val.rate)}</td>
                                    <td>${decimalAmount(val.amount)}</td>
                                    <td>${val.remarks}</td>
                                </tr>`;
                                $(`#boqServiceTable-${index}`).append(serviceItem);
                            });

                            // Append goods items
                            $.each(boq.boqDetail.boq_material_data, function(i, val) {
                                let goodsItem = `
                                <tr>
                                    <td>${val.itemCode}</td>
                                    <td>${trimString(val.itemName,20)}</td>
                                    <td>${decimalQuantity(val.consumption)}</td>
                                    <td>${decimalQuantity(val.extra)}</td>
                                    <td>${val.uom}</td>
                                    <td>${decimalAmount(val.rate)}</td>
                                    <td>${decimalAmount(val.amount)}</td>
                                    <td>${val.remarks}</td>
                                </tr>`;
                                $(`#boqGoodTable-${index}`).append(goodsItem);
                            });

                            // Append hourly deployment items
                            $.each(boq.boqDetail.boq_hd_data, function(i, val) {
                                let boqHD = `
                                <tr>
                                    <td>${trimString(val.CostCenter_desc,20)}</td>
                                    <td>${val.CostCenter_code}</td>
                                    <td>${val.head_type}</td>
                                    <td>${decimalQuantity(val.consumption)}</td>
                                    <td>${decimalQuantity(val.extra)}</td>
                                    <td>${val.uom}</td>
                                    <td>${decimalAmount(val.rate)}</td>
                                    <td>${decimalAmount(val.amount)}</td>
                                    <td>${val.remarks}</td>
                                </tr>`;
                                $(`#boqHdTable-${index}`).append(boqHD);
                            });

                            // Append other head items
                            $.each(boq.boqDetail.boq_other_head_data, function(i, val) {
                                let boqOtherHD = `
                                <tr>
                                    <td>${trimString(val.CostCenter_desc,20)}</td>
                                    <td>${val.CostCenter_code}</td>
                                    <td>${val.head_type}</td>
                                    <td>${decimalQuantity(val.consumption)}</td>
                                    <td>${decimalQuantity(val.extra)}</td>
                                    <td>${val.uom}</td>
                                    <td>${decimalAmount(val.rate)}</td>
                                    <td>${decimalAmount(val.amount)}</td>
                                    <td>${val.remarks}</td>
                                </tr>`;
                                $(`#boqOtherHeadTable-${index}`).append(boqOtherHD);
                            });

                            if (boqDetailObj.length - 1 != index) {
                                $("#allActionJob").append("<br> <hr> <br>");
                            }
                        });
                    }
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
    });
</script>

<script>
    $(document).ready(function() {
        // Job done action
        $(document).on("keyup", ".enterJob", function() {
            let jobVal = parseFloat($(this).val());
            let index = $(this).data("index");
            if (isNaN(jobVal)) {
                $(`#jobWarningMessage_${index}`).html('Enter Valid Number');
                $(`#jobWarningMessage_${index}`).show();
                $(`.approvalTab`).prop("disabled", true);
            } else {
                let maxVal = parseFloat($(this).data("maxvalue"));
                if (jobVal > maxVal) {
                    $(`#jobWarningMessage_${index}`).html('Please Enter Correct Value');
                    $(`#jobWarningMessage_${index}`).show();
                    $(`.approvalTab`).prop("disabled", true);
                } else if (jobVal <= maxVal) {
                    $(`.approvalTab`).prop("disabled", false);
                    $(`#jobWarningMessage_${index}`).hide();
                } else {
                    $(`.approvalTab`).prop("disabled", true);
                }
            }
        });

        let jobFrom = $("#jobForm");
        $("#jobForm").submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "ajaxs/modals/so/ajax-manage-job-order-pending-modal.php",
                dataType: "json",
                data: jobFrom.serialize(),
                success: function(response) {
                    if (response.status == "success") {
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer:4000,
                            showConfirmButton: false,
                        })
                        location.reload();
                    }
                },
                error: function(error) {
                    console.log(error);
                },
            });
        });

         $(document).on('click', ".filter-link", function() {
        let lstName = $(this).data('name');
    
        if (lstName == 'pending'||lstName == 'open'||lstName == 'exceptional') {
            window.location.href = 'manage-sales-orders-taxComponents.php?type=' + lstName;
        }else if(lstName == 'itemOrderList'){
             window.location.href = 'manage-sales-orders-item-wise.php';
        }else if(lstName == 'pendingJobs'){
             window.location.href = 'manage-job-order-pending-list.php';
        }else if(lstName == 'doneJobs'){
             window.location.href = 'manage-job-order-list.php';
        }else {
            window.location.href = 'manage-sales-orders-taxComponents.php';
        }
    });

    });
</script>