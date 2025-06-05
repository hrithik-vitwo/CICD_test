<?php
require_once("../../app/v1/connection-branch-admin.php");

if (!isset($_COOKIE["cookiedebitNotes"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiedebitNotes", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}

$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    echo "Session Timeout";
    exit;
}
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");

$countryCode = $_SESSION['logedBranchAdminInfo']['companyCountry'];
$components = getLebels($countryCode)['data'];
$componentsjsn = json_decode($components, true);
$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Debit Note No.',
        'slag' => 'debit_note_no',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Debitor Type',
        'slag' => 'debitor_type',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Party',
        'slag' => 'party_code',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Party Name',
        'slag' => 'party_name',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Reference',
        'slag' => 'ref',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Posting Date',
        'slag' => 'postingDate',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Remarks',
        'slag' => 'remark',
        'icon' => '<ion-icon name="albums-outline"></ion-icon>',
        'dataType' => 'string'
    ],
];
if ($componentsjsn['source_add']) {
    $columnMapping[] =   [
        'name' => 'Source Address',
        'slag' => 'source_address',
        'icon' => '<ion-icon name="code-outline"></ion-icon>',
        'dataType' => 'string'
    ];
}
if ($componentsjsn['dest_add']) {
    $columnMapping[] = [
        'name' => 'Destination Address',
        'slag' => 'destination_address',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'string'
    ];
}
$columnMapping[] =   [
    'name' => 'Taxable Amount',
    'slag' => 'taxableAmount',
    'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
    'dataType' => 'number'
];

$taxRule = getItemTaxRule($countryCode);
if ($countryCode == '103') {
    // Add GST-specific columns for country 103
    $columnMapping[] = [
        'name' => 'Cgst',
        'slag' => 'cgst',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ];
    $columnMapping[] = [
        'name' => 'Sgst',
        'slag' => 'sgst',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ];
    $columnMapping[] = [
        'name' => 'Igst',
        'slag' => 'igst',
        'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
        'dataType' => 'number'
    ];
} else if ($taxRule['status'] === "success") {
    $taxSql = queryGet("SELECT * FROM `erp_tax_rulebook` WHERE `country_id` =" . $countryCode . "", true);
    $taxName = $taxSql['data'];
    foreach ($taxName as $row) {
        $taxSplit = json_decode($row['tax_spit_ratio'], true);
        foreach ($taxSplit['tax'] as $tax) {
            $columnMapping[] =
                [
                    'name' => $tax['taxComponentName'],
                    'slag' => $tax['taxComponentName'],
                    'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
                    'dataType' => 'number'
                ];
        }
    }
}

// Add final static columns
$columnMapping[] = [
    'name' => 'Total Value',
    'slag' => 'total',
    'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
    'dataType' => 'number'
];
$columnMapping[] = [
    'name' => 'Created By',
    'slag' => 'created_by',
    'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
    'dataType' => 'string'
];
$columnMapping[] = [
    'name' => 'Status',
    'slag' => 'status',
    'icon' => '<ion-icon name="cloud-circle-outline"></ion-icon>',
    'dataType' => 'string'
];



?>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<style>
    .global-view-modal .modal-body {
        overflow: auto;
    }
</style>
<style>
    table.classic-view td p {
        margin: 5px 0;
        font-size: 12px;
        white-space: normal !important;
    }
