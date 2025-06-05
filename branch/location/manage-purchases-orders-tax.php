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


if (isset($_GET["approve"])) {
    //console(($_GET["approve"]));
    ///exit();
    $po_id = base64_decode($_GET["approve"]);
    $po = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` as po, `" . ERP_VENDOR_DETAILS . "` as vendor WHERE po.vendor_id=vendor.vendor_id  AND `po_id`=$po_id ";
    $poGet = queryGet($po);
    $status = 9;
    $update = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$po_id";
    //console($poGet['data']);

    $updatePO = queryUpdate($update);
    $check_service = queryGet("SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE `ref_no` = '" . $po_no . "'", true);
    foreach ($check_service['data'] as $data) {
        $s_po_id = $data['po_id'];
        $update_service = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$s_po_id");
    }


    $encodePo_id = base64_encode($po_id);
    $ref_no = $poGet['data']['ref_no'];
    $del_date = $poGet['data']['delivery_date'];
    $total_amount = $poGet['data']['totalAmount'];
    $po_no = $poGet['data']['po_number'];
    $to = $poGet['data']['vendor_authorised_person_email'];
    $sub = 'PO approved';
    $user_name = $poGet['data']['vendor_authorised_person_name'];
    $trade_name = $poGet['data']['trade_name'];
    $gst = $poGet['data']['vendor_gstin'];
    //   $url=LOCATION_URL;
    //   $user_id=$POST['email'];
    //   $password=$adminPassword;
    $msg = '
 
                <div>
                <div><strong>Dear ' . $user_name . ',</strong>';
    if ($companyCountry == '103') {
        $msg .= '(GSTIN:' . $gst . ')</div>';
    }
    $msg .= '<p>
                Your Purchase Order (' . $po_no . ') has been approved.
                </p>
                <strong>
                    PO details:
                </strong>
                <div style="display:grid">
                    <span>
                        Refernce Number: ' . $ref_no . '
                    </span>
                    <span>
                       Total Amount: ' . $total_amount . '
                    </span>
                    <span>
                        Delivery Date: <strong>' . $del_date . '</strong>
                    </span>
                </div>
               
                <div style="display:grid">
                    Best regards for, <span><b>' . $trade_name . '</b></span>
                </div>
                
                <p>
                <a href="' . BASE_URL . 'branch/location/branch-po-view.php?po_id=' . $encodePo_id . '" style="background: #174ea6;padding: 8px;color: white;text-decoration: none;border-radius: 5px;"><img width="15" src="' . BASE_URL . 'public/storage/invoice-icon/invoice.png" /> View PO</a>
                
                </p>
                </div>
                        ';



    $emailReturn = SendMailByMySMTPmailTemplate($to, $sub, $msg, $tmpId = null);


    if ($emailReturn == true) {
        //     $status = 9;
        //    echo $update = "UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=$status WHERE `po_id`=$po_id";
        // exit();




        swalToast('success', 'email sent', $_SERVER['PHP_SELF']);
    } else {
        swalToast('warning', 'mail not sent', $_SERVER['PHP_SELF']);
    }
}


if (isset($_GET["reject"])) {
    //console(($_GET["reject"]));
    ///exit();
    $po_id = base64_decode($_GET["reject"]);
    $update = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`= 17 WHERE `po_id` = $po_id");
    if ($update['status'] == 'success') {
        swalToast('success', 'PO Rejected', BASE_URL . "branch/location/manage-purchases-orders.php");
    } else {
        swalToast('warning', 'PO Rejection Failed', BASE_URL . "branch/location/manage-purchases-orders.php");
    }
}


if (isset($_GET["close-po"])) {
    // exit();
    $po_id = base64_decode($_GET['close-po']);
    $update = queryUpdate("UPDATE `" . ERP_BRANCH_PURCHASE_ORDER . "` SET `po_status`=10 WHERE `po_id`=$po_id");
    swalToast($update["status"], $update["message"], $_SERVER['PHP_SELF']);
}

