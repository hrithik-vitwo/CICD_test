<?php
require_once("../../app/v1/connection-branch-admin.php");

// administratorLocationAuth();

require_once("../common/header.php");

require_once("../common/navbar.php");

require_once("../common/sidebar.php");

require_once("../common/pagination.php");

require_once("../../app/v1/functions/branch/func-goods-controller.php");

require_once("../../app/v1/functions/branch/func-bom-controller.php");

require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");



$goodsController = new GoodsController();

$goodsBomController = new GoodsBomController();





if (isset($_POST["creategoodsdata"])) {





  $addNewObj = $goodsController->createGoods($_POST);

  //console($addNewObj);

  swalToast($addNewObj["status"], $addNewObj["message"]);
}





if (isset($_POST["editgoodsdata"])) {

  $addNewObj = $goodsController->editGoods($_POST);

  swalToast($addNewObj["status"], $addNewObj["message"]);
}



if (isset($_POST["add-table-settings"])) {

  $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);

  swalToast($editDataObj["status"], $editDataObj["message"]);
}

?>



<link rel="stylesheet" href="../../public/assets/listing.css">

<link rel="stylesheet" href="../../public/assets/sales-order.css">

<link rel="stylesheet" href="../../public/assets/accordion.css">


<style>
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


  .btn-transparent {
    position: absolute;
    top: 23px;
    left: 9px;
    height: 35px;
    z-index: 9;
    width: 92%;
    background: transparent !important;
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

  .hsn-add .col a button {
    width: 100%;
  }

  .goods-modal .modal-dialog {
    max-width: 100%;
    width: 50%;
  }


  .goods-modal .modal-body {
    width: 100%;
    top: -30px;
  }

  .item-img {
    margin-left: 0;
    height: auto !important;
    position: relative;
    top: 0 !important;
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .location-item-modal .modal-header .item-img img {
    max-width: 100%;
  }

  .answer-section {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .answer-section div {
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .item-img img {
    min-height: auto;
  }

  .action-input {
    display: flex;
    gap: 25px;
  }

  .goods-flex-btn {
    justify-content: space-between;
  }

  .location-item-modal .modal-header {
    height: 345px;
  }

  .location-item-modal .modal-header .nav.nav-tabs {
    position: relative;
    top: -15px;
    padding-left: 48px;
  }


  .item-specification-row .item-img {
    margin-left: 0;
    width: 200px;
    height: 100%;
    position: relative;
    top: 0;
  }

  .item-specification-row .item-img img {
    max-width: 100%;
  }

  .item-specification-row .service-img {
    margin-left: auto;
    width: 100%;
    height: 200px;
    position: relative;
    top: 0;
    padding: 20px;
    text-align: center;
  }

  .item-specification-row .service-img .service-icon {
    max-width: 50%;
  }

  .head-title p {
    margin: 15px 0;
    line-height: 25px;
    font-size: 13px;
  }

  .head-title p.heading.lead {
    font-size: 14px;
    font-weight: 300;
  }

  .head-title .item-desc {
    line-height: 18px;
    font-size: 11px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
  }


  .head-title .item-desc {
    line-height: 18px;
    font-size: 11px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
  }

  .classifications-acc .accordion-card-details .display-flex-space-between {
    height: 68px;
  }

  .detail-view-accordion .display-flex-space-between p:nth-child(2) {
    position: absolute;
    left: 19%;
    text-align: left;
  }

  .classification-accordion .display-flex-space-between p.group-desc {
    width: 350px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .text-elipse {
    width: 350px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>


<?php

if (isset($_GET['create'])) {

?>



  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper is-location-master is-location-master-create">

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

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Goods List</a></li>

          <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i> Create Goods</a></li>

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

                    <div class="card-body goods-card-body others-info vendor-info so-card-body classification-card-body" style="height: 174px;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <select id="goodTypeDropDown" name="goodsType" class="form-control" required>

                                  <option value="">Goods Type</option>

                                </select>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <select id="goodGroupDropDown" name="goodsGroup" class="form-control" required>

                                  <option value="">Goods Group</option>

                                </select>

                              </div>

                            </div>







                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <select id="purchaseGroupDropDown" name="purchaseGroup" class="form-control" required>

                                  <option value="">Purchase Group</option>

                                </select>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

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

                                <!-- <label for="" class="mb-0">Required BOM</label>

                                <input type="checkbox" name="bomRequired"> -->

                              </div>
                              <div class="form-inline float-right" id="bomRadioDiv">

                                <!-- <div class="goods-input for-manufac d-flex">

                                  <input type="radio" name="bomRequired">

                                  <label for="" class="mb-0 ml-2">For Manufacturing</label>

                                </div>

                                <div class="goods-input for-trading d-flex">

                                  <input type="radio" name="bomRequired">

                                  <label for="" class="mb-0 ml-2">For Trading</label>

                                </div> -->

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>



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

              </div>



              <div class="row" id="purchase">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Purchase Details</h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Purchasing Value Key</label>

                                <input type="text" name="purchasingValueKey" class="form-control purchasing_value" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">

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

              <div class="row">

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

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Item Name</label>

                                <input type="text" name="itemName" class="form-control item_name" id="exampleInputBorderWidth2">

                              </div>

                            </div>

                            <div class="row calculate-parent-row">

                              <div class="col-lg-6 col-md-6 col-sm-6 col">

                                <div class="form-input">

                                  <select id="buomDrop" name="baseUnitMeasure" class="form-control">

                                    <option value="">Base Unit of Measure</option>

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

                                  <select id="iuomDrop" name="issueUnit" class="form-control">

                                    <option value="">Issue Unit of Measure</option>

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

                              <div class="col-lg-2 col-md-2 col-sm-2 col">

                                <input type="text" class="form-control bg-none" placeholder="1">

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3 col">

                                <input type="text" name="netWeight" class="form-control bg-none" id="buom" placeholder="Base Unit of Measure" readonly>

                              </div>

                              <div class="col-lg-1 col-md-1 col-sm-1 col">

                                <p class="equal-style mt-1">=</p>

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3 col">

                                <input type="text" name="rel" class="form-control item_rel" id="rel">

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3 col">

                                <input type="text" name="netWeight" class="form-control bg-none" placeholder="Issue Unit of Measure" id="ioum" readonly>

                              </div>

                            </div>



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
                                <select name="gross_unit" class="form-control " id="gross_unit" disabled>
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
                                  <option value="cm">cm</option>
                                  <option value="m">m</option>

                                </select>
                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>Volume In CM<sup>3</sup></label>

                                <input type="text" name="volume" class="form-control" id="volcm" readonly>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>Volume In M<sup>3</sup></label>

                                <input type="text" name="volumeCubeCm" class="form-control" id="volm" readonly>

                              </div>

                            </div>





                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>Item Description</label>

                                <textarea class="item_desc" rows="3" name="itemDesc" id="exampleInputBorderWidth2" placeholder="Item Description"></textarea>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>HSN </label>

                                <button class="btn btn-primary btn-transparent" type="button" data-toggle="modal" data-target="#goodsHSNModal"></button>

                                <select name="hsnnnn" class="form-control">

                                  <option id="hsnlabel" value="">HSN</option>

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

                                <label for="">Max Discount</label>

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

            </div>



            <div class="btn-section mt-2 mb-2">

              <button class="btn btn-primary save-close-btn btn-xs float-right add_data" value="add_post">Submit</button>

              <button class="btn btn-danger save-close-btn btn-xs float-right add_data" value="add_draft">Save as Draft</button>

            </div>



          </div>



          <!-----hsn modal start------->


          <div class="modal fade hsn-dropdown-modal" id="goodsHSNModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
            <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
              <div class="modal-content">
                <div class="modal-header">HSN
                  <input class="form-control" id="searchbar" onkeyup="search_hsn()" type="text" name="search" placeholder="Search..">
                </div>
                <div class="modal-body" style="height: 500px; overflow: auto;">
                  <div class="card">
                    <?php
                    $getAllHSN = $goodsController->getAllHsn();
                    $hsns = $getAllHSN['data'];
                    foreach ($hsns as $hsn) {
                      // console($hsn); 
                    ?>
                      <div class="card-body m-3 hsn-code">
                        <div class="hsn-header">
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
                        </div>
                      </div>
                    <?php
                    }
                    ?>
                    <div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" id="hsnsavebtn" data-dismiss="modal">Save changes</button>
                </div>
              </div>
            </div>
          </div>


          <!-----hsn modal end------->

        </form>



        <!-- modal -->

        <div class="modal" id="addNewGoodTypesFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title">Add New Good Type</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewGoodTypesForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="goodTypeName" class="form-control" required>

                      <label>Type Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="goodTypeDesc" class="form-control" required>

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






        <!-- modal -->

        <div class="modal" id="addNewGoodGroupFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title">Add New Good Group</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewGoodGroupForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="goodGroupName" class="form-control" required>

                      <label>Group Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="goodGroupDesc" class="form-control" required>

                      <label>Group Description</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group btn-col">

                      <button type="submit" id="addNewGoodGroupFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                    </div>

                  </div>

                </form>

              </div>

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

                      <option value="">Goods Group</option>

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



        <!-- purchase group -->



        <!-- modal -->

        <div class="modal" id="addNewPurchaseGroupFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title">Add New Purchase Group</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewPurchaseGroupForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="purchaseGroupName" class="form-control" required>

                      <label>Purchase Group Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="purchaseGroupDesc" class="form-control" required>

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

  <div class="content-wrapper is-location-master is-location-master-edit">

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

          <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Goods List</a></li>

          <li class="breadcrumb-item"><a class="text-dark"><i class="fa fa-edit po-list-icon"></i> Edit Goods</a></li>

          <li class="back-button">

            <a href="<?= basename($_SERVER['PHP_SELF']); ?>">

              <i class="fa fa-reply po-list-icon"></i>

            </a>

          </li>

        </ol>

        <!-- <ol class="breadcrumb">

              <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>" class="text-dark"><i class="fas fa-home"></i> Home</a></li>

              <li class="breadcrumb-item"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Manage Goods</a></li>

              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark">Edit Goods</a></li>

            </ol> -->


        <!-- <div class="col-md-6" style="display: flex;">

            <button class="btn btn-danger btnstyle ml-2 edit_data" value="edit_draft">Draft</button>

            <button class="btn btn-primary btnstyle gradientBtn ml-2 edit_data" value="edit_post"><i class="fa fa-plus fontSize"></i> Save</button>

          </div> -->


      </div>

    </div>

    <!-- /.content-header -->

    <?php

    $itemId = base64_decode($_GET['edit']);

    $sql = "SELECT * FROM `erp_inventory_items` as item , `erp_inventory_mstr_purchase_groups` as purchase_group, `erp_inventory_mstr_good_types` as good_type , `erp_inventory_mstr_good_groups` as good_group,`erp_inventory_item_price` as price WHERE `item`.`goodsType` = `good_type`.`goodTypeId` AND `item`.`goodsGroup` = `good_group`.`goodGroupId` AND item.purchaseGroup = `purchase_group`.`purchaseGroupId` AND `price`.`ItemCode`=`item`.`itemCode` AND `item`.`itemId` = $itemId";

    $resultObj = queryGet($sql);

    $row = $resultObj["data"];



    //console($row);



    //echo  $sql = "SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=$editVendorId";

    //  $res = $dbCon->query($sql);

    //   $row = $res->fetch_assoc();

    // $row=[];

    // echo "<pre>";

    // print_r($row);

    // echo "</pre>";

    ?>







    <!-- Main content -->

    <section class="content">

      <div class="container-fluid">

        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="goodsEditForm" name="goodsEditForm">

          <input type="hidden" name="editgoodsdata" id="editgoodsdata" value="<?= $itemId ?>">

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

                    <div class="card-body goods-card-body others-info vendor-info so-card-body classification-card-body" style="height: 174px;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <select id="goodTypeDropDown" name="goodsType" class="form-control" required>

                                  <option value="">Goods Type</option>

                                </select>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <select id="goodGroupDropDown" name="goodsGroup" class="form-control" required>

                                  <option value="">Goods Group</option>

                                </select>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <select id="purchaseGroupDropDown" name="purchaseGroup" class="form-control" required>

                                  <option value="">Purchase Group</option>

                                </select>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

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
                                                          } ?>>Half Yearly</option>

                                  <option value="Year" <?php if ($row['availabilityCheck'] == "Year") {

                                                          echo "selected";
                                                        } ?>>Year</option>

                                </select>

                              </div>

                            </div>

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-inline float-right" id="bomCheckBoxDiv">

                                <label for="" class="mb-0">Required BOM</label>

                                <input type="checkbox" name="bomRequired">

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

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

                                <input type="text" name="storageControl" class="form-control" id="exampleInputBorderWidth2" placeholder="Storage Control" value=<?= $row['storageControl'] ?>>

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Max Storage Period</label>

                                <input type="text" name="maxStoragePeriod" class="form-control" id="exampleInputBorderWidth2" placeholder="Max Storage Period" value=<?= $row['maxStoragePeriod'] ?>>

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Max Storage Period Time Unit </label>

                                <input type="text" name="maxtimeUnit" class="form-control" id="exampleInputBorderWidth2" placeholder="Time Unit" value=<?= $row['maxStoragePeriodTimeUnit'] ?>>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Minimum Remain Self life</label>

                                <input type="text" name="minRemainSelfLife" class="form-control" id="exampleInputBorderWidth2" placeholder="Min Remain Self Life" value=<?= $row['minRemainSelfLife'] ?>>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Min Remain Self Life Time Unit</label>

                                <input type="text" name="mintimeUnit" class="form-control" id="exampleInputBorderWidth2" placeholder="Time Unit" value=<?= $row['minRemainSelfLifeTimeUnit'] ?>>

                              </div>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div>

              </div>

              <div class="row">

                <div class="col-lg-12 col-md-12 col-sm-12">

                  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

                    <div class="card-header">

                      <h4>Purchase Details</h4>

                    </div>

                    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

                      <div class="row">

                        <div class="col-lg-12 col-md-12 col-sm-12">

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label for="">Purchasing Value Key</label>

                                <input type="text" name="purchasingValueKey" class="form-control purchasing_value" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key" value=<?= $row['purchasingValueKey'] ?>>

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

              <div class="row">

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

                          <div class="row goods-info-form-view customer-info-form-view">

                            <div class="col-lg-12 col-md-12 col-sm-12">

                              <div class="form-input">

                                <label>Item Name</label>

                                <input type="text" name="itemName" class="form-control item_name" id="exampleInputBorderWidth2" value="<?= $row['itemName'] ?>">

                              </div>

                            </div>

                            <div class="row calculate-parent-row">

                              <div class="col-lg-6 col-md-6 col-sm-6 col">

                                <div class="form-input">

                                  <!-- <input type="text" name="baseUnitMeasure" class="form-control form-control-border borderColor buomDrop" id="exampleInputBorderWidth2" placeholder="baseUnitOfMeasure" value=<?= $row['baseUnitMeasure'] ?>> -->


                                  <select id="buomDrop" name="baseUnitMeasure" class="form-control">

                                    <option value="">Base Unit of Measure</option>

                                    <?php

                                    $uomList = $goodsController->fetchUom()['data'];



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

                                  <!-- <input type="text" name="issueUnit" class="form-control form-control-border borderColor iuomDrop" id="exampleInputBorderWidth2" placeholder="issueUnit" value=<?= $row['issueUnitMeasure'] ?>> -->


                                  <select id="iuomDrop" name="issueUnit" class="form-control">

                                    <option value="">Issue Unit of Measure</option>

                                    <?php

                                    $uomList = $goodsController->fetchUom()['data'];



                                    foreach ($uomList as $oneUomList) {

                                    ?>

                                      <option value="<?= $oneUomList['uomId'] ?>" <?php if ($oneUomList['uomId'] == $row['issueUnitMeasure']) {
                                                                                    echo "selected";
                                                                                  } ?>><?= $oneUomList['uomName'] ?></option>

                                    <?php

                                    }

                                    ?>

                                  </select>

                                </div>

                              </div>

                            </div>


                            <div class="row calculate-row">

                              <div class="col-lg-2 col-md-2 col-sm-2 col">

                                <input type="text" class="form-control bg-none" placeholder="1">

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3 col">

                                <input type="text" name="netWeight" class="form-control bg-none" id="buom" placeholder="Base Unit of Measure" readonly>

                              </div>

                              <div class="col-lg-1 col-md-1 col-sm-1 col">

                                <p class="equal-style mt-1">=</p>

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3 col">

                                <input type="text" name="rel" class="form-control item_rel" id="rel" value="<?= $row['uomRel']  ?>">

                              </div>

                              <div class="col-lg-3 col-md-3 col-sm-3 col">

                                <input type="text" name="netWeight" class="form-control bg-none" placeholder="Issue Unit of Measure" id="ioum" value="" readonly>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Net Weight</label>

                                <input type="text" name="netWeight" class="form-control net_weight" id="exampleInputBorderWidth2" value=<?= $row['netWeight'] ?>>

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Gross Weight</label>

                                <input type="text" name="grossWeight" class="form-control gross_weight" id="exampleInputBorderWidth2" value=<?= $row['grossWeight'] ?>>

                              </div>

                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Height</label>

                                <input step="0.01" type="number" name="height" class="form-control calculate_volume" id="height" value=<?= $row['height'] ?>>

                              </div>

                            </div>



                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Width</label>

                                <input step="0.01" type="number" name="width" class="form-control calculate_volume" id="width" value=<?= $row['width'] ?>>

                              </div>

                            </div>



                            <div class="col-lg-4 col-md-4 col-sm-4">

                              <div class="form-input">

                                <label for="">Length</label>

                                <input step="0.01" type="number" name="length" class="form-control calculate_volume" id="length" value=<?= $row['length'] ?>>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>Volume In CM<sup>3</sup></label>

                                <input type="text" name="volumeCubeCm" class="form-control" id="volcm" value=<?= $row['volumeCubeCm'] ?> readonly>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>Volume In M<sup>3</sup></label>

                                <input type="text" name="volume" class="form-control" id="volm" value=<?= $row['volume'] ?> readonly>

                              </div>

                            </div>




                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>Item Description</label>

                                <textarea rows="3" name="itemDesc" class="form-control item_desc" id="exampleInputBorderWidth2" placeholder="Item Description"><?= $row['itemDesc'] ?></textarea>

                              </div>

                            </div>



                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label>HSN </label>

                                <select id="hsnDropDown" class="form-control">

                                  <option value="">HSN</option>

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

              <div class="row">

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

                                <input step="0.01" type="number" name="price" class="form-control price" id="exampleInputBorderWidth2" value="<?= $row['ItemPrice'] ?>">

                              </div>

                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-6">

                              <div class="form-input">

                                <label for="">Max Discount</label>

                                <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" value="<?= $row['ItemMaxDiscount'] ?>">

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

            <div class="btn-section mt-2 mb-2">

              <button class="btn btn-primary save-close-btn btn-xs float-right edit_data" value="edit_draft">Submit</button>

              <button class="btn btn-danger save-close-btn btn-xs float-right edit_data" value="edit_draft">Save as Draft</button>

            </div>


          </div>






          <div class="row">

            <div class="col-md-12">

              <button type="button" class="btn-position" data-toggle="modal" data-target="#myModal"><i class="fa fa-cog" aria-hidden="true"></i></button>

              <div id="accordion">

                <!-- <div class="card card-primary">

                  <div class="card-header cardHeader">

                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseOne"> Classification </a> </h4>

                  </div>

                  <div id="collapseOne" class="collapse show" data-parent="#accordion">

                    <div class="card-body">

                      <div class="row">

                        <div class="col-md-6 mb-3">

                          <div class="input-group">

                            <select id="goodTypeDropDown" name="goodsType" class="form-control form-control-border borderColor">

                              <option value="">Goods Type</option>

                            </select>

                          </div>

                        </div>

                        <div class="col-md-6 mb-3">

                          <div class="input-group">

                            <select id="goodGroupDropDown" name="goodsGroup" class="form-control form-control-border borderColor">

                              <option value="">Goods Group</option>

                            </select>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <select id="purchaseGroupDropDown" name="purchaseGroup" class="select2 form-control form-control-border borderColor">

                              <option value="">Purchase Group</option>

                            </select>

                          </div>

                        </div>

                        

                        <div class="col-md-6">

                          <div class="input-group">

                            <select id="avl_check" name="availabilityCheck" class="select2 form-control form-control-border borderColor">

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
                                                      } ?>>Half Yearly</option>

                              <option value="Year" <?php if ($row['availabilityCheck'] == "Year") {

                                                      echo "selected";
                                                    } ?>>Year</option>

                            </select>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div> -->

                <!-- <div class="card card-danger">

                  <div class="card-header cardHeader">

                    <h4 class="card-title w-100"> <a class="d-block w-100 text-dark" data-toggle="collapse" href="#collapseTwo"> Basic Details </a> </h4>

                  </div>

                  <div id="collapseTwo" class="collapse" data-parent="#accordion">

                    <div class="card-body">

                      <div class="row">



                        <div class="col-md-6">

                          <div class="input-group">

                            <input type="text" name="itemName" class="form-control item_name" id="exampleInputBorderWidth2" value=<?= $row['itemName'] ?>>

                            <label>Item Name</label>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <input type="text" name="netWeight" class="form-control net_weight" id="exampleInputBorderWidth2" value=<?= $row['netWeight'] ?>>

                            <label>Net Weight</label>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="input-group">

                            <input type="text" name="grossWeight" class="form-control gross_weight" id="exampleInputBorderWidth2" value=<?= $row['grossWeight'] ?>>

                            <label>Gross Weight</label>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Volume :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="volume" class="form-control form-control-border borderColor vol" id="exampleInputBorderWidth2" placeholder="volume" value=<?= $row['volume'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">height :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="height" class="form-control form-control-border borderColor height" id="exampleInputBorderWidth2" placeholder="height" value=<?= $row['height'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">width :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="width" class="form-control form-control-border borderColor width" id="exampleInputBorderWidth2" placeholder="width" value=<?= $row['width'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">length :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="length" class="form-control form-control-border borderColor length" id="exampleInputBorderWidth2" placeholder="length" value=<?= $row['length'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Base Unit Of Measure :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="baseUnitMeasure" class="form-control form-control-border borderColor buomDrop" id="exampleInputBorderWidth2" placeholder="baseUnitOfMeasure" value=<?= $row['baseUnitMeasure'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Issue Unit :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="issueUnit" class="form-control form-control-border borderColor iuomDrop" id="exampleInputBorderWidth2" placeholder="issueUnit" value=<?= $row['issueUnitMeasure'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-12">

                          <textarea type="text" name="itemDesc" class="form-control form-control-border borderColor item_desc" id="exampleInputBorderWidth2" placeholder="Item Description"><?= $row['itemDesc'] ?></textarea>

                        </div>

                      </div>

                    </div>

                  </div>

                </div> -->

                <!-- <div class="card card-success">



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

                              <input type="text" name="storageBin" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Bin" value=<?= $row['storageBin'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Picking Area :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="pickingArea" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Picking Area" value=<?= $row['pickingArea'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Temp Control :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="tempControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Temp Control" value=<?= $row['tempControl'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Storage Control :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="storageControl" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Storage Control" value=<?= $row['storageControl'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Max Storage Period :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="maxStoragePeriod" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Max Storage Period" value=<?= $row['maxStoragePeriod'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Max Storage Period Time Unit :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="maxtimeUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Time Unit" value=<?= $row['maxStoragePeriodTimeUnit'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Min Remain Self Life :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="minRemainSelfLife" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Min Remain Self Life" value=<?= $row['minRemainSelfLife'] ?>>

                            </div>

                          </div>

                        </div>

                        <div class="col-md-6">

                          <div class="row">

                            <div class="col-md-6">

                              <label for="" class="form-control borderNone">Min Remain Self Life Time Unit :</label>

                            </div>

                            <div class="col-md-6">

                              <input type="text" name="mintimeUnit" class="form-control form-control-border borderColor" id="exampleInputBorderWidth2" placeholder="Time Unit" value=<?= $row['minRemainSelfLifeTimeUnit'] ?>>

                            </div>

                          </div>

                        </div>

                      </div>

                    </div>

                  </div>

                </div> -->

                <!-- <div class="card card-success">

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

                              <input type="text" name="purchasingValueKey" class="form-control form-control-border borderColor purchasing_value" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key" value=<?= $row['purchasingValueKey'] ?>>

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

        </form>



        <!-- modal -->

        <div class="modal" id="addNewGoodTypesFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title">Add New Good Type</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewGoodTypesForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="goodTypeName" class="form-control" required>

                      <label>Type Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="goodTypeDesc" class="form-control" required>

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

        <div class="modal" id="addNewGoodGroupFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title">Add New Good Group</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewGoodGroupForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="goodGroupName" class="form-control" required>

                      <label>Group Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="goodGroupDesc" class="form-control" required>

                      <label>Group Description</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group btn-col">

                      <button type="submit" id="addNewGoodGroupFormSubmitBtn" class="btn btn-primary btnstyle">Submit</button>

                    </div>

                  </div>

                </form>

              </div>

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

                      <option value="">Goods Group</option>

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



        <!-- purchase group -->



        <!-- modal -->

        <div class="modal" id="addNewPurchaseGroupFormModal">

          <div class="modal-dialog">

            <div class="modal-content">

              <div class="modal-header py-1" style="background-color: #003060; color:white;">

                <h4 class="modal-title">Add New Purchase Group</h4>

                <button type="button" class="close" data-dismiss="modal">&times;</button>

              </div>

              <div class="modal-body">

                <form action="" method="post" id="addNewPurchaseGroupForm">

                  <div class="col-md-12 mb-3">

                    <div class="input-group">

                      <input type="text" name="purchaseGroupName" class="form-control" required>

                      <label>Purchase Group Name</label>

                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="input-group">

                      <input type="text" name="purchaseGroupDesc" class="form-control" required>

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

  <div class="content-wrapper is-location-master is-location-master-view">

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

} else if (isset($_GET["bom"]) && base64_decode($_GET["bom"]) > 0) {

  require_once("components/goods/create-bom.php");
} else {

?>



  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper is-location-master is-location-master-bom">

    <!-- Content Header (Page header) -->



    <!-- Main content -->

    <section class="content">

      <div class="container-fluid">





        <!-- row -->

        <div class="row p-0 m-0">

          <div class="col-12 mt-2 p-0">

            <div class="p-0 pt-1 my-2">
              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">
                <li class="py-3 d-flex justify-content-between align-items-center" style="width:100%">
                  <h3 class="card-title">Manage Location Items</h3>
                  <button type="button" class="btn btn-sm btn-primary float-add-btn" data-toggle="modal" data-target="#goodsmodal"><i class="fa fa-plus"></i></button>
                </li>
              </ul>
            </div>

            <!-- <ol class="breadcrumb bg-transparent">

              <li class="breadcrumb-item"><a href="<?= BRANCH_URL ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>

              <li class="breadcrumb-item active"><a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="text-dark"><i class="fa fa-list po-list-icon"></i>Manage Items</a></li>

             

            </ol> -->

            <!-- <div class="p-0 pt-1 my-2">

              <ul class="nav nav-tabs" style="border: none;" id="custom-tabs-two-tab" role="tablist">

                <li class="pt-2 px-3 d-flex justify-content-between align-items-center" style="width:100%">

                  <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>?create" class="btn btn-sm btn-primary float-add-btn"><i class="fa fa-plus"></i></a>

                </li>

              </ul>

            </div> -->




            <div class="card card-tabs" style="border-radius: 20px;">

              <form name="search" id="search" action="<?php $_SERVER['PHP_SELF']; ?>" method="get" onsubmit="return srch_frm();">

                <div class="card-body">

                  <div class="row filter-serach-row">

                    <div class="col-lg-1 col-md-1 col-sm-12">

                      <a type="button" class="btn add-col" data-toggle="modal" data-target="#myModal2" style="position:absolute;"> <i class="fa fa-cog po-list-icon" aria-hidden="true"></i></a>

                    </div>

                    <div class="col-lg-11 col-md-11 col-sm-12">

                      <div class="row table-header-item">

                        <div class="col-lg-11 col-md-11 col-sm-11">

                          <div class="filter-search">

                            <div class="section serach-input-section">

                              <input type="text" id="myInput" placeholder="" name="keyword" class="field form-control" value="<?php echo $keywd; ?>" />

                              <div class="icons-container">

                                <div class="icon-search">

                                  <i style="cursor: pointer" class="fa fa-bars po-list-icon" data-toggle="modal" data-target="#btnSearchCollpase_modal"></i>

                                </div>

                                <div class="icon-close">

                                  <i class="fa fa-search po-list-icon" id="myBtn"></i>
                                  <!-- <script>
                                  var input = document.getElementById("myInput");

                                  input.addEventListener("keypress", function(event) {

                                    if (event.key === "Enter") {

                                      event.preventDefault();

                                      document.getElementById("myBtn").click();

                                    }

                                  });
                                </script> -->

                                </div>

                              </div>

                            </div>

                          </div>

                        </div>

                        <div class="col-lg-1 col-md-1 col-sm-1">

                          <button type="button" class="btn btn-sm btn-primary relative-add-btn" data-toggle="modal" data-target="#goodsmodal"><i class="fa fa-plus"></i></button>

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

              <div class="modal fade item-add-modal hsn-dropdown-modal" id="goodsmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-hidden="true">
                <div class="modal-dialog modal-full-height modal-notify modal-success" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      Item Add
                    </div>
                    <div class="modal-body">
                      <div class="card">
                        <div class="row hsn-add">
                          <div class="col-md-6 col">
                            <a href="goods.php"><button type="button" class="btn btn-primary">Add from Existing Items</button></a>
                          </div>
                          <div class="col-md-6 col">
                            <a href="goods.php?create"><button type="button" class="btn btn-primary">Add new items</button></a>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="col-lg-12 col-md-12 col-sm-12 pr-0">
                <div class="tab-content pt-0" id="custom-tabs-two-tabContent">

                  <div class="tab-pane fade show active" id="listTabPan" role="tabpanel" aria-labelledby="listTab">

                    <?php

                    $cond = '';



                    $sts = " AND stock.`status` !='deleted'";

                    if (isset($_REQUEST['status_s']) && $_REQUEST['status_s'] != '') {

                      $sts = ' AND stock.`status`="' . $_REQUEST['status_s'] . '"';
                    }



                    if (isset($_REQUEST['form_date_s']) && $_REQUEST['form_date_s'] != '') {

                      $cond .= " AND stock.createdAt between '" . $_REQUEST['form_date_s'] . " 00:00:00' AND '" . $_REQUEST['to_date_s'] . " 23:59:59'";
                    }

                    if (isset($_REQUEST['keyword2']) && $_REQUEST['keyword2'] != '') {
                      $cond .= " AND (goods.`itemCode` like '%" . $_REQUEST['keyword2'] . "%' OR goods.`itemName` like '%" . $_REQUEST['keyword2'] . "%' OR goods.`netWeight` like '%" . $_REQUEST['keyword2'] . "%')";
                    } else {

                      if (isset($_REQUEST['keyword']) && $_REQUEST['keyword'] != '') {

                        $cond .= " AND (goods.`itemCode` like '%" . $_REQUEST['keyword'] . "%' OR goods.`itemName` like '%" . $_REQUEST['keyword'] . "%' OR goods.`netWeight` like '%" . $_REQUEST['keyword'] . "%')";
                      }
                    }



                    $sql_list = "SELECT * FROM `erp_inventory_stocks_summary` as stock LEFT JOIN `erp_inventory_items` as goods ON stock.itemId=goods.itemId WHERE 1 " . $cond . " AND stock.location_id=$location_id AND goods.itemId != '' ORDER BY stock.stockSummaryId desc  limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";

                    $qry_list = mysqli_query($dbCon, $sql_list);

                    $num_list = mysqli_num_rows($qry_list);





                    $countShow = "SELECT count(*) FROM `erp_inventory_stocks_summary` as stock RIGHT JOIN `erp_inventory_items` as goods ON stock.itemId=goods.itemId WHERE 1 " . $cond . " AND stock.`location_id`=$location_id  ";

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

                              <th>Moving Weighted Price</th>

                            <?php  }

                            if (in_array(6, $settingsCheckbox)) { ?>

                              <th>Valuation Class</th>

                            <?php

                            }

                            if (in_array(7, $settingsCheckbox)) { ?>

                              <th> Target Price </th>

                            <?php

                            }

                            if (in_array(8, $settingsCheckbox)) { ?>

                              <th> Type </th>

                            <?php

                            }

                            ?>
                            <th>BOM Status</th>

                            <th>Status</th>

                            <th>Action</th>

                          </tr>

                        </thead>

                        <tbody>

                          <?php

                          $customerModalHtml = "";

                          while ($row = mysqli_fetch_assoc($qry_list)) {
                            $rand = rand(10, 999);

                            //console($row);
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

                            $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
                            $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';

                            $gldetails=getChartOfAccountsDataDetails($row['parentGlId'])['data'];
                            $glName=$gldetails['gl_label'];
                            $glCode = $gldetails['gl_code'];

                          ?>

                            <tr>

                              <!-- <td><?= $cnt++ ?></td> -->

                              <?php if (in_array(1, $settingsCheckbox)) { ?>

                                <td><?= $row['itemCode'] ?></td>

                              <?php }

                              if (in_array(2, $settingsCheckbox)) { ?>

                                <td>
                                  <p class="pre-wrap"><?= $row['itemName'] ?></p>
                                </td>

                              <?php }

                              if (in_array(3, $settingsCheckbox)) { ?>

                                <td><?= $buom ?></td>

                              <?php }

                              if (in_array(4, $settingsCheckbox)) { ?>

                                <td>
                                  <p class="pre-normal"><?= $group_name ?></p>
                                </td>

                              <?php }

                              if (in_array(5, $settingsCheckbox)) { ?>

                                <td class="text-right"><?= $row['movingWeightedPrice'] ?></td>

                              <?php }

                              if (in_array(6, $settingsCheckbox)) { ?>

                                <td><?= $row['priceType'] ?></td>



                              <?php }

                              if (in_array(7, $settingsCheckbox)) { ?>

                                <td class="text-right"><?= round($row['itemPrice'], 2) ?></td>



                              <?php }
                              if (in_array(8, $settingsCheckbox)) { ?>

                                <td><?= $type_name ?></td>



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



                                <a style="cursor:pointer" data-toggle="modal" data-target="#fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" class="btn btn-sm">

                                  <i class="fa fa-eye po-list-icon"></i>

                                </a>

                              </td>

                            </tr>

                            <!-- right modal start here  -->

                            <div class="modal fade right location-item-modal goods-modal customer-modal" id="fluidModalRightSuccessDemo_<?= $row['itemId'] ?>" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">

                              <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">

                                <!--Content-->

                                <div class="modal-content">

                                  <!--Header-->

                                  <div class="modal-header pt-4">

                                    <div class="row item-specification-row">

                                      <div class="col-lg-4 col-md-4 col-sm-4">



                                        <?php
                                        if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {

                                          //console($row);

                                        ?>
                                          <div class="service-img">
                                            <img src="../../public/assets/img/icons/goods-service.png" class="service-icon" title="goods-iem-image" alt="goods_item_image">
                                          </div>
                                        <?php

                                        } else {

                                        ?>
                                          <div class="item-img">
                                            <img src="../../public/assets/img/image/goods-item-image.png" title="goods-iem-image" alt="goods_item_image">
                                          </div>
                                        <?php
                                        }

                                        ?>

                                        <!-- <div class="price-sale mt-3 mb-4">

                                            <div class="question-section mb-2">

                                              <p class="text-sm">Which price would you like to happen sales on ?</p>

                                            </div>

                                            <div class="answer-section mb-2 mt-3">

                                              <input type="hidden" id="itemIid" class="itemId itemId_<?= $rand ?>" value="<?= $row['stockSummaryId'] ?>">

                                              <div class="answer1">

                                                <div class="form-input">


                                                  <input type="radio" name="price_switch" class="tmspclass" id="price_switch" data-attr="<?= $row['stockSummaryId'] ?>" <?php if ($row['priceSetOn'] == "TARGET") {
                                                                                                                                                                          echo "checked";
                                                                                                                                                                        } ?> value="TARGET">

                                                  <label class="mb-0">Target Price</label>


                                                </div>

                                              </div>

                                              <div class="answer2">

                                                <div class="form-input">


                                                  <input type="radio" name="price_switch" class="tmspclass" id="price_switch" data-attr="<?= $row['stockSummaryId'] ?>" <?php if ($row['priceSetOn'] == "MSP") {
                                                                                                                                                                          echo "checked";
                                                                                                                                                                        } ?> value="MSP">

                                                  <label class="mb-0">MSP</label>


                                                </div>

                                              </div>

                                            </div>


                                          </div> -->







                                      </div>


                                      <div class="col-lg-8 col-md-8 col-sm-8">

                                        <div class="head-title">

                                          <p class="heading lead text-lg text-elipse" title='Item Name : <?= $itemName ?>'>Item Name : <?= $itemName ?></p>

                                          <p class="item-code">Item Code : <?= $itemCode ?></p>

                                          <p class="item-desc text-elipse" title='Description : <?= $row['itemDesc'] ?>'>Description : <?= $row['itemDesc'] ?></p>

                                          <p class="item-type">Item Type : <?= $type_name ?></p>


                                        </div>


                                      </div>

                                    </div>


                                    <!-- <div class="price-sale mt-3 mb-4">

                                      <div class="question-section mb-2">

                                        <p class="text-sm text-right">Which price would you like to happen sales on ?</p>

                                      </div>

                                      <div class="answer-section mb-2 mt-3">

                                        <input type="hidden" id="itemIid" class="itemId itemId_<?= $rand ?>" value="<?= $row['stockSummaryId'] ?>">

                                        <div class="answer1">

                                          <div class="form-input">


                                            <input type="radio" name="price_switch" class="tmspclass" id="price_switch" data-attr="<?= $row['stockSummaryId'] ?>" <?php if ($row['priceSetOn'] == "TARGET") {
                                                                                                                                                                    echo "checked";
                                                                                                                                                                  } ?> value="TARGET">

                                            <label class="mb-0">Target Price</label>


                                          </div>

                                        </div>

                                        <div class="answer2">

                                          <div class="form-input">


                                            <input type="radio" name="price_switch" class="tmspclass" id="price_switch" data-attr="<?= $row['stockSummaryId'] ?>" <?php if ($row['priceSetOn'] == "MSP") {
                                                                                                                                                                    echo "checked";
                                                                                                                                                                  } ?> value="MSP">

                                            <label class="mb-0">MSP</label>


                                          </div>

                                        </div>

                                      </div>


                                    </div> -->

                                    <div class="display-flex-space-between mt-4 mb-3 location-master-action">


                                      <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item">

                                          <a class="nav-link active" id="home-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" href="#home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="home" aria-selected="true">Info</a>

                                        </li>
                                        <li class="nav-item">

                                          <a class="nav-link" id="classic-view-tab" data-toggle="tab" href="#classic-view<?= $row['itemCode'] ?>" role="tab" aria-controls="classic-view" aria-selected="false"><ion-icon name="apps-outline" class="mr-2"></ion-icon> Classic View</a>

                                        </li>
                                        <!-- -------------------Audit History Button Start------------------------- -->
                                        <li class="nav-item">

                                          <a class="nav-link auditTrail" id="history-tab<?= str_replace('/', '-', $row['itemCode']) ?>" data-toggle="tab" data-ccode="<?= str_replace('/', '-', $row['itemCode']) ?>" href="#history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tab" aria-controls="history<?= str_replace('/', '-', $row['itemCode']) ?>" aria-selected="false"><i class="fa fa-history mr-2"></i> Trail</a>

                                        </li>
                                        <!-- -------------------Audit History Button End------------------------- -->
                                      </ul>


                                      <div class="action-btns display-flex-gap goods-flex-btn" id="action-navbar">

                                        <div class="action-input">

                                          <?php $itemId = base64_encode($row['itemId']) ?>

                                          <form action="" method="POST">

                                            <a href="goods.php?edit=<?= $itemId ?>" name="customerEditBtn">

                                              <i title="Edit" class="fa fa-edit po-list-icon-invert"></i>

                                            </a>

                                            <a href="">

                                              <i title="Delete" class="fa fa-trash po-list-icon-invert"></i>

                                            </a>

                                          </form>

                                        </div>

                                      </div>

                                    </div>

                                  </div>
                                  <!--Body-->
                                  <div class="modal-body">
                                    <div class="tab-content" id="myTabContent">
                                      <div class="tab-pane fade show active" id="home<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="home-tab">
                                        <div class="row">
                                          <div class="col-lg-12 col-md-12 col-sm-12">
                                            <?php if ($row['bomStatus'] != 0) { ?>
                                              <a href="<?= LOCATION_URL ?>bom/bom.php?view=<?= $itemId; ?>" class="btn btn-primary float-right m-3" name="customerEditBtn">
                                                <i title="BOM" class="fa fa-cogs"></i>BOM
                                              </a>
                                            <?php } ?>
                                          </div>
                                        </div>
                                        <div class="row px-3 detail-view-accordion">

                                          <div class="col-lg-12 col-md-12 col-sm-12">

                                            <?php
                                            if ($row['goodsType'] == 5 || $row['goodsType'] == 7 || $row['goodsType'] == 10) {

                                              //console($row);

                                            ?>

                                              <!-- service tab -->
                                              <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                      Service Details
                                                    </button>
                                                  </h2>
                                                  <div id="classifications" class="accordion-collapse collapse classifications-acc show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body p-0">
                                                      <div class="card">
                                                        <div class="card-body accordion-card-details p-0">
                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs"> Name</p>
                                                            <p class="font-bold text-xs">: <?= $row['itemName'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs"> Description</p>
                                                            <p class="font-bold text-xs">: <?= $row['itemDesc'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">HSN</p>
                                                            <p class="font-bold text-xs">: <?= $row['hsnCode'] ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">GL Details :</p>
                                                            <p class="font-bold text-xs"><?= $glName ?> [<?= $glCode ?>]</p>
                                                          </div>
                                                          <?php
                                                          $tds_id = $row['tds'];
                                                          $tds_sql = queryGet("SELECT * FROM `erp_tds_details` WHERE `id`= $tds_id");
                                                          $tds_data = $tds_sql['data'];

                                                          ?>
                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">TDS </p>
                                                            <p class="font-bold text-xs">: <?php if ($tds_sql['numRows'] > 0) {
                                                                                              echo $tds_data['section'] . '(' . $tds_data['TDSRate'] . ')';
                                                                                            } else {
                                                                                              echo '-';
                                                                                            } ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Service Unit</p>
                                                            <p class="font-bold text-xs">: <?= $service_unit_sql['data']['uomName']    ?></p>
                                                          </div>
                                                          <div class="display-flex-space-between">
                                                            <p class="text-xs">Service Target Price</p>
                                                            <p class="font-bold text-xs">: <?= round($summary_sql['data']['itemPrice'], 2)  ?></p>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                            <?php
                                            } else {
                                            ?>

                                              <!-------Classification------>
                                              <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#classifications" aria-expanded="true" aria-controls="flush-collapseOne">
                                                      Classification
                                                    </button>
                                                  </h2>
                                                  <div id="classifications" class="accordion-collapse classification-accordion collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card">

                                                        <div class="card-body p-3">
                                                        <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">GL Details :</p>
                                                            <p class="font-bold text-xs"><?= $glName ?> [<?= $glCode ?>]</p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Goods Type :</p>
                                                            <p class="font-bold text-xs"><?= $type_name ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs"> Group :</p>
                                                            <p class="font-bold text-xs group-desc" title="Group : <?= $group_name ?>"><?= $group_name ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Availablity Check :</p>
                                                            <p class="font-bold text-xs"><?= $row['availabilityCheck'] ?></p>
                                                          </div>

                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                              <!-------Basic Details------>
                                              <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#basicDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                      Basic Details
                                                    </button>
                                                  </h2>
                                                  <div id="basicDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card">

                                                        <div class="card-body p-3">

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Net Weight :</p>
                                                            <p class="font-bold text-xs"><?= $row['netWeight'] . "  " . $row['weight_unit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Gross Weight :</p>
                                                            <p class="font-bold text-xs"><?= $row['grossWeight'] . "  " . $row['weight_unit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Volume :</p>
                                                            <p class="font-bold text-xs"><?= $row['volume'] ?> m<sup>3</sup></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Height :</p>
                                                            <p class="font-bold text-xs"><?= $row['height'] . " " . $row['measuring_unit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Width :</p>
                                                            <p class="font-bold text-xs"><?= $row['width'] . "  " . $row['measuring_unit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Length :</p>
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
                                              <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                  <h2 class="accordion-header" id="flush-headingOne">
                                                    <button class="accordion-button btn btn-primary collapsed mt-3 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#storageDetails" aria-expanded="false" aria-controls="flush-collapseOne">
                                                      Storage Details
                                                    </button>
                                                  </h2>
                                                  <div id="storageDetails" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body p-0">

                                                      <div class="card">

                                                        <div class="card-body p-3">

                                                          <!-- <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">Storage Bin :</p>
                                                          <p class="font-bold text-xs"><?= $row['storageBin'] ?></p>
                                                        </div>

                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">Picking Area :</p>
                                                          <p class="font-bold text-xs"><?= $row['pickingArea'] ?></p>
                                                        </div>

                                                        <div class="display-flex-space-between">
                                                          <p class="font-bold text-xs">Temp Control :</p>
                                                          <p class="font-bold text-xs"><?= $row['tempControl'] ?></p>
                                                        </div> -->

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Storage Control :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['storageControl'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Max Storage Period :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriod'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Max Storage Period Time :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['maxStoragePeriodTimeUnit'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Min Remain Self Life Time Unit :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLife'] ?></p>
                                                          </div>

                                                          <div class="display-flex-space-between">
                                                            <p class="font-bold text-xs">Min Remain Self Life :</p>
                                                            <p class="font-bold text-xs"><?= $storage_data['minRemainSelfLifeTimeUnit'] ?></p>
                                                          </div>

                                                        </div>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>

                                              <!-------Purchase Details------>
                                              <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
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
                                                            <p class="font-bold text-xs">Purchasing Value Key :</p>
                                                            <p class="font-bold text-xs"><?= $row['purchasingValueKey'] ?></p>
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

                                          </div>








                                        </div>

                                      </div>

                                      <!-- -------------------Audit History Tab Body Start------------------------- -->
                                      <div class="tab-pane fade" id="history<?= str_replace('/', '-', $row['itemCode']) ?>" role="tabpanel" aria-labelledby="history-tab">

                                        <div class="audit-head-section mb-3 mt-3 ">
                                          <p class="text-xs font-italic"><span class="font-bold text-normal">Created by </span> <?= getCreatedByUser($row['createdBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['createdAt']) ?></p>
                                          <p class="text-xs font-italic"> <span class="font-bold text-normal">Last Updated by</span> <?= getCreatedByUser($row['updatedBy']) ?> <span class="font-bold text-normal"> on </span> <?= formatDateORDateTime($row['updatedAt']) ?></p>
                                        </div>
                                        <hr>
                                        <div class="audit-body-section mt-2 mb-3 auditTrailBodyContent<?= str_replace('/', '-', $row['itemCode']) ?>">

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
              </div>

            </div>

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

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(4, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="4" />

                            Group</td>

                        </tr>

                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(5, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="5" />

                            Moving Weighted Price</td>

                        </tr>



                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(6, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox3" value="6" />

                            Valuation Class</td>

                        </tr>


                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(7, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox7" value="7" />

                            Target Price</td>

                        </tr>

                        <tr>

                          <td valign="top" style="width: 165px"><input type="checkbox" <?php echo (in_array(8, $settingsCheckbox) ? 'checked="checked"' : ''); ?> name="settingsCheckbox[]" id="settingsCheckbox8" value="8" />

                            Type</td>

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

    //     //$(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodTypesFormModal">Add New</a></div>`);

    //   });



    $("#goodTypeDropDown").change(function() {

      let dataAttrVal = $("#goodTypeDropDown").find(':selected').data('goodtype');

      if (dataAttrVal == "RM") {

        $("#bomCheckBoxDiv").html("");
        $("#bomRadioDiv").html("");
        $("#pricing").html("");
        $("#purchase").html(`<div class="row" id="purchase">

<div class="col-lg-12 col-md-12 col-sm-12">

  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

    <div class="card-header">

      <h4>Purchase Details</h4>

    </div>

    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

      <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12">

          <div class="row goods-info-form-view customer-info-form-view">

            <div class="col-lg-12 col-md-12 col-sm-12">

              <div class="form-input">

                <label for="">Purchasing Value Key</label>

                <input type="text" name="purchasingValueKey" class="form-control purchasing_value" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">

              </div>

            </div>



          </div>

        </div>

      </div>

    </div>

  </div>

</div>

</div>`);

      } else if (dataAttrVal == "SFG") {

        $("#bomRadioDiv").html("");

        $("#bomCheckBoxDiv").html(`<input type="checkbox" name="bomRequired" style="width: auto; margin-bottom: 0;" checked><label class="mb-0">Required BOM</label>`);
        $("#pricing").html(`<div class="row" id="pricing">

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

                <label for="">Max Discount</label>

                <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>

</div>`);

        $("#purchase").html(`<div class="row" id="purchase">

<div class="col-lg-12 col-md-12 col-sm-12">

  <div class="card goods-creation-card so-creation-card po-creation-card" style="height: auto;">

    <div class="card-header">

      <h4>Purchase Details</h4>

    </div>

    <div class="card-body goods-card-body others-info vendor-info so-card-body" style="height: auto;">

      <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12">

          <div class="row goods-info-form-view customer-info-form-view">

            <div class="col-lg-12 col-md-12 col-sm-12">

              <div class="form-input">

                <label for="">Purchasing Value Key</label>

                <input type="text" name="purchasingValueKey" class="form-control purchasing_value" id="exampleInputBorderWidth2" placeholder="Purchasing Value Key">

              </div>

            </div>



          </div>

        </div>

      </div>

    </div>

  </div>

</div>

</div>`);


      } else if (dataAttrVal == "FG") {

        $("#bomCheckBoxDiv").html(``);
        $("#purchase").html("");
        $("#bomRadioDiv").html(`
        <div class="form-inline float-right" id="bomRadioDiv">

<div class="goods-input for-manufac d-flex">

  <input type="radio" name="bomRequired_radio" value="1">

  <label for="" class="mb-0 ml-2">For Manufacturing</label>

</div>

<div class="goods-input for-trading d-flex">

  <input type="radio" name="bomRequired_radio" value="0">

  <label for="" class="mb-0 ml-2">For Trading</label>

</div>

</div>`);

        $("#pricing").html(`<div class="row" id="pricing">

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

                <label for="">Max Discount</label>

                <input step="0.01" type="number" name="discount" class="form-control discount" id="exampleInputBorderWidth2" placeholder="Maximum Discount">

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>

</div>`);

      } else {

        $("#bomCheckBoxDiv").html(``);
        $("#purchase").html("");
        $("#bomRadioDiv").html("");

        $("#pricing").html("");
      }

    });



    //**************************************************************

    $('#goodGroupDropDown')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="btn-row"><a type="button" class="btn btn-primary add-btn" data-toggle="modal" data-target="#addNewGoodGroupFormModal">Add New</a></div>`);

      });



    $('#hsnDropDown')

      .select2()

      .on('select2:open', () => {

        $(".select2-results:not(:has(a))").append(`<div class="col-md-12 mb-12"></div>`);

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













    function loadhsn() {

      $.ajax({

        type: "GET",

        url: `ajaxs/items/ajax-hsn.php`,

        beforeSend: function() {

          $("#hsnDropDown").html(`<option value="">Loding...</option>`);

        },

        success: function(response) {

          $("#hsnDropDown").html(response);

          <?php

          if (isset($row["hsnCode"])) {

          ?>

            $(`#hsnDropDown option[value=<?= $row["hsnCode"] ?>]`).attr('selected', 'selected');

          <?php

          }

          ?>

        }

      });

    }

    loadhsn();

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



    console.log("clickinggggggggg");
    let radioBtnVal = $('input[name="hsn"]:checked').val();
    let hsncode = ($(`#hsnCode_${radioBtnVal}`).html());
    // let hsndesc = ($(`#hsnDescription_${radioBtnVal}`).html()).trim();
    // let hsnpercentage = ($(`#taxPercentage_${radioBtnVal}`).html()).trim();
    //console.log(hsncode);
    $("#hsnlabel").html(hsncode);
  });




  function search_hsn() {
    let input = document.getElementById('searchbar').value
    input = input.toLowerCase();
    let x = document.getElementsByClassName('hsn-code');

    for (i = 0; i < x.length; i++) {
      if (!x[i].innerHTML.toLowerCase().includes(input)) {
        x[i].style.display = "none";
      } else {
        x[i].style.display = "block";
      }
    }
  }
  $('#searchbar').keyup(function() {
    var search_term = $('#searchbar').val();
    console.log(search_term)
    $('.hsn-code').removeHighlight().highlight(search_term);
  });


  $(document).on("click", ".tmspclass", function() {
    // alert(1);
    let checkSelected = $(this).val(); //MSP,TARGET
    //alert(checkSelected);
    itemId = $(this).data("attr");
    //alert(itemId);

    $.ajax({

      type: "POST",

      url: `ajaxs/items/ajax-price.php`,

      data: {
        "price": checkSelected,
        "id": itemId,
      },
      beforeSend: function() {



      },

      success: function(response) {

        console.log(response);

      }

    });



  });
</script>







<script src="<?= BASE_URL; ?>public/validations/goodsValidation.js"></script>

<script src="https://johannburkard.de/resources/Johann/jquery.highlight-4.js"></script>