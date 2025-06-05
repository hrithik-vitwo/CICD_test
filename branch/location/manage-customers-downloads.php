<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");

require_once("../common/sidebar.php");
require_once("../common/pagination.php");
// administratorLocationAuth();



$pageName =  basename($_SERVER['PHP_SELF'], '.php');

if (!isset($_COOKIE["cookieManageCustomerDownload"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieManageCustomerDownload", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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
        'name' => 'Sl. No.',
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
        'name' => 'Trade Name',
        'slag' => 'trade_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Legal Name',
        'slag' => 'legal_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Constitution of Business',
        'slag' => 'constitution_of_business',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Opening Balance',
        'slag' => 'customer_opening_balance',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'GSTIN',
        'slag' => 'customer_gstin',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'PAN',
        'slag' => 'customer_pan',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],    
    [
        'name' => 'Auth Person Name',
        'slag' => 'customer_authorised_person_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Email',
        'slag' => 'customer_authorised_person_email',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Alt Email',
        'slag' => 'customer_authorised_alt_email',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Phone',
        'slag' => 'customer_authorised_person_phone',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Alt Phone',
        'slag' => 'customer_authorised_alt_phone',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Discount Group',
        'slag' => 'disGroup.customer_discount_group',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Mrp Group',
        'slag' => 'mrpGroup.customer_mrp_group',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Currency Name',
        'slag' => 'customer_currency',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Visibility',
        'slag' => 'customer_visible_to_all',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Credit Period',
        'slag' => 'customer_credit_period',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Customer Primary flag',
        'slag' => 'customer_address_primary_flag',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Building No',
        'slag' => 'customer_address_building_no',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address City',
        'slag' => 'customer_address_city',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address Country',
        'slag' => 'customer_address_country',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address District',
        'slag' => 'customer_address_district',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address Flat No',
        'slag' => 'customer_address_flat_no',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address Location',
        'slag' => 'customer_address_location',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address Pin Code',
        'slag' => 'customer_address_pin_code',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address Recipients Name',
        'slag' => 'customer_address_recipient_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address State Code',
        'slag' => 'customer_address_state_code',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address State',
        'slag' => 'customer_address_state',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer Address Street Name',
        'slag' => 'customer_address_street_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],  
    [
        'name' => 'Mail Verification Status',
        'slag' => 'isMailValid',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'customer_status',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]

];


?>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

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
                                            <a href="customer-actions.php?create" class="btn btn-create" type="button">
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
                                                                            if ($columnIndex === 0 ||$columnIndex===5) {
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
                                                                    <p class="info-detail amount"><ion-icon name="business-outline"></ion-icon><span id="custName"></span></p>
                                                                    <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="custCode"></span></p>
                                                                    <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="custCob"></span></p>
                                                                    <p class="info-detail ref-number"><ion-icon name="information-outline"></ion-icon><span id="custGst"></span></p>
                                                                </div>
                                                                <div class="right">
                                                                    <p class="info-detail name"><ion-icon name="person-outline"></ion-icon><span id="custPerson"></span></p>
                                                                    <p class="info-detail qty"><ion-icon name="document-outline"></ion-icon><span id="custPersonDesg"></span></p>
                                                                    <p class="info-detail qty"><ion-icon name="call-outline"></ion-icon><span id="custPersonPhone"></span></p>
                                                                    <p class="info-detail qty"><ion-icon name="mail-outline"></ion-icon><span id="custPersonMail"></span></p>
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
                                                                                <button class="nav-link" id="pills-debitnotesinner-tab" data-bs-toggle="pill" data-bs-target="#pills-debitnotesinner" type="button" role="tab" aria-controls="pills-debitnotesinner" aria-selected="false"><ion-icon name="document-text-outline"></ion-icon>Debit Notes</button>
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
                                                                                        <tbody id="custTransInv">
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade" id="pills-collectioninner" role="tabpanel" aria-labelledby="pills-collectioninner-tab">
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Collections</h4>
                                                                                        <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon></button>
                                                                                    </div>
                                                                                    <table class="exportTable">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Collection Advice</th>
                                                                                                <th>Transaction Id</th>
                                                                                                <th>Collection Amount</th>
                                                                                                <th>Collection Type</th>
                                                                                                <th>Date</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody id="custTransCollection"></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade" id="pills-estimatesinner" role="tabpanel" aria-labelledby="pills-estimatesinner-tab">
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Estimates</h4>
                                                                                        <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create Quotation</button>
                                                                                    </div>
                                                                                    <table class="exportTable">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Quotation Number</th>
                                                                                                <th>Total Items</th>
                                                                                                <th>Total Amount</th>
                                                                                                <th>Goods Type</th>
                                                                                                <th>Posting Date</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody id="custTransEstimate"></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade" id="pills-salesorderinner" role="tabpanel" aria-labelledby="pills-salesorderinner-tab">
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Sales Order</h4>
                                                                                        <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create So</button>
                                                                                    </div>
                                                                                    <table class="exportTable">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>SO Number</th>
                                                                                                <th>Customer PO</th>
                                                                                                <th>Delivery Date</th>
                                                                                                <th>Total Items </th>
                                                                                                <th>Status</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <thead id="custTransSo"></thead>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade" id="pills-journalinner" role="tabpanel" aria-labelledby="pills-journalinner-tab">
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Journals</h4>
                                                                                        <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                    </div>
                                                                                    <table class="exportTable">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Journal Number</th>
                                                                                                <th>Reference Code</th>
                                                                                                <th>Document Number</th>
                                                                                                <th>Document Date</th>
                                                                                                <th>Posting Date</th>
                                                                                                <th>Narration</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody id="custTransJournal"></tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade" id="pills-debitnotesinner" role="tabpanel" aria-labelledby="pills-debitnotesinner-tab">
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Debit Notes</h4>
                                                                                        <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                    </div>
                                                                                    <table class="exportTable">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Debit Note Number</th>
                                                                                                <th>Party Code</th>
                                                                                                <th>Party Name</th>
                                                                                                <th>Invoice Number</th>
                                                                                                <th>Total</th>
                                                                                                <th>Posting Date</th>
                                                                                            </tr>
                                                                                        </thead>



                                                                                        <tbody id="debitnotetable">
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                            <div class="tab-pane fade" id="pills-creditnotesinner" role="tabpanel" aria-labelledby="pills-creditnotesinner-tab">
                                                                                <div class="list-block">
                                                                                    <div class="head">
                                                                                        <h4>Credit Notes</h4>
                                                                                        <button class="btn btn-primary"><ion-icon name="add-outline"></ion-icon>Create invoice</button>
                                                                                    </div>
                                                                                    <table class="exportTable">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Credit Note Number</th>
                                                                                                <th>Party Code</th>
                                                                                                <th>Party Name</th>
                                                                                                <th>Invoice Number</th>
                                                                                                <th>Amount</th>
                                                                                                <th>Posting Date</th>
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
    $(document).on("click", "#serach_reset", function(e) {
        e.preventDefault();
        $("#myForm")[0].reset();
        $("#serach_submit").click();
    });
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
    let data,sql;

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
            var checkboxSettings = Cookies.get('cookieManageCustomerDownload');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-customer2.php",
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
                    console.log(response);
                    csvContent = response.csvContent;
                    csvContentBypagination = response.csvContentBypagination;

                    if (response.status) {
                        var responseObj = response.data;
                        data=responseObj;
                        sql=response.sql;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);


                        $.each(responseObj, function(index, value) {
                            let status = ``;
                            let mailStatus='';
                            if(value.isMailValid=='Verified'){
                                mailStatus='<p class="status-bg status-open">Verified</p>';
                            }else{
                                mailStatus='<p class="status-bg status-closed">Not Verified</p>';
                            }
                            if (value.customer_status == "active") {
                                status = `<p class='status-bg status-approved'>Active</p>`;
                            } else if (value.customer_status == "inactive") {
                                status = `<p class='status-bg status-closed'>Inactive</p>`;
                            } else if (value.customer_status == "draft") {
                                status = `<p class='status-bg status-pending'>Draft</p>`;
                            }

                            let actions=`   <li>
                                              <button class="soModal" data-id="${value.customerId}" data-code="${value.customer_code}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                            </li>  `;

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#"  data-id="${value.customerId}" data-code="${value.customer_code}">${value.customer_code}</a>`,
                                `<p class="pre-normal">${value.trade_name}</p>`,
                                `<p class="pre-normal">${value.legal_name}</p>`,
                                value.constitution_of_business,
                                value.customer_opening_balance,
                                value.customer_gstin,
                                value.customer_pan,
                                value.customer_authorised_person_name,
                                value.customer_authorised_person_email,
                                value.customer_authorised_alt_email,
                                value.customer_authorised_person_phone,
                                value.customer_authorised_alt_phone,
                                value["disGroup.customer_discount_group"],
                                value["mrpGroup.customer_mrp_group"],
                                value.customer_currency,
                                value.customer_visible_to_all,
                                value.customer_credit_period,
                                value.customer_address_primary_flag,
                                value.customer_address_building_no,
                                value.customer_address_city,
                                value.customer_address_country,
                                value.customer_address_district,
                                value.customer_address_flat_no,
                                value.customer_address_location,
                                value.customer_address_pin_code,
                                value.customer_address_recipient_name,
                                value.customer_address_state_code,
                                value.customer_address_state,
                                `<p class="pre-normal">${value.customer_address_street_name}</p>`,
                                mailStatus,
                                status,
                                `<div class="dropout">
                                   <button class="more">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                   </button>
                                   <ul>
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
                error: function(error) {
                    console.log(error);
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
                    sql_data_checkbox: Cookies.get('cookieManageCustomerDownload')
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
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit,columnMapping = columnMapping);
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
                $("#myForm")[0].reset();
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
                url: "ajaxs/ajax-manage-customer2.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    sql: sql,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieManageCustomerDownload'),
                    formDatas:formInputs
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
            // console.log(settingsCheckbox);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'manageCustomerDownload',
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
    $(document).on("click", ".soModal", function() {
        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        $(".classic-view").html('');
        let custId = $(this).data('id');
        let code = $(this).data('code');
        $('.auditTrail').attr("data-ccode", code);
        let ajaxUrl = "ajaxs/modals/customer/ajax-manage-customer-modal.php";

        // Transactional start        
        $.ajax({
            type: "GET",
            url: ajaxUrl,
            dataType: 'json',
            data: {
                act: "modalData",
                custId
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
                // $('#viewGlobalModal .modal-body').append(loader);
            },
            success: function(value) {
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
                }
                // $("#globalModalLoader").remove();
            },
            complete: function() {
                // $("#globalModalLoader").remove();
            },
            error: function(error) {
                console.log(error);
            }
        });

        $.ajax({
            type: "GET",
            url: ajaxUrl,
            data: {
                act: "custTransInv",
                custId
            },
            beforeSend: function() {
                $("#custTransInv").empty();
            },
            success: function(value) {
                $("#custTransInv").append(value);
            },
        });

        $.ajax({
            type: "GET",
            url: ajaxUrl,
            dataType: "json",
            data: {
                act: "custTransCollection",
                custId
            },
            beforeSend: function() {
            },
            success: function(value) {
                // console.log(value);
                if (value.status == 'success') {
                    let responseObj = value.data;
                    let output = [];
                    $.each(responseObj, function(index, val) {
                        let paymentType = (val.payment_type === 'pay') ? 'against invoice' : val.payment_type;
                        output.push(`
                            <tr>
                                <td>${val.payment_advice}</td>
                                <td>${val.transactionId}</td>
                                <td>${decimalAmount(val.payment_amt)}</td>
                                <td>${paymentType}</td>
                                <td>${formatDate(val.created_at)}</td>
                            </tr>                        
                        `);
                    });
                    $('#custTransCollection').append(output.join(''));
                } else {
                    let obj = `<tr><td colspan="5"><p class="text-center">No Collection Found</p> </td></tr>  `;
                    $('#custTransCollection').append(obj);
                }
            },
        });

        $.ajax({
            type: "GET",
            url: ajaxUrl,
            dataType: "json",
            data: {
                act: "custTransEstimate",
                custId
            },
            beforeSend: function() {},
            success: function(value) {
                // console.log(value);
                if (value.status == 'success') {
                    let responseObj = value.data;
                    let output = [];
                    $.each(responseObj, function(index, val) {
                        output.push(`
                            <tr>
                                <td>${val.quotation_no}</td>
                                <td>${val.totalItems}</td>
                                <td>${decimalAmount(val.totalAmount)}</td>
                                <td>${val.goodsType}</td>
                                <td>${formatDate(val.posting_date)}</td>
                            </tr>                        
                        `);
                    });
                    $('#custTransEstimate').append(output.join(''));
                } else {
                    let obj = `<tr><td colspan="5"><p class="text-center">No Quotation Found </p></td></tr>  `;
                    $('#custTransEstimate').append(obj);
                }
            },
        });

        $.ajax({
            type: "GET",
            url: ajaxUrl,
            dataType: "json",
            data: {
                act: "custTransSo",
                custId
            },
            beforeSend: function() {},
            success: function(value) {
                // console.log(value);
                if (value.status == 'success') {
                    let responseObj = value.data;
                    let output = [];
                    $.each(responseObj, function(index, val) {
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
                        output.push(`
                            <tr>
                                <td>${val.so_number}</td>
                                <td>${val.customer_po_no}</td>
                                <td>${formatDate(val.delivery_date)}</td>
                                <td>${val.totalItems}</td>
                                <td>${status}</td>
                            </tr>                        
                        `);
                    });
                    $('#custTransSo').append(output.join(''));
                } else {
                    let obj = `<tr><td colspan="5"><p class="text-center">No Sales Order Found </p></td></tr> `;
                    $('#custTransSo').append(obj);
                }
            },
        });

        $.ajax({
            type: "GET",
            url: ajaxUrl,
            dataType: "json",
            data: {
                act: "custTransJournal",
                code
            },
            beforeSend: function() {
                $('#custTransJournal').empty();
            },
            success: function(value) {
                // console.log(value);
                if (value.status == 'success') {
                    let responseObj = value.data;
                    let output = [];
                    $.each(responseObj, function(index, val) {

                        output.push(`
                            <tr>
                                <td>${val.jv_no}</td>
                                <td>${val.refarenceCode}</td>
                                <td>${val.documentNo}</td>
                                <td>${formatDate(val.documentDate)}</td>
                                <td>${formatDate(val.postingDate)}</td>
                                <td>${trimString(val.remark,20)}</td>
                            </tr>                        
                        `);
                    });
                    $('#custTransJournal').append(output.join(''));
                } else {
                    let obj = `<tr><td colspan="5"><p class="text-center">No Journal Found </p></td></tr> `;
                    $('#custTransJournal').append(obj);
                }
            },
        });

        $.ajax({
            type: "GET",
            url: `ajaxs/credit-note/ajax-customer-vendor-creditnote.php`,
            data: {
                act: "credit-note",
                id: custId,
                creditorsType: "customer"
            },
            beforeSend: function() {
                $("#creditnotetable").empty();
            },
            success: function(res) {
                $('#creditnotetable').append(res);
            }
        });

        $.ajax({
            type: "GET",
            url: `ajaxs/debit-note/ajax-customer-vendor-debitnote.php`,
            data: {
                act: "debit-note",
                id: custId,
                creditorsType: "customer"
            },
            beforeSend: function() {
                $("#debitnotetable").empty();
            },
            success: function(res) {
                $('#debitnotetable').append(res);
            }
        });

        // Transactional End

    });
    $(document).on('click', '.editCustomer', function() {
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
</script>