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

if (isset($_POST["createStorageLocation"])) {


  $addNewObj = $warehouseController->createStorageLocation($_POST, $branch_id, $company_id, $location_id, $created_by);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}

if (isset($_POST["editStorageLocation"])) {

  //console($_SESSION);
  // console($_POST);
  // exit();
  $addNewObj = $warehouseController->editStorageLocation($_POST, $branch_id, $company_id, $location_id, $created_by);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}


// if (isset($_POST["editgoodsdata"])) {
//   $addNewObj = $warehouseController->editGoods($_POST);
//   swalToast($addNewObj["status"], $addNewObj["message"]);
// }

// if (isset($_POST["add-table-settings"])) {
//   $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["itemId"]);
//   swalToast($editDataObj["status"], $editDataObj["message"]);
// }
// 
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
                  class="fas fa-home po-list-icon"></i></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                  class="fa fa-list po-list-icon"></i>Manage Storage Location</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                  class="fa fa-plus po-list-icon"></i>Add Storage Location</a></li>
          </ol>
        </div>
        <!-- <div class="col-md-6" style="display: flex;">
          <button class="btn btn-danger btnstyle ml-2 add_data" value="add_draft">Save As Draft</button>
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
        <input type="hidden" name="createStorageLocation" id="createStorageLocation" value="">
        <div class="row">

          <!-- **************** new code *************** -->
          <div class="col-md-7">
            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog"
                aria-hidden="true"></i></button>
            <div class="card">
              <div class="card-header p-3">
                <div class="row customer-info-head">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="head">
                      <i class="fa fa-info text-white"></i>
                      <h4 class="text-white pt-2">Storage Location Details</h4>
                    </div>
                  </div>
                </div>
              </div>
              <div id="collapseThree">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-input">
                        <label for="date">Select Warehouse</label>
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
                      <div class="form-input">
                        <label for="date">Select Storage Location Type</label>
                        <select id="warehouseDropDown" name="sl_type"
                          class="form-control form-control-border borderColor">
                          <option value="" data-goodType="">Storage Location Type</option>
                          <option value="RM-WH||RM||Open||rmWhOpen" data-goodType="rmWhOpen">RM Warehouse</option>
                          <option value="RM-PROD||RM||Open||rmProdOpen" data-goodType="rmProdOpen">RM Production
                          </option>
                          <option value="SFG-STOCK||SFG||Open||sfgStockOpen" data-goodType="sfgStockOpen">SFG Stock
                          </option>
                          <option value="FG-WH||FG||Open||fgWhOpen" data-goodType="fgWhOpen">FG Warehouse</option>
                          <option value="FG-MKT||FG||Open||fgMktOpen" data-goodType="fgMktOpen">FG Market</option>
                          <option value="ASSET||ASSET||Open||AssetLocation" data-goodType="AssetLocation">Asset</option>
                          <option value="QA||RM||Open||QaLocation" data-goodType="QaLocation">Quality Assurance (QA)
                          </option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-input">
                        <label>Storage Location Name</label>
                        <input type="text" name="name" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-input">
                        <label>Storage Control</label>
                        <input type="text" name="storage_control" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-input">
                        <label>Temp Control</label>
                        <input type="text" name="temp" class="form-control">
                      </div>
                    </div>
                     <div class="m-2 text-right">
            <button type="submit" class="btn btn-primary text-light my-3" value="add_post">Submit</button>
          </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-5" style="overflow-y: auto;">
            <div class="row">
              <table class="table table-hover table-nowrap stock-new-table transactional-book-table mt-n5">
                <thead style="position: sticky; top: 0;">
                  <tr>
                    <th colspan="3" style="text-align: center; font-size: 14px; background-color: #003060; border-top-left-radius: 12px;
            border-top-right-radius: 12px; color: white">Storage Current Changes</th>
                  </tr>
                  <tr>
                    <th>Storage Location Name</th>
                    <th>Storage Location Code</th>
                    <th>Warehouse</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $cond = "";
                  $sts = " AND 'status' !='deleted'";
                  $sqlList = "SELECT storage.* , erpWarehouse.warehouse_name FROM `erp_storage_location` as storage  LEFT JOIN erp_storage_warehouse as erpWarehouse ON erpWarehouse.warehouse_id = storage.warehouse_id WHERE 1 AND  storage.`company_id`=$company_id AND storage.`branch_id`=$branch_id AND storage.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY storage.storage_location_id desc";
                  $sqlStorage =  $dbObject->queryGet($sqlList,true);
                  $numRows=$sqlStorage['numRows'];
                  // console($sqlStorage);
                  
                    if($numRows>0){
                          $sqlData=$sqlStorage['data'];
                          foreach($sqlData as $data){
                            ?>
                  <tr>
                    <td><?=$data['storage_location_name']?></td>
                    <td><?=$data['storage_location_code']?></td>
                    <td><?=$data['warehouse_name']?></td>

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
         
          <!-- ************* old code ************* -->
          <!-- <div class="col-md-8">
              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa- po-list-icon" aria-hidden="true"></i></button>
              <div id="accordion">
                <div class="card card-success">
                  <div class="card-header cardHeader">
                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseThree"> Storage Location Details </a> </h4>
                  </div>
                  <div id="collapseThree">
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <select id="warehouseDropDown" name="warehouse" class="form-control form-control-border borderColor">
                              <option value="" data-goodType="">Warehouse</option>
                              <?php
                              $warehouseList = $warehouseController->getAllWarehouse()['data'];
                              foreach ($warehouseList as $list) {
                              ?>

                                <option value="<?= $list['warehouse_id'] ?>" data-goodType=""><?= $list['warehouse_name'] ?></option>
                              <?php }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-6 mb-3">
                          <div class="input-group">
                            <select id="warehouseDropDown" name="sl_type" class="form-control form-control-border borderColor">
                              <option value="" data-goodType="">Storage Location Type</option>
                             

                                <option value="RM-WH||RM||Open||rmWhOpen" data-goodType="rmWhOpen">RM Warehouse</option>
                                <option value="RM-PROD||RM||Open||rmProdOpen" data-goodType="rmProdOpen">RM Production</option>
                                <option value="SFG-STOCK||SFG||Open||sfgStockOpen" data-goodType="sfgStockOpen">SFG Stock</option>
                                <option value="FG-WH||FG||Open||fgWhOpen" data-goodType="fgWhOpen">FG Warehouse</option>
                                <option value="FG-MKT||FG||Open||fgMktOpen" data-goodType="fgMktOpen">FG Market</option>
                                <option value="ASSET||ASSET||Open||AssetLocation" data-goodType="AssetLocation">Asset</option>
                                <option value="QA||RM||Open||QaLocation" data-goodType="QaLocation">Quality Assurance (QA)</option>

                              
                             
                            </select>
                          </div>
                        </div>


                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="name" class="m-input">
                            <label>Storage Location Name</label>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="storage_control" class="m-input">
                            <label>Storage Control</label>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="input-group">
                            <input type="text" name="temp" class="m-input">
                            <label>Temp Control</label>
                          </div>
                        </div>




                      </div>
                    </div>
                  </div>
                </div>


              </div>
            </div> -->
          <!-- ********* end old code ************ -->
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
<!-- /.content -->
</div>
<?php
} else if (isset($_GET['edit'])) {


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
                  class="fa fa-list po-list-icon"></i>Manage
                Storage</a></li>
            <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i
                  class="fa fa-plus po-list-icon"></i>Edit
                Storage</a></li>
          </ol>
        </div>
        <!-- <div class="col-md-6" style="display: flex;">
          <button class="btn btn-danger btnstyle ml-2 edit_data" value="edit_draft">Draft</button>
          <button class="btn btn-primary btnstyle gradientBtn ml-2 edit_data" value="edit_post"><i
              class="fa fa-plus fontSize"></i> Save</button>
        </div> -->
      </div>
    </div>
  </div>
  <!-- /.content-header -->
  <?php
    $storage_location_id = base64_decode($_GET['edit']);
    // $sql = "SELECT * FROM  `" . ERP_STORAGE_LOCATION . "` WHERE `storage_location_id` = $storage_location_id";
    // $sql = "SELECT storage.* , erpWarehouse.warehouse_name FROM `erp_storage_location` as storage  LEFT JOIN erp_storage_warehouse as erpWarehouse ON erpWarehouse.warehouse_id = storage.warehouse_id WHERE 1 AND  storage.`company_id`=$company_id AND storage.`branch_id`=$branch_id AND storage.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY storage.storage_location_id desc";
    $sql = "SELECT storage.*, warehouse.warehouse_name FROM `" . ERP_STORAGE_LOCATION . "` AS storage LEFT JOIN `" . ERP_WAREHOUSE . "` AS warehouse ON warehouse.warehouse_id = storage.warehouse_id WHERE storage.`storage_location_id` = $storage_location_id AND storage.`company_id` = $company_id AND storage.`branch_id` = $branch_id AND storage.`location_id` = $location_id ORDER BY storage.storage_location_id desc";    
    $resultObj = queryGet($sql);
    $row = $resultObj["data"];

    // console($row);

    ?>

  <section class="content">
    <div class="container-fluid">
      <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="Edit_data" name="Edit_data">
        <input type="hidden" name="editStorageLocation" id="editStorageLocation" value="<?= $storage_location_id ?>">
        <input type="hidden" name="storage_location_id" value="<?= $storage_location_id ?>">
        <input type="hidden" name="storage_location_code" value="<?= $row['storage_location_code'] ?>">

        <!-- <input type="hidden" name="editStorageLocation" id="editStorageLocation" value="">
        <input type="hidden" name="storage_location_id" value="<?= $row['storage_location_id'] ?>"> -->
        <div class="row">
          <!-- ************ old code ********** -->
          <!-- <div class="col-md-8">
            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog"
                aria-hidden="true"></i></button>
            <div id="accordion">

              <div class="card card-success">

                <div class="card-header cardHeader">
                  <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse"
                      href="#collapseThree"> Storage Location Details </a> </h4>
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
                          <label>Storage Location Name</label>
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
          </div> -->

          <!-- ******** new code ********* -->
          <div class="col-md-7">
            <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog"
                aria-hidden="true"></i></button>
            <div class="card">
              <div class="card-header p-3">
                <div class="row customer-info-head">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="head">
                      <i class="fa fa-info text-white"></i>
                      <h4 class="text-white pt-2">Storage Location Details</h4>
                    </div>
                  </div>
                </div>
              </div>
              <div id="collapseThree">
                <div class="card-body">
                  <div class="row">
                    <!-- <div class="col-md-6">
                      <div class="form-input">
                        <label for="date">Warehouse Name</label>
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
                    </div> -->
                    <div class="col-md-6">
                      <div class="form-input">
                        <label>Warehouse Name</label>
                        <select id="warehouseDropDown" name="warehouse"
                          class="form-control form-control-border borderColor">
                          <option value="" data-goodType="">Warehouse</option>
                          <?php
                              $warehouseList = $warehouseController->getAllWarehouse()['data'];
                              foreach ($warehouseList as $list) {
                              ?>

                          <option <?php if($list['warehouse_id']==$row['warehouse_id']){echo "selected";}?>
                            value="<?= $list['warehouse_id'] ?>" data-goodType=""><?= $list['warehouse_name'] ?>
                          </option>
                          <?php }
                              ?>
                        </select> </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-input">
                        <label>Storage Location Name</label>
                        <input type="text" name="name" class="form-control"
                          value="<?= $row['storage_location_name'] ?>">
                      </div>
                    </div>
                    <!-- <div class="col-md-6">
                      <div class="form-input">
                        <label>Storage Location Type</label>
                         <input type="text" name="type" class="form-control"
                            value="<?= $row['storage_location_type'] ?>">
                      </div>
                    </div> -->
                    <div class="col-md-6">
                      <div class="form-input">
                        <label>Storage Control</label>
                        <input type="text" name="storage_control" class="form-control"
                          value="<?= $row['storage_control'] ?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-input">
                        <label>Temp Control</label>
                        <input type="text" name="temp" class="form-control" value="<?= $row['temp_control'] ?>">
                      </div>
                    </div>
                       <div class="m-2 text-right">
        <button type="submit" class="btn btn-primary text-light my-3" value="add_post">Submit</button>
      </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-5" style="overflow-y: auto;">
            <div class="row">
              <table class="table table-hover table-nowrap stock-new-table transactional-book-table mt-n5">
                <thead style="position: sticky; top: 0;">
                  <tr>
                    <th colspan="3" style="text-align: center; font-size: 14px; background-color: #003060; border-top-left-radius: 12px;
            border-top-right-radius: 12px; color: white">Bin Current Changes</th>
                  </tr>
                  <tr>
                    <th>Storage Location Name</th>
                    <th>Storage Location Code</th>
                    <th>Warehouse</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $cond = "";
                  $sts = " AND 'status' !='deleted'";
                  $sqlList = "SELECT storage.* , erpWarehouse.warehouse_name FROM `erp_storage_location` as storage  LEFT JOIN erp_storage_warehouse as erpWarehouse ON erpWarehouse.warehouse_id = storage.warehouse_id WHERE 1 AND  storage.`company_id`=$company_id AND storage.`branch_id`=$branch_id AND storage.`location_id`=$location_id " . $cond . " " . $sts . "  ORDER BY storage.storage_location_id desc";
                  $sqlStorage =  $dbObject->queryGet($sqlList,true);
                  $numRows=$sqlStorage['numRows'];
                  // console($sqlStorage);
                  
                    if($numRows>0){
                          $sqlData=$sqlStorage['data'];
                          foreach($sqlData as $data){
                            ?>
                  <tr>
                    <td><?=$data['storage_location_name']?></td>
                    <td><?=$data['storage_location_code']?></td>
                    <td><?=$data['warehouse_name']?></td>

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

        </div>

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
} else if (isset($_GET["bom"]) && base64_decode($_GET["bom"]) > 0) {
  require_once("components/goods/create-bom.php");
} else {
    $url = BRANCH_URL . 'location/manage-storage-location.php';
    ?>
<script>
  window.location.href = "<?php echo $url; ?>";
</script>
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
      $("#createStorageLocation").val(data);
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