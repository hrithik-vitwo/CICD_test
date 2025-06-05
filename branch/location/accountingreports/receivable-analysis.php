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

    @media (max-width : 575px) {
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
            margin: 0;
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
            margin-bottom: 0;
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

    /* media Query globally */
    @media only screen and (max-width: 1023px) {
        #containerThreeDot {
            position: relative !important;
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
// One single Query



if (isset($_GET['concised-view'])) {
?>
    <!-- Content Wrapper detailed-view -->
    <div class="content-wrapper report-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid px-0 px-md-2">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-3 pt-md-0 px-md-3 d-flex flex-wrap justify-content-between align-items-center" style="width:100%">
                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Receivable Analysis</h3>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="daybook-filter-list filter-list">
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn  waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn active waves-effect waves-light"><i class="fa fa-clock mr-2 active"></i>Concised View</a>
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
                                            </div>
                                            <div id="containerThreeDot">
                                                <div id="menu-wrap">
                                                    <input type="checkbox" class="toggler bg-transparent" />
                                                    <div class="dots">
                                                        <div></div>
                                                    </div>
                                                    <div class="menu">
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
                                                                        <label class="mb-0" for="">TO</label>
                                                                        <div class="form-input">
                                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
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
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        <?php


                                        // $sql_list = "SELECT erp_customer.customer_id AS customer_id,erp_customer.customer_code AS customer_code,erp_customer.trade_name,table1.due_days,table1.count_ AS num_of_invoices,total_due_amount FROM (SELECT customer_id,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND due_amount!=0 AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY customer_id,due_days) AS table1 LEFT JOIN erp_customer ON table1.customer_id=erp_customer.customer_id ORDER BY trade_name,table1.due_days asc;";
                                        $sql_list = "SELECT erp_customer.customer_id AS customer_id, erp_customer.customer_code AS customer_code,erp_customer.trade_name,GREATEST(table1.due_days,0) AS due_days, table1.count_ AS num_of_invoices,total_due_amount FROM (SELECT customer_id,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE()) AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND status='active' AND invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND due_amount!=0 GROUP BY customer_id,due_days) AS table1 LEFT JOIN erp_customer ON table1.customer_id=erp_customer.customer_id ORDER BY trade_name,table1.due_days asc;";


                                        $queryset = queryGet($sql_list, true);
                                        // console($queryset);

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
                                                            <th>Debtor</th>
                                                        <?php }

                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>Total Due</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>0-30 days</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>31-60 days</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>61-90 days</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>91-180 days</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>Above 180 days</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>Amount</th>
                                                        <?php }
                                                        ?>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <?php
                                                    $datas = $queryset['data'];
                                                    $sl = 0;
                                                    $array = [];
                                                    foreach ($datas as $key => $data) {
                                                        //console($data);
                                                        // echo $data['due_days'];
                                                        if ($data['due_days'] >= 0 && $data['due_days'] <= 30) {
                                                            // $array[$data['tradename']][] = $data;
                                                            $array['making'][$data['customer_id']]['Debtor'] = $data['trade_name'];
                                                            $array['making'][$data['customer_id']]['id'] = $data['customer_id'];
                                                            $array['making'][$data['customer_id']]['multi'][30]['days'] = "0-30 days";
                                                            $array['making'][$data['customer_id']]['multi'][30]['due'][] = $data['total_due_amount'];
                                                        } else if ($data['due_days'] >= 31 && $data['due_days'] <= 60) {
                                                            //   $array['making'][$data['customer_id']][$key][$data['tradename']][] = $data;
                                                            $array['making'][$data['customer_id']]['id'] = $data['customer_id'];
                                                            $array['making'][$data['customer_id']]['Debtor'] = $data['trade_name'];
                                                            $array['making'][$data['customer_id']]['multi'][60]['days'] = "31-60 days";
                                                            $array['making'][$data['customer_id']]['multi'][60]['due'][] = $data['total_due_amount'];
                                                        } else if ($data['due_days'] >= 61 && $data['due_days'] <= 90) {
                                                            //  $array['making'][$data['customer_id']][$key][$data['tradename']][] = $data;
                                                            $array['making'][$data['customer_id']]['id'] = $data['customer_id'];
                                                            $array['making'][$data['customer_id']]['Debtor'] = $data['trade_name'];
                                                            $array['making'][$data['customer_id']]['multi'][90]['days'] = "60-90 days";
                                                            $array['making'][$data['customer_id']]['multi'][90]['due'][] = $data['total_due_amount'];
                                                        } else if ($data['due_days'] >= 91 && $data['due_days'] <= 180) {
                                                            // $array['making'][$data['customer_id']][$key][$data['tradename']][] = $data;
                                                            $array['making'][$data['customer_id']]['id'] = $data['customer_id'];
                                                            $array['making'][$data['customer_id']]['Debtor'] = $data['trade_name'];
                                                            $array['making'][$data['customer_id']]['multi'][180]['days'] = "91-180 days";
                                                            $array['making'][$data['customer_id']]['multi'][180]['due'][] = $data['total_due_amount'];
                                                        } else {
                                                            $array['making'][$data['customer_id']]['id'] = $data['customer_id'];
                                                            $array['making'][$data['customer_id']]['Debtor'] = $data['trade_name'];
                                                            $array['making'][$data['customer_id']]['multi']["above"]['days'] = "above 180 days";
                                                            $array['making'][$data['customer_id']]['multi']["above"]['due'][] = $data['total_due_amount'];
                                                        }
                                                    }
                                                    //console($array);

                                                    $overdue_total = 0;
                                                    $thirty_total = 0;
                                                    $sixty_total = 0;
                                                    $ninety_total = 0;
                                                    $oneeighty_total = 0;
                                                    $total_above = 0;
                                                    $total_all = 0;

                                                    foreach ($array['making'] as $key => $data) {
                                                        $thirty = 0;
                                                        $sixty = 0;
                                                        $ninety = 0;
                                                        $oneeighty = 0;
                                                        $above = 0;


                                                        $customer_id = $data['id'];

                                                        if (isset($data['multi']['30'])) {
                                                            $thirty = array_sum($data['multi']['30']['due']);
                                                        }
                                                        if (isset($data['multi']['60'])) {
                                                            $sixty = array_sum($data['multi']['60']['due']);
                                                        }
                                                        if (isset($data['multi']['90'])) {
                                                            $ninety = array_sum($data['multi']['90']['due']);
                                                        }
                                                        if (isset($data['multi']['180'])) {
                                                            $oneeighty = array_sum($data['multi']['180']['due']);
                                                        }
                                                        if (isset($data['multi']['above'])) {
                                                            $above = array_sum($data['multi']['above']['due']);
                                                        }
                                                        $total_sum = $thirty + $sixty + $ninety + $oneeighty + $above;



                                                        $overdue_sql = queryGet("SELECT SUM(invoices.due_amount) AS overdue_amount FROM erp_branch_sales_order_invoices AS invoices WHERE invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY) < CURRENT_DATE AND customer_id=$customer_id");
                                                        //console($overdue_sql);
                                                        if ($overdue_sql['data']['overdue_amount'] != "") {
                                                            $overdue = $overdue_sql['data']['overdue_amount'];
                                                        } else {
                                                            $overdue = 0;
                                                        }



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
                                                                <td><?php echo ($data['Debtor']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td class="text-right"><?= $overdue ?> </td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td class="text-right"> <?= $thirty;  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td class="text-right"> <?php echo $sixty; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td class="text-right"> <?php echo $ninety; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td class="text-right"> <?php echo $oneeighty; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td class="text-right"> <?php echo $above; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td class="text-right">
                                                                    <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view-single&id=<?= $customer_id ?>" target="_blank"><?php echo $total_sum; ?> </a>
                                                                </td>
                                                            <?php }
                                                            ?>
                                                        </tr>
                                                    <?php
                                                        $thirty_total += $thirty;
                                                        $sixty_total += $sixty;
                                                        $ninety_total += $ninety;
                                                        $oneeighty_total  += $oneeighty;
                                                        $total_above += $above;
                                                        $total_all += $total_sum;
                                                        $overdue_total += $overdue;
                                                    }

                                                    ?>
                                                    <tr>
                                                        <?php
                                                        $m = 1;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td></td>
                                                        <?php }
                                                        $m++;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td class="text-right">Grand Total</td>
                                                        <?php }
                                                        $m++;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td class="text-right"><?= $overdue_total ?></td>
                                                        <?php }
                                                        $m++;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td class="text-right"><?= $thirty_total ?></td>
                                                        <?php }
                                                        $m++;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td class="text-right"><?= $sixty_total ?></td>
                                                        <?php }
                                                        $m++;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td class="text-right"><?= $ninety_total ?></td>
                                                        <?php }
                                                        $m++;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td class="text-right"><?= $oneeighty_total ?></td>
                                                        <?php }
                                                        $m++;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td class="text-right"><?= $total_above ?></td>
                                                        <?php }
                                                        $m++;
                                                        if (in_array($m, $settingsCheckbox)) { ?>
                                                            <td class="text-right"><?= $total_all ?></td>
                                                        <?php }
                                                        ?>
                                                    </tr>
                                                </tbody>
                                                <?php $j = 1; ?>
                                                <tfoot class="individual-search">
                                                    <tr>
                                                        <?php if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>SL NO.</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Debtor</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Overdue</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th> 0-30 days </th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th> 31-60 days</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>61-90 days</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>91-180 days</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Above 180</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Total Amount</th>
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
                                                                        Debtor</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Overdue</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        0-30 days</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        31-60 days</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        61-90 days</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        91-180 days</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Above 180 days</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Total Amount</td>
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
    <!-- /.Content Wrapper detailed-view-->
