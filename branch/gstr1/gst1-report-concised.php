<?php
require_once("../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("controller/gstr1-report.controller.php");
require_once("controller/gstr1-view-data.controller.php");


$dbObj = new Database();
$branchObj = $dbObj->queryGet('Select * from erp_branches where branch_id=' . $branch_id . ' and company_id=' . $company_id);
$branch_gstin_file_frequency = $branchObj["data"]["branch_gstin_file_frequency"] != "" ? $branchObj["data"]["branch_gstin_file_frequency"] : "monthly";
$branch_gstin_file_r1_day = $branchObj["data"]["branch_gstin_file_r1_day"];
$branch_gstin_file_r2b_day = $branchObj["data"]["branch_gstin_file_r2b_day"];
$branch_gstin_file_r3b_day = $branchObj["data"]["branch_gstin_file_r3b_day"];

$_SESSION["branch_gstin_file_frequency"] = $branch_gstin_file_frequency;
$_SESSION["branch_gstin_file_r1_day"] = $branch_gstin_file_r1_day;
$_SESSION["branch_gstin_file_r2b_day"] = $branch_gstin_file_r2b_day;
$_SESSION["branch_gstin_file_r3b_day"] = $branch_gstin_file_r3b_day;



$checkPrevCompGstr1StatusObj = $dbObj->queryGet('SELECT * FROM `erp_compliance_gstr1` WHERE `company_id` = ' . $company_id . ' AND `branch_id` = ' . $branch_id . ' AND `gstr1_return_period` = ' . date("mY"));
if ($checkPrevCompGstr1StatusObj["status"] != "success") {
    $insertPendingDataObj = $dbObj->queryInsert("INSERT INTO `erp_compliance_gstr1` SET `company_id`=" . $company_id . ",`branch_id`=" . $branch_id . ",`gstr1_return_period`=" . date("mY") . ",`created_by`='" . $created_by . "', `updated_by`='" . $updated_by . "'");
    // console($insertPendingDataObj);
} else {
    $complianceGSTR1ViewDataObj = new ComplianceGSTR1ViewData(date("01-m-Y"), date("t-m-Y"));
    $getSummaryDataObj = $complianceGSTR1ViewDataObj->getSummaryData();
    $totalCgst = 0;
    $totalSgst = 0;
    $totalIgst = 0;
    $totalCess = 0;
    $totalTax = 0;
    $totalInvAmount = 0;
    foreach ($getSummaryDataObj["data"] as $key => $row) {
        $totalCgst += $row["totalCgst"];
        $totalSgst += $row["totalSgst"];
        $totalIgst += $row["totalIgst"];
        $totalCess += $row["totalCess"];
        $totalTax += $row["totalTax"];
        $totalInvAmount += $row["totalInvAmount"];
    }
    $updatePendingDataObj = $dbObj->queryUpdate("UPDATE `erp_compliance_gstr1` SET `gstr1_return_total_cgst`=" . $totalCgst . ", `gstr1_return_total_sgst`=" . $totalSgst . ",`gstr1_return_total_igst`=" . $totalIgst . ",`gstr1_return_total_cess`=" . $totalCess . ", `updated_by`='" . $updated_by . "' WHERE `company_id`=" . $company_id . " AND `branch_id`=" . $branch_id . " AND `gstr1_return_period`=" . date("mY"));
}


?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">


<style>
    section.gstr-1 {
        padding: 0px 20px;
    }

    .head-btn-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .gst-one-filter {
        left: 0;
        top: 0;
    }

    .gst-one-filter a.active {
        background-color: #003060;
        color: #fff;
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

    .gst-consised-view tr td {
        /* white-space: pre-line !important; */
        text-align: center !important;
    }

    .gst-consised-view tr th {
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

    .dataTables_scrollBody {
        max-height: 100% !important;
        height: auto !important;
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
        top: 0px;
        left: 18px;
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
        margin-bottom: 25px;
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
    }

    .dataTables_scrollHeadInner {
        width: 100% !important;
    }

    table.defaultDataTable {
        width: 100% !important;
    }
</style>


<div class="content-wrapper">
    <section class="gstr-1">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="<?= BRANCH_URL ?>gstr1/gst1-report-graphical.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>GSTR1</a></li>
            <li class="breadcrumb-item active"><a href="" class="text-dark"><i class="fa fa-list po-list-icon"></i>Concised Report</a></li>
            <li class="back-button">
                <a href="gst1-report-graphical.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
        <!-- <h4 class="text-lg font-bold mt-4 mb-4">GSTR-1</h4> -->
        <div class="head-btn-section mb-3">
            <div class="filter-list gst-one-filter">
                <a href="./gst1-report-graphical.php" class="btn"><i class="fas fa-chart-bar mr-2"></i>Graphical View</a>
                <a href="" class="btn active"><i class="fa fa-list mr-2"></i>Concised View</a>
            </div>
            <!-- <a class="btn btn-primary" href="./gst1-preview.php"><i class="fa fa-file mr-2"></i>Action/ File</a> -->
        </div>

        <div class="card">
            <div class="card-body p-0">
                <?php
                $complianceGSTR1ReportObj = new ComplianceGSTR1Report();
                $tableListObj = $complianceGSTR1ReportObj->getGstr1FillingTableList();
                // console($tableListObj);
                // console($branchObj);
                // console($GLOBALS);
                ?>

                <a type="button" class="btn add-col setting-menu" data-toggle="modal" data-target="#myModal2"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                <table id="datatable" width="100" class="table table-hover defaultDataTable gst-consised-view">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Period</th>
                            <th>CGST</th>
                            <th>SGST</th>
                            <th>IGST</th>
                            <th>CESS</th>
                            <th>File Status</th>
                            <th>Last Update</th>
                            <th>View/Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $fileStatusText = [
                            0 => "Pending",
                            1 => "Pending for approval",
                            2 => "Pending for push data",
                            3 => "Pending for push reset data",
                            4 => "Filed",
                            5 => "Marked as filed",
                        ];
                        foreach ($tableListObj["data"] as $sl => $row) {
                        ?>
                            <tr>
                                <td><?= $sl + 1 ?></td>
                                <td><?= date("F, Y", strtotime($row["gstr1_return_period"])) ?></td>
                                <td><?= ($row["created_at"] == $row["updated_at"]) ? "-" : $row["gstr1_return_total_cgst"] ?></td>
                                <td><?= ($row["created_at"] == $row["updated_at"]) ? "-" : $row["gstr1_return_total_sgst"] ?></td>
                                <td><?= ($row["created_at"] == $row["updated_at"]) ? "-" : $row["gstr1_return_total_igst"] ?></td>
                                <td><?= ($row["created_at"] == $row["updated_at"]) ? "-" : $row["gstr1_return_total_cess"] ?></td>
                                <td><span class="status"><?= ucfirst($fileStatusText[$row["gstr1_return_file_status"]]) ?></span></td>
                                <td><?= ($row["created_at"] == $row["updated_at"]) ? "-" : $row["updated_at"] ?></td>
                                <td>
                                    <a class="btn btn-sm" href="./gst1-preview.php?period=<?= base64_encode($row["gstr1_return_period"]) ?>"><i class="fa fa-file po-list-icon"></i></a>
                                    <a style="cursor:pointer" data-toggle="modal" data-target="#gsttrail" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        <!-- <div class="modal fade right audit-history-modal show" id="gsttrail" aria-labelledby="innerModalLabel" aria-modal="true" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content auditTrailBodyContentLineDiv">
                                    <div class="modal-header">
                                        <div class="head-audit">
                                            <p>Delivery Creation</p>
                                        </div>
                                        <div class="head-audit">
                                            <p>Sonie Kushwaha</p>
                                            <p>25-08-2023 12:15:00</p>
                                        </div>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="free-space-bg">
                                            <div class="color-define-text">
                                                <p class="update"><span></span> Record Updated </p>
                                                <p class="all"><span></span> New Added </p>
                                            </div>
                                            <ul class="nav nav-tabs pb-0" id="myTab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" id="concise-tab" data-toggle="tab" href="#consize" role="tab" aria-controls="concise" aria-selected="true"><i class="fa fa-th-large mr-2" aria-hidden="true"></i> Concised View</a>
                                                </li>

                                                <li class="nav-item">
                                                    <a class="nav-link" id="detail-tab" data-toggle="tab" href="#detail" role="tab" aria-controls="detail" aria-selected="false"><i class="fa fa-list mr-2" aria-hidden="true"></i>Detailed View</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content pt-0" id="myTabContent">
                                            <div class="tab-pane fade active show" id="consize" role="tabpanel" aria-labelledby="consize-tab"></div>
                                            <div class="tab-pane fade" id="detail" role="tabpanel" aria-labelledby="detail-tab">
                                                <div class="dotted-box">
                                                    <p class="overlap-title">Sales Order Delivery Details</p>
                                                    <div class="box-content">
                                                        <p>Delivery no</p>
                                                        <div class="existing-cross-data">
                                                            <p>1692945900771</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>So number</p>
                                                        <div class="existing-cross-data">
                                                            <p>SO2308117</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>Customer shipping address</p>
                                                        <div class="existing-cross-data">
                                                            <p>471, 11, Anna Salai, 600018, Teynampet, Teynampet, Chennai, Tamil Nadu</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>Customer billing address</p>
                                                        <div class="existing-cross-data">
                                                            <p>471, 11, Anna Salai, 600018, Teynampet, Teynampet, Chennai, Tamil Nadu</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>So id</p>
                                                        <div class="existing-cross-data">
                                                            <p>162</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>Customer id</p>
                                                        <div class="existing-cross-data">
                                                            <p>2631</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>Delivery date</p>
                                                        <div class="existing-cross-data">
                                                            <p>23-07-2023</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>So delivery posting date</p>
                                                        <div class="existing-cross-data">
                                                            <p>25-08-2023</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>Profit center</p>
                                                        <div class="existing-cross-data">
                                                            <p>3</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>DeliveryStatus</p>
                                                        <div class="existing-cross-data">
                                                            <p>open</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>Customer po no</p>
                                                        <div class="existing-cross-data">
                                                            <p>CON123456</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>TotalItems</p>
                                                        <div class="existing-cross-data">
                                                            <p>1</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>TotalDiscount</p>
                                                        <div class="existing-cross-data">
                                                            <p>0.00</p>
                                                        </div>
                                                    </div>
                                                    <div class="box-content">
                                                        <p>TotalAmount</p>
                                                        <div class="existing-cross-data">
                                                            <p>3540.00</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="dotted-box">
                                                    <p class="overlap-title">Item Details</p>
                                                    <div class="dotted-box">
                                                        <p class="overlap-title">22000001</p>
                                                        <div class="box-content">
                                                            <p>ItemCode</p>
                                                            <div class="existing-cross-data">
                                                                <p>22000001</p>
                                                            </div>
                                                        </div>
                                                        <div class="box-content">
                                                            <p>ItemQuantity</p>
                                                            <div class="existing-cross-data">
                                                                <p>3</p>
                                                            </div>
                                                        </div>
                                                        <div class="box-content">
                                                            <p>Uom</p>
                                                            <div class="existing-cross-data">
                                                                <p>1</p>
                                                            </div>
                                                        </div>
                                                        <div class="box-content">
                                                            <p>ItemPrice</p>
                                                            <div class="existing-cross-data">
                                                                <p>1000</p>
                                                            </div>
                                                        </div>
                                                        <div class="box-content">
                                                            <p>ItemDiscount</p>
                                                            <div class="existing-cross-data">
                                                                <p>0</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal" id="myModal2">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Table Column Settings</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                        <div class="modal-body" style="max-height: 450px;">
                            <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                            <input type="hidden" name="pageTableName" value="ERP_ACC_JOURNAL" />
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
                                                GST-1</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Period</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                Status</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="<?php echo $p; ?>" />
                                                ARN</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="<?php echo $p; ?>" />
                                                Financial year</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="<?php echo $p; ?>" />
                                                Created at</td>
                                        </tr>

                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="<?php echo $p; ?>" />
                                                Created by</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="<?php echo $p; ?>" />
                                                Approved</td>
                                        </tr>
                                        <tr>
                                            <td valign="top" style="width: 165px"><input type="checkbox" <?php $p++;
                                                                                                            echo (in_array($p, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="<?php echo $p; ?>" />
                                                View</td>
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

    </section>
</div>


<script>
    $(document).ready(function() {


        // DataTable
        var columnSl = 0;
        var table = $("#datatable").DataTable({
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



<?php
require_once("../common/footer.php");
?>