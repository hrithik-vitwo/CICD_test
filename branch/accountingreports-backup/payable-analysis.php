<?php
require_once("../../app/v1/connection-branch-admin.php");
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

<link rel="stylesheet" href="../../public/assets/listing.css">
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<!-- Resources -->
<script src="../../public/assets/core.js"></script>
<script src="../../public/assets/charts.js"></script>
<script src="../../public/assets/animated.js"></script>
<script src="../../public/assets/forceDirected.js"></script>
<script src="../../public/assets/sunburst.js"></script>


<?php
// One single Query



if (isset($_GET['concised-view'])) {
?>
    <!-- Content Wrapper detailed-view -->
    <div class="content-wrapper report-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pt-1 my-2">
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Payable Analysis</h3>
                                            <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                                <option value="2022">FY-2022</option>
                                                <option value="2023">FY-2023</option>
                                                <option value="customrange">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                                                </option>
                                            </select>
                                        </div>
                                        <div class="modal fade custom-range-modal" id="customRange" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title text-sm">Select Date Range</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- <input type="text" name="daterange" class="form-control" value="01/01/2018 - 01/15/2018" /> -->
                                                        <div class="date-range-input d-flex">
                                                            <div class="form-input">
                                                                <label for="">From Date</label>
                                                                <input type="date" class="form-control" name="from_date">
                                                            </div>
                                                            <div class="form-input">
                                                                <label for="">To Date</label>
                                                                <input type="date" class="form-control" name="to_date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary float-right">Apply</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                                    </li>
                                </ul>
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
                                        $cond = '';

                                          $sql_list = "SELECT vendorId,vendorName,DATEDIFF(dueDate,CURDATE())AS due_days,COUNT(*) AS count_,SUM(dueAmt) AS total_due_amount FROM erp_grninvoice AS iv WHERE companyId=$company_id AND branchId=$branch_id AND postingDate BETWEEN '2022-04-01' AND '2023-03-31' AND dueAmt!=0 AND dueDate>CURDATE() GROUP BY vendorId,vendorName,due_days ORDER BY due_days,vendorName,total_due_amount desc;";

                                        $queryset = queryGet($sql_list, true);
                                        //console($queryset); 
                                        // echo $company_id;
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
                                                            <th>Overdue</th>
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
                                                            $array['making'][$data['vendorId']]['Debtor'] = $data['vendorName'];
                                                            $array['making'][$data['vendorId']]['id'] = $data['vendorId'];
                                                            $array['making'][$data['vendorId']]['multi'][30]['days'] = "0-30 days";
                                                            $array['making'][$data['vendorId']]['multi'][30]['due'][] = $data['total_due_amount'];
                                                        } else if ($data['due_days'] >= 31 && $data['due_days'] <= 60) {
                                                            //   $array['making'][$data['vendorId']][$key][$data['tradename']][] = $data;
                                                            $array['making'][$data['vendorId']]['id'] = $data['vendorId'];
                                                            $array['making'][$data['vendorId']]['Debtor'] = $data['vendorName'];
                                                            $array['making'][$data['vendorId']]['multi'][60]['days'] = "31-60 days";
                                                            $array['making'][$data['vendorId']]['multi'][60]['due'][] = $data['total_due_amount'];
                                                        } else if ($data['due_days'] >= 61 && $data['due_days'] <= 90) {
                                                            //  $array['making'][$data['vendorId']][$key][$data['tradename']][] = $data;
                                                            $array['making'][$data['vendorId']]['id'] = $data['vendorId'];
                                                            $array['making'][$data['vendorId']]['Debtor'] = $data['vendorName'];
                                                            $array['making'][$data['vendorId']]['multi'][90]['days'] = "60-90 days";
                                                            $array['making'][$data['vendorId']]['multi'][90]['due'][] = $data['total_due_amount'];
                                                        } else if ($data['due_days'] >= 91 && $data['due_days'] <= 180) {
                                                            // $array['making'][$data['vendorId']][$key][$data['tradename']][] = $data;
                                                            $array['making'][$data['vendorId']]['id'] = $data['vendorId'];
                                                            $array['making'][$data['vendorId']]['Debtor'] = $data['vendorName'];
                                                            $array['making'][$data['vendorId']]['multi'][180]['days'] = "91-180 days";
                                                            $array['making'][$data['vendorId']]['multi'][180]['due'][] = $data['total_due_amount'];
                                                        } else {
                                                            $array['making'][$data['vendorId']]['id'] = $data['vendorId'];
                                                            $array['making'][$data['vendorId']]['Debtor'] = $data['vendorName'];
                                                            $array['making'][$data['vendorId']]['multi']["above"]['days'] = "above 180 days";
                                                            $array['making'][$data['vendorId']]['multi']["above"]['due'][] = $data['total_due_amount'];
                                                        }
                                                    }

                                                    //console($array);
                                                    $overdue_total =0;
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
                                                        

                                                        $vendor_id = $data['id'];

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


                                                       
                                                       $overdue_sql = queryGet("SELECT SUM(dueAmt) AS total_due_amount FROM erp_grninvoice WHERE companyId = $company_id AND branchId=$branch_id AND vendorId=$vendor_id AND postingDate BETWEEN '2022-04-01' AND '2023-03-31' AND dueAmt!=0 AND dueDate < CURRENT_DATE;");
                                                    //console($overdue_sql);
                                                      if($overdue_sql['data']['overdue_amount'] != ""){
                                                       $overdue = $overdue_sql['data']['overdue_amount'];
                                                      }
                                                      else{
                                                        $overdue = 0;
                                                      }
                                                      
                                                     

                                                        //console($data);
                                                        $i = 1;
                                                        // console($data);
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
                                                                <td><?=  $overdue ?> </td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?= $thirty;  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo $sixty; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo $ninety; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo $oneeighty; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo $above; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td> <?php echo $total_sum; ?></td>
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
                                                        <td>Grand Total</td>
                                                        <?php }
                                                            $m++;
                                                            if (in_array($m, $settingsCheckbox)) { ?>
                                                        <td><?=$overdue_total ?></td>
                                                        <?php }
                                                            $m++;
                                                            if (in_array($m, $settingsCheckbox)) { ?>
                                                        <td><?= $thirty_total ?></td>
                                                        <?php }
                                                            $m++;
                                                            if (in_array($m, $settingsCheckbox)) { ?>
                                                        <td><?= $sixty_total ?></td>
                                                        <?php }
                                                            $m++;
                                                            if (in_array($m, $settingsCheckbox)) { ?>
                                                        <td><?= $ninety_total ?></td>
                                                        <?php }
                                                            $m++;
                                                            if (in_array($m, $settingsCheckbox)) { ?>
                                                        <td><?= $oneeighty_total ?></td>
                                                        <?php }
                                                            $m++;
                                                            if (in_array($m, $settingsCheckbox)) { ?>
                                                        <td><?= $total_above ?></td>
                                                        <?php }
                                                            $m++;
                                                            if (in_array($m, $settingsCheckbox)) { ?>
                                                        <td><?= $total_all ?></td>
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
                                                            <th> 0-30 days	</th>
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
                                                            <?php $p = 1; ?>
                                                            <table>
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
            <div class="container-fluid">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pt-1 my-2">
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Payable Analysis</h3>
                                            <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                                <option value="2022">FY-2022</option>
                                                <option value="2023">FY-2023</option>
                                                <option value="customrange">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                                                </option>
                                            </select>

                                        </div>
                                        <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                                    </li>
                                </ul>
                            </div>
                            <div class="daybook-filter-list filter-list">
                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn  waves-effect waves-light"><i class="fa fa-clock mr-2  "></i>Concised View</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>Detailed View</a>
                            </div>
                            <div class="card card-tabs mb-0" style="border-radius: 20px;">

                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>


                                        <div class="modal fade custom-range-modal" id="customRange" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title text-sm">Select Date Range</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- <input type="text" name="daterange" class="form-control" value="01/01/2018 - 01/15/2018" /> -->
                                                        <div class="date-range-input d-flex">
                                                            <div class="form-input">
                                                                <label for="">From Date</label>
                                                                <input type="date" class="form-control" name="from_date">
                                                            </div>
                                                            <div class="form-input">
                                                                <label for="">To Date</label>
                                                                <input type="date" class="form-control" name="to_date">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-primary float-right">Apply</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        $cond = '';

                                        $sql_list = "SELECT loc.othersLocation_name AS loc_name,vendorCode,vendorName,vendorDocumentDate AS invoice_date,vendorDocumentNo AS invoice_num,postingDate AS posting_date ,grnIvCode AS posting_doc_num,grnSubTotal AS base_amount,grnTotalCgst AS cgst,grnTotalSgst AS sgst,grnTotalIgst AS igst,grnTotalAmount AS invoice_amount,dueAmt,dueDate,grnCreatedAt AS created_at,grnCreatedBy AS created_by,grnUpdatedAt AS updated_at,grnUpdatedBy AS updated_by  FROM erp_grninvoice AS invoices LEFT JOIN erp_branch_otherslocation AS loc ON invoices.locationId=loc.othersLocation_id WHERE companyId=$company_id AND branchId=$branch_id AND dueAmt!=0 AND vendorDocumentDate BETWEEN '2022-04-01' AND '2023-03-31' ORDER BY invoice_date desc";

                                        $queryset = queryGet($sql_list, true);
                                       // console($queryset);
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
                                                             <th>Posting Date</th>
                                                         <?php }
                                                          $i++;
                                                          if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                              <th>Posting Document Number</th>
                                                          <?php }
                                                        
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Vendor Code</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Vendor Name</th>
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
                                                                <td> <?php echo $data['invoice_date']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $data['invoice_num']; ?></td>
                                                            <?php }
                                                             $i++;
                                                             if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                 <td> <?php echo $data['posting_date']; ?></td>
                                                             <?php }
                                                              $i++;
                                                              if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                  <td> <?php echo $data['posting_doc_num']; ?></td>
                                                              <?php }
                                                            
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo $data['vendorName']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['customer_name']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['base_amount']);  ?></td>
                                                                <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['igst']);  ?></td>
                                                                <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['sgst']);  ?></td>
                                                                <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['cgst']);  ?></td>
                                                                <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['invoice_amount']);  ?></td>
                                                                <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['dueAmt']);  ?></td>
                                                                <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['dueDate']);  ?></td>
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
                                                                <td><?php echo formatDateORDateTime(($data['updated_at']));  ?></td></var>
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
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Document Date</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Document Number</th>
                                                        <?php }
                                                         if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Posting Date</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Posting Document Number</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Vendor Code</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Vendor Name</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Base Amount</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>IGST</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>SGST</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>CGST</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Invoice Amount</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Amount</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Due Date</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created At</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Created By</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Updated At</th>
                                                            <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
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
                                                            <?php $p = 1; ?>
                                                            <table>
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
                                                                        Posting Date</td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                       Posting Document Number</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Vendor Code</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Vendor Name</td>
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
            <div class="container-fluid">

                <!-- row -->
                <div class="row p-0 m-0">
                    <div class="col-12 mt-2 p-0">
                        <div class="card card-tabs reports-card">
                            <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Payable Analysis</h3>
                                            <select name="fydropdown" id="fYDropdown" class="form-control fy-dropdown">
                                                <option value="2022">FY-2022</option>
                                                <option value="2023">FY-2023</option>
                                                <option value="customrange">
                                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customRange">Custom Range</button>
                                                </option>
                                            </select>
                                            <div class="modal fade custom-range-modal" id="customRange" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title text-sm">Select Date Range</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- <input type="text" name="daterange" class="form-control" value="01/01/2018 - 01/15/2018" /> -->
                                                            <div class="date-range-input d-flex">
                                                                <div class="form-input">
                                                                    <label for="">From Date</label>
                                                                    <input type="date" class="form-control" name="from_date">
                                                                </div>
                                                                <div class="form-input">
                                                                    <label for="">To Date</label>
                                                                    <input type="date" class="form-control" name="to_date">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-primary float-right">Apply</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm" onclick="openFullscreen();"><i class="fa fa-expand"></i></button>
                                    </li>
                                </ul>
                            </div>
                            <div class="daybook-filter-list filter-list">
                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn active waves-effect waves-light"><i class="fa fa-stream mr-2  active"></i>Visual Representation</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn waves-effect waves-light"><i class="fa fa-clock mr-2"></i>Concised View</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn  waves-effect waves-light"><i class="fa fa-list mr-2 "></i>Detailed View</a>
                            </div>

                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">

                                    <?php
                                    //Graph View SQL

                                    $sql_list = "SELECT vendorId,vendorName,DATEDIFF(dueDate,CURDATE())AS due_days,COUNT(*) AS count_,SUM(dueAmt) AS total_due_amount FROM erp_grninvoice AS iv WHERE companyId=$company_id AND branchId=$branch_id AND postingDate BETWEEN '2022-04-01' AND '2023-03-31' AND dueAmt!=0 AND dueDate>CURDATE() GROUP BY vendorId,vendorName,due_days ORDER BY due_days,vendorName,total_due_amount desc;";

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
                                                            <div id="chartDivPayableAnalysis" class="chartContainer"></div>
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
require_once("../common/footer.php");
?>

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


            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        columnSl++;
                        console.log(`columnSl=${columnSl}`);
                        if (columnSl == 8 || columnSl == 10) {
                            //For Dropdown column search
                            /*var column = this;
                            var select = $('<select class="form-control p-0"><option value="">All</option></select>')
                              .appendTo($(column.footer()).empty())
                              .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                console.log(val);
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                              });

                            column
                              .data()
                              .unique()
                              .sort()
                              .each(function(d, j) {
                                select.append('<option value="' + d + '">' + d + '</option>');
                              });*/
                        }
                        if (columnSl == 4 || columnSl == 5) {
                            var column = this;
                            var select = $('<input type="text" class="form-control" placeholder="dd-mm-yyyy">')
                                .appendTo($(column.footer()).empty());
                        }
                    });
            },
        });
        // Apply the search
        columnSl2 = 0;
        table.columns().every(function() {
            columnSl2++;
            if (columnSl2 == 4 || columnSl2 == 5) {
                var that = this;
                $('input', this.footer()).on('keyup change', function() {
                    let searchVal = `${(this.value).split("-")[2]}-${(this.value).split("-")[1]}-${(this.value).split("-")[0]}`;
                    that.search(searchVal).draw();
                });
            } else {
                var that = this;
                $('input', this.footer()).on('keyup change', function() {
                    that.search(this.value).draw();
                });
            }
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
            $('.modal-title').html(title);
            $('.custom-range-modal').modal('show');
        }
    });
