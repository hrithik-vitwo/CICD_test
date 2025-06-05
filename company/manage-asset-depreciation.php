<?php
include("../app/v1/connection-company-admin.php");
// administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-depreciation.php");
//console($_POST);
// console($_GET);
if (isset($_POST['addNewDepreciationFormSubmitBtn'])) {
    // console($_POST);
    $newStatusObj = importdep($_POST);
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}

if (isset($_POST['it_btn'])) {

    $newStatusObj = importdep($_POST);
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}

if (isset($_POST['edit_asset_dep'])) {
    // console($_POST);
    // exit();

    $newStatusObj = editRule($_POST);
    swalToast($newStatusObj["status"], $newStatusObj["message"], COMPANY_URL . 'manage-asset-depreciation.php');
}

$ruleis = "";
$method = "";
$schudel = "";
global $company_id;
$alreadyexist1 = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `company_id`='" . $company_id . "'", true);
if ($alreadyexist1['numRows'] > 0) {
$companyd = queryGet("SELECT `depreciation_schedule`,`depreciation_type` FROM `erp_companies` WHERE `company_id`= '" . $company_id . "'");
$method = $companyd['data']['depreciation_type'];
$schudel = $companyd['data']['depreciation_schedule'];
}



?>

<style>
    .rule-company-select {
        display: flex;
        padding-top: 1em !important;
    }

    .rule-select-radio,
    .company-select-radio {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .rule-select-radio .form-inline,
    .company-select-radio .form-inline {
        gap: 7px;
    }

    .rule-select-card-body,
    .company-select-card-body {
        display: grid;
        padding: 10px 20px !important;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">

                    <ul class="nav nav-tabs mb-3 border-bottom-0" id="custom-tabs-two-tab" role="tablist">
                        <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                            <h3 class="card-title">Manage Depreciation Key</h3>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary float-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>
                        </li>
                    </ul>

                    <?php

                    ?>
                    <div class="card card-tabs">
                        <div class="tab-content rule-company-select" id="custom-tabs-two-tabContent">

                            <div class="card-body rule-select-card-body">
                                <h2 class="card-title text-xs font-bold">Select Rule Type</h2>
                                <div class="rule-select-radio">
                                    <div class="form-inline">
                                        <label> Company Rule</label>
                                        <input type="radio" name="rule" value="company" id="companyRule">
                                    </div>
                                    <div class="form-inline">
                                        <label> Management Rule</label>
                                        <input type="radio" name="rule" value="management" id="managementRule">
                                    </div>
                                    <div class="form-inline">
                                        <label> IT Rule</label>
                                        <input type="radio" name="rule" value="it" id="itRule">
                                    </div>

                                </div>
                            </div>




                            <div class="card-body company-select-card-body" id="subtype">
                                <h3 class="card-title text-xs font-bold">Select Company Type</h3>
                                <div class="company-select-radio">
                                    <div class="form-inline">
                                        <label> WDV</label>
                                        <input type="radio" name="crule" id="wdv" value="WDV" />
                                    </div>
                                    <div class="form-inline">
                                        <label> SLM </label>
                                        <input type="radio" name="crule" id="slm" value="SLM" />
                                    </div>
                                </div>
                            </div>
                            <div class="card-body company-select-card-body" id="subtype">
                                <h3 class="card-title text-xs font-bold">Select Depreciation Schedule </h3>
                                <div class="company-select-radio">
                                    <div class="form-inline">
                                        <label> Monthly</label>
                                        <input type="radio" name="srule" id="monthly" value="monthly" />
                                    </div>
                                    <div class="form-inline">
                                        <label> Yearly </label>
                                        <input type="radio" name="srule" id="yearly" value="yearly" />
                                    </div>
                                </div>
                            </div>

                        </div>



                        <div class="card company" id="companyDiv">


                            <div class="row p-0 m-0">

                                <?php
                                $cond = '';
                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND functionalities_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }

                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                    $cond .= " AND (`functionalities_name` like '%" . $_REQUEST['keyword'] . "%' OR `functionalities_desc` like '%" . $_REQUEST['keyword'] . "%')";
                                }
                                // echo $sql_list = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND company_id= 0 " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                $check_it_exists = queryGet("SELECT * FROM `erp_depreciation_table` WHERE  `rule_type`= 'Company' AND company_id=$company_id", true);
                                if ($check_it_exists['numRows'] > 0) {
                                    $sql_list = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND `rule_type`= 'Company' AND company_id=$company_id  " . $sts . "  ORDER BY depreciation_id asc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                    $countShow = "SELECT count(*) FROM `erp_depreciation_table` WHERE 1 " . $cond . " AND `rule_type`= 'Company' AND company_id= $company_id " . $sts . "  ORDER BY depreciation_id asc  ";
                                } else {

                                    $sql_list = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND `rule_type`= 'Company' AND company_id=0   " . $sts . "  ORDER BY depreciation_id asc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                    $countShow = "SELECT count(*) FROM `erp_depreciation_table` WHERE 1 " . $cond . " AND `rule_type`= 'Company' AND company_id=0   " . $sts . "  ORDER BY depreciation_id asc  ";
                                }

                                $qry_list = mysqli_query($dbCon, $sql_list);
                                $num_list = mysqli_num_rows($qry_list);

                                $countQry = mysqli_query($dbCon, $countShow);
                                $rowCount = mysqli_fetch_array($countQry);
                                // console($qry_list);
                                // exit();
                                $count = $rowCount[0];
                                $cnt = $GLOBALS['start'] + 1;
                                $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_DEPRECIATION_TABLE", $company_id);
                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox = unserialize($settingsCh);
                                if ($num_list > 0) {
                                ?>
                                    <table id="mytable" class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>

                                                <th>Depreciation Key</th>

                                                <th>Asset Class</th>

                                                <th>Parent Key</th>

                                                <th>Asset Life</th>

                                                <th>WDV</th>

                                                <th>SLM</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($qry_list)) {
                                                // console($row);
                                                $dep_id = $row['depreciation_id'];
                                            ?>
                                                <tr>
                                                    <td><?= $cnt++ ?></td>

                                                    <td><?= $row['desp_key'] ?></td>

                                                    <td><?= $row['asset_class'] ?></td>

                                                    <td><?= $row['parent_code'] ?></td>

                                                    <td><?= $row['asset_life'] ?></td>

                                                    <td><?= $row['wdv'] ?></td>

                                                    <td><?= $row['slm'] ?></td>

                                                </tr>


                                            <?php  } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8">
                                                    <!-- Start .pagination -->

                                                    <?php
                                                    if ($count > 0 && $count > $GLOBALS['show']) {
                                                    ?>
                                                        <div class="pagination align-right">
                                                            <?php pagination($count, "frm_opts"); ?>
                                                        </div>

                                                        <!-- End .pagination -->

                                                    <?php  } ?>

                                                    <!-- End .pagination -->
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                <?php } else { ?>
                                    <table id="mytable" class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <td>
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                <?php
                                }
                                ?>
                            </div>
                            <form method="POST" id="addNewDepreciationForm">
                                <input type="hidden" name="rule_type" id="rule_type_company">
                                <input type="hidden" name="rule_subtype" id="rule_subtype_company">
                                <input type="hidden" name="schedule" id="schedule_company">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <?php
                                    if ($check_it_exists['numRows'] == 0) {
                                    ?>
                                        <button type="submit" name="addNewDepreciationFormSubmitBtn" id="addNewDepreciationFormSubmitBtn" class="btn btn-primary items-search-btn float-right">Import</button>

                                    <?php }
                                    ?>
                                </div>
                            </form>
                        </div>
                        <div class="card company" id="managementDiv">

                            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                                <div class="card-body">
                                    <div class="row filter-serach-row">
                                        <div class="col-lg-2 col-md-2 col-sm-12">
                                        </div>
                                        <div class="col-lg-10 col-md-10 col-sm-12">
                                            <div class="row table-header-item">
                                                <!-- <div class="col-lg-11 col-md-11 col-sm-11">
                                                    <div class="section serach-input-section">
                                                        <input type="text" id="myInput" placeholder="" class="field form-control" />
                                                        <div class="icons-container">
                                                            <div class="icon-search">
                                                                <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                            </div>
                                                            <div class="icon-close">
                                                                <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>
                                                                <script>
                                                                    var input = document.getElementById("myInput");
                                                                    input.addEventListener("keypress", function(event) {
                                                                        if (event.key === "Enter") {
                                                                            event.preventDefault();
                                                                            document.getElementById("myBtn").click();
                                                                        }
                                                                    });
                                                                </script>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <!-- <div class="col-lg-1 col-md-1 col-sm-1">
                                                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                            </form>
                            <div class="row p-0 m-0">

                                <?php
                                $cond = '';
                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND functionalities_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }

                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                    $cond .= " AND (`functionalities_name` like '%" . $_REQUEST['keyword'] . "%' OR `functionalities_desc` like '%" . $_REQUEST['keyword'] . "%')";
                                }
                                // echo $sql_list = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND company_id= 0 " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                $check_it_exists1 = queryGet("SELECT * FROM `erp_depreciation_table` WHERE  `rule_type`= 'Management' AND company_id=$company_id", true);
                                if ($check_it_exists1['numRows'] > 0) {
                                    $sql_list1 = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND `rule_type`= 'Management' AND company_id=$company_id  " . $sts . "  ORDER BY depreciation_id asc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                    $countShow1 = "SELECT count(*) FROM `erp_depreciation_table` WHERE 1 " . $cond . " AND `rule_type`= 'Management' AND company_id= $company_id " . $sts . "  ORDER BY depreciation_id asc  ";
                                } else {

                                    $sql_list1 = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND `rule_type`= 'Company' AND company_id=0   " . $sts . "  ORDER BY depreciation_id asc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                    $countShow1 = "SELECT count(*) FROM `erp_depreciation_table` WHERE 1 " . $cond . " AND `rule_type`= 'Company' AND company_id=0   " . $sts . "  ORDER BY depreciation_id asc  ";
                                }

                                $qry_list1 = mysqli_query($dbCon, $sql_list1);
                                $num_list1 = mysqli_num_rows($qry_list1);

                                $countQry1 = mysqli_query($dbCon, $countShow1);
                                $rowCount1 = mysqli_fetch_array($countQry1);
                                // console($qry_list);
                                // exit();
                                $count1 = $rowCount1[0];
                                $cnt = $GLOBALS['start'] + 1;
                                $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_DEPRECIATION_TABLE", $company_id);
                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox = unserialize($settingsCh);
                                if ($num_list > 0) {
                                ?>
                                    <table id="mytable" class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>

                                                <th>Depreciation Key</th>

                                                <th>Asset Class</th>

                                                <th>Parent Key</th>

                                                <th>Asset Life</th>

                                                <th>WDV</th>

                                                <th>SLM</th>
                                                <?php
                                                if ($check_it_exists1['numRows'] > 0) {
                                                ?>
                                                    <th>Edit</th>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($qry_list1)) {
                                                // console($row);
                                                $dep_id = $row['depreciation_id'];
                                            ?>
                                                <tr>
                                                    <td><?= $cnt++ ?></td>

                                                    <td><?= $row['desp_key'] ?></td>

                                                    <td><?= $row['asset_class'] ?></td>

                                                    <td><?= $row['parent_code'] ?></td>

                                                    <td><?= $row['asset_life'] ?></td>

                                                    <td><?= $row['wdv'] ?></td>

                                                    <td><?= $row['slm'] ?></td>
                                                    <?php
                                                    if ($check_it_exists1['numRows'] > 0) {
                                                    ?>
                                                        <td>
                                                            <a style="cursor:pointer" data-toggle="modal" data-target="#fcompanyDepriciationRule_<?= $dep_id ?>" class="btn btn-sm" title="edit"><i class="fa fa-edit po-list-icon"></i></a>
                                                            <div class="modal fade company-depriciation-rule-modal" id="fcompanyDepriciationRule_<?= $dep_id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" style="max-width: 50%;">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="exampleModalLabel">Edit Depreciation Key</h5>

                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>



                                                                        <div class="modal-body">
                                                                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                                                                <input type="hidden" name="edit_asset_dep" id="edit_asset_dep" value="edit_asset_dep">
                                                                                <input type="hidden" name="asset_id" value="<?= $dep_id ?>">
                                                                                <div class="row">
                                                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                        <div class="form-input my-2">
                                                                                            <label for=""> Depreciation Key</label>
                                                                                            <input type="text" name="dep_key" class="form-control" value="<?= $row['desp_key']  ?>" readonly>
                                                                                        </div>
                                                                                    </div>


                                                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                        <div class="form-input my-2">
                                                                                            <label for="">Asset Class</label>
                                                                                            <input type="text" name="asset_class" class="form-control" value="<?= $row['asset_class'] ?>" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                        <div class="form-input my-2">
                                                                                            <label for="">Parent</label>
                                                                                            <input type="text" name="parent" class="form-control" value="<?= $row['parent_code'] ?>" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                        <div class="form-input my-2">
                                                                                            <label for="">WDV</label>
                                                                                            <input type="text" name="wdv" class="form-control" value="<?= $row['wdv']  ?>">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                                                        <div class="form-input my-2">
                                                                                            <label for="">SLM</label>
                                                                                            <input type="text" name="slm" class="form-control" value="<?= $row['slm'] ?>">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <button type="submit" class="btn btn-primary" value="edit_data">Update</button>
                                                                            </form>
                                                                        </div>



                                                                    </div>
                                                                </div>
                                                            </div>



                                                        </td>
                                                    <?php }
                                                    ?>
                                                </tr>


                                            <?php  } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="8">
                                                    <!-- Start .pagination -->

                                                    <?php
                                                    if ($count1 > 0 && $count1 > $GLOBALS['show']) {
                                                    ?>
                                                        <div class="pagination align-right">
                                                            <?php pagination($count1, "frm_opts"); ?>
                                                        </div>

                                                        <!-- End .pagination -->

                                                    <?php  } ?>

                                                    <!-- End .pagination -->
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                <?php } else { ?>
                                    <table id="mytable" class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <td>
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                <?php
                                }
                                ?>
                            </div>
                            <form method="POST" id="addNewDepreciationForm">
                                <input type="hidden" name="rule_type" id="rule_type_management">
                                <input type="hidden" name="rule_subtype" id="rule_subtype_management">
                                <input type="hidden" name="schedule" id="schedule_management">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <?php
                                    if ($check_it_exists1['numRows'] == 0) {
                                    ?>
                                        <button type="submit" name="addNewDepreciationFormSubmitBtn" id="addNewDepreciationFormSubmitBtn" class="btn btn-primary items-search-btn float-right">Import</button>

                                    <?php }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card it" id="itDiv">

                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">

                                    <div class="col-lg-2 col-md-2 col-sm-12">



                                    </div>

                                    <div class="col-lg-10 col-md-10 col-sm-12">

                                        <div class="row table-header-item">

                                            <!-- <div class="col-lg-11 col-md-11 col-sm-11">

                                                <div class="section serach-input-section">



                                                    <input type="text" id="myInput" placeholder="" class="field form-control" />

                                                    <div class="icons-container">

                                                        <div class="icon-search">

                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>

                                                        </div>

                                                        <div class="icon-close">

                                                            <i class="fa fa-search po-list-icon" onclick="javascript:alert('Hello World!')" id="myBtn"></i>

                                                            <script>
                                                                var input = document.getElementById("myInput");

                                                                input.addEventListener("keypress", function(event) {

                                                                    if (event.key === "Enter") {

                                                                        event.preventDefault();

                                                                        document.getElementById("myBtn").click();

                                                                    }

                                                                });
                                                            </script>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div> -->

                                            <!-- <div class="col-lg-1 col-md-1 col-sm-1">

                                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>

                                            </div> -->

                                        </div>



                                    </div>

                                </div>
                                <div class="row p-0 m-0">
                                    <?php
                                    $cond = '';
                                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                        $cond .= " AND functionalities_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                    }

                                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                        $cond .= " AND (`functionalities_name` like '%" . $_REQUEST['keyword'] . "%' OR `functionalities_desc` like '%" . $_REQUEST['keyword'] . "%')";
                                    }

                                    // echo $sql_list = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND company_id= 0 " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                    $check_it_exists = queryGet("SELECT * FROM `erp_depreciation_table` WHERE  `rule_type`= 'IT' AND company_id=$company_id", true);
                                    if ($check_it_exists['numRows'] > 0) {
                                        $sql_list = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND `rule_type`= 'IT' AND company_id=$company_id  " . $sts . "  ORDER BY depreciation_id asc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                        $countShow = "SELECT count(*) FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND `rule_type`= 'IT' AND company_id=$company_id  " . $sts . "  ORDER BY depreciation_id asc";
                                    } else {
                                        $sql_list = "SELECT * FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND `rule_type`= 'IT' AND company_id=0   " . $sts . "  ORDER BY depreciation_id asc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                                        $countShow = "SELECT count(*) FROM `erp_depreciation_table` WHERE 1 " . $cond . "  AND `rule_type`= 'IT' AND company_id= 0 " . $sts . "  ORDER BY depreciation_id asc";
                                    }

                                    $qry_list = mysqli_query($dbCon, $sql_list);
                                    $num_list = mysqli_num_rows($qry_list);



                                    $countQry = mysqli_query($dbCon, $countShow);
                                    $rowCount = mysqli_fetch_array($countQry);
                                    // console($qry_list);
                                    // exit();
                                    $count = $rowCount[0];
                                    $cnt = $GLOBALS['start'] + 1;
                                    $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_DEPRECIATION_TABLE", $company_id);
                                    $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                    $settingsCheckbox = unserialize($settingsCh);
                                    if ($num_list > 0) {
                                    ?>
                                        <table id="mytable" class="table table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <th>Depreciation Key</th>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <th>Asset Class</th>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <th>Parent Key</th>
                                                    <?php  }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <th>Asset Life</th>
                                                    <?php  }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <th>WDV</th>
                                                    <?php  }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <th>SLM</th>
                                                    <?php } ?>


                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                while ($row = mysqli_fetch_assoc($qry_list)) {
                                                    // console($row);
                                                ?>
                                                    <tr>
                                                        <td><?= $cnt++ ?></td>
                                                        <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                            <td><?= $row['desp_key'] ?></td>
                                                        <?php }
                                                        if (in_array(2, $settingsCheckbox)) { ?>
                                                            <td><?= $row['asset_class'] ?></td>
                                                        <?php }
                                                        if (in_array(3, $settingsCheckbox)) { ?>
                                                            <td><?= $row['parent_code'] ?></td>
                                                        <?php }
                                                        if (in_array(4, $settingsCheckbox)) { ?>
                                                            <td><?= $row['asset_life'] ?></td>
                                                        <?php }
                                                        if (in_array(5, $settingsCheckbox)) { ?>
                                                            <td><?= $row['wdv'] ?></td>
                                                        <?php }
                                                        if (in_array(6, $settingsCheckbox)) { ?>
                                                            <td><?= $row['slm'] ?></td>
                                                        <?php } ?>


                                                    </tr>
                                                <?php  } ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="8">
                                                        <!-- Start .pagination -->

                                                        <?php
                                                        if ($count > 0 && $count > $GLOBALS['show']) {
                                                        ?>
                                                            <div class="pagination align-right">
                                                                <?php pagination($count, "frm_opts"); ?>
                                                            </div>

                                                            <!-- End .pagination -->

                                                        <?php  } ?>

                                                        <!-- End .pagination -->
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    <?php
                                    } else {
                                    ?>
                                        <table id="mytable" class="table table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <td>
                                                    </td>
                                                </tr>
                                            </thead>
                                        </table>
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>
                        </form>

                        <form method="POST" id="addNewDepreciationForm_it">
                            <input type="hidden" value="it_btn" name="it_btn">
                            <input type="hidden" name="rule_type" id="rule_type_it">
                            <input type="hidden" name="rule_subtype" id="rule_subtype_it">
                            <input type="hidden" name="schedule" id="schedule_it">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <?php
                                if ($check_it_exists['numRows'] == 0) {

                                ?>
                                    <button type="submit" name="it_btn" id="it_btn" class="btn btn-primary items-search-btn float-right">Import</button>
                                <?php }
                                ?>
                            </div>
                        </form>

                    </div>




                </div>

            </div>
            <!-- <div class="card management" id="managementDiv"> -->


        </div>




</div>
</div>
</div>
</div>
<!-- /.row -->


</div>
</section>
<!-- /.content -->
</div>
<!-- /.Content Wrapper. Contains page content -->
<!-- For Pegination------->
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
    <input type="hidden" name="pageNo" value="<?php if (isset($_REQUEST['pageNo'])) {
                                                    echo  $_REQUEST['pageNo'];
                                                } ?>">
