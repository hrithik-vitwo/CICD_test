<?php
require_once("../../../app/v1/connection-branch-admin.php");
$pageName =  basename($_SERVER['PHP_SELF'], '.php');
//administratorLocationAuth();
if (!isset($_SESSION["logedBranchAdminInfo"]["adminId"]) || !isset($_SESSION["logedBranchAdminInfo"]["adminRole"])) {
    echo "Session Timeout";
    exit;
}
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");

// Add Functions
require_once("../../../app/v1/functions/branch/func-customers.php");
require_once("../../../app/v1/functions/branch/func-journal.php");
require_once("../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../app/v1/functions/admin/func-company.php");


if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}




?>


<style>
    .content-wrapper {
        height: 100vh !important;
    }

    .content-wrapper table tr:nth-child(even) td {
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

    .date-range-input.keyword-input .form-input {
        display: block;
        width: 100% !important;
    }

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

    .transactional-book-table tr th {
        padding: 10px 8px !important;
    }


    .dataTables_scrollBody tfoot th {
        background: none !important;
    }

    .dataTables_scrollHead {
        margin-bottom: 40px;
    }

    .dataTables_scrollBody {
        max-height: 100% !important;
        height: 60vh !important;
        overflow-x: auto !important;
        overflow-y: auto !important;
        transition-delay: 0.2s;
    }

    .content-wrapper.fullscreen-mode .dataTables_scrollBody {
        height: 82vh !important;
        max-height: 78vh !important;
        overflow-x: scroll !important;
    }

    .dataTables_scrollBody::-webkit-scrollbar {
        background-color: transparent;
        width: 0px;
        height: 0px;
        cursor: pointer;
    }

    .dataTables_scrollBody:hover::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dataTables_scrollBody:hover::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
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

    .transactional-book-table tr th {
        text-align: center !important;
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
        margin-right: 3rem;
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
        padding-right: 5px !important;
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
        top: 0px;
        left: 0;
        margin: 15px 0;
        float: right;
    }

    .daybook-filter-list.filter-list a.active {
        background-color: #003060;
        color: #fff;
    }

    .date-range-input {
        gap: 7px;
    }

    .date-range-input .form-input {
        width: 100%;
    }

    .daybook-tabs {
        flex-direction: row-reverse;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0px;
    }

    #containerThreeDot #menu-wrap .dots>div,
    #containerThreeDot #menu-wrap .dots>div:after,
    #containerThreeDot #menu-wrap .dots>div:before {
        background-color: #003060 !important;
    }

    #containerThreeDot #menu-wrap .menu {
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
    }

    #containerThreeDot #menu-wrap .toggler:checked~.menu {
        width: 350px !important;
    }

    .stock-table-tabpane {
        max-height: 100%;
        height: 75vh;
        overflow-x: auto;
        overflow-y: auto;
        transition-delay: 0.2s;
    }


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
            gap: 10px;
            flex-direction: column-reverse;
            flex-wrap: nowrap;
        }

        .dataTables_length {
            margin-left: 10px;
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
            margin: 0px;
            display: flex;
            gap: 10px;
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
            margin-bottom: 0px;
            width: 100%;
            padding: 0px 10px;
        }

        div.dataTables_wrapper div.dataTables_filter {
            padding-bottom: 0px;
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

    @media only screen and (max-width: 1023px) {
        #containerThreeDot {
            width: 100% !important;
            padding: 0;
        }

        #containerThreeDot #menu-wrap .toggler:checked~.menu {
            width: 100%;
        }

        .chartContainer {
            width: 100%;
            height: 500px;
            margin-top: 2em;
        }

        .reports-card .filter-list a {
            position: static !important;
            margin: 0;
            height: 30px;
        }

        .chartContainer {
            width: 100%;
            height: 500px;
            margin-top: 2em;
        }

        .daybook-tabs {
            margin-bottom: 0px;
        }

        .daybook-filter-list.filter-list {
            display: flex;
            gap: 7px;
            justify-content: space-between;
            top: 0px;
            left: 0px;
            margin: 10px 0;
            width: 100%;
        }
    }

    /* 
  .dataTables_scrollHeadInner,
  .dataTables_scrollHeadInner table {
    width: 100% !important;
  } */


    td.dataTables_empty {
        position: absolute;
        left: 35%;
        top: 30%;
        transform: translate(100px, 50px);
        background: transparent !important;
    }

    .is-daybook #containerThreeDot {
        height: 50px;
        margin: 0px;
        width: auto !important;
        position: absolute;
        z-index: 9;
        top: 3px;
        right: 17.5rem;
    }

    @media (min-width: 768px) and (max-width: 1023px) {}

    @media (min-width: 980px) and (max-width: 1023px) {}