<?php
} else if (isset($_GET['detailed-view'])) {
?>
    <!-- Content Wrapper concised-view -->
    <div class="content-wrapper report-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid px-0 px-md-2">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-3 pt-md-0 px-md-3 d-flex justify-content-between align-items-center" style="width:100%">
                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Receivable Analysis</h3>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="daybook-filter-list filter-list">
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn  waves-effect waves-light"><i class="fa fa-clock mr-2  "></i>Concised View</a>
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>Detailed View</a>
                                            </div>
                                            <div id="containerThreeDot">
                                                <div id="menu-wrap">
                                                    <input type="checkbox" class="toggler bg-transparent" />
                                                    <div class="dots">
                                                        <div></div>
                                                    </div>
                                                    <div class="menu">
                                                        <div class="fy-custom-section">
                                                            <div class="fy-dropdown-section">
                                                                <?php
                                                                $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);

                                                                if (isset($_POST['from_date'])) {
                                                                    $f_date = $_POST['from_date'];
                                                                    $to_date = $_POST['to_date'];
                                                                    //echo 1;


                                                                } else if (isset($_GET['toDate'])) {
                                                                    $f_date = '';
                                                                    $to_date = $_GET['toDate'];
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
                                                                            <input type="date" class="form-control" name="from_date" id="from_date" value="<?= $f_date ?>" required>
                                                                        </div>
                                                                        <label class="mb-0" for="">TO</label>
                                                                        <div class="form-input">
                                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $to_date ?>" required>
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
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        <?php

                                        $cond = '';
                                        if (isset($_GET['toDate'])) {
                                            // exit();
                                            $to_date = $_GET['toDate'];
                                            $cond = " AND invoices.invoice_date <= '" . $to_date . "'";
                                        } else {
                                            $cond = " AND invoices.invoice_date BETWEEN  '" . $f_date . "' AND '" . $to_date . "'";
                                        }

                                        // $sql_list = "SELECT invoices.invoice_date AS doc_date,invoices.invoice_no AS doc_num,customer.customer_code,customer.trade_name AS customer_name,invoices.sub_total_amt AS base_amount,invoices.igst AS igst,invoices.sgst AS sgst,invoices.cgst AS cgst,invoices.all_total_amt AS invoice_amount,invoices.due_amount AS due_amount,DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY) AS due_date,invoices.created_at AS created_at,invoices.created_by AS created_by,invoices.updated_at AS updated_at,invoices.updated_by AS updated_by FROM erp_branch_sales_order_invoices AS invoices LEFT JOIN erp_customer AS customer ON invoices.customer_id=customer.customer_id WHERE invoices.due_amount!=0 AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY)> CURDATE() AND invoices.invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' ORDER BY due_date DESC";
                                        $sql_list = "SELECT invoices.invoice_date AS doc_date,invoices.invoice_no AS doc_num,customer.customer_code,customer.trade_name AS customer_name,invoices.sub_total_amt AS base_amount,invoices.igst AS igst,invoices.sgst AS sgst,invoices.cgst AS cgst,invoices.all_total_amt AS invoice_amount,invoices.due_amount AS due_amount, DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY) AS due_date,invoices.created_at AS created_at,invoices.created_by AS created_by,invoices.updated_at AS updated_at,invoices.updated_by AS updated_by FROM erp_branch_sales_order_invoices AS invoices LEFT JOIN erp_customer AS customer ON invoices.customer_id=customer.customer_id WHERE invoices.due_amount!=0 AND invoices.status='active' AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id  " . $cond . " ORDER BY due_date DESC";

                                        $queryset = queryGet($sql_list, true);
                                        //console($queryset);
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
                                                            <th>Document Date</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Document Number</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Customer Code</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Customer Name</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Base Amount</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>IGST</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>SGST</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>CGST</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Invoice Amount</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Amount</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Date</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created At</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created By</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated At</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated By</th>
                                                        <?php } ?>
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
                                                    ?>
                                                        <tr>
                                                            <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $sl; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo formatDateORDateTime($data['doc_date']); ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $data['doc_num']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo $data['customer_code']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['customer_name']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['base_amount']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['igst']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['sgst']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['cgst']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['invoice_amount']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['due_amount']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo formatDateORDateTime($data['due_date']);  ?></td>
                                                                <var> <?php }
                                                                    $i++;
                                                                    if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                    <td><?php echo formatDateORDateTime(($data['created_at']));  ?></td>
                                                                <?php }
                                                                    $i++;
                                                                    if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                    <td><?php echo getCreatedByUser(($data['created_by']));  ?></td>
                                                                <?php }
                                                                    $i++;
                                                                    if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                    <td><?php echo formatDateORDateTime(($data['updated_at']));  ?></td>
                                                                </var>
                                                            <?php }
                                                                    $i++;
                                                                    if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo getCreatedByUser(($data['updated_by']));  ?></td></var>


                                                            <?php } ?>
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
                                                            <th>Document Date</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Document Number</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Customer Code</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Customer Name</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Base Amount</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>IGST</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>SGST</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>CGST</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Invoice Amount</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Amount</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Date</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created At</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created By</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated At</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated By</th>
                                                        <?php } ?>
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
                                                                        Document Date</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Document Number</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Customer Code</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Customer Name</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Base Amount</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        IGST</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        SGST</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        CGST</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Invoice Amount</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Due Amount</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Due Date</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Created At</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Created By</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Updated At</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Updated By</td>
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
} else if (isset($_GET['detailed-view-single'])) {

    $id = $_GET['id'];
?>

    <!-- Content Wrapper concised-view -->
    <div class="content-wrapper report-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid px-0 px-md-2">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-3 pt-md-0 px-md-3 d-flex justify-content-between align-items-center" style="width:100%">
                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Receivable Analysis</h3>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="daybook-filter-list filter-list">
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn  waves-effect waves-light"><i class="fa fa-clock mr-2  "></i>Concised View</a>
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>Detailed View</a>
                                            </div>
                                            <div id="containerThreeDot">
                                                <div id="menu-wrap">
                                                    <input type="checkbox" class="toggler bg-transparent" />
                                                    <div class="dots">
                                                        <div></div>
                                                    </div>
                                                    <div class="menu">
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
                                                                        <label class="mb-0" for="">TO</label>
                                                                        <div class="form-input">
                                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
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
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        <?php


                                        $cond = '';
                                        if (isset($_GET['bs'])) {

                                            $cond = '';
                                        } else {
                                            $cond = " AND invoices.invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "'";
                                        }

                                        // $sql_list = "SELECT invoices.invoice_date AS doc_date,invoices.invoice_no AS doc_num,customer.customer_code,customer.trade_name AS customer_name,invoices.sub_total_amt AS base_amount,invoices.igst AS igst,invoices.sgst AS sgst,invoices.cgst AS cgst,invoices.all_total_amt AS invoice_amount,invoices.due_amount AS due_amount,DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY) AS due_date,invoices.created_at AS created_at,invoices.created_by AS created_by,invoices.updated_at AS updated_at,invoices.updated_by AS updated_by FROM erp_branch_sales_order_invoices AS invoices LEFT JOIN erp_customer AS customer ON invoices.customer_id=customer.customer_id WHERE invoices.due_amount!=0 AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY)> CURDATE() ".$cond." ORDER BY due_date DESC";
                                        $sql_list = "SELECT invoices.invoice_date AS doc_date,invoices.invoice_no AS doc_num,customer.customer_id,customer.customer_code,customer.trade_name AS customer_name,invoices.sub_total_amt AS base_amount,invoices.igst AS igst,invoices.sgst AS sgst,invoices.cgst AS cgst,invoices.all_total_amt AS invoice_amount,invoices.due_amount AS due_amount, DATE_ADD(invoices.invoice_date, INTERVAL invoices.credit_period DAY) AS due_date,invoices.created_at AS created_at,invoices.created_by AS created_by,invoices.updated_at AS updated_at,invoices.updated_by AS updated_by FROM erp_branch_sales_order_invoices AS invoices LEFT JOIN erp_customer AS customer ON invoices.customer_id=customer.customer_id WHERE invoices.due_amount!=0 AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND customer.customer_id=$id " . $cond . " ORDER BY due_date DESC";

                                        $queryset = queryGet($sql_list, true);
                                        //  console($queryset);
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
                                                            <th>Document Date</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Document Number</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Customer Code</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Customer Name</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Base Amount</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>IGST</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>SGST</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>CGST</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Invoice Amount</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Amount</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Date</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created At</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created By</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated At</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated By</th>
                                                        <?php } ?>
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
                                                    ?>
                                                        <tr>
                                                            <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $sl; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo formatDateORDateTime($data['doc_date']); ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $data['doc_num']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo $data['customer_code']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['customer_name']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['base_amount']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['igst']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['sgst']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['cgst']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['invoice_amount']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td class="text-right"><?php echo ($data['due_amount']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo formatDateORDateTime($data['due_date']);  ?></td>
                                                                <var> <?php }
                                                                    $i++;
                                                                    if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                    <td><?php echo formatDateORDateTime(($data['created_at']));  ?></td>
                                                                <?php }
                                                                    $i++;
                                                                    if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                    <td><?php echo getCreatedByUser(($data['created_by']));  ?></td>
                                                                <?php }
                                                                    $i++;
                                                                    if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                    <td><?php echo formatDateORDateTime(($data['updated_at']));  ?></td>
                                                                </var>
                                                            <?php }
                                                                    $i++;
                                                                    if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo getCreatedByUser(($data['updated_by']));  ?></td></var>


                                                            <?php } ?>
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
                                                            <th>Document Date</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Document Number</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Customer Code</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Customer Name</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Base Amount</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>IGST</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>SGST</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>CGST</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Invoice Amount</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Amount</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Date</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created At</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created By</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated At</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated By</th>
                                                        <?php } ?>
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
                                                                        Document Date</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Document Number</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Customer Code</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Customer Name</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Base Amount</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        IGST</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        SGST</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        CGST</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Invoice Amount</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Due Amount</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Due Date</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Created At</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Created By</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Updated At</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Updated By</td>
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
                    <div class="col-12 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pb-2" style="border-bottom: 1px solid #dbe5ee;">

                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-3 pt-md-0 px-md-3 d-flex justify-content-between align-items-center" style="width:100%">
                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Receivable Analysis</h3>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="daybook-filter-list filter-list">
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2  active"></i>Visual Representation</a>
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Concised View</a>
                                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
                                            </div>
                                            <div id="containerThreeDot">
                                                <div id="menu-wrap">
                                                    <input type="checkbox" class="toggler bg-transparent" />
                                                    <div class="dots">
                                                        <div></div>
                                                    </div>
                                                    <div class="menu">
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
                                                                        <label class="mb-0" for="">TO</label>
                                                                        <div class="form-input">
                                                                            <input type="date" class="form-control" name="to_date" id="to_date" value="<?= $_POST['to_date']; ?>" required>
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


                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                    <?php


                                    // console($_POST);
                                    //Graph View SQL 
                                    //Changes                                       
                                    $sql_list = "SELECT erp_customer.customer_id AS customer_id,erp_customer.customer_code AS customer_code,erp_customer.trade_name,table1.due_days,table1.count_ AS num_of_invoices,total_due_amount FROM (SELECT customer_id,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND status='active' AND invoice_date BETWEEN '" . $f_date . "' AND '" . $to_date . "' AND due_amount!=0 AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY customer_id,due_days) AS table1 LEFT JOIN erp_customer ON table1.customer_id=erp_customer.customer_id ORDER BY trade_name,table1.due_days asc;";

                                    $queryset = queryGet($sql_list, true);
                                    // console($queryset);
                                    $chartData = json_encode($queryset, true);

                                    $num_list = $queryset['numRows'];


                                    if ($num_list > 0) {
                                        $i = 1;
                                    ?>

                                        <div class="container-fluid mt-10">
                                            <div class="row">
                                                <!-- Bar Chart Div -->
                                                <div class="col-md-6 col-sm-12 d-flex p-0 p-md-3">
                                                    <div class="card flex-fill reports-card">
                                                        <div class="card-body">
                                                            <div id="chartDivColumn" class="chartContainer"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Pie Chart Div -->
                                                <div class="col-md-6 col-sm-12 d-flex p-0 p-md-3">
                                                    <div class="card flex-fill reports-card">
                                                        <div class="card-body">
                                                            <div id="chartDivPie" class="chartContainer"></div>
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
<!-- <script>
    var chartData = <?php echo $chartData; ?>;
    // console.log(chartData.data);

    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        /**
         * Source data
         */

        let finalData = [];
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
        });

        for (let obj of formattedData) {

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
</script> -->

<script>
    var chartData = <?php echo $chartData; ?>;

    am4core.ready(function() {
        // Themes
        am4core.useTheme(am4themes_animated);

        /**
         * Source data
         */

        let finalData = [];
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
        });

        for (let obj of formattedData) {

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

        /**
         * Chart container for Column Chart
         */
        var columnChartContainer = am4core.create("chartDivColumn", am4core.Container);
        columnChartContainer.logo.disabled = true;
        columnChartContainer.width = am4core.percent(100);
        columnChartContainer.height = am4core.percent(100);
        columnChartContainer.layout = "horizontal";

        // Create column chart instance
        var columnChart = columnChartContainer.createChild(am4charts.XYChart);

        // Create axes, series, and other configurations for the column chart
        var categoryAxisColumn = columnChart.yAxes.push(new am4charts.CategoryAxis());
        categoryAxisColumn.dataFields.category = "category"; // Make sure this matches the category field in your data
        categoryAxisColumn.renderer.labels.template.truncate = true;
        categoryAxisColumn.renderer.labels.template.maxWidth = 120;
        categoryAxisColumn.renderer.labels.template.tooltipText = "{category}";

        var valueAxisColumn = columnChart.xAxes.push(new am4charts.ValueAxis());

        var columnSeriesColumn = columnChart.series.push(new am4charts.ColumnSeries());
        columnSeriesColumn.dataFields.valueX = "value"; // Make sure this matches the value field in your data
        columnSeriesColumn.dataFields.categoryY = "category"; // Make sure this matches the category field in your data
        columnSeriesColumn.columns.template.strokeWidth = 0;
        columnSeriesColumn.columns.template.tooltipText = "[bold]{categoryY}: [#fff font-size: 20px]{valueX}";

        // Ensure that the column chart is receiving the correct data
        columnChart.data = data; // Make sure data is correctly formatted for the column chart

        /**
         * Chart container for Pie Chart
         */
        var pieChartContainer = am4core.create("chartDivPie", am4core.Container);
        pieChartContainer.logo.disabled = true;
        pieChartContainer.width = am4core.percent(100);
        pieChartContainer.height = am4core.percent(100);

        // Create pie chart instance
        var pieChart = pieChartContainer.createChild(am4charts.PieChart3D);

        // Set up other configurations for the pie chart
        pieChart.data = data;
        pieChart.hiddenState.properties.opacity = 0;
        pieChart.legend = new am4charts.Legend();
        pieChart.innerRadius = am4core.percent(50);

        var pieSeries = pieChart.series.push(new am4charts.PieSeries3D());
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "category";
        pieSeries.slices.template.propertyFields.fill = "color";
        pieSeries.labels.template.disabled = true;

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

        pieChart.events.on("ready", function(ev) {
            pieSeries.slices.getIndex(0).isActive = true;
        });

        pieSeries.slices.template.events.on("toggled", function(ev) {
            if (ev.target.isActive) {
                pieSeries.slices.each(function(slice) {
                    if (slice != ev.target) {
                        slice.isActive = false;
                    }
                });

                columnSeriesColumn.appeared = false;
                columnChart.data = ev.target.dataItem.dataContext.breakdown;
                columnSeriesColumn.fill = ev.target.fill;
                columnSeriesColumn.reinit();

                label1.text = pieChart.numberFormatter.format(ev.target.dataItem.values.value.percent, "#.'%'");
                label1.fill = ev.target.fill;

                label2.text = ev.target.dataItem.category;
            }
        });
    });
</script>