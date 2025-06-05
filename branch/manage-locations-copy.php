<?php
require_once("../app/v1/connection-branch-admin.php");
administratorAuth();
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("common/pagination.php");
require_once("../app/v1/functions/branch/func-others-location.php");


//console($_SESSION);


if (isset($_POST["visit_location"])) {
  $newStatusObj = VisitLocation($_POST);
  if ($newStatusObj["status"] == "success") {
    redirect(LOCATION_URL);
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  } else {
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  }
}
/*
if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"],);
}




if (isset($_POST["createdata"])) {
  $addNewObj = createDataBranches($_POST);
  if ($addNewObj["status"] == "success") {
    $branchId = base64_encode($addNewObj['branchId']);
    redirect($_SERVER['PHP_SELF'] . "?branchLocation=" . $branchId);
    swalToast($addNewObj["status"], $addNewObj["message"]);
    // console($addNewObj);
  } else {
    swalToast($addNewObj["status"], $addNewObj["message"]);
  }
}

if (isset($_POST["editdata"])) {
  $editDataObj = updateDataBranches($_POST);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
*/
// fetch company name by ID 
function fetchCompanyNameById($id = '')
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `erp_companies` WHERE company_id='$id'";
  if ($res = $dbCon->query($sql)) {
    if ($res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $returnData['status'] = "success";
      $returnData['data'] = $row['company_name'];
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "data not found";
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
  }
  return $returnData;
}

// fetch branch name by ID 
function fetchBranchById($id = '')
{
  global $dbCon;
  $returnData = [];
  $sql = "SELECT * FROM `erp_branches` WHERE branch_id='$id'";
  if ($res = $dbCon->query($sql)) {
    if ($res->num_rows > 0) {
      $row = $res->fetch_assoc();
      $returnData['status'] = "success";
      $returnData['data'] = $row['branch_name'];
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "data not found";
    }
  } else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong";
  }
  return $returnData;
}

// fetch functionalities name by ID 
function fetchFunctionalitiesNameById($functionalitiIds = '1,2,3')
{
  global $dbCon;
  $returnData = [];
  $exp = explode(",", $functionalitiIds);
  $returnData['data'] = "";

  foreach ($exp as $key => $rowId) {
    $sql = "SELECT * FROM `erp_company_functionalities` WHERE `functionalities_id`='$rowId'";
    if ($res = $dbCon->query($sql)) {
      if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $returnData['status'] = "success";
        $returnData['data'] .= $row['functionalities_name'] . ",";
      } else {
        $returnData['status'] = "warning";
        $returnData['message'] = "data not found";
      }
    } else {
      $returnData['status'] = "warning";
      $returnData['message'] = "Somthing went wrong";
    }
  }
  return $returnData;
}

// console(fetchFunctionalitiesNameById()['data']);

if (isset($_POST['add_branch_location'])) {
  // console($_POST);
  $companyId = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
  $branchId = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];
  $locationCode = $_POST['locationCode'];
  $locationName = $_POST['locationName'];
  $flatNo = $_POST['flatNo'];
  $buildingNo = $_POST['buildingNo'];
  $streetName = $_POST['streetName'];
  $pinCode = $_POST['pinCode'];
  $location = $_POST['location'];
  $city = $_POST['city'];
  $district = $_POST['district'];
  $state = $_POST['state'];

  $compFunc = implode(',', $_POST['compFunc']);

  $adminName = $_POST['adminName'];
  $adminEmail = $_POST['adminEmail'];
  $adminPhone = $_POST['adminPhone'];
  $adminPassword = $_POST['adminPassword'];

  $locationInsert = "INSERT INTO `erp_branch_otherslocation` 
                        SET 
                            `othersLocation_code`='$locationCode',
                            `company_id`='$companyId',
                            `branch_id`='$branchId',
                            `othersLocation_primary_flag`='0',
                            `companyFunctionalities`='$compFunc',
                            `othersLocation_name`='$locationName',
                            `othersLocation_building_no`='$buildingNo',
                            `othersLocation_flat_no`='$flatNo',
                            `othersLocation_street_name`='$streetName',
                            `othersLocation_pin_code`='$pinCode',
                            `othersLocation_location`='$location',
                            `othersLocation_city`='$city',
                            `othersLocation_district`='$district',
                            `othersLocation_state`='$state'";
  if ($dbCon->query($locationInsert)) {
    // console($locationUpdate);
    $lastId = $dbCon->insert_id;
    $ins = "INSERT INTO `tbl_branch_admin_details` 
                                SET 
                                  `fldAdminCompanyId`='$companyId',
                                  `fldAdminBranchId`='$branchId',
                                  `fldAdminBranchLocationId`='$lastId',
                                  `fldAdminName`='$adminName',
                                  `fldAdminEmail`='$adminEmail',
                                  `fldAdminPhone`='$adminPhone',
                                  `fldAdminPassword`='$adminPassword',
                                  `fldAdminRole`='2'";
    if ($dbCon->query($ins)) {
      // console($ins);
      redirect($_SERVER['PHP_SELF']);
    } else {
      echo "somthing went wrong";
    }
  } else {
    echo "somthing went wrong";
  }
} ?>