</style>

<link rel="stylesheet" href="../../../public/assets/new_listing.css">
<link rel="stylesheet" href="../../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<!-- Resources -->
<script src="../../../public/assets/core.js"></script>
<script src="../../../public/assets/charts.js"></script>
<script src="../../../public/assets/animated.js"></script>
<script src="../../../public/assets/forceDirected.js"></script>
<script src="../../../public/assets/sunburst.js"></script>


<?php
// // One single Query
?>
    <!-- Content Wrapper detailed-view -->
    <div class="content-wrapper report-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-2 pt-md-0 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Stock Report</h3>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="daybook-filter-list filter-list">
                                                <!-- <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn  waves-effect waves-light"><i class="fa fa-clock mr-2  "></i>Concised View</a> -->
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>Detailed View</a>
                                            </div>
                                            <div id="containerThreeDot">
                                                <div id="menu-wrap">
                                                    <input type="checkbox" class="toggler bg-transparent searchboxop" checked />

                                                    <div class="dots">
                                                        <div></div>
                                                    </div>
                                                    <div class="menu ">
                                                        <div class="fy-custom-section">
                                                            <div class="fy-dropdown-section">
                                                                <?php

                                                                // console($_REQUEST); 
                                                                $keyword = '';
                                                                $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
                                                                if (!isset($_POST['from_date']) || empty($_POST['from_date'])) {
                                                                    // echo 'hello';
                                                                    $_POST['from_date'] = date('Y-m-d', strtotime('-1 day'));
                                                                    $_POST['to_date'] = date('Y-m-d');

                                                                    $start_date = $_POST['from_date'];
                                                                    $end_date = $_POST['to_date'];
                                                                } else {
                                                                    //echo 1;
                                                                    if (isset($_POST['from_date']) || (count($_SESSION["reportFilter"] ?? []) > 0)) {
                                                                        // echo 'hello2';
                                                                        $start_date = $_POST['from_date'] ?? $_SESSION["reportFilter"]["from_date"];
                                                                        $end_date = $_POST['to_date'] ?? $_SESSION["reportFilter"]["to_date"];
                                                                        $_POST['from_date'] = $start_date;
                                                                        $_POST['to_date'] = $end_date;
                                                                        $_SESSION["reportFilter"] = $_POST;
                                                                    } else {
                                                                        // echo 'hello3';
                                                                        $start = explode('-', $variant_sql['data'][0]['year_start']);
                                                                        $end = explode('-', $variant_sql['data'][0]['year_end']);
                                                                        $start_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                                        $end_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                                                                        if (isset($_GET["to_date"]) && $_GET["to_date"] != "") {
                                                                            $end_date = $_GET["to_date"];
                                                                        }
                                                                        $_POST['from_date'] = $start_date;
                                                                        $_POST['to_date'] = $end_date;
                                                                        $_POST['drop_val'] = 'fYDropdown';
                                                                        $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                                                                    }
                                                                }

                                                                $cond = '';

                                                                ?>
                                                                <h6 class="text-xs font-bold">Financial Year</h6>
                                                                <div class="dropdown-fyear">
                                                                    <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                                                        <option value="">--Select FY--</option>
                                                                        <?php
                                                                        foreach ($variant_sql['data'] as $key => $data) {
                                                                            $start = explode('-', $data['year_start']);
                                                                            $end = explode('-', $data['year_end']);
                                                                            $startDates = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                                            $end_dates = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                                                                        ?>
                                                                            <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDates ?>" data-end="<?= $end_dates ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
                                                                                                                                                                                                echo "selected";
                                                                                                                                                                                            } ?>><?= $data['year_variant_name'] ?></option>
                                                                        <?php
                                                                        }
                                                                        ?>

                                                                        <option value="customrange" <?php if ($_POST['drop_id'] == '') {
                                                                                                        echo "selected";
                                                                                                    } ?>>
                                                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                                                                        </option>
                                                                    </select>

                                                                    <label class="mb-0" for="">OR</label>


                                                                    <select name="quickDropdown" id="quickDropdown" class="form-control quick-dropdown">
                                                                        <option value="">--Select One--</option>
                                                                        <option value="0" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 0) {
                                                                                                echo "selected";
                                                                                            } ?>>Today Report</option>
                                                                        <option value="6" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 6) {
                                                                                                echo "selected";
                                                                                            } ?>>Last 7 Days</option>
                                                                        <option value="14" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 14) {
                                                                                                echo "selected";
                                                                                            } ?>>Last 15 Days</option>
                                                                        <option value="29" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 29) {
                                                                                                echo "selected";
                                                                                            } ?>>Last 30 Days</option>
                                                                        <!-- <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                                                                    echo "selected";
                                                                                                } ?>>Last 45 Days</option>
                                                                        <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                                                                echo "selected";
                                                                                            } ?>>Last 60 Days</option> -->
                                                                    </select>
                                                                </div>
                                                                <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                                                            </div>

                                                            <div class="customrange-section">
                                                                <h6 class="text-xs font-bold ">Custom Range</h6>
                                                                <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                                                    <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                                                    <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                                                    <div class="date-range-input d-flex">
                                                                        <div class="form-input">
                                                                            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                                                        </div>
                                                                        <div class="form-input">
                                                                            <label class="mb-0" for="">To</label>
                                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="date-range-input keyword-input">
                                                                        <div class="form-input">
                                                                            <label class="text-xs font-bold" for="">Keyword</label>
                                                                            <input type="text" class="form-control w-100" name="keyword" id="keyword" value="<?= $_POST['keyword']; ?>">
                                                                        </div>
                                                                    </div>
                                                                    <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                                                </form>
                                                                <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm" onclick="openFullscreen()"><i class="fa fa-expand fa-2x"></i></button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>

                            <div class="card card-tabs mb-0" style="border-radius: 20px;">

                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane stock-table-tabpane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="border-radius: 20px;">
                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        <?php

                                        $cond = '';

                                        // $sql_list = "SELECT invoices.invoice_date AS doc_date,invoices.invoice_no AS doc_num,customer.customer_code,customer.trade_name AS customer_name,invoices.sub_total_amt AS base_amount,invoices.igst AS igst,invoices.sgst AS sgst,invoices.cgst AS cgst,invoices.all_total_amt AS invoice_amount,invoices.due_amount AS due_amount,DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY) AS due_date,invoices.created_at AS created_at,invoices.created_by AS created_by,invoices.updated_at AS updated_at,invoices.updated_by AS updated_by FROM erp_branch_sales_order_invoices AS invoices LEFT JOIN erp_customer AS customer ON invoices.customer_id=customer.customer_id WHERE invoices.due_amount!=0 AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY)> CURDATE() AND invoices.invoice_date BETWEEN '" . $start_date . "' AND '" . $endDate . "' ORDER BY due_date DESC";

                                        $sql_list = "SELECT
                                        LOG.refNumber AS document_no,
                                        items.itemCode,
                                        items.itemName,
                                        grp.goodGroupName AS itemGroup,
                                        str_loc.storage_location_id,
                                        str_loc.storage_location_name AS storage_location,
                                        LOG.logRef,
                                        CASE
                                            WHEN DATE(LOG.postingDate) BETWEEN '" . $start_date . "' AND '" . $end_date . "'  THEN DATE_FORMAT(
                                                DATE(LOG.postingDate), '%d-%b-%Y'
                                            )
                                        END AS date,
                                        UOM.uomName AS uom,
                                        LOG.refActivityName AS movement_type,
                                        LOG.itemQty AS qty,
                                        LOG.itemPrice AS rate,
                                        LOG.itemPrice * LOG.itemQty AS value
                                    FROM
                                        erp_inventory_stocks_log AS LOG
                                        LEFT JOIN erp_inventory_items AS items ON LOG.itemId = items.itemId
                                        LEFT JOIN erp_inventory_mstr_uom AS UOM ON LOG.itemUom = UOM.uomId
                                        LEFT JOIN erp_storage_location AS str_loc ON LOG.storageLocationId = str_loc.storage_location_id
                                        LEFT JOIN erp_inventory_mstr_good_groups AS grp ON items.goodsGroup = grp.goodGroupId
                                    WHERE    LOG.companyId=$company_id AND LOG.branchId=$branch_id AND LOG.locationId=$location_id
                                            AND DATE(LOG.postingDate) BETWEEN '" . $start_date . "' AND '" . $end_date . "'
                                    ORDER BY LOG.stockLogId desc
                                    ";

                                        $queryset = queryGet($sql_list, true);
                                        // echo $company_id.' '.$branch_id.' '.$location_id.' '.$start_date.' '.$end_date;

                                        $num_list = $queryset['numRows'];

                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_CONCISED_VIEW_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                        $settingsCheckbox_concised_view = unserialize($settingsCh);
                                        //console($settingsCheckbox_concised_view);



                                        if ($num_list > 0) {
                                            $i = 1;
                                        ?>
                                            <!-- <table class="table defaultDataTable table-hover" data-paging="true" data-responsive="false"> -->
                                            <table id="dataTable" class="table table-hover transactional-book-table" style="width: 100%; position: relative;">

                                                <thead>
                                                    <tr>
                                                        <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>SL NO.</th>
                                                        <?php }


                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Location</th>
                                                        <?php }

                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Dcoument Number</th>
                                                        <?php }




                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Item Group</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Item Code</th>
                                                        <?php }

                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Item Name</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Warehouse</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Storage Location</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Party Code</th>
                                                        <?php }

                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Party Name</th>
                                                        <?php }


                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Batch No.</th>
                                                        <?php }

                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Date</th>
                                                        <?php }


                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Mvt Type</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Quantity</th>
                                                        <?php }

                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>UOM</th>
                                                        <?php }
                                                        ?>
                                                    </tr>
                                                </thead>

                                                <tbody class="">
                                                    <?php
                                                    $datas = $queryset['data'];
                                                    $sl = 0;
                                                    foreach ($datas as $data) {
                                                        $i = 1;
                                                        // console($data);
                                                        $sl++;
                                                        
                                                    $qrysrui = queryGet("SELECT loc.storage_location_id, loc.storage_location_code, loc.storage_location_name, loc.storage_location_type, loc.storageLocationTypeSlug, warh.warehouse_id, warh.warehouse_code, warh.warehouse_name FROM erp_storage_location AS loc LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id WHERE loc.storage_location_id=".$data['storage_location_id']."");
                                                    $sldattaqe = $qrysrui['data'];
                                                    ?>
                                                        <tr>
                                                            <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $sl; ?></td>
                                                            <?php }

                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $locationNameNav; ?></td>
                                                            <?php }


                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $data['document_no']; ?></td>
                                                            <?php }


                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $data['itemGroup']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo $data['itemCode']; ?></td>
                                                            <?php }

                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td>
                                                                    <p class="pre-normal"><?php echo ($data['itemName']);  ?></p>
                                                                </td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td>
                                                                    <p class="pre-normal"><?php echo ($sldattaqe['warehouse_code']." (".$sldattaqe['warehouse_name'].")");  ?></p>
                                                                </td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td>
                                                                    <p class="pre-normal"><?php echo ($data['storage_location']);  ?></p>
                                                                </td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td>
                                                                    <p class="pre-normal text-center"><?php
                                                                                                        $data['party_code'] = ($data['party_code'] == '') ? '-' : $data['party_code'];
                                                                                                        echo ($data['party_code']);  ?></p>
                                                                </td>
                                                            <?php }

                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td>
                                                                    <p class="pre-normal text-center"><?php
                                                                                                        $data['party_name'] = ($data['party_name'] == '') ? '-' : $data['party_name'];
                                                                                                        echo ($data['party_name']);  ?></p>
                                                                </td>
                                                            <?php }


                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['logRef']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['date']);  ?></td>
                                                            <?php }


                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-center"><?php echo ($data['movement_type']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo (($data['qty']));  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['uom']);  ?></td>
                                                            <?php }

                                                            ?>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                                <?php $j = 1; ?>
                                                <tfoot class="individual-search">
                                                    <tr>
                                                        <?php if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>SL NO.</th>
                                                        <?php }

                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Location</th>
                                                        <?php }

                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Dcoument Number</th>
                                                        <?php }


                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>item Group</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Item Code</th>
                                                        <?php }

                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Item Name</th>
                                                        <?php }

                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Warehouse</th>
                                                        <?php }

                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Storage Location</th>
                                                        <?php }


                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Party Code</th>
                                                        <?php }

                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Party Name</th>
                                                        <?php }



                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Batch No.</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Date</th>
                                                        <?php }


                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Mvt Type</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Qty</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>UOM</th>
                                                        <?php }


                                                        ?>
                                                    </tr>
                                                </tfoot>

                                            </table>
                                        <?php } else { ?>
                                            <table id="mytable" class="table defaultDataTable table-hover">
                                                <thead>
                                                    <tr>
                                                        <td style="text-align: center;">
                                                            No data found
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>
                                    </div>
                                <?php } ?>
                                </div>

                                <!---------------------------------Concised View Table settings Model Start--------------------------------->

                                <div class="modal" id="myModal2">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Concised View Column Settings</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form name="table_settings_concised_view" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings_concised_view();">
                                                <div class="modal-body" style="max-height: 450px;">
                                                    <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                    <input type="hidden" name="pageTableName" value="ERP_REPORT_CONCISED_VIEW_<?= $pageName ?>" />
                                                    <div class="modal-body">
                                                        <div id="dropdownframe"></div>
                                                        <div id="main2">
                                                            <div class="checkAlltd d-flex gap-2 mb-2">
                                                                <input type="checkbox" class="grand-checkbox" value="" />
                                                                <p class="text-xs font-bold">Check All</p>
                                                            </div>
                                                            <?php $p = 1; ?>
                                                            <table class="colomnTable">
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        SL NO.</td>
                                                                </tr>


                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Location</td>
                                                                </tr>


                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Dcoument Number</td>
                                                                </tr>


                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Item Group</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Item Code</td>
                                                                </tr>


                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Item Name</td>
                                                                </tr>


                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Warehouse</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Storage Location</td>
                                                                </tr>


                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Party Code</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Party Name</td>
                                                                </tr>



                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Batch No.</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Date</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Mvt Type</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Quantity</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        UOM</td>
                                                                </tr>


                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" name="add-table-settings" class="btn btn-success">Save</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!---------------------------------Table Model End--------------------------------->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.Content Wrapper concised-view -->
