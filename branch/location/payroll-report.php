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


if (isset($_POST['drop_val'])) {
    $year = $_POST['year'];
    $month = $_POST['month'];
} else {
    $year = date('Y');
    $month = date('m');
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


    .customrange-section {
        position: absolute;
        bottom: 20px;
        right: 270px;
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
                            <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Receivable Analysis</h3>
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


                                        $sql_list = "SELECT SUM(`gross`) as gross, SUM(`esi`) as esi, SUM(`pf_employee`) as pf_employee,SUM(`pf_employeer`) as pf_employeer,SUM(`pf_admin`) as pf_admin, SUM(`ptax`) as ptax,`payroll_month` as month,`payroll_year` as year FROM `erp_payroll` WHERE `company_id`=$company_id AND `location_id`=$location_id AND `branch_id`=$branch_id GROUP BY `payroll_year`, `payroll_month`";
                                        // console($sal);

                                        $queryset = queryGet($sql_list, true);
                                        $num_list = $queryset['numRows'];



                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PAYROLL_REPORT" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
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
                                                            <th>Month and Year</th>
                                                        <?php }

                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>Gross</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>PF Employee</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>PF Employeer</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>PF Admin</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>PTax</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox)) { ?>
                                                            <th>ESI</th>
                                                        <?php }


                                                        ?>
                                                    </tr>
                                                </thead>




                                                <tbody>
                                                    <?php
                                                    // console($BranchPrObj->fetchBranchSoListing()['data']);

                                                    foreach ($queryset['data'] as $data) {
                                                        // console($data);

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
                                                                <td><?= $data['month'] . "-" . $data['year'] ?>

                                                                </td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?= $data['gross'] ?>

                                                                </td>

                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?= $data['pf_employee'] ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?= $data['pf_employeer'] ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?= $data['pf_admin'] ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?= $data['ptax'] ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox)) { ?>
                                                                <td><?= $data['esi'] ?></td>
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
                                                            <th>Month and Year</th>
                                                        <?php }

                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>Gross</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>PF Employee</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>PF Employeer</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>PF Admin</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>PTax</th>
                                                        <?php }
                                                        $j++;
                                                        if (in_array($j, $settingsCheckbox)) { ?>
                                                            <th>ESI</th>
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
                                                    <input type="hidden" name="pageTableName" value="ERP_PAYROLL_REPORT<?= $pageName ?>" />
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
                                                                        Month and Year</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        Gross</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        PF Employee</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        PF Employeer </td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        PF Admin</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        PTax</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                                        ESI</td>
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
                            <div class="p-0 pt-1 my-2 pb-2" style="border-bottom: 1px solid #dbe5ee;">
                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Receivable Analysis</h3>
                                        </div>


                                    </li>
                                </ul>
                                <!---------------------- Search END -->
                            </div>
                            <div class="daybook-filter-list filter-list">
                                <a href="<?= $_SERVER['PHP_SELF']; ?>" class="btn waves-effect waves-light"><i class="fa fa-stream mr-2"></i>Visual Representation</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?concised-view" class="btn  waves-effect waves-light"><i class="fa fa-clock mr-2  "></i>Concised View</a>
                                <a href="<?= $_SERVER['PHP_SELF']; ?>?detailed-view" class="btn active waves-effect waves-light"><i class="fa fa-list mr-2 active "></i>Detailed View</a>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="customrange-section">
                                        <form method="POST" action="<?php $_SERVER['PHP_SELF']; ?>" class="custom-Range" id="date_form" name="date_form">
                                            <input type="hidden" name="drop_id" id="drop_id" class="form-control" value="">
                                            <input type="hidden" name="drop_val" id="drop_val" class="form-control" value="customrange">
                                            <div class="date-range-input d-flex">
                                                <div class="form-input">
                                                    <label for="selectMonth" class="mb-0">Select Month</label>
                                                    <select name="month" id="" class="form-control">
                                                        <option value="01" <?php if ($month == '01') {
                                                                                echo "selected";
                                                                            } ?>>January</option>
                                                        <option value="02" <?php if ($month == '02') {
                                                                                echo "selected";
                                                                            } ?>>February</option>
                                                        <option value="03" <?php if ($month == '03') {
                                                                                echo "selected";
                                                                            } ?>>March</option>
                                                        <option value="04" <?php if ($month == '04') {
                                                                                echo "selected";
                                                                            } ?>>April</option>

                                                        <option value="05" <?php if ($month == '05') {
                                                                                echo "selected";
                                                                            } ?>>May</option>
                                                        <option value="06" <?php if ($month == '06') {
                                                                                echo "selected";
                                                                            } ?>>June</option>
                                                        <option value="07" <?php if ($month == '07') {
                                                                                echo "selected";
                                                                            } ?>>July</option>
                                                        <option value="08" <?php if ($month == '08') {
                                                                                echo "selected";
                                                                            } ?>>August</option>

                                                        <option value="05" <?php if ($month == '09') {
                                                                                echo "selected";
                                                                            } ?>>September</option>
                                                        <option value="06" <?php if ($month == '10') {
                                                                                echo "selected";
                                                                            } ?>>October</option>
                                                        <option value="07" <?php if ($month == '11') {
                                                                                echo "selected";
                                                                            } ?>>November</option>
                                                        <option value="08" <?php if ($month == '12') {
                                                                                echo "selected";
                                                                            } ?>>Descember</option>
                                                    </select>
                                                </div>
                                                <div class="form-input">
                                                    <label for="selectYear" class="mb-0">Select Year</label>
                                                    <select name="year" id="" class="form-control">
                                                        <option value="2023" <?php if ($year == '2023') {
                                                                                    echo "selected";
                                                                                } ?>>2023</option>
                                                        <option value="2022" <?php if ($year == '2022') {
                                                                                    echo "selected";
                                                                                } ?>>2022</option>
                                                        <option value="2021" <?php if ($year == '2021') {
                                                                                    echo "selected";
                                                                                } ?>>2021</option>
                                                        <option value="2020" <?php if ($year == '2020') {
                                                                                    echo "selected";
                                                                                } ?>>2020</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary float-right waves-effect waves-light" id="rangeid" name="add_date_form">Apply</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>

                            <div class="card card-tabs mb-0" style="border-radius: 20px;">

                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">
                                        <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                        <?php









                                        $sql_list = "SELECT * FROM `erp_payroll` WHERE `payroll_month` = $month AND `payroll_year`= $year AND `location_id`=$location_id";

                                        $queryset = queryGet($sql_list, true);
                                        //console($queryset);
                                        $num_list = $queryset['numRows'];

                                        $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_PAYROLL_REPORT_CONCISED_VIEW_" . $pageName, $_SESSION["logedBranchAdminInfo"]["adminId"]);
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
                                                            <th>Cost Center</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Gross</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>PF Employee</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th> PF Employeer</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>PF Admin</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>ESI</th>
                                                        <?php }
                                                        $i++;
                                                        if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                            <th>PTax</th>
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


                                                        $cost_center = queryGet("SELECT * FROM `erp_cost_center` WHERE `CostCenter_id` = '" . $data['alpha_costcenter_id'] . "' AND `location_id`=$location_id");
                                                        //console($cost_center);

                                                    ?>
                                                        <tr>
                                                            <?php if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $sl; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?= $cost_center['data']['CostCenter_code'] ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td> <?php echo $data['gross']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo $data['pf_employee']; ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['pf_employeer']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['pf_admin']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['esi']);  ?></td>
                                                            <?php }
                                                            $i++;
                                                            if (in_array($i, $settingsCheckbox_concised_view)) { ?>
                                                                <td><?php echo ($data['ptax']);  ?></td>
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
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Cost Center</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>Gross</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th> PF Employee </th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th> PF Employeer</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th> PF Admin</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>ESI</th>
                                                        <?php }
                                                        if (in_array($j++, $settingsCheckbox_concised_view)) { ?>
                                                            <th>PTax</th>
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
                                                    <input type="hidden" name="pageTableName" value="ERP_PAYROLL_REPORT_CONCISED_VIEW_<?= $pageName ?>" />
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
                                                                        Cost Center</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        Gross</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        PF Employee</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        PF Employeer</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        PF Admin</td>
                                                                </tr>
                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        ESI </td>
                                                                </tr>

                                                                <tr>
                                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                                                    echo (in_array($p, $settingsCheckbox_concised_view) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox_concised_view1" value="<?php echo $p; ?>" />
                                                                        PTax </td>
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

                                <!---------------------- Search START -->
                                <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                                    <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                                        <div class="label-select">
                                            <h3 class="card-title mb-0">Receivable Analysis</h3>
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
                                    $sql_list = "SELECT payroll_month, SUM(gross) AS total_gross,SUM(pf_employee + pf_employeer + pf_admin + ptax + esi) AS deduction,SUM(gross + pf_employee + pf_employeer + pf_admin + ptax + esi) AS nate_amount FROM erp_payroll WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id
                                     AND payroll_year=2023 GROUP BY payroll_month";

                                    $queryset1 = queryGet($sql_list, true);
                                    // console($queryset1);
                                    $chartData2 = json_encode($queryset1, true);

                                    $num_list = $queryset1['numRows'];


                                    if ($num_list > 0) {
                                        $i = 1;
                                    ?>

                                        <div class="container-fluid mt-10">

                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 d-flex">
                                                    <div class="card flex-fill reports-card">
                                                        <div class="card-body">
                                                            <div id="chartDivClusteredColumn" class="chartContainer"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    <?php } else { ?>
                                        <p>No data Found</p>
                                    <?php } ?>
                                </div>




                                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab" style="background: #fff; border-radius: 20px;">


                                    <?php


                                    // console($_POST);
                                    //Graph View SQL 
                                    //Changes                                       
                                    $sql_list = "SELECT cc.CostCenter_desc AS cc_name,erp_payroll.payroll_month,SUM(erp_payroll.pf_employee + erp_payroll.pf_employeer + erp_payroll.pf_admin + erp_payroll.ptax + erp_payroll.esi) AS deduction FROM erp_payroll INNER JOIN erp_cost_center AS cc ON erp_payroll.alpha_costcenter_id=cc.CostCenter_id WHERE erp_payroll.company_id=$company_id AND erp_payroll.branch_id=$branch_id AND erp_payroll.location_id=$location_id AND erp_payroll.payroll_year=2023 GROUP BY cc.CostCenter_desc,erp_payroll.payroll_month";

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
    var chartData2 = <?php echo $chartData2; ?>;
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

        function getMonthName(monthNumber) {
            const monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            const date = new Date();
            date.setMonth(monthNumber - 1); // Subtract 1 because months are zero-based in JavaScript

            return date.toLocaleString("en-US", {
                month: "long"
            }); // "long" format gives the full month name
        }

        let formattedData = chartData.data.map(obj => {

            obj.payroll_month = getMonthName(obj.payroll_month);

            return obj;
        });

        for (let obj of formattedData) {

            const outerObj = finalData.map(obj => {
                return obj.category
            })
            outerIndex = outerObj.indexOf(obj.cc_name)

            if (outerIndex !== -1) {

                const innerObj = finalData[outerIndex].breakdown.map(obj => {
                    return obj.category
                })
                innerIndex = innerObj.indexOf(obj.payroll_month)

                if (innerIndex !== -1) {
                    finalData[outerIndex].value += Number(obj.deduction);
                    finalData[outerIndex].breakdown[innerIndex].value += Number(obj.deduction);
                } else {
                    finalData[outerIndex].value += Number(obj.deduction);
                    finalData[outerIndex].breakdown.push({
                        "category": obj.payroll_month,
                        "value": Number(obj.deduction)
                    });
                };
            } else {
                finalData.push({
                    "category": obj.cc_name,
                    "value": Number(obj.deduction),
                    "breakdown": [{
                        "category": obj.payroll_month,
                        "value": Number(obj.deduction)
                    }]
                });
            };
        };

        data = finalData

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




    // ====================================== Clustered Column Chart ======================================
    am4core.ready(function() {

        // Themes
        am4core.useTheme(am4themes_animated);

        // Create chart instance
        var chart = am4core.create("chartDivClusteredColumn", am4charts.XYChart);
        chart.logo.disabled = true;
        chart.colors.step = 2;

        chart.legend = new am4charts.Legend()
        chart.legend.position = 'top'
        chart.legend.paddingBottom = 10
        chart.legend.labels.template.maxWidth = 95

        var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
        xAxis.dataFields.category = 'payroll_month'
        xAxis.renderer.cellStartLocation = 0.1
        xAxis.renderer.cellEndLocation = 0.9
        xAxis.renderer.grid.template.location = 0;

        var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
        yAxis.min = 0;

        function createSeries(value, name) {
            var series = chart.series.push(new am4charts.ColumnSeries())
            series.dataFields.valueY = value
            series.dataFields.categoryX = 'payroll_month'
            series.name = name

            series.events.on("hidden", arrangeColumns);
            series.events.on("shown", arrangeColumns);

            var bullet = series.bullets.push(new am4charts.LabelBullet())
            bullet.interactionsEnabled = false
            bullet.dy = 10;
            bullet.label.text = '{valueY}'
            bullet.label.fill = am4core.color('#ffffff')

            return series;
        }


        function getMonthName(monthNumber) {
            const monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            const date = new Date();
            date.setMonth(monthNumber - 1); // Subtract 1 because months are zero-based in JavaScript

            return date.toLocaleString("en-US", {
                month: "long"
            }); // "long" format gives the full month name
        }

        let formattedData = chartData2.data.map(obj => {

            obj.payroll_month = getMonthName(obj.payroll_month);

            return obj;
        });

        // Add data
        chart.data = formattedData;

        createSeries('total_gross', 'Gross Amount');
        createSeries('deduction', 'Deduction');
        createSeries('nate_amount', 'Net Amount');

        function arrangeColumns() {

            var series = chart.series.getIndex(0);

            var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
            if (series.dataItems.length > 1) {
                var x0 = xAxis.getX(series.dataItems.getIndex(0), "categoryX");
                var x1 = xAxis.getX(series.dataItems.getIndex(1), "categoryX");
                var delta = ((x1 - x0) / chart.series.length) * w;
                if (am4core.isNumber(delta)) {
                    var middle = chart.series.length / 2;

                    var newIndex = 0;
                    chart.series.each(function(series) {
                        if (!series.isHidden && !series.isHiding) {
                            series.dummyData = newIndex;
                            newIndex++;
                        } else {
                            series.dummyData = chart.series.indexOf(series);
                        }
                    })
                    var visibleCount = newIndex;
                    var newMiddle = visibleCount / 2;

                    chart.series.each(function(series) {
                        var trueIndex = chart.series.indexOf(series);
                        var newIndex = series.dummyData;

                        var dx = (newIndex - trueIndex + middle - newMiddle) * delta

                        series.animate({
                            property: "dx",
                            to: dx
                        }, series.interpolationDuration, series.interpolationEasing);
                        series.bulletsContainer.animate({
                            property: "dx",
                            to: dx
                        }, series.interpolationDuration, series.interpolationEasing);
                    })
                }
            }
        }

    });
    // ++++++++++++++++++++++++++++++++++++++ Clustered Column Chart ++++++++++++++++++++++++++++++++++++++
</script>