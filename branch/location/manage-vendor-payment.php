<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");


// console($_SESSION);

if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}

if (isset($_POST["visit"])) {
    $newStatusObj = VisitBranches($_POST);
    redirect(BRANCH_URL);
}

if (isset($_POST["createdata"])) {
    $addNewObj = createDataBranches($_POST);
    if ($addNewObj["status"] == "success") {
        $branchId = base64_encode($addNewObj['branchId']);
        redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
        swalToast($addNewObj["status"], $addNewObj["message"]);
        // console($addNewObj);
    } else {
        swalToast($addNewObj["status"], $addNewObj["message"]);
    }
}

if (isset($_POST["editdata"])) {
    $editDataObj = updateDataBranches($_POST);

    swalToast($editDataObj["status"], $editDataObj["message"]);
}

// if (isset($_POST["add-table-settings"])) {
//     $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
//     swalToast($editDataObj["status"], $editDataObj["message"]);
// }

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$grnObj = new GrnController();
$BranchSoObj = new BranchSo();
$fetchInvoiceByCustomer = $grnObj->fetchGRNInvoice()['data'];


if (isset($_POST['addNewSOFormSubmitBtn'])) {
    // console($_POST);
    // exit;
    $addBranchSo = $BranchSoObj->addBranchSo($_POST);
    // console($addBranchSo);
    if ($addBranchSo['status'] == "success") {
        $addBranchSoItems = $BranchSoObj->addBranchSoItems($_POST, $addBranchSo['lastID']);
        //console($addBranchSoItems);
        if ($addBranchSoItems['status'] == "success") {
            // swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
            swalToast($addBranchSoItems["status"], $addBranchSoItems["message"], $_SERVER['PHP_SELF']);
        } else {
            swalToast($addBranchSoItems["status"], $addBranchSoItems["message"]);
        }
    } else {
        swalToast($addBranchSo["status"], $addBranchSo["message"]);
    }
}

$currentYear = date('Y');
$currentMonth = date('n');

if ($currentMonth >= 4) {
    $fyStart = $currentYear;
    $fyEnd = substr($currentYear + 1, -2);
} else {
    $fyStart = $currentYear - 1;
    $fyEnd = substr($currentYear, -2);
}

$current_year = "$fyStart-$fyEnd";
$previous_year = ($fyStart - 1) . '-' . substr($fyStart, -2);
$pre_previous_year = ($fyStart - 2) . '-' . substr($fyStart - 1, -2);



