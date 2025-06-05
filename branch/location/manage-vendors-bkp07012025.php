<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
// administratorLocationAuth();
// Add Functions
// require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
require_once("../../app/v1/functions/common/templates/template-sales-order.controller.php");



$pageName = basename($_SERVER['PHP_SELF'], '.php');


if (!isset($_COOKIE["cookiemanagevendors"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiemanagevendors", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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
        'name' => 'Vendor Code',
        'slag' => 'vendor_code',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Name',
        'slag' => 'trade_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => ''
    ],
    [
        'name' => 'Vendor Pan',
        'slag' => 'vendor_pan',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => ''
    ],
    [
        'name' => 'Constitution of Business',
        'slag' => 'constitution_of_business',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'GSTIN',
        'slag' => 'vendor_gstin',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Email',
        'slag' => 'vendor_authorised_person_email',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Phone',
        'slag' => 'vendor_authorised_person_phone',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'vendor_status',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]


];

?>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<style>
    /* css for the onscroll events  */

    .innerQuotations_wrapper {
        overflow-y: auto;
        max-height: 500px;

    }

    .innerPurchaseOrder_wrapper {
        overflow-y: auto;
        max-height: 500px;
        /* position: relative; */
        /* scroll-behavior: smooth; */
        /* will-change: scroll-position; */
    }

    .innerBillsTable_wrapper {
        overflow-y: auto;
        max-height: 500px;
    }

    .innerPayments_wrapper {
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


    /* css for overview section tabs accordion */

    #infoSection .row .col-lg-6 .accordion .accordion-item .accordion-collapse .accordion-body {
        display: grid;
        grid-template-columns: 4fr;
        gap: 35px;
        background: #cccccc29;
        border-radius: 7px;
        margin-top: 7px;
        height: auto;
        overflow: scroll;
        scrollbar-width: none;
    }

    #infoSection .row {
        --bs-gutter-y: 7.5px;
    }


    /* css for bills button fix */

    #billsBtnFix {
        position: absolute;
        right: 155px;
    }





    /* css for dt-top-container search label fix */

    .innerVendTransDiv .dt-top-container {
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

    .innerVendTransDiv .dataTables_wrapper .vendTransDatatable {
        clear: both;
        margin-top: 0px !important;
        margin-bottom: 6px !important;
        max-width: none !important;
        border-collapse: separate !important;
        border-spacing: 0;
    }



    .innerVendTransDiv .dataTables_wrapper .dt-top-container .dataTables_filter {
        display: flex !important;
        align-items: center;
        justify-content: start;
        position: absolute;
        right: 5px;
        top: 0px;
    }



    .innerVendTransDiv .dataTables_wrapper .dt-top-container .dataTables_filter input {
        margin-left: 0;
        display: inline-block;
        width: auto;
        padding-left: 30px;
        border: 1px solid #bfbdbd;
        color: #1B2559;
        height: 30px;
        border-radius: 8px;
    }


    #innerBillsTableBody .dataTables_wrapper .dt-top-container .dataTables_filter {
        display: flex !important;
        align-items: center;
        justify-content: start;
        position: absolute;
        right: auto;
        top: 0px;
    }

    #innerBillsTableBody .dt-top-container {
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

    #innerBillsTableBody .dataTables_wrapper .dt-top-container .dataTables_filter input {
        margin-left: 0;
        display: inline-block;
        width: auto;
        padding-left: 30px;
        border: 1px solid #bfbdbd;
        color: #1B2559;
        height: 30px;
        border-radius: 8px;
    }

    #innerBillsTableBody .dataTables_wrapper .vendTransDatatable {
        clear: both;
        margin-top: 0px !important;
        margin-bottom: 6px !important;
        max-width: none !important;
        border-collapse: separate !important;
        border-spacing: 0;
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
                                                <h3 class="card-title mb-0">Manage Vendors</h3>
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
                                            <a href="vendorAction.php?create"
                                                class="btn btn-create" type="button">
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
                                                                            id="vendorName"></span></p>
                                                                    <p class="info-detail po-number"><ion-icon
                                                                            name="information-outline"></ion-icon><span
                                                                            id="vendorCode"></span></p>
                                                                    <p class="info-detail po-number"><ion-icon
                                                                            name="information-outline"></ion-icon><span
                                                                            id="vendorCob"></span></p>
                                                                    <p class="info-detail ref-number"><ion-icon
                                                                            name="information-outline"></ion-icon><span
                                                                            id="vendorGst"></span></p>
                                                                </div>
                                                                <div class="right">
                                                                    <p class="info-detail name"><ion-icon
                                                                            name="person-outline"></ion-icon><span
                                                                            id="vendorPerson"></span></p>
                                                                    <p class="info-detail qty"><ion-icon
                                                                            name="document-outline"></ion-icon><span
                                                                            id="vendorPersonDesg"></span></p>
                                                                    <p class="info-detail qty"><ion-icon
                                                                            name="call-outline"></ion-icon><span
                                                                            id="vendorPersonPhone"></span></p>
                                                                    <p class="info-detail qty"><ion-icon
                                                                            name="mail-outline"></ion-icon><span
                                                                            id="vendorPersonMail"></span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <nav>
                                                                <div class="nav nav-tabs global-view-navTabs"
                                                                    id="nav-tab" role="tablist">
                                                                    <button class="nav-link active"
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
                                                                    <button class="nav-link" id="nav-trail-tab"
                                                                        data-bs-toggle="tab" data-bs-target="#nav-trail"
                                                                        type="button" role="tab"
                                                                        aria-controls="nav-trail"
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



                                                                    <!-- <div class="col-lg-12 col-md-12 col-xs-12"> -->
                                                                    <div class="card flex-fill bg-transparent">
                                                                        <div
                                                                            class="card-header bg-transparent p-0 border-bottom-0">
                                                                            <h5 class="card-title text-nowrap pl-3">
                                                                                Chart View</h5>
                                                                            <div id="containerThreeDot">

                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="load-wrapp">
                                                                                <div class="load-1">
                                                                                    <div class="line"></div>
                                                                                    <div class="line"></div>
                                                                                    <div class="line"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div id="chartDivSalesVsCollection"
                                                                                class="chartContainer"></div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- </div> -->
                                                                    <div id="infoSection" class="info-view">
                                                                        <h5 class="title">Details View</h5>
                                                                        <hr>
                                                                        <div class="row">



                                                                            <div
                                                                                class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                <div class="accordion view-modal-accordion"
                                                                                    id="accordionExample">
                                                                                    <div class="accordion-item">
                                                                                        <h2 class="accordion-header"
                                                                                            id="headingOne">
                                                                                            <button
                                                                                                class="accordion-button"
                                                                                                type="button"
                                                                                                data-bs-toggle="collapse"
                                                                                                data-bs-target="#collapseBasic"
                                                                                                aria-expanded="true"
                                                                                                aria-controls="collapseOne">
                                                                                                <ion-icon
                                                                                                    name="information-outline"></ion-icon>
                                                                                                Basic Details
                                                                                            </button>
                                                                                        </h2>
                                                                                        <div id="collapseBasic"
                                                                                            class="accordion-collapse collapse show"
                                                                                            aria-labelledby="headingOne"
                                                                                            data-bs-parent="#accordionExample">

                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>


                                                                            <div
                                                                                class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                <div class="accordion view-modal-accordion"
                                                                                    id="accordionExample">
                                                                                    <div class="accordion-item">
                                                                                        <h2 class="accordion-header"
                                                                                            id="headingOne">
                                                                                            <button
                                                                                                class="accordion-button"
                                                                                                type="button"
                                                                                                data-bs-toggle="collapse"
                                                                                                data-bs-target="#collapseAddress"
                                                                                                aria-expanded="true"
                                                                                                aria-controls="collapseOne">
                                                                                                <ion-icon
                                                                                                    name="information-outline"></ion-icon>
                                                                                                Address
                                                                                            </button>
                                                                                        </h2>
                                                                                        <div id="collapseAddress"
                                                                                            class="accordion-collapse collapse show"
                                                                                            aria-labelledby="headingOne"
                                                                                            data-bs-parent="#accordionExample">

                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>


                                                                            <div
                                                                                class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                <div class="accordion view-modal-accordion"
                                                                                    id="accordionExample">
                                                                                    <div class="accordion-item">
                                                                                        <h2 class="accordion-header"
                                                                                            id="headingOne">
                                                                                            <button
                                                                                                class="accordion-button"
                                                                                                type="button"
                                                                                                data-bs-toggle="collapse"
                                                                                                data-bs-target="#collapseAccounting"
                                                                                                aria-expanded="true"
                                                                                                aria-controls="collapseOne">
                                                                                                <ion-icon
                                                                                                    name="information-outline"></ion-icon>
                                                                                                Accounting
                                                                                            </button>
                                                                                        </h2>
                                                                                        <div id="collapseAccounting"
                                                                                            class="accordion-collapse collapse show"
                                                                                            aria-labelledby="headingOne"
                                                                                            data-bs-parent="#accordionExample">
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>






                                                                            <!--------OtheraddressDetails--------->

                                                                            <div
                                                                                class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                                                <div class="accordion view-modal-accordion"
                                                                                    id="accordionExample">
                                                                                    <div class="accordion-item">
                                                                                        <h2 class="accordion-header"
                                                                                            id="headingOne">
                                                                                            <button
                                                                                                class="accordion-button"
                                                                                                type="button"
                                                                                                data-bs-toggle="collapse"
                                                                                                data-bs-target="#collapseOtherAddress"
                                                                                                aria-expanded="true"
                                                                                                aria-controls="collapseOne">
                                                                                                <ion-icon
                                                                                                    name="information-outline"></ion-icon>
                                                                                                Other Address
                                                                                            </button>
                                                                                        </h2>
                                                                                        <div id="collapseOtherAddress"
                                                                                            class="accordion-collapse collapse show"
                                                                                            aria-labelledby="headingOne"
                                                                                            data-bs-parent="#accordionExample">
                                                                                        </div>

                                                                                    </div>
                                                                                </div>
                                                                            </div>





                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                    <div class="card flex-fill bg-transparent">
                                                                        <div class="accordion-header">

                                                                            <h5 class="card-title text-nowrap pl-3">
                                                                                Payables Ageing</h5>


                                                                            <div id="containerThreeDot"
                                                                                class="pieclass">


                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="load-wrapp">
                                                                                <div class="load-1">
                                                                                    <div class="line"></div>
                                                                                    <div class="line"></div>
                                                                                    <div class="line"></div>
                                                                                </div>
                                                                            </div>
                                                                            <div id="chartDivPayableAgeing"
                                                                                class="pieChartContainer">

                                                                            </div>

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
                                                                                    Quotations</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-collectioninner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-collectioninner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-collectioninner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="podium-outline"></ion-icon>Purchase
                                                                                    Orders</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-estimatesinner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-estimatesinner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-estimatesinner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="ticket-outline"></ion-icon>Bills</button>
                                                                            </li>
                                                                            <li class="nav-item" role="presentation">
                                                                                <button class="nav-link"
                                                                                    id="pills-salesorderinner-tab"
                                                                                    data-bs-toggle="pill"
                                                                                    data-bs-target="#pills-salesorderinner"
                                                                                    type="button" role="tab"
                                                                                    aria-controls="pills-salesorderinner"
                                                                                    aria-selected="false"><ion-icon
                                                                                        name="pricetags-outline"></ion-icon>Payments</button>
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

                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Quotations</h4>
                                                                                        <a href="manage-rfq.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Create
                                                                                            RFQ</a>

                                                                                    </div>
                                                                                    <div
                                                                                        class="innerQuotations innerVendTransDiv">
                                                                                        <table id="quotationsTable"
                                                                                            class="exportTable vendTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>RFQ Code</th>
                                                                                                    <th>Item</th>
                                                                                                    <th>MOQ</th>
                                                                                                    <th>Price</th>
                                                                                                    <th>Discount</th>
                                                                                                    <th>Total</th>
                                                                                                    <th>GST</th>
                                                                                                    <th>Lead Time</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody id="vendQuot">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-collectioninner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-collectioninner-tab">

                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Purchase
                                                                                            Orders</h4>
                                                                                        <a href="manage-purchases-orders.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage
                                                                                            PO </a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerPurchaseOrder innerVendTransDiv">
                                                                                        <table
                                                                                            class="exportTable vendTransDatatable"
                                                                                            id="purchaseOrderTable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>PO Number</th>
                                                                                                    <th>Reference Number
                                                                                                    </th>
                                                                                                    <th>PO Type</th>
                                                                                                    <th>Delivery Date
                                                                                                    </th>
                                                                                                    <th>Posting Date
                                                                                                    </th>
                                                                                                    <th> Total Amount
                                                                                                    </th>
                                                                                                    <th> Total Quantity
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody id="vendPurchsOrdr">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>




                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-estimatesinner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-estimatesinner-tab">


                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Bills</h4>
                                                                                        <a id="billsBtnFix"
                                                                                            href="manage-vendor-invoice.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage
                                                                                            Invoice
                                                                                            Quotation</a>
                                                                                    </div>
                                                                                    <div id="innerBillsTableBody"
                                                                                        class="innerBillsTable">
                                                                                        <table id="billsTable"
                                                                                            class="exportTable vendTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>Number</th>
                                                                                                    <th>Date</th>
                                                                                                    <th>GRN/SRN Code
                                                                                                    </th>
                                                                                                    <th>GRN/SRN Date
                                                                                                    </th>
                                                                                                    <th>PO Number</th>
                                                                                                    <th>PO Date</th>
                                                                                                    <th>IV Number</th>
                                                                                                    <th>IV Date</th>
                                                                                                    <th>Due Date</th>
                                                                                                    <th> Basic Amount
                                                                                                    </th>
                                                                                                    <th>GST</th>
                                                                                                    <th>TDS</th>
                                                                                                    <th>Net Payable</th>
                                                                                                    <th>Payment Made
                                                                                                    </th>
                                                                                                    <th>Due Amt</th>
                                                                                                    <th>Due%</th>
                                                                                                    <th>Status</th>
                                                                                                    <th>Reversal Status
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody id="vendorBills">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-salesorderinner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-salesorderinner-tab">

                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Payments</h4>
                                                                                        <a href="manage-payments.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage
                                                                                            Payments</a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerPayments innerVendTransDiv">
                                                                                        <table id="innerPayments"
                                                                                            class="exportTable vendTransDatatable">
                                                                                            <thead
                                                                                                class="innerTableHeadPos">
                                                                                                <tr>
                                                                                                    <th>Transaction Id
                                                                                                    </th>
                                                                                                    <th>Payment Type
                                                                                                    </th>
                                                                                                    <th>Collect Payment
                                                                                                    </th>
                                                                                                    <th>Posting Date
                                                                                                    </th>
                                                                                                    <th> Status</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody id="vendPymnts">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-journalinner" role="tabpanel"
                                                                                aria-labelledby="pills-journalinner-tab">

                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Journals</h4>
                                                                                        <a href="manage-journal.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Manage
                                                                                            Journal</a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="innerJournals innerVendTransDiv">
                                                                                        <table id="innerJournal"
                                                                                            class="exportTable vendTransDatatable">
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
                                                                                            <tbody id="vendorJournal">
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>

                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade"
                                                                                id="pills-debitnotesinner"
                                                                                role="tabpanel"
                                                                                aria-labelledby="pills-debitnotesinner-tab">

                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Debit Notes</h4>
                                                                                        <!-- <button
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Create
                                                                                            invoice</button> -->
                                                                                        <a href="manage-debit-notes.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Create
                                                                                            Debit Note</a>
                                                                                    </div>

                                                                                    <div class="innerVendTransDiv">

                                                                                        <table id="innerDebitNote"
                                                                                            class="exportTable vendTransDatatable">
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

                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Credit Notes</h4>
                                                                                        <!-- <button
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Create
                                                                                            invoice</button> -->
                                                                                        <a href="manage-credit-notes.php"
                                                                                            target="_blank"
                                                                                            class="btn btn-primary"><ion-icon
                                                                                                name="add-outline"></ion-icon>Create
                                                                                            Credit Note</a>
                                                                                    </div>
                                                                                    <div class="innerVendTransDiv">
                                                                                        <table id="innerCreditNote"
                                                                                            class="exportTable vendTransDatatable">
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

                                                                        </ul>
                                                                        <div class="tab-content" id="pills-tabContent">
                                                                            <div class="tab-pane fade show active"
                                                                                id="pills-mailInbox" role="tabpanel"
                                                                                aria-labelledby="pills-mailInbox-tab">
                                                                                <div class="inbox-blocks">



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
                                                                                        class="form-control">
                                                                                        <option value="">Select</option>
                                                                                        <option value="">This Month
                                                                                        </option>
                                                                                        <option value="">Custom</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-12 col-md-9">
                                                                                <div class="date-fields">
                                                                                    <div class="form-inline">
                                                                                        <label for="">From</label>
                                                                                        <input type="date"
                                                                                            id="from_date"
                                                                                            class="form-control">
                                                                                    </div>
                                                                                    <div class="form-inline">
                                                                                        <label for="">To</label>
                                                                                        <input type="date" id="to_date"
                                                                                            class="form-control">
                                                                                    </div>
                                                                                    <button id="stmntDateApply"
                                                                                        class="btn btn-primary">
                                                                                        <ion-icon
                                                                                            name="arrow-forward-outline"></ion-icon>
                                                                                        Apply
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div id="statementSection">


                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane compliance-tab-pane fade"
                                                                    id="nav-compliance" role="tabpanel"
                                                                    aria-labelledby="nav-compliance-tab">


                                                                    <!--<tr>
                                                                                            <td>
                                                                                                2023-24
                                                                                            </td>
                                                                                            <td>March</td>
                                                                                            <td>18-04-2023</td>
                                                                                            <td>
                                                                                                <p
                                                                                                    class="gst-non-filed">
                                                                                                    <ion-icon
                                                                                                        name="close-outline"></ion-icon>Not
                                                                                                    Filed
                                                                                                </p>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div> -->

                                                                    <!-- <div id="complianceContent">

                                                                    </div> -->

                                                                </div>
                                                                <div class="tab-pane recon-tab-pane fade"
                                                                    id="nav-reconciliation" role="tabpanel"
                                                                    aria-labelledby="nav-reconciliation-tab">
                                                                    <div class="inner-content">
                                                                        <div class="date-fields">
                                                                            <div class="form-inline">
                                                                                <label for="">From</label>
                                                                                <input type="date" id="from-recon"
                                                                                    class="form-control">
                                                                            </div>
                                                                            <div class="form-inline">
                                                                                <label for="">To</label>
                                                                                <input type="date" id="to-recon"
                                                                                    class="form-control">
                                                                            </div>
                                                                            <button id="date-recon-apply"
                                                                                class="btn btn-primary waves-effect waves-light">
                                                                                <ion-icon name="arrow-forward-outline"
                                                                                    role="img" class="md hydrated"
                                                                                    aria-label="arrow forward outline"></ion-icon>
                                                                                Apply
                                                                            </button>
                                                                        </div>


                                                                        <div id="reconciliationContent">

                                                                        </div>

                                                                        <p class="recon-note">All valuesare in
                                                                            <b>INR</b>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="nav-trail"
                                                                    role="tabpanel" aria-labelledby="nav-trail-tab">
                                                                    <div class="inner-content">
                                                                        <div class="audit-head-section mb-3 mt-3 ">

                                                                        </div>
                                                                        <hr>
                                                                        <div
                                                                            class="audit-body-section mt-2 mb-3 auditTrailBodyContentVendor">

                                                                        </div>
                                                                        <!-- <div class="modal fade right audit-history-modal"
                                                                            id="innerModal" role="dialog"
                                                                            aria-labelledby="innerModalLabel"
                                                                            aria-modal="true">
                                                                            <div class="modal-dialog">
                                                                                <div
                                                                                    class="modal-content auditTrailBodyContentLineDiv">


                                                                                </div>
                                                                            </div>
                                                                        </div> -->
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
            var checkboxSettings = Cookies.get('cookiemanagevendors');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-vendors.php",
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
                        // console.log(responseObj[0].sl_no);
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);

                        $.each(responseObj, function (index, value) {

                            let status = ``;
                            if (value.vendorStatus == "guest" || value.vendorStatus == "inactive") {
                                status = `<p class='status-bg status-pending'>Guest</p>`;
                            } else if (value.vendorStatus == "active") {
                                status = `<p class='status-bg status-approved'>Active</p>`;
                            } else if (value.vendorStatus == "draft") {
                                status = `<p class='status-bg status-pending'>Draft</p>`;
                            }

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal" data-id="${value.vendorId}" data-vendor_gstin=${value.vendor_gstin} data-code="${value.vendor_code}" data-toggle="modal" data-target="#viewGlobalModal">${value.vendor_code}</a>`,
                                value.trade_name,
                                value.vendor_pan,
                                value.constitution_of_business,
                                value.vendor_gstin,
                                value.email,
                                value.phone,
                                status,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                        <li>
                                            <button data-toggle="modal" class="editbtn" data-id=${value.vendorId} data-vndGstin=${value.vendor_gstin} data-code="${value.vendor_code}"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                        </li>
                                        <li>
                                            <button class="soModal" data-toggle="modal" data-id=${value.vendorId} data-vndGstin=${value.vendor_gstin} data-code="${value.vendor_code}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
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

            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'vendors',
                        fromData: fromData
                    },
                    success: function (response) {
                        console.log(response);
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


<!------------ modal ajax--------- -->

<script>
    $(document).ready(function () {


        // vendor modal transactional tab inner DataTables  



        let tableQuotation;

        tableQuotation = $('#quotationsTable').DataTable({
            // dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerQuotations_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#quotationsTable_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,
        });



        let tablePurchaseOrder;

        tablePurchaseOrder = $('#purchaseOrderTable').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerPurchaseOrder_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#purchaseOrderTable_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,
        });

        let tableBills;

        tableBills = $('#billsTable').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerBillsTable_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#billsTable_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });


        let tablePayments;

        tablePayments = $('#innerPayments').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerPayments_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerPayments_filter input[type="search"]').attr('placeholder', 'Search....');
            },


            buttons: [],
            // select: true,
            "bPaginate": false,
        });


        let tableJournal;

        tableJournal = $('#innerJournal').DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"innerJournals_wrapper"t><ip>',
            "lengthMenu": [10, 25, 50, 100, 200],
            "ordering": false,
            info: false,
            "pageLength": true,
            "initComplete": function (settings, json) {
                $('#innerJournal_filter input[type="search"]').attr('placeholder', 'Search....');
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
        // $(document).on("change", ".custom-select-innerPurchaseOrderTable", function (e) {
        //         var maxlimit = $(this).val();
        //         innerPurchaseOrderTable(maxlimit, page_id = "");
        //     });
        let ajaxUrl;
        let vendorId;
        let vendor_code;
        $(document).on("click", ".soModal", function () {

            $('#viewGlobalModal').modal('show');
            $('#nav-overview-tab').tab('show');
            $(".classic-view").html('');
            vendorId = $(this).data('id');
            vendor_code = $(this).data('code');
            let vendor_gstin = $(this).data('vendor_gstin');
            let created_at, updated_at, created_by, updated_by;
            // $(".custom-select-innerPurchaseOrderTable").val("25");

            // console.log(updated_at, created_by, updated_by , updated_by)

            ajaxUrl = "ajaxs/modals/vendor/ajax-manage-vendor-modal.php";
            $("#reconciliationContent").html(``);
            $("#statementSection").html("");
            // $("#nav-overview-tab").trigger("click");
            // Set the active tab

            $(document).on("click", "#nav-transaction-tab", function () {
                $('#pills-invoicesinner-tab').tab('show');
            });



            tableQuotation.clear().draw();
            tablePurchaseOrder.clear().draw();
            tableBills.clear().draw();
            tablePayments.clear().draw();
            tableJournal.clear().draw();
            tableDebitNote.clear().draw();
            tableCreditNote.clear().draw();

            // console.log(vendor_gstin)

            // var hhValue = so_no + 'test';
            // $('.auditTrail').attr("data-ccode", so_no);
            // console.log(vendorId)
            // alert(vendorId)


            function salesVsCollection(chartData, chartTitle, vendId) {
                // console.log(chartData, chartTitle, vendId);
                $(`.${chartTitle}`).text(`Payable Vs Paid`);

                if (chartData.sql_list_all_cust.length == 0 && chartData.sql_list_specific_cust.length == 0) {
                    const currentDate = new Date();
                    const year = currentDate.getFullYear();
                    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                    const day = String(currentDate.getDate()).padStart(2, '0');

                    const formattedDate = `${year}-${month}-${day}`;

                    chartData = {
                        "sql_list_all_cust": [{
                            date_: formattedDate,
                            total_payable_all: 0,
                            total_paid_all: 0
                        }],
                        "sql_list_specific_cust": [{
                            date_: formattedDate,
                            total_payable: 0,
                            total_paid: 0
                        }]
                    };
                };

                am4core.ready(function () {

                    // Themes begin
                    am4core.useTheme(am4themes_animated);
                    // Themes end

                    // Create chart instance
                    var chart = am4core.create(`${chartTitle}`, am4charts.XYChart);
                    chart.logo.disabled = true;

                    let finalData = [];
                    let outerIndex = 0;

                    for (obj of chartData.sql_list_all_cust) {
                        obj.total_payable_all = Number(obj.total_payable);
                        obj.total_paid_all = Number(obj.total_paid);
                        obj.total_payable = 0;
                        obj.total_paid = 0;
                        finalData.push(obj);
                    };

                    for (obj of chartData.sql_list_specific_cust) {

                        const outerObj = finalData.map(obj => {
                            return obj.date_
                        })
                        outerIndex = outerObj.indexOf(obj.date_)

                        if (outerIndex !== -1) {
                            finalData[outerIndex].total_payable = Number(obj.total_payable);
                            finalData[outerIndex].total_paid = Number(obj.total_paid);
                        } else {
                            obj.total_payable = Number(obj.total_payable);
                            obj.total_paid = Number(obj.total_paid);
                            obj.total_payable_all = 0;
                            obj.total_paid_all = 0;
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
                    valueAxis1.title.text = "This Vendor";

                    var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
                    valueAxis2.title.text = "All Vendors";
                    valueAxis2.renderer.opposite = true;
                    valueAxis2.renderer.grid.template.disabled = true;

                    // Create series
                    var series1 = chart.series.push(new am4charts.ColumnSeries());
                    series1.dataFields.valueY = "total_payable";
                    series1.dataFields.dateX = "date_";
                    series1.yAxis = valueAxis1;
                    series1.name = "Payable";
                    series1.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
                    series1.fill = chart.colors.getIndex(0);
                    series1.strokeWidth = 0;
                    series1.clustered = false;
                    series1.columns.template.width = am4core.percent(40);

                    var series2 = chart.series.push(new am4charts.ColumnSeries());
                    series2.dataFields.valueY = "total_paid";
                    series2.dataFields.dateX = "date_";
                    series2.yAxis = valueAxis1;
                    series2.name = "Paid";
                    series2.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";
                    series2.fill = chart.colors.getIndex(0).lighten(0.5);
                    series2.strokeWidth = 0;
                    series2.clustered = false;
                    series2.toBack();

                    var series3 = chart.series.push(new am4charts.LineSeries());
                    series3.dataFields.valueY = "total_paid_all";
                    series3.dataFields.dateX = "date_";
                    series3.name = "Paid (all vendors)";
                    series3.strokeWidth = 2;
                    series3.tensionX = 0.7;
                    series3.yAxis = valueAxis2;
                    series3.tooltipText = "{name}\n[bold font-size: 20]{valueY}[/]";

                    var bullet3 = series3.bullets.push(new am4charts.CircleBullet());
                    bullet3.circle.radius = 3;
                    bullet3.circle.strokeWidth = 2;
                    bullet3.circle.fill = am4core.color("#fff");

                    var series4 = chart.series.push(new am4charts.LineSeries());
                    series4.dataFields.valueY = "total_payable_all";
                    series4.dataFields.dateX = "date_";
                    series4.name = "Payable (all vendors)";
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
                    act: "chartMenuOptions",

                },
                beforeSend: function () {
                    $("#containerThreeDot").empty();
                },
                success: function (value) {
                    let jsonResponse = JSON.parse(value);
                    // console.log(jsonResponse.data);
                    let options = '';
                    jsonResponse.data.forEach(function (data) {
                        options += `<option value="${data.year_variant_id}">${data.year_variant_name}</option>`;
                    });
                    $("#containerThreeDot").html(`<div id="menu-wrap">
                                                    <input type="checkbox" class="toggler bg-transparent" />
                                                    <div class="dots">
                                                        <div></div>
                                                    </div>
                                                    <div class="menu">
                                                        <div>
                                                            <ul>
                                                                <li>
                                                                    <select name="fYDropdown" id="fYDropdown_${vendorId}" data-attr="${vendorId}" class="form-control fYDropdown">
                                                                        ${options}
                                                                    </select>
                                                                </li>
                                                                <li><label class="mb-0" for="">OR</label></li>
                                                                <li>
                                                                    <input type="month" name="monthRange" id="monthRange_${vendorId}" data-attr="${vendorId}" class="form-control monthRange" style="max-width: 100%;" />
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>

`);



                    for (elem of $(".chartContainer")) {
                        let dataAttrValue = vendorId;
                        let id = $(`#fYDropdown_${vendorId} option:first`).val();
                        // console.log(id);
                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?id=${id}&vend_id=${dataAttrValue}`,
                            beforeSend: function () {
                                // console.log(dataAttrValue);
                                // console.log(id);
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

                        // function monthWiseChart() {
                        var dataAttrValue = $(this).data('attr');
                        var id = $(`#fYDropdown_${dataAttrValue}`).val();

                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?id=${id}&vend_id=${dataAttrValue}`,
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
                        // };

                        // monthWiseChart();
                    });

                    $(document).on("change", '.monthRange', function () {

                        // function dayWiseChart() {
                        var dataAttrValue = $(this).data('attr');
                        var month = $(`#monthRange_${dataAttrValue}`).val();

                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?month=${month}&vend_id=${dataAttrValue}`,
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
                        // };

                        // dayWiseChart();
                    });
                },
            });



            $.ajax({
                type: "GET",
                url: ajaxUrl,
                data: {
                    act: "pieMenuOptions",

                },
                beforeSend: function () {
                    $(".pieclass").empty();
                },
                success: function (value) {
                    // console.log(value);
                    let jsonResponse = JSON.parse(value);
                    let options = '';
                    jsonResponse.data.forEach(function (data) {
                        options += `<option value="${data.year_variant_id}">${data.year_variant_name}</option>`;
                    });
                    $(".pieclass").html(`  
                                    <div id="menu-wrap">
                                        <input type="checkbox" class="toggler bg-transparent" />
                                        <div class="dots">
                                            <div></div>
                                        </div>
                                        <div class="menu">
                                            <div>
                                                <ul>
                                                    <li>
                                                        <select name="piefYDropdown" id="piefYDropdown_${vendorId}" data-attr="${vendorId}" class="form-control piefYDropdown">
                                                            ${options}
                                                        </select>
                                                    </li>
                                                    <li><label class="mb-0" for="">OR</label></li>
                                                    <li>
                                                        <input type="month" name="monthRange" id="monthRange_${vendorId}" data-attr="${vendorId}" class="form-control monthRange" style="max-width: 100%;" />
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div> `);

                    for (elem of $(".pieChartContainer")) {
                        let dataAttrValue = vendorId
                        let id = $(`#piefYDropdown_${vendorId} option:first`).val();
                        // console.log("is value is " + id);
                        // console.log(dataAttrValue);

                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?id=${id}&vend_id=${dataAttrValue}`,
                            beforeSend: function () {
                                $(".load-wrapp").show();
                                $(".load-wrapp").css('opacity', 1);
                            },
                            success: function (result) {
                                $(".load-wrapp").hide();
                                $(".load-wrapp").css('opacity', 0);

                                let res = jQuery.parseJSON(result);
                                // let status = res['status'];

                                $(`#chartDivPayableAgeing`).show();
                                pieChart(res, "chartDivPayableAgeing", dataAttrValue);
                                // else {
                                //     $(`#noTransactionFound_${dataAttrValue}`).show();
                                // }
                            }
                        });
                    };

                    $(document).on("change", '.piefYDropdown', function () {

                        var dataAttrValue = $(this).data('attr');
                        var id = $(`#piefYDropdown_${dataAttrValue}`).val();
                        // console.log(dataAttrValue + ',' + id);
                        $.ajax({
                            type: "GET",
                            url: `<?= LOCATION_URL ?>ajaxs/ajax-vendor-chart.php?id=${id}&vend_id=${dataAttrValue}`,
                            beforeSend: function () {
                                $(".load-wrapp").show();
                                $(".load-wrapp").css('opacity', 1);
                            },
                            success: function (result) {
                                $(".load-wrapp").hide();
                                $(".load-wrapp").css('opacity', 0);

                                let res = jQuery.parseJSON(result);
                                // console.log(res);
                                pieChart(res, "chartDivPayableAgeing", dataAttrValue);
                            }
                        });
                    });

                },
            });


            function pieChart(chartData, chartTitle, custId) {
                console.log(chartData)
                console.log(chartTitle)
                console.log(custId)

                am4core.ready(function () {
                    am4core.useTheme(am4themes_animated);
                    var chart = am4core.create(`${chartTitle}`, am4charts.PieChart3D);
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


            // modal data

            $.ajax({
                type: "GET",
                url: ajaxUrl, // ajaxs/modals/vendor/ajax-manage-vendor-modal.php
                dataType: 'json',
                data: {
                    act: "modalData",
                    vendorId
                },
                beforeSend: function () {
                    // $('.item-cards').remove();
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
                        let dataObj = value.data;
                        // let dataObj = value.data.responseObj;
                        // nav head
                        $("#vendorName").html(dataObj.trade_name);
                        $("#vendorCode").html(dataObj.vendor_code);
                        $("#vendorCob").html(dataObj.constitution_of_business);
                        $("#vendorGst").html(dataObj.vendor_gstin);
                        $("#vendorPerson").html(dataObj.vendor_authorised_person_name);
                        $("#vendorPersonDesg").html(dataObj.vendor_authorised_person_designation);
                        $("#vendorPersonPhone").html(dataObj.vendor_authorised_person_phone);
                        $("#vendorPersonMail").html(dataObj.vendor_authorised_person_email);
                        // console.log(created_at , updated_at , created_by , updated_by);

                    }
                },
                complete: function () {
                    // $("#globalModalLoader").remove();

                }
            });




            // classic view         
            // $.ajax({
            //     type: "GET",
            //     url: "ajaxs/modals/so/ajax-manage-so-modal.php",
            //     data: {
            //         act: "classicView",
            //         so_id
            //     },
            //     success: function (response) {
            //         $(".classic-view").html(response);
            //     },
            //     error: function (error) {
            //         console.log(error);
            //     }
            // });

            //Overview Tab 

            $.ajax({
                type: "GET",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    act: "basicDetails",
                    vendorId
                },
                beforeSend: function () {
                    $('#collapseBasic').empty();
                },
                success: function (value) {
                    if (value.status === 'success') {
                        let details = value.data[0]; // Directly access the object
                        // console.log(details);

                        let output = `
                                    <div class="accordion-body">
                                        <div class="details">
                                            <label><ion-icon name="arrow-forward-outline"></ion-icon> Vendor Code</label>
                                            <p>: ${details.vendor_code ?? "-"}</p>
                                        </div>
                                        <div class="details">
                                            <label><ion-icon name="arrow-forward-outline"></ion-icon> GSTIN</label>
                                            <p>: ${details.vendor_gstin ?? "-"}</p>
                                        </div>
                                        <div class="details">
                                            <label><ion-icon name="arrow-forward-outline"></ion-icon> PAN</label>
                                            <p>: ${details.vendor_pan ?? "-"}</p>
                                        </div>
                                        <div class="details">
                                            <label><ion-icon name="arrow-forward-outline"></ion-icon> Trade Name</label>
                                            <p>: ${details.trade_name}</p>
                                        </div>
                                        <div class="details">
                                            <label><ion-icon name="arrow-forward-outline"></ion-icon> COB</label>
                                            <p>: ${details.constitution_of_business ?? "-"}</p>
                                        </div>
                                    </div>
                                `;

                        $("#collapseBasic").html(output); // Render the object details
                    } else {
                        // Handle case when no data is found
                        $("#collapseBasic").html(`
                                    <div class="accordion-body">
                                        <div class="details">Data not found</div>
                                    </div>
                                    `);
                    }
                },
            });


            $.ajax({
                type: "GET",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    act: "address",
                    vendorId
                },
                beforeSend: function () {
                    $('#collapseAddress').empty();
                },
                success: function (value) {
                    // console.log(value);
                    if (value.status == 'success') {
                        let address = value.data[0];
                        // let output = '';
                            output = `
                                   <div class="accordion-body">
                                    <div class="details">
                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>State</label>
                                        <p>: ${address.vendor_business_state ?? "-"}</p>
                                    </div>
                                    <div class="details">
                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>City</label>
                                        <p>: ${address.vendor_business_city ?? "-"}</p>
                                    </div>
                                    <div class="details">
                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>District</label>
                                        <p>: ${address.vendor_business_district ?? "-"}</p>
                                    </div>
                                    <div class="details">
                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Location</label>
                                        <p>: ${address.vendor_business_location ?? "-"}</p>
                                    </div>
                                    <div class="details">
                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Building No</label>
                                        <p>: ${address.vendor_business_building_no ?? "-"}</p>
                                    </div>
                                    <div class="details">
                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Flat No</label>
                                        <p>: ${address.vendor_business_flat_no ?? "-"}</p>
                                    </div>
                                    <div class="details">
                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Street Name</label>
                                        <p>: ${address.vendor_business_street_name ?? "-"}</p>
                                    </div>
                                    
                                </div>

                                            `;
                            // $('#basicDetails').append(output.join(''));
                            $("#collapseAddress").html(output)
                    }
                    else {
                        $("#collapseAddress").html('<div class="accordion-body"><div class="details">Data not found</div><div>')
                    }
                }
            });


            $.ajax({
                type: "GET",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    act: "accounting",
                    vendorId
                },
                beforeSend: function () {
                    $('#collapseAccounting').empty();
                },
                success: function (value) {
                    // console.log(value);
                    if (value.status == 'success') {
                        let account = value.data[0];
                        // let output = '';
                            output = `
                                    <div class="accordion-body">
                                        <div class="details">
                                            <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Credit Period </label>
                                            <p>: ${account.credit_period ?? "-"}(in days)</p>
                                        </div>
                                        <div class="details">
                                            <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Bank Name</label>
                                            <p>: ${account.vendor_bank_name ?? "-"}</p>
                                        </div>
                                        <div class="details">
                                            <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Account Number</label>
                                            <p>: ${account.vendor_bank_account_no ?? "-"}</p>
                                        </div>
                                        <div class="details">
                                            <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>IFSC</label>
                                            <p>: ${account.vendor_bank_ifsc ?? "-"}</p>
                                        </div>
                                        <div class="details">
                                            <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Branch Name</label>
                                            <p>: ${account.vendor_bank_branch ?? "-"}</p>
                                        </div>
                                        <div class="details">
                                            <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Address</label>
                                            <p>: ${account.vendor_bank_address ?? "-"}</p>
                                        </div>
                                        
                                    </div>

                                            `;
                            // $('#basicDetails').append(output.join(''));
                            $("#collapseAccounting").html(output)
                    }
                    else {
                        $("#collapseAccounting").html('<div class="accordion-body"><div class="details">Data not found</div><div>')
                    }
                }
            });




            $.ajax({
                type: "GET",
                url: ajaxUrl,  // The PHP script that fetches business place data
                dataType: "json",
                data: {
                    act: "other_business_places",   // Action to identify the request
                    vendorId              // Pass the vendorId to fetch the specific data
                },
                beforeSend: function () {
                    $('#collapseOtherAddress').empty();  // Clear any existing content
                },
                success: function (value) {
                    if (value.status == 'success') {
                        let output = '';
                        value.data.forEach(function (businessPlace) {
                            output += `
                                                <div class="accordion-body">
                                                    <div class="details">
                                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>State</label>
                                                        <p>: ${businessPlace.vendor_business_state ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>City</label>
                                                        <p>: ${businessPlace.vendor_business_city ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>District</label>
                                                        <p>: ${businessPlace.vendor_business_district ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Location</label>
                                                        <p>: ${businessPlace.vendor_business_location ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Building No</label>
                                                        <p>: ${businessPlace.vendor_business_building_no ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Flat No</label>
                                                        <p>: ${businessPlace.vendor_business_flat_no ?? "-"}</p>
                                                    </div>
                                                    <div class="details">
                                                        <label for=""><ion-icon name="arrow-forward-outline"></ion-icon>Street Name</label>
                                                        <p>: ${businessPlace.vendor_business_street_name ?? "-"}</p>
                                                    </div>
                                                </div>`;
                        });
                        $("#collapseOtherAddress").html(output);
                    } else {
                        $("#collapseOtherAddress").html('<div class="accordion-body"><div class="details">Data not found</div><div>');
                    }
                },

            });

            //Overview Tab end here

            // quotation ajax for transactional tab

            let pageInnerQuotations = 1;
            let debounceFlagInnerQuotations = true;


            innerQuotationTable()



            $(".innerQuotations_wrapper").on('scroll', function () {
                // alert("quotations")
                const element = $(".innerQuotations_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerQuotations) {
                    innerQuotationTable();
                }
            });

            function innerQuotationTable() {
                // set limit scroll load
                let loadLimit = 10;
                if (debounceFlagInnerQuotations) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: "json",
                        data: {
                            act: "vendQuot",
                            limit: loadLimit,
                            page: pageInnerQuotations,
                            vendorId
                        },
                        beforeSend: function () {
                            debounceFlagInnerQuotations = false;
                        },
                        success: function (value) {
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        tableQuotation.row.add([
                                            `${val.rfq_code ?? "-"}`,
                                            `${val.item_name ?? "-"} (${val.item_code ?? "-"})`,
                                            `${val.moq ?? "-"}`,
                                            `${decimalAmount(val.price) ?? "-"}`,
                                            `${decimalAmount(val.discount) ?? "-"}`,
                                            `${decimalAmount(val.total) ?? "-"}`,
                                            `${val.gst ?? "-"}`,
                                            `${val.lead_time ?? "-"}`
                                        ]).draw(false);
                                    });

                                    pageInnerQuotations++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerQuotations = true;
                                    }

                                } else if (value.status == "error") {
                                    // alert('caught here')
                                    $('#vendQuot').empty();
                                    let obj = `<tr><td colspan="8"><p class="text-center">No Quotations Found</p> </td></tr>  `;
                                    debounceFlagInnerQuotations = false;
                                    $('#vendQuot').append(obj);
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


            // purchase order ajax for transactional tab


            let pageInnerPurchaseOrder = 1;
            let debounceFlagInnerPurchaseOrder = true;


            innerPurchaseOrderTable()

            $(".innerPurchaseOrder_wrapper").on('scroll', function () {
                // alert("Invoices")
                const element = $(".innerPurchaseOrder_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerPurchaseOrder) {
                    innerPurchaseOrderTable();
                }
            });

            function innerPurchaseOrderTable() {
                // set limit scroll load
                let loadLimit = 10;
                if (debounceFlagInnerPurchaseOrder) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: "json",
                        data: {
                            act: "vendPurchsOrdr",
                            limit: loadLimit,
                            page: pageInnerPurchaseOrder,
                            vendorId
                        },
                        beforeSend: function () {
                            debounceFlagInnerPurchaseOrder = false;
                        },
                        success: function (value) {
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        tablePurchaseOrder.row.add([
                                            `<p class="company-name mt-1">${val.po_number ?? "-"}</p>`,
                                            `${val.ref_no ?? "-"}`,
                                            `${val.use_type ?? "-"}`,
                                            `${(val.delivery_date && !isNaN(new Date(val.delivery_date).getTime())) ? formatDate(val.delivery_date) : "-"}`,
                                            `${(val.po_date && !isNaN(new Date(val.po_date).getTime()))? formatDate(val.po_date) : "-"}`,
                                            `${decimalAmount(val.totalAmount) ?? "-"}`,
                                            `${decimalQuantity(val.totalItems) ?? "-"}`
                                        ]).draw(false);
                                    });

                                    pageInnerPurchaseOrder++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerPurchaseOrder = true;
                                    }

                                } else if (value.status == "error") {
                                    // alert('caught here')
                                    $('#vendPurchsOrdr').empty();
                                    let obj = `<tr><td colspan="7"><p class="text-center">No Purchase Order Found</p> </td></tr>  `;
                                    debounceFlagInnerPurchaseOrder = false;
                                    $('#vendPurchsOrdr').append(obj);
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



            let pageInnerBills = 1;
            let debounceFlagInnerBills = true;

            // Bills details ajax for transactional tab

            innerBillsTable()


            $(".innerBillsTable_wrapper").on('scroll', function () {
                // alert("Invoices")
                const element = $(".innerBillsTable_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerBills) {
                    innerBillsTable();
                }
            });

            function innerBillsTable() {
                // set limit scroll load
                let loadLimit = 5;
                if (debounceFlagInnerBills) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: "json",
                        data: {
                            act: "vendorBills",
                            limit: loadLimit,
                            page: pageInnerBills,
                            vendorId
                        },
                        beforeSend: function () {
                            debounceFlagInnerBills = false;
                        },
                        success: function (value) {
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        tableBills.row.add([
                                            `<p class="company-name mt-1">${val.vendorDocumentNo ?? "-"}</p>`,
                                            `${(val.vendorDocumentDate) ?? "-"}`,
                                            `${val.grnCode ?? "-"}`,
                                            `${(val.grnDate) ?? "-"}`,
                                            `${val.grnPoNumber ?? "-"}`,
                                            `${(val.poDate) ?? "-"}`,
                                            `${val.grnIvCode ?? "-"}`,
                                            `${(val.postingDate) ?? "-"}`,
                                            `${(val.dueDate) ?? "-"}`,
                                            `${decimalAmount(val.grnSubTotal) ?? "-"}`,
                                            `${decimalAmount(val.taxTotal) ?? "-"}`, // Sum of CGST, SGST, IGST
                                            `${val.grnTotalTds ?? "-"}`,
                                            `${decimalAmount(val.grnTotalAmount) ?? "-"}`,
                                            `${decimalAmount(val.paidAmount) ?? "-"}`,
                                            `${decimalAmount(val.dueAmt) ?? "-"}`,
                                            `${val.duePercentage ?? "-"}`,
                                            `<span class="text-uppercase ${val.statusClass}">${val.statusLabel ?? "-"}</span>`,
                                            `${val.grnStatus ?? "-"}`
                                        ]).draw(false);
                                    });

                                    pageInnerBills++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerBills = true;
                                    }

                                } else if (value.status == "error") {
                                    // alert('caught here')
                                    $('#vendorBills').empty();
                                    let obj = `<tr><td colspan="17"><p class="text-center">No Bills Found</p> </td></tr>  `;
                                    debounceFlagInnerBills = false;
                                    $('#vendorBills').append(obj);
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


            // Payment details ajax for transactional tab

            let pageInnerPayments = 1;
            let debounceFlagInnerPayments = true; // whether

            innerPaymentsTable()
            $(".innerPayments_wrapper").on('scroll', function () {
                // alert("Invoices")
                const element = $(".innerPayments_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerPayments) {
                    innerPaymentsTable();
                }
            });

            function innerPaymentsTable() {
                // alert("called")
                // set limit scroll load
                let loadLimit = 5;
                if (debounceFlagInnerPayments) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: "json",
                        data: {
                            act: "vendPymnts",
                            limit: loadLimit,
                            page: pageInnerPayments,
                            vendorId
                        },
                        beforeSend: function () {
                            debounceFlagInnerPayments = false;
                        },
                        success: function (value) {
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, payment) {
                                        let paymentType = payment.paymentCollectType === 'collect' ? 'Receipt' : payment.paymentCollectType;

                                        tablePayments.row.add([
                                            `<p class="company-name mt-1">${payment.transactionId ?? "-"}</p>`,
                                            `${paymentType ?? "-"}`,
                                            `${decimalAmount(payment.collect_payment) ?? "-"}`,
                                            `${(payment.postingDate && !isNaN(new Date(payment.postingDate).getTime())) ? formatDate(payment.postingDate) : "-"}`,
                                            `${payment.status ?? "-"}`
                                        ]).draw(false);
                                    });

                                    pageInnerPayments++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerPayments = true;
                                    }

                                } else if (value.status == "error") {
                                    // alert('caught here')
                                    $('#vendPymnts').empty();
                                    let obj = `<tr><td colspan="5"><p class="text-center">No Payments Found</p> </td></tr>  `;
                                    debounceFlagInnerPayments = false;
                                    $('#vendPymnts').append(obj);
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


            // Journal details ajax for transactional tab


            let pageInnerJournals = 1;
            let debounceFlagInnerJournals = true;

            innerJournalTable()

            $(".innerJournals_wrapper").on('scroll', function () {
                // alert("Invoices")
                const element = $(".innerJournals_wrapper")[0];
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;
                const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
                if (scrollPercentage >= 70 && debounceFlagInnerJournals) {
                    innerJournalTable();
                }
            });

            function innerJournalTable() {
                // set limit scroll load
                let loadLimit = 5;
                if (debounceFlagInnerJournals) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: "json",
                        data: {
                            act: "vendorJournal",
                            limit: loadLimit,
                            page: pageInnerJournals,
                            vendorId
                        },
                        beforeSend: function () {
                            debounceFlagInnerJournals = false;
                        },
                        success: function (value) {
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        tableJournal.row.add([
                                            `${val.jv_no ?? "-"}`,
                                            `${val.refarenceCode ?? "-"}`,
                                            `${val.documentNo ?? "-"}`,
                                            `${(val.documentDate && !isNaN(new Date(val.documentDate).getTime())) ? formatDate(val.documentDate) : "-"}`,
                                            `${(val.postingDate && !isNaN(new Date(val.postingDate).getTime())) ?  formatDate(val.postingDate) : "-"}`,
                                            `${val.remark ?? "-"}`
                                        ]).draw(false);
                                    });

                                    pageInnerJournals++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerJournals = true;
                                    }

                                } else if (value.status == "error") {
                                    // alert('caught here')
                                    $('#vendorJournal').empty();
                                    let obj = `<tr><td colspan="6"><p class="text-center">No Journals Found Found</p> </td></tr>  `;
                                    debounceFlagInnerJournals = false;
                                    $('#vendorJournal').append(obj);
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




            // debit note details for transactional tab


            let pageInnerDebitNote = 1;
            let debounceFlagInnerDebitNote = true;

            innerDebitNote();

            $(".innerDebitNote_wrapper").on('scroll', function () {
                // alert("Invoices")
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
                // set limit scroll load
                let loadLimit = 10;
                if (debounceFlagInnerDebitNote) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: "json",
                        data: {
                            act: "debit-note",
                            limit: loadLimit,
                            page: pageInnerDebitNote,
                            id: vendorId,
                            creditorsType: "vendor",
                        },
                        beforeSend: function () {
                            debounceFlagInnerDebitNote = false;
                        },
                        success: function (value) {
                            try {
                                if (value.status == 'success') {
                                    let responseObj = value.data;

                                    $.each(responseObj, function (index, val) {
                                        tableDebitNote.row.add([
                                            `${val.debit_note_no ?? "-"}`,
                                            `${val.party_code ?? "-"}`,
                                            `${val.party_name ?? "-"}`,
                                            `${val.invoice_code != null ? val.invoice_code : "-"}`,
                                            `${decimalAmount(val.total) ?? "-"}`,
                                            `${(val.postingDate && !isNaN(new Date(val.postingDate).getTime())) ? formatDate(val.postingDate) : "-"}`
                                        ]).draw(false);
                                    });

                                    pageInnerDebitNote++;
                                    if (value.numRows == loadLimit) {
                                        debounceFlagInnerDebitNote = true;
                                    }

                                } else if (value.status == "error") {
                                    // alert('caught here')
                                    $('#debitnotetable').empty();
                                    let obj = `<tr><td colspan="6"><p class="text-center">No debit note Found </p> </td></tr>  `;
                                    debounceFlagInnerDebitNote = false;
                                    $('#debitnotetable').append(obj);
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





            // credit note details for transactional tab

            let pageInnerCreditNote = 1;
            let debounceFlagInnerCreditNote = true;

            innerCreditNote();

            $(".innerCreditNote_wrapper").on('scroll', function () {
                // alert("Invoices")
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
                // set limit scroll load
                let loadLimit = 10;
                if (debounceFlagInnerCreditNote) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        dataType: "json",
                        data: {
                            act: "credit-note",
                            limit: loadLimit,
                            page: pageInnerCreditNote,
                            id: vendorId,
                            creditorsType: "vendor"
                        },
                        beforeSend: function () {
                            debounceFlagInnerCreditNote = false;
                        },
                        success: function (value) {
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

                                } else if (value.status == "error") {
                                    // alert('caught here')
                                    $('#creditnotetable').empty();
                                    let obj = `<tr><td colspan="6"><p class="text-center">No credit note Found </p> </td></tr>  `;
                                    debounceFlagInnerCreditNote = false;
                                    $('#creditnotetable').append(obj);
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


            // mails details ajax for transactional tab

            $.ajax({
                type: "GET",
                url: ajaxUrl,
                dataType: "json",
                data: {
                    act: "vendMails",
                    code: vendor_code
                },
                beforeSend: function () {
                    $('.inbox-blocks').empty();
                },
                success: function (value) {
                    // console.log(value);
                    if (value.status == 'success') {
                        let responseObj = value.data;
                        let output = [];
                        $.each(responseObj, function (index, val) {
                            output.push(`
                                    <a href="">
                                            <div class="mail-block">
                                                <div class="left-detail">
                                                    <p class="sender-mail">${val.toaddress}</p>
                                                </div>
                                                <div class="subject-detail">
                                                    <p>${val.mailTitle}</p>
                                                </div>
                                                <div class="right-detail">
                                                    <p class="time-date">${val.created_at}</p>
                                                </div>
                                            </div>
                                        </a>
        `);
                        });
                        $('.inbox-blocks').append(output.join(''));
                    } else {
                        let obj = `<div class="subject-detail">
                             <p>History not found</p>
                         </div>`;
                        $('.inbox-blocks').append(obj);
                    }

                },
            });




            // statement date ajax

            $("#stmntDateApply").on('click', function () {
                const from_date = $("#from_date").val();
                const to_date = $("#to_date").val();

                $.ajax({
                    url: `ajaxs/vendor/ajax-dateRange-statement.php`,
                    type: 'POST',
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        vendor_code: vendor_code
                    },
                    beforeSend: function () {
                        $('#statementSection').html('');
                    },
                    success: function (response) {
                        try {
                            const obj = JSON.parse(response); // Parse the JSON response
                            $('#statementSection').html(obj.html); // Insert the HTML content into #statementSection
                        } catch (e) {
                            console.error("Parsing error:", e);
                            // alert("An error occurred while processing the data.");
                        }
                    }
                });
            });


            // compliance details ajax for transactional tab
            $.ajax({
                url: `ajaxs/vendor/ajax-gst-review.php?gstin=${vendor_gstin}`,
                type: 'get',
                beforeSend: function () {
                    $('#nav-compliance').html(`<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`);
                },
                success: function (response) {
                    responseObj = JSON.parse(response);
                    let fy = responseObj['fy'];
                    responseData = responseObj["data"];
                    let gstinReturnsDataDivHtml = `
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
                                <tbody>`;

                    responseData["EFiledlist"].forEach(function (rowVal) {
                        if (rowVal['rtntype'] == 'GSTR1') {
                            var dateString = rowVal["ret_prd"];
                            var monthString = dateString.substr(0, 2);
                            var month = parseInt(monthString, 10);
                            var monthNames = [
                                "January", "February", "March", "April", "May", "June",
                                "July", "August", "September", "October", "November", "December"
                            ];
                            var monthName = monthNames[month - 1]; //

                            gstinReturnsDataDivHtml += `
                                                    <tr>
                                                        <td>${fy}</td>
                                                        <td>${monthName ?? "-"}</td>
                                                        <td>${rowVal["dof"] ?? "-"}</td>
                                                        <td>
                                                            <p class="${rowVal["status"] ? "gst-filed" : "gst-non-filed"}">
                                                                <ion-icon name="${rowVal["status"] ? "checkmark-outline" : "close-outline"}"></ion-icon>
                                                                ${rowVal["status"] ? "Filed" : "Not Filed"}
                                                            </p>
                                                        </td>
                                                    </tr>`;
                        }
                    });

                    gstinReturnsDataDivHtml += `</tbody></table></div>`;

                    // GSTR3B table
                    let gstinReturnsDataDivHtml3b = `
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
                                                        <tbody>`;

                    responseData["EFiledlist"].forEach(function (rowVal) {
                        if (rowVal['rtntype'] == 'GSTR3B') {
                            var dateString = rowVal["ret_prd"];
                            var monthString = dateString.substr(0, 2);
                            var month = parseInt(monthString, 10);
                            var monthNames = [
                                "January", "February", "March", "April", "May", "June",
                                "July", "August", "September", "October", "November", "December"
                            ];
                            var monthName = monthNames[month - 1]; // 

                            gstinReturnsDataDivHtml3b += `
                                                        <tr>
                                                            <td>${fy}</td>
                                                            <td>${monthName ?? "-"}</td>
                                                            <td>${rowVal["dof"] ?? "-"}</td>
                                                            <td>
                                                                <p class="${rowVal["status"] ? "gst-filed" : "gst-non-filed"}">
                                                                    <ion-icon name="${rowVal["status"] ? "checkmark-outline" : "close-outline"}"></ion-icon>
                                                                    ${rowVal["status"] ? "Filed" : "Not Filed"}
                                                                </p>
                                                            </td>
                                                        </tr>`;
                        }
                    });

                    gstinReturnsDataDivHtml3b += `</tbody></table></div>
                </div></div>`;



                    let complianceContentHtml = gstinReturnsDataDivHtml + gstinReturnsDataDivHtml3b;

                    $("#nav-compliance").html(complianceContentHtml);


                }
            });





            // reconciliation details ajax for transactional tab

            function reconciliation(from_date, to_date, attr) {
                $.ajax({
                    url: `ajaxs/vendor/ajax-reconciliation.php`,
                    type: 'POST',
                    data: {
                        from_date: from_date,
                        to_date: to_date,
                        party_code: attr

                    },
                    beforeSend: function () {

                    },
                    success: function (response) {
                        // alert(response);
                        $("#reconciliationContent").html(response);
                    }
                });
            }


            $("#date-recon-apply").click(function () {
                var vcode = vendor_code
                var from_date = $("#from-recon").val();
                var to_date = $("#to-recon").val();
                reconciliation(from_date, to_date, vcode);
            });


            // trail head details
            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    act: "audit-head-section",
                    id: vendorId
                },
                beforeSend: function () {
                    $('.audit-head-section').html('');
                },
                success: function (response) {
                    if (response.status) {
                        let data = response.data
                        $('.audit-head-section').html('<p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span>' + data.vendor_created_by + '<span class="font-bold text-normal"> on </span>' + data.vendor_created_at + '</p>' +
                            '<p class="text-xs font-italic"><span class="font-bold text-normal">Last Updated by </span>' + data.vendor_updated_by + '<span class="font-bold text-normal"> on </span>' + data.vendor_updated_at + '</p>');
                    }
                }
            });

            $.ajax({
                url: 'ajaxs/audittrail/ajax-audit-trail-vendor.php?auditTrailBodyContent', // <-- point to server-side PHP script 
                type: 'POST',
                data: {
                    ccode: vendor_code,
                    id: vendorId
                },
                beforeSend: function () {
                    // console.log(vendor_code);
                    // console.log(vendorId);
                    $(".auditTrailBodyContentVendor").empty();
                    $(".auditTrailBodyContentVendor").html('Loading...');
                },
                success: function (responseData) {
                    // console.log(responseData);
                    $(`.auditTrailBodyContentVendor`).html(responseData);
                }
            });



            // $.ajax({
            //     url: ajaxUrl, // Replace with your actual endpoint
            //     type: "GET",
            //     dataType: "json",
            //     data: {
            //         act: "auditTrailBodyContent",
            //         ccode: vendor_code,
            //         id: vendorId
            //     },
            //     beforeSend: function () {
            //         // Clear previous content or show a loader
            //         $(".auditTrailBodyContent").html('<p>Loading...</p>');
            //     },
            //     success: function (response) {
            //         if (response.status) {
            //             let auditData = response.data;
            //             let timelineHtml = `<ol class="timeline">`;

            //             // Iterate over the audit trail data and build HTML
            //             auditData.forEach((item, index) => {
            //                 timelineHtml += `
            //                 <li class="timeline-item mb-0 bg-transparent auditTrailBodyContentLineVendor"
            //                     type="button" 
            //                     data-toggle="modal" 
            //                     data-id="${item.id}" 
            //                     data-ccode="${item.document_number}" 
            //                     data-target="#innerModal">
            //                     <span class="timeline-item-icon filled-icon">
            //                         <img src="${item.icon_url}" width="25" height="25">
            //                     </span>
            //                     <span class="step-count">${auditData.length - index}</span>
            //                     <div class="new-comment font-bold">
            //                         <p>${item.created_by}</p>
            //                         <ul class="ml-3 pl-0">
            //                             <li style="list-style: disc; color: #a7a7a7;">
            //                                 ${item.created_at_formatted}
            //                             </li>
            //                         </ul>
            //                     </div>
            //                 </li>
            //                 <p class="mt-0 mb-5 ml-5">${item.action_title}</p>`;
            //             });

            //             timelineHtml += `</ol>`;
            //             $(".auditTrailBodyContent").html(timelineHtml); // Update container
            //         } else {
            //             // Show warning or no data message
            //             $(".auditTrailBodyContent").html(` <ol class="timeline">
            //                                                 <li class="timeline-item mb-0 bg-transparent">
            //                                                     <div class="new-comment font-bold">
            //                                                         <p>History not found </p>
            //                                                     </div>
            //                                                 </li>
            //                                             </ol>`);
            //         }
            //     }
            // });

            // $(document).on('click', '.auditTrailBodyContentLine' , function () {



            //     var doc_code = $(this).data('ccode');
            //     var doc_id = $(this).data('id');


            //     $.ajax({
            //         url: ajaxUrl, // Replace with your actual endpoint
            //         type: "GET",
            //         dataType: "json",
            //         data: {
            //             act: "auditTrailBodyContentLine",
            //             doc_code: doc_code,
            //             doc_id: doc_id
            //         },
            //         beforeSend: function () {
            //             // Clear previous content or show a loader
            //             // $(".auditTrailBodyContentLine").html('<p>Loading...</p>');
            //             console.log("beforesend")
            //         },
            //         success: function (response) {
            //             if (response.status) {
            //                 let currentData = response.currentData
            //                 let previousData = response.previousData;
            //                 let changes = response.changes;
            //                 console.log(currentData);
            //                 console.log(previousData);
            //                 console.log(changes);
            //                 // $(".auditTrailBodyContent").html(timelineHtml); 
            //             } else {
            //                 // Show warning or no data message
            //                 $(".auditTrailBodyContentLine").html(` <ol class="timeline">
            //                                                 <li class="timeline-item mb-0 bg-transparent">
            //                                                     <div class="new-comment font-bold">
            //                                                         <p>History not found </p>
            //                                                     </div>
            //                                                 </li>
            //                                             </ol>`);
            //             }
            //         }
            //     });
            // })

            // ajax trail body content line 

            $(document).on("click", ".auditTrailBodyContentLineVendor", function () {
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
                var ccode = $(this).data('ccode');
                var id = $(this).data('id');
                // alert(ccode);
                $.ajax({
                    url: 'ajaxs/audittrail/ajax-audit-trail-vendor.php?auditTrailBodyContentLine', // <-- point to server-side PHP script 
                    type: 'POST',
                    data: {
                        ccode,
                        id
                    },
                    beforeSend: function () {
                        // $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                        // $(".Ckecked_loder").toggleClass("disabled");
                    },
                    success: function (responseData) {
                        $(`.auditTrailBodyContentLineDiv`).html(responseData);
                    }
                });
            });


        });



    })
</script>


<!-- // close SO -->
<script>
    $(document).ready(function () {
        $(document).on("click", ".closeSoBtn", function () {
            let soId = ($(this).attr("id")).split("_")[1];
            let soNumber = ($(this).attr("id")).split("_")[2];

            // alert("soId" + soId + "sonumbver" + soNumber);

            if (!confirm(`Are you sure to close SO #${soNumber}?`)) {
                return false;
            }

            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-close.php`,
                data: {
                    act: "closeSo",
                    soId
                },
                success: function (response) {
                    // console.log('response => ', response);
                    let data = JSON.parse(response);

                    // js swal alert
                    let timerInterval;
                    Swal.fire({
                        icon: data.status,
                        title: `SO #${soNumber} closed successfully!`,
                        html: "Close in <b></b> seconds.",
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                            const timer = Swal.getPopup().querySelector("b");
                            timerInterval = setInterval(() => {
                                timer.textContent = `${(Swal.getTimerLeft() / 1000).toFixed(0)}`;
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                            location.reload();
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            // console.log("I was closed by the timer");
                        }
                    });
                    $(`#closeSoBtn_${soId}_${soNumber}`).hide();
                    $(`#approvalStatus_${soId}`).html('<div class="status-secondary">CLOSED</div>');
                }
            });
        })
    });
