<?php
require_once("../../app/v1/connection-branch-admin.php");

//administratorLocationAuth();

require_once("../common/header.php");

require_once("../common/navbar.php");

require_once("../common/sidebar.php");

require_once("../common/pagination.php");

require_once("../../app/v1/functions/branch/func-goods-controller.php");

require_once("../../app/v1/functions/branch/func-bom-controller.php");

require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");

require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");



$goodsController = new GoodsController();
$BranchPoObj = new BranchPo();

$goodsBomController = new GoodsBomController();




// $funcList = $BranchPoObj->fetchFunctionality()['data'];
// console($funcList);


if (isset($_POST["creategoodsdata"])) {

  //console($_POST);
  $addNewObj = $goodsController->createGoods($_POST);

  if ($addNewObj["status"] == "success") {
    // console($_POST);
    // exit();
    swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"], BASE_URL . "branch/location/goods.php");
  } else {
    swalAlert($addNewObj["status"], ucfirst($addNewObj["status"]), $addNewObj["message"]);
  }

  //swalToast($addNewObj["status"], $addNewObj["message"], $_SERVER['PHP_SELF']);
}

if (isset($_POST["createLocationItem"])) {
  $addNewObj = $goodsController->createGoodsLocation($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}




if (isset($_POST["editgoodsdata"])) {
  $addNewObj = $goodsController->editGoods($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}



if (isset($_POST["add-table-settings"])) {
  $editDataObj = updateInsertTableSettings($_POST,  $_SESSION["logedBranchAdminInfo"]["adminId"]);
  swalToast($editDataObj["status"], $editDataObj["message"]);
}

?>
<link rel="stylesheet" href="<?= BASE_URL; ?>public/assets/listing.css">
<link rel="stylesheet" href="<?= BASE_URL; ?>public/assets/sales-order.css">
<link rel="stylesheet" href="<?= BASE_URL; ?>public/assets/accordion.css">
<link href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="../../public/assets-2/tree/css/style.css">
<link rel="stylesheet" href="../../public/assets-2/tree/css/treeSortable.css">
<style>
  #dragRoot {
    -moz-user-select: none;
    -ms-user-select: none;
    -webkit-user-select: none;
    user-select: none;
    cursor: default;
    margin: 10px;
    padding: 10px;
    overflow-y: scroll;
    white-space: nowrap;
  }

  #dragRoot ul {
    display: block;
    margin: 0;
    padding: 0 0 0 20px;
  }

  #dragRoot li {
    display: block;
    margin: 2px;
    padding: 2px 2px 2px 0;
  }

  #dragRoot li [class*="node"] {
    display: inline-block;
  }

  #dragRoot li [class*="node"].hover {
    background-color: navy;
    color: white;
  }

  #dragRoot li .node-facility {
    color: navy;
    font-weight: bold;
  }

  #dragRoot li .node-cpe {
    color: black;
    cursor: pointer;
  }

  #dragRoot li li {
    border-left: 1px solid silver;
  }

  #dragRoot li li:before {
    color: silver;
    font-weight: 300;
    content: "— ";
  }

  .item_desc {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 11px;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    border: 1px solid rgb(201 201 201);
    background-color: #fff;
    background-clip: padding-box;
    appearance: none;
    border-radius: 0.25rem;
    transition: box-shadow .15s ease-in-out;
  }

  .label-hidden {
    visibility: hidden;
  }

  .calculate-hsn-row {
    align-items: baseline;
    padding-right: 0;
  }

  .btn-transparent {
    position: absolute;
    top: 23px;
    left: 9px;
    height: 35px;
    z-index: 9;
    width: 92%;
    background: transparent !important;
  }

  .hsn-dropdown-modal .modal-dialog {
    max-width: 700px;
  }

  .hsn-dropdown-modal .modal-dialog .modal-header h4 {
    font-size: 15px;
    margin-bottom: 0;
    white-space: nowrap;
  }

  .hsn-dropdown-modal .modal-dialog .modal-header input {
    max-width: 300px;
    font-size: 12px;
    height: 30px;
    margin: 0;
    margin: 0;
    border: 1px solid #c3c3c3;
    box-shadow: none;
  }

  input.serachfilter-hsn {
    width: 40% !important;
  }

  .hsn-dropdown-modal .modal-body {
    overflow: hidden;
  }

  .hsn-dropdown-modal .modal-body .card {
    background: none;
  }

  .hsn-dropdown-modal .modal-body .card .card-body {
    background: #dbe5ee;
    box-shadow: 3px 5px 11px -1px #0000004d;
  }

  .hsn-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .hsn-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
  }

  .hsn-title h5 {
    margin-bottom: 0;
    font-size: 15px;
    font-weight: 600;
  }

  .tax-per p {
    font-size: 11px;
    font-style: italic;
    font-weight: 600;
    color: #343434;
  }

  .hsn-description p {
    font-size: 12px;
  }

  .highlight {
    background-color: yellow
  }

  .select2-container {
    width: 100% !important;
  }

  .hsn-modal-table tbody td {
    white-space: pre-line !important;
  }

  .hsn-modal-table tbody tr:nth-child(even) td {
    background-color: #b4c7d9;
  }

  .card-body.hsn-code div.dataTables_wrapper div.dataTables_filter,
  .dataTables_wrapper .row:nth-child(3) {
    display: flex;
    position: relative;
    top: 0;
    right: 0;
    justify-content: end;
    padding: 15px;
  }

  .card-body.hsn-code div.dataTables_wrapper div.dataTables_info {
    display: none;
  }

  .card-body.hsn-code div.dataTables_wrapper div.dataTables_filter input {
    margin-left: 0;
    display: inline-block;
    width: auto;
    padding-left: 30px;
    border: 1px solid #8f8f8f;
    color: #1B2559;
    height: 30px;
    border-radius: 8px;
    margin-left: 10px;
  }

  .row.calculate-row {
    justify-content: end;
  }

  .hsn-column {
    padding-right: 0;
  }


  .hsn-dropdown-modal .modal-body {

    max-height: 100%;

    height: 500px;

  }

  .hsn-dropdown-modal .icons-container {
    position: absolute;
    top: 18px;
    right: 0;
    bottom: 0;
    width: 70px;
    height: 30px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .icons-container i {
    color: #9b9b9b;
    font-size: 14px;
  }

  .icon-close {
    position: absolute;
    display: flex;
    align-items: center;
    gap: 5px;
    right: 30px;
  }

  .modal-content.card {
    box-shadow: 1px 1px 19px #4f4f4f;
  }

  p.hsn-description-info {
    /* display: none; */
    max-height: 60px;
    font-size: 10px !important;
    overflow: auto;
  }

  .unit-measure-col,
  .hsn-modal-col {
    border: 1px dashed #8192a3;
    padding-bottom: 11px;
    border-radius: 12px;
    width: 49%;
  }

  .row.basic-info-form-view {
    justify-content: center;
  }

  .dash-border-row {
    justify-content: space-between;
  }


  .serach-input-section button {
    position: absolute;
    border: none;
    display: block;
    width: 15px;
    height: 15px;
    line-height: 16px;
    font-size: 12px;
    border-radius: 50%;
    top: -47em;
    bottom: 0;
    right: 27px;
    margin: auto;
    background: #ddd;
    padding: 0;
    outline: none;
    cursor: pointer;
    transition: .1s;
  }

  .suggestion-item {
    background: #fff;
    margin: 5px 0;
    border-radius: 15px;
    padding: 10px 15px 10px;
    display: none;
  }

  .suggestion-item li {
    list-style: none;
    padding: 5px 15px 5px;
  }

  .add-btn-hsn {
    height: 50vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #my-link {
    color: gray;
    /* change color to indicate disabled state */
    cursor: default;
    /* remove pointer cursor */
    text-decoration: none;
    /* remove underline */
    opacity: 0.5;
    /* reduce opacity to further indicate disabled state */
  }

  .modal.add-new-hsn .modal-dialog {
    width: 40%;
    /* transform: translateY(30%); */
  }

  .modal.add-new-hsn .modal-dialog .modal-content {
    height: auto;
    border-radius: 12px;
    background: #dbe5ee;
  }

  .row.hsn-details .col {
    height: 75px;
  }

  .row.hsn-details .col .selct-hsn-type {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 2em;
    justify-content: flex-end;
  }

  .modal.add-new-hsn .modal-dialog .modal-body {
    display: flex;
    align-items: baseline;
    height: 250px;
    box-shadow: none;
  }

  .modal.add-new-hsn .modal-dialog .modal-footer {
    background: #b2c8db;
  }

  .goods-modal .modal-header {
    height: 226px;
  }

  .goods-modal.modal.fade.right .nav.nav-tabs li.nav-item a,
  .goods-modal.modal.fade.right .nav.nav-tabs li.nav-item a.active {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 10px 15px;
    border: 0 !important;
    border-radius: 10px 10px 0 0 !important;
  }

  .goods-modal.modal.fade.right .nav.nav-tabs li.nav-item a:hover {
    background: transparent;
    color: #fff;
    font-weight: 600;
    border: 0;
  }

  .goods-accordion button,
  .goods-accordion button:hover {
    border-radius: 5px 5px 0 0 !important;
    background: #fff !important;
    border: 1px solid #ccc !important;
    color: #000 !important;
    box-shadow: none !important;
  }

  .goods-accordion .accordion-body .card {
    border-radius: 0 0 5px 5px !important;
    background: #d9e6ff !important;
  }

  .goods-accordion .display-flex-space-between {
    display: flex;
    justify-content: flex-start;
    align-items: baseline;
    margin: 10px 0;
    gap: 10px;
  }

  ion-icon {
    color: #fff;
  }

 .display-flex-space-between .hamburger.show:hover .tab-content {
    filter: blur(5px) !important;
  }

 .display-flex-space-between #reminder.show {
    transform: translateX(-500%);
  }

 .display-flex-space-between #thumb.show {
    transform: translateX(-375%);
  }

 .display-flex-space-between #create.show {
    transform: translateX(-250%);
  }

 .display-flex-space-between #edit.show {
    transform: translateX(-125%);
  }

  .hamburger {
    font-size: 23px;
  }
  
  .blur-body .tab-content {
    filter: blur(0);
    transition: filter 0.5s ease-in-out;
    height: 100%;
  }

  .blur-body .tab-content.blur {
    filter: blur(2px);
  }

  .wrapper-action {
    display: flex;
    align-items: center;
    justify-content: center;
  }


  @media(max-width: 575px) {
    .hsn-column {
      padding-left: 0;
      padding-right: 15px;
    }

    .base-measure {
      padding-right: 15px !important;
    }

    .calculate-row .col {
      width: 20%;
      padding: 0;
    }

    .calculate-row .col input {
      width: 20px !important;
    }

    .calculate-parent-row .col:nth-child(1) {
      padding-left: 15px;
    }

    .calculate-row {
      padding: 0 15px;
      justify-content: center !important;
    }
  }
