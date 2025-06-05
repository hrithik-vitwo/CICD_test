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
require_once("../../app/v1/functions/branch/func-goods-controller.php");
$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;
if (!isset($_COOKIE["cookiesPayroll"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiesPayroll", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    } else {
        for ($i = 0; $i < 5; $i++) {
            $isChecked = ($i < 5) ? 'checked' : '';
        }
    }
}
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
        'name' => 'Document No',
        'slag' => 'payroll_code',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Month and Year',
        'slag' => 'month_year',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Gross',
        'slag' => 'sum_gross',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'PF Employee',
        'slag' => 'sum_pf_employee',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'PF Employeer',
        'slag' => 'sum_pf_employeer',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'PF Admin',
        'slag' => 'sum_pf_admin',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'ESI Employee',
        'slag' => 'sum_esi_employee',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'ESI Employeer',
        'slag' => 'sum_esi_employeer',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'P-Tax',
        'slag' => 'sum_ptax',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'TDS',
        'slag' => 'sum_tds',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Status',
        'slag' => 'status',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Post',
        'slag' => 'post',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]
];

?>



<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-sales-orders is-stock-new vitwo-alpha-global">
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <?php
            // console($_COOKIE);

            ?>

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
                                                <h3 class="card-title mb-0"> Manage Payroll </h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <?php require_once("components/fa/payroll-tabs.php"); ?>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()"><i class="fa fa-expand fa-2x"></i></button>
                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>

                            <div class="card card-tabs mb-0" style="border-radius: 20px;">
                                <div class="card-body">
                                    <div class="tab-content" id="custom-tabs-two-tabContent">
                                        <div class="tab-pane dataTableTemplate dataTable_stock fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
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
                                                        <button class="ion-paginationlistPayroll">
                                                            <ion-icon name="list-outline" class="ion-paginationlistPayroll md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistPayroll">
                                                            <ion-icon name="list-outline" class="ion-fulllistPayroll md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <!-- <a href="manage-rack-actions.php?create" class="btn btn-create" type="button">
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
                                    <!-- right modal start here  -->
                                    <div class="modal fade right global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                            <!--Content-->
                                            <div class="modal-content">
                                                <!--Header-->
                                                <div class="modal-header">
                                                    <div class="top-details">
                                                        <div class="left">
                                                            <p class="info-detail amount" id="amount">
                                                                <ion-icon name="wallet-outline"></ion-icon>

                                                                <span class="rackName"> </span>
                                                            </p>
                                                            <span class="amount-in-words" id="amount-words"></span>
                                                            <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="storagelocationName"> </span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--Body-->
                                                <div class="modal-body">
                                                    <nav>
                                                        <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                            <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                                                            <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                        </div>
                                                    </nav>
                                                    <div class="tab-content global-tab-content" id="nav-tabContent">

                                                        <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                            <div class="d-flex nav-overview-tabs">

                                                            </div>

                                                            <div class="row">
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                    <div class="items-table">
                                                                        <h4>Rack Details</h4>
                                                                        <div class="customer-details">
                                                                            <div class="details warehouse-address">
                                                                                <label for="">Rack Description</label>
                                                                                <p id="rackDescription"></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <ul class="pl-0 ml-0">
                                                                        <li>
                                                                            <details open>
                                                                                <summary><span class="font-weight-bold">Warehouse : </span> <span id="warehouse"></span>
                                                                                </summary>
                                                                                <ul>
                                                                                    <li>
                                                                                        <details>
                                                                                            <summary><span class="font-weight-bold">S Location :</span> <span class="storagelocationName"></span>
                                                                                            </summary>
                                                                                            <ul>
                                                                                                <li>
                                                                                                    <details>
                                                                                                        <summary><span class="font-weight-bold">Rack :</span> <span class="rackName"></span>
                                                                                                        </summary>
                                                                                                    </details>
                                                                                                </li>
                                                                                            </ul>
                                                                                        </details>
                                                                                    </li>
                                                                                </ul>
                                                                            </details>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                                                            <div class="inner-content">
                                                                <div class="audit-head-section mb-3 mt-3 ">
                                                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span id="createdBy"></span><span class="font-bold text-normal"> on </span> <span id="createdat"></span></p>
                                                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <span id="updatedBy"> </span> <span class="font-bold text-normal"> on </span><span id="updatedat"> </span></spa>
                                                                    </p>
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
                                            </div>
                                            <!--/.Content-->
                                        </div>
                                    </div>
                                    <!-- right modal end here  -->
                                </div>
                            </div>
                        </div>
    </section>
    <!-- /.content -->