</script>

<!-- // so delete script -->

<script>
    $(document).on('click', '.deleteSoBtn', function () {
        var soNum = $(this).data('id');
        if (!confirm(`Are you sure to delete SO #${soNum}?`)) {
            return false;
        }
        $.ajax({
            type: "GET",
            url: `ajaxs/so/ajax-delete.php`,
            data: {
                act: "deleteSoall",
                soNum
            },
            success: function (response) {
                console.log('response => ', response);
                let data = JSON.parse(response);

                // js swal alert
                let timerInterval;
                Swal.fire({
                    icon: data.status,
                    title: `SO #${soNum} deleted successfully!`,
                    html: "Close in <b></b> seconds.",
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading();
                        const timer = Swal.getPopup().querySelector("b");
                        timerInterval = setInterval(() => {
                            timer.textContent = `${(Swal.getTimerLeft() / 1000).toFixed(0)}`;
                        }, 100);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                        location.reload();

                    }
                })
            }
        });
    });
</script>

<!-- script for classic view -->
<script>
    // so edit btn---->
    $(document).on('click', '.editSobtn', function () {
        let so_id = $(this).data('id');
        let code = $(this).data('code');
        let url = `direct-create-invoice.php?edit_so=${btoa(so_id)}`;
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

    $(document).ready(function () {
        $(document).on("click", ".approvalTab", function () {
            let soId = ($(this).attr("id")).split("_")[1];

            if (confirm("Are you sure?")) {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-items-list.php`,
                    data: {
                        act: "approvalTab",
                        soId
                    },
                    beforeSend: function () {
                        $(".approvalTab").html(`<option value="">Processing...</option>`);
                    },
                    success: function (response) {
                        //console.log(response);
                        if (response === 'success') {
                            window.location.href = "";
                        } else {
                            $(".approvalTab").html(response);
                        }
                    }
                });
            }
        });

    });


    //so reject

    $(document).ready(function () {
        $(document).on("click", ".rejectTab", function () {
            let soId = ($(this).attr("id")).split("_")[1];

            if (confirm("Are you sure?")) {
                $.ajax({
                    type: "GET",
                    url: `ajaxs/so/ajax-items-list.php`,
                    data: {
                        act: "rejectTab",
                        soId
                    },
                    beforeSend: function () {
                        $(".rejectTab").html(`<option value="">Processing...</option>`);
                    },
                    success: function (response) {
                        //console.log(response);
                        if (response === 'success') {
                            window.location.href = "";
                        } else {
                            $(".rejectTab").html(response);
                        }
                    }
                });
            }
        });

    });
 //  edit btn---->
 $(document).on('click', '.editbtn', function() {
        let id = $(this).data('id');
        let code = $(this).data('code');
        let url = `vendorAction.php?edit=${btoa(id)}`;
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `Are you sure to Edit this Vendor ${code} ?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Edit'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
</script>