$pageName = basename($_SERVER['PHP_SELF'], '.php');

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = $fileNameWithoutExtension . 'download_all_' . $currentDateTime;
if (!isset($_COOKIE["cookieVendorPaymentPro"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieVendorPaymentPro", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    } else {
        for ($i = 0; $i < 5; $i++) {
            $isChecked = ($i < 5) ? 'checked' : '';
        }
    }
}

$columnMapping = [
    [
        'name' => '',
        'slag' => 'checkbox',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'Request Code',
        'slag' => 'req.code',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Code',
        'slag' => 'grniv.vendorCode',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor Name',
        'slag' => 'grniv.vendorName',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Vendor GSTIN',
        'slag' => 'grniv.vendorGstin',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Bank Name',
        'slag' => 'bank.vendor_bank_name',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Bank Account Number',
        'slag' => 'bank.vendor_bank_account_no',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Due Amount',
        'slag' => 'grniv.dueAmt',
        'icon' => '',
        'dataType' => 'number'
    ]
];


?>

<!-- <style>
    /* .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
    font-size: 12px;
  }

  .display-flex-gap {
    gap: 0 !important;
  }

  .card-body.others-info.vendor-info.so-card-body {
    height: 250px !important;
  }

  .fob-section div {
    align-items: center;
    gap: 3px;
  }

  .so-delivery-create-btn {
    display: flex;
    align-items: center;
    gap: 20px;
    max-width: 250px;
    margin-left: auto;
  }

  .customer-modal .modal-header {
    height: 250px !important;
  }


  .display-flex-space-between p {
    width: 77%;
    text-align: left;
  }

  @media (max-width: 575px) {

    .filter-serach-row {
      align-items: center;
      padding-top: 9px;
      margin-bottom: 0 !important;
    }

    .customer-modal .nav.nav-tabs li.nav-item a.nav-link {
      padding: 7px;
    }

    .card-body.others-info.vendor-info.so-card-body {
      height: auto !important;
    }

    .customer-modal .modal-header {
      height: 285px !important;
    }

    .customer-modal .nav.nav-tabs {
      top: 0 !important;
    }

  } */


    .content-wrapper table tr:nth-child(2n+1) td {
        background: #b5c5d3;
    }

    tfoot.individual-search tr th {
        padding: 5px !important;
        border-right: 1px solid #fff !important;
    }

    .vertical-align {
        vertical-align: middle;
    }

    /* .green-text {
    color: #14ca14 !important;
    font-weight: 600;
  }

  .red-text {
    color: red !important;
    font-weight: 600;
  } */

    .dataTables_scrollHeadInner tr th {
        position: sticky;
        top: -1px;
    }

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row {
        display: flex !important;
        align-items: center;
        justify-content: end;
    }

    /* div.dataTables_wrapper {
    overflow: hidden;
  } */

    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(1),
    div.dataTables_wrapper div.dataTables_filter,
    .dataTables_wrapper .row:nth-child(3) {
        padding: 10px 20px;
    }

    div.dataTables_wrapper div.dataTables_length select {
        width: 60% !important;
        appearance: none !important;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    .dataTables_scroll {
        position: relative;
        margin-bottom: 10px;
    }

    .dataTables_scroll::-webkit-scrollbar {
        visibility: hidden;
    }

    .dataTables_scrollBody tfoot th {
        background: none !important;
    }

    .dataTables_scrollHead {
        margin-bottom: 40px;
    }

    .dataTables_scrollBody {
        max-height: 75vh !important;
        height: 75% !important;
        overflow: scroll !important;
    }

    .dataTables_scrollFoot {
        position: absolute;
        top: 37px;
        height: 50px;
        overflow: scroll;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 10px;
    }

    div.dataTables_scrollFoot>.dataTables_scrollFootInner th {
        border: 0;
    }

    .dataTables_filter {
        padding-right: 0 !important;
    }

    div.dataTables_wrapper div.dataTables_paginate ul.pagination {
        padding: 0;
        border: 0;
    }

    .dt-top-container {
        display: flex;
        align-items: center;
        padding: 0 20px;
        gap: 20px;
    }

    .transactional-book-table tr td {
        white-space: pre-line !important;
    }

    .dataTables_length {
        margin-left: 4em;
    }

    a.btn.add-col.setting-menu.waves-effect.waves-light {
        position: absolute !important;
        display: flex;
        justify-content: space-between;
        top: 10px !important;
    }

    div.dataTables_wrapper div.dataTables_length label {
        margin-bottom: 0;
    }

    div.dataTables_wrapper div.dataTables_info {
        padding-left: 20px;
        position: relative;
        top: 0;
    }

    .dataTables_paginate {
        position: relative;
        right: 20px;
        bottom: 20px;
        margin-top: -15px;
    }

    .dt-center-in-div {
        display: block;
        /* order: 3; */
        margin-left: auto;
    }

    .dt-buttons.btn-group.flex-wrap button {
        background-color: #003060 !important;
        border-color: #003060 !important;
        border-radius: 7px !important;
    }

    /* .setting-row .col .btn.setting-menu {
    position: absolute !important;
    right: 255px;
    top: 10px;
  } */

    .dt-buttons.btn-group.flex-wrap {
        gap: 10px;
    }


    table.dataTable>thead .sorting:before,
    table.dataTable>thead .sorting:after,
    table.dataTable>thead .sorting_asc:before,
    table.dataTable>thead .sorting_asc:after,
    table.dataTable>thead .sorting_desc:before,
    table.dataTable>thead .sorting_desc:after,
    table.dataTable>thead .sorting_asc_disabled:before,
    table.dataTable>thead .sorting_asc_disabled:after,
    table.dataTable>thead .sorting_desc_disabled:before,
    table.dataTable>thead .sorting_desc_disabled:after {

        display: block !important;

    }

    .dataTable thead tr th,
    .dataTable tfoot.individual-search tr th {
        padding-right: 30px !important;
        border-right: 0 !important;
    }

    select.fy-dropdown {
        position: absolute;
        max-width: 100px;
        top: 14px;
        left: 255px;
    }

    .daybook-filter-list.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-end;
        position: relative;
        top: -35px;
        left: -75px;
        float: right;
    }

    .daybook-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .vendor-invoice-tab.filter-list {
        display: flex;
        gap: 7px;
        justify-content: flex-start;
        position: relative;
        top: 0;
        left: 0;
    }

    .vendor-invoice-tab.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    /* css for modal to hide  */

    .hidden-modal {
        display: none;
    }

    /* css for table content styling starts here */


    .gst-return-data table th {
        background: #c5ced6 !important;
        color: #000 !important;
        font-weight: 600;
    }

    .gst-return-data table td {
        background: #fff !important;
        border-bottom: 1px solid #cccccc12 !important;
        border-color: #a0a0a06e !important;
        padding: 7px 16px;
        font-weight: 600;
    }

    .gst-return-data table tr:nth-child(2n) td {
        background-color: #fff !important;
    }

    /* css for table content styling ends here */

    @media (max-width: 769px) {
        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: absolute;
            top: -39px;
            right: 60px;
        }

        .dt-buttons.btn-group.flex-wrap button {
            max-width: 60px;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: -10px;
        }


    }

    @media (max-width :575px) {
        .dataTables_scrollFoot {
            position: absolute;
            top: 28px;
        }

        .dt-top-container {
            display: flex;
            align-items: baseline;
            padding: 0 20px;
            gap: 20px;
            flex-direction: column-reverse;
            flex-wrap: nowrap;
        }

        .dataTables_length {
            margin-left: 0;
            margin-bottom: 1em;
        }

        select.fy-dropdown {
            position: absolute;
            max-width: 125px;
            top: 155px;
            left: 189px;
        }

        div.dataTables_wrapper div.dataTables_length select {
            width: 164px !important;
        }

        .dt-center-in-div {
            margin: 3px auto;
        }

        div.dataTables_filter {
            right: 0;
            margin-top: 0;
            position: relative;
            right: -43px;
        }

        .dt-buttons.btn-group.flex-wrap {
            gap: 10px;
            position: relative;
            top: 0;
            right: 0;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin-top: 40px;
        }

        .dataTables_length label {
            font-size: 0;
        }
    }

    @media (max-width: 376px) {
        div.dataTables_wrapper div.dataTables_filter {
            margin-top: 0;
            padding-left: 0 !important;
        }

        select.fy-dropdown {
            position: absolute;
            max-width: 109px;
            top: 144px;
            left: 189px;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            max-width: 150px;
        }

        select.fy-dropdown {
            max-width: 100px;
        }

        /* div.dataTables_wrapper div.dataTables_length select {
      width: 164px !important;
    } */
    }