</div>

</td>

</tr>


<!-----add form modal start --->
<div class="modal fade hsn-dropdown-modal" id="addToLocation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <form method="POST" action="">
                    <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                    <input type="hidden" id="item_id" name="item_id" value="">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">
                                <div class="card-header">
                                    <h4>Storage Details</h4>
                                </div>
                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">
                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">Storage Control</label>
                                                        <input type="text" name="storageControl" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                        <label for="">Max Storage Period</label>

                                                        <input type="text" name="maxStoragePeriod" class="form-control">

                                                    </div>

                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="form-input">
                                                        <label class="label-hidden" for="">Min Time Unit</label>
                                                        <select id="minTime" name="minTime" class="select2 form-control">
                                                            <option value="">Min Time Unit</option>
                                                            <option value="Day">Day</option>
                                                            <option value="Month">Month</option>
                                                            <option value="Hours">Hours</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Minimum Remain Self life</label>

                                                        <input type="text" name="minRemainSelfLife" class="form-control">

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-input">
                                                        <label class="label-hidden" for="">Max Time Unit</label>
                                                        <select id="maxTime" name="maxTime" class="select2 form-control">
                                                            <option value="">Max Time Unit</option>
                                                            <option value="Day">Day</option>
                                                            <option value="Month">Month</option>
                                                            <option value="Hours">Hours</option>

                                                        </select>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                <div class="card-header">

                                    <h4>Pricing and Discount

                                        <span class="text-danger">*</span>

                                    </h4>

                                </div>

                                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Target price</label>

                                                        <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Max Discount</label>

                                                        <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                            </div>

                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-body" style="height: 500px; overflow: auto;">
                <div class="card">

                </div>
            </div>
        </div>
    </div>


    <?php
    require_once("../common/footer2.php");
    ?>

    <script>
        $(document).ready(function() {
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

                    buttons: [
                        // extend: 'collection',
                        // text: '<ion-icon name="download-outline"></ion-icon> Export',
                        // buttons: [{

                        //     extend: 'csv',
                        //     text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> CSV'
                        // }]
                    ],
                    // select: true,
                    "bPaginate": false,
                });

            }
            $('#dataTable_detailed_view thead tr').append('<th>Action</th>');


            initializeDataTable();

            var allData;
            var dataPaginate;

            function fill_datatable(formDatas = '', pageNo = '', limit = '') {
                var fdate = "<?php echo $f_date; ?>";
                var to_date = "<?php echo $to_date; ?>";
                var comid = <?php echo $company_id; ?>;
                var locId = <?php echo $location_id; ?>;
                var bId = <?php echo $branch_id; ?>;
                // var checkboxSettings = Cookies.get('cookiesPayroll');
                var notVisibleColArr = [];

                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-payroll.php",
                    dataType: 'json',
                    data: {
                        act: 'payroll',
                        comid: comid,
                        locId: locId,
                        bId: bId,
                        formDatas: formDatas,
                        pageNo: pageNo,
                        limit: limit,
                        columnMapping
                    },
                    beforeSend: function() {
                        $("#detailed_tbody").html(`<td colspan=16 class='text-center'>Data is loading....</td>`);
                    },
                    success: function(response) {

                        console.log(response);

                        if (response.status) {
                            var responseObj = response.data;
                            dataPaginate = responseObj;
                            $('#yourDataTable_paginate').show();
                            $('#limitText').show();

                            dataTable.clear().draw();
                            dataTable.columns().visible(false);
                            dataTable.column(length - 1).visible(true);

                            $.each(responseObj, function(index, value) {
                                $('#item_id').val(value.itemId);
                                let deleteBtn = "";
                                let status = "";
                                let postAccBtn = "";
                                if (value.status == 'Pending') {
                                    deleteBtn = ` <li>
                                        <button class="deleteBtn" data-id="${value.payroll_main_id}" data-toggle="modal" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                    </li>`;
                                    status = `<div class="status-bg status-pending">Pending</div>`;
                                    postAccBtn = ` <a href="#" data-toggle="modal" class="postAccountingBtn" data-id=${value.process_id} data-payroll_main_id=${value.payroll_main_id} data-documentNo=${value.payroll_code} data-payroll_month=${value.payroll_month} data-payroll_year=${value.payroll_year} data-sum_gross=${value.sum_gross} data-sum_pf_employee=${value.sum_pf_employee} data-sum_pf_employeer=${value.sum_pf_employeer} data-sum_pf_admin=${value.sum_pf_admin} data-sum_ptax=${value.sum_ptax} data-sum_esi_employee=${value.sum_esi_employee} data-sum_esi_employeer=${value.sum_esi_employeer} data-sum_tds=${value.sum_tds} data-sum_gross=${value.sum_gross} data-target="#actionModal">
                                    <i class="fa fa-book po-list-icon" aria-hidden="true"></i>
                                </a>`;

                                } else if (value.status == 'Posted') {
                                    status = `<div class="status-bg status-paid">Posted</div>`;

                                }

                                dataTable.row.add([

                                    value.sl_no,
                                    `<a href="#" class="soModal"  data-id="${value.payroll_main_id}" data-toggle="modal" data-target="#viewGlobalModal">${ value.payroll_code}</a>`,
                                    value.month_year,
                                    value.sum_gross,
                                    value.sum_pf_employee,
                                    value.sum_pf_employeer,
                                    value.sum_pf_admin,
                                    value.sum_esi_employee,
                                    value.sum_esi_employeer,
                                    value.sum_ptax,
                                    value.sum_tds,
                                    status,
                                    postAccBtn,

                                    ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                            
                                    <li>
                                ${deleteBtn}
                                    </li>
                                    <li>
                                        <button  class="soModal" data-toggle="modal" data-id="${value.payroll_main_id}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                    </li>
                                    </ul>
                                </div>`,

                                ]).draw(false);
                            });

                            $('#yourDataTable_paginate').html(response.pagination);
                            $('#limitText').html(response.limitTxt);



                            var checkboxSettings = Cookies.get('cookiesPayroll');
                            // console.log(checkboxSettings);
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

                                // console.log('Cookie value:', checkboxSettings);

                            } else {
                                $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                                $(".settingsCheckbox_detailed").each(function(index) {
                                    if ($(this).prop("checked")) {
                                        dataTable.column(index).visible(true);

                                    }
                                });

                                console.log('Cookie is blank.');

                            }
                        } else {

                            $("#detailed_tbody").html(`<td colspan=16 class='text-center'>No data found</td>`);
                            $('#yourDataTable_paginate').hide();
                            $('#limitText').hide();
                        }
                    }
                });
            }

            fill_datatable();


            $(document).on("click", ".ion-paginationlistPayroll", function(e) {
                var filteredColumnMapping = columnMapping.filter(function(col) {
                    return col.slag !== 'post';
                });
                $.ajax({
                    type: "POST",
                    url: "../common/exportexcel-new.php",
                    dataType: "json",
                    data: {
                        act: 'paginationlist',
                        data: JSON.stringify(dataPaginate),
                        coloum: filteredColumnMapping,
                        sql_data_checkbox: Cookies.get('cookiesPayroll')
                    },
                    beforeSend: function() {
                        // console.log(sql_data_checkbox);
                        $('#loaderModal').show();
                        $('.ion-paginationlistPayroll').prop('disabled', true)
                    },

                    success: function(response) {
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
                        $('.ion-paginationlistPayroll').prop('disabled', false)
                    }
                })

            });
            $(document).on("click", ".ion-fulllistPayroll", function(e) {
                var filteredColumnMapping = columnMapping.filter(function(col) {
                    return col.slag !== 'post';
                });
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-payroll.php",
                    dataType: "json",
                    data: {
                        act: 'alldata',
                        coloum: filteredColumnMapping,
                        sql_data_checkbox: Cookies.get('cookiesPayroll'),
                        formDatas: formInputs
                    },

                    beforeSend: function() {
                        // console.log(sql_data_checkbox);
                        $('#loaderModal').show();
                        $('.ion-fulllistPayroll').prop('disabled', true)
                    },
                    success: function(response) {
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
                        $('.ion-fulllistPayroll').prop('disabled', false);
                    }
                })

            });

            //    ----- page length limit-----\
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


                        if ((columnSlag === 'expectedDate') && operatorName == "BETWEEN") {
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

                    $('#btnSearchCollpase_modal').modal('hide');
                    console.log("FormInputs:", formInputs);

                    fill_datatable(formDatas = formInputs);
                    $("#myForm")[0].reset();

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
                // $("#myModal1").modal().hide();
                $('#btnSearchCollpase_modal').modal('hide');
                var tablename = $("#tablename").val();
                var pageTableName = $("#pageTableName").val();
                var settingsCheckbox = [];
                var formData = {};
                $(".settingsCheckbox_detailed").each(function() {
                    if ($(this).prop('checked')) {
                        var chkBox = $(this).val();
                        settingsCheckbox.push(chkBox);
                        formData = {
                            tablename,
                            pageTableName,
                            settingsCheckbox
                        };
                    }
                });

                console.log(formData);
                if (settingsCheckbox.length < 5) {
                    alert("Please select at least 5");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "ajaxs/ajax-save-cookies.php",
                        dataType: 'json',
                        data: {
                            act: 'payroll',
                            formData: formData
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
                if (columnName === 'Require Date') {
                    inputId = "value2_" + columnIndex;
                }

                if ((columnName === 'Require Date') && operatorName === 'BETWEEN') {
                    inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
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

    <!-- script for modal -->
    <script>
        $(document).on("click", ".soModal", function() {
            $('#viewGlobalModal').modal('show');
            let payrollId = $(this).data('id');
            $.ajax({
                type: "GET",
                url: "ajaxs/modals/fa/ajax-payroll-main-modal.php",
                dataType: 'json',
                data: {
                    payrollId
                },
                beforeSend: function() {
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
                    $('#viewGlobalModal .modal-body').append(loader);

                },
                success: function(res) {
                    // console.log(res);
                    $("#globalModalLoader").remove();
                    $('.left #amount').html("INR " + decimalAmount(res.sqlPayrollObj.sum_gross))
                    $('.left #doc-number').html((res.datemyr))
                    $('.right #due_amt').html(res.sqlPayrollObj.payroll_code)
                    $.each(res.data, function(index, val) {

                        let td = ` <tr>
                                                                <td>${val.costCenter_code}</td>
                                                                <td>${decimalAmount(val.gross)}</td>
                                                                <td>${decimalAmount(val.pf_employee)}</td>
                                                                <td>${decimalAmount(val.pf_employeer)}</td>
                                                                <td>${decimalAmount(val.pf_admin)}</td>
                                                                <td>${decimalAmount(val.esi_employee)}</td>
                                                                <td>${decimalAmount(val.esi_employeer)}</td>
                                                                <td>${decimalAmount(val.ptax)}</td>
                                                                <td>${decimalAmount(val.tds)}</td>
                                                              </tr>
                                                            `;
                        $("#currencyHead").html(val.currency);
                        $("#itemTableBody").append(td);

                    })
                },
                complete: function() {
                    $('#viewGlobalModal .modal-body .load-wrapp').remove();
                }
            })

        });
    </script>
    <!-- //  delete script -->

    <script>
        $(document).on('click', '.deleteBtn', function() {
            var payrollMainId = $(this).data('id');
            if (!confirm(`Are you sure to delete this document?`)) {
                return false;
            }
            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-delete.php`,
                dataType: 'json',
                data: {
                    act: "payroll",
                    payrollMainId
                },
                success: function(response) {
                    console.log(response);
                    let data = response;

                    // js swal alert
                    let timerInterval;
                    Swal.fire({
                        icon: data.status,
                        title: `Deleted successfully!`,
                        timer: 1200,
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
    <!-- post to accounting script -->

    <script>
        $(document).on("click", ".postAccountingBtn", function() {
            let payroll_main_id = $(this).data('payroll_main_id');
            let documentNo = $(this).data('documentno');
            let payroll_month = $(this).data('payroll_month');
            let payroll_year = $(this).data('payroll_year');
            let sum_gross = $(this).data('sum_gross');
            let sum_pf_employee = $(this).data('sum_pf_employee');
            let sum_pf_employeer = $(this).data('sum_pf_employeer');
            let sum_pf_admin = $(this).data('sum_pf_admin');
            let sum_ptax = $(this).data('sum_ptax');
            let sum_esi_employee = $(this).data('sum_esi_employee');
            let sum_esi_employeer = $(this).data('sum_esi_employeer');
            let sum_tds = $(this).data('sum_tds');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `Are you sure to post accounting ${documentNo} ?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Post'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        type: 'POST',
                        url: "ajaxs/ajax-sub-payroll-processing.php",
                        dataType: 'json',
                        data: {
                            act: "payroll",
                            payroll_main_id,
                            documentNo,
                            payroll_month,
                            sum_gross,
                            payroll_year,
                            sum_pf_employee,
                            sum_pf_employeer,
                            sum_pf_admin,
                            sum_ptax,
                            sum_esi_employee,
                            sum_esi_employeer,
                            sum_tds
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.status == "success") {
                                Swal.fire({
                                    icon: response.status,
                                    title: response.message,
                                    timer: 3000,
                                    showConfirmButton: false,
                                })
                                location.reload();
                            }
                        },

                    });
                }

            });


        })
    </script>