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

if (!isset($_COOKIE["cookieStockTransferList"])) {
    $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieStockTransferList", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}


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

// if change in column name and change any order , modify filter 
$columnMapping = [
    [
        'name' => '#',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'string'
    ],
    [
        'name' => 'Document No',
        'slag' => 'documentNo',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Destination',
        'slag' => 'destinationsl',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Destination Type',
        'slag' => 'destination_type',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Remarks',
        'slag' => 'remarks',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Created By',
        'slag' => 'created_by',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ]
];

?>
<style>
    .filterList {
        cursor: pointer
    }
</style>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">
<style>
    .global-view-modal .modal-body {
        overflow: auto;
    }
</style>
<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper is-sales-orders vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">Stock Transfer List</h3>
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

                                            <a href="manage-stock-transfer.php" class="btn btn-create" type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Transfer
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
                                                            <h4 class="modal-title text-sm">Detialed View Column
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

                                                                            $cookieTableStockReport = json_decode($_COOKIE["cookieStockTransferList"], true) ?? [];
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
                                                                            if ($columnIndex === 0 || $columnIndex === 3) {
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
                                <span class="amount-value" id="documentNo"> </span>
                            </p>
                            <p class="info-detail po-number"><ion-icon name="calendar"></ion-icon><span
                                    id="postingDate"> </span></p>
                        </div>
                        <div class="right">
                            <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span
                                    id="destType"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                            <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview"
                                aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                            <button class="nav-link stockTab" id="nav-stock-tab"
                                data-bs-toggle="tab" data-docno=""
                                data-bs-target="#nav-stock" type="button"
                                role="tab" aria-controls="nav-stock"
                                aria-selected="false"><ion-icon
                                    name="repeat-outline"></ion-icon>Stock Activity</button>
                        </div>
                    </nav>
                    <div class="tab-content global-tab-content" id="nav-tabContent">

                        <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview"
                            role="tabpanel" aria-labelledby="nav-overview-tab">

                            <div class="row orders-table">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="items-table">
                                        <h4>Item Details</h4>
                                        <div class="multiple-item-table">
                                            <div class="row head-state-table">
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Item Code</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Item Name</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Quantity</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">UOM</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Destination Item</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Destination Item Code</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Destination Qty</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Destination UOM</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Destination Storage Location</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Created By</div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 state-col-th state-col-td">Status</div>
                                            </div>
                                            <div id="itemTableBody">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div class="tab-pane fade transactional-data-tabpane"
                            id="nav-stock" role="tabpanel"
                            aria-labelledby="nav-stock-tab">
                            <div class="row">
                                <table class="table table-hover stockLogTable transactional-book-table inventory-view-list" id="stockLogsTable" data-responsive="false">
                                    <thead>
                                        <tr>
                                            <!-- <th>Sl No</th> -->
                                            <th>Location</th>
                                            <th>Posting Date</th>
                                            <th>Document Number</th>
                                            <th>Item Group</th>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Storage Location</th>
                                            <!-- <th>Party Code</th>
                                        <th>Party Name</th> -->
                                            <th>Batch Number</th>
                                            <th>UOM</th>
                                            <th>Movement Type</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Value</th>
                                            <!-- <th>Currency</th> -->
                                        </tr>
                                    </thead>
                                    <tbody class="stock-log-body">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
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
    <!-- Global View end -->
</div>

<script>
    // let csvContent;
    // let csvContentBypagination;

    $(document).ready(function() {
        var indexValues = [];
        var dataTable;
        var columnMapping = <?php echo json_encode($columnMapping); ?>;


        var allData;
        var dataPaginate;

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
            var checkboxSettings = Cookies.get('cookieStockTransferList');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-stocktransferlist.php",
                dataType: 'json',
                data: {
                    act: 'stocktransferlist',
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

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="" class="soModal" data-docno="${value.documentNo}"  data-id="${value.transfer_id}">${value.documentNo}</a>`,
                                value.destinationsl,
                                value.destination_type,
                                value.remarks,
                                value.created_by,
                                ` <div class="dropout">
                                     <button class="more">
                                          <span></span>
                                          <span></span>
                                          <span></span>
                                     </button>
                                     <ul>
                                        <li>
                                            <button class="soModal" data-docno="${value.documentNo}" data-id="${value.transfer_id}>
                                                <ion-icon name="create-outline" class="ion-view"></ion-icon>
                                                view
                                            </button>
                                        </li>
                                     </ul>
                                   
                                 </div>`,
                            ]).draw(false);
                        });

                        $('#yourDataTable_paginate').html(response.pagination);
                        $('#limitText').html(response.limitTxt);

                        if (!checkboxSettings) {
                            $(".settingsCheckbox_detailed:lt(5)").prop("checked", true);
                            $(".settingsCheckbox_detailed").each(function(index) {
                                if ($(this).prop("checked")) {
                                    dataTable.column(index).visible(true);
                                }
                            });

                            // console.log('Cookie is blank.');
                        } else {
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
            });
        }


        fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping);


        $(document).on("click", ".ion-paginationlistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieStockTransferList')
                },
                beforeSend: function() {
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


                    if ((columnSlag === 'so.posting_date') && operatorName == "BETWEEN") {
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
                // console.log("FormInputs:", formInputs);

                fill_datatable(formDatas = formInputs);
                $("#myForm")[0].reset();
                $(".m-input2").remove();

            });

            $(document).on("keypress", "#myForm input", function(e) {
                if (e.key === "Enter") {
                    $("#serach_submit").click();
                    e.preventDefault();
                }
            });

            $(document).on("click", "#serach_reset", function(e) {
                e.preventDefault();
                $("#myForm")[0].reset();
                fill_datatable();
            });
        });
        $(document).on("click", ".ion-fulllistnew", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-stocktransferlist.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieStockTransferList'),
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
                    $('.ion-fulllistnew').prop('disabled', false)
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
                console.log(columnVal);

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
                        act: 'stocktransferlist',
                        formData: formData
                    },
                    beforeSend: function() {
                        console.log("formdata beforeSend");
                        console.log(formData);
                    },
                    success: function(response) {
                        console.log(response)
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
            let columnName = $(`#columnName_${columnIndex}`).html().trim();
            let inputContainer = $(`#td_${columnIndex}`);
            let inputId;
            if (columnName === 'Posting Date') {
                inputId = "value2_" + columnIndex;
            }

            if ((columnName === 'Posting Date') && operatorName === 'BETWEEN') {
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


<!-- Modal script -->
<script>
    $(document).ready(function() {
        let table;

        table = $('#stockLogsTable');

        


        $(document).on("click", ".soModal", function(e) {
            e.preventDefault();
            $('#viewGlobalModal').modal('show');
            $('.ViewfirstTab').tab('show');
            let transfer_id = $(this).data('id');
            var documentno = $(this).data("docno");
            $('.stockTab').attr("data-docno", documentno);
            // alert(documentNo);
            // console.log(quotation_id);
            // $('.auditTrail').attr("data-ccode", quotation_id);
            $.ajax({
                type: "GET",
                url: "ajaxs/modals/stock/ajax-stock-transfer-modal.php",
                dataType: 'json',
                data: {
                    act: "modalData",
                    transfer_id,
                },
                beforeSend: function() {
                    // $(".itemCard").remove();
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
                success: function(value) {
                    console.log(value);
                    // if get proper response
                    if (value.status) {

                        var responseObj = value.data;
                        var itemsObj = responseObj

                        $("#documentNo").html(responseObj[0].documentNo);
                        $("#postingDate").html(responseObj[0].postingDate);
                        $("#destType").html(responseObj[0].destination_type);



                        // item table section
                        $.each(itemsObj, function(index, val) {

                            let td = ` <div class="row body-state-table">
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.itemCode}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${val.itemName}">${val.itemName}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.qty}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.uom}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${val.dest_item}">${val.dest_item}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.dest_itemCode}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.dest_qty}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.dest_uom}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td text-elipse w-30 text-dark" title="${val.dest_storage_location}">${val.dest_storage_location}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.createdBy}</div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 state-col-td">${val.status}</div>
                                    </div>
                                                            `;
                            $("#itemTableBody").append(td);


                        });

                        $("#globalModalLoader").remove();
                    } else {
                        // console.log(value)
                    }

                },
                complete: function() {
                    $("#globalModalLoader").remove();

                },
                error: function(error) {
                    console.log(error);
                }


            });


        });

        function innerTable(docNo) {
            $.ajax({
                type: "GET",
                url: `ajaxs/modals/stock/ajax-stock-transfer-modal.php`,
                dataType: "json",
                data: {
                    act: "stockActivity",
                    docNo
                },
                beforeSend: function() {
                    $('.stock-log-body').html('');
                    let loader = `<div class="load-wrapp" id="globalModalLoader">
                        <div class="load-1">
                        <div class="line"></div>
                        <div class="line"></div>
                        <div class="line"></div>
                        </div>
                    </div>`;
                    // Optionally show loader if needed
                },
                success: function(value) {
                    if (value.status) {
                        let resObj = value.data;
                        let html = '';
                        $.each(resObj, function(index, value) {
                            html += `<tr>
                                        <td>${value.location}</td>
                                        <td>${value.postingdate}</td>
                                        <td>${value.document_no}</td>
                                        <td>${value.itemGroup}</td>
                                        <td>${value.itemCode}</td>
                                        <td>${value.itemName}</td>
                                        <td>${value.storage_location}</td>
                                        <td>${value.batchNo}</td>
                                        <td>${value.uom}</td>
                                        <td>${value.movement_type}</td>
                                        <td class="text-right">${value.qty}</td>
                                        <td class="text-right">${value.rate}</td>
                                        <td class="text-right">${value.VALUE}</td>
                                    </tr>`;
                        });
                        $('.stock-log-body').html(html);
                        $("#globalModalLoader").remove();
                    } else {
                        $("#globalModalLoader").remove();
                        $('.stock-log-body').html('<tr><td colspan="13"><p class="text-center">No Stock Activity Found</p></td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading stock activity:", error);
                    $('.stock-log-body').html('<tr><td colspan="13"><p class="text-center">Error loading data</p></td></tr>');
                },
                complete: function() {
                    // Optionally remove loader if needed
                },
            });
        }

        $(document).on("click", ".stockTab", function() {
            // e.preventDefault();
            let docunum = this.getAttribute('data-docno');
            innerTable(docunum);
            // alert(docunum)


        });


        // close Quotation
        $(document).on('click', '#closeQuotation', function() {
            let quotationId = $(this).data('id');
            let quotationNumber = $(this).data('no');

            if (!confirm(`Are you sure to close quotation #${quotationNumber}?`)) {
                return false;
            }

            $.ajax({
                type: "GET",
                url: `ajaxs/so/ajax-close.php`,
                data: {
                    act: "closeQuotation",
                    quotationId
                },
                success: function(response) {
                    console.log('response => ', response);
                    let data = JSON.parse(response);

                    // js swal alert
                    let timerInterval;
                    Swal.fire({
                        icon: data.status,
                        title: `Quotation #${quotationNumber} closed successfully!`,
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
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            console.log("I was closed by the timer");
                        }
                    });
                    $(`#closeQuotation_${quotationId}_${quotationNumber}`).hide();
                    $(`#approvalStatus_${quotationId}`).html('<div class="status-secondary">CLOSED</div>');
                }
            });
        })

    });
</script>


<!--  List Action Script -->
<script>
    $(document).on('click', '.deleteQuotationBtn', function() {
        var soQuotationNo = $(this).data('quotationno');
        var qId = $(this).data('id');

        if (!confirm(`Are you sure to close Quotation No #${soQuotationNo}?`)) {
            return false;
        }
        $.ajax({
            type: "GET",
            url: `ajaxs/so/ajax-delete.php`,
            data: {
                act: "soQuotation",
                quotationId: qId
            },
            success: function(response) {
                // console.log('response => ', response);
                let data = JSON.parse(response);

                // js swal alert
                let timerInterval;
                Swal.fire({
                    icon: data.status,
                    title: `Quotation #${soQuotationNo} deleted successfully!`,
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
    $(document).on('click', "#approveQuotation", function() {
        let qId = atob($(this).data('id'));
        let quotNo = atob($(this).data('no'));
        let $this = $(this); // Store the reference to $(this) for later use
        $this.prop('disabled', true);
        // check confirmation
        Swal.fire({
            icon: 'warning',
            title: `Are you confirmed to approve the Quotation (${quotNo})?`,
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'GET',
                    data: {
                        act: 'approveQuot',
                        qId
                    },
                    url: 'ajaxs/modals/so/ajax-manage-quotations-modal.php',
                    beforeSend: function() {
                        $("#approveQuotation").html(`Waiting...`);
                        $("#rejectQuotation").hide();
                    },
                    success: function(res) {
                        try {
                            let response = JSON.parse(res);
                            console.log(response)
                            $("#approveQuotation").html(`${response.status}`);

                            let Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000
                            });
                            Toast.fire({
                                icon: response.status,
                                title: '&nbsp;' + response.message
                            }).then(function() {
                                // $(".m-input").val("");
                                $("#serach_submit").click();
                            });
                        } catch (error) {
                            console.log(error);
                            console.log(res);
                        }

                    }
                });
            }
        });

    })
    $(document).on('click', "#rejectQuotation", function() {
        let qId = atob($(this).data('id'));
        let quotNo = atob($(this).data('no'));
        // check confirmation
        Swal.fire({
            icon: 'warning',
            title: `Are you confirmed to reject the Quotation (${quotNo})?`,
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm',
        }).then((result) => {
            if (result.isConfirmed) {
                // send request to server
                $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        act: 'rejectQuot',
                        qId
                    },
                    url: 'ajaxs/modals/so/ajax-manage-quotations-modal.php',
                    beforeSend: function() {
                        $("#approveQuotation").html(`Waiting...`);
                    },
                    success: function(response) {
                        // handel response from server
                        console.log(response);
                        // $("#approveQuotation").html(`${response.status}`);

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

    })
    //  filter for advanced filter // please modify this after any change in column * It will Not work If any change occures at columnArray 
    $(document).on('click', ".filterList", function() {
        let lstName = $(this).data('name');
        if (lstName != 'all') {
            $("#value_6").val(lstName);
            $("#serach_submit").click();
            $(".filterList").removeClass("active");
            $(this).addClass('active');
        } else {
            $("#value_6").val("");
            $("#serach_submit").click();
            $(".filterList").removeClass("active");
            $(this).addClass('active');
        }
    });
</script>
<?php
require_once("../common/footer2.php");
?>