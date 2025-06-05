<?php
require_once("../app/v1/connection-branch-admin.php");
require_once("common/header.php");
require_once("common/navbar.php");
require_once("common/sidebar.php");
require_once("common/pagination.php");
require_once("../app/v1/functions/branch/func-others-location.php");

administratorAuth();

if (isset($_POST["changeStatus"])) {
    $newStatusObj = ChangeStatusBranchLocation($_POST,"othersLocation_id", "othersLocation_status");
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
}


if (isset($_POST["createdata"])) {
    $addNewObj = createDataBranchLocation($_POST);
    swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_POST["editdata"])) {
    $editDataObj = updateDataBranchLocation($_POST);
	
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

if (isset($_POST["add-table-settings"])) { 
	$editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);	
    swalToast($editDataObj["status"], $editDataObj["message"]);   
}

$branch_id=$_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
$company_id=$_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
if(isset($_GET['create'])) {
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper"> 
  <!-- Content Header (Page header) -->
  <div class="content-header mb-2 p-0  border-bottom">
    <?php if(isset($msg)){ ?>
    <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
      <?= $msg ?>
    </div>
    <?php } ?>
    <div class="container-fluid">
      <div class="row pt-2 pb-2">
        <div class="col-md-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branch Location</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add Branch Location</a></li>
          </ol>
        </div>
        <div class="col-md-6" style="display: flex;">
          <button class="btn btn-danger btnstyle ml-2 add_data" value="add_draft" >Save As Draft</button>
          <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header --> 
  
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <form action="<?php $_SERVER['PHP_SELF'];?>" method="POST" id="add_frm" name="add_frm">
        <input type="hidden" name="createdata" id="createdata" value="">
        <input type="hidden" name="company_id" id="company_id" value="<?php echo $company_id;?>">
        <input type="hidden" name="branch_id" id="branch_id" value="<?php echo $branch_id;?>">
        <div class="row">
          <div class="col-md-8">
            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
            <div id="accordion">
              <div class="card card-primary">
                <div class="card-header cardHeader">
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne">Location Basic Details  </a> </h4>
                </div>
                <div id="collapseOne" class="collapse show" data-parent="#accordion">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" class="m-input" id="othersLocation_name" name="othersLocation_name">
                          <label>Location Name</label>
                        <span class="error"></span>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" class="m-input" id="othersLocation_building_no" name="othersLocation_building_no">
                          <label>Building No </label>
                        </div>
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" class="m-input" id="othersLocation_flat_no" name="othersLocation_flat_no">
                          <label>Flat No </label>
                        </div>
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" class="m-input" id="othersLocation_street_name" name="othersLocation_street_name">
                          <label>Street No </label>
                        </div>
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" class="m-input" id="othersLocation_location" name="othersLocation_location">
                          <label>Location </label>
                        </div>
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" class="m-input" id="othersLocation_city" name="othersLocation_city">
                          <label>City </label>
                        </div>
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="number" class="m-input" id="othersLocation_pin_code" name="othersLocation_pin_code">
                          <label>Pin Code </label>
                        </div>
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" class="m-input" id="othersLocation_district" name="othersLocation_district">
                          <label>District </label>
                        </div>
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" class="m-input" id="othersLocation_state" name="othersLocation_state">
                          <label>State </label>
                        </div>
                      </div>


                    </div>
                  </div>
                </div>
              </div>
              <div class="card card-primary">
                <div class="card-header cardHeader">
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseTwo"> Location POC Details </a> </h4>
                </div>
                <div id="collapseTwo" class="collapse" data-parent="#accordion">
                  <div class="card-body">
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <select id="adminRole" name="adminRole" class="form-control form-control-border borderColor">
                            <option value="">Select Role</option>
                            <?php
                            $listResult = getAllAdministratorRoles();
                            if ($listResult["status"] == "success") {
                              foreach ($listResult["data"] as $listRow) {
                                  if ($listRow["fldRoleStatus"] == "active") {
                                      if ($listRow["fldRoleKey"] == $editData["fldAdminRole"]) {
                                          echo '<option value="' . $listRow["fldRoleKey"] . '" selected>' . $listRow["fldRoleName"] . '</option>';
                                      } else {
                                          echo '<option value="' . $listRow["fldRoleKey"] . '">' . $listRow["fldRoleName"] . '</option>';
                                      }
                                  }
                              }
                            }?>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text" name="adminName" class="m-input" id="adminName">
                          <label>Person Name</label>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="email"  name="adminEmail" class="m-input" id="adminEmail">
                          <label>Person Email</label>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text"  name="adminPhone" class="m-input" id="adminPhone">
                          <label>Person Phone</label>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <input type="text"  name="adminPassword" class="m-input" id="adminPassword" value="<?php echo rand(1111,9999);?>">
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
                  <li class="nav-item"> <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
                              href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                              aria-selected="true">Tab 1</a> </li>
                  <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                              href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile"
                              aria-selected="false">Tab 2</a> </li>
                  <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill"
                              href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages"
                              aria-selected="false">Tab 3</a> </li>
                  <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-settings-tab" data-toggle="pill"
                              href="#custom-tabs-three-settings" role="tab" aria-controls="custom-tabs-three-settings"
                              aria-selected="false">Tab 4</a> </li>
                </ul>
              </div>
              <div class="card-body fontSize">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel"
                            aria-labelledby="custom-tabs-three-home-tab"> 90 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin malesuada lacus ullamcorper
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
                  <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel"
                            aria-labelledby="custom-tabs-three-profile-tab"> Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut
                    ligula
                    tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas
                    sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu
                    lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod
                    pellentesque diam. </div>
                  <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel"
                            aria-labelledby="custom-tabs-three-messages-tab"> Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue
                    id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac
                    tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit
                    condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus
                    tristique.
                    Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est
                    libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id
                    fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. </div>
                  <div class="tab-pane fade" id="custom-tabs-three-settings" role="tabpanel"
                            aria-labelledby="custom-tabs-three-settings-tab"> Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis
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
            <div class="w-100 mt-3">
              <button type="submit" name="addInventoryItem" class="gradientBtn btn-success btn btn-block btn-sm"> <i class="fa fa-plus fontSize"></i> Add New </button>
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
                    <option value="">BranchLocation Group</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" name="itemCode" class="m-input"
                      id="exampleInputBorderWidth2">
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
                    <option value="">BranchLocation Group</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" name="itemCode" class="m-input"
                      id="exampleInputBorderWidth2">
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
}else if(isset($_GET['edit']) && $_GET["edit"] > 0) {
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper"> 
  <!-- Content Header (Page header) -->
  <div class="content-header mb-2 p-0  border-bottom">
    <?php if(isset($msg)){ ?>
    <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
      <?= $msg ?>
    </div>
    <?php } ?>
    <div class="container-fluid">
      <div class="row pt-2 pb-2">
        <div class="col-md-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branch Location</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Edit Branch Location</a></li>
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
                            <option value="">BranchLocation Type</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <select name="goodsGroup" class="select4 form-control form-control-border borderColor">
                            <option value="">BranchLocation Group</option>
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
                          <input type="text" name="branh" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>BranchLocation</label>
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
                          <input type="text" name="itemCode" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>Item Code</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" name="itemName" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>Item Name</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" name="netWeight" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>Net Weight</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" name="grossWeight" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>Gross Weight</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Volume :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="volume" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="volume">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">height :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="height" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="height">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">width :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="width" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="width">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">length :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="length" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="length">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Base Unit Of Measure :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="baseUnitMeasure" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="baseUnitOfMeasure">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Issue Unit :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="issueUnit" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="issueUnit">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <textarea type="text" name="itemDesc" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>
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
                            <input type="text" name="storageBin" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Storage Bin">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Picking Area :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="pickingArea" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Picking Area">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Temp Control :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="tempControl" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Temp Control">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Storage Control :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="storageControl" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Storage Control">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Max Storage Period :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="maxStoragePeriod" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Max Storage Period">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Time Unit :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="timeUnit" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Time Unit">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Min Remain Self Life :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="minRemainSelfLife" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Min Remain Self Life">
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
                            <input type="text" name="purchasingValueKey" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">
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
                  <li class="nav-item"> <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill"
                              href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home"
                              aria-selected="true">Home</a> </li>
                  <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill"
                              href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile"
                              aria-selected="false">Profile</a> </li>
                  <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill"
                              href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages"
                              aria-selected="false">Messages</a> </li>
                  <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-settings-tab" data-toggle="pill"
                              href="#custom-tabs-three-settings" role="tab" aria-controls="custom-tabs-three-settings"
                              aria-selected="false">Settings</a> </li>
                </ul>
              </div>
              <div class="card-body fontSize">
                <div class="tab-content" id="custom-tabs-three-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-three-home" role="tabpanel"
                            aria-labelledby="custom-tabs-three-home-tab"> 90 Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin malesuada lacus ullamcorper
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
                  <div class="tab-pane fade" id="custom-tabs-three-profile" role="tabpanel"
                            aria-labelledby="custom-tabs-three-profile-tab"> Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut
                    ligula
                    tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas
                    sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu
                    lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod
                    pellentesque diam. </div>
                  <div class="tab-pane fade" id="custom-tabs-three-messages" role="tabpanel"
                            aria-labelledby="custom-tabs-three-messages-tab"> Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue
                    id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac
                    tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit
                    condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus
                    tristique.
                    Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est
                    libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id
                    fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. </div>
                  <div class="tab-pane fade" id="custom-tabs-three-settings" role="tabpanel"
                            aria-labelledby="custom-tabs-three-settings-tab"> Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis
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
            <div class="w-100 mt-3">
              <button type="submit" name="addInventoryItem" class="gradientBtn btn-success btn btn-block btn-sm"> <i class="fa fa-plus fontSize"></i> Add New </button>
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
                    <option value="">BranchLocation Group</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" name="itemCode" class="m-input"
                      id="exampleInputBorderWidth2">
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
                    <option value="">BranchLocation Group</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="input-group">
                  <input type="text" name="itemCode" class="m-input"
                      id="exampleInputBorderWidth2">
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
}else if(isset($_GET['view']) && $_GET["view"] > 0) {
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper"> 
  <!-- Content Header (Page header) -->
  <div class="content-header mb-2 p-0  border-bottom">
    <?php if(isset($msg)){ ?>
    <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
      <?= $msg ?>
    </div>
    <?php } ?>
    <div class="container-fluid">
      <div class="row pt-2 pb-2">
        <div class="col-md-6">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Branch Location</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View Branch Location</a></li>
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
                            <option value="">BranchLocation Type</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <select name="goodsGroup" class="select4 form-control form-control-border borderColor">
                            <option value="">BranchLocation Group</option>
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
                          <input type="text" name="branh" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>BranchLocation</label>
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
                          <input type="text" name="itemCode" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>Item Code</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" name="itemName" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>Item Name</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" name="netWeight" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>Net Weight</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" name="grossWeight" class="m-input"
                                      id="exampleInputBorderWidth2">
                          <label>Gross Weight</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Volume :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="volume" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="volume">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">height :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="height" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="height">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">width :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="width" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="width">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">length :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="length" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="length">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Base Unit Of Measure :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="baseUnitMeasure" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="baseUnitOfMeasure">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Issue Unit :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="issueUnit" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="issueUnit">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <textarea type="text" name="itemDesc" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>
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
                            <input type="text" name="storageBin" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Storage Bin">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Picking Area :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="pickingArea" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Picking Area">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Temp Control :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="tempControl" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Temp Control">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Storage Control :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="storageControl" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Storage Control">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Max Storage Period :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="maxStoragePeriod" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Max Storage Period">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Time Unit :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="timeUnit" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Time Unit">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Min Remain Self Life :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="minRemainSelfLife" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Min Remain Self Life">
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
                            <input type="text" name="purchasingValueKey" class="form-control form-control-border borderColor"
                                      id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">
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
          <div class="card card-tabs">
            <div class="card-header p-0 pt-1">
              <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Branch Location</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF'])?>?create" class="btn btn-sm btn-primary btnstyle m-2"><i class="fa fa-plus"></i> Add New</a> </li>
              </ul>
            </div>
            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF'];?>" method="get" onsubmit="return srch_frm();" >             
            <div class="card-body">
              <div class="filter-col">
                <div class="row">
                  <div class="col-md-2">
                    <div class="input-group">
                      <select name="othersLocation_status_s" id="othersLocation_status_s" class="form-control form-control-border borderColor">
                        <option value="">--- Status --</option>
                        <option value="active" <?php if(isset($_REQUEST['othersLocation_status_s']) && 'active'==$_REQUEST['othersLocation_status_s']){echo 'selected';} ?>>Active</option>
                        <option value="inactive" <?php if(isset($_REQUEST['othersLocation_status_s']) && 'inactive'==$_REQUEST['othersLocation_status_s']){echo 'selected';} ?>>Inactive</option>
                        <option value="draft" <?php if(isset($_REQUEST['othersLocation_status_s']) && 'draft'==$_REQUEST['othersLocation_status_s']){echo 'selected';} ?>>Draft</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="input-group"> <input class="fld" type="date" name="form_date_s" id="form_date_s" value="<?php if(isset($_REQUEST['form_date_s'])){ echo $_REQUEST['form_date_s'];} ?>" />
                      </div>
                      </div>
                    
                  <div class="col-md-2">
                    <div class="input-group"> <input  class="fld" type="date" name="to_date_s" id="to_date_s"  value="<?php if(isset($_REQUEST['to_date_s'])){ echo $_REQUEST['to_date_s'];} ?>" />
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="input-group">
                      <input type="text" name="keyword" class="m-input" id="keyword" placeholder="Enter Keyword" value="<?php if(isset($_REQUEST['keyword'])){ echo $_REQUEST['keyword']; }?>">
                      <!--<label>Keyword</label>-->
                    </div>
                  </div>
                  <div class="col-md-3" style="display: flex;">
                    <button type="submit" class="btn btn-primary btnstyle">Search</button> &nbsp;
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-danger btnstyle">Reset</a>
                  </div>
                </div>
              </div>
              </form>
              <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;z-index:999;"> <i class="fa fa-cog" aria-hidden="true"></i></a>
              <div class="tab-content" id="custom-tabs-two-tabContent">
                <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                <?php 
				$cond='';
				
				$sts=" AND `othersLocation_status` !='deleted'";	
				if(isset($_REQUEST['othersLocation_status_s']) && $_REQUEST['othersLocation_status_s']!=''){
					$sts=' AND othersLocation_status="'.$_REQUEST['othersLocation_status_s'].'"';
				}
				
				if(isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s']!=''){
					$cond.=" AND othersLocation_created_at between '".$_REQUEST['form_date_s']." 00:00:00' AND '".$_REQUEST['to_date_s']." 23:59:59'";
				}
				
				if(isset($_REQUEST['keyword']) && $_REQUEST['keyword']!=''){
					$cond.=" AND (`othersLocation_code` like '%".$_REQUEST['keyword']."%' OR `othersLocation_name` like '%".$_REQUEST['keyword']."%' OR `othersLocation_building_no` like '%".$_REQUEST['keyword']."%')";
				}	
				
                $sql_list = "SELECT * FROM `".ERP_BRANCH_OTHERSLOCATION."` WHERE 1 ".$cond." AND company_id='".$company_id."' AND branch_id='".$branch_id."' ".$sts."  ORDER BY othersLocation_id desc limit ".$GLOBALS['start'].",".$GLOBALS['show']." ";
                $qry_list = mysqli_query($dbCon,$sql_list);
                $num_list = mysqli_num_rows($qry_list);
                
                
                $countShow="SELECT count(*) FROM `".ERP_BRANCH_OTHERSLOCATION."` WHERE 1 ".$cond." AND company_id='".$company_id."' AND branch_id='".$branch_id."' ".$sts." ";
                $countQry=mysqli_query($dbCon,$countShow);
                $rowCount=mysqli_fetch_array($countQry);
                $count=$rowCount[0];
                $cnt=$GLOBALS['start']+1;                   
                $settingsTable=getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS,"ERP_BRANCH_OTHERSLOCATION",$_SESSION["logedBranchAdminInfo"]["adminId"]);
                $settingsCh=($settingsTable['data'][0]['settingsCheckbox']);
                $settingsCheckbox = unserialize($settingsCh);
                if($num_list>0){
                ?>
                  <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                    <thead>
                      <tr>
                        <th>#</th>
                        <?php if(in_array(1,$settingsCheckbox)){ ?>
                        <th>Branch Location Code</th>
                        <?php }if(in_array(2,$settingsCheckbox)){ ?>
                        <th>Branch Location Name</th>
                        <?php }if(in_array(3,$settingsCheckbox)){ ?>
                        <th>GSTIN Number</th>
                        <?php }if(in_array(4,$settingsCheckbox)){ ?>
                        <th>Currency</th>
                        <?php }if(in_array(5,$settingsCheckbox)){ ?>
                        <th>Language</th>
                        <?php } ?>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                    while($row=mysqli_fetch_assoc($qry_list)){
                ?>
                      <tr>
                        <td><?=$cnt++?></td>
                        <?php if(in_array(1,$settingsCheckbox)){ ?>
                        <td><?= $row['othersLocation_code'] ?></td>
                        <?php }if(in_array(2,$settingsCheckbox)){ ?>
                        <td><?= $row['othersLocation_name'] ?></td>
                        <?php }if(in_array(3,$settingsCheckbox)){ ?>
                        <td><?= $row['othersLocation_building_no'] ?></td>
                        <?php }if(in_array(4,$settingsCheckbox)){ ?>
                        <td><?= $row['othersLocation_flat_no'] ?></td>
                        <?php }if(in_array(5,$settingsCheckbox)){ ?>
                        <td><?= $row['othersLocation_street_name'] ?></td>                  
                        <?php }?>
                        <td>
						 <form action="<?php $_SERVER['PHP_SELF'];?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['othersLocation_id'] ?>">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button <?php if($row['othersLocation_status'] == "draft"){?> type="button"  style="cursor: inherit; border:none" <?php }else{?>type="submit" onclick="return confirm('Are you sure change othersLocation_status?')"  style="cursor: pointer; border:none"<?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['othersLocation_status'] ?>">
                                    <?php if($row['othersLocation_status'] == "active"){ ?>
                                    <span class="badge badge-success"><?php echo ucfirst($row['othersLocation_status']);?></span>
                                    <?php }else if($row['othersLocation_status'] == "inactive"){?>
                                    <span class="badge badge-danger"><?php echo ucfirst($row['othersLocation_status']);?></span>
                                    <?php }else if($row['othersLocation_status'] == "draft"){?>
                                    <span class="badge badge-warning"><?php echo ucfirst($row['othersLocation_status']);?></span>
                                    
                                    <?php }?>
                                    
                                </button>
                            </form>
                        </td> 
                        <td >
                          
                          <a href="<?= basename($_SERVER['PHP_SELF']) . "?view=" . $row['othersLocation_id']; ?>" style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-eye"></i></a>
                            <a href="<?= basename($_SERVER['PHP_SELF']) . "?edit=" . $row['othersLocation_id']; ?>" style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-edit"></i></a>
                            <form action="" method="POST" class="btn btn-sm">
                                <input type="hidden" name="id" value="<?php echo $row['othersLocation_id'] ?>">
                                <input type="hidden" name="changeStatus" value="delete">
                                <button type="submit" onclick="return confirm('Are you sure to delete?')" style="cursor: pointer; border:none"><i class='fa fa-trash '></i></button>
                            </form>
                          </td>
                      </tr>
                      <?php  } ?>
                    <tfoot>
                      <tr>
                        <td colspan="8"><!-- Start .pagination -->
                          
                          <?php   
								if($count>0 && $count > $GLOBALS['show'])	
								{
							?>
                          <div class="pagination align-right">
                            <?php pagination($count,"frm_opts");?>
                          </div>
                          
                          <!-- End .pagination -->
                          
                          <?php  } ?>
                          
                          <!-- End .pagination --></td>
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
                      <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF'];?>" onsubmit="return table_settings();" >
                      <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS;?>" />
                     <input type="hidden" name="pageTableName" value="ERP_BRANCH_OTHERSLOCATION" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px" ><input type="checkbox" <?php echo (in_array(1,$settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                              Branch Location Code</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px" ><input type="checkbox" <?php echo (in_array(2,$settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                              Branch Location Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px" ><input type="checkbox" <?php echo (in_array(3,$settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                GSTIN</td>
                            </tr>
                           
                            <tr>
                              <td valign="top" style="width: 165px" ><input type="checkbox" <?php echo (in_array(4,$settingsCheckbox) ? 'checked="checked"' : ''); ?>  name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />
                              Currency</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px" ><input type="checkbox" <?php echo (in_array(5,$settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />
                              Language</td>
                            </tr>
                          </table>
                        </div>
                      </div>
                      
                      <div class="modal-footer">
                        <button  type="submit" name="add-table-settings" class="btn btn-success">Save</button>
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
<form name="frm_opts" action="<?= $_SERVER['REQUEST_URI']; ?>" method="post" >
  <input type="hidden" name="pageNo" value="<?php if(isset($_REQUEST['pageNo'])) { echo  $_REQUEST['pageNo']; }?>">
</form>
<!-- End Pegination from------->

<?php
}
require_once("common/footer.php");
?>
<script>
 

$( ".add_data" ).click(function() {
  var data=this.value;
  $("#createdata").val(data); 
  //confirm('Are you sure to Submit?')
  $("#add_frm" ).submit();
});
$( ".edit_data" ).click(function() {
  var data=this.value;
  $("#editdata").val(data);
  alert(data);
  //$( "#edit_frm" ).submit();
});


function srch_frm(){
	if($('#form_date_s').val().trim()!='' && $('#to_date_s').val().trim()==='')
	  { //$("#phone_r_err").css('display','block');
		//$("#phone_r_err").html("Your Phone Number");
		alert("Enter To Date");
		$('#to_date_s').focus();
		return false;
	}	
	if($('#to_date_s').val().trim()!='' && $('#form_date_s').val().trim()==='')
	  { //$("#phone_r_err").css('display','block');
		//$("#phone_r_err").html("Your Phone Number");
		alert("Enter From Date");
		$('#form_date_s').focus();
		return false;
	}
			
}

function table_settings(){
	var favorite = [];
	$.each($("input[name='settingsCheckbox[]']:checked"), function(){
		favorite.push($(this).val());
	});
	var check = favorite.length;
	if(check<5)
	  {
		alert("Please Check Atlast 5");
		return false;
		}	
			
}


    $(document).ready(function () {
		
		
		
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
wow = new WOW(
                      {
                      boxClass:     'wow',      // default
                      animateClass: 'animated', // default
                      offset:       0,          // default
                      mobile:       true,       // default
                      live:         true        // default
                    }
                    )
                    wow.init();
  </script>