<?php
?>
    
<?php
require_once("../../common/footer.php");
?>

<script>
    $(document).ready(function() {
        var numlist = <?= $num_list ?>;
        console.log(numlist);
        if (numlist > 0) {
            $(".searchboxop").prop('checked', false);
        } else {
            $(".searchboxop").prop('checked', true);
        }
    });
    $(document).ready(function() {
        $(".grand-checkbox").on("click", function() {

            // Check or uncheck all checkboxes within the table based on the grand checkbox state
            $(".colomnTable tr td input[type='checkbox']").prop("checked", this.checked);

        });
    });
</script>

<script>
    function table_settings_concised_view() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }

    function table_settings() {
        var favorite = [];
        $.each($("input[name='settingsCheckbox[]']:checked"), function() {
            favorite.push($(this).val());
        });
        var check = favorite.length;
        if (check < 5) {
            alert("Please Check Atlast 5");
            return false;
        }

    }


    $(document).ready(function() {



        $('.select2')
            .select2()
            .on('select2:open', () => {
                $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal3">
    Add New
  </a></div>`);
            });
        //**************************************************************
        $('.select4')
            .select4()
            .on('select4:open', () => {
                $(".select4-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#myModal4">
    Add New
  </a></div>`);
            });
    });
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


<?php

$originalFileName = basename($_SERVER['PHP_SELF']);
$fileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
$currentDateTime = date('Y-m-d_H-i-s');
$newFileName = $fileNameWithoutExtension . '_' . $currentDateTime;


