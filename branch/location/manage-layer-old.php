<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/branch/func-warehouse-controller.php");


//console($_SESSION);
//console($_SESSION['logedBranchAdminInfo']['fldAdminBranchId']);
//console(date("Y-m-d H:i:s"));
$warehouseController = new WarehouseController();
$dbObject = new Database();


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

if (isset($_POST["createLayer"])) {

  $addNewObj = $warehouseController->createLayer($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_POST["editStorageLocation"])) {

  //console($_SESSION);
  $addNewObj = $warehouseController->editStorageLocation($_POST, $branch_id, $company_id, $location_id, $created_by);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}


// if (isset($_POST["editgoodsdata"])) {
//   $addNewObj = $warehouseController->editGoods($_POST);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["itemId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}
?>

<!-- <link rel="stylesheet" href="../../public/assets/listing.css"> -->
<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<?php
if (isset($_GET['create'])) {
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper report-wrapper is-stock-new is-sales-orders is-warehouse vitwo-alpha-global">
  <!-- Content Header (Page header) -->
  <div class="content-header mb-2 p-0  border-bottom">
    <?php if (isset($msg)) { ?>
    <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
      <?= $msg ?>
    </div>
    <?php } ?>
    <div class="container-fluid">
      <div class="row pt-2 pb-2">
        <div class="col-md-12" style="padding-top: 15px">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i
                  class="fas fa-home po-list-icon"></i>
                Home</a></li>
            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                  class="fa fa-list po-list-icon"></i> Manage
                Layer</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                  class="fa fa-plus po-list-icon"></i> Add
                Layer</a></li>
          </ol>
        </div>
        <!-- <div class="col-md-6" style="display: flex;">

          <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i
              class="fa fa-plus fontSize"></i> Final Submit</button>
        </div> -->
        <script>
          // $(document).ready(function(){
          //   $("#SubmitFormSaveBtn").click(function(){
          //     $("#SubmitForm").submit();
          //   });
          // });
        </script>
      </div>
    </div>
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="SubmitForm" name="SubmitForm">
        <input type="hidden" name="createLayer" id="createLayer" value="">

        <!-- *********** old code ********* -->
        <!-- <div class="row">
          <div class="col-md-8">
            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i
                class="fa fa- po-list-icon" aria-hidden="true"></i></button>
            <div id="accordion">

              <div class="card card-success">
                <div class="card-header cardHeader">
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse"
                      href="#collapseThree"> Layer Details </a> </h4>
                </div>
                <div id="collapseThree">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <?php
                            $rack = $warehouseController->getAllRack();
                          //    console($rack);
                            ?>
                          <select id="warehouseDropDown" name="rack"
                            class="form-control form-control-border borderColor">
                            <option value="" data-goodType="">Rack</option>
                            <?php
                           
                              foreach ($rack['data'] as $list) {
                              ?>

                            <option value="<?= $list['rack_id'] ?>" data-goodType=""><?= $list['rack_name'] ?></option>
                            <?php }
                              ?>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="input-group">
                          <label>Layer Name</label>
                          <input type="text" name="name" class="form-control">

                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="input-group">
                          <textarea name="layer_desc" class="form-control">Description</textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

        </div> -->

        <!-- ************ new code ************ -->
        <div class="row">
          <div class="col-md-7">
            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog"
                aria-hidden="true"></i></button>
            <div class="card">
              <div class="card-header p-3">
                <div class="row customer-info-head">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="head">
                      <i class="fa fa-info text-white"></i>
                      <h4 class="text-white pt-2">Layer Details</h4>
                    </div>
                  </div>
                </div>
              </div>
              <div id="collapseThree">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-input">
                        <label for="date">Rack Name</label>
                        <?php
                            $rack = $warehouseController->getAllRack();
                          //    console($rack);
                            ?>
                        <select id="warehouseDropDown" name="rack" class="form-control form-control-border borderColor">
                          <option value="" data-goodType="">Rack</option>
                          <?php
                           
                              foreach ($rack['data'] as $list) {
                              ?>

                          <option value="<?= $list['rack_id'] ?>" data-goodType=""><?= $list['rack_name'] ?></option>
                          <?php }
                              ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-input">
                        <label for="date">Layer Name</label>
                        <input type="text" name="name" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-input">
                        <label>Layer Description</label>
                        <textarea type="text" name="layer_desc" class="form-control"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-5" style="max-height: 221px; overflow-y: auto;">
            <div class="row">
              <table class="table table-hover table-nowrap stock-new-table transactional-book-table mt-n5">
                <thead style="position: sticky; top: 0;">
                  <tr>
                    <th>Layer Name</th>
                    <th>Rack Name</th>
                    <th>Layer Description</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $cond = "";
                  $sts = " AND 'status' !='deleted'";
                  $sqlList = "SELECT layer.* , erpRack.rack_name FROM `erp_layer` as layer  LEFT JOIN erp_rack as erpRack ON erpRack.rack_id =layer.rack_id WHERE 1 AND  layer.`company_id`=$company_id AND layer.`branch_id`=$branch_id AND layer.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY layer.layer_id desc";
                  $sqlLayer =  $dbObject->queryGet($sqlList,true);
                  $numRows=$sqlLayer['numRows'];
                  // console($sqlRack);
                  
                  if($numRows>0){
                          $sqlData=$sqlLayer['data'];
                          foreach($sqlData as $data){
                            ?>
                  <tr>
                    <td><?=$data['layer_name']?></td>
                    <td><?=$data['rack_name']?></td>
                    <td><?=$data['layer_desc']?></td>

                  </tr>
                  <?php
                          }
                        }
                        else{
                          ?>
                  <tr>
                    <td>No data Found</td>
                  </tr>
                  <?php
                        }
                        ?>

                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer m-0 p-0 text-right">
            <button class="btn btn-primary btnstyle gradientBtn ml-2 add_data" value="add_post"><i
                class="fa fa-plus fontSize"></i> Submit</button>
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
            <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i>
                Home</a></li>
            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage
                Goods</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Edit
                Goods</a></li>
          </ol>
        </div>
        <div class="col-md-6" style="display: flex;">
          <button class="btn btn-danger btnstyle ml-2 edit_data" value="edit_draft">Draft</button>
          <button class="btn btn-primary btnstyle gradientBtn ml-2 edit_data" value="edit_post"><i
              class="fa fa-plus fontSize"></i> Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header -->
  <?php
    $sl_id = base64_decode($_GET['edit']);
    $sql = "SELECT * FROM  `" . ERP_STORAGE_LOCATION . "` WHERE `storage_location_id` = $sl_id";
    $resultObj = queryGet($sql);
    $row = $resultObj["data"];

    console($row);

    //echo  $sql = "SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$editVendorId";
    //  $res = $dbCon->query($sql);
    //   $row = $res->fetch_assoc();
    // $row=[];
    // echo "<pre>";
    // print_r($row);
    // echo "</pre>";
    ?>

  <section class="content">
    <div class="container-fluid">
      <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="Edit_data" name="Edit_data">
        <input type="hidden" name="editStorageLocation" id="editStorageLocation" value="">
        <input type="hidden" name="sl_id" value="<?= $row['storage_location_id'] ?>">
        <div class="row">
          <div class="col-md-8">
            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog"
                aria-hidden="true"></i></button>
            <div id="accordion">

              <div class="card card-success">

                <div class="card-header cardHeader">
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse"
                      href="#collapseThree"> Layer Details </a> </h4>
                </div>
                <div id="collapseThree">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <select id="warehouseDropDown" name="warehouse"
                            class="form-control form-control-border borderColor">
                            <option value="" data-goodType="">Warehouse</option>
                            <?php
                              $warehouseList = $warehouseController->getAllWarehouse()['data'];
                              foreach ($warehouseList as $list) {
                              ?>

                            <option value="<?= $list['warehouse_id'] ?>" data-goodType=""><?= $list['warehouse_name'] ?>
                            </option>
                            <?php }
                              ?>
                          </select>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" name="name" class="m-input" value="<?= $row['storage_location_name'] ?>">
                          <label>Layer Name</label>
                        </div>
                      </div>

                      <div class="col-md-12">
                        <div class="input-group">
                          <input type="text" name="storage_control" class="m-input"
                            value="<?= $row['storage_control'] ?>">
                          <label>Storage Control</label>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="text" name="temp" class="m-input" value="<?= $row['temp_control'] ?>">
                          <label>Temp Control</label>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <!-- <div class="col-md-4">
              <div class="card card-primary card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                  <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                    <li class="nav-item"> <a class="nav-link active" id="custom-tabs-three-home-tab" data-toggle="pill" href="#custom-tabs-three-home" role="tab" aria-controls="custom-tabs-three-home" aria-selected="true">Home</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-profile-tab" data-toggle="pill" href="#custom-tabs-three-profile" role="tab" aria-controls="custom-tabs-three-profile" aria-selected="false">Profile</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-messages-tab" data-toggle="pill" href="#custom-tabs-three-messages" role="tab" aria-controls="custom-tabs-three-messages" aria-selected="false">Messages</a> </li>
                    <li class="nav-item"> <a class="nav-link" id="custom-tabs-three-settings-tab" data-toggle="pill" href="#custom-tabs-three-settings" role="tab" aria-controls="custom-tabs-three-settings" aria-selected="false">Settings</a> </li>
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
              </div> -->
    </div>
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
            <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i>
                Home</a></li>
            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage
                Goods</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View
                Goods</a></li>
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
            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog"
                aria-hidden="true"></i></button>
            <div id="accordion">
              <div class="card card-primary">
                <div class="card-header cardHeader">
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse"
                      href="#collapseOne"> Classification </a> </h4>
                </div>
                <div id="collapseOne" class="collapse show" data-parent="#accordion">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <div class="input-group">
                          <select id="goodsType" name="goodsType"
                            class="select2 form-control form-control-border borderColor">
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
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse"
                      href="#collapseTwo"> Basic Details </a> </h4>
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
                            <input type="text" name="baseUnitMeasure"
                              class="form-control form-control-border borderColor" id="exampleInputBorderWidth2"
                              placeholder="baseUnitOfMeasure">
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
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse"
                      href="#collapseThree"> Storage Details </a> </h4>
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
                            <input type="text" name="storageControl"
                              class="form-control form-control-border borderColor" id="exampleInputBorderWidth2"
                              placeholder="Storage Control">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="" class="form-control borderNone">Max Storage Period :</label>
                          </div>
                          <div class="col-md-6">
                            <input type="text" name="maxStoragePeriod"
                              class="form-control form-control-border borderColor" id="exampleInputBorderWidth2"
                              placeholder="Max Storage Period">
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
                            <input type="text" name="minRemainSelfLife"
                              class="form-control form-control-border borderColor" id="exampleInputBorderWidth2"
                              placeholder="Min Remain Self Life">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card card-success">
                <div class="card-header cardHeader">
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse"
                      href="#collapseFour"> Purchase Details </a> </h4>
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
                            <input type="text" name="purchasingValueKey"
                              class="form-control form-control-border borderColor" id="exampleInputBorderWidth2"
                              placeholder="Purchasing Value Key">
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
}
 else {
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
                <h3 class="card-title">Manage Layer</h3>
                <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create"
                  class="btn btn-sm btn-primary btnstyle m-2"><i class="fa fa-plus"></i> Add New</a>
              </li>
            </ul>
          </div>
          <div class="card card-tabs" style="border-radius: 20px;">
            <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get"
              onsubmit="return srch_frm();">
              <div class="card-body">
                <div class="row filter-serach-row">
                  <div class="col-lg-2 col-md-2 col-sm-12">
                    <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2"
                      style="position:absolute;z-index:999;"> <i class="fa fa-cog po-list-icon"
                        aria-hidden="true"></i></a>
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
                              <div class="input-group-manage-vendor"> <input class="fld form-control" type="date"
                                  name="form_date_s" id="form_date_s"
                                  value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                  echo $_REQUEST['form_date_s'];
                                                                                                                                                                } ?>" />
                              </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2">
                              <div class="input-group-manage-vendor"> <input class="fld form-control" type="date"
                                  name="form_date_s" id="form_date_s"
                                  value="<?php if (isset($_REQUEST['form_date_s'])) {
                                                                                                                                                                  echo $_REQUEST['form_date_s'];
                                                                                                                                                                } ?>" />
                              </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2">
                              <div class="input-group-manage-vendor">
                                <input type="text" name="keyword" class="fld form-control m-input" id="keyword"
                                  placeholder="Enter Keyword"
                                  value="<?php if (isset($_REQUEST['keyword'])) {
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

                  $sql_list = queryGet("SELECT * FROM `erp_layer` as layer ,`erp_rack` as rack WHERE 1 AND rack.rack_id=layer.rack_id  AND layer.`company_id`=$company_id AND layer.`branch_id`=$branch_id AND layer.`location_id`=$location_id  ORDER BY layer.layer_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ",true);
                  
                //  console($sql_list);
                

                  //AND  layer.'warehouse_id'=warehouse.'warehouse_id' 
                  //as sl ,".ERP_WAREHOUSE." as warehouse
                  $countShow = "SELECT COUNT(*) FROM `erp_layer` as layer ,`erp_rack` as rack WHERE 1 AND rack.rack_id=layer.rack_id  AND layer.`company_id`=$company_id AND layer.`branch_id`=$branch_id AND layer.`location_id`=$location_id  ";
                  $countQry = mysqli_query($dbCon, $countShow);
                  $rowCount = mysqli_fetch_array($countQry);
                  $count = $rowCount[0];
                  $cnt = $GLOBALS['start'] + 1;
                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_LAYER", $_SESSION["logedBranchAdminInfo"]["adminId"]);
                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);
                  $settingsCheckbox = unserialize($settingsCh);
                  if ($sql_list['numRows'] > 0) { ?>
                <table class="table defaultDataTable table-hover text-nowrap p-0 m-0">
                  <thead>
                    <tr class="alert-light">
                      <th>#</th>
                      <?php if (in_array(1, $settingsCheckbox)) { ?>
                      <th>Layer Name</th>
                      <?php }
                          if (in_array(2, $settingsCheckbox)) { ?>
                      <th>Layer Code</th>
                      <?php }
                          if (in_array(3, $settingsCheckbox)) { ?>
                      <th>Rack</th>
                      <?php  }
                          if (in_array(4, $settingsCheckbox)) { ?>
                      <th>Description</th>
                      <?php }
                         
                          ?>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                        $customerModalHtml = "";
                     foreach($sql_list['data'] as $row){
                          $layer_name = $row['layer_name'];
                          
                          $rack = $row['rack_name'];
                          $desc = $row['layer_desc'];
                          // $warehouse_lng = $row['warehouse_lng'];
                        ?>
                    <tr>
                      <td><?= $cnt++ ?></td>
                      <?php if (in_array(1, $settingsCheckbox)) { ?>
                      <td><?= $layer_name ?></td>
                      <?php }
                            if (in_array(2, $settingsCheckbox)) { ?>
                      <td></td>
                      <?php }
                            if (in_array(3, $settingsCheckbox)) { ?>
                      <td><?=  $rack ?></td>

                      <?php }
                            if (in_array(4, $settingsCheckbox)) { ?>
                      <td><?= $desc ?></td>
                      <?php }
                            ?>
                      <td>
                        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">
                          <input type="hidden" name="id" value="<?php echo $row['layer_id'] ?>">
                          <input type="hidden" name="changeStatus" value="active_inactive">
                          <button <?php if ($row['status'] == "draft") { ?> type="button"
                            style="cursor: inherit; border:none" <?php } else { ?>type="submit"
                            onclick="return confirm('Are you sure change status?')" style="cursor: pointer; border:none"
                            <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top"
                            title="<?php echo $row['status'] ?>">
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
                        <a style="cursor: pointer;" class="btn btn-sm" data-toggle="modal"
                          data-target="#fluidModalRightSuccessDemo_<?= $row['layer_id'] ?>"><i
                            class="fa fa-eye po-list-icon"></i></a>
                      </td>
                    </tr>
                    <!-- right modal start here  -->
                    <div class="modal fade right storage-location-modal customer-modal"
                      id="fluidModalRightSuccessDemo_<?= $row['layer_id'] ?>" tabindex="-1" role="dialog"
                      aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                      <div class="modal-dialog modal-full-height modal-right modal-notify modal-success"
                        role="document">
                        <!--Content-->
                        <div class="modal-content">
                          <!--Header-->
                          <div class="modal-header">
                            <p class="heading lead"><?= $storage_location_name ?></p>
                            <div class="display-flex-space-between mt-4 mb-3">
                              <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                  <a class="nav-link active" id="home-tab" data-toggle="tab"
                                    href="#home_<?= $layer_id ?>" role="tab" aria-controls="home"
                                    aria-selected="true">Info</a>
                                </li>
                                <!-- -------------------Audit History Button Start------------------------- -->
                                <li class="nav-item">
                                  <a class="nav-link auditTrail"
                                    id="history-tab<?= str_replace('/', '-', $storage_location_code) ?>"
                                    data-toggle="tab" data-ccode="<?= str_replace('/', '-', $storage_location_code) ?>"
                                    href="#history<?= str_replace('/', '-', $storage_location_code) ?>" role="tab"
                                    aria-controls="history<?= str_replace('/', '-', $storage_location_code) ?>"
                                    aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>
                                </li>
                                <!-- -------------------Audit History Button End------------------------- -->
                              </ul>
                              <div class="action-btns display-flex-gap" id="action-navbar">
                                <?php

                                      $sl_id = base64_encode($row['storage_location_id'])

                                      ?>
                                <form action="" method="POST">

                                  <a href="manage-storage-location.php?edit=<?= $sl_id ?>" name="customerEditBtn">
                                    <i title="Edit" style="font-size: 1.2em" class="fa fa-edit po-list-icon"></i>
                                  </a>
                                  <i title="Delete" style="font-size: 1.2em" class="fa fa-trash po-list-icon"></i>
                                  <i title="Toggle" style="font-size: 1.2em" class="fa fa-toggle-on po-list-icon"></i>
                                </form>
                              </div>
                            </div>
                          </div>
                          <!--Body-->
                          <div class="modal-body" style="padding: 0;">

                            <div class="tab-content" id="myTabContent">

                              <div class="tab-pane fade show active" id="home_<?= $layer_id ?>" role="tabpanel"
                                aria-labelledby="home-tab">
                                <div class="col-md-12">
                                  <div class="shadow-sm bg-light py-2 mx-2 my-2" id="action-navbar"
                                    style="text-align:right">

                                  </div>
                                </div>
                                <div class="row px-3 p-0 m-0" style="place-items: self-start;">

                                  <div class="col-md-6">
                                    <div class="row m-2 py-2 shadow-sm bg-light">
                                      <div class="col-md-6">
                                        <span class="font-weight-bold text-secondary">Layer Name: </span>
                                      </div>
                                      <div class="col-md-6">
                                        <span><?= $layer_name ?></span>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="row m-2 py-2 shadow-sm bg-light">
                                      <div class="col-md-6">
                                        <span class="font-weight-bold text-secondary">Layer Code: </span>
                                      </div>
                                      <div class="col-md-6">
                                        <span></span>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="row m-2 py-2 shadow-sm bg-light">
                                      <div class="col-md-6">
                                        <span class="font-weight-bold text-secondary">Storage Location: </span>
                                      </div>
                                      <div class="col-md-6">
                                        <span><?= $sl ?></span>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                    <div class="row m-2 py-2 shadow-sm bg-light">
                                      <div class="col-md-6">
                                        <span class="font-weight-bold text-secondary"> Description: </span>
                                      </div>
                                      <div class="col-md-6">
                                        <span><?= $desc ?></span>
                                      </div>
                                    </div>
                                  </div>

                                </div>
                              </div>
                              <!-- -------------------Audit History Tab Body Start------------------------- -->
                              <div class="tab-pane fade"
                                id="history<?= str_replace('/', '-', $storage_location_code) ?>" role="tabpanel"
                                aria-labelledby="history-tab">

                                <div class="audit-head-section mb-3 mt-3 ">
                                  <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span>
                                    <?= getCreatedByUser($storage_location_code['createdBy']) ?> <span
                                      class="font-bold text-normal"> on </span>
                                    <?= formatDateORDateTime($storage_location_code['createdAt']) ?></p>
                                  <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated
                                      by</span> <?= getCreatedByUser($storage_location_code['updatedBy']) ?> <span
                                      class="font-bold text-normal"> on </span>
                                    <?= formatDateORDateTime($storage_location_code['updatedAt']) ?></p>
                                </div>
                                <hr>
                                <div
                                  class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $storage_location_code) ?>">

                                  <ol class="timeline">

                                    <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal"
                                      data-target="#innerModal">
                                      <span class="timeline-item-icon | filled-icon"><span
                                          class="spinner-border spinner-border-sm" role="status"
                                          aria-hidden="true"></span></span>
                                      <div class="new-comment font-bold">
                                        <p>Loading...
                                          <ul class="ml-3 pl-0">
                                            <li style="list-style: disc; color: #a7a7a7;">-- --, --:-- --</li>
                                          </ul>
                                        </p>
                                      </div>
                                    </li>
                                    <p class="mt-0 mb-5 ml-5">Loading...</p>

                                    <li class="timeline-item mb-0 bg-transparent" type="button" data-toggle="modal"
                                      data-target="#innerModal">
                                      <span class="timeline-item-icon | filled-icon"><span
                                          class="spinner-border spinner-border-sm" role="status"
                                          aria-hidden="true"></span></span>
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
                  <form name="table-settings" method="post" action="<?php $_SERVER['PHP_SELF']; ?>"
                    onsubmit="return table_settings();">
                    <input type="hidden" name="tablename" value="<?= TBL_BRANCH_ADMIN_TABLESETTINGS; ?>" />
                    <input type="hidden" name="pageTableName" value="ERP_LAYER" />
                    <div class="modal-body">
                      <div id="dropdownframe"></div>
                      <div id="main2">
                        <table>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox"
                                <?php echo (in_array(1, $settingsCheckbox) ? 'checked="checked"' : ''); ?>
                                name="settingsCheckbox[]" id="settingsCheckbox1" value="1" />
                              Layer Name</td>
                          </tr>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox"
                                <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?>
                                name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />
                              Layer Code</td>
                          </tr>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox"
                                <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?>
                                name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />
                              Storage Location</td>
                          </tr>
                          <tr>
                            <td valign="top" style="width: 165px"><input type="checkbox"
                                <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?>
                                name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />
                              Description</td>
                          </tr>
                          <tr>

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
require_once("../common/footer.php");
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
    // $('#warehouseDropDown')
    //   .select2()
    //   .on('select2:open', () => {
    //     //$(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodTypesFormModal">Add New</a></div>`);
    //   });
    // $("#warehouseDropDown").change(function() {
    //   let dataAttrVal = $("#warehouseDropDown").find(':selected').data('goodtype');
    //   if (dataAttrVal == "RM") {
    //     $("#bomCheckBoxDiv").html("");
    //   } else if (dataAttrVal == "SFG") {
    //     $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired"style="width: auto; margin-bottom: 0;" checked>Required BOM`);
    //   } else {
    //     $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired"style="width: auto; margin-bottom: 0;">Required BOM`);
    //   }
    // });
    //**************************************************************
    $('#goodGroupDropDown')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(
          `<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodGroupFormModal">Add New</a></div>`
        );
      });
    $('#purchaseGroupDropDown')
      .select2()
      .on('select2:open', () => {
        $(".select2-results:not(:has(a))").append(
          `<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewPurchaseGroupFormModal">Add New</a></div>`
        );
      });
    $('#warehouseDropDown')
      .select2()
      .on('select2:open', () => {
        // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewItemsFormModal">Add New</a></div>`);
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
    // function loadWarehouse() {
    //   $.ajax({
    //     type: "GET",
    //     url: `ajaxs/warehouse/ajax-warehouse.php`,
    //     beforeSend: function() {
    //       $("#warehouseDropDown").html(`<option value="">Loding...</option>`);
    //     },
    //     success: function(response) {
    //       $("#warehouseDropDown").html(response);
    //     }
    //   });
    // }
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
      $("#createLayer").val(data);
      //confirm('Are you sure to Submit?')
      $("#SubmitForm").submit();
    });
    $(".edit_data").click(function() {
      var data = this.value;
      $("#editStorageLocation").val(data);
      //confirm('Are you sure to Submit?')
      $("#Edit_data").submit();
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