</form>
<!-- End Pegination from------->

<?php

include("common/footer.php");
?>
<script>
    function srch_frm() {
        if ($('#form_date_s').val().trim() != '' && $('#to_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter To Date");
            $('#to_date_s').focus();
            return false;
        }
        if ($('#to_date_s').val().trim() != '' && $('#form_date_s').val().trim() === '') { //$("#phone_r_err").css('display','block');
            //$("#phone_r_err").html("Your Phone Number");
            alert("Enter From Date");
            $('#form_date_s').focus();
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


    $(document).on("click", "#btnSearchCollpase", function() {
        sec = document.getElementById("btnSearchCollpase").parentElement;
        coll = sec.getElementsByClassName("collapsible-content")[0];

        if (sec.style.width != '100%') {
            sec.style.width = '100%';
        } else {
            sec.style.width = 'auto';
        }

        if (coll.style.height != 'auto') {
            coll.style.height = 'auto';
        } else {
            coll.style.height = '0px';
        }

        $(this).children().toggleClass("fa-search fa-times");

    });

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

<!-- <script>
    $(document).ready(function() {
        $("#itDiv").hide();
        $("#companyDiv").hide();
        $("#managementDiv").hide();
        $('input[name="rule"]').change(function() {
            if ($('#rule').prop('checked')) {
                $("#itDiv").hide();
                $("#companyDiv").show();
                console.log("opening Company Div");
                console.log("closing IT Div");
                var val = $(this).val();
                //alert(val);
                $("#rule_type_company").val(val);
                // $("#company").css("display", "block");
                // $("#it").hide();

            } else {
                $("#companyDiv").hide();
                $("#itDiv").show();
                console.log("closing Company Div");
                console.log("opening IT Div");
                var val = $(this).val();
                //alert(val);
                $("#rule_type_it").val(val);
                // $("#company").hide();
                // $("#it").show();
            }
        });
    });
</script> -->
<script>
    $(document).ready(function() {
        // Initially hide all divs
        $("#companyDiv").hide();
        $("#managementDiv").hide();
        $("#itDiv").hide();

        // Handle the change event for the radio buttons
        $('input[name="rule"]').change(function() {
            // Get the selected value of the radio button
            var selectedValue = $(this).val();

            // Hide all divs first
            $("#companyDiv").hide();
            $("#managementDiv").hide();
            $("#itDiv").hide();

            // Show the corresponding div based on the selected radio button
            if (selectedValue === "company") {
                $("#companyDiv").show();
                console.log("Opening Company Div");
                $("#rule_type_company").val(selectedValue);
            } else if (selectedValue === "management") {
                $("#managementDiv").show();
                console.log("Opening Management Div");

                $("#rule_type_management").val(selectedValue);
            } else if (selectedValue === "it") {
                $("#itDiv").show();
                $("#rule_type_it").val(selectedValue);
                console.log("Opening IT Div");
            }
        });
    });
</script>




<script>
    $(document).ready(function() {
        $('input[name="crule"]').change(function() {

            if ($('#company_radio').prop('checked')) {

                var val = $(this).val();

                $("#rule_subtype_company").val(val);
                $("#rule_subtype_it").val(val);
                $("#rule_subtype_management").val(val);

            } else {
                var val = $(this).val();

                $("#rule_subtype_company").val(val);
                $("#rule_subtype_it").val(val);
                $("#rule_subtype_management").val(val);
                $("#schedule").val();
            }
        });
        $('input[name="srule"]').change(function() {

            if ($('#company_radio').prop('checked')) {

                var val = $(this).val();

                $("#schedule_company").val(val);
                $("#schedule_it").val(val);
                $("#schedule_management").val(val);

            } else {
                var val = $(this).val();


                $("#schedule_company").val(val);
                $("#schedule_it").val(val);
                $("#schedule_management").val(val);
            }
        });
    });

    function disPage(no) {
        // Get the current URL
        var currentUrl = window.location.href;

        // Get the value of the "rule" checkbox (if selected)
        var selectedRule = $('input[name="rule"]:checked').val(); // Get the selected rule

        // Remove any existing query parameters from the URL
        var newUrl = currentUrl.split('?')[0];

        // Append pageNo and rule parameters
        var params = [];
        params.push('pageNo=' + no); // Add pageNo parameter

        // If rule is selected, add the rule parameter
        if (selectedRule) {
            params.push('rule=' + encodeURIComponent(selectedRule)); // Add rule parameter
        }

        // Append parameters to the URL
        if (params.length > 0) {
            newUrl += '?' + params.join('&'); // Combine the parameters with '&'
        }

        // Redirect to the new URL
        window.location.href = newUrl;
    }
    // Function to get the value of a query parameter from the URL
    function getParameterByName(name) {
        var url = window.location.href; // Get the current URL
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]"); // Escape square brackets
        var regex = new RegExp("[?&]" + name + "=([^&#]*)"); // Create a regex to match the parameter
        var results = regex.exec(url); // Execute the regex on the URL
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " ")); // Return the parameter value or an empty string
    }

    // Get the 'rule' parameter from the URL
    var rule = getParameterByName("rule");

    // If 'rule' is found, check the corresponding checkbox
    $(document).ready(function() {
        if (rule) {
            // Hide all divs before showing the relevant one
            $("#companyDiv, #managementDiv, #itDiv").hide();

            $('input[name="rule"][value="' + rule + '"]').prop('checked', true);

            // Show the relevant div based on the rule
            if (rule === "company") {
                $("#companyDiv").show();
                console.log("Opening Company Div");
                $("#rule_type_company").val(rule);
            } else if (rule === "management") {
                $("#managementDiv").show();
                console.log("Opening Management Div");
                $("#rule_type_management").val(rule);
            } else if (rule === "it") {
                $("#itDiv").show();
                console.log("Opening IT Div");
                $("#rule_type_it").val(rule);
            }
        }
    });