</style>

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-sales-orders is-debit-note vitwo-alpha-global">
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
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space"
                                        style="width:100%">

                                        <div class="left-block">
                                            <div class="label-select">
                                                <h3 class="card-title mb-0">Manage Debit Notes</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()"><i
                                                    class="fa fa-expand fa-2x"></i></button>
                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>



                            <div class="card card-tabs mb-0" style="border-radius: 20px;">
                                <div class="card-body">
                                    <!-- <div class="row filter-search">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <div class="row table-header-item">

                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            
                                        </div>
                                    </div> -->
                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane dataTableTemplate dataTable_stock fade show active"
                                            id="listTabPan" role="tabpanel" aria-labelledby="listTab"
                                            style="background: #fff; border-radius: 20px;">

                                            <div class="filter-action">
                                                <a type="button" class="btn add-col setting-menu" data-toggle="modal"
                                                    data-target="#myModal1"> <ion-icon
                                                        name="settings-outline"></ion-icon> Manage Column</a>
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
                                            <a href="debit-note-creation-taxrule.php?create" class="btn btn-create"
                                                type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a>


                                            <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
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
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieTableStockReport"], true) ?? [];

                                                                            foreach ($columnMapping as $index => $column) {

                                                                            ?>
                                                                                <tr>
                                                                                    <td valign="top" style="width: 165px">

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
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", ">=", ">", "<", "<=", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex => $column) {
                                                                            if ($columnIndex === 0 || $columnIndex === 7 || $columnIndex === 8 || $columnIndex === 9 || $columnIndex === 10||$columnIndex===5) {
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
                                                                                        } elseif ($column['dataType'] === "number") {
                                                                                            $operator = array_slice($operators, 2, 6);
                                                                                            foreach ($operator as $oper) { ?>
                                                                                                <option value="<?= $oper ?>">
                                                                                                    <?= $oper ?>
                                                                                                </option>
                                                                                                <?php }
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
                                                                    class="btn btn-primary"
                                                                    data-dismiss="modal">Reset</button>
                                                                <button type="submit" id="serach_submit"
                                                                    class="btn btn-primary"
                                                                    data-dismiss="modal">Search</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Global View start-->

                        <div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog"
                            aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success"
                                role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="top-details">
                                            <div class="left">
                                                <p class="info-detail amount" id="amounts">
                                                    <ion-icon name="wallet-outline"></ion-icon>
                                                    <span class="amount-value" id="crNoteNo"> </span>
                                                </p>
                                                <span class="amount-in-words" id="amount-words"></span>
                                                <p class="info-detail po-number"><ion-icon
                                                        name="information-outline"></ion-icon><span id="refNo"> </span>
                                                </p>
                                            </div>
                                            <div class="right">
                                                <p class="info-detail name"><ion-icon
                                                        name="business-outline"></ion-icon><span id="cus_name"></span>
                                                </p>
                                                <p class="info-detail default-address"><ion-icon
                                                        name="location-outline"></ion-icon><span id="default_address">

                                                    </span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                        <nav>
                                            <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                <button class="nav-link ViewfirstTab active" id="nav-overview-tab"
                                                    data-bs-toggle="tab" data-bs-target="#nav-overview" type="button"
                                                    role="tab" aria-controls="nav-overview"
                                                    aria-selected="true"><ion-icon
                                                        name="apps-outline"></ion-icon>Overview</button>
                                                <button class="nav-link classicview-btn classicview-link"
                                                    id="nav-classicview-tab" data-id="" data-bs-toggle="tab"
                                                    data-bs-target="#nav-classicview" type="button" role="tab"
                                                    aria-controls="nav-classicview" aria-selected="true"><ion-icon
                                                        name="print-outline"></ion-icon>Print Preview</button>
                                                <button class="nav-link auditTrail" id="nav-trail-tab"
                                                    data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode=""
                                                    type="button" role="tab" aria-controls="nav-trail"
                                                    aria-selected="false"><ion-icon
                                                        name="time-outline"></ion-icon>Trail</button>
                                            </div>
                                        </nav>
                                        <div class="tab-content global-tab-content" id="nav-tabContent">

                                            <div class="tab-pane fade transactional-data-tabpane show active"
                                                id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                <div class="d-flex nav-overview-tabs">
                                                    <div id="createEinvoiceDiv"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                                        <div class="items-table">
                                                            <h4>Party Details</h4>
                                                            <div class="customer-details">
                                                                <div class="name-code">
                                                                    <div class="details name">
                                                                        <p id="custName"></p>
                                                                    </div>
                                                                    <div class="details code">
                                                                        <p id="custCode"></p>
                                                                    </div>
                                                                </div>
                                                                <div id="businessTaxIDDiv" class="details gstin">
                                                                    <label id="businessTaxID" for="">GSTIN</label>
                                                                    <p id="custgst"></p>
                                                                </div>
                                                                <div id="taxNumberDiv" class="details pan">
                                                                    <label id="taxNumber" for="">PAN</label>
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
                                                                        <!-- <div class="details">
                                                                                                <label for="">Place of Supply</label>
                                                                                                <p id="placeofSup"></p>
                                                                                            </div> -->
                                                                    </div>
                                                                    <div class="contact-customer">
                                                                        <div class="details dotted-border-area">
                                                                            <label for="">Contacts</label>
                                                                            <p> <ion-icon
                                                                                    name="mail-outline"></ion-icon><span
                                                                                    id="custEmail"> </span></p>
                                                                            <p> <ion-icon
                                                                                    name="call-outline"></ion-icon><span
                                                                                    id="custPhone"></span></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- <div class="items-table">
                                                                                <h4>Other Details</h4>
                                                                                <div class="other-info">
                                                                                    <div class="details">
                                                                                        <label for="">Posting Date</label>
                                                                                        <p id="postingDate"></p>
                                                                                    </div>
                                                                                    <div class="details">
                                                                                        <label for="">Posting Time</label>
                                                                                        <p id="postingTime"> </p>
                                                                                    </div>
                                                                                    <div class="details">
                                                                                        <label for="">Delivery Date</label>
                                                                                        <p id="delvDate"></p>
                                                                                    </div>
                                                                                    <div class="details">
                                                                                        <label for="">Valid Till</label>
                                                                                        <p id="validTill"></p>
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
                                                                                        <p id="funcnArea"></p>
                                                                                    </div>
                                                                                    <div class="details">
                                                                                        <label for="">Compliance Invoice Type</label>
                                                                                        <p id="compilaceInv"></p>
                                                                                    </div>
                                                                                    <div class="details">
                                                                                        <label for="">Reference Document Link</label>
                                                                                        <p>: <a href="#" id="refDoc"></a></p>
                                                                                    </div>
                                                                                </div>
                                                                            </div> -->
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
                                                                                <!-- <p class="name" id="cardCustPo"></p> -->
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
                                                                                <p id="discount_lable" style="display: none;">Discount</p>
                                                                                <p id="tcs_lable" style="display: none;">TCS</p>
                                                                                <p id="tds_lable" style="display: none;">TDS</p>
                                                                                <p id="igstP">IGST</p>
                                                                                <div id="csgst" style="display: none;">
                                                                                    <p>CGST</p>
                                                                                    <p>SGST</p>
                                                                                </div>
                                                                                <div id="tcomtype" style="display: none;">
                                                                                    <p id="tcompname"></p>
                                                                                </div>
                                                                                <p id="adjust_lable" style="display: none;">Round-Up</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="right-info">
                                                                            <div class="item-info">
                                                                                <p id="sub_total"></p>
                                                                                <p id="discountt" style="display: none;"></p>
                                                                                <p id="tcs_amount" style="display: none;"></p>
                                                                                <p id="tds_amount" style="display: none;"></p>
                                                                                <p id="igst"></p>
                                                                                <div id="csgstVal">
                                                                                    <p id="cgstVal"></p>
                                                                                    <p id="sgstVal"></p>
                                                                                </div>
                                                                                <div id="ccompval">
                                                                                    <p id="compval"></p>
                                                                                </div>
                                                                                <p id="adjust_value" style="display: none;"></p>

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
                                                                <!-- <div class="items-table">
                                                                                        <div class="details">
                                                                                            <label for="">Remarks</label>
                                                                                            <p id="remark"></p>
                                                                                        </div>
                                                                                    </div> -->
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
                                                                    <table>
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Sl No.</th>
                                                                                <th>Item Name</th>
                                                                                <th>HSN</th>
                                                                                <th>Quantity</th>
                                                                                <th>UOM</th>
                                                                                <th>Rate</th>
                                                                                <th>Total Discount</th>
                                                                                <th>Taxable Amount</th>
                                                                                <th id="gstlabel"></th>
                                                                                <th><span id="gstAmtlabel"></span>
                                                                                    Amount(<span
                                                                                        id="currencyHead"></span>)</th>
                                                                                <th>Total Amount</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="itemTableBody">

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                            <div class="tab-pane classicview-pane fade" id="nav-classicview"
                                                role="tabpanel" aria-labelledby="nav-classicview-tab">
                                                <a href="" class="btn btn-primary classic-view-btn float-right"
                                                    id="classicViewPrint" target="_blank">Print</a>
                                                <div class="card classic-view bg-transparent">

                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="nav-trail" role="tabpanel"
                                                aria-labelledby="nav-trail-tab">
                                                <div class="inner-content">
                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                        <p class="text-xs font-italic"><span
                                                                class="font-bold text-normal">Created by </span> <span
                                                                id="createdBy"></span> <span
                                                                class="font-bold text-normal"> on </span> <span
                                                                id="createdAt"></span></p>
                                                        <p class="text-xs font-italic"> <span
                                                                class="font-bold text-normal">Last Updated by</span>
                                                            <span id="updatedBy"></span> <span
                                                                class="font-bold text-normal"> on </span> <span
                                                                id="updatedAt"></span>
                                                        </p>
                                                    </div>
                                                    <hr>
                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent">


                                                    </div>
                                                    <div class="modal fade right audit-history-modal" id="innerModal"
                                                        role="dialog" aria-labelledby="innerModalLabel"
                                                        aria-modal="true">
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
    </section>
    <!-- /.content -->
</div>

<?php
require_once("../common/footer2.php");
?>


<script>
    function cleardiv() {
        $('#tds_amount').hide().text('');
        $('#tcs_amount').hide().text('');
        $('#discountt').hide().text('');
        $('#tds_lable').hide();
        $('#tcs_lable').hide();
        $('#discount_lable').hide();

        $('#igstP').hide();
        $('#csgst').hide();
        $('#csgstVal').hide();
        $('#sgstVal').hide();
        $('#cgstVal').hide();
        $('#igst').hide().text('');
    }
    let components = <?php echo json_encode($components); ?>;
    components = JSON.parse(components);
    let data;
    var columnMapping = <?php echo json_encode($columnMapping); ?>;
    // console.log(components);
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

                buttons: [
                //     {
                //     extend: 'collection',
                //     text: '<ion-icon name="download-outline"></ion-icon> Export',

                //     buttons: [
                //         {
                //         extend: 'csv',
                //         filename: '<?php echo $newFileName ?>',
                //         text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> Excel',
                //         exportOptions: {
                //             columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                //         }
                //     }
                // ]
                // }
            ],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookiedebitNotes');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-debit-notes-tax.php",
                dataType: 'json',
                data: {
                    act: 'debitnotes',
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

                    // console.log(response);

                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();
                        data=responseObj;
                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);


                        $.each(responseObj, function(index, value) {
                            // console.log(value);
                            let reverseRepostButton = '';
                            if (value.dn_status == 'active' && value.goods_journal_id > 0) {
                                reverseRepostButton = `
                                    <li>
                                        <button class="reverseDebitNote" data-id="${value.dr_note_id}" ><ion-icon name="refresh-outline"></ion-icon>Reverse</button>
                                    </li>`;
                                sClass = `status-bg status-open`;
                            } else if (value.dn_status == 'reverse') {
                                reverseRepostButton = `
                                    <li>
                                        <button class="repostDebitNote" data-id="${value.dr_note_id}" data-code="${value.debit_note_no}" ><ion-icon name="repeat-outline"></ion-icon>Repost</button>
                                    </li>`;
                                sClass = `status-bg status-open`;

                            }
                            // alert(value.total);

                            const rowData = [
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.dr_note_id}" data-toggle="modal" data-target="#viewGlobalModal">${value.debit_note_no}</a>`,
                                value.debitor_type,
                                value.party_code,
                                value.party_name,
                                value.ref,
                                value.postingDate,
                                value.remark,
                            ];

                            // Conditionally add source address only if source_add is true
                            if (components['source_add']) {
                                rowData.push(`<p class='pre-normal'>${value.source_address}</p>`);
                            }



                            if (components['dest_add']) {
                                rowData.push(`<p class='pre-normal'>${value.destination_address}</p>`);
                            };
                            // Add taxable amount
                            rowData.push(decimalAmount(value.taxableAmount) ?? '-');

                            // Handle countryCode logic for tax amounts
                            if (value.countryCode === '103') {
                                rowData.push(
                                    decimalAmount(value.cgst) ?? '0.00',
                                    decimalAmount(value.sgst) ?? '0.00',
                                    decimalAmount(value.igst) ?? '0.00'
                                );
                            } else if (value.taxComponents && value.taxComponents.length > 0) {
                                value.taxComponents.forEach((tax) => {
                                    rowData.push(decimalAmount(tax.taxAmount) ?? '0.00');
                                });
                            } else {
                                rowData.push('0.00'); // Default tax amount if no tax components exist
                            }

                            // Add total, created_by, and status
                            rowData.push(
                                decimalAmount(value.total) ?? '-',
                                value.created_by ?? '-',
                                value.status_t ?? '-'
                            );

                            // Add action buttons
                            rowData.push(`
                                            <div class="dropout">
                                                <button class="more">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </button>
                                                <ul>
                                                    <li>
                                                        <button data-toggle="modal" class="soModal" data-id="${value.dr_note_id}" data-target="#viewGlobalModal">
                                                            <ion-icon name="create-outline" class="ion-view"></ion-icon>View
                                                        </button>
                                                    </li>
                                                    ${reverseRepostButton}
                                                </ul>
                                            </div>
                                            `);

                            // Add the row to the DataTable
                            dataTable.row.add(rowData).draw(false);
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
                            // //console.log("notVisibleColArr index:", notVisibleColArr);
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }

                            //console.log('Cookie value:', checkboxSettings);

                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);

                                }
                            });

                            //console.log('Cookie is blank.');
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
        $(document).on("click", ".ion-paginationliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(data),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiedebitNotes')
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

        $(document).on("click", "#pagination a ", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $(".custom-select").val();

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

                    if (columnSlag.trim() === 'postingDate') {
                        values = value4;
                    } else if (columnSlag === 'so_date') {
                        values = value2;
                    } else if (columnSlag === 'created_at') {
                        values = value3;
                    }

                    if ((columnSlag.trim() === 'postingDate') && operatorName == "BETWEEN") {
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
                //console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs);
                $("#myForm")[0].reset();
                $(".m-input2").remove();

            });
        });
        $(document).on("click", ".ion-fullliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-debit-notes-tax.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    formDatas: formInputs,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiedebitNotes')
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
                //console.log(columnVal);

                var index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                //console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function() {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function() {
                    var columnVal = $(this).val();
                    // //console.log(columnVal);
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
            // //console.log("Hiiiii");
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

            //console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'debitnotes',
                        fromData: fromData
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        })
                    },
                    error: function(error) {
                        //console.log(error);
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
            if (columnName.trim() === 'Posting Date') {
                inputId = "value4_" + columnIndex;
            }
            if ((columnName.trim() == 'Posting Date') && operatorName == 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input m-input2" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // //console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
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
    document.querySelector('table.stock-new-table').onclick = ({
        target
    }) => {
        if (!target.classList.contains('more')) return
        document.querySelectorAll('.dropout.active').forEach(
            (d) => d !== target.parentElement && d.classList.remove('active')
        )
        target.parentElement.classList.toggle('active')
    }
</script>
<!------------ modal ajax--------- -->

<script>
    $(document).on("click", ".createEinvoiceBtn", function() {
        let drcrId = $(this).data("id");
        Swal.fire({
            icon: 'warning',
            title: `Are you confirmed to generate an E-invoice for this document?`,
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= BASE_URL ?>branch/ajaxs/compliance/ajax-create-drcr-e-invoice.php',
                    type: 'POST',
                    data: {
                        documentId: drcrId,
                        documentType: "DBN" //debit note
                    },
                    beforeSend: function() {
                        $("#createEinvoiceBtn").html("Generating...");
                        $("#createEinvoiceBtn").attr("disabled", true);
                    },
                    success: function(response, status, xhr) {
                        let responseData = JSON.parse(JSON.stringify(response));
                        console.log(responseData);
                        if (responseData["status"] == "success") {
                            Swal.fire({
                                icon: `success`,
                                title: `Success`,
                                text: `${responseData["message"]}`,
                            }).then(() => {
                                location.reload();;
                            });
                        } else {
                            Swal.fire({
                                icon: `warning`,
                                title: `Opps!`,
                                text: `${responseData["message"]}`,
                            }).then(() => {
                                location.reload();;
                            });
                        }
                    },
                    error: function(jqXhr, textStatus, errorMessage) {
                        console.log(errorMessage);
                        Swal.fire({
                            icon: `warning`,
                            title: `Opps!`,
                            text: `${errorMessage}`,
                        }).then(() => {
                            location.reload();;
                        });
                    },
                    complete: function() {
                        console.log("Completed!!!!!!!!!");
                        $("#createEinvoiceBtn").html("Create E-Invoice");
                        $("#createEinvoiceBtn").attr("disabled", false);
                    }
                });
            }
        });
    });

    $(document).on("click", ".soModal", function() {

        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        let dr_note_id = $(this).data('id');
        // $('.auditTrail').attr("data-ccode", dr_note_id);

        $("#createEinvoiceDiv").html('');

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/fa/ajax-debit-note-modal-tax.php",
            dataType: 'json',
            data: {
                act: 'modaldata',
                dr_note_id: dr_note_id
            },
            beforeSend: function() {
                // $('.item-cards').remove();
                // $('#itemTableBody').html('');
                resetAllFields();
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
                console.log(value);
                $('#globalModalLoader').remove();

                var responseObj = value.data;
                var country_labels = responseObj.country_labels;
                var country_fields = country_labels.fields;
                var place_of_supply = country_labels.place_of_supply;

                $('.auditTrail').attr("data-ccode", responseObj.crNoteobj.debit_note_no);
                if (country_fields.businessTaxID != null) {
                    $("#businessTaxID").empty();
                    $("#businessTaxID").html(country_fields.businessTaxID);
                } else {
                    $("#businessTaxIDDiv").empty();
                }
                if (country_fields.taxNumber) {
                    $("#taxNumber").empty();
                    $("#taxNumber").html(country_fields.taxNumber);
                } else {
                    $("#taxNumberDiv").empty();
                }
                var itemsObj = responseObj.item_details;
                //console.log(responseObj);
                let partyDetails = responseObj.partydetails.customerData;
                var itemsObj = responseObj.items;

                let taxName = responseObj.taxName;

                if (responseObj.taxComponents) {
                    taxComponents = JSON.parse(responseObj.taxComponents);
                }

                $('#updatedBy').html(responseObj.updated_by)
                $('#createdBy').html(responseObj.created_by)
                $('#createdAt').html(formatDate(responseObj.created_at))
                $('#updatedAt').html(formatDate(responseObj.updated_at))

                $(".left #crNoteNo").html(responseObj.companyCurrency + " " + decimalAmount(responseObj.crNoteobj.total));
                $("#refNo").html(responseObj.ref);
                $(".right #cus_name").html(responseObj.crNoteobj.debit_note_no);

                // // customer details section 
                // console.log(responseObj.partydetails.debitor_type);
                $("#custName").html(partyDetails.trade_name);
                // alert(responseObj.partydetails.debitor_type);
                let debitor_type = responseObj.partydetails.debitor_type;
                if (debitor_type == 'customer') {

                    $("#custCode").html(partyDetails.customer_code);
                    $("#custgst").html(partyDetails.customer_gstin);
                    $("#custpan").html(partyDetails.customer_pan);
                    $("#custEmail").html(partyDetails.customer_authorised_person_email);
                    $("#custPhone").html(partyDetails.customer_authorised_person_phone);
                } else {

                    $("#custCode").html(partyDetails.vendor_code);
                    $("#custgst").html(partyDetails.vendor_gstin);
                    $("#custpan").html(partyDetails.vendor_pan);
                    $("#custEmail").html(partyDetails.vendor_authorised_person_email);
                    $("#custPhone").html(partyDetails.vendor_authorised_person_phone);
                }

                $("#billAddress").html(responseObj.partydetails.source_address);
                $("#shipAddress").html(responseObj.partydetails.destination_address);
                // $("#placeofSup").html(partyDetails.placeOfSupply + "(" + responseObj.placeOfsupply + ")");


                let partyType = responseObj.crNoteobj.debitor_type;
                // console.log("Party Type:", partyType);

                // if(partyType == "customer"){
                //     $("#createEinvoiceDiv").html(`<button class="btn btn-sm btn-primary createEinvoiceBtn" id="createEinvoiceBtn" data-id="${dr_note_id}">Create E-Invoice</button>`);
                // }

                let dataObj = responseObj.crNoteobj;

                if (dataObj.irn == null || dataObj.irn == '' || dataObj.irn == undefined || dataObj.e_inv_count > 0) {
                    if (partyType == "customer") {
                        $("#createEinvoiceDiv").html(`<button class="btn btn-sm btn-primary createEinvoiceBtn" id="createEinvoiceBtn" data-id="${dr_note_id}">Create E-Invoice</button>`);

                    }
                } else if (dataObj.irn != null && dataObj.irn != '' && dataObj.irn != undefined && partyType == "customer") {
                    $("#createEinvoiceDiv").html(`<button class="btn btn-sm btn-primary">E-Invoice Generated</button>`);
                }

                let totalAmount = responseObj.crNoteobj.total;
                let subTotal = responseObj.subTotalAmt


                let igst = decimalAmount(responseObj.crNoteobj.igst);
                let cgst = decimalAmount(responseObj.crNoteobj.cgst);
                let sgst = decimalAmount(responseObj.crNoteobj.sgst);
                let tds = decimalAmount(responseObj.crNoteobj.tds);
                let tcs = decimalAmount(responseObj.crNoteobj.tcs);
                let discount = decimalAmount(responseObj.total_discount);
                let adjustment = decimalAmount(responseObj.crNoteobj.adjustment);

                // // card details section
                // alert(discount);
                cleardiv();
                if (discount > 0) {
                    $("#discount_lable").show();
                    $("#discountt").show();
                    $("#discountt").show().text(discount);
                } else {
                    $("#discount_lable").hide();
                    $("#discountt").hide();
                    $("#discountt").text(decimalAmount(0));
                }

                if (tcs > 0) {
                    $("#tcs_lable").css("display", "block");
                    $("#tcs_amount").css("display", "block");
                    $("#tcs_amount").text(tcs);

                }
                if (tds > 0) {
                    $("#tds_lable").show();
                    $("#tds_amount").text(tds);
                    $("#tds_amount").show();
                }
                if (adjustment > 0 || adjustment < 0 && adjustment != 0) {
                    $("#adjust_lable").css("display", "block");
                    $("#adjust_value").css("display", "block");
                    $("#adjust_value").text(adjustment);


                } else {
                    $("#adjust_lable").hide();
                    $("#adjust_value").hide();
                    $("#adjust_value").text(decimalAmount(0));
                }
                $("#cardSoNo").html(responseObj.crNoteobj.debit_note_no);
                // $("#totalItem").html(responseObj.dataObj.totalItems + " " + "Items");
                $("#sub_total").html(responseObj.companyCurrency + " " + decimalAmount(subTotal));
                $("#total_amount").html(responseObj.companyCurrency + " " + decimalAmount(totalAmount));
                // $("#remark").html(responseObj.dataObj.remarks);

                if (responseObj.countryCode === '103') {
                    if (igst == 0) {
                        $("#csgst").css("display", "block");
                        $('#csgstVal').show();
                        $('#cgstVal').show();
                        $('#sgstVal').show();
                        // $("#igstP").hide();
                        // $("#igst").hide();
                        $("#cgstVal").html("INR" + " " + decimalAmount(cgst));
                        $("#sgstVal").html("INR" + " " + decimalAmount(sgst));
                    } else {
                        // $("#csgst").hide();
                        // $('#csgstVal').hide();
                        $("#igstP").show();
                        $("#igst").show();
                        $("#igst").html("INR" + " " + decimalAmount(igst));
                    }


                } else {
                    // Clear existing labels and values
                    $("#gstlabels").empty();
                    $("#gstVals").empty();

                    if (taxComponents && taxComponents.length > 0) {

                        taxComponents.forEach(component => {
                            const taxLabel =
                                `<p class="label">${component.gstType}</p>`;

                            $("#gstlabels").append(taxLabel);

                            const taxValue = `
                                                    <p class="gstValues">
                                                        ${responseObj.companyCurrency} ${decimalAmount(parseFloat(component.taxAmount))}
                                                    </p>`;
                            $("#gstVals").append(taxValue);
                        });
                    }
                }

                $("#currencyHead").html(responseObj.companyCurrency);


                // if (igst == 0) {
                //     $("#csgst").css("display", "block");
                //     $("#igstP").hide();
                //     $("#igst").hide();
                //     $("#cgstVal").html("INR" + " " + parseFloat(cgst).toFixed(2));
                //     $("#sgstVal").html("INR" + " " + parseFloat(sgst).toFixed(2));
                // } else {
                //     ////console.log(1);
                //     $("#igst").html("INR" + " " + igst);
                // }



                // // item table section
                $.each(itemsObj, function(index, val) {

                    let td = `                              <tr>
                                                                <td>${val.itemCode}</td>
                                                                <td title="${val.itemName}">${val.itemName}</td>
                                                                <td>${val.hsnCode}</td>
                                                                <td>${decimalQuantity(val.item_qty)}</td>
                                                                <td>${val.uomName}</td>
                                                                <td class="text-right"> ${decimalAmount(val.item_rate)}</td>
                                                                <td class="text-right"> ${decimalAmount(val.item_dis)}</td>
                                                                <td class="text-right"> ${decimalAmount(val.taxbleAmount)}</td>
                                                                <td> ${decimalAmount(val.item_tax)}</td>
                                                                <td class="text-right"> ${decimalAmount(val.gstamt)}</td>
                                                                <td class="text-right"> ${decimalAmount(val.item_amount)}</td>
                                                            </tr>
                             `;
                    $("#currencyHead").html(val.currency);
                    $("#itemTableBody").append(td);

                });

                $("#gstlabel").html(taxName + '(%)')
                $("#gstAmtlabel").html(taxName);

                // $('.closeSoBtn').attr("id", value.so_id + "_" + value.soNo);
            },
            complete: function() {
                // Hide the loader after the request is complete
                $('#viewGlobalModal .modal-body .load-wrapp').remove();
            },
            error: function(error) {
                ////console.log(error);
            }
        });

        // classic view ajax
        $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print-taxcomponents.php?dr_note_id=${btoa(dr_note_id)}`);
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/fa/ajax-debit-note-modal-tax.php",
            data: {
                act: 'classicView',
                dr_note_id: dr_note_id
            },
            success: function(res) {
                // ////console.log(res)
                $('.classic-view').html(res)
            }
        });

    });
</script>

<script>
    $(document).on("click", ".reverseDebitNote", function() {



        var dep_keys = $(this).data('id');
        //alert(dep_keys);

        var $this = $(this); // Store the reference to $(this) for later use

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'You want to reverse this?',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'continue'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    data: {
                        dep_keys: dep_keys,
                        dep_slug: 'reverseDebitNote'
                    },
                    url: 'ajaxs/ajax-reverse-post.php',
                    beforeSend: function() {
                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {

                        var responseObj = JSON.parse(response);
                        console.log(responseObj);


                        if (responseObj.status == 'success') {
                            $this.parent().parent().find('.listStatus').html('Reverse');
                            $this.parent().parent().find('.einvoiceCls').html('--');
                            $this.parent().parent().find('.duedateCls').html('--');
                            $this.hide();
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



    $(document).on("click", ".repostDebitNote", function() {
        let dnId = $(this).data('id');
        let url = `debit-notes-repost_tax.php?dnId=${btoa(dnId)}`;
        let code = $(this).data('code');

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `Are you sure to Repost this Debit Note ${code} ?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Continue',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }

        });

    });

    function resetAllFields() {
        // Clear text content of elements
        $('#custName').text('');
        $('#custCode').text('');
        $('#custgst').text('');
        $('#custpan').text('');
        $('#billAddress').text('');
        $('#shipAddress').text('');
        $('#custEmail').text('');
        $('#custPhone').text('');
        $('#cardSoNo').text('');
        $('#totalItem').text('');
        $('#sub_total').text('');
        $('#igst').text('');
        $('#cgstVal').text('');
        $('#sgstVal').text('');
        $('#total_amount').text('');

        // Hide CGST/SGST fields
        $('#csgst').hide();
        $('#csgstVal').hide();

        // Clear the item table body
        $('#itemTableBody').empty();
    }
</script>