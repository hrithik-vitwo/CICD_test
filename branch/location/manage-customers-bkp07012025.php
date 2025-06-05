<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");

require_once("../common/sidebar.php");
// require_once("../common/pagination.php");
// administratorLocationAuth();



$pageName = basename($_SERVER['PHP_SELF'], '.php');

if (!isset($_COOKIE["cookieManageCustomerList"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieManageCustomerList", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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
        'dataType' => 'number'
    ],
    [
        'name' => 'Customer Code',
        'slag' => 'customer_code',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    // [
    //     'name' => '	Customer Icon',
    //     'slag' => 'icon ',
    //     'icon' => '<ion-icon name="location-outline"></ion-icon>',
    //     'dataType' => 'string'
    // ],
    [
        'name' => 'Customer Name',
        'slag' => 'trade_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Constitution of Business',
        'slag' => '	constitution_of_business ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'GSTIN',
        'slag' => 'customer_gstin ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Email',
        'slag' => 'customer_authorised_person_email ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Phone',
        'slag' => 'customer_authorised_person_phone ',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    // [
    //     'name' => 'Order Volume',
    //     'slag' => ' vol',
    //     'icon' => '<ion-icon name="location-outline"></ion-icon>',
    //     'dataType' => 'string'
    // ],
    // [
    //     'name' => 'Receipt Amount',
    //     'slag' => 'recpamount ',
    //     'icon' => '<ion-icon name="location-outline"></ion-icon>',
    //     'dataType' => 'string'
    // ],
    [
        'name' => 'Status',
        'slag' => ' customer_status',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]

];


?>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<style>
    /* css for onscroll events */
    .innerInvoices_wrapper {
        overflow-y: auto;
        max-height: 500px;
    }

    .innerCollections_wrapper {
        overflow-y: auto;
        max-height: 500px;
    }

    /* .innerCollectionsTableDiV {
        overflow-y: auto;
        max-height: 500px;
    } */

    .innerEstimates_wrapper {
        overflow-y: auto;
        max-height: 500px;
    }

    .innerSalesOrder_wrapper {
        overflow-y: auto;
        max-height: 500px;
    }

    .innerJournals_wrapper {
        overflow-y: auto;
        max-height: 500px;
    }

    .innerDebitNote_wrapper {
        overflow-y: auto;
        max-height: 500px;
    }

    .innerCreditNote_wrapper {
        overflow-y: auto;
        max-height: 500px;
    }


    .innerTableHeadPos {
        position: sticky;
        top: 0px;
        z-index: 1;
    }

    #collapseBasic .accordion-body {
        display: grid;
        grid-template-columns: 4fr;
        gap: 22px;
        background: #cccccc29;
        border-radius: 7px;
        margin-top: 7px;
        height: auto;
        overflow: scroll;
        scrollbar-width: none;
    }

    /* css for datatable dt-top-container fix */
    .innerCustTransDiv .dt-top-container {
        display: flex;
        align-items: center;
        padding: 0px;
        gap: 0;
        height: 3rem;
        position: sticky;
        top: 0;
        left: 0;
        width: 100%;

    }

    .innerCustTransDiv .dataTables_wrapper .custTransDatatable {
        clear: both;
        margin-top: 0px !important;
        margin-bottom: 6px !important;
        max-width: none !important;
        border-collapse: separate !important;
        border-spacing: 0;
    }



    .innerCustTransDiv .dataTables_wrapper .dt-top-container .dataTables_filter {
        display: flex !important;
        align-items: center;
        justify-content: start;
        position: absolute;
        /* right: auto !important; */
        right: 5px !important;
        top: 0px;
    }

    .innerCustTransDiv .dataTables_wrapper .dt-top-container .dataTables_filter input {
        margin-left: 0;
        display: inline-block;
        width: auto;
        padding-left: 30px;
        border: 1px solid #bfbdbd;
        color: #1B2559;
        height: 30px;
        border-radius: 8px;
    }
</style>
<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">Manage Customer</h3>
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
                                                                        <button>
                                                                            <ion-icon name="list-outline"
                                                                                class="ion-fulllist md hydrated"
                                                                                id="exportAllBtn" role="img"
                                                                                aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline"
                                                                                class="ion-paginationlist md hydrated"
                                                                                role="img"
                                                                                aria-label="list outline"></ion-icon>Download
                                                                        </button>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <a href="#" class="btn btn-create mobile-page mobile-create"
                                                                type="button">
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
                                                        <button class="ion-paginationlist">
                                                            <ion-icon name="list-outline"
                                                                class="ion-paginationlist md hydrated" role="img"
                                                                aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllist">
                                                            <ion-icon name="list-outline"
                                                                class="ion-fulllist md hydrated" role="img"
                                                                aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="customer-actions.php?create" class="btn btn-create" type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a>
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
                                                                            if ($columnIndex === 0) {
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

                                            <!-- Global View start-->

                                            <div class="modal right fade global-view-modal" id="viewGlobalModal"
                                                role="dialog" aria-labelledby="myModalLabel" data-backdrop="true"
                                                aria-modal="true">
                                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="top-details">
                                                                <div class="left">
                                                                    <p class="info-detail amount"><ion-icon
                                                                            name="business-outline"></ion-icon><span
                                                                            id="custName"></span></p>
                                                                    <p class="info-detail po-number"><ion-icon
                                                                            name="information-outline"></ion-icon><span
                                                                            id="custCode"></span></p>
                                                                    <p class="info-detail po-number"><ion-icon
                                                                            name="information-outline"></ion-icon><span
                                                                            id="custCob"></span></p>
                                                                    <p class="info-detail ref-number"><ion-icon
                                                                            name="information-outline"></ion-icon><span
                                                                            id="custGst"></span></p>
                                                                </div>
                                                                <div class="right">
                                                                    <p class="info-detail name"><ion-icon
                                                                            name="person-outline"></ion-icon><span
                                                                            id="custPerson"></span></p>
                                                                    <p class="info-detail qty"><ion-icon
                                                                            name="document-outline"></ion-icon><span
                                                                            id="custPersonDesg"></span></p>
                                                                    <p class="info-detail qty"><ion-icon
                                                                            name="call-outline"></ion-icon><span
                                                                            id="custPersonPhone"></span></p>
                                                                    <p class="info-detail qty"><ion-icon
                                                                            name="mail-outline"></ion-icon><span
                                                                            id="custPersonMail"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <nav>
                                                                <div class="nav nav-tabs global-view-navTabs"
                                                                    id="nav-tab" role="tablist">
                                                                    <button class="nav-link active ViewfirstTab"
                                                                        id="nav-overview-tab" data-bs-toggle="tab"
                                                                        data-bs-target="#nav-overview" type="button"
                                                                        role="tab" aria-controls="nav-overview"
                                                                        aria-selected="true"><ion-icon
                                                                            name="apps-outline"></ion-icon>Overview</button>
                                                                    <button class="nav-link" id="nav-transaction-tab"
                                                                        data-bs-toggle="tab"
                                                                        data-bs-target="#nav-transaction" type="button"
                                                                        role="tab" aria-controls="nav-transaction"
                                                                        aria-selected="false"><ion-icon
                                                                            name="repeat-outline"></ion-icon>Transactional</button>
                                                                    <button class="nav-link" id="nav-mail-tab"
                                                                        data-bs-toggle="tab" data-bs-target="#nav-mail"
                                                                        type="button" role="tab"
                                                                        aria-controls="nav-mail"
                                                                        aria-selected="false"><ion-icon
                                                                            name="mail-outline"></ion-icon>Mails</button>
                                                                    <button class="nav-link" id="nav-statement-tab"
                                                                        data-bs-toggle="tab"
                                                                        data-bs-target="#nav-statement" type="button"
                                                                        role="tab" aria-controls="nav-statement"
                                                                        aria-selected="false"><ion-icon
                                                                            name="document-text-outline"></ion-icon>Statement</button>
                                                                    <button class="nav-link" id="nav-compliance-tab"
                                                                        data-bs-toggle="tab"
                                                                        data-bs-target="#nav-compliance" type="button"
                                                                        role="tab" aria-controls="nav-compliance"
                                                                        aria-selected="false"><ion-icon
                                                                            name="analytics-outline"></ion-icon>Compliance
                                                                        Status</button>
                                                                    <button class="nav-link" id="nav-reconciliation-tab"
                                                                        data-bs-toggle="tab"
                                                                        data-bs-target="#nav-reconciliation"
                                                                        type="button" role="tab"
                                                                        aria-controls="nav-reconciliation"
                                                                        aria-selected="false"><ion-icon
                                                                            name="settings-outline"></ion-icon>Reconciliation</button>
                                                                    <button class="nav-link nav-trail-tab"
                                                                        id="nav-trail-tab" data-bs-toggle="tab"
                                                                        data-bs-target="#nav-trail" type="button"
                                                                        role="tab" aria-controls="nav-trail"
                                                                        aria-selected="false"><ion-icon
                                                                            name="time-outline"></ion-icon>Trail</button>
                                                                </div>
                                                                <div id="more-dropdown" class="more-dropdown">
                                                                    <!-- Dropdown menu for additional tabs -->
                                                                </div>
                                                            </nav>

                                                            <div class="tab-content global-tab-content"
                                                                id="nav-tabContent">
                                                                <div class="tab-pane fade show active" id="nav-overview"
                                                                    role="tabpanel" aria-labelledby="nav-overview-tab">
                                                                    
                                                                    <div class="chart-view">
                                                                        <div class="chat-head p-0 border-bottom-0">
                                                                        </div>
                                                                        <div class="load-wrapp">
                                                                            <div class="load-1">
                                                                                <div class="line"></div>
                                                                                <div class="line"></div>
                                                                                <div class="line"></div>
                                                                            </div>
                                                                        </div>
                                                                        <div id="chartDivSalesVsCollection"
                                                                            class="chartContainer">

                                                                        </div>
                                                                    </div>
                                                                    <div class="info-view">
                                                                        <h5 class="title">Details View</h5>
                                                                        <hr>
                                                                        <div class="row" id="DetailsView">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane transaction-tab-pane fade"
                                                                    id="nav-transaction" role="tabpanel"
                                                                    aria-labelledby="nav-transaction-tab">
                                                                    <div class="inner-content">
                                                                        <ul class="nav nav-pills" id="pills-tab"
                                                                            role="tablist">
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link active"
                                                                                    id="pills-invoicesinner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-invoicesinner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-invoicesinner"
                                                                                    aria-selected="true"><ion-icon
                                                                                        name="receipt-outline"></ion-icon>
                                                                                    Invoices</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-collectioninner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-collectioninner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-collectioninner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="podium-outline"></ion-icon>Collection</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-estimatesinner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-estimatesinner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-estimatesinner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="ticket-outline"></ion-icon>Estimates</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-salesorderinner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-salesorderinner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-salesorderinner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="pricetags-outline"></ion-icon>Sales
                                                                                    Order</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-journalinner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-journalinner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-journalinner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="id-card-outline"></ion-icon>Journals</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-debitnotesinner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-debitnotesinner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-debitnotesinner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="document-text-outline"></ion-icon>Debit
                                                                                    Notes</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-creditnotesinner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-creditnotesinner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-creditnotesinner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="document-text-outline"></ion-icon>Credit
                                                                                    Notes</button>
                                                                            </li>
                                                                        </ul>
                                                                        <div class="tab-content" id="pills-tabContent">
                                                                            <div class="tab-pane fade show active"
                                                                                id="pills-invoicesinner" role="tabpanel"
                                                                                aria-labelledby="pills-invoicesinner-tab">
                                                                                <!-- <div
                                                                                    class="length-row inner-length-row">
                                                                                    <span>Show</span>
                                                                                    <select name="" id=""
                                                                                        class="custom-select-innerInvoices"
                                                                                        value="25">
                                                                                        <option value="10">10
                                                                                        </option>
                                                                                        <option value="25"
                                                                                            selected="selected">25
                                                                                        </option>
                                                                                        <option value="50">50
                                                                                        </option>
                                                                                        <option value="100">100
                                                                                        </option>
                                                                                        <option value="200">200
                                                                                        </option>
                                                                                        <option value="250">250
                                                                                        </option>
                                                                                    </select>
                                                                                    <span>Entries</span>
                                                                                </div> -->
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Invoices</h4>
                                                                                        <a href="manage-invoices.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Create
                                                                                            Invoice</a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerInvoicesTableDiV innerCustTransDiv">
                                                                                        <table id="innerInvoices"
                                                                                            class="exportTable custTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>Icon</th>
                                                                                                    <th>Invoice Number
                                                                                                    </th>
                                                                                                    <th>Amount</th>
                                                                                                    <th>Date</th>
                                                                                                    <th>Due in(day/s)
                                                                                                    </th>
                                                                                                    <th>Status</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody id="custTransInv">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                    
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-collectioninner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-collectioninner-tab">
                                                                                <!-- <div
                                                                                    class="length-row inner-length-row">
                                                                                    <span>Show</span>
                                                                                    <select name="" id=""
                                                                                        class="custom-select-innerCollections"
                                                                                        value="25">
                                                                                        <option value="10">10
                                                                                        </option>
                                                                                        <option value="25"
                                                                                            selected="selected">25
                                                                                        </option>
                                                                                        <option value="50">50
                                                                                        </option>
                                                                                        <option value="100">100
                                                                                        </option>
                                                                                        <option value="200">200
                                                                                        </option>
                                                                                        <option value="250">250
                                                                                        </option>
                                                                                    </select>
                                                                                    <span>Entries</span>
                                                                                </div> -->

                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Collections</h4>
                                                                                        <a href="collect-payment.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage Collections</a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerCollectionsTableDiV innerCustTransDiv">
                                                                                        <table id="innerCollections"
                                                                                            class="exportTable custTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>Collection
                                                                                                        Advice
                                                                                                    </th>
                                                                                                    <th>Transaction Id
                                                                                                    </th>
                                                                                                    <th>Collection
                                                                                                        Amount
                                                                                                    </th>
                                                                                                    <th>Collection Type
                                                                                                    </th>
                                                                                                    <th>Date</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody
                                                                                                id="custTransCollection">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                    
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-estimatesinner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-estimatesinner-tab">
                                                                                <!-- <div
                                                                                    class="length-row inner-length-row">
                                                                                    <span>Show</span>
                                                                                    <select name="" id=""
                                                                                        class="custom-select-innerEstimates"
                                                                                        value="25">
                                                                                        <option value="10">10
                                                                                        </option>
                                                                                        <option value="25"
                                                                                            selected="selected">25
                                                                                        </option>
                                                                                        <option value="50">50
                                                                                        </option>
                                                                                        <option value="100">100
                                                                                        </option>
                                                                                        <option value="200">200
                                                                                        </option>
                                                                                        <option value="250">250
                                                                                        </option>
                                                                                    </select>
                                                                                    <span>Entries</span>
                                                                                </div> -->
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Estimates</h4>
                                                                                        <a href="manage-quotations.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage Quotations</a>

                                                                                    </div>
                                                                                    <div
                                                                                        class="innerEstimatesTableDiV innerCustTransDiv">
                                                                                        <table id="innerEstimates"
                                                                                            class="exportTable custTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>Quotation Number
                                                                                                    </th>
                                                                                                    <th>Total Items</th>
                                                                                                    <th>Total Amount
                                                                                                    </th>
                                                                                                    <th>Goods Type</th>
                                                                                                    <th>Posting Date
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody
                                                                                                id="custTransEstimate">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>

                                                                                    
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-salesorderinner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-salesorderinner-tab">
                                                                                <!-- <div
                                                                                    class="length-row inner-length-row">
                                                                                    <span>Show</span>
                                                                                    <select name="" id=""
                                                                                        class="custom-select-innerSoTable"
                                                                                        value="25">
                                                                                        <option value="10">10
                                                                                        </option>
                                                                                        <option value="25"
                                                                                            selected="selected">25
                                                                                        </option>
                                                                                        <option value="50">50
                                                                                        </option>
                                                                                        <option value="100">100
                                                                                        </option>
                                                                                        <option value="200">200
                                                                                        </option>
                                                                                        <option value="250">250
                                                                                        </option>
                                                                                    </select>
                                                                                    <span>Entries</span>
                                                                                </div> -->
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Sales Order</h4>
                                                                                        <a href="manage-sales-orders.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage SO</a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerSalesOrderTableDiV innerCustTransDiv">
                                                                                        <table id="innerSoTable"
                                                                                            class="exportTable custTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>SO Number</th>
                                                                                                    <th>Customer PO</th>
                                                                                                    <th>Delivery Date
                                                                                                    </th>
                                                                                                    <th>Total Items
                                                                                                    </th>
                                                                                                    <th>Status</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody id="custTransSo">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                    
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-journalinner" role="tabpanel"
                                                                                aria-labelledby="pills-journalinner-tab">
                                                                                <!-- <div
                                                                                    class="length-row inner-length-row">
                                                                                    <span>Show</span>
                                                                                    <select name="" id=""
                                                                                        class="custom-select-innerJournalsTable"
                                                                                        value="25">
                                                                                        <option value="10">10
                                                                                        </option>
                                                                                        <option value="25"
                                                                                            selected="selected">25
                                                                                        </option>
                                                                                        <option value="50">50
                                                                                        </option>
                                                                                        <option value="100">100
                                                                                        </option>
                                                                                        <option value="200">200
                                                                                        </option>
                                                                                        <option value="250">250
                                                                                        </option>
                                                                                    </select>
                                                                                    <span>Entries</span>
                                                                                </div> -->
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Journals</h4>
                                                                                        <a href="manage-journal.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage Journal</a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerJournalsTableDiV innerCustTransDiv">
                                                                                        <table id="innerJournalsTable"
                                                                                            class="exportTable custTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>Journal Number
                                                                                                    </th>
                                                                                                    <th>Reference Code
                                                                                                    </th>
                                                                                                    <th>Document Number
                                                                                                    </th>
                                                                                                    <th>Document Date
                                                                                                    </th>
                                                                                                    <th>Posting Date
                                                                                                    </th>
                                                                                                    <th>Narration</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody
                                                                                                id="custTransJournal">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                    
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-debitnotesinner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-debitnotesinner-tab">
                                                                                <!-- <div
                                                                                    class="length-row inner-length-row">
                                                                                    <span>Show</span>
                                                                                    <select name="" id=""
                                                                                        class="custom-select-innerDebitNote"
                                                                                        value="25">
                                                                                        <option value="10">10
                                                                                        </option>
                                                                                        <option value="25"
                                                                                            selected="selected">25
                                                                                        </option>
                                                                                        <option value="50">50
                                                                                        </option>
                                                                                        <option value="100">100
                                                                                        </option>
                                                                                        <option value="200">200
                                                                                        </option>
                                                                                        <option value="250">250
                                                                                        </option>
                                                                                    </select>
                                                                                    <span>Entries</span>
                                                                                </div> -->
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Debit Notes</h4>
                                                                                        <a href="manage-debit-notes.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage DN</a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerDebitNoteDiV innerCustTransDiv">
                                                                                        <table id="innerDebitNote"
                                                                                            class="exportTable custTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>Debit Note
                                                                                                        Number
                                                                                                    </th>
                                                                                                    <th>Party Code</th>
                                                                                                    <th>Party Name</th>
                                                                                                    <th>Invoice Number
                                                                                                    </th>
                                                                                                    <th>Total</th>
                                                                                                    <th>Posting Date
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </thead>



                                                                                            <tbody id="debitnotetable">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>

                                                                                    
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-creditnotesinner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-creditnotesinner-tab">
                                                                                <!-- <div
                                                                                    class="length-row inner-length-row">
                                                                                    <span>Show</span>
                                                                                    <select name="" id=""
                                                                                        class="custom-select-innerCreditNote"
                                                                                        value="25">
                                                                                        <option value="10">10
                                                                                        </option>
                                                                                        <option value="25"
                                                                                            selected="selected">25
                                                                                        </option>
                                                                                        <option value="50">50
                                                                                        </option>
                                                                                        <option value="100">100
                                                                                        </option>
                                                                                        <option value="200">200
                                                                                        </option>
                                                                                        <option value="250">250
                                                                                        </option>
                                                                                    </select>
                                                                                    <span>Entries</span>
                                                                                </div> -->
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Credit Notes</h4>
                                                                                        <a href="manage-credit-notes.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage CN</a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerCreditNoteDiV innerCustTransDiv">
                                                                                        <table id="innerCreditNote"
                                                                                            class="exportTable custTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>Credit Note
                                                                                                        Number
                                                                                                    </th>
                                                                                                    <th>Party Code</th>
                                                                                                    <th>Party Name</th>
                                                                                                    <th>Invoice Number
                                                                                                    </th>
                                                                                                    <th>Amount</th>
                                                                                                    <th>Posting Date
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody id="creditnotetable">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                   
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane mail-tab-pane fade" id="nav-mail"
                                                                    role="tabpanel" aria-labelledby="nav-mail-tab">
                                                                    <div class="inner-content">
                                                                        <ul class="nav nav-pills" id="pills-tab"
                                                                            role="tablist">
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link active"
                                                                                    id="pills-mailInbox-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-mailInbox"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-mailInbox"
                                                                                    aria-selected="true"><ion-icon
                                                                                        name="mail-outline"></ion-icon>Inbox</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <div class="float-reminder-btn">
                                                                                    <button class="nav-link"
                                                                                        type="button"
                                                                                        data-bs-toggle="offcanvas"
                                                                                        data-bs-target="#offcanvasRight"
                                                                                        aria-controls="offcanvasRight">
                                                                                        <ion-icon
                                                                                            name="notifications-outline"></ion-icon>
                                                                                        Reminder
                                                                                    </button>
                                                                                </div>
                                                                            </li>
                                                                        </ul>
                                                                        <div class="tab-content" id="pills-tabContent">
                                                                            <div class="tab-pane fade show active"
                                                                                id="pills-mailInbox" role="tabpanel"
                                                                                aria-labelledby="pills-mailInbox-tab">
                                                                                <div class="inbox-blocks">

                                                                                </div>
                                                                                <div class="offcanvas offcanvas-end reminder-offcanvas"
                                                                                    tabindex="-1" id="offcanvasRight"
                                                                                    aria-labelledby="offcanvasRightLabel">
                                                                                    <div class="offcanvas-header">
                                                                                        <h5 id="offcanvasRightLabel">
                                                                                            <ion-icon
                                                                                                name="notifications-outline"></ion-icon>Reminder
                                                                                        </h5>
                                                                                        <button type="button"
                                                                                            class="btn-close text-reset"
                                                                                            data-bs-dismiss="offcanvas"
                                                                                            aria-label="Close">
                                                                                            <ion-icon
                                                                                                name="close-outline"></ion-icon>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="offcanvas-body">
                                                                                        <div class="row">
                                                                                            <div
                                                                                                class="col-12 col-md-3">
                                                                                                <div class="form-input">
                                                                                                    <label
                                                                                                        for="">Days</label>
                                                                                                    <input type="text"
                                                                                                        class="form-control"
                                                                                                        id="maildays"
                                                                                                        placeholder="Enter days  name="
                                                                                                        reminerDays">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div
                                                                                                class="col-12 col-md-7">
                                                                                                <div class="form-input">
                                                                                                    <label
                                                                                                        for="">Operator</label>
                                                                                                    <select name=""
                                                                                                        id="mailoperator"
                                                                                                        class="form-control">
                                                                                                        <option
                                                                                                            value="post_of_invoice_date">
                                                                                                            Post of
                                                                                                            Invoice Date
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="Post of Due Date">
                                                                                                            Post of Due
                                                                                                            Date
                                                                                                        </option>
                                                                                                        <option
                                                                                                            value="Early of Invoice Date">
                                                                                                            Early of
                                                                                                            Invoice Date
                                                                                                        </option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div
                                                                                                class="col-12 col-md-2">
                                                                                                <button
                                                                                                    class="btn btn-primary add-mail-reminder"
                                                                                                    onclick="addMultiOperator()">
                                                                                                    <ion-icon
                                                                                                        name="add-outline"></ion-icon>
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="offcanvas-footer">
                                                                                        <button
                                                                                            class="btn btn-primary MailSend">
                                                                                            <ion-icon
                                                                                                name="send-outline"></ion-icon>
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
                                                                <div class="tab-pane statement-tab-pane fade"
                                                                    id="nav-statement" role="tabpanel"
                                                                    aria-labelledby="nav-statement-tab">
                                                                    <div class="inner-content">
                                                                        <div class="row select-date">
                                                                            <div class="col-12 col-md-3">
                                                                                <div class="form-input">
                                                                                    <select name="" id=""
                                                                                        class="form-control dateDrop">
                                                                                        <option value="">Select</option>
                                                                                        <option value="">This Month
                                                                                        </option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12 col-md-9">
                                                                                <div class="date-fields">
                                                                                    <div class="form-inline">
                                                                                        <label for="">From</label>
                                                                                        <input type="date"
                                                                                            class="form-control"
                                                                                            id="fromDate">
                                                                                    </div>
                                                                                    <div class="form-inline">
                                                                                        <label for="">To</label>
                                                                                        <input type="date"
                                                                                            class="form-control"
                                                                                            id="toDate">
                                                                                    </div>
                                                                                    <button
                                                                                        class="btn btn-primary date_apply">
                                                                                        <ion-icon
                                                                                            name="arrow-forward-outline"></ion-icon>
                                                                                        Apply
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row statement-details">

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane compliance-tab-pane fade"
                                                                    id="nav-compliance" role="tabpanel"
                                                                    aria-labelledby="nav-compliance-tab">

                                                                    <div class="inner-content">
                                                                        <div class="list-block">
                                                                            <div class="gst-list gst-one-tab">
                                                                                <div class="head">
                                                                                    <h4><ion-icon
                                                                                            name="document-text-outline"></ion-icon>GST
                                                                                        Filed Status For GSTR1</h4>
                                                                                </div>

                                                                                <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data"
                                                                                    id="gstinReturnsDatacomp_Div">
                                                                                </div>
                                                                            </div>

                                                                            <div class="gst-list gst-one-tab">
                                                                                <div class="head">
                                                                                    <h4>
                                                                                        <ion-icon
                                                                                            name="document-text-outline"></ion-icon>&nbsp;
                                                                                        GST Filed Status For GSTR3B
                                                                                    </h4>
                                                                                </div>
                                                                                <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data"
                                                                                    id="gstinReturnsDatacomp3b_Div">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane recon-tab-pane fade"
                                                                    id="nav-reconciliation" role="tabpanel"
                                                                    aria-labelledby="nav-reconciliation-tab">
                                                                    <div class="inner-content">
                                                                        <div class="date-fields">
                                                                            <div class="form-inline">
                                                                                <label for="">From</label>
                                                                                <input type="date" class="form-control"
                                                                                    id="fromDate_rec">
                                                                            </div>
                                                                            <div class="form-inline">
                                                                                <label for="">To</label>
                                                                                <input type="date" class="form-control"
                                                                                    id="toDate_rec">>
                                                                            </div>
                                                                            <button
                                                                                class="btn btn-primary waves-effect waves-light date_apply_recon">
                                                                                <ion-icon name="arrow-forward-outline"
                                                                                    role="img" class="md hydrated"
                                                                                    aria-label="arrow forward outline"></ion-icon>
                                                                                Apply
                                                                            </button>
                                                                        </div>

                                                                        <div id="recon_preview" class="recon_preview">
                                                                        </div>
                                                                        <p class="recon-note">All values are in
                                                                            <b>INR</b>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="nav-trail"
                                                                    role="tabpanel" aria-labelledby="nav-trail-tab">
                                                                    <div class="inner-content">
                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                            <!-- <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> Sonie Kushwaha <span class="font-bold text-normal"> on </span> 26-02-2024 16:35:10</p>
                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> Sonie Kushwaha <span class="font-bold text-normal"> on </span> 26-02-2024 16:35:10</p> -->
                                                                        </div>
                                                                        <hr>
                                                                        <div
                                                                            class="audit-body-section mt-2 mb-3 auditTrailBodyContentCustomer">


                                                                        </div>
                                                                        <!--  -->
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
<script src="../../public/assets/core.js"></script>
<script src="../../public/assets/charts.js"></script>
<script src="../../public/assets/animated.js"></script>

<script>
    $(document).ready(function () {
        $("button.page-list").click(function () {
            var buttonId = $(this).attr("id");
            $("#modal-container").removeAttr("class").addClass(buttonId);
            $(".mobile-transform-card").addClass("modal-active");
        });

        $(".btn-close-modal").click(function () {
            $("#modal-container").toggleClass("out");
            $(".mobile-transform-card").removeClass("modal-active");
        });
    })
</script>


<!-- modal view responsive more tabs -->

<script>
    $(document).ready(function () {
        // Adjust tabs based on window size
        adjustTabs();

        // Listen for window resize event
        $(window).resize(function () {
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
    let csvContent;
    let csvContentBypagination;

    $(document).ready(function () {
        var indexValues = [];
        var dataTable;
        var columnMapping = <?php echo json_encode($columnMapping); ?>;


        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function (settings, json) {
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
            var checkboxSettings = Cookies.get('cookieManageCustomerList');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-customer-m.php",
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
                beforeSend: function () {
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
                success: function (response) {
                    // console.log(response);
                    csvContent = response.csvContent;
                    csvContentBypagination = response.csvContentBypagination;

                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);


                        $.each(responseObj, function (index, value) {
                            let status = ``;
                            if (value.customerStatus == "active") {
                                status = `<p class='status-bg status-approved'>Active</p>`;
                            } else if (value.customerStatus == "inactive") {
                                status = `<p class='status-bg status-closed'>Inactive</p>`;
                            } else if (value.customerStatus == "draft") {
                                status = `<p class='status-bg status-pending'>Draft</p>`;
                            }
                            // console.log(value);
                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal" data-id="${value.customerId}" data-code="${value.customer_code}" data-gstin="${value.customer_gstin}">${value.customer_code}</a>`,
                                // value.cusIcon,
                                value.cusName,
                                value.constitution_of_business,
                                value.customer_gstin,
                                value.customer_email,
                                value.customer_phone,
                                // value.orderVolume,
                                // value.receipt_amt,
                                status,
                                `<div class="dropout">
                                   <button class="more">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                   </button>
                                   <ul>
                                       <li>
                                           <button class="soModal" data-id="${value.customerId}" data-code="${value.customer_code}" data-gstin="${value.customer_gstin}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                       </li> 
                                       <li>
                                           <button class="editCustomer" data-id="${value.customerId}" data-code="${value.customer_code}"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                       </li>
                                       <li>
                                           <button data-toggle="modal" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                       </li>                                   
                                   </ul>                              
                               </div>`
                            ]).draw(false);
                        });
                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (checkboxSettings) {
                            var checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function (index) {
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
                                notVisibleColArr.forEach(function (index) {
                                    dataTable.column(index).visible(false);
                                });
                            }

                            // console.log('Cookie value:', checkboxSettings);

                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function (index) {
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
                complete: function () {
                    $("#globalModalLoader").remove();

                },
                error: function (error) {
                    console.log(error);
                }
            });
        }

        fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping);

        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function (e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function (e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#itemsPerPage").val();
            //    console.log(limitDisplay);
            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay);

        });

        //<--------------advance search------------------------------->
        $(document).ready(function () {
            $(document).on("click", "#serach_submit", function (event) {
                event.preventDefault();
                let values;
                $(".selectOperator").each(function () {
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
                    } else if (columnSlag === 'created_at') {
                        values = value3;
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
            });
        });

        // -------------checkbox----------------------

        $(document).ready(function () {
            var columnMapping = <?php echo json_encode($columnMapping); ?>;

            var indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                var column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function () {
                var columnVal = $(this).val();
                // console.log(columnVal);

                var index = columnMapping.findIndex(function (column) {
                    return column.slag === columnVal;
                });
                // console.log(index);
                toggleColumnVisibility(index, this);
            });

            $(".grand-checkbox").on("click", function () {
                $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);
                $("input[name='settingsCheckbox[]']").each(function () {
                    var columnVal = $(this).val();
                    // console.log(columnVal);
                    var index = columnMapping.findIndex(function (column) {
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

    $(document).ready(function () {
        $(document).on("click", "#check-box-submt", function (event) {
            // console.log("Hiiiii");
            event.preventDefault();
            // $("#myModal1").modal().hide();
            $('#btnSearchCollpase_modal').modal('hide');
            var tablename = $("#tablename").val();
            var pageTableName = $("#pageTableName").val();
            var settingsCheckbox = [];
            var fromData = {};
            $(".settingsCheckbox_detailed").each(function () {
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
                        act: 'manageCustomerList',
                        formData: fromData
                    },
                    success: function (response) {
                        // console.log(response);
                        Swal.fire({
                            icon: response.status,
                            title: response.message,
                            timer: 1000,
                            showConfirmButton: false,
                        })
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
        });
    });
</script>

<!-- -----fromDate todate input add--- -->
<script>
    $(document).ready(function () {
        $(document).on("change", ".selectOperator", function () {
            let columnIndex = parseInt(($(this).attr("id")).split("_")[1]);
            let operatorName = $(this).val();
            let columnName = $(`#columnName_${columnIndex}`).html();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Delivery Date') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'SO Date') {
                inputId = "value2_" + columnIndex;
            } else if (columnName === 'Created Date') {
                inputId = "value3_" + columnIndex;
            }

            if ((columnName === 'Delivery Date' || columnName === 'SO Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
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
<script>
    function addMultiOperator() {
        $(`.offcanvas-body`).append(`
                                    <div class="row">
                                            <div class="col-12 col-md-3">
                                                <div class="form-input">
                                                    <label for="">Days</label>
                                                    <input type="text" class="form-control" placeholder="Enter days  name=" reminerDays">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-7">
                                                <div class="form-input">
                                                    <label for="">Operator</label>
                                                    <select name="" id="" class="form-control">
                                                        <option value="post_of_invoice_date">Post of Invoice Date</option>
                                                        <option value="Post of Due Date">Post of Due Date</option>
                                                        <option value="Early of Invoice Date">Early of Invoice Date</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-2">
                                                <button class="btn btn-primary dlt-btn ">
                                                    <ion-icon name="remove-outline"></ion-icon>
                                                </button>
                                            </div>
                                        </div>
    `);

        $(document).on("click", ".dlt-btn", function () {
            $(this).closest(".row").remove();
        });
    }
</script>

<!------------ modal ajax--------- -->
<script>
    $(document).ready(function () {



        let ajaxUrl;
        let custId;
        let code;

        let tableInnerInvoices;

        tableInnerInvoices = $('#innerInvoices').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerInvoices_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerInvoices_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,
        });


        let tableInnerCollections;

        tableInnerCollections = $('#innerCollections').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerCollections_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerCollections_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,

        });

        // tableInnerCollections = $('#innerCollections').DataTable({
        //     dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
        //     "lengthMenu": [10, 25, 50, 100, 200],
        //     "ordering": false,
        //     info: false,
        //     "pageLength": true,
        //     "initComplete": function (settings, json) {
        //         $('#innerCollections_filter input[type="search"]').attr('placeholder', 'Search....');
        //     },
        //     buttons: [],
        //     "bPaginate": false,
        //     "scrollY": '400px', // A smaller height for the DataTable
        //     "scrollCollapse": true,
        //     "autoWidth": false
        // });


        let tableInnerEstimates;

        tableInnerEstimates = $('#innerEstimates').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerEstimates_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerEstimates_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,
        });


        let tableInnerSoTable;

        tableInnerSoTable = $('#innerSoTable').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerSalesOrder_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerSoTable_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,
        });


        let tableInnerJournalsTable;

        tableInnerJournalsTable = $('#innerJournalsTable').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerJournals_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerJournalsTable_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,
        });



        let tableDebitNote;

        tableDebitNote = $('#innerDebitNote').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerDebitNote_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerDebitNote_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,
        });

        let tableCreditNote;

        tableCreditNote = $('#innerCreditNote').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerCreditNote_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerCreditNote_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,
        });




        $(document).on("click", ".soModal", function () {
            $(".recon_preview").empty();
            $('.statement-details').empty();
            $("#gstinReturnsDatacomp_Div").empty();
            $("#gstinReturnsDatacomp3b_Div").empty();
            $(".auditTrailBodyContentCustomer").empty();
            $('#viewGlobalModal').modal('show');;
            $('.ViewfirstTab').tab('show');
            $(".classic-view").html('');



            // $("#custTransInv").html('');
            // $("#custTransCollection").html('');
            // $("#custTransEstimate").html('');
            // $('#custTransSo').html('');
            // $("#custTransJournal").html('');
            // $("#debitnotetable").html('');
            // $("#creditnotetable").html('');





            tableInnerInvoices.clear().draw();
            tableInnerCollections.clear().draw();
            tableInnerEstimates.clear().draw();
            tableInnerSoTable.clear().draw();
            tableInnerJournalsTable.clear().draw();
            tableDebitNote.clear().draw();
            tableCreditNote.clear().draw();


            // $(".innerInvoicesTableDiV").scrollTop(0);
            $(".innerInvoices_wrapper").scrollTop(0);
            $(".innerCollections_wrapper").scrollTop(0);
            $(".innerSalesOrderTableDiV").scrollTop(0);
            $(".innerJournalsTableDiV").scrollTop(0);
            $(".innerDebitNoteDiV").scrollTop(0);
            $(".innerCreditNoteDiV").scrollTop(0);

            // $('.innerCollectionsTableDiV')[0].scrollTop = 0; // Reset scroll
            // console.log('Scroll Top Value after reset:', $('.innerCollectionsTableDiV')[0].scrollTop);

            // console.log($('.innerCollectionsTableDiV')[0])





            custId = $(this).data('id');
            code = $(this).data('code');
            let gstin = $(this).data('gstin');
            let created_by, created_at, updated_by, updated_at;
            ajaxUrl = "ajaxs/modals/customer/ajax-manage-customer-modal-m.php";

            // console.log(gstin);
            // modal header        
            $.ajax({
                type: "GET",
                url: ajaxUrl,
                dataType: 'json',
                data: {
                    act: "modalData",
                    custId
                },
                beforeSend: function () {
                    $('#itemTableBody').html('');
                    let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;
                    // Append the new HTML to the modal-body element
                    // $('#viewGlobalModal .modal-body').append(loader);
                },
                success: function (value) {
                    // console.log(value);
                    if (value.status) {
                        let responseObj = value.data;
                        let dataObj = value.data.dataObj;
                        // nav head
                        $("#custName").html(dataObj.trade_name);
                        $("#custCode").html(dataObj.customer_code);
                        $("#custCob").html(dataObj.constitution_of_business);
                        $("#custGst").html(dataObj.customer_gstin);
                        $("#custPerson").html(dataObj.customer_authorised_person_name);
                        $("#custPersonDesg").html(dataObj.customer_authorised_person_designation);
                        $("#custPersonPhone").html(dataObj.customer_authorised_person_phone);
                        $("#custPersonMail").html(dataObj.customer_authorised_person_email);
                        created_by = responseObj.created_by;
                        created_at = responseObj.created_at;
                        updated_by = responseObj.updated_by;
                        updated_at = responseObj.updated_at;
                        $('.audit-head-section').attr('id', 'audit-head-section_' + custId);

                    }
                    // $("#globalModalLoader").remove();
                },
                complete: function () {
                    // $("#globalModalLoader").remove();
                },
                error: function (error) {
                    console.log(error);
                }
            });
            //chart overview
            $.ajax({
                type: "GET",
                url: ajaxUrl,
                data: {
                    act: "chatheader",

                },
                beforeSend: function () {
                    $(".chat-head").empty();
                },
                success: function (value) {
                    let jsonResponse = JSON.parse(value);
                    // console.log(jsonResponse.data);
                    let options = '';
                    jsonResponse.data.forEach(function (data) {
                        options += `<option value="${data.year_variant_id}">${data.year_variant_name}</option>`;
                    });
                    $(".chat-head").html(`
                                        <h5 class="card-title text-nowrap pl-3">Chart View</h5>
                                            <div id="containerThreeDot">
                                                <div id="menu-wrap">
                                                    <input type="checkbox" class="toggler bg-transparent" />
                                                    <div class="dots">
                                                        <div></div>
                                                    </div>
                                                    <div class="menu">
                                                        <div>
                                                            <ul>
                                                            <li>
                                                                <select name="fYDropdown" id="fYDropdown_${custId}" data-attr="${custId}" class="form-control fYDropdown">
                                                                ${options}
                                                                </select>
                                                            </li>
                                                            <li><label class="mb-0" for="">OR</label></li>
                                                            <li>
                                                                <input type="month" name="monthRange" id="monthRange_${custId}" data-attr="${custId}" class="form-control monthRange" style="max-width: 100%;" />
                                                            </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                        `);



                    for (elem of $(".chartContainer")) {
                        let dataAttrValue = custId;
                        let id = $(`#fYDropdown_${custId} option:first`).val();
                        // console.log(id);
                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&cust_id=${dataAttrValue}`,
                            beforeSend: function () {
                                $(".load-wrapp").show();
                                $(".load-wrapp").css('opacity', 1);
                            },
                            success: function (result) {
                                $(".load-wrapp").hide();
                                $(".load-wrapp").css('opacity', 0);

                                let res = jQuery.parseJSON(result);
                                // console.log(res);
                                salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
                            }
                        });
                    };
                    $(document).on("change", '.fYDropdown', function () {
                        var dataAttrValue = $(this).data('attr');
                        var id = $(`#fYDropdown_${dataAttrValue}`).val();
                        // console.log(dataAttrValue+','+id);
                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&cust_id=${dataAttrValue}`,
                            beforeSend: function () {
                                $(".load-wrapp").show();
                                $(".load-wrapp").css('opacity', 1);
                            },
                            success: function (result) {
                                $(".load-wrapp").hide();
                                $(".load-wrapp").css('opacity', 0);
                                let res = jQuery.parseJSON(result);
                                salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
                            }
                        });
                    });

                    $(document).on("change", '.monthRange', function () {
                        var dataAttrValue = $(this).data('attr');
                        var month = $(`#monthRange_${dataAttrValue}`).val();
                        // console.log(dataAttrValue+','+month);
                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?month=${month}&cust_id=${dataAttrValue}`,
                            beforeSend: function () {
                                $(".load-wrapp").show();
                                $(".load-wrapp").css('opacity', 1);
                            },
                            success: function (result) {
                                $(".load-wrapp").hide();
                                $(".load-wrapp").css('opacity', 0);

                                let res = jQuery.parseJSON(result);

                                salesVsCollection(res, "chartDivSalesVsCollection", dataAttrValue);
                            }
                        });
                    });
                },
            });



            function salesVsCollection(chartData, chartTitle, custId) {


                $(`#${chartTitle}`).text(`Recievable Vs Recieved`);

                if (chartData.sql_list_all_cust.length == 0 && chartData.sql_list_specific_cust.length == 0) {
                    const currentDate = new Date();
                    const year = currentDate.getFullYear();
                    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                    const day = String(currentDate.getDate()).padStart(2, '0');

                    const formattedDate = `${year}-${month}-${day}`;

                    chartData = {
                        "sql_list_all_cust": [{
                            date_: formattedDate,
                            total_receivable_all: 0,
                            total_received_all: 0
                        }],
                        "sql_list_specific_cust": [{
                            date_: formattedDate,
                            total_receivable: 0,
                            total_received: 0
                        }]
                    };
                };

                am4core.ready(function () {

                    // Themes begin
                    am4core.useTheme(am4themes_animated);
                    // Themes end

                    // Create chart instance
                    let cleanedCustId = String(custId).trim();
                    var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
                    // console.log(chart);
                    chart.logo.disabled = true;

                    let finalData = [];
                    let outerIndex = 0;

                    for (obj of chartData.sql_list_all_cust) {
                        obj.total_receivable_all = Number(obj.total_receivable);
                        obj.total_received_all = Number(obj.total_received);
                        obj.total_receivable = 0;
                        obj.total_received = 0;
                        finalData.push(obj);
                    };

                    for (obj of chartData.sql_list_specific_cust) {

                        const outerObj = finalData.map(obj => {
                            return obj.date_
                        })
                        outerIndex = outerObj.indexOf(obj.date_)

                        if (outerIndex !== -1) {
                            finalData[outerIndex].total_receivable = Number(obj.total_receivable);
                            finalData[outerIndex].total_received = Number(obj.total_received);
                        } else {
                            obj.total_receivable = Number(obj.total_receivable);
                            obj.total_received = Number(obj.total_received);
                            obj.total_receivable_all = 0;
                            obj.total_received_all = 0;
                            finalData.push(obj);
                        }
                    }

                    finalData.sort((a, b) => (a.date_ > b.date_) ? 1 : ((b.date_ > a.date_) ? -1 : 0))

                    // Add data
                    chart.data = finalData;

                    // Create axes
                    var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
                    //dateAxis.renderer.grid.template.location = 0;
                    //dateAxis.renderer.minGridDistance = 30;

                    var valueAxis1 = chart.yAxes.push(new am4charts.ValueAxis());
                    valueAxis1.title.text = "This Customer";

                    var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
                    valueAxis2.title.text = "All Customers";
                    valueAxis2.renderer.opposite = true;
                    valueAxis2.renderer.grid.template.disabled = true;

                    // Create series
                    var series1 = chart.series.push(new am4charts.ColumnSeries());
                    series1.dataFields.valueY = "total_receivable";
                    series1.dataFields.dateX = "date_";
                    series1.yAxis = valueAxis1;
                    series1.name = "Receivable";
                    series1.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
                    series1.fill = chart.colors.getIndex(0);
                    series1.strokeWidth = 0;
                    series1.clustered = false;
                    series1.columns.template.width = am4core.percent(40);

                    var series2 = chart.series.push(new am4charts.ColumnSeries());
                    series2.dataFields.valueY = "total_received";
                    series2.dataFields.dateX = "date_";
                    series2.yAxis = valueAxis1;
                    series2.name = "Recieved";
                    series2.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
                    series2.fill = chart.colors.getIndex(0).lighten(0.5);
                    series2.strokeWidth = 0;
                    series2.clustered = false;
                    series2.toBack();

                    var series3 = chart.series.push(new am4charts.LineSeries());
                    series3.dataFields.valueY = "total_received_all";
                    series3.dataFields.dateX = "date_";
                    series3.name = "Recieved (all customers)";
                    series3.strokeWidth = 2;
                    series3.tensionX = 0.7;
                    series3.yAxis = valueAxis2;
                    series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

                    var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
                    bullet3.circle.radius = 3;
                    bullet3.circle.strokeWidth = 2;
                    bullet3.circle.fill = am4core.color("#fff");

                    var series4 = chart.series.push(new am4charts.LineSeries());
                    series4.dataFields.valueY = "total_receivable_all";
                    series4.dataFields.dateX = "date_";
                    series4.name = "Recievable (all customers)";
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
            };



            $.ajax({
                type: "GET",
                url: ajaxUrl,
                data: {
                    act: "chatdetails",
                    custId
                },
                beforeSend: function () {
                    $("#DetailsView").empty();
                },
                success: function (value) {
                    let jsonResponse = JSON.parse(value);
                    // console.log(jsonResponse);
                    jsonResponse.data.forEach(function (data) {
                        $('#DetailsView').html(`
                                <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="accordion view-modal-accordion" id="accordionExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingOne">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic" aria-expanded="true" aria-controls="collapseOne">
                                                <ion-icon name="information-outline"></ion-icon>
                                                Basic Details
                                                </button>
                                            </h2>
                                            <div id="collapseBasic" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                <div class="p-0">
                                                <h1></h1>
                                                <div class="accordion-body">
                                                    <div class="details">
                                                        <label for="">
                                                            <ion-icon name="arrow-forward-outline"></ion-icon>
                                                            State
                                                        </label>
                                                        <p class="font-bold text-xs">${data.customer_address_state ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for="">
                                                            <ion-icon name="arrow-forward-outline"></ion-icon>
                                                            City
                                                        </label>
                                                        <p class="font-bold text-xs">${data.customer_address_city ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for="">
                                                            <ion-icon name="arrow-forward-outline"></ion-icon>
                                                            District
                                                        </label>
                                                        <p class="font-bold text-xs">${data.customer_address_district ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for="">
                                                            <ion-icon name="arrow-forward-outline"></ion-icon>
                                                            Location 
                                                        </label>
                                                        <p class="font-bold text-xs">${data.customer_address_location ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for="">
                                                            <ion-icon name="arrow-forward-outline"></ion-icon>
                                                            Building Number
                                                        </label>
                                                        <p class="font-bold text-xs w-75">${data.customer_address_building_no ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for="">
                                                            <ion-icon name="arrow-forward-outline"></ion-icon>
                                                            Flat Number 
                                                        </label>
                                                        <p class="font-bold text-xs w-75">${data.customer_address_flat_no ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for="">
                                                            <ion-icon name="arrow-forward-outline"></ion-icon>
                                                            Street Name 
                                                        </label>
                                                        <p class="font-bold text-xs w-75">${data.customer_address_street_name ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for="">
                                                            <ion-icon name="arrow-forward-outline"></ion-icon>
                                                            PIN Code  
                                                        </label>
                                                        <p class="font-bold text-xs w-75">${data.customer_address_pin_code ?? "-"}</p>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="accordion view-modal-accordion" id="RecievablesAgeing">
                                    </div>
                                    <div id="chartDivReceivableAgeing_${data.customer_id}" class="pieChartContainer" style="display: none">
                                    </div>
                                    <div id="noTransactionFound_${data.customer_id}" class="text-center py-2" style="display: none;">
                                        <img src="../../public/assets/gif/no-transaction.gif" width="150" alt="">
                                        <p>No Transactoin Found</p>
                                    </div>
                                    </div>
                        `);
                    });
                }
            });
            $.ajax({
                type: "GET",
                url: ajaxUrl,
                data: {
                    act: "chatheader",

                },
                beforeSend: function () {
                    $("#RecievablesAgeing").empty();
                },
                success: function (value) {
                    // console.log(value);
                    let jsonResponse = JSON.parse(value);
                    // console.log(jsonResponse.data);
                    let options = '';
                    jsonResponse.data.forEach(function (data) {
                        options += `<option value="${data.year_variant_id}">${data.year_variant_name}</option>`;
                    });
                    $("#RecievablesAgeing").html(`  
                                            <div id="containerThreeDot" style="justify-content: space-between;">
                                            <h5 class="accordion-header" id="headingOne">
                                                    Recievables Ageing      
                                            </h5>
                                            <div id="menu-wrap">
                                            <input type="checkbox" class="toggler bg-transparent" />
                                            <div class="dots">
                                            <div></div>
                                            </div>
                                            <div class="menu">
                                            <div>
                                            <ul>
                                            <li>
                                            <select name="piefYDropdown" id="piefYDropdown_${custId}" data-attr="${custId}" class="form-control piefYDropdown">
                                            ${options}
                                            </select>
                                            </li>
                                            <li><label class="mb-0" for="">OR</label></li>
                                            <li>
                                            <input type="month" name="monthRange" id="monthRange_${custId}" data-attr="${custId}" class="form-control monthRange" style="max-width: 100%;" />
                                        </li>
                                    </ul>
                                    </div>
                                </div>
                                </div>
                            </div>`);
                    for (elem of $(".pieChartContainer")) {
                        let dataAttrValue = elem.getAttribute("id").split("_")[1];
                        let id = $(`#piefYDropdown_${dataAttrValue}`).val();
                        //    console.log(id);
                        //    console.log(dataAttrValue);

                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&customer_id=${dataAttrValue}`,
                            beforeSend: function () {
                                $(".load-wrapp").show();
                                $(".load-wrapp").css('opacity', 1);
                            },
                            success: function (result) {
                                $(".load-wrapp").hide();
                                $(".load-wrapp").css('opacity', 0);

                                let res = jQuery.parseJSON(result);
                                // console.log(res);
                                let status = res['status'];
                                if (status == 'success') {
                                    $(`#chartDivReceivableAgeing_${dataAttrValue}`).show();
                                    pieChart(res, "chartDivReceivableAgeing", dataAttrValue);
                                } else {
                                    $(`#noTransactionFound_${dataAttrValue}`).show();
                                }
                            }
                        });
                    };

                    $(document).on("change", '.piefYDropdown', function () {

                        var dataAttrValue = $(this).data('attr');
                        var id = $(`#piefYDropdown_${dataAttrValue}`).val();
                        // console.log(dataAttrValue + ',' + id);
                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?id=${id}&customer_id=${dataAttrValue}`,
                            beforeSend: function () {
                                $(".load-wrapp").show();
                                $(".load-wrapp").css('opacity', 1);
                            },
                            success: function (result) {
                                $(".load-wrapp").hide();
                                $(".load-wrapp").css('opacity', 0);

                                let res = jQuery.parseJSON(result);
                                // console.log(res);
                                pieChart(res, "chartDivReceivableAgeing", dataAttrValue);
                            }
                        });
                    });

                    $(document).on("change", '.monthRange', function () {
                        var dataAttrValue = $(this).data('attr');
                        var month = $(this).val();
                        // console.log(month);
                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-customer-chart.php?month=${month}&cust_id=${dataAttrValue}`,
                            beforeSend: function () {
                                $(".load-wrapp").show();
                                $(".load-wrapp").css('opacity', 1);
                            },
                            success: function (result) {
                                $(".load-wrapp").hide();
                                $(".load-wrapp").css('opacity', 0);

                                let res = jQuery.parseJSON(result);
                                // console.log(res);
                                pieChart(res, "chartDivReceivableAgeing", dataAttrValue);
                            }
                        });
                    });

                },
            });


            function pieChart(chartData, chartTitle, custId) {

                am4core.ready(function () {
                    am4core.useTheme(am4themes_animated);
                    var chart = am4core.create(`${chartTitle}_${custId.trim()}`, am4charts.PieChart3D);
                    chart.responsive.enabled = true;
                    chart.logo.disabled = true;
                    chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

                    chart.legend = new am4charts.Legend();

                    let finalData = [{
                        "category": "0-30 days",
                        "value": 0
                    },
                    {
                        "category": "31-60 days",
                        "value": 0
                    },
                    {
                        "category": "61-90 days",
                        "value": 0
                    },
                    {
                        "category": "91-180 days",
                        "value": 0
                    },
                    {
                        "category": "181-365 days",
                        "value": 0
                    },
                    {
                        "category": "More than 365 days",
                        "value": 0
                    },
                    ];

                    for (elem of chartData.data) {

                        let due_days = parseInt(elem.due_days);

                        if (due_days >= 0 && due_days <= 30) {
                            finalData[0].value += Number(elem.total_due_amount);
                        } else if (due_days >= 31 && due_days <= 60) {
                            finalData[1].value += Number(elem.total_due_amount);
                        } else if (due_days >= 61 && due_days <= 90) {
                            finalData[2].value += Number(elem.total_due_amount);
                        } else if (due_days >= 91 && due_days <= 180) {
                            finalData[3].value += Number(elem.total_due_amount);
                        } else if (due_days >= 181 && due_days <= 365) {
                            finalData[4].value += Number(elem.total_due_amount);
                        } else {
                            finalData[5].value += Number(elem.total_due_amount);
                        };
                    };

                    // chart.paddingLeft = 50;
                    // chart.paddingRight = 40;
                    // chart.paddingTop = 20;
                    // chart.paddingBottom = 20;

                    // Data 
                    chart.data = finalData;

                    chart.innerRadius = 50;

                    var series = chart.series.push(new am4charts.PieSeries3D());
                    series.dataFields.value = "value";
                    series.dataFields.category = "category";

                    series.ticks.template.disabled = true;
                    series.labels.template.disabled = true;

                    chart.legend.position = "right";
                    chart.legend.valign = "middle";

                });
            }
            //overview end
            // Transactional start 
            $(document).on("click", "#nav-transaction-tab", function () {
                $('#pills-invoicesinner-tab').tab('show');
            });

            // inner invoices function called here
            let pageInnerInvoices = 1;
            let debouceFlagInnerInvoices = true;

            innerinvoicesTable();

            $(".innerInvoices_wrapper").on('scroll', function () {
                // console.log("called scroll")
                const element = $(".innerInvoices_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debouceFlagInnerInvoices) {
                    // console.log("before function scroll")
                    innerinvoicesTable();
                    // console.log("after function scroll")

                }
            });


            // function load the data from server CUSTOMER NON ACC
            function innerinvoicesTable() {
                // set limit scroll load
                // alert('called')
                let loadLimit = 10;
                if (debouceFlagInnerInvoices) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: "json",
                        data: {
                            act: "custTransInv",
                            limit: loadLimit,
                            page: pageInnerInvoices,
                            custId
                        },
                        beforeSend: function () {
                            debouceFlagInnerInvoices = false;
                            // console.log("api called");
                        },
                        success: function (value) {
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, invoice) {
                                        tableInnerInvoices.row.add([
                                            `<p class="company-name mt-1">${invoice.customerPicture}</p>`,
                                            `${invoice.invoiceNo ?? "-"}`,
                                            `${decimalAmount(invoice.totalAmount) ?? "-"}`,
                                            `${(invoice.invoiceDate) ?? "-"}`,
                                            `${invoice.dueDays ?? "-"}`,
                                            `<div class="status-custom w-75 text-secondary">
                                            ${invoice.status ?? "-"}
                                                </div>
                                            <p class="status-date">${(invoice.statusDate) ?? "-"}</p>`
                                        ]).draw(false);
                                    });
                                    // alert("data has been added");

                                    pageInnerInvoices++;
                                    if (value.numRows == loadLimit) {
                                        debouceFlagInnerInvoices = true;
                                        // alert("more data to load");
                                    }

                                } else if (value.status == "error") {
                                    // alert('caught here')
                                    $('#custTransInv').empty();
                                    let obj = `<tr><td colspan="6"><p class="text-center">No Invoices Found</p> </td></tr>  `;
                                    debouceFlagInnerInvoices = false;
                                    $('#custTransInv').append(obj);
                                }
                            }
                            catch (e) {

                            }
                        },
                        error: function (error) {
                            console.error("Error fetching data" + error);
                        }
                    });
                }
            }











            // inner collectins function called here
            let pageInnerCollections = 1;
            let debounceFlagInnerCollection = true;
            let api = 0;


            innerCollectionsTable()

            $(".innerCollections_wrapper").on('scroll', function () {
                const element = $(".innerCollections_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerCollection) {
                    innerCollectionsTable();
                }
            });


            function innerCollectionsTable() {
                let loadLimit = 10;
                if (debounceFlagInnerCollection) {
                    // alert("called")


                    $.ajax({
                        type: "GET",
                        url: ajaxUrl,
                        dataType: "json",
                        data: {
                            act: "custTransCollection",
                            custId,
                            limit: loadLimit,
                            page: pageInnerCollections
                        },
                        beforeSend: function () {
                            debounceFlagInnerCollection = false;
                            api = api + 1;;
                        },
                        success: function (value) {
                            // console.log(value);
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        let paymentType = (val.payment_type === 'pay') ? 'against invoice' : val.payment_type;
                                        tableInnerCollections.row.add([
                                            `${val.payment_advice ?? "-"}`,
                                            `${val.transactionId ?? "-"}`,
                                            `${decimalAmount(val.payment_amt) ?? "-"}`,
                                            `${paymentType ?? "-"}`,
                                            `${(val.created_at && !isNaN(new Date(val.created_at).getTime())) ? formatDate(val.created_at) : "-"}`
                                        ]).draw(false);
                                    });

                                    pageInnerCollections++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerCollection = true;
                                    }

                                } else if (value.status == "error") {
                                    $('#custTransCollection').empty();
                                    let obj = `<tr><td colspan="5"><p class="text-center">No Collection Found</p> </td></tr>  `;
                                    debounceFlagInnerCollection = false;
                                    $('#custTransCollection').append(obj);
                                }
                            }
                            catch (e) {

                            }
                        },
                        complete: function () {
                        }
                    });
                }
            }





            // inner estimates function called here




            let pageInnerEstimates = 1;
            let debounceFlagInnerEstimates = true;


            innerEstimatesTable()

            $(".innerEstimates_wrapper").on('scroll', function () {
                const element = $(".innerEstimates_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerEstimates) {
                    innerEstimatesTable();
                }
            });


            function innerEstimatesTable() {
                let loadLimit = 10;
                if (debounceFlagInnerEstimates) {
                    // alert("called")


                    $.ajax({
                        type: "GET",
                        url: ajaxUrl,
                        dataType: "json",
                        data: {
                            act: "custTransEstimate",
                            custId,
                            limit: loadLimit,
                            page: pageInnerEstimates
                        },
                        beforeSend: function () {
                            debounceFlagInnerEstimates = false;
                        },
                        success: function (value) {
                            // console.log(value);
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;


                                    $.each(responseObj, function (index, val) {
                                        tableInnerEstimates.row.add([

                                            `${val.quotation_no ?? "-"}`,
                                            `${val.totalItems ?? "-"}`,
                                            `${decimalAmount(val.totalAmount) ?? "-"}`,
                                            `${val.goodsType ?? "-"}`,
                                            `${(val.posting_date && !isNaN(new Date(val.posting_date).getTime())) ? formatDate(val.posting_date) : "-"}`
                                        ]).draw(false);
                                    });


                                    pageInnerEstimates++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerEstimates = true;
                                    }
                                    //

                                } else if (value.status == "error") {
                                    $('#custTransEstimate').empty();
                                    let obj = `<tr><td colspan="5"><p class="text-center">No Estimates Found</p> </td></tr>  `;
                                    debounceFlagInnerEstimates = false;
                                    $('#custTransEstimate').append(obj);
                                }
                            }
                            catch (e) {

                            }
                        },
                        complete: function () {
                        }
                    });
                }
            }

            // inner sales order function called here




            let pageInnerSalesOrder = 1;
            let debounceFlagInnerSalesOrder = true;


            innerSoTable()

            $(".innerSalesOrder_wrapper").on('scroll', function () {
                const element = $(".innerSalesOrder_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerSalesOrder) {
                    innerSoTable();
                }
            });


            function innerSoTable() {
                let loadLimit = 10;
                if (debounceFlagInnerSalesOrder) {
                    // alert("called")


                    $.ajax({
                        type: "GET",
                        url: ajaxUrl,
                        dataType: "json",
                        data: {
                            act: "custTransSo",
                            custId,
                            limit: loadLimit,
                            page: pageInnerSalesOrder
                        },
                        beforeSend: function () {
                            debounceFlagInnerSalesOrder = false;
                        },
                        success: function (value) {
                            // console.log(value);
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;


                                    $.each(responseObj, function (index, val) {
                                        let status = ``;
                                        if (val.approvalStatus == 9) {
                                            status = `<p class="status-bg status-open">Open</p>`;
                                        } else if (val.approvalStatus == 14) {
                                            status = `<p class="status-bg status-pending">Pending</p>`;
                                        } else if (val.approvalStatus == 12) {
                                            status = `<p class="status-bg status-exceptional">Exceptional</p>`;
                                        } else if (val.approvalStatus == 10) {
                                            status = `<p class="status-bg status-closed">Closed</p>`;
                                        } else if (val.approvalStatus == 17) {
                                            status = `<p class="status-bg status-closed">Rejected</p>`;
                                        }

                                        tableInnerSoTable.row.add([

                                            `${val.so_number ?? "-"}`,
                                            `${val.customer_po_no ?? "-"}`,
                                            `${(val.delivery_date && !isNaN(new Date(val.delivery_date).getTime())) ? formatDate(val.delivery_date) : "-"}`,
                                            `${val.totalItems}`,
                                            `${status ?? "-"}`
                                        ]).draw(false);
                                    });


                                    pageInnerSalesOrder++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerSalesOrder = true;
                                    }
                                    //

                                } else if (value.status == "error") {
                                    $('#custTransSo').empty();
                                    let obj = `<tr><td colspan="5"><p class="text-center">No Sales Order Found</p> </td></tr>  `;
                                    debounceFlagInnerSalesOrder = false;
                                    $('#custTransSo').append(obj);
                                }
                            }
                            catch (e) {

                            }
                        },
                        complete: function () {
                        }
                    });
                }
            }
            // inner journals function called here



            let pageInnerJournals = 1;
            let debounceFlagInnerJournals = true;


            innerJournalsTable()

            $(".innerJournals_wrapper").on('scroll', function () {
                const element = $(".innerJournals_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerJournals) {
                    innerJournalsTable();
                }
            });


            function innerJournalsTable() {
                let loadLimit = 10;
                if (debounceFlagInnerJournals) {
                    // alert("called")


                    $.ajax({
                        type: "GET",
                        url: ajaxUrl,
                        dataType: "json",
                        data: {
                            act: "custTransJournal",
                            custId,
                            code,
                            limit: loadLimit,
                            page: pageInnerJournals
                        },
                        beforeSend: function () {
                            debounceFlagInnerJournals = false;
                        },
                        success: function (value) {
                            // console.log(value);
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        tableInnerJournalsTable.row.add([


                                            `${val.jv_no ?? "-"}`,
                                            `${val.refarenceCode ?? "-"}`,
                                            `${val.documentNo ?? "-"}`,
                                            `${(val.documentDate && !isNaN(new Date(val.documentDate).getTime())) ? formatDate(val.documentDate) : "-"}`,
                                            `${(val.postingDate && !isNaN(new Date(val.postingDate).getTime())) ? formatDate(val.postingDate) : "-"}`,
                                            `${trimString(val.remark, 20) ?? "-"}`

                                        ]).draw(false);
                                    });



                                    pageInnerJournals++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerJournals = true;
                                    }
                                    //

                                } else if (value.status == "error") {
                                    $('#custTransJournal').empty();
                                    let obj = `<tr><td colspan="5"><p class="text-center">No Journal Found</p> </td></tr>  `;
                                    debounceFlagInnerJournals = false;
                                    $('#custTransJournal').append(obj);
                                }
                            }
                            catch (e) {

                            }
                        },
                        complete: function () {
                        }
                    });
                }
            }

            // inner debit note function called here



            let pageInnerDebitNote = 1;
            let debounceFlagInnerDebitNote = true;


            innerDebitNote()

            $(".innerDebitNote_wrapper").on('scroll', function () {
                const element = $(".innerDebitNote_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerDebitNote) {
                    innerDebitNote();
                }
            });


            function innerDebitNote() {
                let loadLimit = 10;
                if (debounceFlagInnerDebitNote) {
                    // alert("called")


                    $.ajax({
                        type: "GET",
                        url: ajaxUrl,
                        dataType: "json",
                        data: {
                            act: "debit-note",
                            id: custId,
                            limit: loadLimit,
                            creditorsType: "customer",
                            page: pageInnerDebitNote
                        },
                        beforeSend: function () {
                            debounceFlagInnerDebitNote = false;
                        },
                        success: function (value) {
                            // console.log(value);
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        tableDebitNote.row.add([
                                            `${val.debit_note_no ?? "-"}`,
                                            `${val.party_code ?? "-"}`,
                                            `${val.party_name ?? "-"}`,
                                            `${val.invoice_code != null ? data.invoice_code : "-"}`,
                                            `${decimalAmount(val.total) ?? "-"}`,
                                            `${(val.postingDate && !isNaN(new Date(val.postingDate).getTime())) ? formatDate(val.postingDate) : "-"}`
                                        ]).draw(false);
                                    });



                                    pageInnerDebitNote++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerDebitNote = true;
                                    }
                                    //

                                } else if (value.status == "error") {
                                    $('#debitnotetable').empty();
                                    let obj = `<tr><td colspan="5"><p class="text-center">No Debit Note Found</p> </td></tr>  `;
                                    debounceFlagInnerDebitNote = false;
                                    $('#debitnotetable').append(obj);
                                }
                            }
                            catch (e) {

                            }
                        },
                        complete: function () {
                        }
                    });
                }
            }

            // inner credit note function called here



            let pageInnerCreditNote = 1;
            let debounceFlagInnerCreditNote = true;


            innerCreditNote()

            $(".innerCreditNote_wrapper").on('scroll', function () {
                const element = $(".innerCreditNote_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerCreditNote) {
                    innerCreditNote();
                }
            });


            function innerCreditNote() {
                let loadLimit = 10;
                if (debounceFlagInnerCreditNote) {
                    // alert("called")


                    $.ajax({
                        type: "GET",
                        url: ajaxUrl,
                        dataType: "json",
                        data: {
                            act: "credit-note",
                            id: custId,
                            limit: loadLimit,
                            creditorsType: "customer",
                            page: pageInnerCreditNote
                        },
                        beforeSend: function () {
                            debounceFlagInnerCreditNote = false;
                        },
                        success: function (value) {
                            // console.log(value);
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        tableCreditNote.row.add([
                                            `${val.credit_note_no ?? "-"}`,
                                            `${val.party_code ?? "-"}`,
                                            `${val.party_name ?? "-"}`,
                                            `${val.invoice_code != null ? val.invoice_code : "-"}`,
                                            `${decimalAmount(val.total) ?? "-"}`,
                                            `${(val.postingDate && !isNaN(new Date(val.postingDate).getTime())) ? formatDate(val.postingDate) : "-"}`
                                        ]).draw(false);
                                    });



                                    pageInnerCreditNote++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerCreditNote = true;
                                    }
                                    //

                                } else if (value.status == "error") {
                                    $('#creditnotetable').empty();
                                    let obj = `<tr><td colspan="5"><p class="text-center">No Credit Note Found</p> </td></tr>  `;
                                    debounceFlagInnerCreditNote = false;
                                    $('#creditnotetable').append(obj);
                                }
                            }
                            catch (e) {

                            }
                        },
                        complete: function () {
                        }
                    });
                }
            }






            // Transactional End



            //Mail Start


            $.ajax({
                type: "GET",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    act: "Mail",
                    ccode: code,
                    custId
                },
                beforeSend: function () {
                    $('.inbox-blocks').empty();
                },
                success: function (value) {
                    // console.log(value);
                    if (value.status === 'success') {
                        var rowmail = value.data;
                        $.each(rowmail, function (Key, Data) {
                            var cardHtml = `<a href="">
                                            <div class="mail-block">
                                                <div class="left-detail">
                                                    <p class="sender-mail">${Data.toaddress}</p>
                                                </div>
                                                <div class="subject-detail">
                                                    <p>${Data.mailTitle}</p>
                                                </div>
                                                <div class="right-detail">
                                                    <p class="time-date">${Data.created_at}</p>
                                                </div>
                                            </div>
                                        </a>`;
                            $('.inbox-blocks').append(cardHtml);
                        });
                    } else {
                        var cardHtml = `
                        <div class="subject-detail">
                             <p>History not found</p>
                         </div>`;
                        $('.inbox-blocks').append(cardHtml);
                    }
                },
            });
            $(".MailSend").click(function () {
                var shootingDays = $('#maildays').val();
                var operator = $('#mailoperator').val();
                // console.log(shootingDays+','+operator);
                $.ajax({
                    url: ajaxUrl,
                    type: 'GET',
                    dataType: "json",
                    data: {
                        act: "Mailsubmit",
                        id: custId,
                        shootingDays: shootingDays,
                        operator: operator
                    },
                    beforeSend: function () {

                    },
                    success: function (response) {
                        //  console.log(response);
                        if (response.message == "Data saved successfully") {
                            alert(response.message);
                        }
                        else {
                            alert("Data saved failed, try again later");
                        }
                    }
                });
            });



            //Mail end



            //Statment start



            function statement_date(customer_code) {
                $.ajax({
                    url: `ajaxs/customer/ajax-statement-18-11-2024.php`,
                    type: 'POST',
                    data: {

                        customer_code
                    },
                    beforeSend: function () {
                        $('#fromDate').val('');
                        $('#toDate').val('');
                        $('.statement-details').empty();
                    },
                    success: function (response) {
                        // alert(response);
                        //console.log(response);
                        var obj = jQuery.parseJSON(response);
                        //console.log(obj['html']);
                        $('.statement-details').html(obj['html']);
                    }
                });
            }


            $(document).on("change", ".dateDrop", function () {
                var customer_code = code;
                statement_date(customer_code);

            });

            $(".date_apply").click(function () {
                var from_date = $('#fromDate').val();
                var to_date = $('#toDate').val();


                $.ajax({
                    url: `ajaxs/customer/ajax-dateRange-statement-18-11-2024.php`,
                    type: 'POST',
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        customer_code: custId

                    },
                    beforeSend: function () {
                        $(".statement-details").empty();
                    },
                    success: function (response) {
                        //  console.log(response);
                        // alert(response);
                        var obj = jQuery.parseJSON(response);
                        $('.statement-details').html(obj['html']);
                    }
                });
            });



            //Statment end



            //Complinace Status start



            $.ajax({
                url: `ajaxs/vendor/ajax-gst-review.php?gstin=${gstin}`,
                type: 'get',
                beforeSend: function () {
                    $("#gstinReturnsDatacomp_Div").html(`Loading...`);
                    $("#gstinReturnsDatacomp3b_Div").html(`Loading...`);
                },

                success: function (response) {
                    // console.log(response);
                    responseObj = JSON.parse(response);
                    let fy = responseObj['fy'];
                    responseData = responseObj["data"];
                    let gstinReturnsDataDivHtml = `
                      <table class="table table-striped table-bordered w-100">
                      <thead>
                        <tr>
                          <th>Finalcial Year</th>
                          <th>Tax Period</th>
                          <th>Date of Filing</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>`;


                    responseData["EFiledlist"].forEach(function (rowVal, rowId) {
                        if (rowVal['rtntype'] == 'GSTR1') {
                            var dateString = rowVal["ret_prd"];

                            // Extract the first two characters as the month
                            var monthString = dateString.substr(0, 2);

                            // Convert the month string to an integer
                            var month = parseInt(monthString, 10);

                            // Array of month names
                            var monthNames = [
                                "January", "February", "March", "April", "May", "June",
                                "July", "August", "September", "October", "November", "December"
                            ];

                            // Get the month name based on the numeric month
                            var monthName = monthNames[month - 1]; // Subtract 1 because arrays are 0-based
                            gstinReturnsDataDivHtml += `
                            <tr>
                              <td>${fy}</td>
                              <td>${monthName ?? "-"}</td>
                              <td>${rowVal["dof"] ?? "-"}</td>
                              <td>${rowVal["status"] ? '<i class="fa fa-check" style="color: green;"> FILED</i>' : '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'}</td>
                          
                            </tr>
                          `;
                        }
                    });
                    gstinReturnsDataDivHtml += `</tbody></table>`;
                    //3b
                    let gstinReturnsDataDivHtml3b = `
                      <table class="table table-striped table-bordered w-100">
                      <thead>
                        <tr>
                          <th>Finalcial Year</th>
                          <th>Tax Period</th>
                          <th>Date of Filing</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>`;
                    responseData["EFiledlist"].forEach(function (rowVal, rowId) {
                        if (rowVal['rtntype'] == 'GSTR3B') {
                            var dateString = rowVal["ret_prd"];

                            // Extract the first two characters as the month
                            var monthString = dateString.substr(0, 2);

                            // Convert the month string to an integer
                            var month = parseInt(monthString, 10);

                            // Array of month names
                            var monthNames = [
                                "January", "February", "March", "April", "May", "June",
                                "July", "August", "September", "October", "November", "December"
                            ];

                            // Get the month name based on the numeric month
                            var monthName = monthNames[month - 1]; // Subtract 1 because arrays are 0-based
                            gstinReturnsDataDivHtml3b += `
                        <tr>
                          <td>${fy}</td>
                          <td>${monthName ?? "-"}</td>
                          <td>${rowVal["dof"] ?? "-"}</td>
                          <td>${rowVal["status"] ? '<i class="fa fa-check" style="color: green;"> FILED</i>' : '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'}</td>
                      
                        </tr>
                          `;
                        }
                    });
                    gstinReturnsDataDivHtml3b += `</tbody></table>`;
                    $("#gstinReturnsDatacomp_Div").html(gstinReturnsDataDivHtml);
                    $("#gstinReturnsDatacomp3b_Div").html(gstinReturnsDataDivHtml3b);
                }
            });

            //Complinace Status end


            //reconciliation start



            function reconciliation(from_date, to_date, attr) {
                $.ajax({
                    url: `ajaxs/customer/ajax-reconciliation-18-11-2024.php`,
                    type: 'POST',
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        party_code: attr
                    },
                    beforeSend: function () {

                        $(".recon_preview").html(` <div class="spinner-border text-dark" role="status">
                                                  <span class="visually-hidden">Loading...</span>
                                                </div>`)
                    },
                    success: function (response) {
                        // console.log(response);
                        $(".recon_preview").html(response);
                    }

                });
            }
            $(".date_apply_recon").click(function () {
                var from_date = $('#fromDate_rec').val();
                var to_date = $('#toDate_rec').val();
                reconciliation(from_date, to_date, custId);
            });



            //reconciliation end



            //Trail start
            $.ajax({
                url: 'ajaxs/audittrail/ajax-audit-trail-customer.php?auditTrailBodyContent', // <-- point to server-side PHP script 
                type: 'POST',
                data: {
                    ccode: code,
                    id: custId
                },
                beforeSend: function () {
                    // console.log(code);
                    // console.log(custId);
                    $("#audit-head-section_" + custId).empty();
                    $(".auditTrailBodyContentCustomer").empty();
                    $(".auditTrailBodyContentCustomer").html('Loading...');
                },
                success: function (responseData) {
                    // console.log(responseData);
                    let $output = '<p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span>' + created_by + '<span class="font-bold text-normal"> on </span>' + created_at + '</p>' +
                        '<p class="text-xs font-italic"><span class="font-bold text-normal">Last Updated by </span>' + updated_by + '<span class="font-bold text-normal"> on </span>' + updated_at + '</p>';
                    // console.log($output);
                    $("#audit-head-section_" + custId).empty();
                    $(".auditTrailBodyContentCustomer").empty();
                    $("#audit-head-section_" + custId).append($output);
                    $(`.auditTrailBodyContentCustomer`).html(responseData);
                }
            });

            $(document).on("click", ".auditTrailBodyContentLineCustomer", function () {
                $(`.auditTrailBodyContentLineDiv`).html(`<div class="modal-header">
                                                            <div class="head-audit">
                                                                <p><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading ...</p>
                                                            </div>
                                                            <div class="head-audit">
                                                                <p>xxxxxxxxxxxxxx</p>
                                                                <p>xxxxxxxxx</p>
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
                                                                <div class="tab-pane fade show active" id="consize" role="tabpanel" aria-labelledby="consize-tab">
                                                                <div class="dotted-box">
                                                                    <p class="overlap-title">Loading ...</p>
                                                                    <div class="box-content hightlight">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                    <div class="box-content">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                    <div class="box-content">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                </div>
                                                                </div>

                                                                <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                                                                <div class="dotted-box">
                                                                    <p class="overlap-title">Loading ...</p>
                                                                    <div class="box-content hightlight">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                    <div class="box-content">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                    <div class="box-content">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                </div>
                                                                <div class="dotted-box">
                                                                    <p class="overlap-title">Loading ...</p>
                                                                    <div class="box-content hightlight">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                    <div class="box-content">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                    <div class="box-content">
                                                                    <p>xxxxxxxxxxxxx</p>
                                                                    <p>xxxxxxx</p>
                                                                    </div>
                                                                </div>
                                                                </div>
                                                                <!-- -------------------Audit History Tab Body End------------------------- -->
                                                            </div>
                                                        </div>`);
                var c = $(this).data('ccode');
                var id = $(this).data('id');
                // // alert(ccode);
                $.ajax({
                    url: 'ajaxs/audittrail/ajax-audit-trail-customer.php?auditTrailBodyContentLine', // <-- point to server-side PHP script 
                    type: 'POST',
                    data: {
                        ccode: c,
                        id: id
                    },
                    beforeSend: function () {
                        //console.log(code);
                        //console.log(custId);
                        $(".auditTrailBodyContentLineDiv").empty();
                    },
                    success: function (responseData) {
                        $(`.auditTrailBodyContentLineDiv`).html(responseData);
                    }
                });
            });
            //Trail End
        });




        // $('.soModal').on('shown.bs.modal', function () {
        //     $(".innerCollections_wrapper").scrollTop(0);
        // });

        // $('.soModal').on('hidden.bs.modal', function () {
        //     $(".innerCollections_wrapper").scrollTop(0);
        // });
        $(document).on('click', '.editCustomer', function () {
            let id = $(this).data('id');
            let code = $(this).data('code');
            let url = `customer-actions.php?edit=${btoa(id)}`;
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `Are you sure to Edit this Customer ( ${code} ) ?`,
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
    })
</script>