</script>

<?php
$alreadyexist = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `company_id`='" . $company_id . "'", true);
if ($alreadyexist['numRows'] > 0) {
    $ruleis = $alreadyexist['data'][0]['rule_type'];


    echo "<script>
        $(document).ready(function() {
            var ruleis = '$ruleis'; // PHP variable passed to JS

            // Handle visibility and disable logic based on the rule
            switch (ruleis) {
                case 'Company':
                    $('#companyRule').prop('checked', true);
                    $('#companyDiv').show();
                    $('#managementRule').prop('disabled', true);
                    $('#itRule').prop('disabled', true);
                    $('#managementDiv').hide();
                    $('#itDiv').hide();
                    break;
                case 'management':
                    $('#managementRule').prop('checked', true);
                    $('#managementDiv').show();
                    $('#companyRule').prop('disabled', true);
                    $('#itRule').prop('disabled', true);
                    $('#companyDiv').hide();
                    $('#itDiv').hide();
                    break;
                case 'IT':
                    $('#itRule').prop('checked', true);
                    $('#itDiv').show();
                    $('#companyRule').prop('disabled', true);
                    $('#managementRule').prop('disabled', true);
                    $('#companyDiv').hide();
                    $('#managementDiv').hide();
                    break;
                default:
                    // If no match, make all rules available
                    $('#companyRule').prop('disabled', false);
                    $('#managementRule').prop('disabled', false);
                    $('#itRule').prop('disabled', false);
                    break;
            }
        });
    </script>";
}
?>
<script>
    $(document).ready(function() {
        var returnedMethod = "<?php echo $method; ?>"; // Get the returned method from PHP

        if (returnedMethod === "SLM") {
            // Check the SLM radio button and disable the WDV radio button
            $('#slm').prop('checked', true);
            $('#wdv').prop('disabled', true);
        } else if (returnedMethod === "WDV") {
            // Check the WDV radio button and disable the SLM radio button
            $('#wdv').prop('checked', true);
            $('#slm').prop('disabled', true);
        } else {
            // If no specific method is returned, enable both radio buttons
            $('#wdv').prop('disabled', false);
            $('#slm').prop('disabled', false);
        }
    });

    $(document).ready(function() {
        var returnedRule = "<?php echo $schudel; ?>"; // Get the returned rule from PHP

        if (returnedRule === "monthly") {
            // Check the Monthly radio button and disable the Yearly radio button
            $('#monthly').prop('checked', true);
            $('#yearly').prop('disabled', true);
        } else if (returnedRule === "yearly") {
            // Check the Yearly radio button and disable the Monthly radio button
            $('#yearly').prop('checked', true);
            $('#monthly').prop('disabled', true);
        } else {
            // If no specific rule is returned, enable both radio buttons
            $('#monthly').prop('disabled', false);
            $('#yearly').prop('disabled', false);
        }
    });
</script>