$pageName = basename($_SERVER['PHP_SELF'], '.php');
if (!isset($_COOKIE["cookiesPoall"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiesPoall", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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
        'name' => 'PO Number',
        'slag' => 'po_number',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Code',
        'slag' => 'vendor.vendor_code',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Icon',
        'slag' => 'vendorIcon',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'icon'
    ],
    [
        'name' => 'Vendor Name',
        'slag' => 'vendor.trade_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Reference Number',
        'slag' => 'ref_no',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'PO Date',
        'slag' => 'po_date',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],

    [
        'name' => 'Total Item',
        'slag' => 'so.totalItems',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Total Amount',
        'slag' => 'so.totalAmount',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Delivery Date',
        'slag' => 'delivery_date',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Use type',
        'slag' => 'use_type',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Inco Type',
        'slag' => 'inco_type',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Ship Address',
        'slag' => 'shipLoc',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Bill Address',
        'slag' => 'bill_location',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Created By',
        'slag' => 'so.created_by',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'stat.label',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
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
<!-- <div class="content-wrapper report-wrapper is-stock-new vitwo-alpha-global"> -->
<div class="content-wrapper report-wrapper is-sales-orders is-purchase-order vitwo-alpha-global">

    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <?php
            // console ($_COOKIE);

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
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center header-space"
                                        style="width:100%">
                                        <div class="left-block">
                                            <div class="label-select">
                                                <h3 class="card-title mb-0">Manage Purchase Orders</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
                                            <?php require_once("components/mm/manage-po-tabs.php"); ?>
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

                                            <a href="purchase-order-creation.php?po-creation"
                                                class="btn btn-create waves-effect waves-light" type="button">
                                                <ion-icon name="add-outline" role="img" class="md hydrated"
                                                    aria-label="add outline"></ion-icon>
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
                                                                        $operators = ["CONTAINS", "NOT CONTAINS", "<", ">", ">=", "<=", "=", "!=", "BETWEEN"];

                                                                        foreach ($columnMapping as $columnIndex => $column) {
                                                                            if ($columnIndex === 0 || $column['name'] == 'Bill Address' || $column['name'] == 'Ship Address' || $column['name'] == 'Vendor Icon') {
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

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    </section>
    <!-- /.content -->


    <!-- Global View start-->

    <div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel"
        data-backdrop="true" aria-modal="true">
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


                            <div id="exchangeNum">

                            </div>

                            <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span
                                    id="po-numbers"> </span></p>
                        </div>
                        <div class="right">
                            <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span
                                    id="cus_name"></span></p>
                            <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span
                                    id="default_address"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                            <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview"
                                aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                            <button class="nav-link classicview-btn classicview-link" id="nav-classicview-tab"
                                data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button"
                                role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon
                                    name="apps-outline"></ion-icon>Preview</button>
                            <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-trail" data-ccode="" type="button" role="tab"
                                aria-controls="nav-trail" aria-selected="false"><ion-icon
                                    name="time-outline"></ion-icon>Trail</button>
                        </div>
                    </nav>
                    <div class="tab-content global-tab-content" id="nav-tabContent">

                        <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview"
                            role="tabpanel" aria-labelledby="nav-overview-tab">

                            <div class="d-flex nav-overview-tabs">
                            </div>

                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-12 col-12">
                                    <div class="items-table">
                                        <h4>Vendor Details</h4>
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
                                                        <p> <ion-icon name="mail-outline"></ion-icon><span
                                                                id="custEmail"> </span></p>
                                                        <p> <ion-icon name="call-outline"></ion-icon><span
                                                                id="custPhone"></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="items-table">
                                        <h4>Other Details</h4>
                                        <div class="other-info">

                                            <!-- <div class="details">
                                                <label for="">Reference No</label>
                                                <p id="referenceNo"></p>
                                            </div>
                                             -->
                                            <div class="details">
                                                <label for="">Delivery Date</label>
                                                <p id="delvDate"></p>
                                            </div>
                                            <div class="details">
                                                <label for=""> po Creation Date</label>
                                                <p id="poCreationDate"> </p>
                                            </div>
                                            <div class="details">
                                                <label for="">Valid Till</label>
                                                <p id="validTill"></p>
                                            </div>

                                            <div class="details">
                                                <label for="">Use Types</label>
                                                <p id="use_type"></p>
                                            </div>
                                            <div class="details">
                                                <label for="">Po Type</label>
                                                <p id="po_type"></p>
                                            </div>
                                            <div class="details" id="parentPo">
                                                <label for="">Parent Po</label>
                                                <p id="parentPoNo"></p>
                                            </div>
                                            <div class="details">
                                                <label for="">Functional Area</label>
                                                <p id="funcnArea"></p>
                                            </div>

                                            <div class="details">
                                                <label for="">Vendor currency </label>
                                                <p id="vendorCur"></p>
                                            </div>

                                            <div class="details">
                                                <label for="">Reference Document Link</label>
                                                <p>: <a href="#" id="refDoc"></a></p>
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
                                                            <p class="code" id="cardPoNo"></p>
                                                            <p class="name" id="referenceNo"></p>
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
                                                            <div id="gstlabels">
                                                                <p id="just_gst">GST</p>
                                                                <p id="igstP">IGST</p>
                                                                <div id="csgst" style="display: none;">
                                                                    <p>CGST</p>
                                                                    <p>SGST</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="right-info">
                                                        <div class="item-info">
                                                            <p id="sub_total"></p>
                                                            <div id="gstVals">
                                                                <p id="igst"></p>
                                                                <div id="csgstVal">
                                                                    <p id="cgstVal"></p>
                                                                    <p id="sgstVal"></p>
                                                                </div>
                                                                <div id="just_gstValDisplay">
                                                                    <p id="just_gstVal">
                                                                        0.00
                                                                    </p>
                                                                </div>
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

                                            <!-- <div class="items-table">
                                                <div class="details">
                                                    <label for="">Remarks</label>
                                                    <p id="remark"></p>
                                                </div>
                                            </div>
                                             -->
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
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Code
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Name
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Qty
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                    Remaining Qty</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">
                                                    Currency</div>
                                                <div
                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">
                                                    Unit Price</div>
                                                <div
                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">
                                                    Base Amount</div>
                                                <div id="gstlabel"
                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">
                                                </div>
                                                <div
                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">
                                                    <span id="gstAmtlabel"></span>
                                                    Amount(<span id="currencyHead"></span>)
                                                </div>
                                                <div
                                                    class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td text-right">
                                                    Total Amount</div>
                                            </div>
                                            <div id="itemTableBody">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="tab-pane classicview-pane fade" id="nav-classicview" role="tabpanel"
                            aria-labelledby="nav-classicview-tab">
                            <div class="print-tc-btn">
                                <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrint"
                                    target="_blank">Print</a>
                                <div class="check-input" id="checkboxDiv">
                                    <input type="checkbox" id="printChkbox">
                                    <label for="">Print With Terms and Conditions</label>
                                </div>
                            </div>
                            <div class="card classic-view bg-transparent">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-trail" role="tabpanel" aria-labelledby="nav-trail-tab">
                            <div class="inner-content">
                                <div class="audit-head-section mb-3 mt-3 ">
                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by
                                        </span><span class="created_by_trail"></span></p>
                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated
                                            by</span><span class="updated_by"> </span></p>
                                </div>
                                <hr>
                                <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent">


                                </div>
                                <div class="modal fade right audit-history-modal" id="innerModal" role="dialog"
                                    aria-labelledby="innerModalLabel" aria-modal="true">
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

</div>

</td>

</tr>


<!-----add form modal start --->
<div class="modal fade hsn-dropdown-modal" id="addToLocation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    data-backdrop="true" aria-hidden="true">
    <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <form method="POST" action="">
                    <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                    <input type="hidden" id="item_id" name="item_id" value="">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card goods-creation-card so-creation-card po-creation-card"
                                style="height: auto;">
                                <div class="card-header">
                                    <h4>Storage Details</h4>
                                </div>
                                <div class="card-body goods-card-body others-info vendor-info so-card-body"
                                    style="height: auto;">
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
                                                        <select id="minTime" name="minTime"
                                                            class="select2 form-control">
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

                                                        <input type="text" name="minRemainSelfLife"
                                                            class="form-control">

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-input">
                                                        <label class="label-hidden" for="">Max Time Unit</label>
                                                        <select id="maxTime" name="maxTime"
                                                            class="select2 form-control">
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

                            <div class="card goods-creation-card so-creation-card po-creation-card"
                                style="height: auto;">

                                <div class="card-header">

                                    <h4>Pricing and Discount

                                        <span class="text-danger">*</span>

                                    </h4>

                                </div>

                                <div class="card-body goods-card-body others-info vendor-info so-card-body"
                                    style="height: auto;">

                                    <div class="row">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                            <div class="row goods-info-form-view customer-info-form-view">

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Target price</label>

                                                        <input step="0.01" type="number" name="price"
                                                            class="form-control price" id="exampleInputBorderWidth2"
                                                            placeholder="price">

                                                    </div>

                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                        <label for="">Max Discount</label>

                                                        <input step="0.01" type="number" name="discount"
                                                            class="form-control discount" id="exampleInputBorderWidth2"
                                                            placeholder="Maximum Discount">

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <button class="btn btn-primary save-close-btn btn-xs float-right add_data"
                                    value="add_post">Submit</button>
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

<script>
    function cleardiv() {
        $('#igstP').hide();
        $('#csgst').hide();
        $('#csgstVal').hide();
        $('#sgstVal').hide();
        $('#cgstVal').hide();
        $('#igst').hide();
    }
    // let csvContent;
    // let csvContentBypagination;
    let data;
    let columnMapping = <?php echo json_encode($columnMapping); ?>;


    $(document).ready(function() {
        let indexValues = [];
        let dataTable;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
                "lengthMenu": [10, 25, 50, 100, 200, 250],
                "ordering": false,
                info: false,
                "initComplete": function(settings, json) {
                    $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
                },

                buttons: [{
                    extend: 'collection',
                    text: '<ion-icon name="download-outline"></ion-icon> Export',
                    buttons: [{
                        extend: 'csv',
                        filename: '<?php echo $newFileName ?>',
                        text: '<ion-icon name="document-outline" class="ion-csv"></ion-icon> Excel',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                        }
                    }]
                }],
                // select: true,
                "bPaginate": false,
            });

        }
        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

        initializeDataTable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
            let fdate = "<?php echo $f_date; ?>";
            let to_date = "<?php echo $to_date; ?>";
            let comid = <?php echo $company_id; ?>;
            let locId = <?php echo $location_id; ?>;
            let bId = <?php echo $branch_id; ?>;

            let notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-po-all.php",
                dataType: 'json',
                data: {
                    act: 'managePoall',
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
                        dataTable.column(length - 1).visible(true);

                        $.each(responseObj, function(index, value) {

                            let status = '';
                            if (value["stat.label"] == "open") {
                                status = '<p class="status-bg status-open">Open</p>';
                            } else if (value["stat.label"] == "pending") {
                                status = '<p class="status-bg status-pending">Pending</p>';
                            } else if (value["stat.label"] == "exceptional") {
                                status = '<p class="status-bg status-exceptional">Exceptional</p>';
                            } else if (value["stat.label"] == "closed") {
                                status = '<p class="status-bg status-closed">Closed</p>';
                            } else if (value["stat.label"] == "rejected") {
                                status = '<p class="status-bg status-closed">Rejected</p>';
                            }

                            let actions = ``;
                            if (value.poStatus == 14) {
                                actions = `
                                    <li>
                                        <button class="editModal" data-toggle="modal" data-target="#editModal" data-id=${value.poId} data-po=${value.pono}><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                    </li>
                                    <li>
                                        <button class="deleteModal" data-toggle="modal" data-id=${value.poId} data-po=${value.pono}><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                                    </li>`;
                            }


                            // $('#item_id').val(value.itemId);
                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.poId}">${value.po_number}</a>`,
                                value["vendor.vendor_code"],
                                ` <div class="img-fluid avatar rounded-circle d-flex justify-content-center align-items-center" style="border: 1px solid grey;">${value.vendorIcon}</div>`,
                                `<p class="pre-normal">${value["vendor.trade_name"]}</p>`,
                                value.ref_no,
                                formatDate(value.po_date),
                                decimalQuantity(value["so.totalItems"]),
                                decimalAmount(value["so.totalAmount"]), // this Currency With Value From php
                                formatDate(value["delivery_date"]),
                                value.use_type,
                                value.inco_type,
                                `<p class="pre-normal">${value.shipLoc}</p>`,
                                `<p class="pre-normal">${value.bill_location}</p>`,
                                value["so.created_by"],
                                status,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                        ${actions}
                                        <li>
                                            <button class="soModal" data-id=${value.poId}><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                        </li>
                                  
                                    </ul>
                                   
                                </div>`,

                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);
                        let checkboxSettings = Cookies.get('cookiesPoall');

                        if (checkboxSettings) {
                            let checkedColumns = JSON.parse(checkboxSettings);

                            $(".settingsCheckbox_detailed").each(function(index) {
                                let columnVal = $(this).val();
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
                    sql_data_checkbox: Cookies.get('cookiesPoall')
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
            let maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit);

        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a ", function(e) {
            e.preventDefault();
            let page_id = $(this).attr('id');
            let limitDisplay = $(".custom-select").val();

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
                    let value4 = $(`#value4_${columnIndex}`).val() ?? "";
                    if (columnSlag === 'po_date') {
                        values = value2;
                    } else if (columnSlag === 'delivery_date') {
                        values = value4;
                    }

                    if ((columnSlag === 'po_date' || columnSlag === 'delivery_date') && operatorName == "BETWEEN") {
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
        $(document).on("click", ".ion-fullliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-po-all.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    formDatas: formInputs,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiesPoall')
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
            let columnMapping = <?php echo json_encode($columnMapping); ?>;

            let indexValues = [];

            function toggleColumnVisibility(columnIndex, checkbox) {
                let column = dataTable.column(columnIndex);
                column.visible(checkbox.checked);

            }

            $("input[name='settingsCheckbox[]']").change(function() {
                let columnVal = $(this).val();
                console.log(columnVal);

                let index = columnMapping.findIndex(function(column) {
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
            event.preventDefault();
            $('#btnSearchCollpase_modal').modal('hide');
            let tablename = $("#tablename").val();
            let pageTableName = $("#pageTableName").val();
            let settingsCheckbox = [];
            let formData = {};
            $(".settingsCheckbox_detailed").each(function() {
                if ($(this).prop('checked')) {
                    let chkBox = $(this).val();
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
                        act: 'mangePoall',
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
            if (columnName.trim() === 'PO Date') {
                inputId = "value2_" + columnIndex;
            }else if(columnName.trim() === 'Delivery Date'){
                inputId = "value4_" + columnIndex;
            }

            if ((columnName.trim() === 'PO Date' || columnName.trim() === 'Delivery Date') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input m-input2 " id="${(inputId)}" placeholder="Enter Keyword" value="">`);
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



<?php
require_once("../common/footer2.php");
?>

<script>
    $(document).ready(function() {
        // main data model
        $(document).on("click", ".soModal", function() {

            $('#viewGlobalModal').modal('show');
            $('.ViewfirstTab').tab('show');
            $(".classic-view").html('');
            let poId = $(this).data('id');
            // console.log(poId);
            // var printChkbox = document.getElementById("printChkbox");
            document.getElementById('printChkbox').addEventListener('change', function() {
                if (this.checked) {
                    $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print-taxcomponents.php?poId=${(poId)}&& printChkbox`);
                } else {
                    $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print-taxcomponents.php?poId=${(poId)}`);
                }
            })
            $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print-taxcomponents.php?poId=${(poId)}`);

            // var hhValue = so_no + 'test';

            $.ajax({
                type: "GET",
                url: "ajaxs/modals/mm/ajax-manage-purchases-orders-modal-tax.php",
                dataType: 'json',
                data: {
                    act: 'modaldata',
                    po_id: poId,
                },
                beforeSend: function() {
                    // $('.item-cards').remove();
                    $('#itemTableBody').html('');
                    $("#exchangeNum").html('');

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
                    let responseObj = value.data;
                    let itemsObj = responseObj.items;
                    var country_labels = responseObj.country_labels;
                    var country_fields = country_labels.fields;
                    var place_of_supply = country_labels.place_of_supply;
                    // checkbox action
                    // checkbox action

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
                    console.log(itemsObj)
                    if (responseObj.taxComponents) {
                        var taxComponents = JSON.parse(responseObj.taxComponents);

                    }
                    if (value.printChkTC === '1' && value.showChkbox == 1) {
                        $('#printChkbox').prop('checked', true);
                        $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print-taxcomponents.php?poId=${(poId)}&& printChkbox`);
                    } else {
                        $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print-taxcomponents.php?poId=${(poId)}`);
                        $('#checkboxDiv').hide();
                    }



                    if (responseObj.companyCurrency != responseObj.vendorCur) {
                        let obj = `<p class="info-detail amount">
                    <ion-icon name="wallet-outline"></ion-icon>
                    <span class="amount-value" >${responseObj.vendorCur} ${decimalAmount(responseObj.dataObj.totalAmount * responseObj.dataObj.conversion_rate)}</span>
                    </p>
                    <span class="amount-in-words">( ${responseObj.currecy_name_wordsVendorCur} )</span>`;

                        $("#exchangeNum").html(obj);

                    }

                    // var delivery_qty = [];
                    // var deliveryStatus = [];
                    // var del_date = [];

                    // $.each(itemsObj, function(index, item) {
                    //     delivery_qty.push(item.del_qty);
                    //     deliveryStatus.push(item.deliveryStatus);
                    //     del_date.push(item.delivery_date);
                    // });
                    let responsedataobj = responseObj.dataObj;
                    let address = `
                    ${responsedataobj.vendor_business_flat_no ? `${responsedataobj.vendor_business_flat_no}, ` : ''}
                    ${responsedataobj.vendor_business_building_no ? `${responsedataobj.vendor_business_building_no}, ` : ''}
                    ${responsedataobj.vendor_business_street_name ? `${responsedataobj.vendor_business_street_name}, ` : ''}
                    ${responsedataobj.vendor_business_location ? `${responsedataobj.vendor_business_location}, ` : ''}
                    ${responsedataobj.vendor_business_city ? `${responsedataobj.vendor_business_city}, ` : ''}
                    ${responsedataobj.vendor_business_district ? `${responsedataobj.vendor_business_district}, ` : ''}
                    ${responsedataobj.vendor_business_state ? `${responsedataobj.vendor_business_state}, ` : ''}
                    ${responsedataobj.vendor_business_pin_code ? `${responsedataobj.vendor_business_pin_code}, ` : ''}
                    ${responsedataobj.vendor_business_country ? `${responsedataobj.vendor_business_country}` : ''}`.trim();
                    address = address.replace(/,\s*$/, '');
                    $(".left #amount").html(responseObj.companyCurrency + " " + decimalAmount(responseObj.dataObj.totalAmount));
                    $("#amount-words").html("(" + responseObj.currecy_name_words + ")");
                    $("#po-numbers").html(responseObj.dataObj.po_number);
                    $(".right #cus_name").html(responseObj.dataObj.vendor_name);
                    $("#default_address").html(address);
                    $(".nav-overview-tabs").html(responseObj.navBtn);
                    // $('.classicview-btn').attr("data-id", responseObj.so_IdBase);
                    // $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print.php?so_id=${responseObj.so_IdBase}`);
                    $(".created_by_trail").html(responseObj.created_by + "<span class='font-bold text-normal'> on </span>" + responseObj.created_at);
                    $(".updated_by").html(responseObj.updated_by + "<span class='font-bold text-normal'> on </span>" + responseObj.updated_at);

                    $('.auditTrail').attr("data-ccode", responseObj.dataObj.po_number);

                    // vendor details section 
                    $("#custName").html(responseObj.dataObj.vendor_name);
                    $("#custCode").html(responseObj.dataObj.vendor_code);
                    $("#custgst").html(responseObj.dataObj.vendor_gstin);
                    $("#custpan").html(responseObj.dataObj.vendor_pan);
                    $("#billAddress").html(responseObj.billAddress);
                    $("#shipAddress").html(responseObj.shipAddress);
                    $("#custEmail").html(responseObj.dataObj.vendor_email);
                    $("#custPhone").html(responseObj.dataObj.vendor_phone);

                    //others details section
                    $("#delvDate").html(" : " + formatDate(responseObj.dataObj.delivery_date));
                    $("#poCreationDate").html(" : " + formatDate(responseObj.dataObj.po_date));
                    $("#validTill").html(" : " + formatDate(responseObj.dataObj.validityperiod));
                    $("#vendorCur").html(" : " + responseObj.vendorCur);
                    $("#use_type").html(" : " + responseObj.dataObj.use_type);

                    if (responseObj.dataObj.use_type == "servicep") {
                        $("#parentPoNo").html(" : " + responseObj.parentPoNo);
                        $("#parentPo").show();
                    } else {
                        $("#parentPo").hide();
                    }

                    $("#po_type").html(` :  ${capFirstLetter(responseObj.dataObj.po_type)}/${responseObj.dataObj.inco_type}`);
                    $("#funcnArea").html(" : " + responseObj.functionalAreaName);

                    // card Section
                    $("#cardPoNo").html(responseObj.dataObj.po_number);
                    $("#referenceNo").html(responseObj.dataObj.ref_no);

                    if (responseObj.dataObj.po_attachment !== null && responseObj.dataObj.po_attachment !== undefined && responseObj.dataObj.po_attachment !== "") {

                        var link = $("<a></a>").attr("href", `<?= COMP_STORAGE_URL . "/others/" ?>${responseObj.dataObj.po_attachment}`).attr("download", responseObj.dataObj.po_attachment).css("text-decoration", "underline").text("Download");
                    } else {
                        var link = $("<a></a>").attr("href", "#").text("No Attached File");
                    }
                    // Set the link inside the #refDoc element
                    $("#refDoc").html("").append(link);

                    // let igst = 0;
                    // let cgst = 0;
                    // let sgst = 0;

                    // let subTotal = responseObj.allSubTotal;
                    // let totalTax = responseObj.dataObj.total_gst;
                    // let totalAmt = responseObj.dataObj.totalAmount;
                    // console.log(responseObj.dataObj.total_igst);


                    // if (responseObj.dataObj.total_sgst > 0 || responseObj.dataObj.total_cgst > 0) {
                    //     cgst = responseObj.dataObj.total_cgst;
                    //     sgst = responseObj.dataObj.total_sgst;
                    // }

                    // if (responseObj.dataObj.total_igst > 0) {
                    //     igst = responseObj.dataObj.total_igst;
                    // }

                    // // card details section
                    // $("#totalItem").html(responseObj.dataObj.totalItems + " " + "Items");
                    // $("#sub_total").html(responseObj.companyCurrency + " " + parseFloat(subTotal).toFixed(2));
                    // $("#total_amount").html(responseObj.companyCurrency + " " + parseFloat(totalAmt).toFixed(2));

                    // $("#igst").hide();
                    // if (responseObj.dataObj.total_igst > 0) {
                    //     $("#igst").html(responseObj.companyCurrency + " " + parseFloat(igst).toFixed(2));
                    //     $("#igst").show();

                    // } else if (responseObj.dataObj.total_sgst > 0 || responseObj.dataObj.total_cgst > 0) {
                    //     $("#csgst").css("display", "block");
                    //     $("#igstP").hide();
                    //     $("#igst").hide();
                    //     $("#cgstVal").html(responseObj.companyCurrency + " " + parseFloat(cgst).toFixed(2));
                    //     $("#sgstVal").html(responseObj.companyCurrency + " " + parseFloat(sgst).toFixed(2));
                    // } else {
                    //     $("#igstP").hide();
                    //     $("#igst").hide();
                    //     $("#csgst").hide();
                    // }
                    let taxName = responseObj.taxName;

                    let just_gst = 0;

                    let igst = responseObj.dataObj.total_igst || 0; // Default to 0 if igst is undefined
                    let cgst = responseObj.dataObj.total_cgst || 0; // Default to 0 if cgst is undefined
                    let sgst = responseObj.dataObj.total_sgst || 0; // Default to 0 if sgst is undefined
                    console.log("IGST " + igst + " Cgst " + cgst + " Sgst " + sgst);
                    // Display card details section
                    $("#totalItem").html(decimalQuantity(responseObj.dataObj.totalItems) + " Items");
                    $("#sub_total").html(responseObj.companyCurrency + " " + (responseObj.allSubTotal));
                    $("#total_amount").html(responseObj.companyCurrency + " " + decimalAmount(responseObj.dataObj.totalAmount));

                    // Logic to show/hide GST elements
                    // if (igst > 0) {
                    //     $("#igst").html(responseObj.companyCurrency + " " + decimalAmount(igst));
                    //     $("#igst").show();
                    //     $("#csgst").hide();
                    // } else if (cgst > 0 || sgst > 0) {
                    //     $("#cgstVal").html(responseObj.companyCurrency + " " + decimalAmount(cgst));
                    //     $("#sgstVal").html(responseObj.companyCurrency + " " + decimalAmount(sgst));
                    //     $("#csgst").css("display", "block");
                    //     $("#igst").hide();
                    // } else {
                    //     $("#igst").hide();
                    //     $("#csgst").hide();
                    // }



                    if (responseObj.countryCode === '103') {
                        // For India
                        if (igst > 0) {
                            $("#igst").html(responseObj.companyCurrency + " " + decimalAmount(igst));
                            $("#igst").show();
                            $("#csgst").hide();
                            $("#just_gst").hide();
                            $("#just_gstVal").hide();
                        } else if (cgst > 0 || sgst > 0) {
                            $("#cgstVal").html(responseObj.companyCurrency + " " + decimalAmount(cgst));
                            $("#sgstVal").html(responseObj.companyCurrency + " " + decimalAmount(sgst));
                            $("#csgst").css("display", "block");
                            $("#igst").hide();
                            $("#just_gst").hide();
                            $("#just_gstVal").hide();
                        } else {
                            $("#igst").hide();
                            $("#csgst").hide();
                            $("#just_gst").hide();
                            $("#just_gstVal").hide();
                        }
                        $("#igstP").toggle(igst > 0);


                    } else {
                        // Clear existing labels and values
                        $("#gstlabels").empty();
                        $("#gstVals").empty();

                        if (taxComponents && taxComponents.length > 0) {

                            taxComponents.forEach(component => {
                                // Append labels to gstlabels div
                                const taxLabel = `<p class="label">${component.gstType}</p>`;
                                $("#gstlabels").append(taxLabel);

                                // Append values to gstVals div
                                const taxValue = `
                <p class="gstValues">
                    ${responseObj.companyCurrency} ${decimalAmount(component.taxAmount)}
                </p>`;
                                $("#gstVals").append(taxValue);
                            });
                        }
                    }

                    $("#currencyHead").html(responseObj.companyCurrency);




                    $.each(itemsObj, function(index, val) {
                        let td = ` <div class="row body-state-table">
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.itemCode}</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${val.itemName}">${val.itemName}</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${(val.qty)}</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${(val.remainingQty)}</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${responseObj.companyCurrency}</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${(val.unitPrice)}</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${(val.subTotal)}</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${(val.tax)}%</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${(val.gstAmount)}</div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-right">${responseObj.companyCurrency} ${(val.itemTotalAmount)}</div>
                                                    </div>
                                                    `;

                        $("#itemTableBody").append(td);
                        // alert(val.currency);
                    });
                    $("#gstlabel").html(taxName + '(%)')
                    $("#gstAmtlabel").html(taxName);
                    $("#globalModalLoader").remove();

                    // $('.closeSoBtn').attr("id", value.so_id + "_" + value.soNo);
                },
                complete: function() {
                    $("#globalModalLoader").remove();

                },
                error: function(error) {
                    console.log(error);
                }
            });

            $.ajax({
                type: "GET",
                url: "ajaxs/modals/mm/ajax-manage-purchases-orders-modal-tax.php",
                data: {
                    act: 'classicview',
                    po_id: poId,
                },
                success: function(response) {
                    // console.log(response);
                    $(".classic-view").html(response);

                },
                error: function(error) {
                    console.log(error);
                }
            });

        });
        // edit event
        $(document).on("click", ".editModal", function() {
            let poId = $(this).data('id');
            let poNo = $(this).data('po');
            let url = `<?= LOCATION_URL ?>purchase-order-creation.php?edit=${btoa(poId)}`;
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You want to Edit this Purchase Order ${poNo} ?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to action page for edit the purchase order
                    window.location.href = url;
                }
            });
        });
        // delete event
        $(document).on('click', '.deleteModal', function(e) {
            let poId = $(this).data('id');
            let poNo = $(this).data('po');
            Swal.fire({
                icon: 'warning',
                title: 'Are you sure?',
                text: `You want to delete this Purchase Order ${poNo} ?`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    // send request to server
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            act: 'deletepo',
                            po_id: poId
                        },
                        url: 'ajaxs/modals/mm/ajax-manage-purchases-orders-modal.php',
                        beforeSend: function() {},
                        success: function(response) {
                            // handel response from server
                            console.log(response);
                            // swal toast to show the response
                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000
                            });
                            Toast.fire({
                                // show response to user
                                icon: response.status,
                                title: '&nbsp;' + response.message
                            }).then(function() {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });
    });
</script>