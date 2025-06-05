<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
// administratorLocationAuth();

$pageName =  basename($_SERVER['PHP_SELF'], '.php');

if (!isset($_COOKIE["cookiescollectpayment"])) {
    $settingsTable = getTableSettingsCheckbox(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_TEST_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
    $settingsCheckbox_concised_view = unserialize($settingsCh);
    if ($settingsCheckbox_concised_view) {
        setcookie("cookiescollectpayment", json_encode($settingsCheckbox_concised_view), time() + 86400 * 30, '/');
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
        'name' => 'Transaction Id',
        'slag' => 'transactionId',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Party code',
        'slag' => 'cust.customer_code',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Party Name',
        'slag' => 'cust.trade_name',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Posting Date',
        'slag' => 'postingDate',
        'icon' => '<ion-icon name="location-outline"></ion-icon>',
        'dataType' => 'date'
    ],
    [
        'name' => 'Payment Type',
        'slag' => 'paymentCollectType',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Collected Amount',
        'slag' => 'collect_payment',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'number'
    ],
    [
        'name' => 'Created By',
        'slag' => 'sopayment.created_by',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ],
    [
        'name' => 'Status',
        'slag' => 'status',
        'icon' => '<ion-icon name="document-outline"></ion-icon>',
        'dataType' => 'string'
    ]
];

?>

<style>
    .collect-pay-modal-body {
        overflow: auto !important;
    }
</style>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper report-wrapper vitwo-alpha-global is-collect-payment">
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
                                                <h3 class="card-title mb-0">Collection</h3>
                                            </div>
                                        </div>

                                        <div class="right-block">

                                            <div class="col-lg-6 col-md-6 col-sm-6 d-flex justify-content-end create-btns">



                                            </div>
                                            <div class="page-list-filer filter-list">
                                            </div>
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
                                                        <div class="page-list-filer filter-list mobile-page mobile-filter-list">

                                                        </div>
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
                                                                            <ion-icon name="list-outline" class="ion-fulllist md hydrated" id="exportAllBtn" role="img" aria-label="list outline"></ion-icon>Export
                                                                        </button>
                                                                    </li>
                                                                    <li>
                                                                        <button>
                                                                            <ion-icon name="list-outline" class="ion-paginationlist md hydrated" role="img" aria-label="list outline"></ion-icon>Download
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
                                                        <button class="ion-paginationlist">
                                                            <ion-icon name="list-outline" class="ion-paginationlist md hydrated" role="img" aria-label="list outline"></ion-icon>Export
                                                        </button>
                                                    </li>
                                                    <li>

                                                        <button class="ion-fulllist">
                                                            <ion-icon name="list-outline" class="ion-fulllist md hydrated" role="img" aria-label="list outline"></ion-icon>Download
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="dropdown payment-collection-dropdown">
                                                <a class="btn btn-secondary dropdown-toggle btn-create btn-payment" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <ion-icon name="add-outline"></ion-icon>
                                                    Create
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <!-- <li>
                                                        <a type="button" class="btn dropdown-toggle dropdown-sub-toggle btn-transparent" id="invoiceCreationDrop" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <ion-icon name="document-text-outline"></ion-icon> Create Invoice
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu" id="invoiceDropdown">
                                                            <li><a class="dropdown-item" href="direct-create-invoice.php">Goods Invoice</a></li>
                                                            <hr>
                                                            <li><a class="dropdown-item" href="direct-create-invoice.php?create_service_invoice">Service Invoice</a></li>
                                                        </ul>
                                                    </li> -->
                                                    <!-- <hr> -->
                                                    <li>
                                                        <a type="button" class="btn dropdown-toggle dropdown-sub-toggle btn-transparent" id="collectionDrop" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <ion-icon name="arrow-redo-outline"></ion-icon> Collection
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-submenu" id="collectDropdown">
                                                            <li><a class="dropdown-item" href="collectpaymentsettelment.php?collect-payment">Collect</a></li>
                                                            <hr>
                                                            <li><a class="dropdown-item" href="collectpaymentsettelment.php?adjust-payment">Settlement</a></li>
                                                        </ul>
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
                <!-- <div class="modal-header">
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
                                                        </div> -->
                <div class="modal-body">
                    <nav>
                        <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                            <!-- <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button> -->
                            <!-- <button class="nav-link classicview-btn classicview-link active" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Preview</button> -->
                            <!-- <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button> -->
                        </div>
                    </nav>
                    <div class="tab-content global-tab-content" id="nav-tabContent">
                        <div class="tab-pane classicview-pane show active" id="nav-classicview" role="tabpanel" aria-labelledby="nav-classicview-tab">
                            <a href="" class="btn btn-primary classic-view-btn float-right" id="classicViewPrint" target="_blank" onclick="">Print</a>
                            <div class="card classic-view bg-transparent" id='printMe'>
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

<?php
require_once("../common/footer2.php");
?>

<!-----------mobile filter list------------>

<script>
    // script for create button 
    $(document).ready(function() {
        $(document).on("click", "button.page-list", function() {
            let buttonId = $(this).attr("id");
            $("#modal-container").removeAttr("class").addClass(buttonId);
            $(".mobile-transform-card").addClass("modal-active");
        });

        $(document).on("click", ".btn-close-modal", function() {
            $("#modal-container").toggleClass("out");
            $(".mobile-transform-card").removeClass("modal-active");
        });
        $(document).on("mouseenter", "#invoiceCreationDrop", function() {
            $("#collectDropdown").removeClass("show");

            $("#invoiceDropdown").addClass("show");
        });


        $(document).on("mouseenter", "#collectionDrop", function() {
            $("#invoiceDropdown").removeClass("show");

            $("#collectDropdown").addClass("show");
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

<script>
    let csvContent;
    let csvContentBypagination;

    $(document).ready(function() {
        let indexValues = [];
        let dataTable;
        let columnMapping = <?php echo json_encode($columnMapping); ?>;


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
            let fdate = "<?php echo $f_date; ?>";
            let to_date = "<?php echo $to_date; ?>";
            let comid = <?php echo $company_id; ?>;
            let locId = <?php echo $location_id; ?>;
            let bId = <?php echo $branch_id; ?>;
            let checkboxSettings = Cookies.get('cookiescollectpayment');

            let notVisibleColArr = [];

            // console.log("cookie" + checkboxSettings);
            $.ajax({
                type: "POST",
                url: "ajaxs/ajax-journal_double_entry_collection.php",
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
                },
                success: function(response) {
                    console.log(response);
                    csvContent = response.csvContent;
                    csvContentBypagination = response.csvContentBypagination;

                    if (response.status) {
                        let responseObj = response.data;
                        $('#yourDataTable_paginate').show();
                        $('#limitText').show();

                        dataTable.clear().draw();
                        dataTable.columns().visible(false);
                        dataTable.column(-1).visible(true);

                        $.each(responseObj, function(index, value) {

                            let status;
                            let revCollectPay = '';
                            if (value.status === 'active') {
                                status = '<p class="status-bg status-open">Collected</p>';
                                revCollectPay = `
                                    <li>
                                        <button class="reverseCollection" data-id="${value.paymentId}"  data-no="${value.transactionId}"><ion-icon name="repeat-outline"></ion-icon>Reverse</button>
                                    </li>`;
                            } else if (value.status === "reverse") {
                                status = '<p class="status-bg status-pending">Reverse</p>'
                            }

                            dataTable.row.add([
                                value.sl_no,
                                `<a href="#" class="soModal"  data-id="${value.paymentId}" data-toggle="modal" data-target="#viewGlobalModal">${value.transactionId}</a>`,
                                `<p>${value.customer_code}</p>`,
                                `<p class="pre-normal">${value.customer_name}</p>`,
                                `<p>${formatDate(value.posting_date)}</p>`,
                                `<p>${value.paymentCollectType}</p`,
                                `<p class="text-right">${decimalAmount(value.collect_payment)}</p>`,
                                `<p>${value.created_by}</p>`,
                                status,
                                ` <div class="dropout">
                                    <button class="more">
                                         <span></span>
                                         <span></span>
                                         <span></span>
                                    </button>
                                    <ul>                                    
                                        <li>
                                            <button href="#" class="soModal" data-id=${value.paymentId} data-toggle="modal"  data-target="#viewGlobalModal"><ion-icon name="create-outline" class="ion-view"></ion-icon>View</button>
                                        </li>
                                        ${revCollectPay}
                                    </ul>
                                </div>`
                            ]).draw(false);

                            // <li>
                            //             <button data-toggle="modal" data-target="#editModal"><ion-icon name="create-outline" class="ion-edit"></ion-icon>Edit</button>
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
                        $('#yourDataTable_paginate').remove();
                        $('#limitText').remove();
                    }
                }
            });
        }

        fill_datatable(formDatas = '', pageNo = '', limit = '', columnMapping = columnMapping);



        //    ----- page length limit-----
        let formInputs = {};
        $(document).on("change", ".custom-select", function(e) {
            var maxlimit = $(this).val();
            fill_datatable(formDatas = formInputs, pageNo = '', limit = maxlimit, columnMapping = columnMapping);
        });

        //    ------------ pagination-------------

        $(document).on("click", "#pagination a", function(e) {
            e.preventDefault();
            var page_id = $(this).attr('id');
            var limitDisplay = $("#itemsPerPage").val();
            //    console.log(limitDisplay);
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
                    let value4 = $(`#value5_${columnIndex}`).val() ?? "";

                    if (columnSlag === 'postingDate') {
                        values = value2;
                    } else if (columnSlag === 'created_at') {
                        values = value4;
                    }

                    if ((columnSlag === 'postingDate' || columnSlag === 'created_at') && operatorName == "BETWEEN") {
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
                        act: 'managecollectpayment',
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
            if (columnName === 'Posting Date') {
                inputId = "value2_" + columnIndex;
            } else if (columnName === 'Created Date') {
                inputId = "value5_" + columnIndex;
            }

            if ((columnName === 'Posting Date' || columnName === 'Created Date') && operatorName === 'BETWEEN') {
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

<script>
    $(document).on('click', '.reverseCollection', function(e) {
        e.preventDefault(); // Prevent default click behavior

        var dep_keys = $(this).data('id');
        let transId = $(this).data('no');
        var $this = $(this); // Store the reference to $(this) for later use

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `You want to reverse this ${transId} ?`,
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
                        dep_slug: 'reverseCollection'
                    },
                    url: 'ajaxs/ajax-reverse-post.php',
                    beforeSend: function() {
                        $this.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        var responseObj = JSON.parse(response);
                        console.log(responseObj);

                        if (responseObj.status == 'success') {
                            $this.parent().parent().find('.listStatus').html('reverse');
                            $this.hide();
                        } else {
                            $this.html('<i class="far fa-undo po-list-icon"></i>');
                        }

                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1000
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

    $(document).on('click', '.soModal', function() {
        // $('#viewGlobalModal').modal('show');
        // $('.classicview-pane').tab('show');
        let paymentId = $(this).data('id');
        $("#classicViewPrint").attr('href', `classic-view/invoice-preview-print.php?payment_id=${btoa(paymentId)}`);
        // console.log(paymentId);
        // console.log();
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/so/ajax-collect-payment-modal.php",
            data: {
                act: "classicView",
                paymentId
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
                $('#viewGlobalModal .modal-body').append(loader);

            },
            success: function(response) {
                $(".classic-view").html(response);
                $("#globalModalLoader").remove();



            },
            complete: function() {
                $("#globalModalLoader").remove();


            },
            error: function(error) {
                console.log(error);
            }
        });

    });
</script>