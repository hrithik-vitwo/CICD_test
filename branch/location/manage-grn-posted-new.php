<?php
require_once("../../app/v1/connection-branch-admin.php");

if (!isset($_COOKIE["cookiegrninvposted"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiegrninvposted", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
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
require_once("../../app/v1/functions/branch/func-goods-controller.php");
include_once("../../app/v1/functions/branch/func-grn-controller.php");
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

$dbObj = new Database();
$grnObj = new GrnController();
$accountingControllerObj = new Accounting();

// start 
if (isset($_POST["ivPostingFormSubmitBtn"])) {

    $ivPostingData = json_decode(base64_decode($_POST["ivPostingGrnData"]), true);
    $grnPostingJournalId = $ivPostingData["grnDetails"]["grnPostingJournalId"];
    $grnId = $ivPostingData["grnDetails"]["grnId"];
    $vendorDetailsObj = $dbObj->queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=' . $_POST["vendorId"]);
    $vendorParentGlId = $vendorDetailsObj["data"]["parentGlId"] ?? 0;
    $tdsDetails = [];
    $postingDate = $_POST['invoicePostingDate'] ?? date("Y-m-d");
    $extra_remark = $POST['extra_remark'] ?? '';
    $symbol = $_POST["adjust_symbol"];
    $roundOffValue = $_POST["roundOffGL"];
    if ($symbol == "add") {
        $roundOffGL = $roundOffValue;
    } else {
        $roundOffGL = $roundOffValue * -1;
    }

    $ivPostingInputData = [
        "BasicDetails" => [

            "documentNo" => $_POST["documentNo"], // Invoice Doc Number

            "documentDate" => $_POST["documentDate"], // Invoice number

            "postingDate" => $postingDate, // current date

            "grnJournalId" => $grnPostingJournalId,

            "reference" => $_POST["grnCode"], // grn code

            "remarks" => "Invoice Posting - " . $_POST["grnCode"] . " " . $extra_remark,

            "journalEntryReference" => "Purchase"

        ],

        "vendorDetails" => [

            "vendorId" => $_POST["vendorId"],

            "vendorName" => $_POST["vendorName"],

            "vendorCode" => $_POST["vendorCode"],

            "parentGlId" => $vendorParentGlId

        ],

        "grIrItems" => $_POST['grnItemList'],

        "taxDetails" => [

            "cgst" => $_POST["totalInvoiceCGST"],

            "sgst" => $_POST["totalInvoiceSGST"],

            "igst" => $_POST["totalInvoiceIGST"]

        ],

        "tdsDetails" => $tdsDetails,
        "roundOffValue" => $roundOffGL

    ];

    $createInvObj = $grnObj->createInvoice($_POST);


    if ($createInvObj["status"] == "success") {
        $ivPostingObj = $accountingControllerObj->grnIvAccountingPosting($ivPostingInputData, "grniv", $createInvObj['grnIVId']);
        $queryObj = $dbObj->queryUpdate('UPDATE `erp_grninvoice` SET `ivPostingJournalId`=' . $ivPostingObj["journalId"] . ' WHERE `grnIVId`=' . $createInvObj['grnIVId']);
        swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"], BASE_URL . "branch/location/manage-vendor-invoice-tax.php");
    } else {
        swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"]);
    }
} elseif (isset($_POST["ivPostingFormSubmitBtnSRN"])) {
    $ivPostingData = json_decode(base64_decode($_POST["ivPostingGrnData"]), true);
    $grnPostingJournalId = $ivPostingData["grnDetails"]["grnPostingJournalId"];
    $grnId = $ivPostingData["grnDetails"]["grnId"];
    $vendorDetailsObj = queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=' . $ivPostingData["grnDetails"]["vendorId"]);
    $vendorParentGlId = $vendorDetailsObj["data"]["parentGlId"] ?? 0;
    $tdsDetails = ["0" => $_POST['totalInvoiceTDS']];
    $symbol = $_POST["adjust_symbol"];
    $roundOffValue = $_POST["roundOffGL"];
    if ($symbol == "add") {
        $roundOffGL = $roundOffValue;
    } else {
        $roundOffGL = $roundOffValue * -1;
    }


    $postingDate = $_POST['invoicePostingDate'] ?? date("Y-m-d");
    $extra_remark = $POST['extra_remark'] ?? '';

    $ivPostingInputData = [

        "BasicDetails" => [

            "documentNo" => $ivPostingData["grnDetails"]["vendorDocumentNo"], // Invoice Doc Number

            "documentDate" => $ivPostingData["grnDetails"]["vendorDocumentDate"], // Invoice number

            "postingDate" => $postingDate, // current date

            "grnJournalId" => $grnPostingJournalId,

            "reference" => $ivPostingData["grnDetails"]["grnCode"], // grn code

            "remarks" => "Invoice Posting - " . $ivPostingData["grnDetails"]["grnCode"] . " " . $extra_remark,

            "journalEntryReference" => "Purchase"

        ],

        "vendorDetails" => [

            "vendorId" => $ivPostingData["grnDetails"]["vendorId"],

            "vendorName" => $ivPostingData["grnDetails"]["vendorName"],

            "vendorCode" => $ivPostingData["grnDetails"]["vendorCode"],

            "parentGlId" => $vendorParentGlId

        ],

        "grIrItems" => $_POST['grnItemList'],

        "taxDetails" => [

            "cgst" => $ivPostingData["grnDetails"]["grnTotalCgst"],

            "sgst" => $ivPostingData["grnDetails"]["grnTotalSgst"],

            "igst" => $ivPostingData["grnDetails"]["grnTotalIgst"],

            "rcm" => $_POST["rcm"] ?? 0

        ],

        "tdsDetails" => $tdsDetails,
        "roundOffValue" => $roundOffGL

    ];

    $createInvObj = $grnObj->createInvoice($_POST);
    if ($createInvObj["status"] == "success") {
        //console($ivPostingInputData);
        $ivPostingObj = $accountingControllerObj->srnIvAccountingPosting($ivPostingInputData, "srniv", $createInvObj['grnIVId']);
        $queryObj = queryUpdate('UPDATE `erp_grninvoice` SET `ivPostingJournalId`=' . $ivPostingObj["journalId"] . ' WHERE `grnIVId`=' . $createInvObj['grnIVId']);
        swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"], BASE_URL . "branch/location/manage-vendor-invoice-tax.php");
    } else {
        swalAlert($createInvObj["status"], ucfirst($createInvObj["status"]), $createInvObj["message"]);
    }
}
// end 


$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'GRN/SRN Number',
        'slag' => 'grnCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'GRN IV Code',
        'slag' => 'grnIvCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'PO No',
        'slag' => 'grnPoNumber',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Code',
        'slag' => 'vendorCode',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Name',
        'slag' => 'vendorName',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Document No',
        'slag' => 'vendorDocumentNo',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Document Date',
        'slag' => 'vendorDocumentDate',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Posting Date',
        'slag' => 'postingDate',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Total Invoice Amount',
        'slag' => 'grnTotalAmount',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Created By',
        'slag' => 'grnCreatedBy',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Approve Status',
        'slag' => 'grnApprovedStatus',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'grnStatus',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]
];

?>
<!-- <link rel="stylesheet" href="../../../public/assets/new_listing.css"> -->
<!-- <link rel="stylesheet" href="../../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">

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
                                                <h3 class="card-title mb-0">Posted GRN</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <?php require_once("components/mm/grnInvoice-tabs-new.php"); ?>
                                            <button class="btn btn-sm fillscreen-btn" onclick="openFullscreen()"><i class="fa fa-expand fa-2x"></i></button>
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

                                            <!-- <a href="manage-grn-invoice.php" class="btn btn-create waves-effect waves-light" type="button">
                                                <ion-icon name="add-outline" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
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
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", ">=", ">", "<", "<=", "=", "!=", "BETWEEN"];

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
                                                                                        } elseif ($column['dataType'] === "number") {
                                                                                            $operator = array_slice($operators, 2, 6);
                                                                                            foreach ($operator as $oper) { ?>
                                                                                                <option value="<?= $oper ?>"><?= $oper ?></option>
                                                                                                <?php  }
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
                                </div>
                            </div>
                        </div>
    </section>
    <!-- /.content -->

    <!-- Global View start-->

    <div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
        <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
            <div class="modal-content">

                <div class="modal-body">

                    <div class="tab-content global-tab-content" id="nav-tabContent">

                        <div class="inneraction">

                        </div>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

    <!-- Global View end -->
</div>

</td>

</tr>


<!-----add form modal start --->
<!-- <div class="modal fade hsn-dropdown-modal" id="addToLocation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
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
</div> -->


<?php
require_once("../common/footer2.php");
?>

<script>
    $(document).ready(function() {
        // Form reset button
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
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            // var checkboxSettings = Cookies.get('cookiesProdDeclare');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-grn-posted.php",
                dataType: 'json',
                data: {
                    act: 'grnPosted',
                    comid: comid,
                    locId: locId,
                    bId: bId,
                    formDatas: formDatas,
                    pageNo: pageNo,
                    limit: limit,
                    columnMapping

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

                    if (response.status) {
                        var responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);

                        $.each(responseObj, function(index, value) {
                            $('#item_id').val(value.itemId);
                            let status = '';
                            if (value.grnStatus == "active") {
                                status = '<p class="status-bg status-open">Active</p>';
                            } else if (value.grnStatus == "reverse") {
                                status = '<p class="status-bg status-pending">Reverse</p>';
                            }

                            let approveStatus = '';
                            if (value.grnApprovedStatus == "approved") {
                                approveStatus = '<p class="goods-type type-service">Approved</p>';
                            } else if (value.grnApprovedStatus == "pending") {
                                approveStatus = '<p class="goods-type type-project">Pending</p>';
                            }

                            let revBtn = '';
                            if (value.grnStatus == 'active' && (value.grnPostingJournalId != "" || value.grnPostingJournalId != null || value.grnPostingJournalId != undefined)) {
                                revBtn = `
                                        <li>
                                            <button class="reverseGRNIV"  data-id=${value.grnIvId}><ion-icon name="arrow-undo-outline"></ion-icon>Reverse</button>
                                        </li>`;
                            }

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.grnId}" data-status="${value.grnStatus}" data-grnType="${value.grnType}" >${value.grnCode}</a>`,
                                value.grnIvCode,
                                value.grnPoNumber,
                                value.vendorCode,
                                `<p class="pre-normal">${value.vendorName}</p>`,
                                value.vendorDocumentNo,
                                (value.vendorDocumentDate),
                                (value.postingDate),
                                `<p class="text-right">${(value.grnTotalAmount)}</p>`,
                                value.grnCreatedBy,
                                approveStatus,
                                status,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>

                                    </button>
                                    <ul>
                                        <li>
                                        <button class="soModal" data-id="${value.grnId}" data-grnType="${value.grnType}"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>                                        
                                        </li>
                                        ${revBtn}                                                                            
                                    </ul>
                                   
                                </div>`,
                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        var checkboxSettings = Cookies.get('cookiegrninvposted');
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
                    $("#globalModalLoader").remove();
                },
                complete: function() {
                    $("#globalModalLoader").remove();

                },
            });
        }

        fill_datatable();


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
                    if (columnSlag === 'postingDate') {
                        values = value2;
                    } else if (columnSlag === 'vendorDocumentDate') {
                        values = value3;
                    }


                    if ((columnSlag === 'postingDate' || columnSlag === 'vendorDocumentDate') && operatorName == "BETWEEN") {
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
                console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs);
                $("#myForm")[0].reset();
                $(".m-input2").remove();


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
                    dataType: "JSON",
                    data: {
                        act: 'grninvposted',
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
            if (columnName == 'Document Date') {
                inputId = "value3_" + columnIndex;
            } else if (columnName == 'Posting Date') {
                inputId = "value2_" + columnIndex;
            }

            if ((columnName == 'Document Date' || columnName == 'Posting Date') && operatorName === 'BETWEEN') {
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

<script>
    $(document).on("click", ".soModal", function() {
        let id = $(this).data("id");
        let grnType = $(this).data('grntype');
        let grnStatus = $(this).data('status');

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/grn/ajax-manage-grn-invoice-new.php",
            data: {
                type: grnType,
                view: id,
                status: grnStatus
            },
            beforeSend: function() {
                $(".inneraction").html('');
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
                // console.log(response);
                $(".inneraction").html(response);
                $("#globalModalLoader").remove();
            },
            complete: function() {
                $("#globalModalLoader").remove();
            },
            error: function(error) {
                console.log(error);
            }
        });
        $('#viewGlobalModal').modal('show');

    });
</script>

<!-- script for action from ajax -->
<!-- <script>
    $(document).ready(function() {

        let total_value = 0;
        var sign = "add";
        let roudoff = 0.0;

        function roundofftotal(total_value, sign, roudoff) {
            let final_value = 0;
            if (sign === "add") {
                final_value = total_value + roudoff;
            } else {
                final_value = total_value - roudoff;
            }

            $("#totalInvoiceTotal").val(final_value.toFixed(2));
            $("#tdAdjustedTotal").html(final_value.toFixed(2));

        }

        $(document).on("blur", "#round_value", function() {
            let roundValue = parseFloat($(this).val());
            let total_value = parseFloat($("#totalInvoiceTotal").val());
            var sign = $('#round_sign').val();
            roundofftotal(total_value, sign, roundValue);
        });


        $(document).on("keyup", ".itemCgst", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });
        $(document).on("keyup", ".itemSgst", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        $(document).on("keyup", ".itemIgst", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });

        $(document).on("keyup", ".itemTds", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            calculateOneItemAmounts(rowNo);
        });


        function calculateOneItemAmounts(rowNo) {
            let basicPrice = (parseFloat($(`#grnItemBaseInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemBaseInput_${rowNo}`).val()) : 0;
            let cgst = (parseFloat($(`#grnItemUnitCgstInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitCgstInput_${rowNo}`).val()) : 0;
            let sgst = (parseFloat($(`#grnItemUnitSgstInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitSgstInput_${rowNo}`).val()) : 0;
            let igst = (parseFloat($(`#grnItemUnitIgstInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitIgstInput_${rowNo}`).val()) : 0;
            let tds = (parseFloat($(`#grnItemUnitTdsInput_${rowNo}`).val()) > 0) ? parseFloat($(`#grnItemUnitTdsInput_${rowNo}`).val()) : 0;

            let tds_value = basicPrice * (tds / 100);

            let totalItemPrice = basicPrice + cgst + sgst + igst - tds_value;

            console.log(totalItemPrice, cgst, sgst, igst, tds_value);

            $(`#grnItemUnitTDTotal_${rowNo}`).html(totalItemPrice.toFixed(2));
            $(`#grnItemTDSValue_${rowNo}`).val(tds_value.toFixed(2));



            calculateGrandTotalAmount();
        }

        function calculateGrandTotalAmount() {
            let totalAmount = 0;
            let grandSubTotalAmt = 0;
            let TotalCGSt = 0;
            let TotalSGSt = 0;
            let TotalIGSt = 0;
            let TotalTds = 0;


            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemCgst").each(function() {
                TotalCGSt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemSgst").each(function() {
                TotalSGSt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".itemIgst").each(function() {
                TotalIGSt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(".ItemTotalTds").each(function() {
                TotalTds += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });

            $(`#totalInvoiceCGST`).val(TotalCGSt);
            $(`#totalInvoiceSGST`).val(TotalSGSt);
            $(`#totalInvoiceIGST`).val(TotalIGSt);
            $(`#totalInvoiceTDS`).val(TotalTds);

            let ToTalcgst = (parseFloat($(`#totalInvoiceCGST`).val()) > 0) ? parseFloat($(`#totalInvoiceCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalInvoiceSGST`).val()) > 0) ? parseFloat($(`#totalInvoiceSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalInvoiceIGST`).val()) > 0) ? parseFloat($(`#totalInvoiceIGST`).val()) : 0;
            let ToTalinvTds = (parseFloat($(`#totalInvoiceTDS`).val()) > 0) ? parseFloat($(`#totalInvoiceTDS`).val()) : 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - ToTalinvTds;


            $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
            $("#tdGrandTotal").html(totalAmount.toFixed(2));
        }

        $(document).on("keyup", "#totalInvoiceCGST", function() {
            let grandSubTotalAmt = 0;
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            let ToTalcgst = (parseFloat($(`#totalInvoiceCGST`).val()) > 0) ? parseFloat($(`#totalInvoiceCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalInvoiceSGST`).val()) > 0) ? parseFloat($(`#totalInvoiceSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalInvoiceIGST`).val()) > 0) ? parseFloat($(`#totalInvoiceIGST`).val()) : 0;
            let ToTalinvTds = (parseFloat($(`#totalInvoiceTDS`).val()) > 0) ? parseFloat($(`#totalInvoiceTDS`).val()) : 0;

            let totalAmount = 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - ToTalinvTds;

            $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
            $("#tdGrandTotal").html(totalAmount.toFixed(2));

        });

        $(document).on("keyup", "#totalInvoiceSGST", function() {
            let grandSubTotalAmt = 0;
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            let ToTalcgst = (parseFloat($(`#totalInvoiceCGST`).val()) > 0) ? parseFloat($(`#totalInvoiceCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalInvoiceSGST`).val()) > 0) ? parseFloat($(`#totalInvoiceSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalInvoiceIGST`).val()) > 0) ? parseFloat($(`#totalInvoiceIGST`).val()) : 0;
            let ToTalinvTds = (parseFloat($(`#totalInvoiceTDS`).val()) > 0) ? parseFloat($(`#totalInvoiceTDS`).val()) : 0;

            let totalAmount = 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - ToTalinvTds;

            $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
            $("#tdGrandTotal").html(totalAmount.toFixed(2));
        });

        $(document).on("keyup", "#totalInvoiceIGST", function() {
            let grandSubTotalAmt = 0;
            $(".ItemInvoiceTotalPrice").each(function() {
                grandSubTotalAmt += (parseFloat($(this).val()) > 0) ? parseFloat($(this).val()) : 0;
            });
            let ToTalcgst = (parseFloat($(`#totalInvoiceCGST`).val()) > 0) ? parseFloat($(`#totalInvoiceCGST`).val()) : 0;
            let ToTalsgst = (parseFloat($(`#totalInvoiceSGST`).val()) > 0) ? parseFloat($(`#totalInvoiceSGST`).val()) : 0;
            let ToTaligst = (parseFloat($(`#totalInvoiceIGST`).val()) > 0) ? parseFloat($(`#totalInvoiceIGST`).val()) : 0;
            let ToTalinvTds = (parseFloat($(`#totalInvoiceTDS`).val()) > 0) ? parseFloat($(`#totalInvoiceTDS`).val()) : 0;

            let totalAmount = 0;

            totalAmount = grandSubTotalAmt + ToTalcgst + ToTalsgst + ToTaligst - ToTalinvTds;

            $("#totalInvoiceTotal").val(totalAmount.toFixed(2));
            $("#tdGrandTotal").html(totalAmount.toFixed(2));

        });





    });

    $(function() {

        $('#iframePreview').click(function() {

            if (!$('#iframe').length) {

                $('#iframeHolder').html('<iframe src="<?= COMP_STORAGE_URL ?>/grn-invoice/<?= $grnDetails["vendorDocumentFile"]  ?? "" ?>" id="grnInvoicePreviewIfram" width="100%" height="100%" <p>This browser does not support PDF!</p></iframe>');

            }

        });

    });
</script> -->
<!-- Rev grn-->
<script>
    $(document).on("click", ".reverseGRNIV", function(e) {
        e.preventDefault(); // Prevent default click behavior

        var dep_keys = $(this).data('id');
        var $this = $(this); // Store the reference to $(this) for later use

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
                $.ajax({
                    type: 'POST',
                    data: {
                        dep_keys: dep_keys,
                        dep_slug: 'reverseGRNIV'
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
    $(document).on("click", '#iframePreview', function() {
        if (!$('#iframe').length) {
            var src = $('iframe#grnInvoicePreviewIfram').attr('src');
            $('#iframeHolder').html(
                '<iframe src="' + src + '" id="grnInvoicePreviewIfram" width="100%" height="100%">' +
                '<p>This browser does not support PDFs!</p>' +
                '</iframe>'
            );
        }
    })
</script>