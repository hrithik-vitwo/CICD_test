<?php
include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/branch/func-cost-center.php");
require_once("../app/v1/functions/company/func-ChartOfAccounts.php");

$company_id = $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"];
if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusCostCenter($_POST, "CostCenter_id", "CostCenter_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
  $addNewObj = createDataCostCenter($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);

  if ($addNewObj["status"] == "success") {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataCostCenter($_POST);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
$sqqql = "SELECT CostCenter_code FROM `" . ERP_COST_CENTER . "` WHERE `company_id`='" . $company_id . "' AND `CostCenter_status`!='deleted' ORDER BY CostCenter_id DESC LIMIT 1";
$CostCenter_code = queryGet($sqqql);
if (isset($CostCenter_code['data'])) {
  $CostCenter_Lastcode = $CostCenter_code['data']['CostCenter_code'];
} else {
  $CostCenter_Lastcode = '';
}
?>

<link rel="stylesheet" href="../public/assets/listing.css">

<style>
  .cost-center-add-modal .modal-dialog {
    transform: translate(0px, 0%) !important;
}

.cost-center-list tbody tr:nth-child(odd) td {
  background-color: #f5f5f5;
}
.cost-center-list tbody tr:nth-child(even) td {
  background-color: #fff;
}
</style>

<?php if (isset($_GET['edit']) && $_GET["edit"] > 0) {
  $sqqql = "SELECT * FROM `" . ERP_COST_CENTER . "` WHERE `CostCenter_id`='" . $_GET["edit"] . "' AND `CostCenter_status`!='deleted'";
  $CostCenter_code = queryGet($sqqql);
  if (isset($CostCenter_code['data'])) {
    $CostCenter_code = $CostCenter_code['data'];
  } else {
    redirect(basename($_SERVER['PHP_SELF']));
    exit;
  }
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header mb-2 p-0  border-bottom">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">
        <div class="row pt-2 pb-2">
          <div class="col-md-6">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage CostCenter</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Edit CostCenter</a></li>
            </ol>
          </div>
          <div class="col-md-6" style="display: flex;">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-danger btnstyle ml-2">Back</button></a>
            <?php if ($CostCenter_code['CostCenter_status'] != 'active') { ?>
              <button class="btn btn-danger btnstyle ml-2 edit_data" value="add_draft">Save As Draft</button>
            <?php } ?>
            <button class="btn btn-primary btnstyle gradientBtn ml-2 edit_data" value="add_post"><i class="fa fa-plus fontSize"></i> Update</button>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" name="edit_frm" id="edit_frm">
          <input type="hidden" name="editdata" id="editdata" value="">
          <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
          <input type="hidden" name="fldAdminBranchId" id="fldAdminBranchId" value="">
          <input type="hidden" name="fldAdminBranchLocationId" id="fldAdminBranchLocationId" value="">
          <input type="hidden" name="CostCenter_id" id="CostCenter_id" value="<?php echo $_GET['edit']; ?>">
          <div class="row">
            <div class="col-md-8">
              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
              <div id="accordion">
                <div class="card card-primary">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Basic Details </a> </h4>
                  </div>
                  <div id="collapseOne" class="collapse show" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" class="m-input" id="CostCenter_code" name="CostCenter_code" value="<?= $CostCenter_code['CostCenter_code']; ?>" required readonly>
                            <label>Cost Center Code* </b></label>
                            <span class="error CostCenter_code"></span>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="CostCenter_desc" class="m-input" id="CostCenter_desc" value="<?= $CostCenter_code['CostCenter_desc']; ?>" required>
                            <label>Name/Description*</label>
                            <span class="error CostCenter_desc"></span>
                          </div>
                        </div>


                      </div>
                    </div>
                  </div>
                </div>


              </div>
            </div>
            <!---------------------------------------------------------------------------------------------->
            <div class="col-md-4">
              <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                  <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">TAB1</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">TAB2</a> </li>
                  </ul>
                </div>
                <div class="card-body fontSize">
                  <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab"> 90 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin malesuada lacus ullamcorper
                      dui
                      molestie, sit amet congue quam finibus. Etiam ultricies nunc non magna feugiat commodo. Etiam
                      odio
                      magna, mollis auctor felis vitae, ullamcorper ornare ligula. Proin pellentesque tincidunt nisi,
                      vitae ullamcorper felis aliquam id. Pellentesque habitant morbi tristique senectus et netus et
                      malesuada fames ac turpis egestas. Proin id orci eu lectus blandit suscipit. Phasellus porta,
                      ante
                      et varius ornare, sem enim sollicitudin eros, at commodo leo est vitae lacus. Etiam ut porta
                      sem.
                      Proin porttitor porta nisl, id tempor risus rhoncus quis. In in quam a nibh cursus pulvinar non
                      consequat neque. Mauris lacus elit, condimentum ac condimentum at, semper vitae lectus. Cras
                      lacinia erat eget sapien porta consectetur. </div>
                    <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab"> Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut
                      ligula
                      tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                      Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas
                      sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu
                      lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod
                      pellentesque diam. </div>
                    <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel" aria-labelledby="custom-tabs-three-messages-tab"> Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue
                      id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac
                      tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit
                      condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus
                      tristique.
                      Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est
                      libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id
                      fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. </div>
                    <div class="tab-pane fade" id="custom-tabs-three-settings" role="tabpanel" aria-labelledby="custom-tabs-three-settings-tab"> Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis
                      ac,
                      ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi
                      euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum
                      placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam.
                      Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet
                      accumsan ex sit amet facilisis. </div>
                  </div>
                </div>
                <!-- /.card -->
              </div>
              <!--<div class="w-100 mt-3">
              <button type="submit" name="addInventoryItem" class="gradientBtn btn-success btn btn-block btn-sm"> <i class="fa fa-plus fontSize"></i> Add New </button>
            </div>-->
            </div>
          </div>
        </form>


      </div>
    </section>
    <!-- /.content -->
  </div>
<?php
} else {
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <ul class="nav nav-tabs border-bottom-0" id="custom-tabs-two-tab" role="tablist">
              <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                <h3 class="card-title mb-3">Manage Cost Center</h3>
                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" data-toggle="modal" data-target="#addCostCenter" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
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

                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#addCostCenter"><i class="fa fa-plus"></i></a>

                        </div>

                      </div>



                    </div>

                  </div>

                </div>

              </form>
              <div class="modal fade add-modal cost-center-add-modal" id="addCostCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="add_frm" name="add_frm">
                    <input type="hidden" name="createdata" id="createdata" value="">
                    <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
                    <input type="hidden" name="fldAdminBranchId" id="fldAdminBranchId" value="0">
                    <input type="hidden" name="fldAdminBranchLocationId" id="fldAdminBranchLocationId" value="0">
                    <div class="modal-content card">
                      <div class="modal-header card-header pt-2 pb-2 px-3">
                        <h4 class="text-xs text-white mb-0">Create Cost Center</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="form-input mb-3">
                              <label>Cost Center Code*</label>
                              <input type="text" class="form-control" id="CostCenter_code" name="CostCenter_code" value="<?= getCostCenterSerialNumber($CostCenter_Lastcode); ?>" required readonly>
                              <span class="error CostCenter_code"></span>
                            </div>
                            <div class="form-input">
                              <label>Name/Description</label>
                              <input type="text" name="CostCenter_desc" class="form-control" id="CostCenter_desc" value="" required>
                              <span class="error CostCenter_desc"></span>
                            </div>
                            <!-- <div class="form-input">
                              <label>Labour Hour Rate</label>
                              <input type="text" name="lhr" class="form-control" id="lhr" value="" required>
                              <span class="error lhr"></span>
                            </div>
                            <div class="form-input">
                              <label>Machine Hour Rate</label>
                              <input type="text" name="mhr" class="form-control" id="mhr" value="" required>
                              <span class="error mhr"></span>
                            </div> -->

                            <!-- <div class="form-input">
                              <label> SELECT GL</label>
                              <select name="gl" class="form-control" id="gl"><?php 
                            $gl =  getAllChartOfAccounts_list_by_p($company_id, 2);
                            
                            foreach($gl['data'] as $gl){
                             
                            ?>
                          
                            <option value="<?= $gl['id'] ?>"><?= $gl['gl_code'] ."(". $gl['gl_label'] .")" ?></option>
                            <?php
                            }
                            ?>
                              </select>
                              <span class="error mhr"></span>
                            </div> -->
                            <div class="form-input">
                              <label> Select Location</label>
                              <select name="location" class="form-control" id="location">
                              <option>Select Location</option>
                              <?php 
                            $location = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `company_id`=$company_id AND `othersLocation_status`!='deleted' ",true);

                            
                            foreach($location['data'] as $location)
                            {
                             
                            ?>
                          
                            <option value="<?= $location['othersLocation_id'] ?>"><?= $location['othersLocation_name'] ."(". $location['othersLocation_code'] .")" ?></option>
                           
                            <?php
                            }
                            ?>
                              </select>
                         
                            </div>
                            
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary add_data" value="add_post"> Submit</button>

                      </div>
                    </div>

                  </form>
                </div>
              </div>



              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                  <?php
                  $cond = '';
                  $con = '';
                  $sts = " AND `CostCenter_status` !='deleted'";
                  if (isset($_REQUEST['CostCenter_status']) && $_REQUEST['CostCenter_status'] != '') {
                    $sts = ' AND CostCenter_status="' . $_REQUEST['CostCenter_status'] . '"';
                  }

                  if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                    $cond .= " AND CostCenter_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                  }

                  if (isset($_SESSION["logedCompanyAdminInfo"]["fldAdminBranchId"]) && $_SESSION["logedCompanyAdminInfo"]["fldAdminBranchId"] != '') {
                    $con = " AND branch_id ='0' AND location_id ='0'";
                  }

                  if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                    $cond .= " AND (`CostCenter_code` like '%" . $_REQUEST['keyword'] . "%' OR `CostCenter_desc` like '%" . $_REQUEST['keyword'] . "%')";
                  }

                  $sql_list = "SELECT * FROM `" . ERP_COST_CENTER . "` WHERE 1 " . $cond . $con . " " . $sts . " AND company_id='" . $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"] . "' ORDER BY CostCenter_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM `" . ERP_COST_CENTER . "` WHERE 1 " . $cond . $con  . " AND company_id='" . $company_id . "' " . $sts . " ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_COST_CENTER", $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) {
                  ?>
                    <table id="mytable" class="table cost-center-list table-hover text-nowrap">
                      <thead>
                        <tr>
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>CostCenter</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>CostCenter Desc</th>
                          <?php }
                          
                              
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th>Created By</th>
                          <?php  }
                          if (in_array(6, $settingsCheckbox)) { ?>
                            <th>Created At</th>
                          <?php  }
                          if (in_array(7, $settingsCheckbox)) { ?>
                            <th>Modified By</th>
                          <?php  }
                          if (in_array(8, $settingsCheckbox)) { ?>
                            <th>Modified At</th>
                          <?php } ?>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        while ($row = mysqli_fetch_assoc($qry_list)) {
                        ?>
                          <tr>
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $row['CostCenter_code'] ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><p class="pre-normal"><?= $row['CostCenter_desc'] ?></p></td>
                            <?php }
                          
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td><?= getCreatedByUser($row['CostCenter_created_by']) ?></td>
                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>
                              <td><?= formatDateTime($row['CostCenter_created_at']); ?></td>
                            <?php }
                            if (in_array(7, $settingsCheckbox)) { ?>
                              <td><?= getCreatedByUser($row['CostCenter_updated_by']) ?></td>
                            <?php }
                            if (in_array(8, $settingsCheckbox)) { ?>
                              <td><?= formatDateTime($row['CostCenter_updated_at']); ?></td>
                            <?php } ?>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['CostCenter_id'] ?>">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button <?php if ($row['CostCenter_status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change Status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['CostCenter_status'] ?>">
                                  <?php if ($row['CostCenter_status'] == "active") { ?>
                                    <span class="status"><?php echo ucfirst($row['CostCenter_status']); ?></span>
                                  <?php } else if ($row['CostCenter_status'] == "inactive") { ?>
                                    <span class="status-danger"><?php echo ucfirst($row['CostCenter_status']); ?></span>
                                  <?php } else if ($row['CostCenter_status'] == "draft") { ?>
                                    <span class="status-warning"><?php echo ucfirst($row['CostCenter_status']); ?></span>

                                  <?php } ?>

                                </button>
                              </form> 
                            </td>
                            <td>

                              <!-- <a href="<?= basename($_SERVER['PHP_SELF']) . "?view=" . $row['CostCenter_id']; ?>" style="cursor: pointer;" class="btn btn-sm" title="View Branch"><i class="fa fa-eye"></i></a> -->
                              <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['CostCenter_id']; ?>" data-toggle="modal" data-target="#editCostCenter<?php echo $row['CostCenter_id'] ?>" style="cursor: pointer;" class="btn btn-sm" title="Edit Cost Center"><i class="fa fa-edit po-list-icon"></i></a>

                              <form action="" method="POST" class="btn btn-sm">
                                <input type="hidden" name="id" value="<?php echo $row['CostCenter_id'] ?>">
                                <input type="hidden" name="changeStatus" value="delete">
                                <button title="Delete Cost Center" type="submit" onclick="return confirm('Are you sure to delete?')" class="btn btn-sm" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button>
                              </form>
                            </td>

                          </tr>

                          <div class="modal fade add-modal cost-center-edit-modal" id="editCostCenter<?php echo $row['CostCenter_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content card">
                                  <div class="modal-header card-header pt-2 pb-2 px-3">
                                    <h4 class="text-xs text-white mb-0">Create Cost Center</h4>
                                  </div>

                                  <div class="modal-body">
                                    <div class="row">
                                      <div class="col-lg-12 col-md-12 col-sm-12">
                                        <input type="hidden" name="editdata" value="">
                                        <input type="hidden" name="CostCenter_id" value="<?php echo $row['CostCenter_id'] ?>">
                                        <div class="form-input mb-3">
                                          <label>Cost Center Code*</label>
                                          <input type="text" class="form-control" name="CostCenter_code" value="<?= getCostCenterSerialNumber($CostCenter_Lastcode); ?>" required readonly>
                                          <span class="error CostCenter_code"></span>
                                        </div>
                                        <div class="form-input">
                                          <label>Name/Description</label>
                                          <input type="text" name="CostCenter_desc" class="form-control" value="<?= $row['CostCenter_desc']; ?>" required>
                                          <span class="error CostCenter_desc"></span>
                                        </div>

                                        <!-- <div class="form-input">
                              <label>Labour Hour Rate</label>
                              <input type="text" name="lhr" class="form-control" id="lhr" value="<?= $row['labour_hour_rate']; ?>" required>
                              <span class="error lhr"></span>
                            </div>
                            <div class="form-input">
                              <label>Machine Hour Rate</label>
                              <input type="text" name="mhr" class="form-control" id="mhr" value="<?= $row['machine_hour_rate']; ?>" required>
                              <span class="error mhr"></span>
                            </div> -->

                            <div class="form-input">
                              <label> Select Location</label>
                              <select name="location" class="form-control" id="location">
                              <option>Select Location</option>
                              <?php 
                            $location = queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `company_id`=$company_id AND `othersLocation_status`!='deleted' ",true);

                            
                            foreach($location['data'] as $location)
                            {
                             
                            ?>
                          
                            <option value="<?= $location['othersLocation_id'] ?>" <?php if($location['othersLocation_id'] == $row['location_id']) { echo "selected"; } ?>><?= $location['othersLocation_name'] ."(". $location['othersLocation_code'] .")" ?></option>
                           
                            <?php
                            }
                            ?>
                              </select>
                         
                            </div>

                            


                                      </div>
                                    </div>
                                  </div>

                                  <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary update_data" value="update_post">Update</button>
                                  </div>



                                </div>

                              </div>
                            </form>
                          </div>

                        <?php  } ?>

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
                      <input type="hidden" name="pageTableName" value="ERP_COST_CENTER" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                CostCenter</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                CostCenter Desc</td>
                            </tr>

                            <!-- <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                 LHR</td>
                            </tr> -->

                            <!-- <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                                 MHR</td>
                            </tr> -->


                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                                Created By</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />
                                Created At</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />
                                Modified By</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />
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
}
include("common/footer.php");
?>
<script>
  $('.m-input').on('keyup', function() {
    $(this).parent().children('.error').hide()
  });
  /*
    $(".update_data").click(function() {
      var data = this.value;
      $("#createdata").val(data);
      let flag = 1;
      var Ragex = "/[0-9]{4}/";
      if ($("#CostCenter_code").val() == "") {
        $(".CostCenter_code").show();
        $(".CostCenter_code").html("Credit Period is requried.");
        flag++;
      } else {
        $(".CostCenter_code").hide();
        $(".CostCenter_code").html("");
      }
      if ($("#CostCenter_desc").val() == "") {
        $(".CostCenter_desc").show();
        $(".CostCenter_desc").html("Description is requried.");
        flag++;
      } else {
        $(".CostCenter_desc").hide();
        $(".CostCenter_desc").html("");
      }

      if (flag != 1) {
        return false;
      } else {
        $("#add_frm").submit();
      }


    });
    $(".edit_data").click(function() {
      var data = this.value;
      $("#editdata").val(data);
      let flag = 1;
      var Ragex = "/[0-9]{4}/";
      if ($("#CostCenter_code").val() == "") {
        $(".CostCenter_code").show();
        $(".CostCenter_code").html("Credit Period is requried.");
        flag++;
      } else {
        $(".CostCenter_code").hide();
        $(".CostCenter_code").html("");
      }
      if ($("#CostCenter_desc").val() == "") {
        $(".CostCenter_desc").show();
        $(".CostCenter_desc").html("Description is requried.");
        flag++;
      } else {
        $(".CostCenter_desc").hide();
        $(".CostCenter_desc").html("");
      }

      if (flag != 1) {
        return false;
      } else {
        $("#edit_frm").submit();
      }

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


    $(document).on("keyup paste keydown", "#branch_gstin", function() {
      var branch_gstin = $("#branch_gstin").val();
      var leng_gstin = branch_gstin.length;
      if (leng_gstin > 14) {
        $("#vendorPanNo").val(branch_gstin.substr(2, 10));

        $.ajax({
          type: "GET",
          url: `ajaxs/ajax-gst-details.php?gstin=${branch_gstin}`,
          beforeSend: function() {
            $('#gstinloder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          },
          success: function(response) {

            $('#gstinloder').html("");
            responseObj = JSON.parse(response);
            if (responseObj["status"] == "success") {
              responseData = responseObj["data"];

              console.log(responseData);

              $("#branch_name").val(responseData["tradeNam"]);
              $("#con_business").val(responseData["ctb"]);
              $("#build_no").val(responseData['pradr']['addr']['bno']);
              $("#flat_no").val(responseData['pradr']['addr']['flno']);
              $("#street_name").val(responseData['pradr']['addr']['st']);
              $("#pincode").val(responseData['pradr']['addr']['pncd']);
              $("#location").val(responseData['pradr']['addr']['loc']);
              $("#city").val(responseData['pradr']['addr']['city']);
              $("#district").val(responseData['pradr']['addr']['dst']);
              $("#state").val(responseData['pradr']['addr']['stcd']);

              //$("#status").val(responseData["sts"]);

            } else {
              let Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
              });
              Toast.fire({
                icon: `warning`,
                title: `&nbsp;Invalid GSTIN No!`
              });
            }
          }
        });
      }

    });


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