</style> -->




<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">


<style>
    .head h4 {
        color: white;
    }

    .vitwo-alpha-global.is-vendor-invoice a.btn.btn-create {
        top: 16px;
        font-size: 0.75rem;
    }

    .vitwo-alpha-global a.btn.btn-create {
        position: absolute;
        top: 18px;
        right: 5px;
        display: flex;
        align-items: center;
        gap: 3px;
        font-size: 0.7rem;
        background: #003060;
        color: #fff;
        z-index: 91;
    }

    .card.card-tabs .card-body {
        padding: 0;
        overflow: hidden;
    }
</style>
<!-- <link rel="stylesheet" href="../../public/assets/accordion.css"> -->
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<div class="content-wrapper report-wrapper is-vendor-invoice vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">Manage Vendor Payment</h3>
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
                                                                                class="ion-paginationlistnew md hydrated"
                                                                                id="exportAllBtn" role="img"
                                                                                aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline"
                                                                                class="ion-fulllistnew md hydrated"
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
                                                    <select name="" id="vendorPaymentlimit" class="custom-select">
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
                                                        <button class="ion-paginationlistnew">
                                                            <ion-icon name="list-outline"
                                                                class="ion-paginationlistnew md hydrated" role="img"
                                                                aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistnew">
                                                            <ion-icon name="list-outline"
                                                                class="ion-fulllistnew md hydrated" role="img"
                                                                aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <a class="btn btn-create " type="button" id="initiate_id">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Proceed Payment
                                            </a>


                                            <table id="dataTable_detailed_view"
                                                class="table table-hover table-nowrap stock-new-table transactional-book-table">

                                                <thead>
                                                    <tr>
                                                        <?php
                                                        foreach ($columnMapping as $index => $column) {
                                                        ?>
                                                            <th class="text-left" data-value="<?= $index ?>">
                                                                <?= $column['name'] ?>
                                                            </th>
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
                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieVendorPaymentPro"], true) ?? [];

                                                                            foreach ($columnMapping as $index => $column) {
                                                                                //  checkbox column 
                                                                                if ($index === 0) {
                                                                                    continue;
                                                                                }
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
                                                                            if ($columnIndex === 0 || $columnIndex === 14 || $columnIndex === 17 || $columnIndex === 19) {
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

                                            <!-- GSTIN View start-->

                                            <div class="modal fade gst-field-status-modal" id="gst-field-status-modal"
                                                tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                                                data-backdrop="true" style="display: none;" aria-hidden="true">
                                                <div class="modal-dialog field-status modal-dialog-centered modal-dialog-scrollable"
                                                    role="document">
                                                    <div class="modal-content p-0" style="width: 593px;">
                                                        <div class="modal-header">
                                                            <div class="head p-2">
                                                                <h4 class="mb-0">
                                                                    <ion-icon name="document-text-outline" role="img"
                                                                        class="md hydrated"
                                                                        aria-label="document text outline"></ion-icon>
                                                                    GST Filed Status
                                                                </h4>
                                                            </div>
                                                            <div class="gst-number d-flex gap-2">
                                                                <span class="text-xs font-bold">GSTIN :</span>
                                                                <p id="mdl_gstin_span" class="text-xs"></p>
                                                            </div>
                                                            <div class="dropdown">

                                                                <form id="gstForm">
                                                                    <select class="p-1 text-xs border rounded" id="financialYear">
                                                                        <option selected value="<?= $current_year ?>"><?= $current_year ?></option>
                                                                        <option value="<?= $previous_year ?>"><?= $previous_year ?></option>
                                                                        <option value="<?= $pre_previous_year ?>"><?= $pre_previous_year ?></option>
                                                                    </select>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                                    <div class="card mb-0 bg-transparent">
                                                                        <div class="card-header p-0 rounded mb-2">
                                                                            <div class="head p-2">
                                                                                <h4>
                                                                                    <ion-icon
                                                                                        name="document-text-outline"
                                                                                        role="img" class="md hydrated"
                                                                                        aria-label="document text outline"></ion-icon>&nbsp;
                                                                                    GST Filed Status
                                                                                </h4>
                                                                            </div>
                                                                        </div>

                                                                        <div class="card-body">
                                                                            <div class="d-flex gap-2">
                                                                                <span class="text-xs font-bold">FY
                                                                                    :</span>
                                                                                <p id="gstinyear" class="text-xs"></p>
                                                                            </div>
                                                                            <div class="row">


                                                                                <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span29ACJFS5232R1ZA" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                                                                            </div>
                                                                            <div class="row">
                                                                                <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data"
                                                                                    id="gstinReturnsDatacomp_Div">

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- <div class="card mb-0 bg-transparent">
                  <div class="card-header p-0 rounded mb-2">
                    <div class="head p-2">
                      <h4>
                        <ion-icon name="document-text-outline" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>&nbsp; GST Filed Status For GSTR3B
                      </h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">


                       <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span29ACJFS5232R1ZA" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                                                                    <!-- </div>
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp3b_Div">

                      </div>
                    </div>
                  </div>
                </div>  -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- GSTIN View end -->

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
<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo $_REQUEST['pageNo'];
                                                } ?>">
</form>


<?php
require_once("../common/footer2.php");
?>
<!-- End Pegination from------->


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
        e.preventDefault();
        $("#serach_submit").click();
        
      }
    });

        $("#initiate_id").click(function(e) {

            if ($("input:checkbox[class=checkbx]:checked").length === 0) {
                alert("Select Atleast one check-box");
            } else {
                var yourArray = [];
                $("input:checkbox[class=checkbx]:checked").each(function() {
                    var id = $(this).val();
                    yourArray.push(id);
                });

                var array = JSON.stringify(yourArray);

                var url = "<?= LOCATION_URL ?>vendor-multipayments-test.php?code=" + array;

                window.location.href = url;

            }

        });




        $('.reverseGRNIV').click(function(e) {
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
                            // console.log(responseObj);

                            if (responseObj.status == 'success') {
                                $this.parent().parent().find('.listStatus').html('reversed');
                                $this.parent().parent().find('.listStatus').addClass("status-warning");
                                $this.parent().html('');
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
                                // location.reload();
                            });
                        }
                    });
                }
            });
        });

        $("#dataTable tfoot th").each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        });

        // DataTable
        var columnSl = 0;
        var table = $("#dataTable").DataTable({
            dom: '',
            buttons: ['copy', 'csv', 'excel', 'print'],
            "lengthMenu": [
                [1000, 5000, 10000, -1],
                [1000, 5000, 10000, 'All'],
            ],
            "scrollY": 200,
            "scrollX": true,
            "ordering": false,
        });


        $('.pay_btn').on('click', function() {
            var image = "<?= BASE_URL ?>public/assets/img/logo/vitwo-logo.png";
            var attr = $(this).data("amount");
            var amount = $(".attr_" + attr).val();

            // alert(image);

            var options = {
                "key": "rzp_test_zdoyJ0Amdyg3HB", // Enter the Key ID generated from the Dashboard
                "amount": amount * 100, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
                "currency": "INR",
                "name": "VITWO",
                "description": "Test Transaction",
                "image": "http://devalpha.vitwo.ai//public/storage/logo/165985132599981.ico",
                "callback_url": "https://eneqd3r9zrjok.x.pipedream.net/",
                // "prefill": {
                //     "name": "Gaurav Kumar",
                //     "email": "gaurav.kumar@example.com",
                //     "contact": "9000090000"
                // },
                "notes": {
                    "address": "Razorpay Corporate Office"
                },
                "theme": {
                    "color": "#3399cc"
                }
            };
            var rzp1 = new Razorpay(options);

            rzp1.open();
            e.preventDefault();
        });

        var gstin = ""
        $(document).on('click', '#getGstinReturnFiledStatusicon', function() {
            // url: `ajaxs/vendor/ajax-gst-filed-status.php?gstin=${gstin}`,
            gstin = $(this).data('gstin');
            // $("#gstinReturnsDatacomp_Div").empty();
            // $("#gstinReturnsDatacomp3b_Div").empty();
            $("#mdl_gstin_span").empty();
            $.ajax({
                url: `ajaxs/vendor/ajax-gst-review.php?gstin=${gstin}`,
                type: 'get',
                beforeSend: function() {
                    $("#gstinReturnsDatacomp_Div").html('');
                    $("#gstForm").trigger("reset");

                    // $("#gstinReturnsDatacomp_Div").html(`Loading...`);
                },

                success: function(response) {
                    responseObj = JSON.parse(response);
                    if (responseObj.status == "success") {
                        let fy = responseObj['fy'];
                        responseData = responseObj["data"];

                        let taxPeriods = {};

                        // Iterate over the response data
                        responseData["EFiledlist"].forEach(function(rowVal) {
                            if (rowVal['rtntype'] == 'GSTR1' || rowVal['rtntype'] == 'GSTR3B') {
                                let taxPeriod = rowVal["ret_prd"];
                                let filingDate = rowVal["dof"];
                                let returnType = rowVal["rtntype"];

                                // Extract the month name
                                let monthString = taxPeriod.substr(0, 2);
                                let month = parseInt(monthString, 10);
                                let monthNames = [
                                    "January", "February", "March", "April", "May", "June",
                                    "July", "August", "September", "October", "November", "December"
                                ];
                                let monthName = monthNames[month - 1] || "-";

                                // If the tax period doesn't exist, initialize it
                                if (!taxPeriods[taxPeriod]) {
                                    taxPeriods[taxPeriod] = {
                                        monthName: monthName,
                                        gstr1_date: "-",
                                        gstr1_status: '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>',
                                        gstr3b_date: "-",
                                        gstr3b_status: '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'
                                    };
                                }

                                // Update status and filing date based on return type
                                if (returnType === "GSTR1") {
                                    taxPeriods[taxPeriod]["gstr1_status"] = '<i class="fa fa-check" style="color: green;"> FILED</i>';
                                    taxPeriods[taxPeriod]["gstr1_date"] = filingDate;
                                } else if (returnType === "GSTR3B") {
                                    taxPeriods[taxPeriod]["gstr3b_status"] = '<i class="fa fa-check" style="color: green;"> FILED</i>';
                                    taxPeriods[taxPeriod]["gstr3b_date"] = filingDate;
                                }
                            }
                        });

                        // Sorting tax periods
                        let sortedTaxPeriods = Object.keys(taxPeriods)
                            .sort((a, b) => {
                                let yearA = parseInt(a.substr(2, 4), 10);
                                let monthA = parseInt(a.substr(0, 2), 10);
                                let yearB = parseInt(b.substr(2, 4), 10);
                                let monthB = parseInt(b.substr(0, 2), 10);

                                if (yearA === yearB) {
                                    return monthA - monthB;
                                }
                                return yearA - yearB;
                            })
                            .map(key => taxPeriods[key]);

                        // Generate table rows
                        let gstinReturnsDataDivHtml = `<table class="table table-striped table-bordered w-100">
                                                    <thead>
                                                    <tr>
                                                        <th>Tax Period</th>
                                                        <th>GSTR1 Filing Date</th>
                                                        <th>GSTR1</th>
                                                        <th>GSTR3B Filing Date</th>
                                                        <th>GSTR3B</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>`;

                        if (!sortedTaxPeriods || Object.keys(sortedTaxPeriods).length === 0) {
                            gstinReturnsDataDivHtml += `
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center">No Compliance Status Found</td>
                                    </tr>
                                </tbody>
                            </table>`;
                        } else {
                            Object.values(sortedTaxPeriods).forEach(function(row) {
                                gstinReturnsDataDivHtml += `
                                                        <tr>
                                                            <td>${row.monthName}</td>
                                                            <td>${row.gstr1_date}</td>
                                                            <td>${row.gstr1_status}</td>
                                                            <td>${row.gstr3b_date}</td>
                                                            <td>${row.gstr3b_status}</td>
                                                        </tr>`;
                            });
                            gstinReturnsDataDivHtml += `</tbody></table>`;
                        }

                        $("#gstinReturnsDatacomp_Div").html(gstinReturnsDataDivHtml);
                        //$("#gstinReturnsDatacomp3b_Div").html(gstinReturnsDataDivHtml3b);
                        $("#mdl_gstin_span").html(gstin);
                        $("#gstinyear").html(fy);
                        // console.log(gstinReturnsDataDivHtml);
                    } else {
                        let gstinReturnsDataDivHtml = `<table class="table table-striped table-bordered w-100">
                                                        <thead>
                                                            <tr>
                                                                <th>Tax Period</th>
                                                                <th>GSTR1 Filing Date</th>
                                                                <th>GSTR1</th>
                                                                <th>GSTR3B Filing Date</th>
                                                                <th>GSTR3B</th>
                                                            </tr>
                                                        </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td colspan="5" class="text-center">No Data Available</td>
                                                                </tr>
                                                            </tbody>
                                                    </table>`;
                        $("#gstinReturnsDatacomp_Div").html(gstinReturnsDataDivHtml);
                        $("#mdl_gstin_span").html(gstin);
                    }
                }
            });
        });


        $("#financialYear").on("change", function() {
            let selectedYear = $(this).val(); // Get selected financial year
            // gstin_data = gstin; // Ensure this is set in your PHP
            console.log("🔄 Selected Year:", selectedYear);
            console.log("✅ GSTIN Before Sending:", gstin);


            $.ajax({
                url: `ajaxs/vendor/ajax-gst-review.php`,
                type: "POST", // Use POST
                data: {
                    gstin: gstin,
                    financial_year: selectedYear
                }, // Send the selected year
                beforeSend: function() {
                    console.log("GSTIN Value Before Sending:", gstin);
                    $("#gstinReturnsDatacomp_Div").html("Loading...");
                },
                success: function(response) {
                    console.log("✅ Response Received:", response);
                    responseObj = JSON.parse(response);
                    if (responseObj.status == "success") {
                        let fy = responseObj["fy"];
                        responseData = responseObj["data"];

                        let taxPeriods = {};

                        responseData["EFiledlist"].forEach(function(rowVal) {
                            if (rowVal["rtntype"] == "GSTR1" || rowVal["rtntype"] == "GSTR3B") {
                                let taxPeriod = rowVal["ret_prd"];
                                let filingDate = rowVal["dof"];
                                let returnType = rowVal["rtntype"];

                                let monthString = taxPeriod.substr(0, 2);
                                let month = parseInt(monthString, 10);
                                let monthNames = [
                                    "January", "February", "March", "April", "May", "June",
                                    "July", "August", "September", "October", "November", "December"
                                ];
                                let monthName = monthNames[month - 1] || "-";

                                if (!taxPeriods[taxPeriod]) {
                                    taxPeriods[taxPeriod] = {
                                        monthName: monthName,
                                        gstr1_date: "-",
                                        gstr1_status: '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>',
                                        gstr3b_date: "-",
                                        gstr3b_status: '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'
                                    };
                                }

                                if (returnType === "GSTR1") {
                                    taxPeriods[taxPeriod]["gstr1_status"] =
                                        '<i class="fa fa-check" style="color: green;"> FILED</i>';
                                    taxPeriods[taxPeriod]["gstr1_date"] = filingDate;
                                } else if (returnType === "GSTR3B") {
                                    taxPeriods[taxPeriod]["gstr3b_status"] =
                                        '<i class="fa fa-check" style="color: green;"> FILED</i>';
                                    taxPeriods[taxPeriod]["gstr3b_date"] = filingDate;
                                }
                            }
                        });

                        let sortedTaxPeriods = Object.keys(taxPeriods)
                            .sort((a, b) => {
                                let yearA = parseInt(a.substr(2, 4), 10);
                                let monthA = parseInt(a.substr(0, 2), 10);
                                let yearB = parseInt(b.substr(2, 4), 10);
                                let monthB = parseInt(b.substr(0, 2), 10);

                                if (yearA === yearB) {
                                    return monthA - monthB;
                                }
                                return yearA - yearB;
                            })
                            .map((key) => taxPeriods[key]);

                        let gstinReturnsDataDivHtml = `<table class="table table-striped table-bordered w-100">
                                                        <thead>
                                                            <tr>
                                                                <th>Tax Period</th>
                                                                <th>GSTR1 Filing Date</th>
                                                                <th>GSTR1</th>
                                                                <th>GSTR3B Filing Date</th>
                                                                <th>GSTR3B</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>`;

                        if (!sortedTaxPeriods || Object.keys(sortedTaxPeriods).length === 0) {
                            gstinReturnsDataDivHtml += `
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">No Compliance Status Found</td>
                                </tr>
                            </tbody>
                        </table>`;
                        } else {
                            Object.values(sortedTaxPeriods).forEach(function(row) {
                                gstinReturnsDataDivHtml += `
                                <tr>
                                    <td>${row.monthName}</td>
                                    <td>${row.gstr1_date}</td>
                                    <td>${row.gstr1_status}</td>
                                    <td>${row.gstr3b_date}</td>
                                    <td>${row.gstr3b_status}</td>
                                </tr>`;
                            });
                            gstinReturnsDataDivHtml += `</tbody></table>`;
                        }

                        $("#gstinReturnsDatacomp_Div").html(gstinReturnsDataDivHtml);
                        $("#mdl_gstin_span").html(gstin);
                        $("#gstinyear").html(fy);
                    } else {
                        let gstinReturnsDataDivHtml = `<table class="table table-striped table-bordered w-100">
                                                        <thead>
                                                            <tr>
                                                                <th>Tax Period</th>
                                                                <th>GSTR1 Filing Date</th>
                                                                <th>GSTR1</th>
                                                                <th>GSTR3B Filing Date</th>
                                                                <th>GSTR3B</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="5" class="text-center">No Data Available</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>`;
                        $("#gstinReturnsDatacomp_Div").html(gstinReturnsDataDivHtml);
                        $("#mdl_gstin_span").html(gstin);
                    }
                },
                error: function() {
                    $("#gstinReturnsDatacomp_Div").html(`<div class="text-center text-danger">Error fetching data</div>`);
                },
            });
        });






    });