</script>


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
                    innerIndex = innerObj.indexOf(obj.vendorName)

                    if (innerIndex !== -1) {
                        finalData[outerIndex].value += Number(obj.total_due_amount);
                        finalData[outerIndex].breakdown[innerIndex].value += Number(obj.total_due_amount);
                    } else {
                        finalData[outerIndex].value += Number(obj.total_due_amount);
                        finalData[outerIndex].breakdown.push({
                            "category": obj.vendorName,
                            "value": Number(obj.total_due_amount)
                        });
                    };
                } else {
                    finalData.push({
                        "category": obj.type,
                        "value": Number(obj.total_due_amount),
                        "breakdown": [{
                            "category": obj.vendorName,
                            "value": Number(obj.total_due_amount)
                        }]
                    });
                };
            };

            data = finalData

            /**
             * Chart container
             */

            // Create chart instance
            var chart = am4core.create("chartDivPayableAnalysis", am4core.Container);
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


            /**
             * Pie chart
             */

            // Create chart instance
            var pieChart = chart.createChild(am4charts.PieChart3D);
            pieChart.data = data;
            pieChart.hiddenState.properties.opacity = 0; // this creates initial fade-in

            pieChart.legend = new am4charts.Legend();
            // pieChart.innerRadius = am4core.percent(50);

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