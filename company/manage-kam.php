<?php

include("../app/v1/connection-company-admin.php");
administratorAuth();
include("common/header.php");
include("common/navbar.php");
include("common/sidebar.php");

require_once("common/pagination.php");
require_once("../app/v1/functions/company/func-kam-controller.php");


//console($_SESSION);
//console($_SESSION['logedBranchAdminInfo']['fldAdminBranchId']);
//console(date("Y-m-d H:i:s"));
$kamController = new KAMController();

// if (isset($_POST["changeStatus"])) {
//   $newStatusObj = ChangeStatus($_POST, "fldAdminKey", "fldAdminStatus");
//   swalToast($newStatusObj["status"], $newStatusObj["message"]);  
// }


// if (isset($_POST["create"])) {
//   $addNewObj = createData($_POST + $_FILES);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

// if (isset($_POST["edit"])) { 
//   $editDataObj = updateData($_POST);

//   swalToast($editDataObj["status"], $editDataObj["message"]);
// }

if (isset($_POST["createKam"])) {


  $addNewObj = $kamController->createKam($_POST, $company_id, $created_by);

  swalToast($addNewObj["status"], $addNewObj["message"], BASE_URL . "company/manage-kam.php");
}
if (isset($_POST["editKam"])) {


  $addNewObj = $kamController->editKam($_POST, $company_id, $created_by);
  swalToast($addNewObj["status"], $addNewObj["message"], BASE_URL . "company/manage-kam.php");
}


// if (isset($_POST["editgoodsdata"])) {
//   $addNewObj = $kamController->editGoods($_POST);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["itemId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>

<link rel="stylesheet" href="../../public/assets/listing.css">


