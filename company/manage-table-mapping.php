<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/company/func-functionalities.php");


if (isset($_POST["changeStatus"])) {

    // console($_POST);
    // exit();
    $newStatusObj = ChangeStatusfunctionalities($_POST, "work_center_id", "status");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}

if (isset($_POST["visit"])) {
    $newStatusObj = Visitfunctionalities($_POST);
    swalToast($newStatusObj["status"], $newStatusObj["message"], COMPANY_URL);
}


if (isset($_POST["createdata"])) {
    // console($_POST);
    // exit();
    $addNewObj = map_table($_POST);
    if ($addNewObj["status"] == "success") {
        swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
    } else {
        swalToast($addNewObj["status"], $addNewObj["message"]);
    }
}

if (isset($_POST["editdata"])) {
    //   console($_POST);
    //   exit();
    $editDataObj = edit_work_center($_POST);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
} ?>
<link rel="stylesheet" href="../public/assets/listing.css">


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
                            <h3 class="card-title">Manage Work Center-Table Mapping</h3>
                            <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary float-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>
                        </li>
                    </ul>
                    <div class="card card-tabs">
                        <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                            <div class="card-body">

                                <div class="row filter-serach-row">

                                    <div class="col-lg-2 col-md-2 col-sm-12">

                                        <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                                    </div>

                                    <div class="col-lg-10 col-md-10 col-sm-12">

                                        <div class="row table-header-item">

                                            <div class="col-lg-11 col-md-11 col-sm-11">

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

                                            </div>

                                            <div class="col-lg-1 col-md-1 col-sm-1">

                                                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#funcAddForm"><i class="fa fa-plus"></i></a>

                                            </div>

                                        </div>



                                    </div>

                                </div>

                            </div>

                        </form>

                        <div class="modal fade add-modal func-add-modal" id="funcAddForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                                    <input type="hidden" name="createdata" id="createdata" value="">
                                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

                                    <div class="modal-content card">
                                        <div class="modal-header card-header pt-2 pb-2 px-3">
                                            <h4 class="text-xs text-white mb-0">Map Work Center</h4>
                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                    </button> -->
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input">
                                                        <label>Work Center</label>
                                                        <select name="wc" class="form-control" id="wct_id">
                                                            <option>Select Work Center</option>
                                                            <?php
                                                            $wc = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id`=$company_id", true);
                                                            foreach ($wc['data'] as $wc) {

                                                            ?>
                                                                <option value="<?= $wc['work_center_id'] ?>"><?= $wc['work_center_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>




                                                        </select>
                                                        <span class="error work_center"></span>
                                                    </div>
                                                    <div class="form-input">
                                                        <label>Table</label>
                                                        <select name="table_id" class="form-control" id="table_map">
                                                            <option>Select table</option>
                                                            <?php
                                                            $table = queryGet("SELECT * FROM `erp_table_master` WHERE `company_id`=$company_id", true);
                                                            foreach ($table['data'] as $table) {

                                                            ?>
                                                                <option value="<?= $table['table_id'] ?>"><?= $table['table_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>




                                                        </select>
                                                        <span class="error cost_center"></span>
                                                    </div>


                                                    <div class="form-input">
                                                        <label>KAM</label>
                                                        <select name="kam" class="form-control" id="kam">
                                                            <option>Select KAM</option>
                                                            <?php
                                                            $kam = queryGet("SELECT * FROM `erp_kam` WHERE `company_id`=$company_id", true);
                                                            foreach ($kam['data'] as $kam) {

                                                            ?>
                                                                <option value="<?= $kam['kamId'] ?>"><?= $kam['kamCode'] ?></option>
                                                            <?php
                                                            }
                                                            ?>




                                                        </select>
                                                        <span class="error cost_center"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary add_data" value="add_post">Submit</button>

                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>


                        <div class="tab-content" id="custom-tabs-two-tabContent">
                            <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                                <?php
                                $cond = '';
                                $sts = " AND `status` !='deleted'";
                                if (isset($_REQUEST['status']) && $_REQUEST['status'] != '') {
                                    $sts = ' AND status="' . $_REQUEST['status'] . '"';
                                }

                                if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                                    $cond .= " AND created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                                }

                                if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                                    $cond .= " AND (`work_center_name` like '%" . $_REQUEST['keyword'] . "%' OR `work_center_description` like '%" . $_REQUEST['keyword'] . "%')";
                                }

                                $sql_list = "SELECT * FROM `erp_work_center` WHERE 1 " . $cond . " " . $sts . " AND company_id='" . $company_id . "' ORDER BY work_center_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                                $qry_list = mysqli_query($dbCon, $sql_list);
                                $num_list = mysqli_num_rows($qry_list);


                                $countShow = "SELECT count(*) FROM `erp_work_center` WHERE 1 " . $cond . " AND company_id='" . $company_id . "' " . $sts . " ";
                                $countQry = mysqli_query($dbCon, $countShow);
                                $rowCount = mysqli_fetch_array($countQry);
                                $count = $rowCount[0];
                                $cnt = $GLOBALS['start'] + 1;
                                $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_WORK_CENTER_TABLE_MAPPING", $company_id);
                                $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                                $settingsCheckbox = unserialize($settingsCh);
                                if ($num_list > 0) {
                                ?>
                                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                    <th>Work Center</th>
                                                <?php }
                                                if (in_array(2, $settingsCheckbox)) { ?>
                                                    <th>Work Center Desc</th>
                                                <?php }
                                                ?>
                                                <th>Table</th>
                                                <?php
                                                if (in_array(3, $settingsCheckbox)) { ?>
                                                    <th>Created By</th>
                                                <?php  }
                                                if (in_array(4, $settingsCheckbox)) { ?>
                                                    <th>Created At</th>
                                                <?php  }
                                                if (in_array(5, $settingsCheckbox)) { ?>
                                                    <th>Modified At</th>
                                                <?php  }
                                                if (in_array(6, $settingsCheckbox)) { ?>
                                                    <th>Modified At</th>
                                                <?php } ?>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($qry_list)) {
                                                $wc_id = $row['work_center_id'];
                                                $table_sql = queryGet("SELECT * FROM `erp_table_wc_mapping` AS wc_mapping LEFT JOIN `erp_table_master` AS table_master ON wc_mapping.table_id = table_master.table_id WHERE wc_mapping.`wc_id` = $wc_id AND wc_mapping.`company_id` = $company_id", true);
                                               //  console($table_sql);
                                                $table_arr = [];
                                                $tableNames = array_column($table_sql['data'], 'table_name');

                                                // Convert array to comma-separated string
                                                $tableNamesString = implode(', ', $tableNames);

                                                // Output the result


                                            ?>
                                                <tr>
                                                    <td><?= $cnt++ ?></td>
                                                    <?php if (in_array(1, $settingsCheckbox)) { ?>
                                                        <td><?= $row['work_center_name'] . '(' . $row['work_center_code'] . ')' ?></td>
                                                    <?php }
                                                    if (in_array(2, $settingsCheckbox)) { ?>
                                                        <td><?= $row['work_center_description'] ?></td>
                                                    <?php }
                                                    ?>
                                                    <td>

                                                        <?= $tableNamesString;
                                                        ?>

                                                    </td>
                                                    <?php
                                                    if (in_array(3, $settingsCheckbox)) { ?>
                                                        <td><?= getCreatedByUser($row['created_by']) ?></td>
                                                    <?php }
                                                    if (in_array(4, $settingsCheckbox)) { ?>
                                                        <td><?= formatDateTime($row['created_at']); ?></td>
                                                    <?php }
                                                    if (in_array(5, $settingsCheckbox)) { ?>
                                                        <td><?= getCreatedByUser($row['updated_by']) ?></td>
                                                    <?php }
                                                    if (in_array(6, $settingsCheckbox)) { ?>
                                                        <td><?= formatDateTime($row['update_at']); ?></td>
                                                    <?php } ?>
                                                    <td>
                                                        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                                            <input type="hidden" name="id" value="<?php echo $row['work_center_id'] ?>">
                                                            <input type="hidden" name="changeStatus" value="active_inactive">
                                                            <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change Status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">
                                                                <?php if ($row['status'] == "active") { ?>
                                                                    <span class="status"><?php echo ucfirst($row['status']); ?></span>
                                                                <?php } else if ($row['status'] == "inactive") { ?>
                                                                    <span class="status-danger"><?php echo ucfirst($row['status']); ?></span>
                                                                <?php } else if ($row['status'] == "draft") { ?>
                                                                    <span class="status-warning"><?php echo ucfirst($row['status']); ?></span>

                                                                <?php } ?>

                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td>

                                                        <!-- <a href="<?= basename($_SERVER['PHP_SELF']) . "?view=" . $row['work_center_id']; ?>" style="cursor: pointer;" class="btn btn-sm" title="View Branch"><i class="fa fa-eye po-list-icon"></i></a> -->
                                                        <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['work_center_id']; ?>" style="cursor: pointer;" class="btn btn-sm" data-toggle="modal" data-target="#editFunctionality_<?= $row['work_center_id'] ?>" title="Edit Branch"><i class="fa fa-edit po-list-icon"></i></a>


                                                        <div class="modal fade add-modal func-add-modal" id="editFunctionality_<?= $row['work_center_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                           <?php
                                                            console($row)
                                                            ?>
                                                            <div class="modal-dialog" role="document">
                                                                <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
                                                                    <input type="hidden" name="editdata" id="editdata" value="">
                                                                    <input type="hidden" name="id" id="" value="<?= $row['work_center_id'] ?>">

                                                                    <div class="modal-content card">
                                                                        <div class="modal-header card-header pt-2 pb-2 px-3">
                                                                            <h4 class="text-xs text-white mb-0">Edit Table Mapping</h4>

                                                                        </div>
                                                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input">
                                                        <label>Work Center</label>
                                                        <select name="wc" class="form-control" id="wct_id">
                                                            <option>Select Work Center</option>
                                                            <?php
                                                            $wc = queryGet("SELECT * FROM `erp_work_center` WHERE `company_id`=$company_id", true);
                                                            foreach ($wc['data'] as $wc) {

                                                            ?>
                                                                <option value="<?= $wc['work_center_id'] ?>" <?php if($row['wc_id'] == $wc['work_center_id']){ echo 'selected';} ?>><?= $wc['work_center_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>




                                                        </select>
                                                        <span class="error work_center"></span>
                                                    </div>
                                                    <div class="form-input">
                                                        <label>Table</label>
                                                        <select name="table_id" class="form-control" id="table_map">
                                                            <option>Select table</option>
                                                            <?php
                                                            $table = queryGet("SELECT * FROM `erp_table_master` WHERE `company_id`=$company_id", true);
                                                            foreach ($table['data'] as $table) {

                                                            ?>
                                                                <option value="<?= $table['table_id'] ?>"><?= $table['table_name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>




                                                        </select>
                                                        <span class="error cost_center"></span>
                                                    </div>


                                                    <div class="form-input">
                                                        <label>KAM</label>
                                                        <select name="kam" class="form-control" id="kam">
                                                            <option>Select KAM</option>
                                                            <?php
                                                            $kam = queryGet("SELECT * FROM `erp_kam` WHERE `company_id`=$company_id", true);
                                                            foreach ($kam['data'] as $kam) {

                                                            ?>
                                                                <option value="<?= $kam['kamId'] ?>"><?= $kam['kamCode'] ?></option>
                                                            <?php
                                                            }
                                                            ?>




                                                        </select>
                                                        <span class="error cost_center"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                                        <div class="modal-footer">
                                                                            <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                                                            <button type="submit" class="btn btn-primary update_data" value="update_post">Update</button>
                                                                            <!-- <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button> -->

                                                                        </div>
                                                                    </div>

                                                                </form>
                                                            </div>
                                                        </div>




                                                        <!-- <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" class="btn btn-sm">
                                                                <input type="hidden" name="id" value="<?php echo $row['work_center_id'] ?>">
                                                                <input type="hidden" name="changeStatus" value="delete">
                                                                <button title="Delete Branch" type="submit" onclick="return confirm('Are you sure to delete?')" class="p-0 btn btn-sm" style="cursor: pointer; border:none; background: none;"><i class="fa fa-trash po-list-icon" style="color: red;"></i></button>
                                                            </form> -->
                                                    </td>
                                                </tr>

                                                <!-- <div class="modal select-pr-modal" id="select-pr">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header py-1" style="background-color: #003060; color:white;">
                                                                <h5 class="modal-title" style="color:white;">TABLE MAPPING</h5>
                                                                <button type="button" id="mapInvoiceItemCodeModalCloseBtn" class="close" data-dismiss="modal">&times;</button>
                                                            </div>
                                                            
                                                            <form id="pr_form"> 
                                                                <div class="modal-body">
                                                                    <input type="hidden" id="wc_id" name="wc_id" value="work_center_id" >

                                                                <?php

                                                                $select_table = queryGet("SELECT *,table_map.table_id as tab_id,table_master.table_id as table_id FROM `erp_table_master` AS table_master  LEFT JOIN `erp_table_wc_mapping` AS table_map  ON table_master.table_id = table_map.table_id WHERE table_master.company_id = $company_id ", true);
                                                                //  /  console($select_table);

                                                                foreach ($select_table['data'] as $select) {

                                                                ?>
                                                                     <input type="checkbox" id="tablecheckbox" name="tablecheckbox[]" value="<?= $select['table_id'] ?>" 
                                                                     <?php
                                                                        if ($select['tab_id'] != '') {
                                                                            echo 'checked';
                                                                        }
                                                                        ?>
                                                                     >
                                                                                            <p><?= $select['table_name'] ?></p>

                                                                <?php
                                                                }
                                                                ?>

                                                                    <button id="map_table" class="btn btn-primary float-right mt-3">Select Table</button>
                                                                </div>
                                                            </form> 
                                                        </div>
                                                    </div>
                                                </div> -->

                                            <?php  } ?>
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
                                    <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
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

                        <!---------------------------------Table settings Model Start--------------------------------->

                        <div class="modal" id="myModal2">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Table Column Settings</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>" onsubmit="return table_settings();">
                                        <input type="hidden" name="tablename" value="<?= TBL_COMPANY_ADMIN_TABLESETTINGS; ?>" />
                                        <input type="hidden" name="pageTableName" value="ERP_WORK_CENTER_TABLE_MAPPING" />
                                        <div class="modal-body">
                                            <div id="dropdownframe"></div>
                                            <div id="main2">
                                                <table>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                                            Work Center</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                                            Work Center Desc</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                                            Created By</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="4" />
                                                            Created At</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="5" />
                                                            Modified By</td>
                                                    </tr>
                                                    <tr>
                                                        <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="6" />
                                                            Modified At</td>
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
    $(document).on("change", "#wct_id", function() {
        //  alert(1);
        var wc_id = $(this).val();
        // alert(val);

        $.ajax({
            type: "GET",
            url: "ajaxs/ajax-table-map.php",
            data: {
                wc_id: wc_id
            },
            beforeSend: function() {
                $('#table_map').html('Loading...');
            },
            success: function(response) {
                // Handle the success response here

                console.log(response);

                $('#table_map').html(response);


            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error("Error:", error);
            },
            complete: function() {}
        });


    });


    $('#map_table').click(function() {

        // alert(1);
        var wc_id = $('#wc_id').val();
        var map_array = $('input[name="tablecheckbox[]"]:checked').map(function() {
            return this.value;
        }).get();

        //alert(map_array);

        $.ajax({
            type: "GET",
            url: "<?= BASE_URL ?>branch/location/bom/ajax/ajax-table-map.php", // Specify the URL where you want to submit the form
            data: {
                map_array: map_array
            },
            success: function(response) {
                // Handle the success response here



            },
            error: function(xhr, status, error) {
                // Handle errors here
                console.error("Error:", error);
            },
            complete: function() {}
        });



    });
</script>
<script>
    $('.m-input').on('keyup', function() {
        $(this).parent().children('.error').hide()
    });
    /*
      $(".add_data").click(function() {
        var data = this.value;
        $("#createdata").val(data);
        let flag = 1;
        var Ragex = "/[0-9]{4}/";
        if ($("#functionalities_name").val() == "") {
          $(".functionalities_name").show();
          $(".functionalities_name").html("functionalities name is requried.");
          flag++;
        } else {
          $(".functionalities_name").hide();
          $(".functionalities_name").html("");
        }
        if ($("#functionalities_desc").val() == "") {
          $(".functionalities_desc").show();
          $(".functionalities_desc").html("Description is requried.");
          flag++;
        } else {
          $(".functionalities_desc").hide();
          $(".functionalities_desc").html("");
        }
        if (flag == 1) {
          $("#add_frm").submit();
        }


      });
      $(".edit_data").click(function() {
        var data = this.value;
        $("#editdata").val(data);
        alert(data);
        //$( "#edit_frm" ).submit();
      });
    */

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
<style>
    .dataTable thead {
        top: 0px !important;
    }
</style>