?>

<script>
    $(document).ready(function() {

        $("#dataTable tfoot th").each(function() {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" />');
        });

        // DataTable
        var columnSl = 0;
        var table = $("#dataTable").DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            buttons: [{
                    extend: 'copy',
                    text: 'Copy',
                    filename: '$newFileName'
                },
                {
                    extend: 'csvHtml5',
                    text: 'CSV',
                    filename: '<?= $newFileName ?>'
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel',
                    filename: '<?= $newFileName ?>'
                },
                {
                    extend: 'print',
                    text: 'Print',
                    filename: '$newFileName'
                }
            ],
            "lengthMenu": [
                [1000, 5000, 10000, -1],
                [1000, 5000, 10000, 'All'],
            ],
            "scrollY": 200,
            "scrollX": true,
            "ordering": false,
        });



        // Apply the search

        table.columns().every(function() {

            var that = this;
            $('input', this.footer()).on('keyup change', function() {
                that.search(this.value).draw();
            });

        });



    });
</script>

<script>
    var elem = document.getElementById("listTabPan");

    function openFullscreen() {
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        } else if (elem.webkitRequestFullscreen) {
            /* Safari */
            elem.webkitRequestFullscreen();
        } else if (elem.msRequestFullscreen) {
            /* IE11 */
            elem.msRequestFullscreen();
        }
    }