</script>

<!-- my input script starts here  -->


<script>
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function(event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function() {
        form.submit();
    });
</script>

<!-- my input script finishes here -->



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


<!-- datatable and modal script portion  -->

<script>
    $(document).ready(function() {
        var indexValues = [];
        var dataTable;
        let columnMapping = <?php echo json_encode($columnMapping); ?>
        // let dataPaginate;

        function initializeDataTable() {
            dataTable = $("#dataTable_detailed_view").DataTable({
                dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r><"billList_wrapper"t><ip>',
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
        // $('#dataTable_detailed_view thead tr').prepend('<th></th>'); // Checkbox as the first column
        $('#dataTable_detailed_view thead tr').append('<th>GSTIN Status</th>');


        initializeDataTable();

        var allData;
        var dataPaginate;


        // function full_datatable() {
        //     let fromDate = "<?= $fromDate ?>"; // For Date Filter
        //     let toDate = "<?= $toDate ?>"; // For Date Filter        
        //     let comid = <?= $company_id ?>;
        //     let locId = <?= $location_id ?>;
        //     let bId = <?= $branch_id ?>;

        //     $.ajax({
        //         type: "POST",
        //         url: "ajaxs/vendor/ajax-manage-vendor-payment-all.php",
        //         dataType: 'json',
        //         data: {
        //             act: 'alldata',
        //         },
        //         beforeSend: function () {

        //         },
        //         success: function (response) {
        //             // all_data = response.all_data;
        //             allData = response.all_data;


        //         },
        //     });
        // };
        // full_datatable();

        function fill_datatable(formDatas = '', pageNo = '', limit = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var columnMapping = <?php echo json_encode($columnMapping); ?>;
            var checkboxSettings = Cookies.get('cookieVendorPaymentPro');
            console.log(checkboxSettings);
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/vendor/ajax-manage-vendor-payment-all.php",
                dataType: 'json',
                data: {
                    act: 'vendorpaymentAll',
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
                    // alert(response)

                    if (response.status) {
                        var responseObj = response.data;
                        dataPaginate = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);
                        dataTable.column(-2).visible(true);
                        // dataTable.column(-2).visible(true);
                        $.each(responseObj, function(index, value) {
                            //  $('#item_id').val(value.itemId);
                            // hiddenInput = `<input type="hidden" name="" id="id_${value.sl_no}" >`;
                            checkBox = `<input type="checkbox" id="check_box_${value.sl_no}" name="check_box" class="checkbx" value="${btoa(btoa(value['req.code'] + "|" + value.vendor_id))}">`;

                            dataTable.row.add([
                                checkBox,
                                `<p>${value.sl_no}</p>`,
                                `<p>${value['req.code'] || '-'}</p>`,
                                `<p>${value['grniv.vendorCode'] || '-'}</p>`,
                                `<p>${value['grniv.vendorName'] || '-'}</p>`,
                                `<p>${value['grniv.vendorGstin'] || '-'}</p>`,
                                `<p>${value['bank.vendor_bank_name'] || '-'}</p>`,
                                `<p>${value['bank.vendor_bank_account_no'] || '-'}</p>`,
                                `<p class="text-right"><span class="text-right">${value['grniv.dueAmt'] || '-'}</span></p>`,
                                `<a style="cursor: pointer;" id="getGstinReturnFiledStatusicon"
                                    class="btn btn-sm waves-effect waves-light"
                                    data-gstin="${value['grniv.vendorGstin'] || '-'}" data-toggle="modal"
                                    data-target="#gst-field-status-modal"><i class="fa fa-eye po-list-icon"></i>
                                </a>`,
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

                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }

                        } else {

                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                var isChecked = $(this).prop("checked");

                                if (isChecked) {
                                    dataTable.column(index).visible(true);
                                }
                            });
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




        $(document).on("click", ".ion-paginationlistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieVendorPaymentPro')
                },
                beforeSend: function() {
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-paginationlistnew').prop('disabled', true)
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
                    $('.ion-paginationlistnew').prop('disabled', false)
                }
            })

        });
        $(document).on("click", ".ion-fulllistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/vendor/ajax-manage-vendor-payment-all.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieVendorPaymentPro'),
                    formDatas: formInputs
                },

                beforeSend: function() {
                    $('#loaderModal').show();
                    $('.ion-fulllistnew').prop('disabled', true)
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
                    $('.ion-fulllistnew').prop('disabled', false);
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
            var limitDisplay = $("#VendorPaymentLimit").val();
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

                    if (columnSlag === 'created_at') {
                        values = value4;
                    } else if (columnSlag === 'valid_from') {
                        values = value2;
                    } else if (columnSlag === 'valid_to') {
                        values = value3;
                    }



                    if ((columnSlag === 'updated_at' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
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
                        act: 'vendorPaymentPro',
                        fromData: fromData
                    },
                    success: function(response) {
                        console.log(response);
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

<!-- datatable and modal portion script ⬆️ -->


<!-- initiate id fucntion  -->

<script>
    $(document).ready(function() {
        $("#initiate_id").click(function(e) {

            if ($("input:checkbox[class=checkbx]:checked").length === 0) {
                alert("Select Atleast one check-box");
            } else {
                var yourArray = [];
                $("input:checkbox[class=checkbx]:checked").each(function() {
                    var codeArr = $(this).val();
                    yourArray.push(codeArr).val();
                });

                var array = JSON.stringify(yourArray);

                alert(array)

                var url = "<?= LOCATION_URL ?>vendor-multipayments-test.php?code=" + array;

                window.location.href = url;

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
            let columnName = $(`#columnName_${columnIndex}`).html().trim();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Created At') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'Valid From') {
                inputId = "value2_" + columnIndex;
            } else if (columnName === 'Valid Upto') {
                inputId = "value3_" + columnIndex;
            }

            if ((columnName === 'Created At' || columnName === 'Valid From' || columnName === 'Valid Upto') && operatorName === 'BETWEEN') {
                inputContainer.append(`<input type="date" name="value[]" class="fld form-control m-input" id="${(inputId)}" placeholder="Enter Keyword" value="">`);
            } else {
                $(`#${inputId}`).remove();
            }
            // console.log(`Change operator => ${operatorName}, columnName => ${columnName}`);
        });

    });
</script>


<!-- data toggle sript  -->



<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>



<script>
    function leaveInput(el) {
        if (el.value.length > 0) {
            if (!el.classList.contains('active')) {
                el.classList.add('active');
            }
        } else {
            if (el.classList.contains('active')) {
                el.classList.remove('active');
            }
        }
    }

    var inputs = document.getElementsByClassName("m-input");
    for (var i = 0; i < inputs.length; i++) {
        var el = inputs[i];
        el.addEventListener("blur", function() {
            leaveInput(this);
        });
    }

    // *** autocomplite select *** //
    wow = new WOW({
        boxClass: 'wow', // default
        animateClass: 'animated', // default
        offset: 0, // default
        mobile: true, // default
        live: true // default
    })
    wow.init();
</script>


<!-- fullscreen script here  -->


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
    var input = document.getElementById("myInput");
    input.addEventListener("keypress", function(event) {
        // console.log(event.key)

        if (event.key === "Enter") {
            event.preventDefault();
            // alert("clicked")
            document.getElementById("myBtn").click();
        }
    });
    var form = document.getElementById("search");

    document.getElementById("myBtn").addEventListener("click", function() {
        form.submit();
    });

    //     $(document).ready(function() {
    //     $('#valid_upto').change(function() {
    //         alert(1);
    //         var fromDate = new Date($('#valid_from').val());
    //         var toDate = new Date($(this).val());

    //         if (toDate < fromDate) {
    //             alert('To Date cannot be greater than From Date');
    //             $(this).val(''); // Clear the invalid date
    //         }
    //     });
    // });
</script>


<script>
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
</script>