<?php
if (isset($_GET['create'])) {
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
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage KAM</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Add KAM</a></li>
            </ol>
          </div>
          <div class="col-md-6" style="display: flex;">
            <button class="btn btn-danger btnstyle ml-2 add_data" value="add_draft">Save As Draft</button>
            <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i class="fa fa-plus fontSize"></i> Final Submit</button>
          </div>
          <script>
            // $(document).ready(function(){
            //   $("#warehouseSubmitFormSaveBtn").click(function(){
            //     $("#warehouseSubmitForm").submit();
            //   });
            // });
          </script>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <!-- <section class="content">
      <div class="container-fluid">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="warehouseSubmitForm" name="warehouseSubmitForm">
          <input type="hidden" name="createKam" id="createKam" value="">
          <div class="row">
            <div class="col-md-8">
              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
              <div id="accordion">
                <div class="card card-success">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseThree"> KAM Details </a> </h4>
                    <?php
                    echo $location_id;
                    echo $company_id;
                    echo $branch_id;
                    ?>
                  </div>
                  <div id="collapseThree">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="name" class="m-input">
                            <label>KAM Name</label>
                          </div>
                        </div>
                        <?php
                        $kam = queryGet("SELECT * FROM `" . ERP_KAM . "` WHERE `company_id`=$company_id", TRUE);
                        $row = $kam["data"];
                        ?>
                        <div class="col-md-6">
                          <div class="input-group">
                            <select name="p_id" class="form-control form-control-border borderColor">
                              <option value="">KAM Parent</option>
                              <?php foreach ($row as $row) { ?>
                                <option value="<?= $row['kamId'] ?>"><?= $row['kamName'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="contact" class="m-input">
                            <label>Contact Number</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="email" class="m-input">
                            <label>Email</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="emp_code" class="m-input">
                            <label>Employee Code</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="designation" class="m-input">
                            <label>Designation </label>
                          </div>
                        </div>

                        <div class="col-md-12">
                          <div class="input-group">
                            <input type="text" name="description" class="m-input">
                            <label>KAM Description</label>
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
    </section> -->
  </div>
  </form>
  </div>
  </section>
  <!-- /.content -->
  </div>
<?php
} else if (isset($_GET['edit'])) {

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
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Goods</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Edit Goods</a></li>
            </ol>
          </div>
          <!-- <div class="col-md-6" style="display: flex;">
            <button class="btn btn-danger btnstyle ml-2 edit_data" value="edit_draft">Draft</button>
            <button class="btn btn-primary btnstyle gradientBtn ml-2 edit_data" value="edit_post"><i class="fa fa-plus fontSize"></i> Save</button>
          </div> -->
        </div>
      </div>
    </div>
    <!-- /.content-header -->






    <!-- <section class="content">
      <div class="container-fluid">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="edit_kam" name="edit_kam">
          <input type="hidden" name="editKam" id="editKam" value="">
          <input type="hidden" name="kam_id" value="<?= $row['kamId'] ?>">
          <div class="row">
            <div class="col-md-8">
              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>
              <div id="accordion">


                <div class="card card-success">

                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseThree"> KAM Details </a> </h4>
                  </div>
                  <div id="collapseThree">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="name" class="m-input" value="<?= $row['kamName'] ?>">
                            <label>KAM Name</label>
                          </div>
                        </div>
                        <?php
                        $kam = queryGet("SELECT * FROM `" . ERP_KAM . "` WHERE `company_id`=$company_id ", TRUE);
                        $row = $kam["data"];


                        ?>
                        <div class="col-md-6">
                          <div class="input-group">
                            <select name="p_id" class="form-control form-control-border borderColor">
                              <option value="">KAM Parent</option>
                              <?php foreach ($row as $row) { ?>
                                <option value="<?= $row['kamId'] ?>"><?= $row['kamName'] ?></option>
                              <?php } ?>

                            </select>
                          </div>
                        </div>


                        <div class="col-md-12">
                          <div class="input-group">
                            <input type="text" name="description" class="m-input" value="<?= $row['description'] ?>">
                            <label>KAM Description</label>
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
    </section> -->



  </div>
  </form>

  </div>
  </section>

  <!-- Main content -->

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
              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Goods</a></li>
              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View Goods</a></li>
            </ol>
          </div>
          <div class="col-md-6" style="display: flex;">
            <button class="btn btn-primary btnstyle gradientBtn ml-2"><i class="fa fa-plus fontSize"></i> Back</button>
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
                            <select id="goodsType" name="goodsType" class="select2 form-control form-control-border borderColor">
                              <option value="" data-goodType="">Goods Type</option>
                              <option value="A">A</option>
                              <option value="B">B</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <select name="goodsGroup" class="select4 form-control form-control-border borderColor">
                              <option value="">Goods Group</option>
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
                            <label>Branch</label>
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
} else if (isset($_GET["bom"]) && base64_decode($_GET["bom"]) > 0) {
  require_once("components/goods/create-bom.php");
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
            <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage KAM</h3>
                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-primary" data-toggle="modal" data-target="#kamCreateForm"><i class="fa fa-plus"></i> Add New</a>
                </li>
              </ul>
            </div>


            <div id="kamCreateForm" class="modal kam-create-modal">

              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    Create KAM
                  </div>
                  <div class="modal-body">
                    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="warehouseSubmitForm" name="warehouseSubmitForm">
                      <input type="hidden" name="createKam" id="createKam" value="">

                      <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <div class="form-input my-2">
                            <label for="">Kam Name</label>
                            <input type="text" name="name" class="form-control">
                          </div>
                        </div>
                        <?php
                        $kam = queryGet("SELECT * FROM `" . ERP_KAM . "` WHERE `company_id`=$company_id", TRUE);
                        // console($kam);
                        $row = $kam["data"];
                        // console($row);


                        ?>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <div class="form-input my-2">
                            <label for="">Kam Parent</label>
                            <select class="form-control" name="p_id" id="">
                              <option value="">KAM Parent</option>
                              <?php foreach ($row as $row) { ?>
                                <option value="<?= $row['kamId'] ?>"><?= $row['kamName'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <div class="form-input my-2">
                            <label for="">Employee Code</label>
                            <input type="text" name="emp_code" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <div class="form-input my-2">
                            <label for="">Designation</label>
                            <input type="text" name="designation" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <div class="form-input my-2">
                            <label for="">Email</label>
                            <input type="text" name="email" class="form-control">
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                          <div class="form-input my-2">
                            <label for="">Contact</label>
                            <input type="text" name="contact" class="form-control">
                          </div>
                        </div>

                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="form-input my-2">
                            <label for="">Kam Description</label>
                            <textarea name="description" id="" cols="30" rows="5" class="form-control"></textarea>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-danger add_data" value="add_data">Cancel</button>
                    <button class="btn btn-primary add_data" value="add_post">Submit</button>
                  </div>
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

                  $sql_list = "SELECT * FROM " . ERP_KAM . " WHERE 1 " . $cond . " " . $sts . " AND `company_id`=$company_id  ORDER BY kamId desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
                  $qry_list = mysqli_query($dbCon, $sql_list);
                  $num_list = mysqli_num_rows($qry_list);


                  $countShow = "SELECT count(*) FROM " . ERP_KAM . " WHERE 1 " . $cond . " AND `company_id`=$company_id ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedCompanyAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($num_list > 0) { ?>
                    <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                      <thead>
                        <tr class="alert-light">
                          <th>#</th>
                          <?php if (in_array(1, $settingsCheckbox)) { ?>
                            <th>KAM Name</th>
                          <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                            <th>KAM Code</th>
                          <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                            <th>KAM Parent</th>
                          <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                            <th>KAM Description</th>
                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>
                            <th>Created By</th>
                          <?php  }


                          ?>
                          <th>Status</th>

                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $customerModalHtml = "";
                        while ($row = mysqli_fetch_assoc($qry_list)) {
                          $kam_name = $row['kamName'];
                          $kam_code = $row['kamCode'];
                          $kam_description = $row['description'];
                          $kam_parent = $row['parentId'];
                          $kam_created = $row['created_by'];
                        ?>
                          <tr style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['kamId'] ?>">
                            <td><?= $cnt++ ?></td>
                            <?php if (in_array(1, $settingsCheckbox)) { ?>
                              <td><?= $kam_name ?></td>
                            <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                              <td><?= $kam_code ?></td>
                            <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                              <td>
                                <?php if ($kam_parent != "" || $kam_parent != 0) {
                                  $p_kam = queryGet("SELECT * FROM `" . ERP_KAM . "` WHERE `kamId`=$kam_parent");
                                  if ($p_kam['status'] == "success") {
                                    echo $p_kam['data']['kamName'] . "(" . $p_kam['data']['kamCode'] . ")";
                                  } else {
                                    echo "";
                                  }
                                } else {
                                  echo "no parent kam";
                                }
                                ?></td>
                            <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                              <td><?= $kam_description ?></td>
                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>
                              <td><?= getCreatedByUser($kam_created) ?></td>
                            <?php }
                            ?>
                            <td>
                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['kamId'] ?>">
                                <input type="hidden" name="changeStatus" value="active_inactive">
                                <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure change status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">
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
                              <a style="cursor: pointer;" class="btn btn-sm"><i class="fa fa-eye po-list-icon"></i></a>
                            </td>
                          </tr>
                          <!-- right modal start here  -->
                          <div class="modal fade right customer-modal" id="fluidModalRightSuccessDemo_<?= $row['kamId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                              <!--Content-->
                              <div class="modal-content">
                                <!--Header-->
                                <div class="modal-header">
                                  <p class="heading lead"><?= $kam_name ?></p>
                                  <div class="display-flex-space-between mt-4 mb-3">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $kam_code) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $kam_code) ?>" role="tab" aria-controls="home<?= str_replace('/', '-', $kam_code) ?>" aria-selected="true">Info</a>
                                      </li>
                                      <!-- -------------------Audit History Button Start------------------------- -->
                                      <li class="nav-item">
                                        <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $kam_code) ?>" data-toggle="tab" data-ccode="<?= $kam_code ?>" href="#history<?= str_replace('/', '-', $kam_code) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $kam_code) ?>" aria-selected="false">Trail</a>
                                      </li>
                                      <!-- -------------------Audit History Button End------------------------- -->
                                    </ul>
                                    <div class="action-btns display-flex-gap" id="action-navbar">



                                      <a name="customerEditBtn" data-toggle="modal" data-target="#kamEditForm_<?= $row['kamId'] ?>">
                                        <i title="Edit" style="font-size: 1.2em" class="fa fa-edit po-list-icon"></i>
                                      </a>
                                      <i title="Delete" style="font-size: 1.2em" class="fa fa-trash po-list-icon"></i>
                                      <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on po-list-icon"></i>

                                    </div>
                                  </div>
                                </div>





                                <!--Body-->
                                <div class="modal-body">

                                  <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $kam_code) ?>" role="tabpanel" aria-labelledby="home-tab">

                                      <div class="row px-3 p-0 m-0" style="place-items: self-start;">



                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">KAM Name: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $kam_name ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">KAM Code: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $kam_code ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Parent: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $kam_parent ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary"> Description: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $kam_description ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Created By : </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= getCreatedByUser($kam_created) ?></span>
                                            </div>
                                          </div>
                                        </div>


                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary">Contact number: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['contact'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary"> Email: </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['email'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary"> Employee Code : </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['emp_code'] ?></span>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="row m-2 py-2 shadow-sm bg-light">
                                            <div class="col-md-6">
                                              <span class="font-weight-bold text-secondary"> Designation : </span>
                                            </div>
                                            <div class="col-md-6">
                                              <span><?= $row['designation'] ?></span>
                                            </div>
                                          </div>
                                        </div>




                                      </div>

                                    </div>
                                    <!-- -------------------Audit History Tab Body Start------------------------- -->
                                    <div class="tab-pane fade" id="history<?= str_replace('/', '-', $kam_code) ?>" role="tabpanel" aria-labelledby="history-tab">

                                      <div class="audit-head-section mb-3 mt-3 ">
                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['created_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['created_at']) ?></p>
                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updated_by']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updated_at']) ?></p>
                                      </div>
                                      <hr>
                                      <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $kam_code) ?>">
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


                          <div id="kamEditForm_<?= $row['kamId'] ?>" class="modal kam-edit-modal">


                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  Edit KAM
                                </div>
                                <div class="modal-body">
                                  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="editKam_form" name="editKam_form">
                                    <input type="hidden" name="editKam" id="editKam" value="">
                                    <input type="hidden" name="kam_id" value="<?= $row['kamId'] ?>">
                                    <div class="row">
                                      <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-input my-2">
                                          <label for="">Kam Name</label>
                                          <input type="text" name="name" class="form-control" value="<?= $kam_name  ?>">
                                        </div>
                                      </div>
                                      <?php
                                      $kam = queryGet("SELECT * FROM `" . ERP_KAM . "` WHERE `company_id`=$company_id", TRUE);
                                      // console($kam);
                                      $kam_row = $kam["data"];
                                      // console($row);


                                      ?>
                                      <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-input my-2">
                                          <label for="">Kam Parent</label>
                                          <select class="form-control" name="p_id" id="">
                                            <option value="">KAM Parent</option>
                                            <?php foreach ($kam_row as $kam_row) { ?>
                                              <option value="<?= $kam_row['kamId'] ?>" <?php if ($row['kamId'] == $kam_row['kamId']) {
                                                                                          echo "selected";
                                                                                        } ?>><?= $kam_row['kamName'] ?></option>
                                            <?php } ?>
                                          </select>
                                        </div>
                                      </div>
                                      <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-input my-2">
                                          <label for="">Employee Code</label>
                                          <input type="text" name="emp_code" class="form-control" value="<?= $row['emp_code'] ?>">
                                        </div>
                                      </div>
                                      <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-input my-2">
                                          <label for="">Designation</label>
                                          <input type="text" name="designation" class="form-control" value="<?= $row['designation'] ?>">
                                        </div>
                                      </div>
                                      <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-input my-2">
                                          <label for="">Email</label>
                                          <input type="text" name="email" class="form-control" value="<?= $row['email'] ?>">
                                        </div>
                                      </div>
                                      <div class="col-lg-6 col-md-6 col-sm-12">
                                        <div class="form-input my-2">
                                          <label for="">Contact</label>
                                          <input type="text" name="contact" class="form-control" value="<?= $row['contact'] ?>">
                                        </div>
                                      </div>

                                      <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-input my-2">
                                          <label for="">Kam Description</label>
                                          <textarea name="description" id="" cols="30" rows="5" class="form-control"><?= $kam_description ?></textarea>
                                        </div>
                                      </div>
                                    </div>
                                    <button class="btn btn-danger edit_data">Cancel</button>
                                    <button class="btn btn-primary edit_data" value="edit_data">Update</button>
                                  </form>
                                </div>
                                <div class="modal-footer">

                                </div>
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
                      <input type="hidden" name="pageTableName" value="ERP_INVENTORY_ITEMS" />
                      <div class="modal-body">
                        <div id="dropdownframe"></div>
                        <div id="main2">
                          <table>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                                KAM Name</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                                KAM Code</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                                KAM Address</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                KAM Description</td>
                            </tr>
                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                KAM Latitude</td>
                            </tr>

                            <tr>
                              <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                                KAM Longitude</td>
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
    $('#goodGroupDropDown')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodGroupFormModal">Add New</a></div>`);
      });

    $('#purchaseGroupDropDown')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewPurchaseGroupFormModal">Add New</a></div>`);
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
    function loadGoodTypes() {
      $.ajax({
        type: "GET",
        url: `ajaxs/items/ajax-good-types.php`,
        beforeSend: function() {
          $("#goodTypeDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#goodTypeDropDown").html(response);

          <?php
          if (isset($row["goodTypeId"])) {
          ?>
            $(`#goodTypeDropDown option[value=<?= $row["goodTypeId"] ?>]`).attr('selected', 'selected');
          <?php
          }
          ?>
        }
      });
    }
    loadGoodTypes();
    $(document).on('submit', '#addNewGoodTypesForm', function(event) {
      event.preventDefault();
      let formData = $("#addNewGoodTypesForm").serialize();
      $.ajax({
        type: "POST",
        url: `ajaxs/items/ajax-good-types.php`,
        data: formData,
        beforeSend: function() {
          $("#addNewGoodTypesFormSubmitBtn").toggleClass("disabled");
          $("#addNewGoodTypesFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
        },
        success: function(response) {
          $("#goodTypeDropDown").html(response);
          $('#addNewGoodTypesForm').trigger("reset");
          $("#addNewGoodTypesFormModal").modal('toggle');
          $("#addNewGoodTypesFormSubmitBtn").html("Submit");
          $("#addNewGoodTypesFormSubmitBtn").toggleClass("disabled");
        }
      });
    });

    function loadGoodGroup() {
      $.ajax({
        type: "GET",
        url: `ajaxs/items/ajax-good-groups.php`,
        beforeSend: function() {
          $("#goodGroupDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#goodGroupDropDown").html(response);
          <?php
          if (isset($row["goodGroupId"])) {
          ?>
            $(`#goodGroupDropDown option[value=<?= $row["goodGroupId"] ?>]`).attr('selected', 'selected');
          <?php
          }
          ?>
        }
      });
    }
    loadGoodGroup();
    $(document).on('submit', '#addNewGoodGroupForm', function(event) {
      event.preventDefault();
      let formData = $("#addNewGoodGroupForm").serialize();
      $.ajax({
        type: "POST",
        url: `ajaxs/items/ajax-good-groups.php`,
        data: formData,
        beforeSend: function() {
          $("#addNewGoodGroupFormSubmitBtn").toggleClass("disabled");
          $("#addNewGoodGroupFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
        },
        success: function(response) {
          $("#goodGroupDropDown").html(response);
          $('#addNewGoodGroupForm').trigger("reset");
          $("#addNewGoodGroupFormModal").modal('toggle');
          $("#addNewGoodGroupFormSubmitBtn").html("Submit");
          $("#addNewGoodGroupFormSubmitBtn").toggleClass("disabled");
        }
      });
    });




    function loadPurchaseGroup() {
      $.ajax({
        type: "GET",
        url: `ajaxs/items/ajax-purchase-groups.php`,
        beforeSend: function() {
          $("#purchaseGroupDropDown").html(`<option value="">Loding...</option>`);
        },
        success: function(response) {
          $("#purchaseGroupDropDown").html(response);
          <?php
          if (isset($row["purchaseGroupId"])) {
          ?>
            $(`#purchaseGroupDropDown option[value=<?= $row["purchaseGroupId"] ?>]`).attr('selected', 'selected');
          <?php
          }
          ?>
        }
      });
    }
    loadPurchaseGroup();
    $(document).on('submit', '#addNewPurchaseGroupForm', function(event) {
      event.preventDefault();
      let formData = $("#addNewPurchaseGroupForm").serialize();
      $.ajax({
        type: "POST",
        url: `ajaxs/items/ajax-purchase-groups.php`,
        data: formData,
        beforeSend: function() {
          $("#addNewPurchaseGroupFormSubmitBtn").toggleClass("disabled");
          $("#addNewPurchaseGroupFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');
        },
        success: function(response) {
          $("#purchaseGroupDropDown").html(response);
          $('#addNewPurchaseGroupForm').trigger("reset");
          $("#addNewPurchaseGroupFormModal").modal('toggle');
          $("#addNewPurchaseGroupFormSubmitBtn").html("Submit");
          $("#addNewPurchaseGroupFormSubmitBtn").toggleClass("disabled");
        }
      });
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


    $(".add_data").click(function() {
      var data = this.value;
      $("#createKam").val(data);
      //confirm('Are you sure to Submit?')
      $("#warehouseSubmitForm").submit();
    });


    $(".edit_data").click(function() {
      //  alert(1);
      var data = this.value;
      $("#editKam").val(data);
      //confirm('Are you sure to Submit?')
      $("#editKam_form").submit();
    });


    //volume calculation
    function calculate_volume() {
      let height = $("#height").val();
      let width = $("#width").val();
      let length = $("#length").val();
      let res = height * length * width;
      let resm = res * 0.000001;
      console.log(res);
      $("#volcm").val(res);
      $("#volm").val(resm);


    }

    // $(document).on("keyup", ".calculate_volume", function(){
    //  calculate_volume();
    // });

    $("#height").keyup(function() {
      calculate_volume();
    });
    $("#width").keyup(function() {
      calculate_volume();
    });
    $("#length").keyup(function() {
      calculate_volume();
    });


    $("#buomDrop").change(function() {
      let res = $(this).val();
      $("#buom").val(res);
      console.log("buomDrop", res);
    });

    $("#iuomDrop").change(function() {
      let rel = $(this).val();
      $("#ioum").val(rel);
      console.log("iuomDrop", rel);
    });

  });
</script>
<style>
  .dataTable thead {
    top: 0px !important;
  }
</style>