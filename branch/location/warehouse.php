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

if (!isset($_COOKIE["cookieTableWarehouse"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookieTableWarehouse", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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
    ],
    [
        'name' => 'Warehouse Name',
        'slag' => 'warehouse_name',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Warehouse Code',
        'slag' => 'warehouse_code',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Warehouse Address',
        'slag' => 'warehouse_address',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Warehouse Description',
        'slag' => 'warehouse_description',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Warehouse Latitude',
        'slag' => 'warehouse_lat',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Warehouse Longitude',
        'slag' => 'warehouse_lng',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Created By',
        'slag' => 'created_by',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
    ],
    [
        'name' => 'Status',
        'slag' => 'status',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
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
<div class="content-wrapper report-wrapper is-stock-new is-sales-orders is-warehouse vitwo-alpha-global">
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
                                                <h3 class="card-title mb-0">Manage Warehouse</h3>
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
                                                        <button class="ion-paginationlistWareHouse">
                                                            <ion-icon name="list-outline" class="ion-paginationlistWareHouse md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllistWareHouse">
                                                            <ion-icon name="list-outline" class="ion-fulllistWareHouse md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                            <a href="warehouse-actions.php?create" class="btn btn-create" type="button">
                                                <ion-icon name="add-outline"></ion-icon>
                                                Create
                                            </a>
                                            <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
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
                                                            <p class="info-detail amount" id="amounts">
                                                                <ion-icon name="business-outline">></ion-icon>
                                                                <span class="amount-value" id="whName"> </span>
                                                            </p>
                                                        </div>
                                                        <div class="right">
                                                            <!-- <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span id="cus_name"></span></p> -->
                                                            <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address">

                                                                </span></p>
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
                                                                        <h4>Warehouse Details</h4>
                                                                        <div class="customer-details">
                                                                            <div class="name-code">
                                                                                <div class="details name">
                                                                                    <p id="warehousename"></p>
                                                                                </div>
                                                                                <div class="details code">
                                                                                    <p id="warehouse_code"></p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="details warehouse-address">
                                                                                <label for="">Address</label>
                                                                                <p id="warehouse_address"></p>
                                                                            </div>
                                                                            <div class="details warehouse-description">
                                                                                <label for="">Description</label>
                                                                                <p id="custpan"></p>
                                                                            </div>
                                                                            <div class="address-contact">
                                                                                <div class="details">
                                                                                    <label for="">Latitude</label>
                                                                                    <p id="warehouse_lat" class="pre-normal"></p>
                                                                                </div>
                                                                                <div class="details">
                                                                                    <label for="">Longitude</label>
                                                                                    <p class="pre-normal" id="warehouse_lng"></p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
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

<?php
require_once("../common/footer2.php");
?>

<script>
    $(document).on("click", "#serach_reset", function(e) {
        e.preventDefault();
        $("#myForm")[0].reset();
        $("#serach_submit").click();
    });
    let csvContent;
    let csvContentBypagination;
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

                buttons: [{
                    extend: 'collection',
                    text: '<ion-icon name="download-outline"></ion-icon> Export',
                    buttons: []
                }],
                // select: true,
                "bPaginate": false,
            });

        }

        $('#dataTable_detailed_view thead tr').append('<th>Action</th>');
        initializeDataTable();

        var allData;
        var dataPaginate;

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = '') {
            var fdate = "<?php echo $f_date; ?>";
            var to_date = "<?php echo $to_date; ?>";
            var comid = <?php echo $company_id; ?>;
            var locId = <?php echo $location_id; ?>;
            var bId = <?php echo $branch_id; ?>;
            var checkboxSettings = Cookies.get('cookieTableWarehouse');
            var notVisibleColArr = [];

            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-warehouse.php",
                dataType: 'json',
                data: {
                    act: 'warehouse',
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
                            let formObj = `<form action="/branch/location/warehouse-p.php" method="POST">
                                                <input type="hidden" name="id" value="${value.warehouse_id}">
                                                <input type="hidden" name="changeStatus" value="active_inactive">
                                                <button class="btn btn-sm" data-toggle="tooltip" data-placement="top" title="${value.status}"`;
                            if (value.status === "draft") {
                                formObj += ' style="cursor: inherit; border:none"';
                            } else {
                                formObj += ' onclick="return confirm(\'Are you sure to change status?\')"';
                            }

                            formObj += '>';

                            if (value.status === "active") {
                                formObj += `<p class="status-bg status-open">${capitalize(value.status)}</p>`;
                            } else if (value.status === "inactive" || value.status === "guest") {
                                formObj += `<p class="status-bg status-delete">${capitalize(value.status)}</p>`;
                            } else if (value.status === "draft") {
                                formObj += `<p class="status-warning text-xs">${capitalize(value.status)}</p>`;
                            }

                            formObj += `</button></form>`;
                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.warehouse_id}" data-toggle="modal" data-target="#fluidModalRightSuccessDemo">${ value.warehouse_name}</a>`,
                                value.warehouse_code,
                                value.warehouse_address,
                                value.warehouse_description,
                                value.warehouse_lat,
                                value.warehouse_lng,
                                value.created_by,
                                formObj,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>
                                    <li>
                                        <button id="editBtn"  data-id="${btoa(value.warehouse_id)}" data-toggle="modal" data-target="#editModal"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
                                    </li>
                                   
                                    <li>
                                        <button class="soModal" data-toggle="modal" data-id="${value.warehouse_id}" data-target="#fluidModalRightSuccessDemo"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                    </li>
                                    <li>
                              
                                    </li>
                                    </ul>
                                   
                                </div>`
                            ]).draw(false);
                            // <li>
                            //             <button data-toggle="modal" data-target="#viewModal"><ion-icon name="trash-outline" class="ion-delete"></ion-icon>Delete</button>
                            //         </li>
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
            });
        }

        fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping);

        $(document).on("click", ".ion-paginationlistWareHouse", function(e) {
            $.ajax({
                type: "POST",
                url: "../common/exportexcel-new.php",
                dataType: "json",
                data: {
                    act: 'paginationlist',
                    data: JSON.stringify(dataPaginate),
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieTableWarehouse')
                },
                beforeSend:function(){
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-paginationlistWareHouse').prop('disabled', true)
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
                    $('.ion-paginationlistWareHouse').prop('disabled', false)
                }
            })

        });
        $(document).on("click", ".ion-fulllistWareHouse", function(e) {
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-warehouse.php",
                dataType: "json",
                data: {
                    act: 'alldata',
                    coloum: columnMapping,
                    sql_data_checkbox: Cookies.get('cookieTableWarehouse'),
                    formDatas: formInputs
                },

                beforeSend:function(){
                    // console.log(sql_data_checkbox);
                    $('#loaderModal').show();
                    $('.ion-fulllistWareHouse').prop('disabled', true)
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
                    $('.ion-fulllistWareHouse').prop('disabled', false);
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

            console.log(fromData);
            if (settingsCheckbox.length < 5) {
                alert("Please select at least 5");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxs/ajax-save-cookies.php",
                    dataType: 'json',
                    data: {
                        act: 'warehouse',
                        fromData: fromData
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

<!------------ modal ajax--------- -->

<script>
    $(document).on("click", ".soModal", function() {

        $('#viewGlobalModal').modal('show');
        $('.ViewfirstTab').tab('show');
        // $('#home').tab('show');
        var warehouse_id = $(this).data('id');
        // alert(warehouse_id);
        // var hhValue = so_no + 'test';
        // $('.auditTrail').attr("data-ccode", warehouse_id);

        $.ajax({
            type: "GET",
            url: "ajaxs/modals/wms/ajax-warehouse-modal.php",
            dataType: 'json',
            data: {
                warehouse_id: warehouse_id
            },
            beforeSend: function() {},
            success: function(value) {
                console.log(value);
                let data = value.data;
                $('.auditTrail').attr("data-ccode", data.warehouse_code);
                $('#created_by').html(data.createdBy)
                $('#createdat').html(data.created_at)
                $('#updated_by').html(data.updated_by)
                $('#updatedat').html(data.updated_at)

                $('#whName').html(data.warehouse_name)
                $('#default_address').html(data.warehouse_code)

                $('#warehousename').html(data.warehouse_name);
                $('#warehouse_code').html(data.warehouse_code)
                $('#warehouse_address').html(data.warehouse_address)
                $('#warehouse_description').html(data.warehouse_description)
                $('#warehouse_lat').html(data.warehouse_lat)
                $('#warehouse_lng').html(data.warehouse_lng)
                $('.created_by_trail').html(data.createdBy);
                $('.created_at_trail').html(data.created_at);
                $('.updated_by').html(data.updated_by);
                $('.updated_at').html(data.updated_at);

            },
            complete: function() {
                // Hide loader modal after request completes
                $('#viewGlobalModal .modal-body .load-wrapp').remove();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    // edit 
    $(document).on("click", "#editBtn", function() {
        let prId = $(this).data('id')
        let url = "<?php echo  BRANCH_URL . 'location/warehouse-actions.php?edit=' ?>" + prId;
        window.location.href = url;
    });
</script>