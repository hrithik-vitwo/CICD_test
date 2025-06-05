<?php
include("../app/v1/connection-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");
require_once("common/pagination.php");
include("../app/v1/functions/admin/func-packages.php");


if (isset($_POST["changeStatus"])) {
  $newStatusObj = ChangeStatusBranches($_POST, "branch_id", "branch_status");
  swalToast($newStatusObj["status"], $newStatusObj["message"],);
}

if (isset($_POST["visit"])) {
  $newStatusObj = VisitBranches($_POST);
  if ($newStatusObj["status"] == "success") {
    redirect(BRANCH_URL);
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  } else {
    swalToast($newStatusObj["status"], $newStatusObj["message"]);
  }
}


if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedCompanyAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

// submit package management btn 
if (isset($_POST["addPackageSubmitBtn"])) {
  // console($_POST);
  $addPackage = insertPackage($_POST);
  swalToast($addPackage["status"], $addPackage["message"]);
  // console($addPackage);
}



?>
<link rel="stylesheet" href="../public/assets/listing.css">
<link rel="stylesheet" href="../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/accordion.css">
<style>
  .packages-modal .modal-header {

    min-height: 300px;

  }
</style>
<?php
if (isset($_GET['create'])) {
?>



  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= COMPANY_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Packages</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Add Packages</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>


        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
              <input type="hidden" name="createdata" id="createdata" value="">
              <input type="hidden" name="fldAdminCompanyId" id="fldAdminCompanyId" value="<?= $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"]; ?>">

              <div class="row">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Packages Details</h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Package Name</label>

                                <input type="text" class="form-control" id="packageName" name="packageDetails[packageName]">

                                <span class="error packageName"></span>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Duration</label>

                                <input type="text" name="packageDetails[duration]" class="form-control" id="duration" value="">

                                <span class="error duration"></span>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Base Price</label>

                                <input type="text" name="packageDetails[basePrice]" class="form-control" id="basePrice" value="">

                                <span class="error basePrice"></span>

                              </div>

                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Description</label>

                                <textarea type="text" name="packageDetails[description]" class="form-control" id="description"></textarea>

                                <span class="error description"></span>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Variant Details</h4>

                    </div>

                    <!-- ***************************************** -->
                    <div class="card p-3 items-select-table" id="otherCostCard">
                      <div class="row p-0 m-0">
                        <div class="col-md-12">
                          <div class="row">
                            <div class="col-md-3">
                              <div class="form-input">
                                <label for="">Variant Name</label>
                                <input type="text" name="variantDetails[12345][variantName]" class="form-control" id="variantName">
                              </div>
                            </div>
                            <input type="hidden" name="variantDetails[12345][isPrimary]" value="1" class="form-control" id="isPrimary">
                            <div class="col-md-2">
                              <div class="form-input">
                                <label for="">Price</label>

                                <input type="number" name="variantDetails[12345][price]" class="form-control" id="price">
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-input">
                                <label for="">Transaction</label>

                                <input type="number" name="variantDetails[12345][transaction]" class="form-control" id="transaction">
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-input">
                                <label for="">OCR</label>

                                <input type="number" name="variantDetails[12345][OCR]" class="form-control" id="OCR">
                              </div>
                            </div>
                            <div class="col-md-1">
                              <div class="add-btn-plus">
                                <a style="cursor: pointer" class="btn btn-primary" onclick="addOtherCostCardRow()">
                                  <i class="fa fa-plus"></i>
                                </a>
                              </div>
                            </div>
                          </div>


                        </div>
                      </div>
                    </div>
                    <!-- ***************************************** -->
                  </div>
                </div>
                <div class="btn-section mt-2 mb-2 ml-auto">
                  <button type="submit" class="btn btn-primary save-close-btn float-right add_data waves-effect waves-light" name="addPackageSubmitBtn">Submit</button>
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
                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
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
                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
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
                                <input type="text" name="branh" class="form-control" id="exampleInputBorderWidth2">
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
                                <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                                <label>Item Code</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="itemName" class="form-control" id="exampleInputBorderWidth2">
                                <label>Item Name</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="netWeight" class="form-control" id="exampleInputBorderWidth2">
                                <label>Net Weight</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="grossWeight" class="form-control" id="exampleInputBorderWidth2">
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
                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
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
                        <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
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
                                <input type="text" name="branh" class="form-control" id="exampleInputBorderWidth2">
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
                                <input type="text" name="itemCode" class="form-control" id="exampleInputBorderWidth2">
                                <label>Item Code</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="itemName" class="form-control" id="exampleInputBorderWidth2">
                                <label>Item Name</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="netWeight" class="form-control" id="exampleInputBorderWidth2">
                                <label>Net Weight</label>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="input-group">
                                <input type="text" name="grossWeight" class="form-control" id="exampleInputBorderWidth2">
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

                <ul class="nav nav-tabs border-0 mb-3" id="custom-tabs-two-tab" role="tablist">
                  <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                    <h3 class="card-title">Manage Packages</h3>
                    <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>
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

                              <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                            </div>

                          </div>



                        </div>

                      </div>

                    </div>
                  </form>
                  <div class="tab-content" id="custom-tabs-two-tabContent">
                    <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">
                      <?php
                      $cond = '';

                      $sts = " AND `branch_status` !='deleted'";
                      if (isset($_REQUEST['branch_status_s']) && $_REQUEST['branch_status_s'] != '') {
                        $sts = ' AND branch_status="' . $_REQUEST['branch_status_s'] . '"';
                      }

                      if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {
                        $cond .= " AND branch_created_at between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                      }

                      if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {
                        $cond .= " AND (`branch_cin` like '%" . $_REQUEST['keyword'] . "%' OR `branch_name` like '%" . $_REQUEST['keyword'] . "%' OR `branch_gstin` like '%" . $_REQUEST['keyword'] . "%')";
                      }

                      $sql_list = "SELECT * FROM `" . ERP_PACKAGE_MANAGEMENT . "` WHERE status != 'deleted' limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                      $qry_list = mysqli_query($dbCon, $sql_list);
                      $num_list = mysqli_num_rows($qry_list);


                      $countShow = "SELECT count(*) FROM `" . ERP_BRANCHES . "` WHERE 1 " . $cond . " AND company_id='" . $_SESSION["logedCompanyAdminInfo"]["fldAdminCompanyId"] . "' " . $sts . " ";
                      $countQry = mysqli_query($dbCon, $countShow);
                      $rowCount = mysqli_fetch_array($countQry);
                      $count = $rowCount[0];
                      $cnt = $GLOBALS['start'] + 1;
                      $settingsTable = getTableSettings(TBL_COMPANY_ADMIN_TABLESETTINGS, "ERP_BRANCHES", $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                      $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                      $settingsCheckbox = unserialize($settingsCh);
                      if ($num_list > 0) {
                      ?>
                        <table id="mytable" class="table defaultDataTable table-hover text-nowrap">
                          <thead>
                            <tr>
                              <th>#</th>
                              <?php if (in_array(1, $settingsCheckbox)) { ?>
                                <th>Name</th>
                              <?php }
                              if (in_array(2, $settingsCheckbox)) { ?>
                                <th>Duration (in days)</th>
                              <?php }
                              if (in_array(3, $settingsCheckbox)) { ?>
                                <th>Base Price <small>/ 30D</small></th>
                              <?php  }
                              if (in_array(4, $settingsCheckbox)) { ?>
                                <th>Package Value</th>
                              <?php } 
                              if (in_array(5, $settingsCheckbox)) { ?>
                                <th>Created At</th>
                              <?php } ?>
                              <th>Status</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($qry_list)) {
                              $packageValue = ($row['packageDuration'] / 30) * ($row['packageBasePrice']);
                            ?>
                              <tr>
                                <td><?= $cnt++ ?></td>
                                <?php if (in_array(1, $settingsCheckbox)) { ?>
                                  <td><?= $row['packageTitle'] ?></td>
                                <?php }
                                if (in_array(2, $settingsCheckbox)) { ?>
                                  <td><?= $row['packageDuration'] ?></td>
                                <?php }
                                if (in_array(3, $settingsCheckbox)) { ?>
                                  <td><span style="font-family: 'Source Sans Pro'">₹</span><?= number_format($row['packageBasePrice'], 2) ?></td>
                                <?php }
                                if (in_array(5, $settingsCheckbox)) { ?>
                                  <td><span style="font-family: 'Source Sans Pro'">₹</span><?= number_format($packageValue, 2) ?></td>
                                <?php } 
                                if (in_array(4, $settingsCheckbox)) { ?>
                                  <td><?= $row['created_at'] ?></td>
                                <?php } ?>
                                <td>
                                  <input type="hidden" name="packageId" value="<?php echo $row['packageId'] ?>">
                                  <?php if ($row['status'] == "active") { ?>
                                    <p class="status"><?php echo ucfirst($row['status']); ?></p>
                                  <?php } else if ($row['status'] == "inactive") { ?>
                                    <p class="status-danger"><?php echo ucfirst($row['status']); ?></p>
                                  <?php } ?>
                                  </button>
                                </td>
                                <td>
                                  <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemoBranch_<?= $row['packageId'] ?>" class="btn btn-sm">
                                    <i class="fa fa-eye po-list-icon"></i>
                                  </a>
                                </td>
                              </tr>
                              <!-- right modal start here  -->

                              <div class="modal fade right packages-modal customer-modal" id="fluidModalRightSuccessDemoBranch_<?= $row['packageId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                  <!--Content-->
                                  <?php $getVariantDetails = fetchVariantDetails($row['packageId'])['data']; ?>
                                  <div class="modal-content">

                                    <!--Header-->

                                    <div class="modal-header pt-4">

                                      <div class="row">

                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                          <div class="item-img">

                                            <h2 class="text-light"><?= $row['packageTitle'] ?></h2>

                                          </div>

                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6">

                                          <p class="heading lead text-sm text-right mt-2 mb-2">Duration : <?= $row['packageDuration'] ?></p>

                                          <p class="text-sm mt-2 mb-2 text-right">Base Price : <?= $row['packageBasePrice'] ?></p>

                                        </div>

                                      </div>



                                      <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                        <?php $itemId = base64_encode($row['itemId']) ?>

                                        <form action="" method="POST">

                                          <a href="#" name="customerEditBtn">

                                            <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                          </a>

                                          <a href="#">

                                            <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                          </a>

                                          <a href="#">

                                            <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                          </a>

                                        </form>

                                      </div>

                                    </div>

                                    <!--Body-->

                                    <div class="modal-body" style="padding: 0;">
                                      <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">

                                          <div class="row px-3">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                              <div class="btn btn-light">Variant Details</div>
                                              <?php foreach ($getVariantDetails as $one) { ?>
                                                <div class="card">
                                                  <div class="card-header text-light"><?= $one['variantTitle'] ?></div>
                                                  <div class="card-body p-3">
                                                    <p><?php if ($one['isPrimary'] == "1") {
                                                          echo "<span>Primary</span>";
                                                        } ?></p>
                                                    <p>Price: <span style="font-family: 'Source Sans Pro'">₹</span><?= $one['variantPrice'] ?></p>
                                                    <p>Transaction: <?= $one['transaction'] ?></p>
                                                    <p>OCR: <?= $one['OCR'] ?></p>
                                                  </div>
                                                </div>
                                              <?php } ?>
                                            </div>
                                          </div>

                                        </div>

                                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                          <?= $itemName ?>

                                        </div>

                                      </div>

                                    </div>

                                  </div>

                                  <!--/.Content-->

                                </div>

                              </div>

                              <!-- right modal end here  -->
                            <?php  } ?>
                          </tbody>
                          <tbody>
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
                          <input type="hidden" name="pageTableName" value="ERP_BRANCHES" />
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
                                    Duration</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                    Base Price</td>
                                </tr>
                                <tr>
                                  <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox10" value="4" />
                                    Created At</td>
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
  let otherCostCardRow = 1;

  function removeOtherCostCardRow(rowId) {
    $(`#otherCostCardRow_${rowId}`).remove();
  }

  function addOtherCostCardRow() {
    otherCostCardRow++;
    let addressRandNo = Math.ceil(Math.random() * 100000);
    let html = `<div class="row p-0 m-0 mt-4" id="otherCostCardRow_${otherCostCardRow}">
                <div class="col-md-12">
                    <div class="row othe-cost-infor">

                      <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="form-input">
                          <label for="">Variant Name</label>
                          <input type="text" name="variantDetails[${addressRandNo}][variantName]" class="form-control" id="variantName">
                        </div>
                      </div>

                      <input type='hidden' name="variantDetails[${addressRandNo}][isPrimary]" value="0" class="form-control" id="isPrimary">

                      <div class="col-lg-2 col-md-2 col-sm-2">
                        <div class="form-input">
                          <label for="">Price</label>
                          <input type="number" name="variantDetails[${addressRandNo}][price]" class="form-control" id="price">
                        </div>
                      </div>

                      <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="form-input">
                          <label for="">Transaction</label>
                          <input type="number" name="variantDetails[${addressRandNo}][transaction]" class="form-control" id="transaction">
                        </div>
                      </div>

                      <div class="col-lg-3 col-md-3 col-sm-3">
                        <div class="form-input">
                          <label for="">OCR</label>
                          <input type="number" name="variantDetails[${addressRandNo}][OCR]" class="form-control" id="OCR">
                        </div>
                      </div>

                      <div class="col-md-1">
                        <div class="add-btn-plus">
                          <a style="cursor: pointer" class="btn btn-danger" onclick="removeOtherCostCardRow(${otherCostCardRow})">
                            <i class="fa fa-minus"></i>
                          </a>
                        </div>
                      </div>
                    </div>
                    </div>
                  </div>`;

    $(`#otherCostCard`).append(html);
    console.log(`${id} = ${html}`);
  }

  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });

  $('.form-control').on('keyup', function() {
    $(this).parent().children('.error').hide()
  });
  $(".add_data").click(function() {
    var data = this.value;
    $("#createdata").val(data);
    let flag = 1;
    if (data == 'add_post') {
      if ($("#branch_gstin").val() == "") {
        $(".branch_gstin").show();
        $(".branch_gstin").html("GSTIN  is requried.");
        flag++;
      } else {
        $(".branch_gstin").hide();
        $(".branch_gstin").html("");
      }
      if ($("#branch_name").val() == "") {
        $(".branch_name").show();
        $(".branch_name").html(" Trade name is requried.");
        flag++;
      } else {
        $(".branch_name").hide();
        $(".branch_name").html("");
      }
      if ($("#con_business").val() == "") {
        $(".con_business").show();
        $(".con_business").html("Constitution of Business is requried.");
        flag++;
      } else {
        $(".con_business").hide();
        $(".con_business").html("");
      }
      if ($("#build_no").val() == "") {
        $(".build_no").show();
        $(".build_no").html("Build number is requried.");
        flag++;
      } else {
        $(".build_no").hide();
        $(".build_no").html("");
      }
      if ($("#flat_no").val() == "") {
        $(".flat_no").show();
        $(".flat_no").html("Flat number is requried.");
        flag++;
      } else {
        $(".flat_no").hide();
        $(".flat_no").html("");
      }
      if ($("#street_name").val() == "") {
        $(".street_name").show();
        $(".street_name").html(" is requried.");
        flag++;
      } else {
        $(".street_name").hide();
        $(".street_name").html("");
      }
      if ($("#pincode").val() == "") {
        $(".pincode").show();
        $(".pincode").html("pincode is requried.");
        flag++;
      } else {
        $(".pincode").hide();
        $(".pincode").html("");
      }
      if ($("#location").val() == "") {
        $(".location").show();
        $(".location").html("location is requried.");
        flag++;
      } else {
        $(".location").hide();
        $(".location").html("");
      }
      if ($("#city").val() == "") {
        $(".city").show();
        $(".city").html("city is requried.");
        flag++;
      } else {
        $(".city").hide();
        $(".city").html("");
      }
      if ($("#district").val() == "") {
        $(".district").show();
        $(".district").html("district is requried.");
        flag++;
      } else {
        $(".district").hide();
        $(".district").html("");
      }
      if ($("#state").val() == "") {
        $(".state").show();
        $(".state").html("state is requried.");
        flag++;
      } else {
        $(".state").hide();
        $(".state").html("");
      }
      if ($("#adminName").val() == "") {
        $(".adminName").show();
        $(".adminName").html("username is requried.");
        flag++;
      } else {
        $(".adminName").hide();
        $(".adminName").html("");
      }
      var Regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
      if ($("#adminEmail").val() == "") {
        $(".adminEmail").show();
        $(".adminEmail").html("Email is requried.");
        flag++;
      } else {
        if ($("#adminEmail").val().match(Regex)) {
          console.log($("#adminEmail").val())
          $(".adminEmail").show();
          $(".adminEmail").html("");
          flag++;
        } else {
          console.log("1")
          $(".adminEmail").show();
          $(".adminEmail").html("Enter a valid email.");
        }
      }
      if ($("#adminPhone").val() == "") {
        $(".adminPhone").show();
        $(".adminPhone").html("Phone number is requried.");
        flag++;
      } else {
        $(".adminPhone").hide();
        $(".adminPhone").html("");
      }
      if ($("#adminPassword").val() == "") {
        $(".adminPassword").show();
        $(".adminPassword").html("Password is requried.");
        flag++;
      } else {
        $(".adminPassword").hide();
        $(".adminPassword").html("");
      }
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

  var inputs = document.getElementsByClassName("form-control");
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