</style>
<?php
if (isset($_GET['create'])) {
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card">
          <div class="modal-header card-header py-2 px-3">
            <h4 class="modal-title font-monospace text-md text-white" id="exampleModalLabel"><i class="fa fa-info"></i>&nbsp;Notes</h4>
            <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div id="notesModalBody" class="modal-body card-body">
          </div>
        </div>
      </div>
    </div>

    <!-- Content Header (Page header) -->

    <div class="content-header">

      <?php if (isset($msg)) { ?>

        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">

          <?= $msg ?>

        </div>

      <?php } ?>

      <div class="container-fluid">

        <ol class="breadcrumb">

          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Item List</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Item</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>

      </div>
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="goodsSubmitForm" name="goodsSubmitForm">
          <input type="hidden" name="creategoodsdata" id="creategoodsdata" value="">

          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">
                    <div class="card-header">
                      <h4>Classification
                        <span class="text-danger">*</span>
                      </h4>
                    </div>
                    <div class="card-body goods-card-body others-info vendor-info so-card-body classification-card-body">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="row goods-info-form-view customer-info-form-view">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="form-input">
                                <select id="goodTypeDropDown" name="goodsType" class="form-control">
                                  <option value="">Select Item Type</option>
                                </select>
                              </div>
                            </div>
                            <!-- <span id = "asset_cl"> -->
                            <div class="col-lg-6 col-md-6 col-sm-6" id="asset_classification" style="display:none;">
                              <div class="form-input">
                                <select id="asset_classification_select" name="asset_classification[]" class="form-control asset_classificationDropDown" data-classattr="asset_classification_new">
                                  <option value="">Select Asset Classification</option>
                                  <?php
                                  $asset_class = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `wdv` IS NOT NULL AND `slm` IS NOT NULL ", true);

                                  foreach ($asset_class['data'] as $data) {
                                  ?>
                                    <option value="<?= $data['depreciation_id'] ?>"><?= $data['asset_class'] ?></option>
                                  <?php
                                  }
                                  ?>

                                </select>
                              </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6" id="asset_gl" style="display:none;">
                              <div class="form-input">
                                <label>GL Code </label>



                                <select id="glCodeAsset" name="glCodeAsset" class="form-control">
                                  <option value="">SELECT GL Code</option>

                                </select>
                              </div>
                            </div>


                            <!-- <span id="asset_classification_new" class="asset_classification_new" style="display:none; display: inline-flex;  ">

                            </span> -->
                            <!-- </span> -->

                            <div class="col-lg-6 col-md-6 col-sm-6" id="goodsGroup" style="display:none;">
                              <div class="form-input">
                                <select id="goodGroupDropDown" name="goodsGroup[]" class="form-control" data-classattr="group_parent_new">
                                  <option value="">Select Group</option>
                                </select>
                              </div>
                            </div>

                            <span class="group_parent_new" style="display:none; display: inline-flex;  ">

                            </span>


                            <div class="col-lg-6 col-md-6 col-sm-6" id="purchaseGroup" style="display:none;">
                              <div class="form-input">
                                <select id="purchaseGroupDropDown" name="purchaseGroup" class="form-control">
                                  <option value="">Select Purchase Group</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6" id="availability" style="display:none;">
                              <div class="form-input">
                                <select id="avl_check" name="availabilityCheck" class="form-control">
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

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-inline float-right" id="bomCheckBoxDiv">


                              </div>
                              <div class="form-inline float-right" id="bomRadioDiv">



                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>
              <div class="row" id="storageDetails" style="display: none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Storage Details</h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Storage Control</label>

                                <input type="text" name="storageControl" class="form-control">

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Max Storage Period</label>

                                <input type="text" name="maxStoragePeriod" class="form-control">

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">
                              <div class="form-input">
                                <label class="label-hidden" for="">Min Time Unit</label>
                                <select id="minTime" name="minTime" class="select2 form-control">
                                  <option value="">Min Time Unit</option>
                                  <option value="Day">Day</option>
                                  <option value="Month">Month</option>
                                  <option value="Hours">Hours</option>

                                </select>
                              </div>
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Minimum Remain Self life</label>

                                <input type="text" name="minRemainSelfLife" class="form-control">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="form-input">
                                <label class="label-hidden" for="">Max Time Unit</label>
                                <select id="maxTime" name="maxTime" class="select2 form-control">
                                  <option value="">Max Time Unit</option>
                                  <option value="Day">Day</option>
                                  <option value="Month">Month</option>
                                  <option value="Hours">Hours</option>

                                </select>
                              </div>
                            </div>

                          </div>

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for=""> Minimum Stock</label>

                                  <input step="0.01" type="number" name="min_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2">

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for=""> Maximum Stock </label>

                                  <input step="0.01" type="number" name="max_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2">

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




              <div class="row" id="serviceStock" style="display:none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Service Stock</h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-3 col-md-3 col-sm-3">

                              <div class="form-input">

                                <label for=""> Service Quantity</label>

                                <input step="0.01" type="number" name="service_stock" id="service_stock" class="form-control stock" id="exampleInputBorderWidth2" value="0">

                              </div>

                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3">

                              <div class="form-input">

                                <label for="">Service Unit Price</label><label id="buom_per"> </label>

                                <input step="0.01" type="number" name="service_rate" id="service_rate" class="form-control rate" id="exampleInputBorderWidth2" value="0">

                              </div>

                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3">

                              <div class="form-input">

                                <label for="">Value</label>

                                <input step="0.01" type="number" name="service_total" id="service_total" class="form-control total" id="exampleInputBorderWidth2" value="0" readonly>

                              </div>

                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-3">

                              <div class="form-input">

                                <label for=""> Dated on </label>

                                <input type="date" name="service_stock_date" id="service_stock_date" class="form-control stock" id="exampleInputBorderWidth2">

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

            <div class="col-lg-6 col-md-6 col-sm-6">

              <div class="row" id="basicDetails" style="display: none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Basic Details

                        <span class="text-danger">*</span>

                      </h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view basic-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Item Name</label>

                                <input type="text" name="itemName" class="form-control item_name" id="exampleInputBorderWidth2">
                                <ul class="suggestion-item" id="suggestedNames">

                                </ul>

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Additional Description</label>

                                <textarea class="item_desc" rows="3" name="itemDesc" id="exampleInputBorderWidth2" placeholder="Additional Description"></textarea>

                              </div>

                            </div>


                            <div class="row">

                              <div class="col-12">

                                <div class="row dash-border-row">

                                  <div class="col-lg-6 col-md-6 col-sm-6 unit-measure-col">

                                    <div class="row mb-4">

                                      <div class="col-lg-6 col-md-6 col-sm-6 col pr-0 base-measure">

                                        <div class="form-input">

                                          <label>Base UOM</label>

                                          <select id="buomDrop" name="baseUnitMeasure" class="form-control">

                                            <option value="">Base Unit of Measurement</option>

                                            <?php

                                            $uomList = $goodsController->fetchUom()['data'];



                                            foreach ($uomList as $oneUomList) {

                                            ?>

                                              <option value="<?= $oneUomList['uomId'] ?>"><?= $oneUomList['uomName'] ?> </option>

                                            <?php

                                            }

                                            ?>

                                          </select>

                                        </div>

                                      </div>

                                      <div class="col-lg-6 col-md-6 col-sm-6 col">

                                        <div class="form-input">

                                          <label>Alternate UOM</label>

                                          <select id="iuomDrop" name="issueUnit" class="form-control">

                                            <option value="">Alternate Unit of Measurement</option>

                                            <?php

                                            $uomList = $goodsController->fetchUom()['data'];



                                            foreach ($uomList as $oneUomList) {

                                            ?>

                                              <option value="<?= $oneUomList['uomId'] ?>"><?= $oneUomList['uomName'] ?></option>

                                            <?php

                                            }

                                            ?>

                                          </select>

                                        </div>

                                      </div>

                                    </div>

                                    <div class="row calculate-row">

                                      <div class="col-lg-1 col-md-1 col-sm-1 col p-0">

                                        <input type="text" class="form-control bg-none p-0" placeholder="1" readonly>

                                      </div>

                                      <div class="col-lg-3 col-md-3 col-sm-3 col">

                                        <!-- <input type="text" name="netWeight" class="form-control bg-none p-0" id="buom" placeholder="unit" readonly> -->
                                        <input type="text" name="netWeight" class="form-control bg-none p-0" id="buom" value="unit" readonly>

                                      </div>

                                      <div class="col-lg-1 col-md-1 col-sm-1 col">

                                        <p class="equal-style mt-1">=</p>

                                      </div>

                                      <div class="col-lg-3 col-md-3 col-sm-3 col">

                                        <input type="text" name="rel" class="form-control item_rel" id="rel">

                                      </div>

                                      <div class="col-lg-3 col-md-3 col-sm-3 col">

                                        <!-- <input type="text" name="netWeight" class="form-control bg-none p-0" placeholder="unit" id="ioum" readonly> -->
                                        <input type="text" name="netWeight" id="ioum" class="form-control bg-none p-0" value="unit" readonly>

                                      </div>

                                    </div>

                                  </div>

                                  <div class="col-lg-6 col-md-6 col-sm-6 hsn-modal-col">

                                    <div class="row calculate-parent-row mb-4">

                                      <div class="col-lg-12 col-md-12 col-sm-12 col hsn-column pr-3">

                                        <div class="form-input">

                                          <label>HSN </label>

                                          <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#goodsHSNModal"></button>

                                          <select id="hsnDropDown" name="" class="form-control">

                                            <option id="hsnlabelOne" value="">HSN</option>

                                          </select>

                                        </div>

                                      </div>

                                    </div>




                                    <div class="row calculate-hsn-row mt-3 mb-2">

                                      <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="form-input">

                                          <!-- <label class="label-hidden">HSN</label> -->

                                          <p class="hsn-description-info" id="hsnDescInfo"></p>

                                        </div>

                                      </div>

                                    </div>

                                  </div>


                                  <div class="col-lg-12 col-md-12 col-sm-12">

                                    <div class="form-input" style="display:none;" id="mwp">

                                      <label for="">Moving Weighted Price</label><label id="buom_per"> </label>

                                      <input step="0.01" type="number" name="rate" id="rate" class="form-control rate" id="exampleInputBorderWidth2" value="0">

                                    </div>

                                  </div>

                                </div>

                              </div>

                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="cost-center" id="cost-center" style="display:none;">
                                <label for="">Cost Center</label>
                                <select id="cost_center" name="costCenter" class="form-control">
                                  <option value="">Cost Center</option>
                                  <?php
                                  $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                  foreach ($funcList as $func) {
                                  ?>
                                    <option value="<?= $func['CostCenter_id'] ?>">
                                      <?= $func['CostCenter_code'] ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="cost-center" id="depKey" style="display:none;">
                                <label for="">Asset Depreciation Key <span id="despkey_id"></span></label>
                                <input type="hidden" id="dep_key_val" name="dep_key">

                                </select>
                              </div>
                            </div>










                            <div class="row othe-cost-infor modal-add-row_537">
                              <div class="row othe-cost-infor pl-0 pr-0">

                                <div class="col-lg-5 col-md-5 col-sm-5">

                                  <div class="form-input">

                                    <label>Specification</label>

                                    <input type="text" name="spec[1][spec_name]" data-attr="1" class="form-control spec_vldtn specification_1" id="">

                                  </div>

                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5">

                                  <div class="form-input">

                                    <label>Specification Details</label>

                                    <input type="text" name="spec[1][spec_detail]" data-attr="1" class="form-control spec_dtls_vldtn specificationDetails_1" id="">

                                  </div>

                                </div>



                                <div class="col-lg col-md-2 col-sm-2">
                                  <div class="add-btn-plus">
                                    <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                      <i class="fa fa-plus"></i>
                                    </a>
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


              </div>

              <div class="row" id="pricing" style="display:none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Pricing and Discount

                        <span class="text-danger">*</span>

                      </h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Target price</label>

                                <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Max Discount (%)</label>

                                <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

              <div class="row" id="service_sales_details" style="display: none;">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Service Details

                        <span class="text-danger">*</span>

                      </h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Service Name</label>

                                <input type="text" name="serviceName" class="form-control item_name service_name" id="exampleInputBorderWidth2">

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Service Description</label>

                                <textarea class="item_desc service_desc" rows="3" name="serviceDesc" id="exampleInputBorderWidth2" placeholder="Service Description"></textarea>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>HSN </label>

                                <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#goodsHSNModal"></button>

                                <select id="hsnDropDown" name="" class="form-control servicehsnDropDown">

                                  <option id="hsnlabelservice" value="">HSN</option>

                                </select>

                              </div>

                            </div>


                            <div class="col-lg-6 col-md-6 col-sm-6" id="tds" style="display:none;">

                              <div class="form-input">

                                <label>TDS </label>

                                <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#tdsmodal"></button>

                                <select id="tdsDropDown" name="tds" class="form-control">
                                  <option id="tdslabel" value="">SELECT TDS</option>
                                  <!-- <?php
                                        $tds_sql = queryGet("SELECT * FROM `erp_tds_details`", true);
                                        $tds_data = $tds_sql['data'];
                                        foreach ($tds_data as $tds) {
                                        ?>
                                    <option id="" value="<?= $tds['id'] ?>"><?= $tds['section'] . "[nature-" . $tds['natureOfTransaction'] . ", threshold-" . $tds['thresholdLimit'] . "]" ?></option>
                                  <?php
                                        }
                                  ?> -->



                                </select>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>GL Code </label>



                                <select id="glCode" name="glCode" class="form-control">
                                  <option value="">SELECT GL Code</option>

                                </select>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>Service Unit</label>

                                <input type="text" name="serviceUnit" class="form-control service_unit" id="exampleInputBorderWidth2">

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


            <div class="col-lg-12 col-md-12 col-sm-12">

              <div class="card goods-creation-card so-creation-card po-creation-card" id="specificationDetails" style="height: auto; display: none;">

                <div class="card-header">

                  <h4>Specification Details

                    <!-- <span class="text-danger">*</span> -->

                  </h4>

                </div>

                <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                  <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12">

                      <div class="row goods-info-form-view customer-info-form-view">


                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Net Weight</label>

                            <input step="0.01" type="number" name="netWeight" class="form-control net_weight" id="net_weight">

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">
                            <label class="" for="">Net Weight Unit</label>
                            <select name="net_unit" class="form-control " id="net_unit">
                              <option value="">Select net weight unit</option>
                              <option value="kg">kg</option>
                              <option value="g">g</option>
                              <option value="ton">ton</option>

                            </select>
                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Gross Weight</label>

                            <input step="0.01" type="number" name="grossWeight" class="form-control gross_weight" id="gross_weight">
                            <span id="gross_span"></span>

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">
                            <label class="" for="">Gross Weight Unit</label>
                            <select name="gross_unit" class="form-control " id="gross_unit" disabled="">
                              <option value="">Select gross weight unit</option>
                              <option value="kg">kg</option>
                              <option value="g">g</option>
                              <option value="ton">ton</option>

                            </select>
                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Height</label>

                            <input step="0.01" type="number" name="height" class="form-control calculate_volume" id="height">

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Width</label>

                            <input step="0.01" type="number" name="width" class="form-control calculate_volume" id="width">

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">

                            <label for="">Length</label>

                            <input step="0.01" type="number" name="length" class="form-control calculate_volume" id="length">

                          </div>

                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">

                          <div class="form-input">
                            <label class="" for="">Unit</label>
                            <select name="measure_unit" class="form-control volume_unit" id="volume_unit">
                              <option value="">Select</option>
                              <option value="cm">cm</option>
                              <option value="m">m</option>

                            </select>
                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">

                            <label>Volume In CM<sup>3</sup></label>

                            <input type="text" name="volumeCubeCm" class="form-control" id="volcm" readonly="">

                          </div>

                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6">

                          <div class="form-input">

                            <label>Volume In M<sup>3</sup></label>

                            <input type="text" name="volume" class="form-control" id="volm" readonly="">

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>



            <!-----hsn modal start------->


            <div class="modal fade hsn-dropdown-modal" id="goodsHSNModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4>Choose HSN</h4>
                    <!-- <b id="hsnLoding"></b> -->
                    <!-- <input class="form-control" id="searchbar" type="text" name="search" placeholder="Search.."> -->

                    <div class="section serach-input-section">
                      <input type="text" class="dataTables_filter" id="searchbar" placeholder="" class="field serachfilter-hsn form-control">
                      <button type="reset">&times;</button>
                      <!-- <div class="icons-container">
                        <div class="icon-close">
                          <i class="fa fa-spinner fa-spin hsnSearchSpinner"></i>
                          <i style="cursor: pointer" type="reset" class="fa fa-times hsnSearchclear"></i>
                        </div>
                        <div class="icon-search">
                          <i class="fa fa-search" id="myBtn"></i>
                          <script>
                            var input = document.getElementById("myInput");
                            input.addEventListener("keypress", function(event) {
                              if (event.key === "Enter") {
                                event.preventDefault();
                                document.getElementById("myBtn").click();
                              }
                            });
                            $(".hsnSearchclear").click(function() {
                              $("#searchbar").val('');
                            });
                          </script>
                        </div>
                      </div> -->
                    </div>

                  </div>
                  <div class="modal-body">

                    <div class="card">

                      <div class="card-body m-0 p-0 hsn-code">

                        <div class="hsn-list" style="height: 500px; overflow-y: scroll;" id="myPopup">

                          <table class="table table-hover hsn-modal-table" id="myPopupTable">
                            <thead>
                              <th></th>
                              <th>Code</th>
                              <th>Description</th>
                              <th>Rate</th>
                            </thead>
                            <tbody class="hsn_tbody">

                            </tbody>
                          </table>

                        </div>
                      </div>
                      <div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="hsnsavebtn" data-dismiss="modal">Select</button>
                  </div>
                </div>
              </div>
            </div>


            <!-----hsn modal end------->

            <script>
              var popup = $('#myPopup');
              var popupTable = $('#myPopupTable');
              popup.scroll(function() {
                console.log(popup.scrollTop());
                console.log(popupTable[0].scrollHeight * 0.9);

                if (popup.scrollTop() >= popupTable[0].scrollHeight * 0.9) {
                  // Load AJAX content
                  // $.ajax({
                  //   url: 'ajax/content.html',
                  //   success: function(data) {
                  //     // Insert the content into the popup container


                  //     popup.append(data);
                  //   }
                  // });
                  console.log('trdx');
                } else {
                  console.log('trdx22');


                }
              });

              // $("#myPopup").scroll(function() {
              //   console.log($("#myPopup").scrollTop());
              //   console.log($("#myPopup").height());

              //   console.log($("#myPopup").scrollTop() + $("#myPopup").height());
              //   console.log($("#myPopup").height() * 0.8);
              //   if ($("#myPopup").scrollTop() + $("#myPopup").height() <= $("#myPopup").height() * 0.8) {
              //     // Load Ajax content l
              //     console.log('cgfc');
              //   } else {
              //     console.log('test');
              //   }
              // });
            </script>


            <!-----tds modal start------->


            <div class="modal fade hsn-dropdown-modal" id="tdsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                <div class="modal-content">
                  <div class="modal-body" style="height: 500px; overflow: auto;">
                    <h4 class="text-sm pl-2">Choose TDS</h4>
                    <div class="card">
                      <div class="card-body m-2 p-0 hsn-code">
                        <table class="table defaultDataTable table-hover tds-modal-table hsn-modal-table ">
                          <thead>
                            <th></th>
                            <th>Section</th>
                            <th>Nature</th>
                            <th>Threshold</th>
                            <th>Rate</th>
                          </thead>
                          <tbody>
                            <?php
                            $tds_sql = queryGet("SELECT * FROM `erp_tds_details`", true);
                            $tds_data = $tds_sql['data'];
                            foreach ($tds_data as $tds) {
                              // console($hsn); 
                            ?>
                              <tr>
                                <td> <input type="radio" id="tds" name="tds" data-attr="<?= $tds['section']  ?>" value="<?= $tds['id']  ?>"></td>
                                <td>
                                  <p id="section_<?= $tds['id'] ?>"><?= $tds['section'] ?></p>
                                </td>
                                <td>
                                  <p id="nature_<?= $tds['id'] ?>"><?= $tds['natureOfTransaction'] ?></p>
                                </td>
                                <td>
                                  <p id="threshold<?= $tds['id'] ?>"><?= $tds['thresholdLimit'] ?></p>
                                </td>
                                <td>
                                  <p id="rate<?= $tds['id'] ?>"><?= $tds['TDSRate'] ?>%</p>
                                </td>
                              </tr>
                            <?php
                            }
                            ?>
                          </tbody>
                        </table>
                        <!-- <div class="hsn-header">
                            <div class="hsn-title">
                              <input type="radio" id="hsn" name="hsn" value="<?= $hsn['hsnCode']  ?>">
                              <h5 id="hsnCode_<?= $hsn['hsnId'] ?>"><?= $hsn['hsnCode'] ?></h5>
                            </div>
                            <div class="tax-per">
                              <p id="taxPercentage_<?= $hsn['hsnId'] ?>"><?= $hsn['taxPercentage'] ?>%</p>
                            </div>
                          </div>
                          <div class="hsn-description">
                            <p id="hsnDescription_<?= $hsn['hsnId'] ?>"><?= $hsn['hsnDescription'] ?></p>
                          </div>-->


                      </div>






                      <div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="tdssavebtn" data-dismiss="modal">Save changes</button>
                  </div>
                </div>
              </div>
            </div>


            <!-----tds modal end------->


            <div class="btn-section mt-2 mb-2">

              <button class="btn btn-primary save-close-btn btn-xs float-right add_data" id="submit_btn" value="add_post" style="display:none;">Submit</button>

              <!-- <button class="btn btn-danger save-close-btn btn-xs float-right add_data" id="draft_btn" value="add_draft" style="display:none;">Save as Draft</button> -->

            </div>
          </div>



        </form>



        <!-- modal -->

        <div class="modal" id="addNewGoodTypesFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title text-white">Add New Item Type</h4>

                <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewGoodTypesForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="goodTypeName" class="form-control">

                      <label>Type Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="goodTypeDesc" class="form-control">

                      <label>Type Description</label>

                    </div>

                  </div>

                  <div class="col-md-12 flex-radio" style="display: flex; align-items: center; gap: 5px;">

                    <input type="radio" name="type" value="RM" style="margin-bottom: 0; width: auto; padding-right: 5px;">Raw Material

                    <input type="radio" name="type" value="SFG" style="margin-bottom: 0; width: auto; padding-right: 5px;">Semi Finished Good

                    <input type="radio" name="type" value="FG" style="margin-bottom: 0; width: auto; padding-right: 5px;">Finished Good

                  </div>

                  <div class="col-md-12">

                    <div class="input-group btn-col">

                      <button type="submit" id="addNewGoodTypesFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                    </div>

                  </div>

                </form>

              </div>

            </div>

          </div>

        </div>

        <!-- modal end -->
      </div>

      <!-- end --->




      <!-- modal -->


      <div class="modal fade  addNewGoodGroup addNewGoodGroupFormModal" id="addNewGoodGroupFormModal">
        <div class="modal-dialog" role="document">
          <div class="modal-content card">
            <div class="modal-header card-header p-3">
              <h4 class="modal-title" id="exampleModalLabel">Add Group</h4>

            </div>
            <form action="" method="post" id="addNewGoodGroupForm">

              <div class="modal-body card-body">

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <label>Group Name</label>

                    <input type="text" name="goodGroupName" class="form-control goodGroupName">


                  </div>

                </div>

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <label>Select Parent</label>
                    <select id="parent_group_dropdown" class="form-control" name="group_parent">



                    </select>
                  </div>

                </div>

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <input type="text" id="goodType_input" name="goodType_name" class="form-control" readonly>

                    <input type="hidden" id="goodType_id" name="goodType_id" class="form-control" readonly>

                  </div>

                </div>

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <label>Group Description</label>

                    <input type="text" name="goodGroupDesc" class="form-control goodGroupDesc">

                  </div>

                </div>

              </div>

              <div class="modal-footer">

                <div class="input-group btn-col">

                  <button type="submit" id="addNewGoodGroupFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                </div>

              </div>

            </form>

          </div>
        </div>
      </div>






      <!-- modal end -->


      <!---uom modal-->



      <div class="modal fade addNewUOM addNewUOMFormModal" id="addNewUOMFormModal">
        <div class="modal-dialog" role="document">
          <div class="modal-content card">
            <div class="modal-header card-header p-3">
              <h4 class="modal-title" id="exampleModalLabel">Add UOM</h4>

            </div>
            <form action="" method="post" id="addNewUOMForm">
              <input type="hidden" name="uomType" value="material" readonly>

              <div class="modal-body card-body">

                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <label>UOM Name</label>

                    <input type="text" name="uomName" class="form-control uomName">


                  </div>

                </div>



                <div class="col-md-12">

                  <div class="form-input mb-2">

                    <label>UOM Description</label>

                    <input type="text" name="uomDesc" class="form-control uomDesc">

                  </div>

                </div>

              </div>

              <div class="modal-footer">

                <div class="input-group btn-col">

                  <button type="submit" id="addNewUOMFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                </div>

              </div>

            </form>

          </div>
        </div>
      </div>



      <!---end uom modal---->







      <!-- purchase group -->



      <!-- modal -->

      <div class="modal" id="addNewPurchaseGroupFormModal">

        <div class="modal-dialog">

          <div class="modal-content">

            <div class="modal-header py-1" style="background-color: #003060; color:white;">

              <h4 class="modal-title text-white">Add New Purchase Group</h4>

              <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>

            <div class="modal-body">

              <form action="" method="post" id="addNewPurchaseGroupForm">

                <div class="col-md-12 mb-3">

                  <div class="input-group">

                    <input type="text" name="purchaseGroupName" class="form-control">

                    <label>Purchase Group Name</label>

                  </div>

                </div>

                <div class="col-md-12">

                  <div class="input-group">

                    <input type="text" name="purchaseGroupDesc" class="form-control">

                    <label>Purchase Group Description</label>

                  </div>

                </div>

                <div class="col-md-12">

                  <div class="input-group btn-col">

                    <button type="submit" id="addNewPurchaseGroupFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                  </div>

                </div>

              </form>

            </div>

          </div>

        </div>

      </div>

      <!-- modal end -->



      <!-- end purchase group -->

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
    <div class="content-header">
      <?php if (isset($msg)) { ?>
        <div style="z-index: 999; float:right" class="mx-3 p-1 alert-success rounded">
          <?= $msg ?>
        </div>
      <?php } ?>
      <div class="container-fluid">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Item List</a></li>
          <li class="breadcrumb-item"><a class="text-dark"><i class="fa fa-edit po-list-icon"></i> Edit Item</a></li>
          <li class="back-button">
            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">
              <i class="fa fa-reply po-list-icon"></i>
            </a>
          </li>
        </ol>
      </div>
    </div>
    <!-- /.content-header -->
    <?php
    $itemId = base64_decode($_GET['edit']);
    $sql = "SELECT * FROM `erp_inventory_items` as item LEFT JOIN `erp_inventory_mstr_good_types` as type ON item.goodsType= type.goodTypeId   LEFT JOIN `erp_inventory_mstr_purchase_groups` as purchase ON item.purchaseGroup = purchase.purchaseGroupId LEFT JOIN `erp_inventory_mstr_good_groups` as groups ON item.goodsGroup= groups.goodGroupId WHERE  `item`.`itemId` = $itemId";

    $resultObj = queryGet($sql);
    $row = $resultObj["data"];
    //console($row);

    ?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="goodsEditForm" name="goodsEditForm">
          <input type="hidden" name="editgoodsdata" id="editgoodsdata" value="<?= $itemId ?>">
          <input type="hidden" name="goodsType" value="<?= $row['goodsType'] ?>">
          <input type="hidden" name="id" value="<?= $itemId ?>">






          <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">
                    <div class="card-header">
                      <h4>Classification
                        <span class="text-danger">*</span>
                      </h4>
                    </div>
                    <div class="card-body goods-card-body others-info vendor-info so-card-body classification-card-body">
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="row goods-info-form-view customer-info-form-view">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <div class="form-input">
                                <select id="" name="goodsType" class="form-control goodTypeDropDown" disabled>
                                  <option value=""><?= $row['type'] ?></option>

                                </select>
                              </div>
                            </div>
                            <?php
                            if ($row['goodsType'] != 9) {
                            ?>
                              <div class="col-lg-6 col-md-6 col-sm-6" id="goodsGroup" disabled>
                                <div class="form-input">
                                  <select id="" name="goodsGroup" class="form-control">
                                    <!-- <option value="">Select Group</option> -->
                                    <?php
                                    $good_group = queryGet("SELECT * FROM `erp_inventory_mstr_good_groups` WHERE `goodType`='" . $row['goodsType'] . "' AND `companyId`=$company_id", true);
                                    foreach ($good_group['data'] as $goodsgroup) {
                                    ?>

                                      <option value="<?= $goodsgroup['goodGroupId'] ?>" <?php if ($goodsgroup['goodGroupId'] == $row['goodsGroup']) {
                                                                                          echo "selected";
                                                                                        } ?>><?php echo $goodsgroup['goodGroupName']; ?></option>

                                    <?php
                                    }
                                    ?>

                                  </select>
                                </div>
                              </div>
                            <?php
                            }
                            ?>
                            <?php
                            if ($row['goodsType'] != 7 && $row['goodsType'] != 5 && $row['goodsType'] != 9) {
                              //  echo 1;
                            ?>
                              <div class="col-lg-6 col-md-6 col-sm-6" id="purchaseGroup" disabled>
                                <div class="form-input">
                                  <select id="" name="purchaseGroup" class="form-control">
                                    <!-- <option value="">Select Purchase Group</option> -->
                                    <?php
                                    $purchase_group = queryGet("SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE`companyId`=$company_id", true);
                                    foreach ($purchase_group['data'] as $purchasesgroup) {
                                    ?>

                                      <option value="<?= $purchasesgroup['purchaseGroupId'] ?>" <?php if ($purchasesgroup['purchaseGroupId'] == $row['purchaseGroup']) {
                                                                                                  echo "selected";
                                                                                                } ?>><?php echo $purchasesgroup['purchaseGroupName']; ?></option>

                                    <?php
                                    }
                                    ?>

                                  </select>
                                </div>
                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6" id="availability">
                                <div class="form-input">
                                  <select id="avl_check" name="availabilityCheck" class="form-control">
                                    <option value="">Availability Check</option>
                                    <option value="Daily" <?php if ($row['availabilityCheck'] == "Daily") {
                                                            echo "selected";
                                                          } ?>>Daily</option>
                                    <option value="Weekly" <?php if ($row['availabilityCheck'] == "Weekly") {
                                                              echo "selected";
                                                            } ?>>Weekly</option>
                                    <option value="By Weekly" <?php if ($row['availabilityCheck'] == "By Weekly") {
                                                                echo "selected";
                                                              } ?>>By Weekly</option>
                                    <option value="Monthly" <?php if ($row['availabilityCheck'] == "Monthly") {
                                                              echo "selected";
                                                            } ?>>Monthly</option>
                                    <option value="Qtr" <?php if ($row['availabilityCheck'] == "Qtr") {
                                                          echo "selected";
                                                        } ?>>Qtr</option>
                                    <option value="Half Y" <?php if ($row['availabilityCheck'] == "Half Y") {
                                                              echo "selected";
                                                            } ?>>Half Y</option>
                                    <option value="Year" <?php if ($row['availabilityCheck'] == "Year") {
                                                            echo "selected";
                                                          } ?>>Year</option>
                                  </select>
                                </div>

                              </div>
                              <?php
                            } elseif ($row['goodsType'] == 9) {
                              $array_ex = (explode(",", $row['asset_classes']));
                              // console($array_ex);
                              foreach ($array_ex as $arr) {
                                $clas_sql = queryGet("SELECT * FROM `erp_depreciation_table` WHERE `depreciation_id`=$arr");

                              ?>
                                <div class="col-lg-6 col-md-6 col-sm-6 asset_classification_edit" id="asset_classification_edit">
                                  <div class="form-input">
                                    <input type="text" name="" class="form-control" value="<?= $clas_sql['data']['asset_class'] ?>" readonly>
                                  </div>
                                </div>

                                <span id="asset_classification_new" class="asset_classification_new" style="display:none;">
                                </span>
                            <?php
                              }
                            }
                            ?>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-inline float-right" id="bomCheckBoxDiv">


                              </div>
                              <div class="form-inline float-right" id="bomRadioDiv">



                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>
              <?php
              if ($row['goodsType'] != 7 && $row['goodsType'] != 5) {
                $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "`  WHERE `item_id`=$itemId");
                // console($storage_sql);
              ?>
                <div class="row" id="storageDetails">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Storage Details</h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Storage Control</label>

                                  <input type="text" name="storageControl" class="form-control" value="<?= $storage_sql['data']['storageControl'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Max Storage Period</label>

                                  <input type="text" name="maxStoragePeriod" class="form-control" value="<?= $storage_sql['data']['maxStoragePeriod'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="form-input">
                                  <label class="label-hidden" for="">Min Time Unit</label>
                                  <select id="minTime" name="minTime" class="select2 form-control">
                                    <option value="">Min Time Unit</option>
                                    <option value="Day" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Day") {
                                                          echo "selected";
                                                        }  ?>>Day</option>
                                    <option value="Month" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Month") {
                                                            echo "selected";
                                                          }  ?>>Month</option>
                                    <option value="Hours" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Hours") {
                                                            echo "selected";
                                                          }  ?>>Hours</option>

                                  </select>
                                </div>
                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for="">Minimum Remain Self life</label>

                                  <input type="text" name="minRemainSelfLife" class="form-control" value="<?= $storage_sql['data']['minRemainSelfLife'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="form-input">
                                  <label class="label-hidden" for="">Max Time Unit</label>
                                  <select id="maxTime" name="maxTime" class="select2 form-control">
                                    <option value="">Max Time Unit</option>
                                    <option value="Day" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Day") {
                                                          echo "selected";
                                                        }  ?>>Day</option>
                                    <option value="Month" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Month") {
                                                            echo "selected";
                                                          }  ?>>Month</option>
                                    <option value="Hours" <?php if ($storage_sql['data']['maxStoragePeriodTimeUnit'] == "Hours") {
                                                            echo "selected";
                                                          }  ?>>Hours</option>

                                  </select>
                                </div>
                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              <?php
              }
              if ($row['goodsType'] != 7 && $row['goodsType'] != 5) {

                $stock_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='" . $row['itemId'] . "' AND `location_id`=$location_id");
                // console($stock_sql);
              ?>
                <div class="row" id="stockRate">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Stock Position</h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for=""> Stock Quantity</label>

                                  <input step="0.01" type="number" name="stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['itemTotalQty'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Unit Price</label><label id="buom_per"> </label>

                                  <input step="0.01" type="number" name="rate" id="rate" class="form-control rate" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['movingWeightedPrice'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for="">Value</label>

                                  <input step="0.01" type="number" name="total" id="total" class="form-control total" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['movingWeightedPrice'] * $stock_sql['data']['itemTotalQty'] ?>">

                                </div>

                              </div>

                            </div>

                          </div>

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for=""> Minimum Stock</label>

                                  <input step="0.01" type="number" name="min_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['min_stock'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for=""> Maximum Stock </label>

                                  <input step="0.01" type="number" name="max_stock" id="stock" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['max_stock'] ?>">

                                </div>

                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-4">

                                <div class="form-input">

                                  <label for=""> Stock dated on </label>

                                  <input type="date" name="stock_date" id="stock_date" class="form-control stock" id="exampleInputBorderWidth2" value="<?= $stock_sql['data']['stock_date'] ?>">

                                </div>

                              </div>
                            </div>
                          </div>



                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              <?php
              }
              ?>


              <?php
              if ($row['goodsType'] == 7 || $row['goodsType'] == 5) {
              ?>
                <div class="row" id="serviceStock">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Service Stock</h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-3 col-md-3 col-sm-3">

                                <div class="form-input">

                                  <label for=""> Service Quantity</label>

                                  <input step="0.01" type="number" name="service_stock" id="service_stock" class="form-control stock" id="exampleInputBorderWidth2" value="0">

                                </div>

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3">

                                <div class="form-input">

                                  <label for="">Service Unit Price</label><label id="buom_per"> </label>

                                  <input step="0.01" type="number" name="service_rate" id="service_rate" class="form-control rate" id="exampleInputBorderWidth2" value="0">

                                </div>

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3">

                                <div class="form-input">

                                  <label for="">Value</label>

                                  <input step="0.01" type="number" name="service_total" id="service_total" class="form-control total" id="exampleInputBorderWidth2" value="0" readonly>

                                </div>

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3">

                                <div class="form-input">

                                  <label for=""> Dated on </label>

                                  <input type="date" name="service_stock_date" id="service_stock_date" class="form-control stock" id="exampleInputBorderWidth2">

                                </div>

                              </div>

                            </div>

                          </div>




                        </div>

                      </div>

                    </div>

                  </div>

                </div>
              <?php }
              ?>


            </div>

            <div class="col-lg-6 col-md-6 col-sm-6">
              <?php
              if ($row['goodsType'] != 7 && $row['goodsType'] != 5) {
              ?>
                <div class="row" id="basicDetails">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Basic Details

                          <span class="text-danger">*</span>

                        </h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view basic-info-form-view">

                              <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="form-input">

                                  <label>Item Name</label>

                                  <input type="text" name="itemName" class="form-control item_name" id="exampleInputBorderWidth2" value="<?= $row['itemName'] ?>">
                                  <ul class="suggestion-item" id="suggestedNames">

                                  </ul>

                                </div>

                              </div>
                              <?php
                              $uomList_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `companyId`=$company_id", true);
                              $uomList = $uomList_sql['data'];


                              ?>
                              <div class="row">

                                <div class="col-12">

                                  <div class="row dash-border-row">

                                    <div class="col-lg-6 col-md-6 col-sm-6 unit-measure-col">

                                      <div class="row mb-4">

                                        <div class="col-lg-6 col-md-6 col-sm-6 col pr-0 base-measure">

                                          <div class="form-input">

                                            <label>Base UOM </label>


                                            <select id="buomDrop" name="baseUnitMeasure" class="form-control">

                                              <option value="">Base Unit of Measurement</option>

                                              <?php





                                              foreach ($uomList as $oneUomList) {

                                              ?>

                                                <option value="<?= $oneUomList['uomId'] ?>" <?php if ($oneUomList['uomId'] == $row['baseUnitMeasure']) {
                                                                                              echo "selected";
                                                                                            } ?>><?= $oneUomList['uomName'] ?> </option>

                                              <?php

                                              }

                                              ?>

                                            </select>

                                          </div>

                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col">

                                          <div class="form-input">

                                            <label>Alternate UOM</label>

                                            <select id="iuomDrop" name="issueUnit" class="form-control">

                                              <option value="">Alternate Unit of Measurement</option>

                                              <?php




                                              foreach ($uomList as $oneUomLists) {

                                              ?>

                                                <option value="<?= $oneUomLists['uomId'] ?>" <?php if ($oneUomLists['uomId'] == $row['issueUnitMeasure']) {
                                                                                                echo "selected";
                                                                                              } ?>><?= $oneUomLists['uomName'] ?></option>

                                              <?php

                                              }

                                              ?>

                                            </select>

                                          </div>

                                        </div>

                                      </div>

                                      <div class="row calculate-row">

                                        <div class="col-lg-1 col-md-1 col-sm-1 col p-0">

                                          <input type="text" class="form-control bg-none p-0" placeholder="1" readonly>

                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col">

                                          <!-- <input type="text" name="netWeight" class="form-control bg-none p-0" id="buom" placeholder="unit" readonly> -->
                                          <input type="text" name="netWeight" class="form-control bg-none p-0" id="buom" value="unit" readonly>

                                        </div>

                                        <div class="col-lg-1 col-md-1 col-sm-1 col">

                                          <p class="equal-style mt-1">=</p>

                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col">

                                          <input type="text" name="rel" class="form-control item_rel" id="rel" value="<?= $row['uomRel'] ?>">

                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3 col">

                                          <!-- <input type="text" name="netWeight" class="form-control bg-none p-0" placeholder="unit" id="ioum" readonly> -->
                                          <input type="text" name="netWeight" id="ioum" class="form-control bg-none p-0" value="unit" readonly>

                                        </div>

                                      </div>

                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6 hsn-modal-col">

                                      <div class="row calculate-parent-row mb-4">

                                        <div class="col-lg-12 col-md-12 col-sm-12 col hsn-column pr-3">

                                          <div class="form-input">

                                            <label>HSN </label>

                                            <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#goodsHSNModal"></button>

                                            <select id="hsnDropDown" name="hsn" class="form-control">

                                              <option id="hsnlabelOne" value="<?= $row['hsnCode'] ?>"><?= $row['hsnCode'] ?></option>

                                            </select>

                                          </div>

                                        </div>

                                      </div>

                                      <?php
                                      $hsn_desc = queryGet("SELECT `hsnDescription` FROM `erp_hsn_code` WHERE `hsnCode`='" . $row['hsnCode'] . "'");
                                      ?>


                                      <div class="row calculate-hsn-row mt-3 mb-2">

                                        <div class="col-lg-12 col-md-12 col-sm-12">

                                          <div class="form-input">

                                            <!-- <label class="label-hidden">HSN</label> -->

                                            <p class="hsn-description-info" id="hsnDescInfo"><?= $hsn_desc['data']['hsnDescription'] ?></p>

                                          </div>

                                        </div>

                                      </div>

                                    </div>

                                  </div>

                                </div>

                              </div>

                              <?php

                              if ($row['goodsType'] == "9") {

                              ?>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                  <div class="cost-center" id="cost-center">
                                    <label for="">Cost Center</label>
                                    <select name="costCenter" class="form-control">
                                      <option value="">Cost Center</option>
                                      <?php
                                      $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                      foreach ($funcList as $func) {
                                      ?>
                                        <option value="<?= $func['CostCenter_id'] ?>" <?php if ($row['cost_center'] == $func['CostCenter_id']) {
                                                                                        echo "selected";
                                                                                      } ?>>
                                          <?= $func['CostCenter_code'] ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                </div>

                                <div class="col-lg-6 col-md-6 col-sm-6">
                                  <div class="cost-center" id="depKey">
                                    <label for="">Asset Depreciation Key <span id="despkey_id"><?= $row['dep_key'] ?></span></label>
                                    <input type="hidden" id="dep_key_val" name="dep_key">

                                    </select>
                                  </div>
                                </div>

                              <?php
                              }
                              ?>




                              <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="form-input">

                                  <label>Additional Description</label>

                                  <textarea class="item_desc" rows="3" name="itemDesc" id="exampleInputBorderWidth2" placeholder="Additional Description"><?= $row['itemDesc'] ?></textarea>

                                </div>

                              </div>


                              <!-- 
                                  <?php
                                  //$sp = queryGet("SELECT * FROM `erp_item_specification` WHERE `item_id`='".$row['itemId']."'");
                                  //if($sp['numRows'] > 1){
                                  //$limit = $sp['numrows'] - 1;


                                  // }
                                  // else{
                                  // foreach($sp as $data){
                                  ?> -->
                              <div class="row othe-cost-infor modal-add-row_537">
                                <div class="row othe-cost-infor pl-0 pr-0">

                                  <div class="col-lg-5 col-md-5 col-sm-5">

                                    <div class="form-input">

                                      <label>Specification</label>

                                      <input type="text" name="spec[1][spec_name]" data-attr="1" class="form-control spec_vldtn specification_1" id="">

                                    </div>

                                  </div>
                                  <div class="col-lg-5 col-md-5 col-sm-5">

                                    <div class="form-input">

                                      <label>Specification Details</label>

                                      <input type="text" name="spec[1][spec_detail]" data-attr="1" class="form-control spec_dtls_vldtn specificationDetails_1" id="">

                                    </div>

                                  </div>



                                  <div class="col-lg col-md-2 col-sm-2">
                                    <div class="add-btn-plus">
                                      <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                        <i class="fa fa-plus"></i>
                                      </a>
                                    </div>
                                  </div>
                                </div>
                              </div>

                              <!-- <?php
                                    // }

                                    //}
                                    //console($sp);
                                    ?> -->

                              <!-- <div class="row othe-cost-infor modal-add-row_537">
                                <div class="row othe-cost-infor pl-0 pr-0">

                                  <div class="col-lg-5 col-md-5 col-sm-5">

                                    <div class="form-input">

                                      <label>Specification</label>

                                      <input type="text" name="spec[1][spec_name]" data-attr="1" class="form-control spec_vldtn specification_1" id="">

                                    </div>

                                  </div>
                                  <div class="col-lg-5 col-md-5 col-sm-5">

                                    <div class="form-input">

                                      <label>Specification Details</label>

                                      <input type="text" name="spec[1][spec_detail]" data-attr="1" class="form-control spec_dtls_vldtn specificationDetails_1" id="">

                                    </div>

                                  </div>



                                  <div class="col-lg col-md-2 col-sm-2">
                                    <div class="add-btn-plus">
                                      <a style="cursor: pointer" class="btn btn-primary" onclick="addMultiQtyf(537)">
                                        <i class="fa fa-plus"></i>
                                      </a>
                                    </div>
                                  </div>
                                </div>
                              </div> -->






                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>


                </div>

              <?php
              }
              if ($row['goodsType'] == 3 || $row['goodsType'] == 4 ||  $row['goodsType'] == 5) {

              ?>

                <div class="row" id="pricing">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Pricing and Discount

                          <span class="text-danger">*</span>

                        </h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for="">Target price</label>

                                  <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label for="">Max Discount (%)</label>

                                  <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                </div>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              <?php
              }
              if ($row['goodsType'] == 7 || $row['goodsType'] == 5) {
              ?>
                <div class="row" id="service_sales_details">

                  <div class="col-lg-12 col-md-12 col-sm-12">

                    <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                      <div class="card-header">

                        <h4>Service Details

                          <span class="text-danger">*</span>

                        </h4>

                      </div>

                      <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                        <div class="row">

                          <div class="col-lg-12 col-md-12 col-sm-12">

                            <div class="row goods-info-form-view customer-info-form-view">

                              <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="form-input">

                                  <label>Service Name</label>

                                  <input type="text" name="serviceName" class="form-control item_name service_name" id="exampleInputBorderWidth2" value="<?= $row['itemName'] ?>">

                                </div>

                              </div>

                              <div class="col-lg-12 col-md-12 col-sm-12">

                                <div class="form-input">

                                  <label>Service Description</label>

                                  <textarea class="item_desc service_desc" rows="3" name="serviceDesc" id="exampleInputBorderWidth2"><?= $row['itemDesc'] ?></textarea>

                                </div>

                              </div>



                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label>HSN </label>

                                  <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#goodsHSNModal"></button>

                                  <select id="hsnDropDown" name="" class="form-control servicehsnDropDown">

                                    <option id="hsnlabelservice" value="<?= $row['hsnCode'] ?>"><?= $row['hsnCode'] ?></option>

                                  </select>

                                </div>

                              </div>

                              <?php
                              if ($row['goodsType'] == 7) {
                              ?>
                                <div class="col-lg-6 col-md-6 col-sm-6" id="tds">

                                  <div class="form-input">

                                    <label>TDS </label>

                                    <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#tdsmodal"></button>

                                    <select id="tdsDropDown" name="tds" class="form-control">
                                      <option id="tdslabel" value="<?= $row['tds'] ?>"><?= $row['tds'] ?></option>




                                    </select>

                                  </div>

                                </div>
                              <?php
                              }

                              ?>



                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label>GL Code </label>



                                  <select id="glCode" name="glCode" class="form-control">
                                    <option value="<?= $row['parentGlId'] ?>"><?= $row['parentGlId'] ?></option>

                                  </select>

                                </div>

                              </div>

                              <div class="col-lg-6 col-md-6 col-sm-6">

                                <div class="form-input">

                                  <label>Service Unit</label>

                                  <input type="text" name="serviceUnit" class="form-control service_unit" id="exampleInputBorderWidth2" value="<?= $row['service_unit'] ?>">

                                </div>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              <?php }
              ?>

            </div>
            <?php

            if ($row['goodsType'] != 7 && $row['goodsType'] != 5) {
            ?>

              <div class="col-lg-12 col-md-12 col-sm-12">

                <div class="card goods-creation-card so-creation-card po-creation-card" id="specificationDetails" style="height: auto;">

                  <div class="card-header">

                    <h4>Specification Details

                      <!-- <span class="text-danger">*</span> -->


                    </h4>

                  </div>

                  <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                    <div class="row">

                      <div class="col-lg-12 col-md-12 col-sm-12">

                        <div class="row goods-info-form-view customer-info-form-view">


                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Net Weight</label>

                              <input step="0.01" type="number" name="netWeight" class="form-control net_weight" id="net_weight" value="<?= $row['netWeight'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">
                              <label class="" for="">Net Weight Unit</label>
                              <select name="net_unit" class="form-control " id="net_unit">
                                <option value="">Select net weight unit</option>
                                <option value="kg" <?php if ($row['weight_unit'] == "kg") {
                                                      echo "selected";
                                                    } ?>>kg</option>
                                <option value="g" <?php if ($row['weight_unit'] == "g") {
                                                    echo "selected";
                                                  } ?>>g</option>
                                <option value="ton" <?php if ($row['weight_unit'] == "ton") {
                                                      echo "selected";
                                                    } ?>>ton</option>

                              </select>
                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Gross Weight</label>

                              <input step="0.01" type="number" name="grossWeight" class="form-control gross_weight" id="gross_weight" value="<?= $row['grossWeight'] ?>">
                              <span id="gross_span"></span>

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">
                              <label class="" for="">Gross Weight Unit</label>
                              <select name="gross_unit" class="form-control " id="gross_unit" disabled="">
                                <option value="">Select gross weight unit</option>
                                <option value="kg" <?php if ($row['weight_unit'] == "kg") {
                                                      echo "selected";
                                                    } ?>>kg</option>
                                <option value="g" <?php if ($row['weight_unit'] == "g") {
                                                    echo "selected";
                                                  } ?>>g</option>
                                <option value="ton" <?php if ($row['weight_unit'] == "ton") {
                                                      echo "selected";
                                                    } ?>>ton</option>

                              </select>
                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Height</label>

                              <input step="0.01" type="number" name="height" class="form-control calculate_volume" id="height" value="<?= $row['height'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Width</label>

                              <input step="0.01" type="number" name="width" class="form-control calculate_volume" id="width" value="<?= $row['width'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">

                              <label for="">Length</label>

                              <input step="0.01" type="number" name="length" class="form-control calculate_volume" id="length" value="<?= $row['length'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-3 col-md-3 col-sm-3">

                            <div class="form-input">
                              <label class="" for="">Unit</label>
                              <select name="measure_unit" class="form-control volume_unit" id="volume_unit">
                                <option value="">Select</option>
                                <option value="cm" <?php if ($row['measuring_unit'] == "cm") {
                                                      echo "selected";
                                                    } ?>>cm</option>
                                <option value="m" <?php if ($row['measuring_unit'] == "m") {
                                                    echo "selected";
                                                  } ?>>m</option>

                              </select>
                            </div>

                          </div>

                          <div class="col-lg-6 col-md-6 col-sm-6">

                            <div class="form-input">

                              <label>Volume In CM<sup>3</sup></label>

                              <input type="text" name="volumeCubeCm" class="form-control" id="volcm" readonly="" value="<?= $row['volumeCubeCm'] ?>">

                            </div>

                          </div>

                          <div class="col-lg-6 col-md-6 col-sm-6">

                            <div class="form-input">

                              <label>Volume In M<sup>3</sup></label>

                              <input type="text" name="volume" class="form-control" id="volm" readonly="" value="<?= $row['volume'] ?>">

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

            <?php
            }
            ?>



            <!-----hsn modal start------->


            <div class="modal fade hsn-dropdown-modal" id="goodsHSNModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4>Choose HSN</h4>
                    <!-- <b id="hsnLoding"></b> -->
                    <!-- <input class="form-control" id="searchbar" type="text" name="search" placeholder="Search.."> -->

                    <div class="section serach-input-section">
                      <input type="text" class="dataTables_filter" id="searchbar" placeholder="" class="field serachfilter-hsn form-control">
                      <button type="reset">&times;</button>
                      <!-- <div class="icons-container">
                        <div class="icon-close">
                          <i class="fa fa-spinner fa-spin hsnSearchSpinner"></i>
                          <i style="cursor: pointer" type="reset" class="fa fa-times hsnSearchclear"></i>
                        </div>
                        <div class="icon-search">
                          <i class="fa fa-search" id="myBtn"></i>
                          <script>
                            var input = document.getElementById("myInput");
                            input.addEventListener("keypress", function(event) {
                              if (event.key === "Enter") {
                                event.preventDefault();
                                document.getElementById("myBtn").click();
                              }
                            });
                            $(".hsnSearchclear").click(function() {
                              $("#searchbar").val('');
                            });
                          </script>
                        </div>
                      </div> -->
                    </div>

                  </div>
                  <div class="modal-body">

                    <div class="card">

                      <div class="card-body m-0 p-0 hsn-code">

                        <div class="hsn-list" style="height: 500px; overflow-y: scroll;" id="myPopup">

                          <table class="table table-hover hsn-modal-table" id="myPopupTable">
                            <thead>
                              <th></th>
                              <th>Code</th>
                              <th>Description</th>
                              <th>Rate</th>
                            </thead>
                            <tbody class="hsn_tbody">

                            </tbody>
                          </table>

                        </div>
                      </div>
                      <div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="hsnsavebtn" data-dismiss="modal">Select</button>
                  </div>
                </div>
              </div>
            </div>


            <!-----hsn modal end------->

            <script>
              var popup = $('#myPopup');
              var popupTable = $('#myPopupTable');
              popup.scroll(function() {
                console.log(popup.scrollTop());
                console.log(popupTable[0].scrollHeight * 0.9);

                if (popup.scrollTop() >= popupTable[0].scrollHeight * 0.9) {
                  // Load AJAX content
                  // $.ajax({
                  //   url: 'ajax/content.html',
                  //   success: function(data) {
                  //     // Insert the content into the popup container


                  //     popup.append(data);
                  //   }
                  // });
                  console.log('trdx');
                } else {
                  console.log('trdx22');


                }
              });

              // $("#myPopup").scroll(function() {
              //   console.log($("#myPopup").scrollTop());
              //   console.log($("#myPopup").height());

              //   console.log($("#myPopup").scrollTop() + $("#myPopup").height());
              //   console.log($("#myPopup").height() * 0.8);
              //   if ($("#myPopup").scrollTop() + $("#myPopup").height() <= $("#myPopup").height() * 0.8) {
              //     // Load Ajax content l
              //     console.log('cgfc');
              //   } else {
              //     console.log('test');
              //   }
              // });
            </script>


            <!-----tds modal start------->


            <div class="modal fade hsn-dropdown-modal" id="tdsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
              <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                <div class="modal-content">
                  <div class="modal-body" style="height: 500px; overflow: auto;">
                    <h4 class="text-sm pl-2">Choose TDS</h4>
                    <div class="card">
                      <div class="card-body m-2 p-0 hsn-code">
                        <table class="table defaultDataTable table-hover tds-modal-table hsn-modal-table ">
                          <thead>
                            <th></th>
                            <th>Section</th>
                            <th>Nature</th>
                            <th>Threshold</th>
                            <th>Rate</th>
                          </thead>
                          <tbody>
                            <?php
                            $tds_sql = queryGet("SELECT * FROM `erp_tds_details`", true);
                            $tds_data = $tds_sql['data'];
                            foreach ($tds_data as $tds) {
                              // console($hsn); 
                            ?>
                              <tr>
                                <td> <input type="radio" id="tds" name="tds" data-attr="<?= $tds['section']  ?>" value="<?= $tds['id']  ?>"></td>
                                <td>
                                  <p id="section_<?= $tds['id'] ?>"><?= $tds['section'] ?></p>
                                </td>
                                <td>
                                  <p id="nature_<?= $tds['id'] ?>"><?= $tds['natureOfTransaction'] ?></p>
                                </td>
                                <td>
                                  <p id="threshold<?= $tds['id'] ?>"><?= $tds['thresholdLimit'] ?></p>
                                </td>
                                <td>
                                  <p id="rate<?= $tds['id'] ?>"><?= $tds['TDSRate'] ?>%</p>
                                </td>
                              </tr>
                            <?php
                            }
                            ?>
                          </tbody>
                        </table>
                        <!-- <div class="hsn-header">
                            <div class="hsn-title">
                              <input type="radio" id="hsn" name="hsn" value="<?= $hsn['hsnCode']  ?>">
                              <h5 id="hsnCode_<?= $hsn['hsnId'] ?>"><?= $hsn['hsnCode'] ?></h5>
                            </div>
                            <div class="tax-per">
                              <p id="taxPercentage_<?= $hsn['hsnId'] ?>"><?= $hsn['taxPercentage'] ?>%</p>
                            </div>
                          </div>
                          <div class="hsn-description">
                            <p id="hsnDescription_<?= $hsn['hsnId'] ?>"><?= $hsn['hsnDescription'] ?></p>
                          </div>-->


                      </div>






                      <div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="tdssavebtn" data-dismiss="modal">Save changes</button>
                  </div>
                </div>
              </div>
            </div>


            <!-----tds modal end------->


            <div class="btn-section mt-2 mb-2">

              <button class="btn btn-primary save-close-btn btn-xs float-right add_data" id="submit_btn" value="add_post">Submit</button>

              <!-- <button class="btn btn-danger save-close-btn btn-xs float-right add_data" id="draft_btn" value="add_draft" style="display:none;">Save as Draft</button> -->

            </div>
          </div>







        </form>



        <!-- modal -->

        <div class="modal" id="addNewGoodTypesFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title text-white">Add New Item Type</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewGoodTypesForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="goodTypeName" class="form-control">

                      <label>Type Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="goodTypeDesc" class="form-control">

                      <label>Type Description</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group btn-col">

                      <button type="submit" id="addNewGoodTypesFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                    </div>

                  </div>

                </form>

              </div>

            </div>

          </div>

        </div>

        <!-- modal end -->






        <!-- modal -->



        <!-- modal end -->



        <!-- purchase group -->



        <!-- modal -->

        <div class="modal" id="addNewPurchaseGroupFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title text-white">Add New Purchase Group</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewPurchaseGroupForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="purchaseGroupName" class="form-control">

                      <label>Purchase Group Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="purchaseGroupDesc" class="form-control">

                      <label>Purchase Group Description</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group btn-col">

                      <button type="submit" id="addNewPurchaseGroupFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                    </div>

                  </div>

                </form>

              </div>

            </div>

          </div>

        </div>

        <!-- modal end -->







        <!-- end purchase group -->

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

              <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>

              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Item</a></li>

              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">View Item</a></li>

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

                              <option value=""> Group</option>

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

                          <textarea rows="3" name="itemDesc" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>

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



            <!-- <ol class="breadcrumb bg-transparent">

              <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Items</a></li>


            </ol> -->

            <div class="p-0 pt-1 my-2">

              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                  <h3 class="card-title">
                    Item Master
                  </h3>


                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>

                </li>

              </ul>

            </div>
            <div class="filter-list">
              <a href="goods.php" class="btn active"><i class="fa fa-stream mr-2 active"></i>All</a>
              <a href="goods-type-items.php" class="btn"><i class="fa fa-list mr-2"></i>Raw Materials</a>
              <a href="goods-type-items.php?sfg" class="btn"><i class="fa fa-clock mr-2"></i>SFG</a>
              <a href="goods-type-items.php?fg" class="btn"><i class="fa fa-lock-open mr-2"></i>FG</a>
              <a href="goods-type-items.php?service" class="btn"><i class="fa fa-lock mr-2"></i>Services</a>
              <a href="manage-assets.php" class="btn"><i class="fa fa-lock mr-2"></i>Assets</a>
            </div>




            <div class="card card-tabs" style="border-radius: 20px;">

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

                            <input type="text" id="myInput" name="keyword" placeholder="" class="field form-control" value="<?php echo $keywd; ?>" />

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

                        <div class="col-lg-1 col-md-1 col-sm-1">

                          <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary relative-add-btn"><i class="fa fa-plus"></i></a>

                        </div>

                      </div>



                    </div>

                  </div>

                </div>
                <div class="modal fade list-filter-search-modal" id="btnSearchCollpase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Filter Purchase Request</h5>

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
                              <option value="active" <?php if (isset($_REQUEST['status_s']) && 'active' == $_REQUEST['status_s']) {
                                                        echo 'selected';
                                                      } ?>>Active
                              </option>
                              <option value="inactive" <?php if (isset($_REQUEST['status_s']) && 'inactive' == $_REQUEST['status_s']) {
                                                          echo 'selected';
                                                        } ?>>Inactive
                              </option>
                              <option value="draft" <?php if (isset($_REQUEST['status_s']) && 'draft' == $_REQUEST['status_s']) {
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
              <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

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

                  if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                    $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR `itemName` like '%" . $_REQUEST['keyword2'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword2'] . "%')";
                  } else {

                    if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                      $cond .= " AND (`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR `itemName` like '%" . $_REQUEST['keyword'] . "%' OR `netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                    }
                  }




                  $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id ORDER BY itemId desc  limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                  $qry_list = mysqli_query($dbCon, $sql_list);

                  $num_list = mysqli_num_rows($qry_list);





                  $countShow = "SELECT count(*) FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND `company_id`=$company_id  ";

                  $countQry = mysqli_query($dbCon, $countShow);

                  $rowCount = mysqli_fetch_array($countQry);

                  $count = $rowCount[0];

                  $cnt = $GLOBALS['start'] + 1;

                  $settingsTable = getTableSettings(TBL_BRANCH_ADMIN_TABLESETTINGS, "ERP_INVENTORY_ITEMS", $_SESSION["logedBranchAdminInfo"]["adminId"]);

                  $settingsCh = ($settingsTable['data'][0]['settingsCheckbox']);

                  $settingsCheckbox = unserialize($settingsCh);

                  if ($num_list > 0) { ?>

                    <table class="table defaultDataTable table-hover text-nowrap">

                      <thead>

                        <tr class="alert-light">

                          <!-- <th>#</th> -->

                          <?php if (in_array(1, $settingsCheckbox)) { ?>

                            <th>Item Code</th>

                          <?php }

                          if (in_array(2, $settingsCheckbox)) { ?>

                            <th>Item Name</th>

                          <?php }

                          if (in_array(3, $settingsCheckbox)) { ?>

                            <th>Base UOM</th>

                          <?php  }

                          if (in_array(4, $settingsCheckbox)) { ?>

                            <th>Group</th>

                          <?php }
                          if (in_array(5, $settingsCheckbox)) { ?>

                            <th>Type</th>

                          <?php

                          }

                          if (in_array(6, $settingsCheckbox)) { ?>

                            <th>Moving Weighted Price</th>

                          <?php  }

                          if (in_array(7, $settingsCheckbox)) { ?>

                            <th>Valuation Class</th>

                          <?php

                          }


                          if (in_array(8, $settingsCheckbox)) { ?>

                            <th> Target Price</th>

                          <?php

                          }




                          ?>

                          <th>BOM Status</th>

                          <th>Status</th>

                          <th>Action</th>
                          <th>Add</th>

                        </tr>

                      </thead>

                      <tbody>

                        <?php

                        $customerModalHtml = "";

                        while ($row = mysqli_fetch_assoc($qry_list)) {
                          //console($row);
                          $itemId = $row['itemId'];
                          $itemCode = $row['itemCode'];

                          $itemName = $row['itemName'];

                          $netWeight = $row['netWeight'];

                          $volume = $row['volume'];

                          $goodsType = $row['goodsType'];

                          $grossWeight = $row['grossWeight'];

                          $buom_id = $row['baseUnitMeasure'];

                          $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
                          $buom = $buom_sql['data']['uomName'];
                          //  console($buom);



                          $goodTypeId = $row['goodsType'];
                          $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                          $type_name = $type_sql['data']['goodTypeName'];



                          $goodGroupId = $row['goodsGroup'];
                          $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
                          $group_name = $group_sql['data']['goodGroupName'];


                          $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
                          $mwp = $summary_sql['data']['movingWeightedPrice'];
                          $val_class = $summary_sql['data']['priceType'];

                        ?>

                          <tr>

                            <!-- <td><?= $cnt++ ?></td> -->

                            <?php if (in_array(1, $settingsCheckbox)) { ?>

                              <td><?= $row['itemCode'] ?></td>

                            <?php }

                            if (in_array(2, $settingsCheckbox)) { ?>

                              <td><?= $row['itemName'] ?></td>

                            <?php }

                            if (in_array(3, $settingsCheckbox)) { ?>

                              <td><?= $buom ?> </td>

                            <?php }

                            if (in_array(4, $settingsCheckbox)) { ?>

                              <td><?= $group_name ?></td>

                            <?php }
                            if (in_array(5, $settingsCheckbox)) { ?>

                              <td><?= $type_name ?></td>

                            <?php }
                            if (in_array(6, $settingsCheckbox)) { ?>

                              <td><?= $mwp ?></td>

                            <?php }

                            if (in_array(7, $settingsCheckbox)) { ?>

                              <td><?= $val_class  ?></td>

                            <?php }

                            if (in_array(8, $settingsCheckbox)) { ?>

                              <td><?= $summary_sql['data']['itemPrice'] ?></td>

                            <?php }


                            ?>



                            <td>

                              <?php

                              if ($row['bomStatus'] == 1) {

                                if ($goodsBomController->isBomCreated($row['itemId'])) {

                                  echo '<span class="status">Created</span>';
                                } else {

                                  echo '<span class="status-warning">Not Created</span>';
                                }
                              } else {

                                echo '<span class="status-danger">Not Required</span>';
                              }

                              ?>

                            </td>



                            <td>

                              <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                                <input type="hidden" name="id" value="<?php echo $row['itemId'] ?>">

                                <input type="hidden" name="changeStatus" value="active_inactive">

                                <button <?php if ($row['status'] == "draft") { ?> type="button" style="cursor: inherit; border:none" <?php } else { ?>type="submit" onclick="return confirm('Are you sure to change status?')" style="cursor: pointer; border:none" <?php } ?> class="p-0 m-0 ml-2" data-toggle="tooltip" data-placement="top" title="<?php echo $row['status'] ?>">

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



                              <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" class="btn btn-sm">

                                <i class="fa fa-eye po-list-icon"></i>

                              </a>

                            </td>

                            <td>
                              <?php
                              $item_id = $row['itemId'];
                              $check_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE  `location_id`=$location_id  AND `itemId`=$item_id ", true);
                              if ($check_sql['status'] == "success") {

                              ?>
                                <button class="btn btn-success" type="button">Added</button>

                              <?php

                              } else {

                              ?>


                                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#addToLocation_<?= $row['itemId'] ?>">Add</button>
                              <?php
                              }

                              ?>
                            </td>

                          </tr>


                          <!-----add form modal start --->
                          <div class="modal fade hsn-dropdown-modal" id="addToLocation_<?= $row['itemId'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                            <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <form method="POST" action="">
                                    <input type="hidden" name="createLocationItem" id="createLocationItem" value="">
                                    <input type="hidden" name="item_id" value="<?= $row['itemId'] ?>">


                                    <div class="row">


                                      <div class="col-lg-12 col-md-12 col-sm-12">

                                        <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                          <div class="card-header">

                                            <h4>Storage Details</h4>

                                          </div>

                                          <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                            <div class="row">

                                              <div class="col-lg-12 col-md-12 col-sm-12">

                                                <div class="row goods-info-form-view customer-info-form-view">









                                                  <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                      <label for="">Storage Control</label>

                                                      <input type="text" name="storageControl" class="form-control">

                                                    </div>

                                                  </div>

                                                  <div class="col-lg-4 col-md-4 col-sm-4">

                                                    <div class="form-input">

                                                      <label for="">Max Storage Period</label>

                                                      <input type="text" name="maxStoragePeriod" class="form-control">

                                                    </div>

                                                  </div>

                                                  <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="form-input">
                                                      <label class="label-hidden" for="">Min Time Unit</label>
                                                      <select id="minTime" name="minTime" class="select2 form-control">
                                                        <option value="">Min Time Unit</option>
                                                        <option value="Day">Day</option>
                                                        <option value="Month">Month</option>
                                                        <option value="Hours">Hours</option>

                                                      </select>
                                                    </div>
                                                  </div>

                                                  <div class="col-lg-6 col-md-6 col-sm-6">

                                                    <div class="form-input">

                                                      <label for="">Minimum Remain Self life</label>

                                                      <input type="text" name="minRemainSelfLife" class="form-control">

                                                    </div>

                                                  </div>

                                                  <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-input">
                                                      <label class="label-hidden" for="">Max Time Unit</label>
                                                      <select id="maxTime" name="maxTime" class="select2 form-control">
                                                        <option value="">Max Time Unit</option>
                                                        <option value="Day">Day</option>
                                                        <option value="Month">Month</option>
                                                        <option value="Hours">Hours</option>

                                                      </select>
                                                    </div>
                                                  </div>

                                                </div>

                                              </div>

                                            </div>

                                          </div>

                                        </div>

                                      </div>




                                      <div class="col-lg-12 col-md-12 col-sm-12">
                                        <?php
                                        //  }
                                        if ($type_name == "Finished Good" || $type_name == "Service Sales" || $type_name == "FG Trading") {
                                        ?>

                                          <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                                            <div class="card-header">

                                              <h4>Pricing and Discount

                                                <span class="text-danger">*</span>

                                              </h4>

                                            </div>

                                            <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                                              <div class="row">

                                                <div class="col-lg-12 col-md-12 col-sm-12">

                                                  <div class="row goods-info-form-view customer-info-form-view">

                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                      <div class="form-input">

                                                        <label for="">Target price</label>

                                                        <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" placeholder="price">

                                                      </div>

                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                                      <div class="form-input">

                                                        <label for="">Max Discount</label>

                                                        <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

                                                      </div>

                                                    </div>

                                                  </div>

                                                </div>

                                              </div>

                                            </div>

                                          </div>
                                        <?php }
                                        ?>

                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                          <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>
                                        </div>


                                      </div>






                                    </div>












                                  </form>

                                </div>
                                <div class="modal-body" style="height: 500px; overflow: auto;">
                                  <div class="card">

                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>


                          <!---end modal --->


                          <!-- right modal start here  -->

                          <div class="modal fade right goods-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                              <!--Content-->

                              <div class="modal-content">

                                <!--Header-->

                                <div class="modal-header pt-4">

                                  <div class="row">

                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                      <div class="item-img">

                                        <img src="../../public/assets/img/image/goods-item-image.png" title="goods-iem-image" alt="goods_item_image">

                                      </div>

                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6">

                                      <p class="heading lead text-xs text-right mt-2 mb-2">Item Name : <?= $itemName ?></p>

                                      <p class="text-xs mt-2 mb-2 text-right">Item Code : <?= $itemCode ?></p>

                                      <p class="text-xs mt-2 mb-2 text-right">Description : <?= $row['itemDesc'] ?></p>

                                    </div>

                                  </div>
                                  <div class="display-flex-space-between mt-4 mb-3">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                      <li class="nav-item">
                                        <a class="nav-link active" id="home-tab<?= $row['itemId'] ?>" data-toggle="tab" href="#home<?= $row['itemId'] ?>" role="tab" aria-controls="home<?= $row['itemId'] ?>" aria-selected="true"><ion-icon name="information-outline"></ion-icon>Info</a>
                                      </li>

                                      <!-- -------------------Audit History Button Start------------------------- -->
                                      <li class="nav-item">
                                        <a class="nav-link auditTrail" id="history-tab<?= $row['itemId'] ?>" data-toggle="tab" data-ccode="<?= $row['itemCode'] ?>" href="#history<?= $row['itemId'] ?>" role="tab" aria-controls="history<?= $row['itemId'] ?>" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</a>
                                      </li>
                                      <!---------------------Audit History Button End--------------------------->
                                    </ul>


                                    <form action="" method="POST">
                                      <?php $itemId = base64_encode($row['itemId']) ?>
                                      <div class="hamburger">
                                        <div class="wrapper-action">
                                          <ion-icon name="settings"></ion-icon>
                                        </div>
                                      </div>
                                      <div class="nav-action" id="reminder">
                                        <a title="Mail the customer" href="#" name="vendorReminerBtn">
                                          <ion-icon name="notifications"></ion-icon>
                                        </a>
                                      </div>
                                      <div class="nav-action" id="edit">
                                        <?php
                                        $check_item = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='" . $row['itemId'] . "' AND `location_id`!=$location_id", true);
                                        // console($check_item);
                                        if ($check_item['numRows'] > 0) {
                                        ?>
                                          <a title="Mail the customer" title="This item is uneditable because this item has already been used by some other location" href="#" name="vendorEditBtn">
                                            <ion-icon name="create"></ion-icon>
                                          </a>
                                        <?php
                                        } else {



                                        ?>
                                          <a title="Mail the customer" title="This item is uneditable because this item has already been used by some other location" href="#" name="vendorEditBtn">
                                            <ion-icon name="create"></ion-icon>
                                          </a>
                                        <?php
                                        }
                                        ?>
                                      </div>
                                      <div class="nav-action bg-danger" id="thumb">
                                        <a title="Chat the customer" href="#" name="vendorEditBtn">
                                          <ion-icon name="trash"></ion-icon>
                                        </a>
                                      </div>
                                      <div class="nav-action" id="create">
                                        <a title="Call the customer" href="#" name="vendorEditBtn">
                                          <ion-icon name="toggle"></ion-icon>
                                        </a>
                                      </div>
                                    </form>


                                    <!-- <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                      <?php $itemId = base64_encode($row['itemId']) ?>




                                      <form action="" method="POST">

                                        <?php
                                        $check_item = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='" . $row['itemId'] . "' AND `location_id`!=$location_id", true);
                                        // console($check_item);
                                        if ($check_item['numRows'] > 0) {
                                        ?>
                                          <a href="" id="my-link" name="customerEditBtn" disabled>

                                            <i class="fa fa-edit po-list-icon-invert" title="This item is uneditable because this item has already been used by some other location"></i>

                                          </a>
                                        <?php
                                        } else {



                                        ?>

                                          <a href="goods.php?edit=<?= $itemId ?>" name="customerEditBtn">

                                            <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                          </a>
                                        <?php
                                        }
                                        ?>

                                        <a href="">

                                          <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                        </a>

                                        <a href="">

                                          <i title="Toggle" class="fa fa-toggle-on po-list-icon-invert"></i>

                                        </a>

                                      </form>

                                    </div> -->

                                  </div>

                                </div>



                                <!--Body-->

                                <div class="modal-body blur-body" style="padding: 0;">

                                  <div class="tab-content" id="myTabContent">

                                    <div class="tab-pane fade show active" id="home<?= $row['itemId'] ?>" role="tabpanel" aria-labelledby="home-tab">

                                      <div class="row px-3">
                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                          <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                              <!-------Classification------>
                                              <div class="accordion accordion-flush matrix-accordion goods-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-0" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                      Classification
                                                    </button>
                                                  </h2>
                                                  <div id="classifications" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card">

                                                        <div class="card-body p-3">

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Goods Type :</p>
                                                            <p class="font-bold text-xs"><?= $type_name ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs"> Group :</p>
                                                            <p class="font-bold text-xs"><?= $group_name ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Availablity Check :</p>
                                                            <p class="font-bold text-xs"><?= $row['availabilityCheck'] ?></p>
                                                          </div>

                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                              <!-------Basic Details------>
                                              <div class="accordion accordion-flush matrix-accordion goods-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-0" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                      Basic Details
                                                    </button>
                                                  </h2>
                                                  <div id="basicDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card">
                                                        <div class="card-body p-3">

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Net Weight :</p>
                                                            <p class="font-bold text-xs"><?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Gross Weight :</p>
                                                            <p class="font-bold text-xs"><?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Volume :</p>
                                                            <p class="font-bold text-xs"><?= $row['volume'] ?> m<sup>3</sup></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Height :</p>
                                                            <p class="font-bold text-xs"><?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Width :</p>
                                                            <p class="font-bold text-xs"><?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Length :</p>
                                                            <p class="font-bold text-xs"><?= $row['length'] . "  " . $row['measuring_unit'] ?></p>
                                                          </div>

                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                              <?php
                                              $item_id = $row['itemId'];
                                              $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
                                              $storage_data = $storage_sql['data'];


                                              ?>

                                              <!-------Storage Details------>
                                              <div class="accordion accordion-flush matrix-accordion goods-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-0" type="button" data-bs-toggle="collapse" data-bs-target="#storageDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                      Storage Details
                                                    </button>
                                                  </h2>
                                                  <div id="storageDetails" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card">

                                                        <div class="card-body p-3">

                                                          <!-- <div class="display-flex-space-between">
                                                        <p class="text-xs">Storage Bin :</p>
                                                        <p class="text-xs"><?= $row['storageBin'] ?></p>
                                                      </div>

                                                      <div class="display-flex-space-between">
                                                        <p class="text-xs">Picking Area :</p>
                                                        <p class="text-xs"><?= $row['pickingArea'] ?></p>
                                                      </div>

                                                      <div class="display-flex-space-between">
                                                        <p class="text-xs">Temp Control :</p>
                                                        <p class="text-xs"><?= $row['tempControl'] ?></p>
                                                      </div> -->

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Storage Control :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['storageControl'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Max Storage Period :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriod'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Max Storage Period Time :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriodTimeUnit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Min Remain Self Life Time Unit :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLife'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Min Remain Self Life :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLifeTimeUnit'] ?></p>
                                                          </div>

                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                              <!-------Purchase Details------>
                                              <!-- <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                            <div class="accordion-item">
                                              <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#purchaseDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                  Storage Details
                                                </button>
                                              </h2>
                                              <div id="purchaseDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body p-0">

                                                  <div class="card">

                                                    <div class="card-body p-3">

                                                      <div class="display-flex-space-between">
                                                        <p class="text-xs">Purchasing Value Key :</p>
                                                        <p class="text-xs"><?= $row['purchasingValueKey'] ?></p>
                                                      </div>



                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div> -->

                                            </div>

                                          </div>
                                        </div>
                                        <div class="col-lg-8 col-md-8 col-sm-8">
                                          <section style="padding: 10px;">
                                            <ul id="dragRoot">
                                              <li><i class="icon-building"></i> <span class="node-facility">Test</span>
                                                <ul>
                                                  <li><i class="icon-hdd"></i> <span class="node-cpe">test-1</span>
                                                    <ul>
                                                      <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-1</span>

                                                        <ul>
                                                          <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-1</span></li>
                                                          <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-2</span></li>
                                                          <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-3</span>

                                                            <ul>
                                                              <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-1</span></li>
                                                              <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-2</span></li>
                                                              <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-3</span></li>
                                                            </ul>
                                                          </li>
                                                        </ul>
                                                      </li>
                                                      <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-2</span></li>
                                                      <li><i class="icon-hdd"></i> <span class="node-cpe">test-sub-3</span></li>
                                                    </ul>
                                                  </li>
                                                </ul>
                                              </li>
                                            </ul>

                                          </section>
                                        </div>
                                      </div>
                                    </div>



                                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                                      <?= $itemName ?>

                                    </div>

                                    <!-- -------------------Audit History Tab Body Start------------------------- -->

                                    <div class="tab-pane fade" id="history<?= $row['itemId'] ?>" role="tabpanel" aria-labelledby="history-tab">
                                      <div class="audit-head-section mb-3 mt-3 ">
                                        <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                        <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                      </div>
                                      <hr>
                                      <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= $row['itemCode'] ?>">

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

                              Item Code</td>

                          </tr>

                          <tr>

                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(2, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox2" value="2" />

                              Item Name</td>

                          </tr>

                          <tr>

                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(3, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="3" />

                              Base UOM</td>

                          </tr>

                          <tr>

                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox4" value="4" />

                              Group</td>

                          </tr>

                          <tr>

                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox5" value="5" />

                              Type</td>

                          </tr>

                          <tr>

                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox6" value="6" />

                              Moving Weighted Price</td>

                          </tr>

                          <tr>

                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                              Valuation Class</td>

                          </tr>

                          <tr>

                            <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                              Target Price</td>

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

    // $('#goodTypeDropDown')

    //   .select2()

    //   .on('select2:open', () => {

    //     // $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodTypesFormModal">Add New</a></div>`);

    //   });


    function loadGLCode(accType) {
      // console.log(1);
      $.ajax({

        type: "POST",

        url: `ajaxs/accounting/ajax-getglbyp.php`,
        data: {
          accType: accType
        },
        beforeSend: function() {

          $("#glCode").html(`<option value="">Loding...</option>`);

        },

        success: function(response) {
          //alert(response);
          if (accType == 1) {
            $("#glCodeAsset").html(response);
          } else {
            $("#glCode").html(response);
          }

        }

      });

    }


    $(document).on("change", "#goodTypeDropDown", function() {

      // alert(1);



      let dataAttrVal = $("#goodTypeDropDown").find(':selected').data('goodtype');

      if (dataAttrVal == "RM") {

        console.log(1);

        $("#bomCheckBoxDiv").html("");
        $("#cost-center").hide("");
        $("#bomRadioDiv").html("");
        $("#pricing").hide();
        $("#purchase").html();
        $("#basicDetails").show();
        $("#storageDetails").show();
        $("#service_sales_details ").hide();
        $("#stockRate").show();
        $("#goodsGroup").show();
        $("#purchaseGroup").show();
        $("#availability").show();
        $("#mwp").show();
        $("#submit_btn").show();
        $("#draft_btn").show();
        $(".error").hide();
        $(".error").html("");
        $("#notesModalBody").html("");
        $("#specificationDetails").show("");
        $("#serviceStock").hide("");
        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();






      } else if (dataAttrVal == "SFG") {

        $("#bomRadioDiv").html("");
        $("#serviceStock").hide("");
        $("#cost-center").hide("");
        $("#basicDetails").show();
        $("#storageDetails").show();
        $("#service_sales_details ").hide();
        $("#goodsGroup").show();
        $("#purchaseGroup").show();
        $("#availability").show();
        $("#stockRate").show();
        $("#mwp").hide();
        $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired" style="width: auto; margin-bottom: 0;" checked disabled><label class="mb-0">Required BOM</label>`);
        $("#pricing").hide();

        $("#submit_btn").show();
        $("#draft_btn").show();

        $(".error").hide();
        $(".error").html("");

        $("#notesModalBody").html("");
        $("#specificationDetails").show("");

        $("#asset_classification").hide("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");

        $("#asset_gl").html();
        $("#asset_gl").hide();


      } else if (dataAttrVal == "FG") {
        $("#serviceStock").hide("");
        $("#mwp").hide();
        $("#bomCheckBoxDiv").html(``);
        $("#cost-center").hide("");
        $("#purchase").html("");
        $("#basicDetails").show();
        $("#storageDetails").show();
        $("#service_sales_details ").hide();
        $("#goodsGroup").show();
        $("#purchaseGroup").show();
        $("#availability").show();
        $("#stockRate").show();

        $("#submit_btn").show();
        $("#draft_btn").show();

        $("#bomRadioDiv").html(`<div class="goods-input for-manufac d-flex">

          <input type="radio" name="bomRequired_radio" value="1">

          <label for="" class="mb-0 ml-2">For Manufacturing</label>

        </div>

        <div class="goods-input for-trading d-flex">

          <input type="radio" name="bomRequired_radio" value="0">

          <label for="" class="mb-0 ml-2">For Trading</label>

        </div>`);

        $("#pricing").show();
        $(".error").hide();
        $(".error").html("");
        $("#notesModalBody").html("");
        $("#specificationDetails").show("");

        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();

      } else if (dataAttrVal == "SERVICES") {
        $("#serviceStock").show("");
        $("#mwp").hide();
        $("#submit_btn").show();
        $("#draft_btn").show();
        $("#bomCheckBoxDiv").html(``);
        $("#purchase").hide();
        $("#bomRadioDiv").html("");
        $("#goodsGroup").show();
        $("#purchaseGroup").hide();
        $("#availability").hide();
        $("#service_sales_details ").show();
        $("#tds ").hide();
        $("#basicDetails").hide();
        $("#storageDetails").hide();
        $("#pricing").show();
        $("#stockRate").hide();
        loadGLCode(3); //INCOME GL: 3

        $(".error").hide();
        $(".error").html("");
        $("#notesModalBody").html("");
        $("#specificationDetails").hide("");
        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();

      } else if (dataAttrVal == "SERVICEP") {
        $("#serviceStock").show("");
        $("#mwp").hide();
        $("#submit_btn").show();
        $("#draft_btn").show();
        $("#pricing").hide();
        $("#bomCheckBoxDiv").html(``);
        $("#purchase").hide();
        $("#bomRadioDiv").html("");
        $("#goodsGroup").show();
        $("#purchaseGroup").hide();
        $("#availability").hide();
        $("#specificationDetails").hide("");
        $("#service_sales_details ").show();
        $("#basicDetails").hide();
        $("#storageDetails").hide();
        $("#tds ").show();
        $("#stockRate").hide();

        loadGLCode(4); //EXPENSE GL:4

        $(".error").hide();
        $(".error").html("");
        $("#notesModalBody").html("");
        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();

      } else if (dataAttrVal == "ASSET") {
        loadGLCode(1); //ASSET GL:1
        $("#serviceStock").hide("");
        $("#cost-center").show("");
        $("#submit_btn").show();
        $("#draft_btn").show();
        $("#bomCheckBoxDiv").html("");
        $("#mwp").hide();
        $("#bomRadioDiv").html("");
        $("#pricing").hide();
        $("#purchase").html();
        $("#basicDetails").show();
        $("#storageDetails").show();
        $("#service_sales_details ").hide();
        $("#stockRate").show();
        $("#goodsGroup").hide();
        $("#purchaseGroup").hide();
        $("#availability").hide();
        $(".error").hide();
        $(".error").html("");
        $("#notesModalBody").html("");
        $("#specificationDetails").show("");
        $("#depKey").show("");
        $("#asset_gl").show();
        $("#asset_classification").show();
      } else {

        $("#submit_btn").hide();
        $("#draft_btn").hide();
        $("#mwp").hide();
        $("#bomCheckBoxDiv").html(``);
        $("#purchase").html("");
        $("#bomRadioDiv").html("");

        $("#pricing").hide();

        $(".error").hide();
        $(".error").html("");

        $("#notesModalBody").html("");
        $("#serviceStock").hide("");

        $("#asset_classification").hide("");
        $("#asset_classification_new").html("");
        $("#asset_classification_new").hide("");
        $("#depKey").hide("");
        $("#asset_gl").html();
        $("#asset_gl").hide();
      }



      let typeId = $(this).val();
      // alert(typeId);
      loadGoodGroup(typeId);
      load_group_modal(typeId);


      function load_group_modal(typeId) {
        //console.log("hiiiiii");
        // alert(typeId);
        $.ajax({

          type: "GET",

          url: `ajaxs/items/ajax-group-modal.php`,

          data: {
            typeId
          },



          beforeSend: function() {
            // $("#goodGroupDropDown").html(`<option value="">Loding...</option>`);
            $("#goodType_input").html(``);
            $("#goodType_id").html(``);

          },

          success: function(response) {

            //alert(response);
            var obj = jQuery.parseJSON(response);
            $("#goodType_input").val(obj['type_name']);
            $("#goodType_id").val(obj['type_id']);

            $.ajax({

              type: "GET",
              url: `ajaxs/items/ajax-good-groups.php`,
              data: {
                typeId
              },
              success: function(response) {
                $("#parent_group_dropdown").html(response);
              }

            });

          }

        });

      }



      function loadGoodGroup(typeId) {



        $.ajax({

          type: "GET",

          url: `ajaxs/items/ajax-good-groups.php`,

          data: {
            typeId
          },



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
      $(document).ready(function() {



        $('#addNewGoodGroupFormSubmitBtn').click(function(e) {

          //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

          event.preventDefault();

          let formData = $("#addNewGoodGroupForm").serialize();
          // console.log(formData);
          $.ajax({

            type: "POST",

            url: `ajaxs/items/ajax-good-groups.php`,

            data: formData,

            beforeSend: function() {

              $("#addNewGoodGroupFormSubmitBtn").toggleClass("disabled");

              $("#addNewGoodGroupFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

            },

            success: function(response) {
              console.log(response);
              // $("#goodGroupDropDown").html(response);

              // $('.goodGroupName').val('');
              // $('.goodGroupDesc').val('');

              // $("#addgoodGroupFormModal").modal('toggle');

              // $("#addNewgoodGroupFormSubmitBtn").html("Submit");

              // $("#addNewgoodGroupFormSubmitBtn").toggleClass("disabled");


              $("#goodGroupDropDown").html(response);

              $('#addNewGoodGroupForm').trigger("reset");

              $("#addNewGoodGroupFormModal").modal('toggle');

              $("#addNewGoodGroupFormSubmitBtn").html("Submit");

              $("#addNewGoodGroupFormSubmitBtn").toggleClass("disabled");

            }

          });

        });
      });

    });

    //**************************************************************

    $('#goodGroupDropDown')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodGroupFormModal">Add New</a></div>`);

      });

    $('#asset_classificationDropDown')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(``);

      });





    $('#buomDrop')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewUOMFormModal">Add New</a></div>`);

      });


    $('#iuomDrop')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewUOMFormModal">Add New</a></div>`);

      });







    // $('#hsnDropDown')

    //   .select2()

    //   .on('select2:open', () => {

    //     $(".select2-results:not(:has(a))").append(`<div class="col-md-12 mb-12"></div>`);

    //   });





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


    function loadhsn(pageNo, limit, keyword = null) {
      $.ajax({
        method: 'POST',
        data: {
          pageNo: pageNo,
          limit: limit,
          keyword: keyword,
        },
        url: `ajaxs/items/ajax-hsn.php`,
        beforeSend: function() {
          $(".hsnSearchSpinner").show();
          $(".hsn_tbody").html('<tr><td colspan="4"><span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Loading ...</td></tr>');
        },
        success: function(response) {
          $(".hsn_tbody").html(response);
          $(".hsnSearchSpinner").hide();

        }

      });

    }

    loadhsn(0, 50);

    $(document).ready(function() {
      $(".hsnSearchSpinner").hide();
      $('#searchbar').on('keyup keydown paste', function() {
        var keyword = $(this).val();
        var pageNo = 0;
        var limit = 50;
        loadhsn(pageNo, limit, keyword);
      });
    });

    $(document).on('submit', '#addNewhsnForm', function(event) {

      event.preventDefault();

      let formData = $("#addNewhsnForm").serialize();

      $.ajax({

        type: "POST",

        url: `ajaxs/items/ajax-hsn.php`,

        data: formData,

        beforeSend: function() {

          $("#addNewhsnFormSubmitBtn").toggleClass("disabled");

          $("#addNewhsnFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

        },

        success: function(response) {

          $("#hsnDropDown").html(response);

          $('#addNewhsnForm').trigger("reset");

          $("#addNewhsnFormModal").modal('toggle');

          $("#addNewhsnFormSubmitBtn").html("Submit");

          $("#addNewhsnFormSubmitBtn").toggleClass("disabled");

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


    $(document).ready(function() {

      /*@ Registration start */
      $('#addNewPurchaseGroupFormSubmitBtn').click(function(e) {


        //  $(document).on('submit', '#addNewPurchaseGroupForm', function(event) {

        event.preventDefault();

        let formData = $("#addNewPurchaseGroupForm").serialize();

        // console.log(formData);
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

      $("#creategoodsdata").val(data);

      //confirm('Are you sure to Submit?')

      $("#goodsSubmitForm").submit();

    });





    $(".edit_data").click(function() {

      var data = this.value;

      $("#editgoodsdata").val(data);

      //confirm('Are you sure to Submit?')

      $("#goodsEditForm").submit();

    });





    //volume calculation

    function calculate_volume() {


      let height = $("#height").val();

      let width = $("#width").val();

      let length = $("#length").val();
      let vol_unit = $(".volume_unit").val();
      //console.log(vol_unit);
      if (vol_unit == "m") {


        let resm = height * length * width;

        let res = resm * 1000000;

        $("#volcm").val(res);

        $("#volm").val(resm);

      } else {

        let res = height * length * width;

        let resm = res * 0.000001;
        $("#volcm").val(res);

        $("#volm").val(resm);
      }


      //console.log(res);

      // $("#volcm").val(res);

      // $("#volm").val(resm);





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






    function calculate_amount() {


      let stock = $("#stock").val();

      let rate = $("#rate").val();



      let res = stock * rate;

      $("#total").val(res);





      //console.log(res);

      // $("#volcm").val(res);

      // $("#volm").val(resm);





    }


    $("#stock").keyup(function() {

      calculate_amount();

    });

    $("#rate").keyup(function() {

      calculate_amount();

    });



    $(".volume_unit").change(function() {
      let vol_unit = $(".volume_unit").val();
      console.log(vol_unit);
      calculate_volume();

    });


    function compare() {


      let gross = $("#gross_weight").val();
      let net = $("#net_weight").val();

      if (Number(gross) < Number(net)) {
        $("#gross_span").html(`<span class="text-danger text-xs" id="gross_span">Gross weight can not Be lesser than net weight</small></span>`);



      } else {
        $("#gross_span").html("");
      }


    }

    $("#gross_weight").keyup(function() {

      compare();

    });

    $("#net_weight").keyup(function() {

      compare();

    });

    $("#gross_weight").keyup(function() {

      compare();

    });



    $("#buomDrop").change(function() {

      // let res = $(this).html();

      let res = $(this).find(":selected").text();

      $("#buom").val(res);
      $("#buom_per").html('<label id="buom_per">/' + res + '<label>')

      console.log("buomDrop", res);

    });



    $("#iuomDrop").change(function() {

      // let rel = $(this).html();

      let rel = $(this).find(":selected").text();

      $("#ioum").val(rel);

      console.log("iuomDrop", rel);

    });



    $("#goodGroupDropDown").select2({

      customClass: "Myselectbox",

    });

  });

  $('#minTime').change(function() {
    $("#maxTime option").eq($(this).find(':selected').index()).prop('selected', true);
  });
  $('#net_unit').change(function() {
    $("#gross_unit option").eq($(this).find(':selected').index()).prop('selected', true);
  });


  $(document).on("click", "#hsnsavebtn", function() {



    //console.log("clickinggggggggg");
    let radioBtnVal = $('input[name="hsn"]:checked').val();
    let hsncode = ($(`#hsnCode_${radioBtnVal}`).html());
    let hsndesc = ($(`#hsnDescription_${radioBtnVal}`).html());
    console.log(hsndesc);
    // let hsnpercentage = ($(`#taxPercentage_${radioBtnVal}`).html()).trim();
    //salert(radioBtnVal);
    $("#hsnlabelOne").html(radioBtnVal);
    $("#hsnlabelservice").html(radioBtnVal);


    $("#hsnDescInfo").html(hsndesc);

  });


  $(document).on("click", "#tdssavebtn", function() {



    //console.log("clickinggggggggg");
    let radioBtnVal = $('input[name="tds"]:checked').val();
    let sec = $('input[name="tds"]:checked').attr("data-attr");
    //console.log(sec);
    let section = ($(`#section_${radioBtnVal}`).html());
    // let hsndesc = ($(`#hsnDescription_${radioBtnVal}`).html()).trim();
    // let hsnpercentage = ($(`#taxPercentage_${radioBtnVal}`).html()).trim();
    console.log(radioBtnVal);
    $("#tdslabel").html(sec);

  });





  //uom add


  $('#addNewUOMFormSubmitBtn').click(function(e) {

    //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

    event.preventDefault();

    let formData = $("#addNewUOMForm").serialize();
    //console.log(formData);
    $.ajax({

      type: "POST",

      data: formData,

      url: `ajaxs/items/ajax-uom.php`,




      beforeSend: function() {

        $("#addNewUOMFormSubmitBtn").toggleClass("disabled");

        $("#addNewUOMFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

      },

      success: function(response) {
        console.log(response);
        $("#buomDrop").html(response);
        $("#iuomDrop").html(response);

        $('.UOMName').val('');
        $('.UOMDesc').val('');

        //$("#addNewUOMFormModal").modal('toggle');

        $("#addNewUOMFormSubmitBtn").html("Submit");

        $("#addNewUOMFormSubmitBtn").toggleClass("disabled");
        $('.addNewUOM').hide();


      }

    });

  });


  //end uom
  function addMultiQtyf(id) {
    let addressRandNo = Math.ceil(Math.random() * 100000);
    $(`.modal-add-row_${id}`).append(`
                              <div class="row othe-cost-infor pl-0 pr-0">

                                <div class="col-lg-5 col-md-5 col-sm-5">

                                  <div class="form-input">

                                    <label>Specification</label>

                                    <input type="text" name="spec[${addressRandNo}][spec_name]" data-attr="${addressRandNo}" class="form-control spec_vldtn specification_${addressRandNo}" id="">

                                  </div>

                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5">

                                  <div class="form-input">

                                    <label>Specification Details</label>

                                    <input type="text" name="spec[${addressRandNo}][spec_detail]" data-attr="${addressRandNo}" class="form-control spec_dtls_vldtn specificationDetails_${addressRandNo}" id="">

                                  </div>

                                </div>



                                <div class="col-lg col-md-6 col-sm-6">
                                                                    <div class="add-btn-minus">
                                                                        <a style="cursor: pointer" class="btn btn-danger">
                                                                            <i class="fa fa-minus"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                              </div>
                           `);
  }

  $(document).on("click", ".add-btn-minus", function() {
    $(this).parent().parent().remove();
  });

  $(document).on("keyup", ".form-control-sm", function() {
    // alert(1);
    var search_term = $('#searchbar').val();
    console.log(search_term)
    $('.hsn-code').removeHighlight().highlight(search_term);
  });
</script>

<script>
  $('#DataTables_Table_0').dataTable({
    "filter": true,
    "length": false
  });
</script>
<script>
  $('.item_name').keyup(function() {
    $("#suggestedNames").slideToggle(200);
    // $("#suggestedNames").css("padding", "15px 20px");
    // {alert('oi');}
    var item_name = $(this).val();


    //alert(item_name);
    $.ajax({

      type: "GET",
      url: `ajaxs/items/ajax-suggestions.php`,
      data: {
        item_name: item_name
      },
      beforeSend: function() {

        // $("#glCode").html(`<option value="">Loding...</option>`);

      },

      success: function(response) {
        console.log(response);
        var obj = jQuery.parseJSON(response);
        $("#suggestedNames").html(obj['item_sugg']);

      }
    })
  });


  $('.item_name').keydown(function() {
    // var padding = 10;
    // $("#suggestedNames").css("padding","0");

    // {alert('oi');}
    var item_name = $(this).val();

    $("#suggestedNames").css("padding", "0", "display", "none");
    //alert(item_name);
    $.ajax({

      type: "GET",
      url: `ajaxs/items/ajax-suggestions.php`,
      data: {
        item_name: item_name
      },
      beforeSend: function() {

        // $("#glCode").html(`<option value="">Loding...</option>`);

      },

      success: function(response) {
        console.log(response);
        var obj = jQuery.parseJSON(response);
        $("#suggestedNames").html(obj['item_sugg']);

      }
    })
  });
</script>

<!-- Note that this code snippet uses AJAX to retrieve the suggested names from the PHP script without reloading the page. -->

<script>
  // function HSNfunction(){
  //   console.log('oi');
  //   var hsnSearch = $("#hsnSearch").val();
  //  // alert(hsnSearch);
  // $("#hsnName").val(hsnSearch);
  // //  document.getElementById("hsnAdd").showModal(); 
  //  $("#hsnAdd").modal('toggle');
  // }


  // $(document).on("click", "#searchValue", function(e) {
  //   e.preventDefault();
  //   // alert(1);
  //   console.log('oi')
  //   var hsnSearch = $("#hsnSearch").val();
  //   $("#hsnName").val(hsnSearch);
  //   $("#hsnAdd").modal('show');


  // });

  $(document).on("click", '#addNewHSNFormSubmitBtn', function(e) {
    //alert(1);
    //  $(document).on('submit', '#addNewGoodGroupForm', function(event) {

    event.preventDefault();

    var code = $("#hsnName").val();
    var desc = $("#hsnDesc").val();
    var rate = $("#hsnRate").val();
    var public = $("#hsnPublic").val();

    alert(public);
    //console.log(formData);
    $.ajax({

      type: "POST",

      data: {
        hsnCode: code,
        hsnDesc: desc,
        hsnRate: rate,
        hsnPublic: public
      },

      url: `ajaxs/items/ajax-hsn-submit.php`,




      beforeSend: function() {

        $("#addNewHSNFormSubmitBtn").toggleClass("disabled");

        $("#addNewHSNFormSubmitBtn").html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Submitting...');

      },

      success: function(response) {
        console.log(response);
        // $("#buomDrop").html(response);
        // $("#iuomDrop").html(response);

        // $('.UOMName').val('');
        // $('.UOMDesc').val('');

        //$("#addNewUOMFormModal").modal('toggle');

        $("#addNewHSNFormSubmitBtn").html("Submit");

        $("#addNewHSNFormSubmitBtn").toggleClass("disabled");
        $('#hsnAdd').hide();


      }

    });

  });

  function calculate_service_amount() {


    let stock = $("#service_stock").val();

    let rate = $("#service_rate").val();



    let res = stock * rate;

    $("#service_total").val(res);


  }

  $("#service_stock").keyup(function() {

    calculate_service_amount();

  });

  $("#service_rate").keyup(function() {

    calculate_service_amount();

  });



  var link = document.getElementById("my-link");

  link.addEventListener("click", function(event) {
    if (link.getAttribute("href") === "#") {
      event.preventDefault(); // prevent default action of navigating to #
    }
  });

  //   $("#asset_classificationDropDown").change(function() {
  // console.log(1);
  // // let dataAttrVal = $("#asset_classificationDropDown").find(':selected').val();
  // // alert(dataAttrVal);



  //   }); 
</script>



<script>
  $(document).ready(function() {

    function addAssetClll(val, valclass) {

      $.ajax({

        type: "GET",

        url: `ajaxs/items/ajax-asset-classification.php`,
        data: {
          val
        },


        beforeSend: function() {

          $(`.${valclass}`).html("");

        },

        success: function(response) {
          //  console.log(response);
          $(`.${valclass}`).show();
          $(`.${valclass}`).append(response);

        }


      });
    }

    function checkdepkey(val) {


      $.ajax({

        type: "GET",

        url: `ajaxs/items/ajax-asset-classification.php`,
        data: {
          val: val,
          act: "key"
        },


        beforeSend: function() {

          // $(`.${valclass}`).html("");

        },

        success: function(response) {
          //console.log(response);
          $("#despkey_id").html(response);
          $("#dep_key_val").val(response);



        }


      });


    }


    $(document).on("change", ".asset_classificationDropDown", function() {



      let valclass = $(this).data('classattr');
      // alert(valclass);
      $(`.${valclass}`).html("");
      let val = $(this).val();
      addAssetClll(val, valclass);
      checkdepkey(val);

    });



  });
  $(document).on("change", "#goodGroupDropDown", function() {



    let valclass = $(this).data('classattr');
    //alert(valclass);
    $(`.${valclass}`).html("");
    let val = $(this).val();
    //alert(val);
    GroupChild(val, valclass);


  });


  function GroupChild(val, valclass) {


    $.ajax({

      type: "GET",

      url: `ajaxs/items/ajax-group-child.php`,
      data: {
        val: val,
        act: "key"
      },


      beforeSend: function() {

        // $(`.${valclass}`).html("");

      },

      success: function(response) {
        // alert(response);
        $(`.${valclass}`).html(response);




      }


    });


  }

  var DragAndDrop = (function(DragAndDrop) {
    function shouldAcceptDrop(item) {
      var $target = $(this).closest("li");
      var $item = item.closest("li");

      if ($.contains($item[0], $target[0])) {
        // can't drop on one of your children!
        return false;
      }

      return true;
    }

    function itemOver(event, ui) {}

    function itemOut(event, ui) {}

    function itemDropped(event, ui) {
      var $target = $(this).closest("li");
      var $item = ui.draggable.closest("li");

      var $srcUL = $item.parent("ul");
      var $dstUL = $target.children("ul").first();

      // destination may not have a UL yet
      if ($dstUL.length == 0) {
        $dstUL = $("<ul></ul>");
        $target.append($dstUL);
      }

      $item.slideUp(50, function() {
        $dstUL.append($item);

        if ($srcUL.children("li").length == 0) {
          $srcUL.remove();
        }

        $item.slideDown(50, function() {
          $item.css("display", "");
        });
      });
    }

    DragAndDrop.enable = function(selector) {
      $(selector).find(".node-cpe").draggable({
        helper: "clone"
      });

      $(selector).find(".node-cpe, .node-facility").droppable({
        activeClass: "active",
        hoverClass: "hover",
        accept: shouldAcceptDrop,
        over: itemOver,
        out: itemOut,
        drop: itemDropped,
        greedy: true,
        tolerance: "pointer"
      });
    };

    return DragAndDrop;
  })(DragAndDrop || {});

  (function($) {
    $.fn.beginEditing = function(whenDone) {
      if (!whenDone) {
        whenDone = function() {};
      }

      var $node = this;
      var $editor = $(
        "<input type='text' style='width:auto; min-width: 25px;'></input>"
      );
      var currentValue = $node.text();

      function commit() {
        $editor.remove();
        $node.text($editor.val());
        whenDone($node);
      }

      function cancel() {
        $editor.remove();
        $node.text(currentValue);
        whenDone($node);
      }

      $editor.val(currentValue);
      $editor.blur(function() {
        commit();
      });
      $editor.keydown(function(event) {
        if (event.which == 27) {
          cancel();
          return false;
        } else if (event.which == 13) {
          commit();
          return false;
        }
      });

      $node.empty();
      $node.append($editor);
      $editor.focus();
      $editor.select();
    };
  })(jQuery);

  $(function() {
    DragAndDrop.enable("#dragRoot");

    $(document).on("dblclick", "#dragRoot *[class*=node]", function() {
      $(this).beginEditing();
    });
  });

  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('show');
    $('#overlay').toggleClass('show');
    $('.nav-action').toggleClass('show');
    // if ($('.blur-body').css('filter') === 'blur(2px)') {
    //     $('.blur-body').css('filter', 'none');
    // } else {
    //     $('.blur-body').css('filter', 'blur(2px)');
    // }
    $('.blur-body .tab-content').toggleClass('blur');
  });

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap.js"></script>


<script src="<?= BASE_URL; ?>public/validations/goodsValidation.js"></script>
<!-- <script src="https://johannburkard.de/resources/Johann/jquery.highlight-4.js"></script> -->