</script>

<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            },
            function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
    });
</script>


<!-- CHANGES -->
<script>
    $(function() {
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });
    });
    $('#fYDropdown').change(function() {
        var title = $(this).val();
        if (title == "customrange") {
            $("#drop_val").val('customrange');
            $("#from_date").val('');
            $("#to_date").val('');
            $("#from_date").focus();
        } else {
            let start = $(this).find(':selected').data('start');
            let end = $(this).find(':selected').data('end');
            //alert(start);
            $("#from_date").val(start);
            $("#to_date").val(end);
            $("#drop_val").val('fYDropdown');
            $("#drop_id").val(title);
            $('#date_form').submit();
        }
    });

    $('#quickDropdown').change(function() {
        var days = $(this).val();
        var today = new Date();
        var seven_days_ago = new Date(today.getTime() - (days * 24 * 60 * 60 * 1000));

        var end = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2) + '-' + ('0' + today.getDate()).slice(-2);
        var start = seven_days_ago.getFullYear() + '-' + ('0' + (seven_days_ago.getMonth() + 1)).slice(-2) + '-' + ('0' + seven_days_ago.getDate()).slice(-2);

        // alert(start);
        // alert(end);
        $("#from_date").val(start);
        $("#to_date").val(end);
        $("#drop_val").val('quickDrop');
        $("#drop_id").val(days);

        $('#date_form').submit();
    });

    function compare_date() {
        let fromDate = $("#from_date").val();
        let toDate = $("#to_date").val();

        const date1 = new Date(fromDate);
        const date2 = new Date(toDate);
        const diffTime = Math.abs(date2 - date1);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));


        if (fromDate && toDate) {
            if (diffDays > 366) {
                document.getElementById("rangeid").disabled = true;
                $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">Date Range can not be greater than 1 year</p>`);
            } else {
                $(".customRangeCla").html('');
                document.getElementById("rangeid").disabled = false;

                if (toDate < fromDate) {
                    $(".customRangeCla").html(`<p class="text-danger text-xs prdatelabel">From Date can not be greater than To Date</p>`);
                    document.getElementById("rangeid").disabled = true;

                } else {
                    $(".customRangeCla").html('');
                    document.getElementById("rangeid").disabled = false;
                }
            }
        }
    }

    $("#to_date").keyup(function() {
        compare_date();
    });

    $("#from_date").change(function() {
        compare_date();
    });

    $("#to_date").change(function() {
        compare_date();
    });
</script>
<!-- CHANGES -->