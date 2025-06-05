<?php
include("../app/v1/connection-branch-admin.php");
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");

administratorAuth();
require_once("common/pagination.php");

if (isset($_POST["variantSave"])) {
    // console($_POST);
    $admin_id = $_POST['admin_id'];
    $var_id = $_POST['variant'];
    $last_date = $_POST['last_date'];
    $time = "23:59:59";
    $last_date_time = $last_date . " " . $time;

    $update = "UPDATE `tbl_branch_admin_details` SET `flAdminVariant`=$var_id, `flAdminVariantLastDate`='$last_date_time' WHERE `fldAdminKey`=$admin_id";
    $updateObj = queryUpdate($update);
   // console($updateObj);
    if($updateObj['status'] =='success') {
    $insert_sql = queryInsert("INSERT `erp_admin_variant_log` SET `admin_id`= $admin_id , `variant_id` = $var_id");
    // exit;
        swalToast($updateObj["status"], $updateObj["message"]);
    }else{        
        swalToast($updateObj["status"], $updateObj["message"]);
    }
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">




<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- row -->
            <div class="row p-0 m-0">
                <div class="col-12 mt-2 p-0">



                    <?php
                    $keywd = '';
                    if (isset($_REQUEST['keyword']) && !empty($_REQUEST['keyword'])) {
                        $keywd = $_REQUEST['keyword'];
                    } else if (isset($_REQUEST['keyword2']) && !empty($_REQUEST['keyword2'])) {
                        $keywd = $_REQUEST['keyword2'];
                    } ?>

                    <div class="card card-tabs" style="border-radius: 20px;">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return srch_frm();">
                            <div class="card-body">
                                <div class="row filter-serach-row">
                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-lg-10 col-md-10 col-sm-12">
                                        <div class="row table-header-item">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="section serach-input-section">
                                                    <input type="text" name="keyword" id="myInput" placeholder="" class="field form-control" value="<?php echo $keywd; ?>">
                                                    <div class="icons-container">
                                                        <div class="icon-search">
                                                            <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>
                                                        </div>
                                                        <div class="icon-close">
                                                            <i class="fa fa-search po-list-icon" id="myBtn"></i>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Order</h5>

                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <input type="text" name="keyword2" class="fld form-control m-input" id="keyword" placeholder="Enter Keyword" value="<?php /*if (isset($_REQUEST['keyword2'])) {
                                                                                                                                                      echo $_REQUEST['keyword2'];
                                                                                                                                                    } */ ?>">
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                                            <select name="status_s" id="status_s" class="fld form-control" style="appearance: auto;">
                                                                <option value=""> Status </option>
                                                                <option value="6" <?php if (isset($_REQUEST['status_s']) && '6' == $_REQUEST['status_s']) {
                                                                                        echo 'selected';
                                                                                    } ?>>Active
                                                                </option>
                                                                <option value="7" <?php if (isset($_REQUEST['status_s']) && '7' == $_REQUEST['status_s']) {
                                                                                        echo 'selected';
                                                                                    } ?>>Inactive
                                                                </option>
                                                                <option value="8" <?php if (isset($_REQUEST['status_s']) && '8' == $_REQUEST['status_s']) {
                                                                                        echo 'selected';
                                                                                    } ?>>Draft</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                            <input class="fld form-control" type="date" name="form_date_s" id="form_date_s" value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                        echo $_REQUEST['form_date_s'];
                                                                                                                                                    } ?>" />
                                                        </div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                            <input class="fld form-control" type="date" name="to_date_s" id="to_date_s" value="<?php if (isset($_REQUEST['to_date_s'])) {
                                                                                                                                                    echo $_REQUEST['to_date_s'];
                                                                                                                                                } ?>" />
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <!-- <a class="btn btn-primary" href="<?php echo $_SERVER['PHP_SELF']; ?>"><i class="fa fa-sync "></i>Reset</a>-->
                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i>
                                                        Search</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                        </form>

                        <script>
                            var input = document.getElementById("myInput");
                            input.addEventListener("keypress", function(event) {
                                if (event.key === "Enter") {
                                    event.preventDefault();
                                    document.getElementById("myBtn").click();
                                }
                            });
                            var form = document.getElementById("search");
                            document.getElementById("myBtn").addEventListener("click", function() {
                                form.submit();
                            });
                        </script>
                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                <?php
                                $cond = '';

                                $sts = " AND `fldAdminStatus` !='deleted'";
                                if (isset($_REQUEST['customer_status_s']) && $_REQUEST['customer_status_s'] != '') {
                                    $sts = ' AND fldAdminStatus="' . $_REQUEST['customer_status_s'] . '"';
                                }

                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND branch_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }

                                // if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                //   $cond .= " AND (`customer_code` like '%" . $_REQUEST['keyword'] . "%' OR `trade_name` like '%" . $_REQUEST['keyword'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                                // }


                                if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                                    $cond .= " AND (`fldAdminEmail` like '%" . $_REQUEST['keyword2'] . "%' OR `fldAdminName` like '%" . $_REQUEST['keyword2'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword2'] . "%')";
                                } else {
                                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                        $cond .= " AND (`fldAdminEmail` like '%" . $_REQUEST['keyword'] . "%' OR `fldAdminName` like '%" . $_REQUEST['keyword'] . "%' OR `customer_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                                    }
                                }


                                $sql_list = "SELECT * FROM `tbl_branch_admin_details` WHERE 1 " . $cond . "  AND fldAdminBranchId=$branch_id " . $sts . "  ORDER BY fldAdminKey desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                $qry_list = mysqli_query($dbCon, $sql_list);
                                $num_list = mysqli_num_rows($qry_list);


                                $countShow = "SELECT count(*) FROM `tbl_branch_admin_details` WHERE 1 " . $cond . " AND fldAdminBranchId=$branch_id " . $sts;
                                $countQry = mysqli_query($dbCon, $countShow);
                                $rowCount = mysqli_fetch_array($countQry);
                                $count = $rowCount[0];
                                $cnt = $GLOBALS['start'] + 1;
                                $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_BRANCH_ADMIN_VARIANT", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox = unserialize($settingsCh);
                                if ($num_list > 0) {
                                ?>
                                    <table class="table defaultDataTable table-hover text-nowrap">
                                        <thead>
                                            <tr class="alert-light">
                                                <th>#</th>
                                                <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                    <th>Name</th>
                                                <?php }
                                                if (in_array(2, $settingsCheckbox)) { ?>
                                                    <th>Email </th>
                                                <?php }

                                                if (in_array(3, $settingsCheckbox)) { ?>
                                                    <th>Admin Branch</th>
                                                <?php }
                                                if (in_array(4, $settingsCheckbox)) { ?>
                                                    <th>Admin Location</th>
                                                <?php  }
                                                if (in_array(5, $settingsCheckbox)) { ?>
                                                    <th> Year Variant</th>
                                                <?php }
                                                if (in_array(6, $settingsCheckbox)) { ?>
                                                    <th> Month Variant</th>

                                                <?php }
                                                if (in_array(7, $settingsCheckbox)) { ?>
                                                    <th>Variant Valid Till</th>

                                                <?php } ?>
                                                <th>Varient</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $customerModalHtml = "";
                                            while ($row = mysqli_fetch_assoc($qry_list)) {
                                                $randno = rand(999, 9999);
                                                $admin_var = $row['flAdminVariant'];
                                                if ($admin_var != 0) {
                                                    $var_sql = queryGet("SELECT * FROM " . ERP_MONTH_VARIANT . " WHERE `month_variant_id`=$admin_var");
                                                    $var_data = $var_sql['data'];
                                                    //  console($var_sql);
                                                    $month_var = $var_data['month_variant_name'];
                                                    $year_var_id = $var_data['year_id'];
                                                    $year_sql = queryGet("SELECT * FROM " . ERP_YEAR_VARIANT . " WHERE `year_variant_id`=$year_var_id ");
                                                    $year_data = $year_sql['data'];
                                                    $year_var = $year_data['year_variant_name'];
                                                } else {
                                                    $year_var_id = "";
                                                    $month_var = "";
                                                    $year_var = "";
                                                }

                                                //console($row);
                                            ?>
                                                <tr>
                                                    <td><?= $cnt++ ?></td>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <td><?= $row['fldAdminName'] ?></td>
                                                    <?php }

                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <td><?= $row['fldAdminEmail'] ?></td>
                                                    <?php }
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <td><?= $row['fldAdminBranchId'] ?></td>
                                                    <?php }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <td><?= $row['fldAdminBranchLocationId'] ?></td>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <td><?= $year_var ?></td>
                                                    <?php }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <td><?= $month_var ?></td>
                                                    <?php }
                                                    if (in_array(7, $settingsCheckbox)) { ?>
                                                        <td><?= $row['flAdminVariantLastDate'] ?></td>
                                                    <?php }
                                                    ?>

                                                    <td> <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#goodsHSNModal_<?= $row['fldAdminKey'] ?>">Change Variant</button> </td>
                                                    <td> <a type="button" data-toggle="modal" data-target="#varientView">
                                                            <i class="fa fa-eye po-list-icon"></i>
                                                        </a> </td>
                                                </tr>
                                                <!-- right modal start here  -->


                                                <div class="modal fade hsn-dropdown-modal" id="goodsHSNModal_<?= $row['fldAdminKey'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                                    <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                                        <div class="modal-content card">
                                                            <div class="modal-header card-header p-3 text-white">
                                                                Change Variant Form
                                                            </div>
                                                            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="" name="">
                                                                <div class="modal-body card-body p-3">
                                                                    <div class="hsn-header">
                                                                        <input type="hidden" name="variantSave" id="variantSave" value="">
                                                                        <input type="hidden" name="admin_id" id="admin_id" value="<?= $row['fldAdminKey']  ?>">
                                                                        <input type="hidden" name="admin_month_var" class="form-control admin_month_var_<?= $randno ?>" value="<?= $admin_var ?>">
                                                                        <label>Allow <?= $row['fldAdminName'] ?> for the posting period of financial year</label>
                                                                        <select id="yeardropdown" name="variant" data-val="<?= $randno ?>" class="form-control" required>

                                                                            <option value="">Select Year Variant</option>
                                                                            <?php


                                                                            $variants =  queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id", true);
                                                                            foreach ($variants['data'] as $data) {


                                                                            ?>
                                                                                <option value="<?= $data['year_variant_id'] ?>" <?php if ($data['year_variant_id'] == $year_var_id) {
                                                                                                                                    echo "selected";
                                                                                                                                } ?>><?= $data['year_variant_name'] ?></option>

                                                                            <?php
                                                                            }
                                                                            ?>

                                                                        </select>
                                                                        <label for="">on month</label>
                                                                        <select id="monthvariant" name="variant" class="form-control monthvariant_<?= $randno ?>" required>

                                                                            <option value="">Select Month Variant</option>
                                                                            <?php


                                                                            $variants =  queryGet("SELECT * FROM `erp_month_variant` WHERE `year_id`= $year_var_id", true);
                                                                            foreach ($variants['data'] as $data) {


                                                                            ?>
                                                                                <option value="<?= $data['month_variant_id'] ?>" <?= ($data['month_variant_id'] == $admin_var) ? "selected" : ""  ?>><?= $data['month_variant_name'] ?></option>

                                                                            <?php
                                                                            }
                                                                            ?>

                                                                        </select>
                                                                        <label for="">till</label>

                                                                        <input type="date" name="last_date" class="form-control">


                                                                    </div>

                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-primary" id="variant_save">Save changes</button>
                                                                </div>
                                                            </form>

                                                        </div>

                                                    </div>
                                                </div>
                            </div>

                            <!-- right modal end here  -->

                            <!-- right modal start here  -->


                            <div class="modal fade right varient-view-modal customer-modal" id="varientView" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                                <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <div class="display-flex-space-between mt-4 mb-3">
                                                <ul class="nav nav-tabs" id="myTab" role="tablist">

                                                    <!-- -------------------Audit History Button Start------------------------- -->
                                                    <li class="nav-item">
                                                        <a class="nav-link auditTrail" id="history-tab" data-toggle="tab" data-ccode="" href="#history" role="tab" aria-controls="history<?= $onePrList['rfqId']  ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                                    </li>
                                                    <!-- -------------------Audit History Button End------------------------- -->
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="tab-content" id="myTabContent">
                                                <!-- -------------------Audit History Tab Body Start------------------------- -->
                                                <div class="tab-pane fade show active" id="history" role="tabpanel" aria-labelledby="history-tab">

                                                    <div class="audit-head-section mb-3 mt-3 ">
                                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($onePrList['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePrList['created_at']) ?></p>
                                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($onePrList['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($onePrList['updated_at']) ?></p>
                                                    </div>
                                                    <hr>
                                                    <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['']) ?>">

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

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" id="variant_save">Save changes</button>
                                        </div>

                                        <div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- right modal end here  -->

                        <?php } ?>
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
                        </tbody>

                        </table>
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
                                    <input type="hidden" name="pageTableName" value="ERP_BRANCH_ADMIN_VARIANT" />
                                    <div class="modal-body">
                                        <div id="dropdownframe"></div>
                                        <div id="main2">
                                            <table>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                        Name</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                        Email</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                        Admin Branch</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                                        Admin Location</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                                        Admin Year Variant</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                                        Admin Month Variant</td>
                                                </tr>
                                                <tr>
                                                    <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                                        Variant Valid Till</td>
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
    //********************************************************************************************************** */

    var BASE_URL = `<?= BASE_URL ?>`;
    var BRANCH_URL = `<?= BRANCH_URL ?>`;
    var LOCATION_URL = `<?= LOCATION_URL ?>`;
    $(document).ready(function() {
        $(document).on("change", "#isGstRegisteredCheckBoxBtn", function() {
            let isChecked = $(this).is(':checked');
            if (isChecked) {
                $("#customerGstNoInput").attr("readonly", "readonly");
                $("#customerPanNo").removeAttr("readonly");

                $.ajax({
                    type: "GET",
                    url: `${LOCATION_URL}ajaxs/ajax-customer-with-out-verify-gstin.php`,
                    beforeSend: function() {
                        $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Loading...');
                        $(".checkAndVerifyGstinBtn").toggleClass("disabled");
                    },
                    success: function(response) {
                        $(".checkAndVerifyGstinBtn").toggleClass("disabled");
                        // $('.checkAndVerifyGstinBtn').html("Re-Verify");
                        responseObj = (response);
                        //  $('.checkAndVerifyGstinBtn').html("Re-Verify");
                        responseObj = (response);
                        //responseObj = JSON.parse(responseObj);
                        $("#VerifyGstinBtnDiv").hide();
                        $("#multistepform").show();
                        $("#multistepform").html(responseObj);
                        // console.log(responseObj);
                    }
                });

            } else {
                $("#customerCreateMainForm").html("");
                $("#customerGstNoInput").removeAttr("readonly");
                $("#customerPanNo").attr("readonly", "readonly");
            }
            $(".checkAndVerifyGstinBtn").toggleClass("disabled");
        });

        $(".checkAndVerifyGstinBtn").click(function() {
            let customerGstNo = $("#customerGstNoInput").val();
            if (customerGstNo != "") {
                $.ajax({
                    type: "GET",
                    url: `${LOCATION_URL}ajaxs/ajax-customer-verify-gstin.php?gstin=${customerGstNo}`,
                    beforeSend: function() {
                        $('.checkAndVerifyGstinBtn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                        $(".checkAndVerifyGstinBtn").toggleClass("disabled");
                    },
                    success: function(response) {
                        $(".checkAndVerifyGstinBtn").toggleClass("disabled");
                        //  $('.checkAndVerifyGstinBtn').html("Re-Verify");
                        responseObj = (response);
                        //responseObj = JSON.parse(responseObj);
                        $("#VerifyGstinBtnDiv").hide();
                        $("#multistepform").show();
                        $("#multistepform").html(responseObj);
                        //console.log(responseObj);
                        load_js();
                    }
                });
            } else {
                let Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                Toast.fire({
                    icon: `warning`,
                    title: `&nbsp;Please provide GSTIN No!`
                });
            }
        });

    });

    $(document).ready(function() {
        $(document).on('change', '.customer_bank_cancelled_cheque', function() {
            var file_data = $('.customer_bank_cancelled_cheque').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            // alert(form_data);
            $.ajax({
                url: 'ajaxs/ajax_cancelled_cheque_upload.php', // <-- point to server-side PHP script 
                dataType: 'text', // <-- what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                beforeSend: function() {
                    $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    $(".Ckecked_loder").toggleClass("disabled");
                },
                success: function(responseData) {
                    $('.Ckecked_loder').html('<i class="fa fa-upload"></i>');
                    $(".Ckecked_loder").toggleClass("enabled");
                    responseObj = JSON.parse(responseData);
                    console.log(responseObj);
                    $("#customer_bank_ifsc").val(responseObj["payload"]["cheque_details"]["ifsc"]["value"]);
                    $("#account_number").val(responseObj["payload"]["cheque_details"]["acc no"]["value"]);
                    $("#account_holder").val(responseObj["payload"]["cheque_details"]["acc holder"]["value"]);

                    $("#customer_bank_address").val(responseObj["payload"]["bank_details"]["ADDRESS"]);
                    $("#customer_bank_name").val(responseObj["payload"]["bank_details"]["BANK"]);
                    $("#customer_bank_branch").val(responseObj["payload"]["bank_details"]["BRANCH"]);
                }
            });
        });


        $(document).on('click', '.visiting_card_btn', function() {
            var file_data = $('#visitingFileInput').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            // alert(form_data);
            $.ajax({
                url: 'ajaxs/ajax_visiting_card.php', // <-- point to server-side PHP script 
                dataType: 'text', // <-- what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                beforeSend: function() {
                    $('.visiting_card_btn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    $(".visiting_card_btn").toggleClass("disabled");
                },
                success: function(responseData) {
                    $('.visiting_card_btn').html('Submit');
                    $(".visiting_card_btn").toggleClass("enabled");
                    responseObj = JSON.parse(responseData);
                    console.log(responseObj);
                    $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
                    $("#vendor_authorised_person_designation").val('');

                    $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
                    $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);

                    $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
                    $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

                }
            });
        });

        $(document).on('change', '.visiting_card', function() {
            var file_data = $('.visiting_card').prop('files')[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            // alert(form_data);
            $.ajax({
                url: 'ajaxs/ajax_visiting_card.php', // <-- point to server-side PHP script 
                dataType: 'text', // <-- what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,
                type: 'post',
                beforeSend: function() {
                    $('.visiting_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
                    $(".visiting_loder").toggleClass("disabled");
                },
                success: function(responseData) {
                    $('.visiting_loder').html('<i class="fa fa-upload"></i>');
                    $(".visiting_loder").toggleClass("enabled");
                    responseObj = JSON.parse(responseData);
                    console.log(responseObj);
                    $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
                    $("#vendor_authorised_person_designation").val('');

                    $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
                    $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);

                    $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
                    $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

                }
            });
        });


        $(document).on("click", ".add_data", function() {
            var data = this.value;
            $("#createdata").val(data);
            // confirm('Are you sure to Submit?')
            $("#add_frm").submit();
        });

        // $(document).on("click", ".edit_data", function() {
        //   var data = this.value;
        //   $("#editData").val(data);
        //   alert(data);
        //   $("#edit_frm").submit();
        // });

        $(".edit_data").click(function() {
            var data = this.value;
            $("#editData").val(data);
            //confirm('Are you sure to Submit?')
            $("#edit_frm").submit();
        });

        // $(document).on("click", ".js-btn-next", function() {
        //   console.log("hi there!!!!!");
        // });

    });


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


    $(document).on("change", "#yeardropdown", function() {
        console.log("hi");
        let yearId = $(this).val();
        let itemRowVal = $(this).data('val');
        let admin_month_var = $(".admin_month_var_" + itemRowVal).val()
        //    alert(admin_month_var);

        $.ajax({
            type: "GET",
            url: `ajaxs/ajax-variant-list.php`,
            data: {
                act: "variant",
                yearId,
                admin_month_var
            },
            beforeSend: function() {
                $(".monthvariant_" + itemRowVal).html(`<option value="">Loding...</option>`);
                // $(".storagelocation_" + itemRowVal).html(`<option value="">Loding...</option>`);
            },
            success: function(response) {
                console.log(response);
                // var obj = jQuery.parseJSON(response);
                $(".monthvariant_" + itemRowVal).html(response);
                //$(".storagelocation_" + itemRowVal).html(obj['slocation']);


            }

        });

    });



    // datatable
    // $('#mytable2').DataTable({
    //   "paging": false,
    //   "searching": false,
    //   "ordering": true,
    // });

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };

    window.onscroll = function() {
        myFunction()
    };

    var navbar = document.getElementById("action-navbar");
    var sticky = action - navbar.offsetTop;

    function myFunction() {
        if (window.pageYOffset >= sticky) {
            action - navbar.classList.add("sticky")
        } else {
            action - navbar.classList.remove("sticky");
        }
    };
</script>

<script src="<?= BASE_URL; ?>public/validations/customerValidation.js"></script>