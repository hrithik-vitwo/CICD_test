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

if (!isset($_COOKIE["cookieInvoiceList"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieInvoiceList", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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

$templateSalesOrderControllerObj = new TemplateSalesOrderController();

$columnMapping = [
    [
        'name' => 'Sl. No.',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Invoice No',
        'slag' => 'salesInvoice.invoice_no',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Name',
        'slag' => 'cust.trade_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Total Items',
        'slag' => 'salesInvoice.totalItems',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Taxable Amount',
        'slag' => 'taxable_amount',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Total Tax Amount',
        'slag' => 'salesInvoice.total_tax_amt',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'number'
    ],

    [
        'name' => 'Invoice  Amount',
        'slag' => 'salesInvoice.all_total_amt',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Invoice Date',
        'slag' => 'salesInvoice.invoice_date',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Due in (day/s)',
        'slag' => 'salesInvoice.duedate',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Mail Status',
        'slag' => 'salesInvoice.mailStatus',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'salesInvoice.status',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Created By',
        'slag' => 'salesInvoice.created_by',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ]

];


?>


<style>
    .modal.generate-bill-modal.show {
        backdrop-filter: blur(4px);
    }

    .modal.generate-bill-modal .modal-dialog {
        max-width: 60%;
    }

    .modal.generate-bill-modal .modal-dialog .modal-body {
        height: auto;
        max-height: 520px;
        overflow: auto;
    }

    .modal.generate-bill-modal .modal-dialog .form-field .row {
        row-gap: 10px;
    }

    .modal.generate-bill-modal .modal-dialog .form-field .row.line-border-area {
        position: relative;
        padding: 23px 3px;
    }

    .modal.generate-bill-modal .modal-dialog .form-field .row.line-border-area label.float-label {
        position: absolute;
        top: -12px;
        font-weight: 600;
        left: 22px;
        background-color: #fff;
        padding: 3px 7px;
        text-align: center;
        display: inline-block;
        width: auto;
    }
</style>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<style>
    .global-view-modal .modal-body {
        overflow: auto;
    }
</style>

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-invoices is-sales-orders vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">All Invoices</h3>
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

                                            <div class="dropdown payment-collection-dropdown">
                                                <a class="btn btn-secondary dropdown-toggle btn-create btn-payment" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <ion-icon name="add-outline"></ion-icon>
                                                    Create
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a type="button" class="btn dropdown-toggle dropdown-sub-toggle btn-transparent" id="invoiceCreationDrop" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <ion-icon name="document-text-outline"></ion-icon> Create Invoice
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu" id="invoiceDropdown">
                                                            <li><a class="dropdown-item" href="invoice-creation.php">Goods Invoice</a></li>
                                                            <hr>
                                                            <li><a class="dropdown-item" href="invoice-creation.php?create_service_invoice">Service Invoice</a></li>
                                                            <hr>
                                                            <li><a class="dropdown-item" href="invoice-creation.php?proforma_invoice">Proforma Invoice</a></li>

                                                        </ul>
                                                    </li>
                                                    <hr>
                                                    <li>
                                                        <a type="button" class="btn dropdown-toggle dropdown-sub-toggle btn-transparent" id="collectionDrop" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <ion-icon name="arrow-redo-outline"></ion-icon> Settlement
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu" id="collectDropdown">
                                                            <li><a class="dropdown-item" href="collectpaymentsettelment.php?collect-payment">Collect Payment</a></li>
                                                            <hr>
                                                            <li><a class="dropdown-item" href="collectpaymentsettelment.php?adjust-payment">Settlement</a></li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>

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
                                                                            if ($columnIndex === 0 || $columnIndex === 6) {
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
                                                                    <button class="nav-link classicview-btn" id="nav-company-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-companyview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="print-outline"></ion-icon>Company <span id="compCurrencyNavBtn"></span></button>
                                                                    <button class="nav-link classicview-btn customerPrintView" id="nav-customer-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-customerview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="print-outline"></ion-icon>Customer <span id="custInvNav"></span></button>
                                                                    <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" data-cid="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                                </div>
                                                            </nav>
                                                            <div class="tab-content global-tab-content" id="nav-tabContent">

                                                                <!-- Overview -->
                                                                <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                                    <div class="d-flex nav-overview-tabs" id="navBTns">
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
                                                                                    <div class="details gstin" style="display: none;" id="businessTaxIDdiv">
                                                                                        <label for="" id="businessTaxID">GSTIN</label>
                                                                                        <p id="custgst"></p>
                                                                                    </div>
                                                                                    <div class="details pan" style="display: none;" id="taxNumberdiv">
                                                                                        <label for="" id="taxNumber">PAN</label>
                                                                                        <p id="custpan"></p>
                                                                                    </div>
                                                                                    <div class="address-contact">
                                                                                        <div class="address-customer">
                                                                                            <div class="details">
                                                                                                <label for="">Billing Address</label>
                                                                                                <p id="billAddress" class="pre-normal"></p>
                                                                                            </div>
                                                                                            <div class="details">
                                                                                                <label for="">Shipping Address</label>
                                                                                                <p class="pre-normal" id="shipAddress"></p>
                                                                                            </div>

                                                                                            <div class="details" style="display: none;" id="supplydiv">
                                                                                                <label for="">Place of Supply</label>
                                                                                                <p id="placeofSup"></p>
                                                                                            </div>

                                                                                        </div>
                                                                                        <div class="contact-customer">
                                                                                            <div class="details dotted-border-area">
                                                                                                <label for="">Contacts</label>
                                                                                                <p> <ion-icon name="mail-outline"></ion-icon><span id="custEmail"> </span></p>
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
                                                                                        <label for="">Invoice Date</label>
                                                                                        <p id="invDate"></p>
                                                                                    </div>
                                                                                    <div class="details">
                                                                                        <label for="">Invoice Time</label>
                                                                                        <p id="invTime"> </p>
                                                                                    </div>
                                                                                    <!-- <div class="details">
                                                                                        <label for="">Posting Period</label>
                                                                                        <p id="postingPeriod"></p>
                                                                                    </div> -->
                                                                                    <!-- <div class="details">
                                                                                        <label for="">Valid Till</label>
                                                                                        <p id="validTill"></p>
                                                                                    </div> -->

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
                                                                                        <p id="funcnArea"></p>
                                                                                    </div>
                                                                                    <div class="details" id="Compliance_Invoice" style="display: none;">
                                                                                        <label for="">Compliance Invoice Type</label>
                                                                                        <p id="compilaceInv"></p>
                                                                                    </div>
                                                                                    <div class="details" id="SONumber">
                                                                                        <label for="">So Number</label>
                                                                                        <p id="soNum"></p>
                                                                                    </div>

                                                                                    <!-- <div class="details">
                                                                                        <label for="">Reference Document Link</label>
                                                                                        <p>: <a href="#" id="refDoc"></a></p>
                                                                                    </div>
                                                                                     -->
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
                                                                                                    <p class="tcsAmount">TCS Amount</p>
                                                                                                    <p id="igstP" style="display: none;">IGST</p>
                                                                                                    <div id="csgst" style="display: none;">
                                                                                                        <p>CGST</p>
                                                                                                        <p>SGST</p>
                                                                                                    </div>
                                                                                                    <div id="tcomtype" style="display: none;">
                                                                                                        <p id="tcompname"></p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="right-info">
                                                                                                <div class="item-info">
                                                                                                    <p id="sub_total"></p>
                                                                                                    <p id="totalDis"></p>
                                                                                                    <p id="taxableAmt"></p>
                                                                                                    <p class="tcsAmount" id="tcsAmt"></p>
                                                                                                    <p id="igst"></p>
                                                                                                    <div id="csgstVal">
                                                                                                        <p id="cgstVal"></p>
                                                                                                        <p id="sgstVal"></p>
                                                                                                    </div>
                                                                                                    <div id="ccompval">
                                                                                                        <p id="compval"></p>
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
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Qty</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Currency</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Rate</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Base Amount</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Discount</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Taxable Amount</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right" id="GstInd">GST(%)</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right" id="taxName" style="display:none"><span id="GstName"></span>(%)</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right" id="GstInd2">GST Amount(<span id="currencyHead"></span>)</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right" id="taxName2" style="display: none;"><span id="GstName2"></span>Amount(<span id="currencyHead"></span>)</div>
                                                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">Total Amount</div>
                                                                                    </div>
                                                                                    <div id="itemTableBody">

                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>


                                                                </div>



                                                                <!-- company printView -->
                                                                <div class="tab-pane fade  classicview-pane " id="nav-companyview" role="tabpanel" aria-labelledby="nav-classicview-tab">

                                                                    <div class="template-div">
                                                                        <h6>Company Copy</h6>
                                                                        <select title="Select Template" class="form-control handleTemplates" id="templateSelectorCompany">
                                                                            <option value="0">Default Template</option>
                                                                            <option value="1">Template 2</option>
                                                                            <option value="2">Template 3</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="print-tc-btn">
                                                                        <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrintCompany" target="_blank">Print</a>
                                                                        <div class="check-input" id="checkboxDiv">
                                                                            <input type="checkbox" id="printChkbox">
                                                                            <label for="">Print With Terms and Conditions</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card classic-view bg-transparent" id="compnayPreview">

                                                                    </div>
                                                                </div>

                                                                <!-- customer printView -->
                                                                <div class="tab-pane fade classicview-pane" id="nav-customerview" role="tabpanel" aria-labelledby="nav-classicview-tab">

                                                                    <div class="template-div">
                                                                        <h6>Customer Copy</h6>
                                                                        <select title="Select Template" class="form-control handleTemplates" id="templateSelectorCustomer">
                                                                            <option value="0">Default Template</option>
                                                                            <option value="1">Template 2</option>
                                                                            <option value="2">Template 3</option>
                                                                        </select>
                                                                    </div>

                                                                    <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrintCustomer" target="_blank">Print</a>
                                                                    <div class="card classic-view bg-transparent" id="customerPreview">


                                                                    </div>
                                                                </div>

                                                                <!-- trail -->
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


                                            <!-- E -way generate bill modal start -->
                                            <div class="modal fade generate-bill-modal" id="generateEBillModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Generate E-Way Bill</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="" method="post" id="generateEbillform" class="generateEbillform">
                                                            <input type="hidden" name="so_inv_id" id="eWayFormInvId" value="">
                                                            <div class="modal-body">
                                                                <div class="form-field">
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">IRN</label>
                                                                                <input type="text" id="irn" name="irn" class="form-control" value="">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Distance</label>
                                                                                <input type="text" name="distance" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Transport Mode</label>
                                                                                <option>Select Transport Mode</option>
                                                                                <select class="form-control" id="transport_mode" name="transport_mode">
                                                                                    <option value="1">Road</option>
                                                                                    <option value="2">Rail</option>
                                                                                    <option value="3">Air</option>
                                                                                    <option value="4">Ship or Sship Cum Road/Rail</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Transporter Id</label>
                                                                                <input type="text" name="transport_id" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Transporter Name</label>
                                                                                <input type="text" name="transport_name" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Transport Document Number</label>
                                                                                <input type="text" name="transport_doc_no" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row">
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Transport Document Date</label>
                                                                                <input type="date" name="transport_doc_date" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Vehicle Number</label>
                                                                                <input type="text" name="vehicle_number" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Vehicle Type</label>
                                                                                <select class="form-control" id="vehicle_type" name="vehicle_type">
                                                                                    <option value="r">Regular</option>
                                                                                    <option value="o">Over Dimensional Cargo</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row line-border-area">
                                                                        <label for="" class="float-label">Export Address</label>
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Address 1</label>
                                                                                <input type="text" name="exp_addr1" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-6 col-md-6 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Address 2</label>
                                                                                <input type="text" name="exp_addr2" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Location</label>
                                                                                <input type="text" name="exp_loc" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Pin</label>
                                                                                <input type="text" name="exp_pin" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">State Code</label>
                                                                                <input type="text" name="exp_state_code" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="row line-border-area">
                                                                        <label for="" class="float-label">Dispatch Address</label>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Name</label>
                                                                                <input type="text" name="disp_addr_name" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Address 1</label>
                                                                                <input type="text" name="disp_addr1" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Address 2</label>
                                                                                <input type="text" name="disp_addr2" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Location</label>
                                                                                <input type="text" name="disp_loc" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">Pin</label>
                                                                                <input type="text" name="disp_pin" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 col-md-4 col-12">
                                                                            <div class="form-input">
                                                                                <label for="">State Code</label>
                                                                                <input type="text" name="disp_state_code" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-sm btn-success submitForm">Save</button>
                                                            </div>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- E -way generate bill modal end -->


                                            <!-- Global View end -->

                                            <!---- tc prit ---->
                                            <div class="modal right fade global-view-modal" id="tcContentModal" role="dialog" aria-labelledby="tcContentModalLabel" data-backdrop="true" aria-modal="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title tc-modal-title"></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true" class="text-white">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body discountViewBody">
                                                            <!-- <h6 class="tc-modal-title"></h6> -->
                                                            <p class='tc-modal-body'></p>


                                                        </div>
                                                        <div class="modal-footer modal-footer-fixed">
                                                            <button type="button" class="btn btn-primary w-100" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-------- end tc print ------>



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
</section>
<!-- /.content -->
</div>

<?php
require_once("../common/footer2.php");
$countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
$components = getLebels($countrycode)['data'];
?>

<!-----------mobile filter list------------>


<script>
    function cleardiv() {
        $('#igstP').hide();
        $('#csgst').hide();
        $('#csgstVal').hide();
        $('#sgstVal').hide();
        $('#cgstVal').hide();
        $('#igst').hide();
    }
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
    let countrycode = <?php echo json_encode($countrycode); ?>;
    let components = <?php echo json_encode($components); ?>;
    components = JSON.parse(components);
    // console.log(components);
    // console.log("Country Code:", countrycode);

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
            var checkboxSettings = Cookies.get('cookieInvoiceList');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-invoices.php",
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

                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();
                        data = responseObj;
                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        // dataTable.column(-2).visible(tru e);


                        $.each(responseObj, function(index, value) {
                            let reverseInvBtn = '';
                            let ackNoBtn = '';

                            if (value.ackNo == "") {
                                ackNoBtn = `
                                <a class="btn btn-sm btn-primary generateEInvoice" data-id="${value.soInvoiceId}" onclick="return confirm('Are you sure to generate E-invoice?')">Generate</a>
                                `;
                            } else {
                                ackNoBtn = `
                                 <a class="btn btn-sm btn-success">Generated</a>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateEBill_<?= $oneSoList['so_invoice_id'] ?>">Generate E-way Bill</button>
                                `;
                            }

                            if (value.status === 'active') {
                                reverseInvBtn = `<li>
                                        <button class="reverseInvoice" data-id="${value.soInvoiceId}" ><i class="far fa-undo po-list-icon"></i>Reverse</button>
                                    </li>`;
                            } else if (value.status === 'reverse') {
                                reverseInvBtn = `<li>
                                         <button class="repostInvoice" data-id="${value.soInvoiceId}" data-code="${value['salesInvoice.invoice_no']}" ><i class="far fa-retweet po-list-icon"></i></ion-icon>Repost</button>
                                    </li>
                                `;
                            }

                            let invStatus

                            if (value.status === 'reverse') {
                                invStatus = `<p class="status-bg status-closed">Reversed</p>`;
                            } else if (value.status === 'reposted') {
                                invStatus = `<p class="status-bg status-closed">Reposted</p>`;

                            } else {

                                if (value.invoiceStatus == 14) {
                                    invStatus = `<p class="status-bg status-pending">Pending</p>`;
                                } else if (value.invoiceStatus == 17) {
                                    invStatus = `<p class="status-bg status-closed">Rejected</p>`;
                                } else {
                                    invStatus = `<p class="status-bg status-approved">Approved</p>`;
                                }
                            }

                            let editBtn = '';
                            if (value.status === "active") {
                                editBtn = `                               
                                        <li>
                                            <button class="editInvBtn" data-id=${value.soInvoiceId} data-code="${value['salesInvoice.invoice_no']}"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                        </li>`;
                            }


                            dataTable.row.add([
                                value.sl_no,
                                `<a class="soModal" href="#" data-id="${value.soInvoiceId}" >${value["salesInvoice.invoice_no"]}</a>`,
                                `<p class="pre-normal">${value["cust.trade_name"]}</p>`,
                                value["salesInvoice.totalItems"],
                                value["taxable_amount"],
                                value["salesInvoice.total_tax_amt"],
                                value["salesInvoice.all_total_amt"],
                                value["salesInvoice.invoice_date"],
                                value.duedate,
                                value.mailStatus,
                                invStatus,
                                value["salesInvoice.created_by"],
                                // value.so_number,
                                // value.irn,
                                `<div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>                                   
                                        <li>
                                            <button class="soModal" data-toggle="modal" data-id=${value.soInvoiceId}><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                        </li>
                                        ${editBtn}                                        
                                        ${reverseInvBtn}
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
                    sql_data_checkbox: Cookies.get('cookieInvoiceList')
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
                    let value5 = $(`#value5_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'salesInvoice.invoice_date') {
                        values = value5;
                    } else if (columnSlag === 'so_date') {
                        values = value2;
                    } else if (columnSlag === 'created_at') {
                        values = value3;
                    }

                    if ((columnSlag === 'salesInvoice.invoice_date' || columnSlag === 'so_date' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
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
                url: "ajaxs/ajax-manage-invoices.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    formDatas: formInputs,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieInvoiceList')
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
                        act: 'manageInvoiceList',
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
            } else if (columnName === 'Invoice Date') {
                inputId = "value5_" + columnIndex;
            } else if (columnName === 'Created Date') {
                inputId = "value3_" + columnIndex;
            }

            if ((columnName === 'Invoice Date' || columnName === 'SO Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
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
    // function to load the print view for both customer and company 
    function loadPrintView(invId, invType) {
        let invoiceId = invId;
        let templateId
        let invoiceType
        if (invType == 'company') {
            invoiceType = 'company';
            templateId = $("#templateSelectorCompany").val();
        } else {
            templateId = $("#templateSelectorCustomer").val();
            invoiceType = 'customer';
        }
        // console.log("function calling ");
        // console.log(invId);
        // console.log(invoiceType);
        // console.log(templateId);

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/so/ajax-manage-invoices-modal-taxComponents.php",
            data: {
                act: "classicView",
                invoiceId,
                templateId,
                invoiceType
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

            },
            success: function(response) {
                // console.log(response);
                if (invoiceType == "company") {
                    $("#compnayPreview").html(response);
                } else {
                    $("#customerPreview").html(response);
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
    }

    // main on click event handler for loading all modal data 
    let soInvId
    $(document).on("click", ".soModal", function() {
        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');

        soInvId = $(this).data('id');
        console.log(soInvId);
        //t$c 
        $(document).on("change", "#templateSelectorCompany", function() {

            //console.log('okayy');
            loadPrintView(soInvId, "company");

            let templateId = $("#templateSelectorCompany").val();
            // alert(templateId);
            let companyPrint = `classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=company&template_id=${templateId}&printChkbox`;
            $('#classicViewPrintCompany').attr("href", companyPrint);
            document.getElementById('printChkbox').addEventListener('change', function() {
                if (this.checked) {
                    //alert(0);   
                    let companyPrint = `classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=company&template_id=${templateId}&printChkbox`;
                    $('#classicViewPrintCompany').attr("href", companyPrint);
                } else {
                    //alert(1);
                    let companyPrint = `classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=company&template_id=${templateId}`;
                    $('#classicViewPrintCompany').attr("href", companyPrint);
                }
            });


        })
        // select print template dropdown for customer

        $(document).on("change", "#templateSelectorCustomer", function() {
            // alert(1);
            loadPrintView(soInvId, "customer");
            let templateId = $("#templateSelectorCustomer").val();
            let customerPrint = `classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=customer&template_id=${templateId}&printChkbox`;
            $('#classicViewPrintCustomer').attr("href", customerPrint);

            document.getElementById('printChkbox').addEventListener('change', function() {
                if (this.checked) {
                    alert(1)
                    let customerPrint = `classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=customer&template_id=${templateId}&printChkbox`;
                    $('#classicViewPrintCustomer').attr("href", customerPrint);
                } else {
                    alert(2);
                    let customerPrint = `classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=customer&template_id=${templateId}`;
                    $('#classicViewPrintCustomer').attr("href", customerPrint);
                }
            });

        })
        let companyPrint = `classic-view/invoice-preview-print-taxcomponents.php?invoice_id=${btoa(soInvId)}&type=company&template_id=0`;
        $('#classicViewPrintCompany').attr("href", companyPrint);
        // // add print href to print button
        let customerPrint = `classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=customer&template_id=0`;
        $('#classicViewPrintCustomer').attr("href", customerPrint);

        // let companyPrint = `classic-view/invoice-preview-print.php?invoice_id=${btoa(soInvId)}&type=company&template_id=0`;
        // $('#classicViewPrintCompany').attr("href", companyPrint);


        // $('.auditTrail').attr("data-ccode", soInvId);
        // adding ccode to for trail
        $("#eWayFormInvId").val(soInvId);

        // ajax to load modal data
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/so/ajax-manage-invoices-modal-taxComponents.php",
            dataType: 'json',
            data: {
                act: "modalData",
                soInvId
            },
            beforeSend: function() {

                $("#itemTableBody").html('');
                $("#navBTns").html('');
                $(".tcsAmount").hide();

                let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;

                $('#viewGlobalModal .modal-body').append(loader);

            },
            success: function(response) {
                // console.log(response);
                if (response.status) {

                    let responseObj = response.data;
                    let dataObj = responseObj.dataObj;
                    // console.log(components);
                    let taxComponents;
                    $('#checkboxDiv').hide();
                    if (dataObj.tc_id != 0) {
                        $('#checkboxDiv').show();
                    }
                    if (dataObj.taxComponents) {
                        taxComponents = JSON.parse(dataObj.taxComponents);
                        console.log(taxComponents);
                    }
                    // console.log(taxComponents);
                    if (components.fields['businessTaxID'] != null) {
                        $("#businessTaxIDdiv").show();
                        $("#businessTaxID").html(components.fields['businessTaxID']);
                        $('.auditTrail').attr("data-ccode", dataObj.invoice_no);
                        $('.auditTrail').attr("data-cid", dataObj.so_invoice_id);
                        $("#custgst").html(dataObj.customer_gstin ? dataObj.customer_gstin : "--");
                        // set Title by Given Id 
                        setTitleAttributeById('custgst', dataObj.customer_gstin);

                    }
                    if (components.fields['taxNumber'] != null) {
                        $("#taxNumberdiv").show();
                        $("#taxNumber").html(components.fields['taxNumber']);
                        $("#custpan").html(dataObj.customer_pan ? dataObj.customer_pan : "--");
                    }
                    if (components.place_of_supply == true) {
                        $("#supplydiv").show();
                        $("#placeofSup").html(dataObj.placeOfSupply + "(" + responseObj.placeOfsupply + ")");
                    }
                    if (components.compliance_invoice == true) {
                        $("#Compliance_Invoice").show();
                        $("#compilaceInv").html(" : " + dataObj.compInvoiceType);
                    }
                    if (responseObj.so_number == null) {
                        $("#SONumber").hide();
                    } else {
                        $("#SONumber").show();
                    }
                    // Nav head 
                    $(".left #amount").html(`${responseObj.companyCurrency}` + " " + decimalAmount(dataObj.all_total_amt));
                    $("#po-numbers").html(dataObj.invoice_no);
                    $("#amount-words").html("(" + responseObj.currecy_name_words + ")");
                    $(".right #cus_name").html(dataObj.trade_name);
                    $("#default_address").html(dataObj.customer_code);
                    $("#compCurrencyNavBtn").html(`(${responseObj.companyCurrency})`);
                    $("#irn").val(`${responseObj.irn}`);
                    // overview section

                    // overview action button 
                    let obj = '';
                    let placeOfSupply = dataObj.placeOfSupply;

                    if (dataObj.status == "active") {
                        if (dataObj.invoiceStatus == 17) {
                            obj = `
                        <button class="btn btn-sm btn-danger "><ion-icon name="close-outline"></ion-icon>Rejected Invoice</button>
                        `;
                        } else if (dataObj.invoiceStatus == 14) {
                            obj = `
                        <button class="btn btn-sm btn-success" data-id="${btoa(soInvId)}" data-no="${dataObj.invoice_no}" id="acceptInv"><i class="fa fa-check mr-2"></i>Approve</button>
                        <button class="btn btn-sm btn-danger " data-id="${btoa(soInvId)}" data-no="${dataObj.invoice_no}" id="rejectInv"><ion-icon name="close-outline"></ion-icon>Reject</button>
                        `;
                        } else {
                            if (dataObj.irn == null || dataObj.irn == '') {
                                // console.log("irn is not defined or null");
                                obj = `<button class="btn btn-sm btn-primary generateEInvoice" id="generateEInvoiceBtn" data-id="${soInvId}" data-no="${dataObj.invoice_no}">Generate E-invoice</button>`;
                            } else {

                                if (responseObj.ewbNo == "" || responseObj.ewbNo == null) {
                                    obj = `
                                            <button class="btn btn-sm btn-primary eBillGenBtns" >E-Invoice Generated</button>
                                            <button class="btn btn-sm btn-primary eBillGenBtns" data-bs-toggle="modal" id="generateEwayBillModalBtn" data-bs-target="#generateEBillModal">Generate E-way Bill</button>`;

                                } else {

                                    obj = `
                                            <button class="btn btn-sm btn-primary eBillGenBtns" >E-Invoice Generated</button>
                                            <button class="btn btn-sm btn-primary">E-way Bill Generated</button>`;
                                }

                            }
                        }
                    }

                    $("#navBTns").html(obj);

                    // customer details section 
                    $("#custName").html(dataObj.trade_name);
                    $("#custCode").html(dataObj.customer_code);
                    // if (countrycode != 103) {
                    //     $(".gstin").hide();
                    // } else {
                    //     $("#custgst").html(dataObj.customer_gstin);
                    //     // set Title by Given Id 
                    //     setTitleAttributeById('custgst', dataObj.customer_gstin);
                    // }
                    // $("#custpan").html(dataObj.customer_pan);
                    $("#billAddress").html(dataObj.customer_billing_address);
                    $("#shipAddress").html(dataObj.customer_shipping_address);
                    // $("#placeofSup").html(dataObj.placeOfSupply + "(" + responseObj.placeOfsupply + ")");
                    $("#custEmail").html(dataObj.customer_authorised_person_email);
                    $("#custPhone").html(dataObj.customer_authorised_person_phone);

                    //others details section
                    $("#invDate").html(" : " + formatDate(dataObj.invoice_date));

                    $("#soNum").html(" : " + responseObj.so_number);
                    $("#invTime").html(" : " + dataObj.invoice_time);
                    $("#delvDate").html(" : " + dataObj.delivery_date);
                    // $("#validTill").html(" : " + dataObj.validityperiod);
                    // $("#cusOrderno").html(" : " + dataObj.customer_po_no);
                    $("#creditPeriod").html(" : " + dataObj.credit_period);
                    $("#salesPerson").html(" : " + dataObj.kamName);
                    $("#funcnArea").html(" : " + dataObj.functionalities_name);
                    // $("#compilaceInv").html(" : " + dataObj.compInvoiceType);
                    // $("#refDoc").html(" : " + dataObj.fileName);

                    // card calculation
                    let taxableAmt = 0;
                    let igst = 0;
                    let cgst = 0;
                    let sgst = 0;

                    let subTotal = parseFloat(responseObj.allSubTotal);
                    // alert(subTotal);
                    // alert(subTotal);

                    let totalTax = dataObj.totalTax;

                    let totalDiscount = parseFloat(dataObj.totalDiscount) || 0;
                    let totalCashDiscount = parseFloat(dataObj.totalCashDiscount) || 0;
                    let disCount = totalDiscount + totalCashDiscount;

                    let tcsAmount = dataObj.tcs_amount;
                    if (tcsAmount > 0 && tcsAmount != null && tcsAmount != undefined) {
                        $(".tcsAmount").show();
                        $("#tcsAmt").html(decimalAmount(tcsAmount));
                    }
                    let totalAmt = parseFloat(dataObj.all_total_amt);


                    if (disCount == 0) {
                        taxableAmt = parseFloat(subTotal);
                    } else {
                        taxableAmt = parseFloat(subTotal) - disCount;
                    }


                    if (dataObj.igst == 0 && (dataObj.cgst > 0 || dataObj.sgst > 0)) {
                        cgst = parseFloat(dataObj.cgst);
                        sgst = parseFloat(dataObj.sgst);
                    } else {
                        igst = parseFloat(dataObj.igst);
                    }

                    $("#cardSoNo").html(dataObj.so_number);
                    $("#cardCustPo").html(dataObj.customer_po_no);
                    $("#totalItem").html(decimalQuantity(dataObj.totalItems) + " " + "Items");
                    $("#sub_total").html(responseObj.companyCurrency + " " + decimalAmount(subTotal));
                    $("#totalDis").html(responseObj.companyCurrency + " " + decimalAmount(disCount));
                    $("#taxableAmt").html(responseObj.companyCurrency + " " + decimalAmount(taxableAmt));
                    $("#total_amount").html(responseObj.companyCurrency + " " + decimalAmount(dataObj.all_total_amt));
                    $("#remark").html(responseObj.dataObj.remarks);
                    $("#csgst").hide();
                    $("#igstP").hide();
                    $("#igst").hide();
                    $("#csgstVal").hide();


                    if (countrycode == 103) {
                        if (!placeOfSupply) { //CHEKING IF PLACE OF SUPPLY IS EXIST
                            $("#csgst").css("display", "none");
                            $("#igstP").css("display", "none");
                            $("#igstP").hide();
                            $("#igst").hide();
                            $('#ccompval').hide();
                            cleardiv();

                        } else {
                            if (dataObj.igst == 0 && (dataObj.cgst > 0 || dataObj.sgst > 0)) {
                                cleardiv();
                                $("#csgst").css("display", "block");
                                $("#igstP").css("display", "none");
                                $("#igstP").hide();
                                $("#igst").hide();
                                $("#csgstVal").show();
                                $("#cgstVal").show().html(responseObj.companyCurrency + " " + decimalAmount(cgst));
                                $("#sgstVal").show().html(responseObj.companyCurrency + " " + decimalAmount(sgst));
                            } else if (dataObj.igst > 0) {
                                cleardiv();
                                $("#csgst").css("display", "none");
                                $("#igstP").css("display", "block");
                                $("#igst").show();
                                $("#igst").html(responseObj.companyCurrency + " " + decimalAmount(igst));
                            }
                        }
                    } else {
                        // alert("called")
                        $.each(taxComponents, function(index, component) {
                            // console.log(decimalAmount(component.taxAmount));
                            $('#tcomtype').css("display", "block");
                            $("#igstP").hide();
                            $("#igst").hide();
                            $('#tcompname').html(component.gstType);
                            $("#compval").html(responseObj.companyCurrency + " " + decimalAmount(component.taxAmount));
                        })
                    }


                    // item table section
                    let itemsObj = responseObj.itemDetail;
                    $.each(itemsObj, function(index, val) {
                        if (countrycode != 103) {
                            $("#GstInd").hide();
                            $("#taxName").css("display", "block");
                            $("#GstName").html(responseObj.gstName);
                            $("#GstInd2").hide();
                            $("#taxName2").css("display", "block");
                            $("#GstName2").html(responseObj.gstName);

                        }
                        let td = ` <div class="row body-state-table">
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.itemCode}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${val.itemName}">${val.itemName}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.hsnCode}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${decimalQuantity(val.qty)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${responseObj.companyCurrency}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${decimalAmount(val.rate)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${decimalAmount(val.subTotal)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${decimalAmount(val.total_discount)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${decimalAmount(val.taxAbleAmount)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${decimalQuantity(val.tax)}%</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${decimalAmount(val.gstAmount)}</div>
                                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${decimalAmount(val.itemTotalAmount)}</div>
                                                            </div>
                                                            `;
                        $("#currencyHead").html(responseObj.companyCurrency);

                        $("#itemTableBody").append(td);

                    });


                    // customer printpreview
                    if (dataObj.currency_id != responseObj.compCurrencyId && dataObj.currency_id != "") {
                        $(".customerPrintView").show();
                        $("#custInvNav").html(`(${dataObj.currency_name})`);
                        loadPrintView(soInvId, "customer");
                    } else {
                        $(".customerPrintView").hide();
                    }
                    loadPrintView(soInvId, "company");

                    // trail create and update 
                    $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                    $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

                }
                $("#globalModalLoader").remove();
            },
            complete: function() {
                $("#globalModalLoader").remove();
            }
        });


    });

    // accept invoice 
    $(document).on("click", "#acceptInv", function() {
        let invId = $(this).data('id');
        let invNo = $(this).data('no');
        Swal.fire({
            icon: 'warning',
            title: `Are you confirmed to Accept this Invoice( ${invNo} )?`,
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: "ajaxs/modals/so/ajax-manage-invoices-modal-taxComponents.php",
                    dataType: 'json',
                    data: {
                        act: "acceptInv",
                        soInvId: atob(invId)
                    },
                    beforeSend: function() {},
                    success: function(response) {
                        if (response.status == "success") {
                            Swal.fire({
                                icon: `success`,
                                title: `Success`,
                                text: `${response.message}`,
                            });
                        } else {
                            Swal.fire({
                                icon: `warning`,
                                title: `Opps!`,
                                text: `${response.message}`,
                            });
                        }
                        location.reload();
                    }
                });
            }
        });
    });
    // reject invoice
    $(document).on('click', "#rejectInv", function() {
        let invId = $(this).data('id');
        let invNo = $(this).data('no');
        // console.log(invId, invNo);
        Swal.fire({
            icon: 'warning',
            title: `Are you confirmed to Reject this Invoice( ${invNo} )?`,
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: "ajaxs/modals/so/ajax-manage-invoices-modal-taxComponents.php",
                    dataType: 'json',
                    data: {
                        act: "rejectInv",
                        soInvId: invId
                    },
                    beforeSend: function() {
                        // console.log("Sending data.......");
                    },
                    success: function(response) {
                        // console.log(response);
                        if (response.status == "success") {
                            Swal.fire({
                                icon: `success`,
                                title: `Success`,
                                text: `${response.message}`,
                            });
                        } else {
                            Swal.fire({
                                icon: `warning`,
                                title: `Opps!`,
                                text: `${response.message}`,
                            });
                        }
                        location.reload();
                    }
                });
            }
        });
    });

    // select print template dropdown for company
    // $(document).on("change", "#templateSelectorCompany", function() {

    //     loadPrintView(soInvId, "company");
    //     let templateId = $("#templateSelectorCompany").val();
    //     document.getElementById('printChkbox').addEventListener('change', function() {
    //         if (this.checked) {
    //             let companyPrint = `classic-view/invoice-preview-print-taxcomponents.php?invoice_id=${btoa(soInvId)}&type=company&template_id=${templateId}&printChkbox`;
    //             $('#classicViewPrintCompany').attr("href", companyPrint);
    //         } else {

    //             let companyPrint = `classic-view/invoice-preview-print-taxcomponents.php?invoice_id=${btoa(soInvId)}&type=company&template_id=${templateId}`;
    //             $('#classicViewPrintCompany').attr("href", companyPrint);
    //         }
    //     });


    // })
    $(document).on("change", "#templateSelectorCompany, #printChkbox", function() {
        loadPrintView(soInvId, "company");
        let templateId = $("#templateSelectorCompany").val();
        let printChkbox = document.getElementById('printChkbox').checked;

        let companyPrint = `classic-view/invoice-preview-print-taxcomponents.php?invoice_id=${btoa(soInvId)}&type=company&template_id=${templateId}`;
        if (printChkbox) {
            companyPrint += `&printChkbox`;
        }

        $('#classicViewPrintCompany').attr("href", companyPrint);
    });
    // select print template dropdown for customer

    $(document).on("change", "#templateSelectorCustomer", function() {
        loadPrintView(soInvId, "customer");
        let templateId = $("#templateSelectorCustomer").val();

        document.getElementById('printChkbox').addEventListener('change', function() {
            if (this.checked) {
                let customerPrint = `classic-view/invoice-preview-print-taxcomponents.php?invoice_id=${btoa(soInvId)}&type=customer&template_id=${templateId}&printChkbox`;
                $('#classicViewPrintCustomer').attr("href", customerPrint);
            } else {
                let customerPrint = `classic-view/invoice-preview-print-taxcomponents.php?invoice_id=${btoa(soInvId)}&type=customer&template_id=${templateId}`;
                $('#classicViewPrintCustomer').attr("href", customerPrint);
            }
        });

    })

    $(document).on("click", "#generateEwayBillModalBtn", function() {
        $('#generateEBillModal').modal('show');
    })
</script>



<!-- script for Edit and actions view -->
<script>
    // Invoice edit btn for redirection---->
    $(document).on('click', '.editInvBtn', function() {
        let id = $(this).data('id');
        let code = $(this).data('code');
        let url = `invoice-creation.php?edit_invoice=${btoa(id)}`;
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `Are you sure to Edit this so ${code} ?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Edit'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }

        });
    })
    $(document).on('click', '.repostInvoice', function() {
        let id = $(this).data('id');
        let code = $(this).data('code');
        let url = `invoice-creation.php?repost_invoice=${btoa(id)}`;
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `Are you sure to Repost this Invoice( ${code} ) ?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Repost'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }

        });
    })
</script>

<!--  js  for create button  -->
<script>
    $(document).ready(function() {
        $(document).on("click", "button.page-list", function() {
            let buttonId = $(this).attr("id");
            $("#modal-container").removeAttr("class").addClass(buttonId);
            $(".mobile-transform-card").addClass("modal-active");
        });

        $(document).on("click", ".btn-close-modal", function() {
            $("#modal-container").toggleClass("out");
            $(".mobile-transform-card").removeClass("modal-active");
        });
        $(document).on("mouseenter", "#invoiceCreationDrop", function() {
            $("#collectDropdown").removeClass("show");

            $("#invoiceDropdown").addClass("show");
        });


        $(document).on("mouseenter", "#collectionDrop", function() {
            $("#invoiceDropdown").removeClass("show");

            $("#collectDropdown").addClass("show");
        });

    })
</script>

<!-- old page script  start -->
<script>
    // Reverse the Invoice 
    let revInvCount = 0;
    $(document).on("click", ".reverseInvoice", function(e) {
        e.preventDefault();
        var dep_keys = $(this).data('id');
        var $this = $(this);

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
                if (revInvCount == 0) {
                    $.ajax({
                        type: 'POST',
                        data: {
                            dep_keys: dep_keys,
                            dep_slug: 'reverseInvoice'
                        },
                        url: 'ajaxs/ajax-reverse-post.php',
                        beforeSend: function() {
                            revInvCount += 1;
                            $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                        },
                        success: function(response) {
                            let responseObj = JSON.parse(response);
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
                                revInvCount = 0;
                                location.reload();
                            });
                        }
                    });
                }

            }
        });
    });
    // generate E-invoice
    $(document).on("click", ".generateEInvoice", function(e) {
        // let btnId = $(this).attr("id");
        let invId = $(this).data('id');
        let invNO = $(this).data('no');

        // check confirmation
        Swal.fire({
            icon: 'warning',
            title: `Are you confirmed to  generate an E-invoice for this Invoice( ${invNO} )?`,
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                // send request to server
                $.ajax({
                    url: '<?= BASE_URL ?>branch/ajaxs/compliance/ajax-create-e-invoice.php',
                    type: 'POST',
                    data: {
                        invoiceId: invId
                    },
                    beforeSend: function() {
                        // $(`#${btnId}`).html("Generating...");
                    },
                    success: function(response, status, xhr) {
                        let responseData = JSON.parse(response);
                        console.log(responseData);
                        if (responseData["status"] == "success") {
                            // $(`#${btnId}`).html("Generated");
                            // $(`#${btnId}`).removeClass("btn-primary");
                            // $(`#${btnId}`).addClass("btn-success");
                            Swal.fire({
                                icon: `success`,
                                title: `Success`,
                                text: `${responseData["message"]}`,
                            });
                        } else {
                            // $(`#${btnId}`).html("Try again");
                            // $(`#${btnId}`).html("Generate");
                            // alert(`${responseData["message"]}`);
                            Swal.fire({
                                icon: `warning`,
                                title: `Opps!`,
                                text: `${responseData["message"]}`,
                            });
                        }
                    },
                    error: function(jqXhr, textStatus, errorMessage) {
                        Swal.fire({
                            icon: `warning`,
                            title: `Opps!`,
                            text: `${errorMessage}`,
                        });
                        // alert(`${errorMessage}`);
                        console.log(errorMessage);
                    }
                });
            }
        });



    });
    // script for submit E way bill
    $(document).on("click", ".submitForm", function(e) {
        e.preventDefault();
        //  console.log("ok");        
        var formData = new FormData($('#generateEbillform')[0]);
        // console.log("Value of irn:", formData.get("irn"));
        // formData.forEach(function(value, key){
        //     console.log(key + ": " + value);
        // });

        $.ajax({
            url: '../ajaxs/compliance/ajax-create-e-way-bill.php', // Replace this with your server URL
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                // Handle success response here
                console.log(res);
                try {
                    let response = JSON.parse(res);
                    if (response.status == "success") {
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        }).then(() => {
                            location.reload();
                        });
                    }
                } catch (error) {
                    console.error("Invalid JSON string", error);
                }
            },
            error: function(xhr, status, error) {
                // Handle error response here
                console.error(xhr.responseText);
            }
        });
    });
