<?php

include("../app/v1/connection-company-admin.php");
// administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("common/pagination.php");
require_once("../app/v1/functions/company/func-sl.php");






//console($_SESSION);
//console($_SESSION['logedBranchAdminInfo']['fldAdminBranchId']);
//console(date("Y-m-d H:i:s"));
$slController = new SLController();

if (isset($_POST["changeStatus"])) {
    $newStatusObj = $slController->deleteSl($_POST['id']);
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


// if (isset($_POST["create"])) {
//   $addNewObj = createData($_POST + $_FILES);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

// if (isset($_POST["edit"])) { 
//   $editDataObj = updateData($_POST);

//   swalToast($editDataObj["status"], $editDataObj["message"]);
// }

if (isset($_POST["createSl"])) {

    // console($_POST);
    // exit();
    $addNewObj = $slController->createSl($_POST, $company_id, $created_by);

    swalToast($addNewObj["status"], $addNewObj["message"], BASE_URL . "company/manage-sl.php");
}
if (isset($_POST["editSl"])) {



    $addNewObj = $slController->editSl($_POST, $company_id, $created_by);
    swalToast($addNewObj["status"], $addNewObj["message"], BASE_URL . "company/manage-sl.php");
}


// if (isset($_POST["editgoodsdata"])) {
//   $addNewObj = $slController->editGoods($_POST);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["itemId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}


$sql = "SELECT * FROM `" . ERP_ACC_MAPPING . "` WHERE `company_id`='" . $company_id . "' and map_status='active' ORDER BY `map_id` DESC limit 1";
$query = queryGet($sql);
if ($query['status'] = "success") {
    $datas = $query['data'];
    $rData = array($datas['vendor_gl'], $datas['itemsRM_gl'], $datas['itemsSFG_gl'], $datas['itemsFG_gl'], $datas['customer_gl'], $datas['bank_gl'], $datas['cash_gl']);
}


$glexcept = implode(', ', $rData);
$gl = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE  glStType='account' AND company_id=$company_id AND id NOT IN ($glexcept) AND `status`!='deleted' AND `lock_status` = 0 ORDER BY gl_code", true);
// console($gl);

// console($rData);
?>

<style>
    #slCreateForm .select2-container,
    .asset-ledger .select2-container {
        width: 100% !important;
    }
</style>

<link rel="stylesheet" href="../../public/assets/listing.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">
                    <div class="p-0 pt-1 my-2">
                        <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                            <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                                <h3 class="card-title">Manage Sub Ledger</h3>
                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary" data-toggle="modal" data-target="#slCreateForm"><i class="fa fa-plus"></i> Add New</a>
                            </li>
                        </ul>
                    </div>


                    <div id="slCreateForm" class="modal kam-create-modal">

                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    Create SL
                                </div>
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="slSubmitForm" name="slSubmitForm">
                                    <div class="modal-body">

                                        <input type="hidden" name="createSl" id="createSl" value="">

                                        <div class="row">


                                            <div class="col-lg-6 col-md-6 col-sm-6" id="asset_gl">
                                                <div class="form-input">
                                                    <label>GL Code </label>



                                                    <select id="glCodeAsset" name="gl" class="form-control">
                                                        <option value="">SELECT GL Code</option>
                                                        <?php
                                                        foreach ($gl['data'] as $gl) {
                                                            $gl_id = $gl['id'];

                                                            $check_sl_dr = queryGet("SELECT * FROM `erp_acc_debit` WHERE `glId` = '$gl_id' AND `company_id` = $company_id");
                                                            $check_sl_cr = queryGet("SELECT * FROM `erp_acc_credit` WHERE `glId` = '$gl_id' AND `company_id` = $company_id");

                                                            if ($check_sl_dr['numRows'] == 0 && $check_sl_cr['numRows'] == 0) {




                                                        ?>
                                                            <option value="<?= $gl['id'] ?>"><?= $gl["gl_code"] . "|" . $gl["gl_label"] ?></option>
                                                        <?php
                                                            }
                                                        }
 
                                                        ?>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6" id="">
                                                <div class="form-input">
                                                    <label>Sub Ledger Name </label>
                                                    <input type="text" name="name" class="form-control" id="exampleInputBorderWidth2">


                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12" id="">
                                                <div class="form-input">
                                                    <label>Sub Ledger Description </label>
                                                    <textarea class="form-control" name="desc"></textarea>


                                                </div>
                                            </div>


                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary add_data" value="add_post">Submit</button>
                                    </div>

                                </form>
                            </div>


                        </div>

                    </div>


                    <div class="card card-tabs" style="border-radius: 20px;">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">
                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-12">
                                        <div class="section serach-input-section">

                                            <div class="collapsible-content">
                                                <div class="filter-col">

                                                    <div class="row">
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <div class="input-group-manage-vendor">
                                                                <select name="vendor_status_s" id="vendor_status_s" class="form-control">
                                                                    <option value="">--- Status --</option>
                                                                    <option value="active" <?php if (isset($_REQUEST['vendor_status_s']) && 'active' == $_REQUEST['vendor_status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Active</option>
                                                                    <option value="inactive" <?php if (isset($_REQUEST['vendor_status_s']) && 'inactive' == $_REQUEST['vendor_status_s']) {
                                                                                                    echo 'selected';
                                                                                                } ?>>Inactive</option>
                                                                    <option value="draft" <?php if (isset($_REQUEST['vendor_status_s']) && 'draft' == $_REQUEST['vendor_status_s']) {
                                                                                                echo 'selected';
                                                                                            } ?>>Draft</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                                                echo $_REQUEST['form_date_s'];
                                                                                                                                                                                            } ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <div class="input-group-manage-vendor"> <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                                                echo $_REQUEST['form_date_s'];
                                                                                                                                                                                            } ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <div class="input-group-manage-vendor">
                                                                <input type="text" name="keyword" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php if (isset($_REQUEST['keyword'])) {
                                                                                                                                                                                        echo $_REQUEST['keyword'];
                                                                                                                                                                                    } ?>">
                                                            </div>
                                                        </div>


                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <button type="submit" class="btn btn-primary">Search</button>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger">Reset</a>
                                                        </div>
                                                    </div>






                                                </div>
                                            </div>
                                            <button type="button" class="collapsible btn-search-collpase" id="btnSearchCollpase">
                                                <i class="fa fa-search po-list-icon"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>

                        </form>
                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                <?php
                                $cond = '';

                                $sts = " AND `status` !='deleted'";
                                if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {
                                    $sts = ' AND status="' . $_REQUEST['status_s'] . '"';
                                }

                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }

                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                    $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                                }

                                $sql_list = "SELECT * FROM `erp_extra_sub_ledger` WHERE 1 " . $cond . "  AND `company_id`=$company_id  ORDER BY sl_Id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                $qry_list = mysqli_query($dbCon, $sql_list);
                                $num_list = mysqli_num_rows($qry_list);
                                // $gl =  loadGLCode(1,2,3,4);
                                //console($qry_list);



                                $countShow = "SELECT count(*) FROM `erp_extra_sub_ledger` WHERE 1 " . $cond . " AND `company_id`=$company_id ";
                                $countQry = mysqli_query($dbCon, $countShow);
                                $rowCount = mysqli_fetch_array($countQry);
                                $count = $rowCount[0];
                                $cnt = $GLOBALS['start'] + 1;
                                $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "erp_extra_sub_ledger", $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox = unserialize($settingsCh);
                                if ($num_list > 0) { ?>
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr class="alert-light">
                                                <th>#</th>
                                                <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                    <th>Sub Ledger Name</th>
                                                <?php }
                                                if (in_array(2, $settingsCheckbox)) { ?>
                                                    <th>Sub Ledger Code</th>
                                                <?php }
                                                if (in_array(3, $settingsCheckbox)) { ?>
                                                    <th>Parent GL</th>
                                                <?php }
                                                if (in_array(4, $settingsCheckbox)) { ?>
                                                    <th>Description</th>
                                                <?php  }

                                                if (in_array(5, $settingsCheckbox)) { ?>
                                                    <th>Created By</th>
                                                <?php  }
                                                ?>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $customerModalHtml = "";
                                            while ($row = mysqli_fetch_assoc($qry_list)) {
                                                //console($row);
                                                $sl_name = $row['sl_name'];
                                                $sl_id = $row['sl_id'];
                                                $gl_id = $row['parentGlId'];
                                                // $kam_description = $row['description'];
                                                // $kam_parent = $row['parentId'];
                                                // $kam_created = $row['created_by'];

                                                $gl = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE company_id=$company_id AND `id` = $gl_id");
                                                // console($gl);
                                            ?>
                                                <tr>
                                                    <td><?= $cnt++ ?></td>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <td><?= $row['sl_name'] ?></td>
                                                    <?php }
                                                      if (in_array(2, $settingsCheckbox)) { ?>
                                                        <td><?= $row['sl_code'] ?></td>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <td><?= $gl['data']['gl_code'] . '(' . $gl['data']['gl_label'] . ')' ?></td>
                                                    <?php }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <td>
                                                            <?= $row['sl_description'] ?>
                                                        </td>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <td><?= getCreatedByUser($row['created_by']) ?></td>
                                                    <?php }
                                                    ?>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <a style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['sl_id'] ?>"><i class="fa fa-eye po-list-icon"></i></a>
                                                            <?php

                                                            // $check_sl = "SELECT * FROM `erp_acc_journal` WHERE `company_id` = $company_id AND ``";

                                                            ?>
                                                            <a style="cursor: pointer;" name="slEditBtn" data-toggle="modal" data-target="#slEditForm_<?= $row['sl_id'] ?>">
                                                                <i title="Edit" style="font-size: 1.2em" class="fa fa-edit po-list-icon"></i>
                                                            </a>
                                                            <?php
                                                            $sl_code = $row['sl_code'];

                                                            $check_sl_dr = queryGet("SELECT * FROM `erp_acc_debit` WHERE `subGlCode` = '$sl_code' AND `company_id` = $company_id");
                                                            $check_sl_cr = queryGet("SELECT * FROM `erp_acc_credit` WHERE `subGlCode` = '$sl_code' AND `company_id` = $company_id");

                                                            if ($check_sl_dr['numRows'] == 0 && $check_sl_cr['numRows'] == 0) {



                                                            ?>
                                                                <form action="" method="POST" class="btn btn-sm">
                                                                    <input type="hidden" name="id" value="<?php echo $row['sl_id'] ?>">
                                                                    <input type="hidden" name="changeStatus" value="delete">
                                                                    <button title="Delete Cost Center" type="submit" onclick="return confirm('Are you sure to delete?')" class="btn btn-sm" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button>
                                                                </form>
                                                            <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!-- right modal start here  -->
                                                <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $row['sl_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                                                        <!--Content-->
                                                        <div class="modal-content">
                                                            <!--Header-->
                                                            <div class="modal-header">
                                                                <p class="heading lead"><?= $sl_name ?></p>
                                                                <div class="display-flex-space-between mt-4 mb-3">
                                                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $sl_id) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $sl_id) ?>" role="tab" aria-controls="home<?= str_replace('/', '-', $sl_id) ?>" aria-selected="true">Info</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button Start------------------------- -->
                                                                        <li class="nav-item">
                                                                            <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $sl_id) ?>" data-toggle="tab" data-ccode="<?= $sl_id ?>" href="#history<?= str_replace('/', '-', $sl_id) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $sl_id) ?>" aria-selected="false">Trail</a>
                                                                        </li>
                                                                        <!-- -------------------Audit History Button End------------------------- -->
                                                                    </ul>
                                                                    <div class="action-btns display-flex-gap" id="action-navbar">



                                                                        <!-- <a name="customerEditBtn" data-toggle="modal" data-target="#slEditForm_<?= $row['sl_id'] ?>">
                                                                                <i title="Edit" style="font-size: 1.2em" class="fa fa-edit po-list-icon"></i>
                                                                            </a> -->
                                                                        <i title="Delete" style="font-size: 1.2em" class="fa fa-trash po-list-icon"></i>
                                                                        <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on po-list-icon"></i>

                                                                    </div>
                                                                </div>
                                                            </div>





                                                            <!--Body-->
                                                            <div class="modal-body">

                                                                <div class="tab-content" id="myTabContent">
                                                                    <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $sl_id) ?>" role="tabpanel" aria-labelledby="home-tab">

                                                                        <div class="row px-3 p-0 m-0" style="place-items: self-start;">



                                                                            <div class="col-md-6">
                                                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                                                    <div class="col-md-6">
                                                                                        <span class="font-weight-bold text-secondary">SL Name: </span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <span><?= $row['sl_name'] ?></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                                                    <div class="col-md-6">
                                                                                        <span class="font-weight-bold text-secondary">SL Description : </span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <span><?= $row['sl_description'] ?></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-6">
                                                                                <div class="row m-2 py-2 shadow-sm bg-light">
                                                                                    <div class="col-md-6">
                                                                                        <span class="font-weight-bold text-secondary">Created By : </span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <span><?= getCreatedByUser($row['created_by']) ?></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>




                                                                        </div>

                                                                    </div>
                                                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                                    <div class="tab-pane fade" id="history<?= str_replace('/', '-', $sl_id) ?>" role="tabpanel" aria-labelledby="history-tab">

                                                                        <div class="audit-head-section mb-3 mt-3 ">
                                                                            <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['created_at']) ?></p>
                                                                            <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updated_at']) ?></p>
                                                                        </div>
                                                                        <hr>
                                                                        <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $sl_id) ?>">
                                                                            <ol class="timeline">
                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                    <div class="new-comment font-bold">
                                                                                        <p>Loading...
                                                                                        <ul class="ml-3 pl-0">
                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                        </ul>
                                                                                        </p>
                                                                                    </div>
                                                                                </li>
                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                                <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal" data-target="#innerModal">
                                                                                    <span class="timeline-item-icon | filled-icon"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                                                                    <div class="new-comment font-bold">
                                                                                        <p>Loading...
                                                                                        <ul class="ml-3 pl-0">
                                                                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                                                                        </ul>
                                                                                        </p>
                                                                                    </div>
                                                                                </li>
                                                                                <p class="mt-0 mb-5 ml-5">Loading...</p>
                                                                            </ol>
                                                                        </div>
                                                                    </div>
                                                                    <!-- -------------------Audit History Tab Body End------------------------- -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--/.Content-->
                                                    </div>
                                                </div>
                                                <!-- kam edit modal -->


                                                <div id="slEditForm_<?= $row['sl_id'] ?>" class="modal slEditForm">


                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                Edit SL
                                                            </div>
                                                            <?php

                                                            $sl = queryGet("SELECT * FROM `erp_extra_sub_ledger` WHERE `sl_id` = '" . $row['sl_id'] . "'");

                                                            $sl_data = $sl['data'];
                                                            ?>
                                                            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="slSubmitForm" name="slSubmitForm">
                                                                <div class="modal-body">

                                                                    <input type="hidden" name="editSl" id="editSl" value="editSl">
                                                                    <input type="hidden" name="slId" id="slId" value="<?= $row['sl_id'] ?>">

                                                                    <div class="row">


                                                                        <div class="col-lg-6 col-md-6 col-sm-6" id="asset_gl">
                                                                            <div class="form-input asset-ledger">
                                                                                <label>GL Code </label>
                                                                                <select id="glCodeAsset_edit" name="gl" class="form-control w-100" disabled>
                                                                                    <option value="">SELECT GL Code</option>
                                                                                    <option value="<?= $gl['data']['id'] ?>" selected> <?= $gl['data']['gl_code'] . '||' . $gl['data']['gl_label'] ?> </option>

                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-lg-6 col-md-6 col-sm-6" id="">
                                                                            <div class="form-input">
                                                                                <label>Sub Ledger Name </label>
                                                                                <input type="text" name="name" class="form-control" id="exampleInputBorderWidth2" value="<?= $sl_data['sl_name'] ?>">


                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 col-md-12 col-sm-12 mt-3" id="">
                                                                            <div class="form-input">
                                                                                <label>Sub Ledger Description </label>
                                                                                <textarea class="form-control" name="desc"><?= $sl_data['sl_description'] ?></textarea>


                                                                            </div>
                                                                        </div>


                                                                    </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button class="btn btn-primary edit_data" value="edit_post">Submit</button>
                                                                </div>

                                                            </form>

                                                        </div>
                                                    </div>

                                                </div>


                                                <!-- kam edit modal end -->


                                                <!-- right modal end here  -->
                                            <?php } ?>

                                        </tbody>

                                    </table>





                                    <?php
                                    if ($count > 0 && $count > $GLOBALS['show']) {
                                    ?>
                                        <div class="pagination align-right">
                                            <?php pagination($count, "frm_opts"); ?>
                                        </div>

                                        <!-- End .pagination -->

                                    <?php  } ?>

                                <?php } else { ?>
                                    <table class="table defaultDataTable table-hover text-nowrap">
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
                        <?= $customerModalHtml ?>
                        <!---------------------------------Table settings Model Start--------------------------------->
                        <div class="modal" id="myModal2">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Table Column Settings</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                        <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                                        <input type="hidden" name="pageTableName" value="erp_extra_sub_ledger" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            Sub Ledger Name</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox12 value="2" />
                                                            Sub Ledger Code</td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            Parent GL</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                            Description</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                            Created By</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                            Action</td>
                                                    </tr>

                                                </table>
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
require_once("common/footer.php");
?>
<script>
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
        // $('#goodTypeDropDown')
        //   .select2()
        //   .on('select2:open', () => {
        //     //$(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodTypesFormModal">Add New</a></div>`);
        //   });

        $("#goodTypeDropDown").change(function() {
            let dataAttrVal = $("#goodTypeDropDown").find(':selected').data('goodtype');
            if (dataAttrVal == "RM") {
                $("#bomCheckBoxDiv").html("");
            } else if (dataAttrVal == "SFG") {
                $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired"style="width: auto; margin-bottom: 0;" checked>Required BOM`);

            } else {
                $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired"style="width: auto; margin-bottom: 0;">Required BOM`);
            }
        });

        //**************************************************************
        $('#glCodeAsset_edit').select2({
            dropdownParent: $(".slEditForm")
        });

        $('#glCodeAsset').select2({
            dropdownParent: $("#slCreateForm")
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