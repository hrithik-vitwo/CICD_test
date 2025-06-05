<?php
require_once("../../app/v1/connection-branch-admin.php");

if (!isset($_COOKIE["cookiesoPgi"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiesoPgi", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
    }
}

$pageName =  basename($_SERVER['PHP_SELF'], '.php');


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
// export download file name section
$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = explode("-", pathinfo($originalFileName, PATHINFO_FILENAME), 2)[1];
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = 'export_' . $fileNameWithoutExtension . '_' . $currentDateTime;
$newFileNameDownloadall = 'download_' . $fileNameWithoutExtension . $currentDateTime;


$columnMapping = [
    [
        'name' => 'Sl. No.',
        'slag' => 'sl_no',
        'icon' => '',
        'dataType' => 'number'
    ],
    [
        'name' => 'PGI No',
        'slag' => 'so.pgi_no',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Customer PO',
        'slag' => 'so.customer_po_no',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Delivery Date',
        'slag' => 'so.pgiDate',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Customer Name',
        'slag' => 'cust.trade_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Total Items',
        'slag' => 'so.totalItems',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Created By',
        'slag' => 'so.created_by',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'so.pgiStatus',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'string'
    ],


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
<div class="content-wrapper report-wrapper is-stock-new is-sales-orders vitwo-alpha-global">

    <!-- Content Header (Page header) -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            <?php
            // $cookieTableStockReport = $_COOKIE["cookieTableStockReport"];
            // //console(["cookieTableStockReport" => $cookieTableStockReport]);

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
                                                <h3 class="card-title mb-0">Manage SO PGI</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">
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
                <div class="modal-header">
                    <div class="top-details">
                        <div class="left">
                            <p class="info-detail amount" id="amounts">
                                <ion-icon name="wallet-outline"></ion-icon>
                                <span class="amount-value" id="amount"> </span>
                            </p>
                            <span class="amount-in-words" id="amount-words"></span>
                            <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="po-numbers"> </span></p>
                        </div>
                        <div class="right">
                            <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span id="cus_name"></span></p>
                            <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address">

                                </span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                            <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button>
                            <button class="nav-link classicview-btn classicview-link classicView" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Print View</button>
                            <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button>
                        </div>
                    </nav>
                    <div class="tab-content global-tab-content" id="nav-tabContent">
                        <div class="tab-pane fade transactional-data-tabpane show active" id="nav-overview" role="tabpanel" aria-labelledby="nav-overview-tab">
                            <div class="d-flex nav-overview-tabs">

                            </div>
                            <div class="d-flex navBtn">

                            </div>




                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                    <div class="items-table">
                                        <h4>Customer Details</h4>
                                        <div class="customer-details">
                                            <div class="name-code">
                                                <div class="details name">
                                                    <p id="custName"></p>
                                                </div>
                                                <div class="details code">
                                                    <p id="custCode"></p>
                                                </div>
                                            </div>
                                            <div class="address-contact">
                                                <div class="address-customer">
                                                    <div class="d-flex">
                                                        <div class="details line-border-area">
                                                            <label for=""><ion-icon name="business-outline"></ion-icon>Billing Address</label>
                                                            <p class="pre-normal" id="custBillAdd"></p>
                                                        </div>
                                                        <div class="details line-border-area">
                                                            <label for=""><img src="<?= BASE_URL ?>public/assets/img/icons/ship-address.png" width="20">Shiping Address</label>
                                                            <p class="pre-normal" id="custShipAdd"></p>
                                                        </div>
                                                    </div>
                                                    <div id="place_of_supply" class="details">
                                                        <label for="">Place of Supply</label>
                                                        <p id="placeofsupply"></p>
                                                    </div>
                                                </div>
                                                <div class="contact-customer">
                                                    <div class="details dotted-border-area">
                                                        <label for="">Contacts</label>
                                                        <p> <ion-icon name="mail-outline"></ion-icon> <span id="custEmail"></span></p>
                                                        <p> <ion-icon name="call-outline"></ion-icon><span id="custPhone"></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="items-table">
                                        <h4>Other Details</h4>
                                        <div class="other-info">
                                            <div class="details">
                                                <label for="">Pgi Posting Date</label>
                                                <p id="postingDate"></p>
                                            </div>
                                            <div class="details">
                                                <label for="">Profit Center</label>
                                                <p id="profitcenter"></p>

                                            </div>
                                            <div class="details">
                                                <label for="">Customer Po Number</label>
                                                <p id="custPoNumber"></p>

                                            </div>

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
                                                            <th>Code</th>
                                                            <th>Name</th>
                                                            <th>Qty</th>
                                                            <th>Batch No</th>
                                                            <th>Storage Location</th>
                                                            <th>Warehouse</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="itemTableBody">

                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- <div id="itemTableBody">

                                            </div> -->
                                        </div>
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
                                    <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span><span class="created_by_trail"></span></p>
                                    <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span><span class="updated_by"> </span></p>
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
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

        <!-- Global View end -->
    </div>

    <!-- Global View end -->


</div>

<?php
require_once("../common/footer2.php");
?>



<script>
    $(document).on("click", "#serach_reset", function(e) {
        e.preventDefault();
        $("#myForm")[0].reset();
        $("#serach_submit").click();
    });

    // let csvContent;
    // let csvContentBypagination;
    let data;

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
            var checkboxSettings = Cookies.get('cookiesoPgi');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-pgi.php",
                dataType: 'json',
                data: {
                    act: 'sopgi',
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

                    //console.log(response);
                    // csvContent = response.csvContent;
                    // csvContentBypagination = response.csvContentBypagination;

                    if (response.status) {
                        var responseObj = response.data;
                        data = responseObj;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(length - 1).visible(true);
                        let reverseDeliveryButton='';
                        $.each(responseObj, function(index, value) {
                            if (value.status === 'active') {
                                reverseDeliveryButton = `
                                    <li>
                                        <button class="reversePGI" data-id="${value.so_delivery_pgi_id}" ><ion-icon name="repeat-outline"></ion-icon>Reverse</button>
                                    </li>`;
                            }

                            let pstatusClass = 'status-bg';
                            if (value["so.pgiStatus"] == "open") {
                                pstatusClass = 'status-bg status-open';
                            } else if (value["so.pgiStatus"] == "invoice") {
                                pstatusClass = 'status-bg status-accepted';
                            }

                            let delAct = `
                                    <li>
                                    <button class="deletePgiBtn" data-toggle="modal"  data-id="${value.so_delivery_pgi_id}" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>                                    </li>
                                    
                            `;

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.so_delivery_pgi_id}" data-toggle="modal" data-target="#viewGlobalModal">${ value["so.pgi_no"]}</a>`,
                                value["so.customer_po_no"],
                                value["so.pgiDate"],
                                value["cust.trade_name"],
                                value["so.totalItems"],
                                value["so.created_by"],
                                `<p class="${pstatusClass}">${value["so.pgiStatus"]}</p>`,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                    <li>
                                    <button class="soModal" data-toggle="modal" data-target="#viewGlobalModal" data-id=${value.so_delivery_pgi_id}><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                    </li>                                
                                    ${reverseDeliveryButton}
                                    </ul>
                                     
                                </div>`
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

                            //console.log('Cookie is blank.');
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
                            // //console.log("notVisibleColArr index:", notVisibleColArr);
                            if (notVisibleColArr.length > 0) {
                                notVisibleColArr.forEach(function(index) {
                                    dataTable.column(index).visible(false);
                                });
                            }

                            // //console.log('Cookie value:', checkboxSettings);
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
        $(document).on("click", ".ion-paginationliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(data),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiesoPgi')
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
                    let value3 = $(`#value3_${columnIndex}`).val() ?? "";
                    let value4 = $(`#value4_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'so.pgiDate') {
                        values = value4;
                    }

                    if ((columnSlag === 'delivery_date' || columnSlag === 'so.pgiDate' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
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
                // //console.log("FormInputs:", formInputs);

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
        });
        $(document).on("click", ".ion-fullliststock", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-manage-pgi.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    formDatas: formInputs,
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookiesoPgi')
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
                // //console.log(columnVal);

                var index = columnMapping.findIndex(function(column) {
                    return column.slag === columnVal;
                });
                // //console.log(index);
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

            // //console.log(formData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: "json",
                    data: {
                        act: 'soPgi',
                        formData: formData
                    },
                    success: function(response) {
                        // //console.log(response);
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
            if (columnName === 'Delivery Date') {
                inputId = "value4_" + columnIndex;
            } else if (columnName === 'SO Date') {
                inputId = "value2_" + columnIndex;
            } else if (columnName === 'Created Date') {
                inputId = "value3_" + columnIndex;
            }

            if ((columnName === 'Delivery Date' || columnName === 'SO Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
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


<!-- Modal script -->
<script>
    // click to load main modal data
    $(document).on("click", ".soModal", function() {
        $('.ViewfirstTab').tab('show');
        let so_delivery_pgi_id = $(this).data('id');
        // //console.log(so_delivery_pgi_id);
        let so_id;

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/so/ajax-manage-pgi-modal.php",
            dataType: 'json',
            data: {
                act: "modalData",
                so_delivery_pgi_id,
            },
            beforeSend: function() {
                // $('.item-cards').remove();
                $("#itemTableBody").html('');
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
                //console.log(value);

                if (value.status) {
                    let responseObj = value.data;
                    var country_labels = responseObj.country_labels;
                    // console.log(country_labels);
                    if (country_labels.place_of_supply) {
                        $("#place_of_supply").show();
                    } else {
                        $("#place_of_supply").hide();
                    }
                    let itemsObj = responseObj.item_details;
                    $('.ViewfirstTab').tab('show');
                    let delivery_qty = [];
                    let deliveryStatus = [];
                    let del_date = [];

                    $.each(itemsObj, function(index, item) {
                        delivery_qty.push(item.del_qty);
                        deliveryStatus.push(item.deliveryStatus);
                        del_date.push(item.delivery_date);
                    });

                    let dataObj = responseObj.dataObj;
                    let currency = responseObj.currency_name;

                    $(".left #amount").html(dataObj.pgi_no);
                    $("#default_address").html(responseObj.customer_address);
                    // $("#amount-words").html("(" + responseObj.currecy_name_words + ")");
                    $("#po-numbers").html(dataObj.so_number);
                    $(".right #cus_name").html(dataObj.trade_name);
                    $(".navBtn").hide();
                    $("#action-navbar").hide();
                    if (dataObj.pgiStatus == 'open') {
                        $(".navBtn").show().html(responseObj.navBtn);
                    }
                    $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print-taxcomponents.php?pgiId=${btoa(so_delivery_pgi_id)}`);
                    $(".created_by_trail").html(responseObj.createdBy + "<span class='font-bold text-normal'> on </span>" + responseObj.createdAt);
                    $(".updated_by").html(responseObj.updatedBy + "<span class='font-bold text-normal'> on </span>" + responseObj.updateAt);
                    $(".auditTrail").attr('data-ccode', responseObj.dataObj.pgi_no);
                    $(".nav-overview-tabs").html(responseObj.navbar);
                    $("#custName").html(dataObj.trade_name);
                    $("#custCode").html(dataObj.customer_code);
                    $("#custBillAdd").html(dataObj.customer_billing_address);
                    $("#custShipAdd").html(dataObj.customer_shipping_address);

                    let stCode = dataObj.placeOfSupply || "";
                    let stName = responseObj.placeOfsupply || "";

                    let finalplace = (stCode || stName) ? `${stCode} || ${stName}` : "--";
                    $("#placeofsupply").html(finalplace);

                    $("#custEmail").html(dataObj.customer_authorised_person_email);
                    $("#custPhone").html(dataObj.customer_authorised_person_phone);

                    $("#postingDate").html(` : ` + formatDate(dataObj.pgiDate));
                    $("#profitcenter").html(` : ` + dataObj.profit_center);
                    $("#custPoNumber").html(` : ` + dataObj.customer_po_no);

                    let taxableAmt = 0;
                    let igst = 0;
                    let cgst = 0;
                    let sgst = 0;

                    let subTotal = responseObj.allSubTotal;

                    let totalTax = responseObj.dataObj.totalTax;
                    let disCount = responseObj.dataObj.totalDiscount;
                    let totalAmt = responseObj.dataObj.totalAmount;


                    if (disCount == 0) {
                        taxableAmt = subTotal;
                    } else {
                        taxableAmt = subTotal - disCount;
                    }

                    if (responseObj.dataObj.igst == 0) {
                        cgst = totalTax / 2;
                        sgst = totalTax / 2;
                    } else {
                        igst = responseObj.dataObj.igst;
                    }

                    // card details section

                    $("#cardSoNo").html(responseObj.dataObj.so_number);
                    $("#cardCustPo").html(responseObj.dataObj.customer_po_no);
                    $("#totalItem").html(responseObj.dataObj.totalItems + " " + "Items");
                    $("#sub_total").html(currency + " " + parseFloat(subTotal).toFixed(2));
                    $("#totalDis").html(currency + " " + parseFloat(disCount).toFixed(2));
                    $("#taxableAmt").html(currency + " " + parseFloat(taxableAmt).toFixed(2));
                    $("#total_amount").html(currency + " " + responseObj.dataObj.totalAmount);
                    $("#remark").html(responseObj.dataObj.remarks);

                    if (responseObj.dataObj.igst == 0) {
                        $("#csgst").css("display", "block");
                        $("#igstP").hide();
                        $("#igst").hide();
                        $("#cgstVal").html(currency + " " + parseFloat(cgst).toFixed(2));
                        $("#sgstVal").html(currency + " " + parseFloat(sgst).toFixed(2));
                    } else {
                        $("#igst").html(currency + " " + parseFloat(igst).toFixed(2));
                    }

                    $.each(itemsObj, function(index, val) {
                        let tableData = `                                       <tr>
                                                                                <td>${val.itemCode}</td>
                                                                                <td title="${val.itemName}">${val.itemName}</td>
                                                                                <td>${decimalQuantity(val.qty)}</td>
                                                                                <td>${val.batch}</td>
                                                                                <td>${val.storage_location_name}</td>
                                                                                <td>${val.warehouse_name}</td>
                                                                            </tr>
                                    `;

                        $("#currencyHead").html(val.currency)
                        $("#itemTableBody").append(tableData);

                    });


                    // async function testing() {
                    //     console.log("testing function has been triggered");
                    //     await setTimeout(function() {
                    //         console.log("bla bla")
                    // $("#globalModalLoader").remove();

                    //     }, 5000)
                    //     console.log("bla");
                    // }
                    // testing();
                    $("#globalModalLoader").remove();
                } else {
                    console.log(value);
                }


            },
            complete: function() {
                $("#globalModalLoader").remove();

            },
            error: function(error) {
                //console.log(error);
            }
        });

        // click to load the print preview 
        $(document).on('click', '.classicView', function() {

            $.ajax({
                type: "GET",
                url: "ajaxs/modals/so/ajax-manage-pgi-modal.php",
                data: {
                    act: "classicView",
                    so_delivery_pgi_id,
                },

                beforeSend: function() {
                    let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;

                    // Append the new HTML to the modal-body element
                    $('#viewGlobalModal .modal-body').append(loader)
                },
                success: function(response) {
                    // //console.log(response);
                    $("#innerClassicView").html(response);
                    $("#globalModalLoader").remove();


                },
                complete: function() {
                    $("#globalModalLoader").remove();

                },
                error: function(error) {
                    //console.log(error);
                }
            });

        });
    });
    // Reverse  the pgi
    $(document).on('click', '.reversePGI', function(e) {
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
                        dep_slug: 'reversePGI'
                    },
                    url: 'ajaxs/ajax-reverse-post.php',
                    beforeSend: function() {
                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        var responseObj = JSON.parse(response);
                        // //console.log(responseObj);

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
                            // location.reload();
                        });
                    }
                });
            }
        });
    });
    // delete pgi 
    $(document).on('click', '.deletePgiBtn', function() {
        var soPgi = $(this).data('id');
        if (!confirm(`Are you sure to close This is Pgi #${soPgi}?`)) {
            return false;
        }
        $.ajax({
            type: "GET",
            url: `ajaxs/so/ajax-delete.php`,
            data: {
                act: "soPgi",
                soPgi
            },
            success: function(response) {
                // //console.log('response => ', response);
                let data = JSON.parse(response);

                // js swal alert
                let timerInterval;
                Swal.fire({
                    icon: data.status,
                    title: `SO #${soPgi} deleted successfully!`,
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