</script>
<script>
    $(document).on("click", ".tcContent", function() {
        // alert(1);
        var selectedValue = $(this).data('value');
        //console.log(selectedValue);

        $.ajax({
            url: 'ajaxs/so/ajax-tc.php', // Replace with your API endpoint or server URL
            type: 'GET',
            data: {
                value: selectedValue, // Send the selected value to the server
                act: "tc"
            },

            success: function(response) {
                console.log(response);
                let obj = JSON.parse(response);

                $('.tc-modal-title').html(obj['termHead']);
                $('.tc-modal-body').html(obj['termscond']);
                // Assuming the response contains the content you want to show in the modal
                // You can adjust this depending on your response structure
                // $('#modalBody').html(response.data); // Populate the modal with the response data
            },
            error: function(error) {
                // Handle any error that occurs during the AJAX request
                console.log('Error:', error);
                $('#modalBody').html('An error occurred while fetching the data.');
            }
        });

    });
</script>


<!-- old page script  end -->

<!-- 
<script>
    //  clean the # extra at end of any url
    // can be added later to footer for every page

    (function() {
  let currentUrl = window.location.href;

  if (currentUrl.endsWith('#')) {
    let newUrl = currentUrl.slice(0, -1);
    window.location.replace(newUrl);
  }
})();
</script> -->