<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<?php
if (isset($_GET['create'])) {
?>

<style>
  .noAccess_security{
    display: none;
  }
</style>

  <!-- ############### branch location ################################# -->
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
              <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branch Location</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add Branch Location</a></li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->
    <?php

    if (checkAccess('add')) {
      noAccess(100);
    } else {
      echo 'test';
    } ?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="" name="">
          <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">
          <div class="row">
            <div class="col-md-8">
              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
              <div id="accordion">
                <div class="card card-primary">
                  <div class="card-header">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Location Details </a> </h4>
                  </div>
                  <div id="collapseOne" class="collapse show" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="branchId" value="<?= fetchBranchById($getBranchId = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'])['data'] ?>" class="m-input bg-light" id="locationCode" readonly>
                            <label>Branch </label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="locationCode" class="m-input" id="locationCode">
                            <label>Location Code</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="locationName" class="m-input" id="locationName" value="">
                            <label>Location Name</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="flatNo" class="m-input" id="flatNo" value="">
                            <label>Flat Number</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="buildingNo" class="m-input" id="buildingNo" value="">
                            <label>Building Number</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="streetName" class="m-input" id="streetName" value="">
                            <label>Street Name</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="number" name="pinCode" class="m-input" id="pinCode" value="">
                            <label>Pin Code</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="location" class="m-input" id="location" value="">
                            <label>Location</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="city" class="m-input" id="city" value="">
                            <label>City</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="district" class="m-input" id="district" value="">
                            <label>District</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="state" class="m-input" id="state" value="">
                            <label>State</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-primary">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseTwo"> Company Functionalities </a> </h4>
                  </div>
                  <div id="collapseTwo" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <?php
                          $sql = "SELECT * FROM `erp_company_functionalities` WHERE `functionalities_status`='active'";
                          $res = $dbCon->query($sql);
                          while ($row = $res->fetch_assoc()) {
                          ?>
                            <div class="shadow-sm my-1">
                              <input type="checkbox" name="compFunc[]" id="compFunc_<?= $row['functionalities_id'] ?>" value="<?= $row['functionalities_id'] ?>">
                              <?= $row['functionalities_name'] ?>
                            </div>
                          <?php } ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-primary">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseThree"> Location Admin Details </a> </h4>
                  </div>
                  <div id="collapseThree" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="adminName" class="m-input" id="adminName">
                            <label>User Name</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="email" name="adminEmail" class="m-input" id="adminEmail">
                            <label>User Email</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="adminPhone" class="m-input" id="adminPhone">
                            <label>User Phone</label>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <input type="text" name="adminPassword" class="m-input" id="adminPassword" value="<?php echo rand(1111, 9999); ?>">
                            <label>Password</label>
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
                    <li class="nav-item"> <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#aiBranchDetails1" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">TAB1</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#aiBranchDetails2" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">TAB2</a> </li>
                  </ul>
                </div>
                <div class="card-body fontSize">
                  <div class="tab-content" id="custom-tabs-three-tabContent">
                    <div class="tab-pane fade show active aiBranchDetails1" id="aiBranchDetails1" role="tabpanel" aria-labelledby="custom-tabs-three-home-tab">
                      <?php

                      $branchId = $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'];

                      $sql = "SELECT * FROM `erp_branch_otherslocation` WHERE `branch_id`=$branchId";
                      if ($res = $dbCon->query($sql)) {
                        if ($res->num_rows > 0) {
                          while ($row = $res->fetch_assoc()) {
                      ?>
                            <div class="card">
                              <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Location Name: </strong><?= $row['othersLocation_name'] ?></li>
                                <li class="list-group-item"><strong>Street Name: </strong><?= $row['othersLocation_street_name'] ?></li>
                                <li class="list-group-item"><strong>Status: </strong><?= $row['othersLocation_status'] ?></li>
                              </ul>
                            </div>
                          <?php
                          }
                        } else { ?>
                          <div class="alert alert-danger" style="font-size:1.2em"><strong>Location not found!</strong> <span>In this branch.</span></div>
                        <?php
                        }
                      } else { ?>
                        <div class="alert alert-secondary" style="font-size:1.2em"><strong>Select A Branch!</strong></div>
                      <?php
                      }
                      ?>
                    </div>
                    <div class="tab-pane fade aiBranchDetails2" id="aiBranchDetails2" role="tabpanel" aria-labelledby="custom-tabs-three-profile-tab"> zsdfs Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut
                      ligula
                      tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                      Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas
                      sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu
                      lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod
                      pellentesque diam. </div>
                  </div>
                </div>
                <!-- /.card -->
              </div>
              <!--<div class="w-100 mt-3">
              <button type="submit" name="addInventoryItem" class="gradientBtn btn-success btn btn-block btn-sm"> <i class="fa fa-plus fontSize"></i> Add New </button>
            </div>-->
              <div class="col-md-6">
                <button type="submit" name="add_branch_location" class="btn btn-primary btnstyle gradientBtn ml-2 add_branch_location" value="add_branch_location"><i class="fa fa-plus fontSize"></i> Final Submit</button>
              </div>
            </div>
          </div>
        </form>

        <!-- modal -->
        <div class="modal" id="myModal3">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="col-md-12 mb-3">
                  <div class="input-group">
                    <select name="goodsGroup" class="form-control form-control-border borderColor">
                      <option value="">Branches Group</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group">
                    <input type="text" name="itemCode" class="m-input" id="exampleInputBorderWidth2">
                    <label>Item Code</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group btn-col">
                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
            </div>
          </div>
        </div>
        <!-- modal end -->
        <!-- modal -->
        <div class="modal" id="myModal4">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Heading4</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="col-md-12 mb-3">
                  <div class="input-group">
                    <select name="goodsGroup" class="form-control form-control-border borderColor">
                      <option value="">Branches Group</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group">
                    <input type="text" name="itemCode" class="m-input" id="exampleInputBorderWidth2">
                    <label>Item Code</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group btn-col">
                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
            </div>
          </div>
        </div>
        <!-- modal end -->
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- ############### branch location ################################# -->

<?php
} else if (isset($_GET['edit']) && $_GET["edit"] > 0) {
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
              <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branches</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Edit Branches</a></li>
            </ol>
          </div>
          <div class="col-md-6" style="display: flex;">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-danger btnstyle ml-2">Back</button></a>
            <button class="btn btn-danger btnstyle ml-2 edit_data">Save As Draft</button>
            <button class="btn btn-primary btnstyle gradientBtn ml-2 edit_data"><i class="fa fa-plus fontSize"></i> Final Submit</button>
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
          <div class="row">
            <div class="col-md-8">
              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
              <div id="accordion">
                <div class="card card-primary">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Classification </a> </h4>
                  </div>
                  <div id="collapseOne" class="collapse show" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <select id="" name="goodsType" class="select2 form-control form-control-border borderColor">
                              <option value="">Branches Type</option>
                              <option value="A">A</option>
                              <option value="B">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <select name="goodsGroup" class="select4 form-control form-control-border borderColor">
                              <option value="">Branches Group</option>
                              <option value="A">A</option>
                              <option value="B">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <select name="purchaseGroup" class="select2 form-control form-control-border borderColor">
                              <option value="">Purchase Group</option>
                              <option value="">A</option>
                              <option value="">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="branh" class="m-input" id="exampleInputBorderWidth2">
                            <label>Branches</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <select name="availabilityCheck" class="select2 form-control form-control-border borderColor">
                              <option value="">Availability Check</option>
                              <option value="Daily">Daily</option>
                              <option value="Weekly">Weekly</option>
                              <option value="By Weekly">By Weekly</option>
                              <option value="Monthly">Monthly</option>
                              <option value="Qtr">Qtr</option>
                              <option value="Half Y">Half Y</option>
                              <option value="Year">Year</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-danger">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseTwo"> Basic Details </a> </h4>
                  </div>
                  <div id="collapseTwo" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="itemCode" class="m-input" id="exampleInputBorderWidth2">
                            <label>Item Code</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="itemName" class="m-input" id="exampleInputBorderWidth2">
                            <label>Item Name</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="netWeight" class="m-input" id="exampleInputBorderWidth2">
                            <label>Net Weight</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="grossWeight" class="m-input" id="exampleInputBorderWidth2">
                            <label>Gross Weight</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Volume :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="volume" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="volume">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">height :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="height" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="height">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">width :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="width" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="width">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">length :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="length" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="length">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Base Unit Of Measure :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="baseUnitMeasure" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="baseUnitOfMeasure">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Issue Unit :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="issueUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="issueUnit">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <textarea type="text" name="itemDesc" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-success">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseThree"> Storage Details </a> </h4>
                  </div>
                  <div id="collapseThree" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Storage Bin :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="storageBin" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Bin">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Picking Area :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="pickingArea" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Picking Area">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Temp Control :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="tempControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Temp Control">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Storage Control :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="storageControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Control">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Max Storage Period :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="maxStoragePeriod" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Max Storage Period">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Time Unit :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="timeUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Time Unit">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Min Remain Self Life :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="minRemainSelfLife" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Min Remain Self Life">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-success">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseFour"> Purchase Details </a> </h4>
                  </div>
                  <div id="collapseFour" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Purchasing Value Key :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="purchasingValueKey" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!----------------------------------------------------------------------------------------------->

            <div class="col-md-4">
              <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                  <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Tab1</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Tab2</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill" href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages" aria-selected="false">Tab3</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-settings-tab" data-toggle="pill" href="#custom-tabs-three-settings" role="tab" aria-controls="custom-tabs-three-settings" aria-selected="false">Tab4</a> </li>
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
              <!-- <div class="w-100 mt-3">
              <button type="submit" name="addInventoryItem" class="gradientBtn btn-success btn btn-block btn-sm"> <i class="fa fa-plus fontSize"></i> Add New </button>
            </div>-->
            </div>
          </div>
        </form>

        <!-- modal -->
        <div class="modal" id="myModal3">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="col-md-12 mb-3">
                  <div class="input-group">
                    <select name="goodsGroup" class="form-control form-control-border borderColor">
                      <option value="">Branches Group</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group">
                    <input type="text" name="itemCode" class="m-input" id="exampleInputBorderWidth2">
                    <label>Item Code</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group btn-col">
                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
            </div>
          </div>
        </div>
        <!-- modal end -->
        <!-- modal -->
        <div class="modal" id="myModal4">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Heading4</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="col-md-12 mb-3">
                  <div class="input-group">
                    <select name="goodsGroup" class="form-control form-control-border borderColor">
                      <option value="">Branches Group</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group">
                    <input type="text" name="itemCode" class="m-input" id="exampleInputBorderWidth2">
                    <label>Item Code</label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="input-group btn-col">
                    <button type="submit" class="btn btn-primary btnstyle">Submit</button>
                  </div>
                </div>
              </div>
              <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div> -->
            </div>
          </div>
        </div>
        <!-- modal end -->
      </div>
    </section>
    <!-- /.content -->
  </div>
<?php
} else if (isset($_GET['view']) && $_GET["view"] > 0) {
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
              <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branches</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View Branches</a></li>
            </ol>
          </div>
          <div class="col-md-6" style="display: flex;">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>"><button class="btn btn-danger btnstyle ml-2">Back</button></a>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="" method="POST">
          <div class="row">
            <div class="col-md-8">
              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
              <div id="accordion">
                <div class="card card-primary">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Classification </a> </h4>
                  </div>
                  <div id="collapseOne" class="collapse show" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <select id="" name="goodsType" class="select2 form-control form-control-border borderColor">
                              <option value="">Branches Type</option>
                              <option value="A">A</option>
                              <option value="B">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <select name="goodsGroup" class="select4 form-control form-control-border borderColor">
                              <option value="">Branches Group</option>
                              <option value="A">A</option>
                              <option value="B">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <select name="purchaseGroup" class="select2 form-control form-control-border borderColor">
                              <option value="">Purchase Group</option>
                              <option value="">A</option>
                              <option value="">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="branh" class="m-input" id="exampleInputBorderWidth2">
                            <label>Branches</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <select name="availabilityCheck" class="select2 form-control form-control-border borderColor">
                              <option value="">Availability Check</option>
                              <option value="Daily">Daily</option>
                              <option value="Weekly">Weekly</option>
                              <option value="By Weekly">By Weekly</option>
                              <option value="Monthly">Monthly</option>
                              <option value="Qtr">Qtr</option>
                              <option value="Half Y">Half Y</option>
                              <option value="Year">Year</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-danger">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseTwo"> Basic Details </a> </h4>
                  </div>
                  <div id="collapseTwo" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="itemCode" class="m-input" id="exampleInputBorderWidth2">
                            <label>Item Code</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="itemName" class="m-input" id="exampleInputBorderWidth2">
                            <label>Item Name</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="netWeight" class="m-input" id="exampleInputBorderWidth2">
                            <label>Net Weight</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="grossWeight" class="m-input" id="exampleInputBorderWidth2">
                            <label>Gross Weight</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Volume :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="volume" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="volume">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">height :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="height" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="height">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">width :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="width" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="width">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">length :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="length" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="length">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Base Unit Of Measure :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="baseUnitMeasure" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="baseUnitOfMeasure">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Issue Unit :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="issueUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="issueUnit">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <textarea type="text" name="itemDesc" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-success">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseThree"> Storage Details </a> </h4>
                  </div>
                  <div id="collapseThree" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Storage Bin :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="storageBin" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Bin">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Picking Area :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="pickingArea" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Picking Area">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Temp Control :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="tempControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Temp Control">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Storage Control :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="storageControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Control">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Max Storage Period :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="maxStoragePeriod" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Max Storage Period">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Time Unit :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="timeUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Time Unit">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Min Remain Self Life :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="minRemainSelfLife" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Min Remain Self Life">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card card-success">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseFour"> Purchase Details </a> </h4>
                  </div>
                  <div id="collapseFour" class="collapse" data-parent="#accordion">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="row">
                            <div class="col-md-6">
                              <label for="" class="form-control borderNone">Purchasing Value Key :</label>
                            </div>
                            <div class="col-md-6">
                              <input type="text" name="purchasingValueKey" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </section>
    <!-- /.content -->
  </div>
<?php
} else { ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- row -->
        <div class="row p-0 m-0">
          <div class="col-12 mt-2 p-0">
            <div class="card card-tabs">
              <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 my-2 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Locations</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary"><i class="fa fa-plus" style="margin-right: 0;"></i></a>
                </li>
              </ul>

              <table class="table defaultDataTable table-hover">
                <thead>
                  <tr>
                    <th>SL.No</th>
                    <th>Location Code</th>
                    <th>Company</th>
                    <th>Branch</th>
                    <th>Location Name</th>
                    <th>Functionalities</th>
                    <th>Status</th>
                    <th>Action </th>
                  </tr>
                </thead>

                <tbody>
                  <?php
                  $sql = "SELECT * FROM `erp_branch_otherslocation` WHERE `branch_id`='" . $_SESSION['logedBranchAdminInfo']['fldAdminBranchId'] . "' AND `othersLocation_status`='active'";
                  $res = $dbCon->query($sql);
                  $i = 1;
                  while ($row = $res->fetch_assoc()) {
                    $functionality = fetchFunctionalitiesNameById($row['companyFunctionalities'])['data'];
                  ?>
                    <tr>
                      <td><?= $i++ ?></td>
                      <td><?= $row['othersLocation_code'] ?></td>
                      <td><?= fetchCompanyNameById($row['company_id'])['data'] ?></td>
                      <td><?= fetchBranchById($row['branch_id'])['data'] ?></td>
                      <td><?= $row['othersLocation_name'] ?></td>
                      <td class="text-capitalize"><?= rtrim($functionality, ',') ?></td>
                      <td>
                        <div class="status-active text-sm"><?= $row['othersLocation_status'] ?></div>
                      </td>
                      <td>
                        <button class="btn btn-sm text-info" data-toggle="modal" data-target="#locationModal_<?= $row['othersLocation_id'] ?>"><i class="fa fa-eye po-list-icon"></i></button>
                        <?php
                        if (isset($_SESSION["logedBranchAdminInfo"]["adminRole"]) && $_SESSION["logedBranchAdminInfo"]["adminRole"] == 1) { ?>
                          <form action="" method="POST" class="btn btn-sm">
                            <input type="hidden" name="fldAdminBranchLocationId" value="<?php echo $row['othersLocation_id'] ?>">
                            <input type="hidden" name="fldAdminBranchId" value="<?php echo $row['branch_id'] ?>">
                            <input type="hidden" name="fldAdminCompanyId" value="<?php echo $row['company_id'] ?>">
                            <input type="hidden" name="visit_location" value="visit_location">
                            <button class="btn btn-sm" title="Visit Location" type="submit" onclick="return confirm('Are you sure to Visit?')" style="cursor: pointer;"><i class="fa fa-share po-list-icon" aria-hidden="true"></i></button>
                          </form>
                        <?php } ?>
                      </td>
                    </tr>
                </tbody>
                <!-- Modal -->
                <div class="modal right fade" id="locationModal_<?= $row['othersLocation_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
                  <div class="modal-dialog" style="max-width: 50%; min-width:50%" role="document">
                    <div class="modal-content">
                      <div class="modal-header bg-light">
                        <h4 class="modal-title" id="myModalLabel2"><?= $row['othersLocation_name'] ?></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      </div>

                      <div class="modal-body">
                        <div class="col-md-12">
                          <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar" style="text-align:right">
                            <?php $locationId = base64_encode($row['othersLocation_id']) ?>
                            <form action="" method="POST">
                              <a href="#" name="vendorEditBtn">
                                <i title="Edit" style="font-size: 1.2em" class="fa fa-edit text-success mx-3"></i>
                              </a>
                              <i title="Delete" style="font-size: 1.2em" class="fa fa-trash text-danger mx-3"></i>
                              <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on text-primary mx-3"></i>
                            </form>
                          </div>
                        </div>
                        <div class="row px-3 p-0 m-0" style="place-items: self-start;">


                          <!-- <div class="col-md-12">
                                  <div class="shadow-sm py-2 px-2" style="background: #dfdfdf; margin-bottom: 15px;">
                                    POC Details
                                  </div>
                                </div> -->
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Location Code: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_code'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Company: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= fetchCompanyNameById($row['company_id'])['data'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Branch: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= fetchBranchById($row['branch_id'])['data'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Functionality: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= fetchFunctionalitiesNameById($row['companyFunctionalities'])['data'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Building No: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_building_no'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Flat No: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_flat_no'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Street Name: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_street_name'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Flat No: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_street_name'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Location: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_location'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">City: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_city'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">District: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_district'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">State: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_state'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Created At: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?php
                                      $date = date_create($row['othersLocation_created_at']);
                                      echo date_format($date, "F j, Y, g:i a");
                                      ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Created By: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_created_by'] ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Updated At: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?php
                                      $date = date_create($row['othersLocation_created_at']);
                                      echo date_format($date, "F j, Y, g:i a");
                                      ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="row m-2 py-2 shadow-sm bg-light">
                              <div class="col-md-6">
                                <span class="font-weight-bold text-secondary">Updated By: </span>
                              </div>
                              <div class="col-md-6">
                                <span><?= $row['othersLocation_updated_by'] ?></span>
                              </div>
                            </div>
                          </div>

                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                          Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus magni nemo, laboriosam id accusamus laborum a cumque. Nemo suscipit commodi adipisci dignissimos, corporis esse alias odit, distinctio, at laudantium ut.
                        </div>
                      </div><!-- modal-content -->
                    </div><!-- modal-dialog -->
                  </div>
                <?php } ?>
              </table>
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
require_once("common/footer.php");
?>
<script>
  $("#selectedBranch").on("change", function() {
    let seletedValue = $(this).val();

    $.ajax({
      url: "ajaxs/ajax-branchLocation.php",
      type: "POST",
      data: {
        branchId: seletedValue
      },
      beforeSend: function() {
        $(".aiBranchDetails1").html(`<p class="h6 text-secondary ">Loading...</h5>`);
      },
      success: function(resp) {
        $(".aiBranchDetails1").html(resp);
      }
    });

  })

  $(".add_data").click(function() {
    var data = this.value;
    $("#createdata").val(data);
    confirm('Are you sure to Submit?')

    $("#add_frm").submit();
  });
  $(".edit_data").click(function() {
    var data = this.value;
    $("#editdata").val(data);
    alert(data);
    //$( "#edit_frm" ).submit();
  });

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