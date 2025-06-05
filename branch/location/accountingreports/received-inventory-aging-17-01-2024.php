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
    .chartContainer {
        width: 100%;
        height: 500px;
        margin-top: 6em;
    }

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
        overflow-y: scroll;
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
        gap: 0;
    }

    .transactional-book-table tr td {
        white-space: pre-line !important;
    }

    .dataTables_length {
        margin-left: 50px;
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
        max-width: 100px;
    }

    .report-wrapper .daybook-filter-list.filter-list {
        display: flex;
        gap: 6px;
        justify-content: flex-start;
        position: relative;
        top: 45px;
        left: 255px;
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

    .report-wrapper table tr td {
        background: #e7ebef;
    }

    .reports-card .filter-list a {
        background: #dedede;
        color: #003060;
        z-index: 9;
    }

    .report-wrapper .reports-card {
        background: #fff;
    }

    .report-wrapper table tr:nth-child(2n+1) td {
        background: #ffffff;
    }

    .label-select {
        display: flex;
        align-items: center;
        gap: 10px;
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
            gap: 20px;
            flex-direction: column-reverse;
            flex-wrap: nowrap;
        }

        .dataTables_length {
            margin-left: 0;
            margin-bottom: 1em;
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
</style>

<link rel="stylesheet" href="../../../public/assets/listing.css">
<link rel="stylesheet" href="../../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<!-- Resources -->
<script src="../../../public/assets/core.js"></script>
<script src="../../../public/assets/charts.js"></script>
<script src="../../../public/assets/animated.js"></script>
<script src="../../../public/assets/forceDirected.js"></script>
<script src="../../../public/assets/sunburst.js"></script>


<?php
if (isset($_GET['concised-view'])) {
?>
    <div class="content-wrapper report-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Inventory Aging by Received Date</h3>
                                        </div>

                                        <div class="fy-custom-section">
                                            <div class="fy-dropdown-section">
                                                <?php
                                                $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                                                if (isset($_POST['from_date'])) {
                                                    $f_date = $_POST['from_date'];
                                                    $to_date = $_POST['to_date'];
                                                    //echo 1;
                                                } else {
                                                    $start = explode('-', $variant_sql['data'][0]['year_start']);
                                                    $end = explode('-', $variant_sql['data'][0]['year_end']);
                                                    $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                    $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                                                    $_POST['from_date'] = $f_date;
                                                    $_POST['to_date'] = $to_date;
                                                    $_POST['drop_val'] = 'fYDropdown';
                                                    $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                                                }
                                                ?>
                                                <h6 class="text-xs font-bold">Financial Year</h6>
                                                <div class="dropdown-fyear">
                                                    <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                                        <option value="">--Select FY--</option>
                                                        <?php
                                                        foreach ($variant_sql['data'] as $key => $data) {
                                                            $start = explode('-', $data['year_start']);
                                                            $end = explode('-', $data['year_end']);
                                                            $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                            $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                                                        ?>
                                                            <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
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
                                                        <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                                                echo "selected";
                                                                            } ?>>Last 45 Days</option>
                                                        <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                                                echo "selected";
                                                                            } ?>>Last 60 Days</option>
                                                    </select>
                                                </div>
                                                <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                                            </div>

                                            <div class="customrange-section">
                                                <h6 class="text-xs font-bold">Custom Range</h6>
                                                <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                                    <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                                    <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                                    <div class="date-range-input d-flex">
                                                        <div class="form-input">
                                                            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                                        </div>
                                                        <div class="form-input">
                                                            <label class="mb-0" for="">TO</label>
                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                                </form>
                                                <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                                            </div>

                                            <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                                        </div>

                                    </li>
                                </ul>
                                <!---------------------- Search END -->

                            </div>
                            <div class="daybook-filter-list filter-list">
                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn active waves-effect waves-light"><i class="fa fa-clock mr-2 active"></i>Concised View</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
                            </div>
                            <div class="card card-tabs mb-0" style="border-radius: 20px;">

                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        <?php


                                        // $sql_list = "SELECT erp_customer.customer_id AS customer_id,erp_customer.customer_code AS customer_code,erp_customer.trade_name,table1.due_days,table1.count_ AS num_of_invoices,total_due_amount FROM (SELECT customer_id,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND due_amount!=0 AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY customer_id,due_days) AS table1 LEFT JOIN erp_customer ON table1.customer_id=erp_customer.customer_id ORDER BY trade_name,table1.due_days asc;";

                                        // $sql_list = "SELECT items.itemCode AS item_code, items.itemName AS item_name, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 0 AND 30 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `0-30_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 0 AND 30 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `0-30_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 31 AND 60 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `31-60_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 31 AND 60 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `31-60_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 61 AND 90 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `61-90_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 61 AND 90 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `61-90_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 91 AND 180 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `91-180_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 91 AND 180 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `91-180_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) > 180 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `more_than_180_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) > 180 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `more_than_180_days_total_value` FROM erp_inventory_items AS items LEFT JOIN erp_inventory_mstr_good_types AS type ON items.goodsType = type.goodTypeId LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId LEFT JOIN erp_inventory_stocks_log AS log ON items.itemId = log.itemId WHERE summary.company_id=1 AND summary.branch_id=1 AND summary.location_id=1 AND type.goodTypeName = 'FG Trading' OR type.goodTypeName = 'Finished Good' AND log.postingDate BETWEEN '2023-04-01' AND '2024-03-31' GROUP BY items.itemId ;";
                                        $sql_list = "SELECT items.itemCode AS item_code, items.itemName AS item_name, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 0 AND 30 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `0-30_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 0 AND 30 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `0-30_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 31 AND 60 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `31-60_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 31 AND 60 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `31-60_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 61 AND 90 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `61-90_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 61 AND 90 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `61-90_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 91 AND 180 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `91-180_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 91 AND 180 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `91-180_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) > 180 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `more_than_180_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) > 180 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `more_than_180_days_total_value` FROM erp_inventory_items AS items LEFT JOIN erp_inventory_mstr_good_types AS type ON items.goodsType = type.goodTypeId LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId LEFT JOIN erp_inventory_stocks_log AS log ON items.itemId = log.itemId WHERE summary.company_id=1 AND summary.branch_id=1 AND summary.location_id=1 AND type.goodTypeName = 'FG Trading' OR type.goodTypeName = 'Finished Good' AND log.bornDate BETWEEN '2023-04-01' AND '2024-03-31' GROUP BY items.itemId ;";

                                        $queryset = queryGet($sql_list, true);
                                        //console($queryset);

                                        $num_list = $queryset['numRows'];

                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_REPORT_DETAILED_VIEW_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                        $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                        $settingsCheckbox = unserialize($settingsCh);
                                        //console($settingsCheckbox);
                                        if ($num_list > 0) {
                                            $i = 1;
                                        ?>
                                            <table id="dataTable" class="table table-hover transactional-book-table" style="width: 100%; position: relative;">

                                                <thead>
                                                    <tr>
                                                        <?php if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>SL NO.</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>Item Code</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>Item Name</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>0-30 days quantity</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>0-30 days balance</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>31-60 days quantity</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>31-60 days balance</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>61-90 days quantity</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>61-90 days balance</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>91-180 days quantity</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>91-180 days balance</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>Above 180 days quantity</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>Above 180 days balance</th>
                                                        <?php }
                                                        ?>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
                                                    $datas = $queryset['data'];
                                                    $sl = 0;
                                                    $array = [];

                                                    //console($array);



                                                    foreach ($datas as $data) {



                                                        // console($data);
                                                        $i = 1;

                                                        $sl++;
                                                    ?>
                                                        <tr>

                                                            <?php if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo  $sl; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['item_code'] ;  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['item_name'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['0-30_days_total_qty'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['0-30_days_total_value'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['31-60_days_total_qty'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['31-60_days_total_value'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['61-90_days_total_qty'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['61-90_days_total_value'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['91-180_days_total_qty'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['91-180_days_total_value'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['more_than_180_days_total_qty'];  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?php echo $data['more_than_180_days_total_value'];  ?></td>
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
                                                        <?php if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>SL NO.</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Item Code</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Item Name</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>0-30 days quantity</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>0-30 days balance</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>31-60 days quantity</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>31-60 days balance</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>61-90 days quantity</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>61-90 days balance</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>91-180 days quantity</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>91-180 days balance</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Above 180 days quantity</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Above 180 days balance</th>
                                                        <?php }
                                                        ?>
                                                    </tr>
                                                </tfoot>

                                            </table>
                                        <?php } else { ?>
                                            <table id="mytable" class="table defaultDataTable table-hover">
                                                <thead>
                                                    <tr>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>
                                    </div>
                                <?php } ?>
                                </div>

                                <!---------------------------------Detailed View  Table settings Model Start--------------------------------->

                                <div class="modal" id="myModal2">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Detailed View Column Settings</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                                <div class="modal-body" style="max-height: 450px;">
                                                    <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                                    <input type="hidden" name="pageTableName" value="ERP_REPORT_DETAILED_VIEW_<?= $pageName ?>" />
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
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        SL NO.</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Item Code</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Item Name</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        0-30 days qty</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        0-30 days value</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        31-60 days qty</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        31-60 days value</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        61-90 days qty</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        61-90 days value</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        91-180 days qty</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        91-180 days value</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Above 180 days qty</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Above 180 days value</td>
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



    <?php
} else {
?>
    <!-- Content Wrapper. Graph View -->
    <div class="content-wrapper report-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid px-0 px-md-2">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">

                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 px-md-3 d-md-flex justify-content-between align-items-center" style="width:100%">

                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Inventory Aging by Received Date</h3>
                                        </div>

                                        <div class="fy-custom-section">
                                            <div class="fy-dropdown-section">
                                                <?php
                                                $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                                                if (isset($_POST['from_date'])) {
                                                    $f_date = $_POST['from_date'];
                                                    $to_date = $_POST['to_date'];
                                                    //echo 1;


                                                } else {

                                                    $start = explode('-', $variant_sql['data'][0]['year_start']);
                                                    $end = explode('-', $variant_sql['data'][0]['year_end']);
                                                    $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                    $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));
                                                    $_POST['from_date'] = $f_date;
                                                    $_POST['to_date'] = $to_date;
                                                    $_POST['drop_val'] = 'fYDropdown';
                                                    $_POST['drop_id'] = $variant_sql['data'][0]['year_variant_id'];
                                                }

                                                ?>
                                                <h6 class="text-xs font-bold">Financial Year</h6>
                                                <div class="dropdown-fyear">
                                                    <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                                        <option value="">--Select FY--</option>
                                                        <?php
                                                        foreach ($variant_sql['data'] as $key => $data) {
                                                            $start = explode('-', $data['year_start']);
                                                            $end = explode('-', $data['year_end']);
                                                            $startDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));
                                                            $endDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));

                                                        ?>
                                                            <option value="<?= $data['year_variant_id'] ?>" data-start="<?= $startDate ?>" data-end="<?= $endDate ?>" <?php if (($_POST['drop_val'] == 'fYDropdown' && $_POST['drop_id'] == $data['year_variant_id'])) {
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
                                                        <option value="44" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 44) {
                                                                                echo "selected";
                                                                            } ?>>Last 45 Days</option>
                                                        <option value="59" <?php if ($_POST['drop_val'] == 'quickDrop' && $_POST['drop_id'] == 59) {
                                                                                echo "selected";
                                                                            } ?>>Last 60 Days</option>
                                                    </select>
                                                </div>
                                                <h6 class="text-xs font-bold "><span class="finacialYearCla"></span></h6>
                                            </div>

                                            <div class="customrange-section">
                                                <h6 class="text-xs font-bold">Custom Range</h6>
                                                <form method="POST" action="" class="custom-Range" id="date_form" name="date_form">
                                                    <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="" />
                                                    <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange" />
                                                    <div class="date-range-input d-flex">
                                                        <div class="form-input">
                                                            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $_POST['from_date']; ?>" required>
                                                        </div>
                                                        <div class="form-input">
                                                            <label class="mb-0" for="">TO</label>
                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary float-right" id="rangeid" name="add_date_form">Apply</button>
                                                </form>
                                                <h6 class="text-xs font-bold "><span class="customRangeCla"></span></h6>
                                            </div>

                                            <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>

                                        </div>

                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>
                            <div class="daybook-filter-list filter-list">
                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2  active"></i>Visual Representation</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Concised View</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
                            </div>

                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                    <?php


                                    // console($_POST);
                                    //Graph View SQL 
                                    //Changes                                       
                                    // $sql_list = "SELECT erp_customer.customer_id AS customer_id,erp_customer.customer_code AS customer_code,erp_customer.trade_name,table1.due_days,table1.count_ AS num_of_invoices,total_due_amount FROM (SELECT customer_id,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND due_amount!=0 AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY customer_id,due_days) AS table1 LEFT JOIN erp_customer ON table1.customer_id=erp_customer.customer_id ORDER BY trade_name,table1.due_days asc;";
                                    // $sql_list = "SELECT items.itemCode AS item_code, items.itemName AS item_name, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 0 AND 30 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `0-30_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 0 AND 30 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `0-30_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 31 AND 60 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `31-60_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 31 AND 60 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `31-60_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 61 AND 90 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `61-90_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 61 AND 90 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `61-90_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 91 AND 180 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `91-180_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) BETWEEN 91 AND 180 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `91-180_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) > 180 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `more_than_180_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.postingDate) > 180 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `more_than_180_days_total_value` FROM erp_inventory_items AS items LEFT JOIN erp_inventory_mstr_good_types AS type ON items.goodsType = type.goodTypeId LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId LEFT JOIN erp_inventory_stocks_log AS log ON items.itemId = log.itemId WHERE summary.company_id=1 AND summary.branch_id=1 AND summary.location_id=1 AND type.goodTypeName = 'FG Trading' OR type.goodTypeName = 'Finished Good' AND log.postingDate BETWEEN '2023-04-01' AND '2024-03-31' GROUP BY items.itemId ;";
                                    $sql_list = "SELECT items.itemCode AS item_code, items.itemName AS item_name, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 0 AND 30 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `0-30_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 0 AND 30 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `0-30_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 31 AND 60 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `31-60_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 31 AND 60 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `31-60_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 61 AND 90 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `61-90_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 61 AND 90 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `61-90_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 91 AND 180 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `91-180_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) BETWEEN 91 AND 180 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `91-180_days_total_value`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) > 180 THEN ROUND(summary.itemTotalQty, 0) ELSE 0 END) AS `more_than_180_days_total_qty`, SUM(CASE WHEN DATEDIFF(NOW(), log.bornDate) > 180 THEN summary.movingWeightedPrice * summary.itemTotalQty ELSE 0 END) AS `more_than_180_days_total_value` FROM erp_inventory_items AS items LEFT JOIN erp_inventory_mstr_good_types AS type ON items.goodsType = type.goodTypeId LEFT JOIN erp_inventory_stocks_summary AS summary ON items.itemId = summary.itemId LEFT JOIN erp_inventory_stocks_log AS log ON items.itemId = log.itemId WHERE summary.company_id=1 AND summary.branch_id=1 AND summary.location_id=1 AND type.goodTypeName = 'FG Trading' OR type.goodTypeName = 'Finished Good' AND log.bornDate BETWEEN '2023-04-01' AND '2024-03-31' GROUP BY items.itemId ;";

                                    $queryset = queryGet($sql_list, true);
                                    // console($queryset);
                                    $chartData = json_encode($queryset, true);

                                    $num_list = $queryset['numRows'];


                                    if ($num_list > 0) {
                                        $i = 1;
                                    ?>

                                        <div class="container-fluid mt-10">

                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 d-flex">
                                                    <div class="card flex-fill reports-card">
                                                        <div class="card-body">
                                                            <div id="chartDivReceivableAnalysis" class="chartContainer"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    <?php } else { ?>
                                        <p>No data Found</p>
                                    <?php } ?>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.Content Wrapper. Graph View -->




    <?php
}
require_once("../../common/footer.php");
?>


<script>
    //check all
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
            buttons: ['copy', 'csv', 'excel', 'print'],
            "lengthMenu": [
                [1000, 5000, 10000, -1],
                [1000, 5000, 10000, 'All'],
            ],
            "scrollY": 200,
            "scrollX": true,
            "ordering": false,

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

<!-- CHART FUNCTION -->
<script>
    var chartData = <?php echo $chartData; ?>;
    // console.log(chartData.data);

    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        /**
         * Source data
         */

        /*let finalData = [];
        let outerIndex = 0;
        let innerIndex = 0;

        let formattedData = chartData.data.map(obj => {

            let due_days = parseInt(obj.due_days);

            if (due_days >= 0 && due_days <= 30) {
                obj.type = "0-30 days";
                return obj;
            } else if (due_days >= 31 && due_days <= 60) {
                obj.type = "31-60 days";
                return obj;
            } else if (due_days >= 61 && due_days <= 90) {
                obj.type = "61-90 days";
                return obj;
            } else if (due_days >= 91 && due_days <= 180) {
                obj.type = "91-180 days";
                return obj;
            } else if (due_days >= 181 && due_days <= 365) {
                obj.type = "181-365 days";
                return obj;
            } else {
                obj.type = "More than 365 days";
                return obj;
            };
        });*/

        //for (let obj of formattedData) {
        for (let obj of data) {

            const outerObj = finalData.map(obj => {
                return obj.category
            })
            outerIndex = outerObj.indexOf(obj.type)

            if (outerIndex !== -1) {

                const innerObj = finalData[outerIndex].breakdown.map(obj => {
                    return obj.category
                })
                innerIndex = innerObj.indexOf(obj.trade_name)

                if (innerIndex !== -1) {
                    finalData[outerIndex].value += Number(obj.total_due_amount);
                    finalData[outerIndex].breakdown[innerIndex].value += Number(obj.total_due_amount);
                } else {
                    finalData[outerIndex].value += Number(obj.total_due_amount);
                    finalData[outerIndex].breakdown.push({
                        "category": obj.trade_name,
                        "value": Number(obj.total_due_amount)
                    });
                };
            } else {
                finalData.push({
                    "category": obj.type,
                    "value": Number(obj.total_due_amount),
                    "breakdown": [{
                        "category": obj.trade_name,
                        "value": Number(obj.total_due_amount)
                    }]
                });
            };
        };

        data = finalData

        // console.log(finalData)

        /**
         * Chart container
         */

        // Create chart instance
        var chart = am4core.create("chartDivReceivableAnalysis", am4core.Container);
        chart.logo.disabled = true;
        chart.width = am4core.percent(100);
        chart.height = am4core.percent(100);
        chart.layout = "horizontal";

        /**
         * Column chart
         */

        // Create chart instance
        var columnChart = chart.createChild(am4charts.XYChart);

        // Create axes
        var categoryAxis = columnChart.yAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "category";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.inversed = true;

        var valueAxis = columnChart.xAxes.push(new am4charts.ValueAxis());

        // Create series
        var columnSeries = columnChart.series.push(new am4charts.ColumnSeries());
        columnSeries.dataFields.valueX = "value";
        columnSeries.dataFields.categoryY = "category";
        columnSeries.columns.template.strokeWidth = 0;
        columnSeries.columns.template.tooltipText = "[bold]{categoryY}: [#fff font-size: 20px]{valueX}"

        categoryAxis.renderer.labels.template.truncate = true;
        categoryAxis.renderer.labels.template.maxWidth = 120; // Adjust the maximum width as needed
        categoryAxis.renderer.labels.template.tooltipText = "{category}"; // Display full category name in tooltip

        /**
         * Pie chart
         */

        // Create chart instance
        var pieChart = chart.createChild(am4charts.PieChart3D);
        pieChart.data = data;
        pieChart.hiddenState.properties.opacity = 0; // this creates initial fade-in

        pieChart.legend = new am4charts.Legend();
        pieChart.innerRadius = am4core.percent(50);

        // Add and configure Series
        var pieSeries = pieChart.series.push(new am4charts.PieSeries3D());
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "category";
        pieSeries.slices.template.propertyFields.fill = "color";
        pieSeries.labels.template.disabled = true;

        // Set up labels
        var label1 = pieChart.seriesContainer.createChild(am4core.Label);
        label1.text = "";
        label1.horizontalCenter = "middle";
        label1.fontSize = 35;
        label1.fontWeight = 600;
        label1.dy = -30;

        var label2 = pieChart.seriesContainer.createChild(am4core.Label);
        label2.text = "";
        label2.horizontalCenter = "middle";
        label2.fontSize = 12;
        label2.dy = 20;

        // Auto-select first slice on load
        pieChart.events.on("ready", function(ev) {
            pieSeries.slices.getIndex(0).isActive = true;
        });

        // Set up toggling events
        pieSeries.slices.template.events.on("toggled", function(ev) {
            if (ev.target.isActive) {

                // Untoggle other slices
                pieSeries.slices.each(function(slice) {
                    if (slice != ev.target) {
                        slice.isActive = false;
                    }
                });

                // Update column chart
                columnSeries.appeared = false;
                columnChart.data = ev.target.dataItem.dataContext.breakdown;
                columnSeries.fill = ev.target.fill;
                columnSeries.reinit();

                // Update labels
                label1.text = pieChart.numberFormatter.format(ev.target.dataItem.values.value.percent, "#.'%'");
                label1.fill = ev.target.fill;

                label2.text = ev.target.dataItem.category;
            }
        });

    });
</script>