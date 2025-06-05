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
include_once("../../app/v1/functions/branch/func-branch-pr-controller.php");
require_once("../../app/v1/functions/branch/func-items-controller.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../app/v1/functions/branch/func-goods-controller.php");

if (!isset($_COOKIE["cookiesManagePr"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiesManagePr", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addNewRFQFormSubmitBtn'])) {
        $addBranchRfq = $BranchPrObj->addBranchRFQ($_POST);
        // swalToast($addBranchRfq["status"], $addBranchRfq["message"]);
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


$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => '	PR Number',
        'slag' => 'pr.prCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Required Date',
        'slag' => 'pr.expectedDate',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Reference Number',
        'slag' => 'pr.refNo',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'stat.label',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => '	Created By',
        'slag' => 'admin.fldAdminName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
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
<div class="content-wrapper report-wrapper is-purchase-request vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">Manage Purchase Request</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <!-- <?php require_once("components/mm/manage-pr-tabs.php"); ?> -->
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
                                                        <button class="ion-paginationlistPR">
                                                            <ion-icon name="list-outline" class="ion-paginationlistPR md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistPR">
                                                            <ion-icon name="list-outline" class="ion-fulllistPR md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="manage-pr-creation.php?pr-creation" class="btn btn-create waves-effect waves-light" type="button">
                                                <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
                                                Create
                                            </a>

                                            <table id="dataTable_detailed_view" class="table table-hover table-nowrap stock-new-table transactional-book-table">

                                                <thead>
                                                    <tr>
                                                        <?php
                                                        foreach ($columnMapping as $index => $column) {
                                                            if ($column['slag'] === 'stat.label') {
                                                        ?>
                                                                <th width='8%' data-value="<?= $index ?>"><?= $column['name'] ?></th>
                                                            <?php   } else { ?>
                                                                <th data-value="<?= $index ?>"><?= $column['name'] ?></th>
                                                        <?php
                                                            }
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
                                                            <h4 class="modal-title text-sm">Column Settings</h4>
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
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "=", "!=", "BETWEEN"];

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
                                                        <div class="" id="modalstatusborder">
                                                            <div class="top-details">
                                                                <div class="left">
                                                                    <p class="info-detail purchase-number" id="purchaseNumber">
                                                                        <ion-icon name="wallet-outline"></ion-icon>
                                                                        <label for="">Purchase Request No.</label>
                                                                        <span class="pr-number" id="prNumber"></span>
                                                                    </p>
                                                                    <p class="info-detail reference" id="referenceNo">
                                                                        <ion-icon name="wallet-outline"></ion-icon>
                                                                        <label for="">Reference No.</label>
                                                                        <span class="ref-number" id="refNumber"></span>
                                                                    </p>
                                                                </div>
                                                                <div class="right">
                                                                    <p class="info-detail rqst-date">
                                                                        <ion-icon name="business-outline"></ion-icon>
                                                                        <label for="">Requested On:</label>
                                                                        <span id="reqdate"></span>
                                                                    </p>
                                                                    <p class="info-detail purchase-status">
                                                                        <ion-icon name="location-outline"></ion-icon>
                                                                        <label for="">Status</label>
                                                                        <span id="pr-status" class=""></span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <nav>
                                                                <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                                                                    <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                                                                    <button class="nav-link classicview-btn classicview-link" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Classic View</button>
                                                                    <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                                                                </div>
                                                            </nav>
                                                            <div class="tab-content global-tab-content" id="nav-tabContent">

                                                                <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                                                                    <div class="nav-overview-tabs" id="practionbtns">
                                                                        <div class="d-flex">

                                                                            <div class="d-flex" id="createAddrfqdiv">
                                                                                <button class="btn btn-primary" id="createPobtn"><ion-icon name="add-outline"></ion-icon>Create PO</button>

                                                                                <div class="rfq-link-add">
                                                                                    <button class="btn btn-primary" type="submit" name="addNewRFQFormSubmitBtn" id="addtoRfq"><ion-icon name="add-outline"></ion-icon>Add to Request</button>
                                                                                    <p>Check in <a href="" id="rfqList">RFQ List</a></p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="d-flex editClosePodiv">
                                                                                <button class="btn btn-primary" data-pridcode="" id="editPobtn"><ion-icon name="create-outline"></ion-icon>Edit PR</button>
                                                                                <button class="btn btn-danger closePobtn" data-pridcode="" id=""><ion-icon name="close-outline"></ion-icon>Close PR</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row rfq-item-table">
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                                                            <div class="items-table">
                                                                                <h4>Item Details</h4>
                                                                                <form action="purchase-order-creation.php" method="GET" name="" id="prActionForm">
                                                                                    <input type="hidden" name="pr-po-creation" id="formPrId">
                                                                                    <table>
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>#</th>
                                                                                                <th>Item Code</th>
                                                                                                <th>Item Name</th>
                                                                                                <th>QTY</th>
                                                                                                <th>Remaining QTY</th>
                                                                                                <th>Unit</th>
                                                                                                <th>Required Date</th>
                                                                                                <th>Status</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody id="itemdetailsbody">



                                                                                        </tbody>
                                                                                    </table>
                                                                                </form>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane classicview-pane fade" id="nav-classicview" role="tabpanel" aria-labelledby="nav-classicview-tab">
                                                                    <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrint" target="_blank">Print</a>
                                                                    <div class="card classic-view bg-transparent" id="innerClassicView">

                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                                                                    <div class="inner-content">
                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span><span class="font-bold text-normal"> on </span> <span class="created_at_trail"></span></p>
                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"></span><span class="font-bold text-normal"> on </span> <span class="updated_at"></span> </span></p>
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
    </section>
    <!-- /.content -->
</div>

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
</div>


<?php
require_once("../common/footer2.php");
?>


<script>
    let csvContent;
    let csvContentBypagination;
    var columnMapping = <?php echo json_encode($columnMapping); ?>;
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
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        var allData;
        var dataPaginate;

        function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-pr.php",
                dataType: 'json',
                data: {
                    act: 'managePr',
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
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);

                        $.each(responseObj, function(index, value) {

                            if (value['stat.label'] == "open") {
                                status = '<div class="status-bg status-open">Open</div>';
                            } else if (value['stat.label'] == "closed") {
                                status = '<div class="status-bg status-closed">Closed</div>';
                            }
                            // $('#item_id').val(value.itemId);
                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.purchaseRequestId}" data-prcode=${ value['pr.prCode']} data-toggle="modal" data-target="#viewGlobalModal">${ value['pr.prCode']}</a>`,
                                value['pr.expectedDate'],
                                value['pr.refNo'],
                                status,
                                value['admin.fldAdminName'],

                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                   
                                    <li>
                                    <button class="soModal" data-toggle="modal" data-prcode=${ value['pr.prCode']} data-id=${value.purchaseRequestId}><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                  </li>
                                  
                                    </ul>
                                   
                                </div>`,

                            ]).draw(false);

                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);
                        var checkboxSettings = Cookies.get('cookiesManagePr');

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

                            notVisibleColArr.forEach(function(index) {
                                dataTable.column(index).visible(false);
                            });

                            console.log('Cookie value:', checkboxSettings);

                        } else {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);
                                }
                            });
                            console.log("Cookies blank");
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

        $(document).on("click", ".closePobtn", function(e) {
            var pridcode = $(this).data('pridcode');

            var $modal = $('#viewGlobalModal');

            var userConfirmed = confirm(`Are you sure you want to close this PR?`);

            $modal.modal('hide');

            if (!userConfirmed) {
                return false;
            }

            $.ajax({
                type: "GET",
                url: `ajaxs/pr/ajax-pr-close.php`,
                data: {
                    act: "prClose",
                    pr_id: pridcode
                },
                success: function(response) {
                    let parsedResponse = JSON.parse(response);

                    if (parsedResponse.status === 'success') {
                        alert('The PR has been successfully closed.');
                    } else {
                        alert('There was an issue closing the PR.');
                    }

                    fill_datatable();
                },
                error: function(xhr, status, error) {
                    alert('Oops! Something went wrong while closing the PR.');
                }
            });
        });


        $(document).on("click", ".ion-paginationlistPR", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiesManagePr')
                },
                beforeSend: function() {
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-paginationlistPR').prop('disabled', true)
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
                    $('.ion-paginationlistPR').prop('disabled', false)
                }
            })

        });
        $(document).on("click", ".ion-fulllistPR", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-pr.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiesManagePr'),
                    formDatas: formInputs
                },

                beforeSend: function() {
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-fulllistPR').prop('disabled', true)
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
                    $('.ion-fulllistPR').prop('disabled', false);
                }
            })

        });

        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit, columnMapping = columnMapping);

        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a ", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $(".custom-select").val();

            fill_datatable(formDatas = formInputs, pageNo = page_id, limit = limitDisplay, columnMapping = columnMapping);

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


                    if ((columnSlag === 'pr.expectedDate') && operatorName == "BETWEEN") {
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

            });

            $(document).on("keypress", "#myForm input", function(e) {
                if (e.key === "Enter") {
                    $("#serach_submit").click();
                    e.preventDefault();
                }
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


        let pr_id;

        $(document).on("click", ".soModal", function() {

            $('#viewGlobalModal').modal('show');
            $('.ViewfirstTab').tab('show');
            pr_id = $(this).data('id');


            $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print.php?pr_id=${btoa(pr_id)}`);
            // alert(pr_id)
            $('#rfqList').attr("href", 'manage-rfq.php?prid=' + pr_id)
            $('#practionbtns #createAddrfqdiv .btn').prop('disabled', true);
            $('#createPobtn').prop('disabled', true);
            $.ajax({
                type: "GET",
                url: "ajaxs/modals/mm/ajax-manage-pr-modal.php",
                dataType: 'json',
                data: {
                    act: 'modalData',
                    pr_id: pr_id

                },
                beforeSend: function() {
                    $('#itemdetailsbody').html('');
                    $("#pr-status").removeClass();
                    $("#modalstatusborder").removeClass();

                },
                success: function(response) {
                    console.log(response);
                    var responseObj = response.data;
                    var itemsObj = response.item_details;

                    $(".closePobtn").attr('data-pridcode', pr_id);
                    $("#editPobtn").attr('data-pridcode', pr_id);
                    $('#addtoRfq').attr('data-id', pr_id);
                    $('#addtoRfq').attr('data-prcode', responseObj.prCode);
                    $('#nav-trail-tab').attr('data-ccode', responseObj.prCode);
                    // enable disable action button
                    if (responseObj.pr_status == "10") {
                        // closed part
                        $('#practionbtns .closePobtn').prop('disabled', true);
                        $('#practionbtns #editPobtn').prop('disabled', true);
                        $('#practionbtns #addtoRfq').prop('disabled', false);
                    } else {
                        // open part
                        // console.log("hit else part");

                        $('#practionbtns .closePobtn').prop('disabled', false);
                        $('#practionbtns #editPobtn').prop('disabled', false);
                        $('#practionbtns #addtoRfq').prop('disabled', true);
                        // console.log(responseObj.pr_status);

                        actionButton();
                    }

                    if (response.prPoExist) {
                        $('#practionbtns #editPobtn').hide()
                    }

                    // header portion
                    $("#prNumber").html(responseObj.prCode);
                    $("#refNumber").html(responseObj.refNo);
                    $("#reqdate").html(formatDate(responseObj.pr_date));

                    // condition for modal header open || closed status
                    if (responseObj.label === 'open') {
                        $("#pr-status").html('Open');
                        $("#pr-status").addClass('status-bg status-open');
                        $("#modalstatusborder").addClass('modal-header border-status-open');
                    } else if (responseObj.label === 'closed') {
                        $("#pr-status").html('Closed');
                        $("#pr-status").addClass('status-bg status-closed');
                        $("#modalstatusborder").addClass('modal-header border-status-close');
                    }

                    $('#formPrId').attr('value', btoa(responseObj.purchaseRequestId));
                    $('.created_by_trail').html(response.created_by);
                    $('.created_at_trail').html(response.created_at);
                    $('.updated_by').html(response.updated_by);
                    $('.updated_at').html(response.updated_at);

                    $.each(itemsObj, function(index, val) {

                        // let deliveryobj = JSON.parse(val.delivery_schedule);
                        let tbody = '';

                        tbody += `<tr>
                                <td>
                                <input type="checkbox" name="itemcheckbox[]" data-id=${val.itemId} data-delvId=${val.pr_delivery_id}  class="item-checkbox" value="${btoa(val.pr_delivery_id)}">
                                </td>
                                <td>${val.itemCode}</td>
                                <td>${val.itemName}</td>
                                <td>${decimalQuantity(val.qty)}</td>
                                <td>${decimalQuantity(val.remaining_qty)}</td>
                                <td>${val.uomName}</td>
                                <td>${formatDate(val.delivery_date)} </td>
                                <td>${val.status??'-'}</td>
                            </tr>`;
                        // });

                        // } else {
                        //     tbody += `<tr>
                        //                 <td><input type="checkbox" name="itemcheckbox[]" class="item-checkbox" value="${btoa(val.prItemId)}" data-date=""> </td>
                        //                 <td>${val.itemCode} </td>
                        //                 <td>${val.itemName} </td>
                        //                 <td>${val.itemQuantity} </td>
                        //                 <td>${val.uomName}</td>
                        //                 <td>-</td>
                        //                 <td>-</td>
                        //             </tr>`;
                        // }
                        $('#itemdetailsbody').append(tbody);

                    })

                },
                error: function(error) {
                    console.log(error);
                }
            });


            $.ajax({
                type: "GET",
                url: "ajaxs/modals/mm/ajax-manage-pr-modal.php",
                data: {
                    act: 'classicView',
                    pr_id
                },
                success: function(res) {
                    // console.log(res);
                    $("#innerClassicView").html(res);
                }
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

            // console.log(formData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'managePr',
                        formData: formData
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
            if (columnName === 'Required Date') {
                inputId = "value2_" + columnIndex;
            }

            if ((columnName === 'Required Date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
        });

        $(document).on("click", "#serach_reset", function(e) {
            e.preventDefault();
            $("#myForm")[0].reset();
            $("#serach_submit").click();
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
    // let pr_id;
    // $(document).on("click", ".soModal", function() {

    //     $('#viewGlobalModal').modal('show');
    //     $('.ViewfirstTab').tab('show');
    //     pr_id = $(this).data('id');


    //     $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print.php?pr_id=${btoa(pr_id)}`);
    //     // alert(pr_id)
    //     $('#rfqList').attr("href", 'manage-rfq.php?prid=' + pr_id)
    //     $('#practionbtns #createAddrfqdiv .btn').prop('disabled', true);
    //     $('#createPobtn').prop('disabled', true);
    //     $.ajax({
    //         type: "GET",
    //         url: "ajaxs/modals/mm/ajax-manage-pr-modal.php",
    //         dataType: 'json',
    //         data: {
    //             act: 'modalData',
    //             pr_id: pr_id

    //         },
    //         beforeSend: function() {
    //             $('#itemdetailsbody').html('');
    //             $("#pr-status").removeClass();
    //             $("#modalstatusborder").removeClass();

    //         },
    //         success: function(response) {
    //             console.log(response);
    //             var responseObj = response.data;
    //             var itemsObj = response.item_details;
    //             $('#addtoRfq').attr('data-id',pr_id);
    //             $('#addtoRfq').attr('data-prcode',responseObj.prCode);
    //             $('#nav-trail-tab').attr('data-ccode', responseObj.prCode);
    //             // enable disable action button
    //             if (responseObj.pr_status == 10) {
    //                 // closed part
    //                 $('#practionbtns #closePobtn').prop('disabled', true);
    //                 $('#practionbtns #editPobtn').prop('disabled', true);
    //                 $('#practionbtns #addtoRfq').prop('disabled', false);
    //             } else {
    //                 // open part
    //                 // console.log("hit else part");

    //                 $('#practionbtns #closePobtn').prop('disabled', false);
    //                 $('#practionbtns #editPobtn').prop('disabled', false);
    //                 $('#practionbtns #addtoRfq').prop('disabled', true);
    //                 // console.log(responseObj.pr_status);

    //                 actionButton();
    //             }

    //             // header portion
    //             $("#prNumber").html(responseObj.prCode);
    //             $("#refNumber").html(responseObj.refNo);
    //             $("#reqdate").html(formatDate(responseObj.pr_date));

    //             // condition for modal header open || closed status
    //             if (responseObj.label === 'open') {
    //                 $("#pr-status").html('Open');
    //                 $("#pr-status").addClass('status-bg status-open');
    //                 $("#modalstatusborder").addClass('modal-header border-status-open');
    //             } else if (responseObj.label === 'closed') {
    //                 $("#pr-status").html('Closed');
    //                 $("#pr-status").addClass('status-bg status-closed');
    //                 $("#modalstatusborder").addClass('modal-header border-status-close');
    //             }

    //             $('#formPrId').attr('value', btoa(responseObj.purchaseRequestId));
    //             $('.created_by_trail').html(response.created_by);
    //             $('.created_at_trail').html(response.created_at);
    //             $('.updated_by').html(response.updated_by);
    //             $('.updated_at').html(response.updated_at);

    //             $.each(itemsObj, function(index, val) {

    //                 // let deliveryobj = JSON.parse(val.delivery_schedule);
    //                 let tbody = '';

    //                 tbody += `<tr>
    //                                     <td>
    //                                     <input type="checkbox" name="itemcheckbox[]" data-id=${val.itemId} data-delvId=${val.pr_delivery_id}  class="item-checkbox" value="${btoa(val.pr_delivery_id)}">
    //                                     </td>
    //                                     <td>${val.itemCode}</td>
    //                                     <td>${val.itemName}</td>
    //                                     <td>${decimalQuantity(val.qty)}</td>
    //                                     <td>${decimalQuantity(val.remaining_qty)}</td>
    //                                     <td>${val.uomName}</td>
    //                                     <td>${formatDate(val.delivery_date)} </td>
    //                                     <td>${val.status??'-'}</td>
    //                                 </tr>`;
    //                 // });

    //                 // } else {
    //                 //     tbody += `<tr>
    //                 //                 <td><input type="checkbox" name="itemcheckbox[]" class="item-checkbox" value="${btoa(val.prItemId)}" data-date=""> </td>
    //                 //                 <td>${val.itemCode} </td>
    //                 //                 <td>${val.itemName} </td>
    //                 //                 <td>${val.itemQuantity} </td>
    //                 //                 <td>${val.uomName}</td>
    //                 //                 <td>-</td>
    //                 //                 <td>-</td>
    //                 //             </tr>`;
    //                 // }
    //                 $('#itemdetailsbody').append(tbody);

    //             })

    //         },
    //         error: function(error) {
    //             console.log(error);
    //         }
    //     });


    //     $.ajax({
    //         type: "GET",
    //         url: "ajaxs/modals/mm/ajax-manage-pr-modal.php",
    //         data: {
    //             act: 'classicView',
    //             pr_id
    //         },
    //         success: function(res) {
    //             // console.log(res);
    //             $("#innerClassicView").html(res);
    //         }
    //     });


    // });

    // edit po 
    $(document).ready(function() {
        $(document).on("click", "#editPobtn", function() {
            let pr_id = $(this).data('pridcode')
            let url = `<?= BRANCH_URL ?>location/manage-pr-creation.php?edit=${pr_id}`;
            window.location.href = url;
        });
    });
</script>

<script>
    // action button show 
    function actionButton() {
        $(document).on("change", ".item-checkbox", function() {
            if ($(".item-checkbox:checked").length > 0) {
                $('#practionbtns .btn').prop('disabled', false);
            } else {
                // $('#practionbtns .editClosePodiv .btn').prop('disabled', true);
                $('#practionbtns #createAddrfqdiv .btn').prop('disabled', true);
                $('#createPobtn').prop('disabled', true);
            }
        });
    }
    $(document).ready(function() {

        // create po

        $(document).on("click", "#createPobtn", function() {
            $('#prActionForm').submit();

        });


        // close pr
        // $(document).on("click", "#closePobtn", function() {
        //     let prId = $('.soModal').data('id')
        //     let url = "<?php // echo  BRANCH_URL . 'location/manage-pr-creation.php?close-pr=' 
                            ?>" + prId;
        //     window.location.href = url;
        // });


        // Function to handle button click event

        $(document).on("click", "#addtoRfq", function(e) {
            var checkboxes = document.querySelectorAll('.item-checkbox');
            var itemcheckbox = []; // Initialize as an array
            var prCode = $(this).data('id');
            var prid = $(this).data('prcode');

            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    var deliveryId = checkbox.getAttribute('data-delvId');
                    var itemId = checkbox.getAttribute('data-id');
                    var item = {
                        'itemId': itemId,
                        'deliveryId': deliveryId
                    };
                    itemcheckbox.push(item);
                }
            });

            var itemObj = {
                'prCode': prCode,
                'prid': prid,
                'itemObj': itemcheckbox
            };

            $.ajax({
                url: 'ajaxs/modals/mm/ajax-manage-pr-modal.php',
                type: "POST",
                data: itemObj,
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: response.status,
                        title: response.message,
                        timer: 1000,
                        showConfirmButton: false,
                    })
                },
                error: function(xhr, status, error) {
                    // Handle errors here if needed
                    console.error(error);
                }
            });

            // console.log(itemObj);
        });


    });
</script>