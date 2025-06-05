<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
//require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/branch/func-branch-pr-controller.php");
require_once("../../app/v1/functions/branch/func-vendor-from-matrix.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
if (isset($_POST["createdata"])) {
  $addNewObj = createDataVendor($_POST);
  swalToast($addNewObj["status"], $addNewObj["message"]);
}
?>







<link rel="stylesheet" href="../../public/assets/manage-rfq.css">







<link rel="stylesheet" href="../../public/assets/sales-order.css">







<link rel="stylesheet" href="../../public/assets/listing.css">







<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">







<link rel="stylesheet" href="../../public/assets/animate.css">







<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>



<script src="https://cdn.amcharts.com/lib/4/core.js"></script>



<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>



<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>



<script src="https://cdn.amcharts.com/lib/4/plugins/forceDirected.js"></script>





<style>
  .content-wrapper {

    background: #fff;

  }


  .striped-bg-alter {


    background: #9c9c9c1c;


  }


  .matrix-card {

    border: 0;







    box-shadow: rgb(0 0 0 / 24%) 0px 3px 8px;







    border-radius: 12px;







    display: block;







  }

  img.check-img {
    max-width: 68%;
    box-shadow: 1px 1px 4px 0px #0000008f;
  }

  .card.visiting-card-upload .drag-file-area {
    width: 100%;
  }





  .card.matrix-card .card-body {







    margin: 0px 0 13px 0;







    padding: 0;







  }







  .card.matrix-card .card-body .row {







    padding: 8px 10px !important;







    margin: 0px 0px;







  }







  .card.matrix-card .card-body .row:hover {







    background: #d2d1d1;







  }







  .card.matrix-card .card-body .row:nth-child(1):hover {







    background: #fff;







    border-radius: 12px 12px 0 0;







  }







  .accordion-other-cost .tab-label {







    display: flex;







    justify-content: space-between;







    padding: 1em;







    background: #003060;







    font-weight: bold;







    cursor: pointer;







    color: #fff;







  }







  .row.accordion-other-cost label {







    position: relative;







    z-index: 999;







    align-items: center;







  }







  .row.accordion-other-cost label p {







    font-size: 11px;







    font-weight: 500;







    margin-bottom: 0;







    margin-left: 3em;







  }


  .matrix-accordion .create-po-btn button {

    border: 1px solid #fff !important;

    border-radius: 12px !important;

  }





  body.sidebar-mini.layout-fixed.sidebar-collapse .create-po-btn {

    position: relative;

    top: -11px;

    right: 75px;

    z-index: 99;

    border-radius: 12px;

    text-align: center;

    width: 100%;

    max-width: 100px;

    margin-left: auto;

    margin-top: -37px;

  }

  .btn-create-po15 button {

    width: 100%;

  }

  /*body.sidebar-mini.layout-fixed.sidebar-collapse .btn-create-po16 {*/

  /*  position: relative;*/
  /*  right: -62em;*/
  /*  top: 0;*/
  /*  z-index: 99;*/
  /*  border: 1px solid #fff;*/
  /*  border-radius: 12px;*/
  /*  text-align: center;*/
  /*  width: 100px;*/
  /*  margin-top: -62px;*/

  /*}*/

  .create-po-btn button {

    width: 100%;

  }



  body.sidebar-mini.layout-fixed .create-po-btn {

    position: relative;

    top: -11px;

    right: 75px;

    z-index: 99;

    border-radius: 12px;

    text-align: center;

    width: 100%;

    max-width: 100px;

    margin-left: auto;

    margin-top: -37px;


  }


  /*body.sidebar-mini.layout-fixed .btn-create-po16 {*/

  /*    position: relative;*/
  /*  right: -52em;*/
  /*  top: 0;*/
  /*  z-index: 99;*/
  /*  border: 1px solid #fff;*/
  /*  border-radius: 12px;*/
  /*  text-align: center;*/
  /*  width: 100px;*/
  /*  margin-top: -62px;*/

  /*}*/







  .btn-create-po button {







    border: 1px solid #fff !important;







  }







  .row.accordion-other-cost .tab:nth-child(1) input[type="checkbox"] {







    position: absolute;







    top: 19px;







    left: 28px;







    z-index: 999;







  }







  .row.accordion-other-cost .tab:nth-child(2) input[type="checkbox"] {







    position: relative;







    top: -40px;







    left: 17px;







    z-index: 999;







  }







  /* input.form-control.input-matrix {







    display: none;







  } */







  .card.list-view-div .card-body .row .col {







    height: 32px;







  }



  .matrix-modal {
    backdrop-filter: blur(3px);
  }






  .card.list-view-div .card-body .row:nth-child(1) {







    margin-bottom: 0;







  }







  div#chartDivPieChartAsBullet {







    height: 500px;







    width: 100%;







  }


  .matrix-hr {

    border-top-color: #fff !important;

    opacity: 1;

    margin: 0px 11px !important;

  }


  .matrix-card .row:nth-child(1):hover {

    pointer-events: none;

  }

  .matrix-card .row:hover {

    border-radius: 0 0 10px 10px;

  }

  .matrix-card .row:nth-child(1) {

    background: #fff;

  }

  .matrix-card .row .col {

    display: flex;

    align-items: center;

  }

  .matrix-accordion {

    background: transparent;

  }

  .matrix-accordion .accordion-body {

    box-shadow: 1px 0px 13px 1px #0000002e;

  }

  .matrix-accordion button {

    color: #fff;

    border-radius: 10px 10px 0 0 !important;

    margin: 20px 0;

  }

  .accordion-button:not(.collapsed) {

    color: #fff;

  }

  .accordion-button.collapsed::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

  }

  .accordion-button:not(.collapsed)::after {

    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");

  }

  .accordion-item {

    border-radius: 15px !important;

    margin-bottom: 2em;

  }

  .matrix-accordion table thead th {

    background: #cedae4;

    color: #003060;

    padding: 5px 15px;

    font-weight: 600;

  }

  .vendor-name-accordion-body {

    overflow-x: auto;

  }

  .accordion-button {

    z-index: 9;

  }

  .no-response {

    height: auto;

    display: flex;

    align-items: center;

    justify-content: center;

  }

  .no-response img {

    max-width: 350px;

  }

  .no-response-text {

    color: #4f5d73;

    text-align: center;


  }


  @media (max-width: 575px) {

    div#chartDivPieChartAsBullet {

      font-size: 13px;

    }

    .head.select-box-head {

      position: absolute;

      top: 100px;

      left: 0;

      padding: 0 14px;

    }

    .head.select-box-head button {

      position: absolute;

      right: 0;

      top: 63px;

      margin-right: 1em;

    }

    .card-body.canvas-info {

      margin-top: 8em;

    }

    .form-inline.display-flex label {

      color: #000 !important;

    }

    .form-inline.display-flex select {

      width: 100% !important;

    }

    .matrix-accordion .create-po-btn {

      position: relative !important;

      right: 1px !important;

      top: -6px !important;

      margin-top: 15px !important;


    }

  }

  .multisteps-form__form {
    height: 50vh !important;
  }

  .matrix-modal .modal-dialog {
    max-width: 700px;
  }
</style>



















<div class="content-wrapper">







  <section class="content">







    <div class="container-fluid">


      <!-- <form method="POST"  > -->


      <?php

      $rfq_id = $_GET['rfq'];
      $vendor_query = "SELECT * FROM erp_vendor_response WHERE rfqId = '$rfq_id'";
      $dataset = queryGet($vendor_query, true);

      $closing_date_query = "SELECT * FROM erp_rfq_list WHERE rfqId = '$rfq_id'";
      $closing_date_data = queryGet($closing_date_query, false);
      $closing_date = $closing_date_data["data"]["closing_date"];


      $required_date_query = "SELECT * FROM erp_rfq_list LEFT JOIN erp_branch_purchase_request ON erp_rfq_list.prId = erp_branch_purchase_request.purchaseRequestId WHERE erp_rfq_list.rfqId = '$rfq_id'";
      $required_date_data = queryGet($required_date_query, false);
      $required_date = $required_date_data["data"]["expectedDate"];

      $closing_date_plus_x = date('Y-m-d', strtotime($closing_date . ' +1 day'));
      $date1 = new DateTime($closing_date_plus_x);
      $date2 = new DateTime($required_date);
      $interval = $date2->diff($date1);


      $expected_lead_time = ($interval->days);

      // print_r($expected_lead_time);



      if ($dataset['numRows'] > 0) {
        foreach ($dataset['data'] as $row) {
          $id = $row['erp_v_id'];
          $item_list = "SELECT * FROM erp_vendor_item WHERE `erp_v_id` = '$id'";
          $items = queryGet($item_list, true);
          $moq_total = 0;
          $rate = 0;
          $lead_time = 0;
          foreach ($items['data'] as $item) {
            $highest_rating = 10;
            $lowest_rating = 9;
            $moq = $item['moq'];
            $rq = $item['rq'];
            $percent = ($moq - $rq) / $rq;
            if ($item['moq_diff_value'] == 2) {
              $percent = $percent > 1 ? 1 : $percent;
              $above_sent_percent = $percent > 1.1 ? 0 : 0.5;
              $moq_factor = ($lowest_rating - ($percent * $lowest_rating)) + $above_sent_percent;
            } else {
              $moq_factor = $highest_rating;
            }
            $moq_total += $moq_factor;
            $rate += $item['total'];
            $lead_time += $item['lead_time'];
          }

          $moq_array[] = $moq_total;

          $rate_array[] = $rate;

          $lead_time_array[] = $lead_time;

          $vendor_array[] = $row['vendor_name'];

          $vendor_id_array[] = $id;

          $vendor_code_array[] = $row['vendor_code'];
        }

        if (count($moq_array) > 0) {
          $min_moq_array = min($moq_array);
        } else {
          $min_moq_array = 1;
        }
        if (count($rate_array) > 0) {
          $min_rate_array = min($rate_array);
        } else {
          $min_rate_array = 1;
        }
        if (count($lead_time_array) > 0) {
          $min_lead_time_array = min($lead_time_array);
        } else {
          $min_lead_time_array = 1;
        }


        $i = 0;
        $j = 0;
        $k = 0;
        $l = 0;
        $v = 0;
        $vi = 0;
        $vc = 0;
        $vendor_array_1 = [];

        while ($i < count($moq_array) && $j < count($rate_array) && $k < count($lead_time_array) && $v < count($vendor_array) && $vi < count($vendor_id_array) && $vc < count($vendor_code_array)) {

          $moq_value = (10 * $moq_array[$i++]) / $min_moq_array;
          $rate_value = (10 * $min_rate_array) / $rate_array[$j++];
          if ($lead_time_array[$k] < $expected_lead_time) {
            $lead_time_value = 10;
          } else {
            $lead_time_value = (10 * $min_lead_time_array) / $lead_time_array[$k];
          }

          $rate_weight = 10;
          $rate_weight_array = array("value" => $rate_weight * $rate_value, "title" => "Rate Weight");
          $moq_weight = 2;
          $moq_weight_array = array("value" => $moq_weight * $moq_value, "title" => "MOQ Weight");
          $lead_time_weight = 5;
          $lead_time_weight_array = array("value" => $lead_time_weight * $lead_time_value, "title" => "LEAD TIME Weight");
          $pie = array($rate_weight_array, $moq_weight_array, $lead_time_weight_array);
          $sum[$l] = $moq_weight * $moq_value + $rate_weight * $rate_value + $lead_time_weight * $lead_time_value;
          $vendor_array_1[] = array("vendor_id" => $vendor_id_array[$vi++], "vendor_code" => $vendor_code_array[$vc++], "vendor" => $vendor_array[$v++], "units" => $sum[$l], "pie" => $pie);

          $l++;
          $k++;
        }

        usort($vendor_array_1, function ($item1, $item2) {
          return $item2['units'] <=> $item1['units'];
        });
      ?>






        <div class="accordion accordion-flush matrix-accordion p-0" id="accordionFlushExample">
          <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingOne">
              <button class="accordion-button btn btn-primary collapsed mb-0" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="true" aria-controls="flush-collapseOne">
                <p class="vendor-name mb-0"> Required Item list</p>
              </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body p-0">

                <table class="table defaultDataTable table-hover table-nowrap">
                  <thead>
                    <tr>
                      <th>Item Name</th>
                      <th>Item Code</th>
                      <th>Required Quantity</th>
                      <th>Remaining Quantity</th>
                      <th>UoM</th>
                      <th>Expected Date</th>
                      <th>Closing Date</th>
                    </tr>
                  </thead>

                  <!-- <div class="row">

                    <div class="col">Item Name</div>



                    <div class="col">Item Code</div>



                    <div class="col">Required Quantity</div>



                    <div class="col">Remaining Quantity</div>



                    <div class="col">UoM</div>



                    <div class="col">Expected Date</div>



                  </div>

                  <hr class="matrix-hr"> -->




                  <tbody>


                    <?php







                    $item_detail = "SELECT * FROM erp_rfq_list LEFT JOIN erp_branch_purchase_request ON erp_rfq_list.prId = erp_branch_purchase_request.purchaseRequestId WHERE erp_rfq_list.rfqId = '$rfq_id'";

                    // console($item_detail);





                    $item_datasets = queryGet($item_detail, false);







                    // print_r($item_datasets['data']);







                    $prid = $item_datasets['data']['purchaseRequestId'];















                    $all_item = "SELECT * FROM erp_rfq_items LEFT JOIN erp_purchase_register_item_delivery_schedule ON erp_rfq_items.deliverySceduleId = erp_purchase_register_item_delivery_schedule.pr_delivery_id LEFT JOIN erp_inventory_items ON erp_rfq_items.ItemId = erp_inventory_items.itemId LEFT JOIN erp_inventory_mstr_uom ON erp_inventory_items.baseUnitMeasure = erp_inventory_mstr_uom.uomId WHERE
                    erp_rfq_items.rfqId = '$rfq_id' AND erp_purchase_register_item_delivery_schedule.pr_id = '$prid'";

                    







                    $item_data = queryGet($all_item, true);

                    // console($item_data);













                    // print_r($item_data['data']);















                    foreach ($item_data['data'] as $key => $value) {







                      // print_r($value);







                    ?>







                      <tr>
                        <td>
                          <p class="pre-normal"><?= $value['itemName'] ?></p>
                        </td>
                        <td><?= $value['itemCode'] ?></td>
                        <td><?= decimalQuantityPreview($value['qty']) ?></td>
                        <td><?= decimalQuantityPreview($value['remaining_qty']) ?></td>
                        <td><?= $value['uomName'] ?></td>
                        <td><?= formatDateWeb($value['delivery_date']) ?></td>
                        <td><?= formatDateWeb($item_datasets['data']['closing_date']) ?></td>
                      </tr>




                      <!-- <div class="row">

                        <div class="col"><?= $value['itemName'] ?></div>

                        <div class="col"><?= $value['itemCode'] ?></div>

                        <div class="col"><?=decimalQuantityPreview($value['itemQuantity']) ?></div>

                        <div class="col"><?= decimalQuantityPreview($value['itemQuantity']) ?></div>

                        <div class="col"><?= $value['uomName'] ?></div>

                        <div class="col"><?= formatDateWeb($item_datasets['data']['expectedDate']) ?></div>

                      </div>


                      <hr class="matrix-hr"> -->

                    <?php



                      $date = $item_datasets['data']['expectedDate'];
                    }



                    ?>



                  </tbody>


                </table>


              </div>
            </div>
          </div>
        </div>




        <!-- <div class="row">

          <div class="col-lg-12 col-md-12 col-sm-12 col-12">

            <div class="row accordion-other-cost mb-5">

              <div class="col">

                <div class="tabs">

                  <div class="tab">

                    <input type="checkbox" id="chck1" style="display: none;">

                    <label class="tab-label" for="chck1">Required Item list</label>

                    <div class="tab-content">

                      <div class="card">

                        <div class="card-body">

                          <div class="row">

                            <div class="col">Item Name</div>



                            <div class="col">Item Code</div>



                            <div class="col">Required Quantity</div>



                            <div class="col">Remaining Quantity</div>



                            <div class="col">UoM</div>



                            <div class="col">Expected Date</div>



                          </div>







                          <?php







                          $item_detail = "SELECT * FROM erp_rfq_list LEFT JOIN erp_branch_purchase_request ON erp_rfq_list.prId = erp_branch_purchase_request.purchaseRequestId WHERE erp_rfq_list.rfqId = '$rfq_id'";







                          $item_datasets = queryGet($item_detail, false);







                          // print_r($item_datasets['data']);







                          $prid = $item_datasets['data']['purchaseRequestId'];















                          $all_item = "SELECT * FROM erp_rfq_items LEFT JOIN erp_branch_purchase_request_items ON erp_branch_purchase_request_items.itemId = erp_rfq_items.itemId LEFT JOIN erp_inventory_mstr_uom ON erp_inventory_mstr_uom.uomId = erp_branch_purchase_request_items.uom  LEFT JOIN erp_inventory_items ON erp_inventory_items.itemId = erp_branch_purchase_request_items.itemId WHERE erp_rfq_items.rfqId = '$rfq_id' AND erp_branch_purchase_request_items.prId = '$prid'";







                          $item_data = queryGet($all_item, true);















                          // print_r($item_data['data']);















                          foreach ($item_data['data'] as $key => $value) {







                            // print_r($value);







                          ?>











                            <div class="row">

                              <div class="col"><?= $value['itemName'] ?></div>

                              <div class="col"><?= $value['itemCode'] ?></div>

                              <div class="col"><?= decimalQuantityPreview($value['itemQuantity']) ?></div>

                              <div class="col"><?=decimalQuantityPreview( $value['itemQuantity'] )?></div>

                              <div class="col"><?= $value['uomName'] ?></div>

                              <div class="col"><?=formatDateWeb($item_datasets['data']['expectedDate']) ?></div>

                            </div>


                          <?php



                            $date = $item_datasets['data']['expectedDate'];
                          }



                          ?>







                        </div>



                      </div>



                    </div>



                  </div>







                </div>



              </div>

            </div>

          </div>

        </div> -->























        <div class="row">















          <div class="col-lg-12 col-md-12 col-sm-12">







            <!-- <a style="cursor: pointer;" class="btn btn-primary" data-toggle="modal" data-target="#fluidModalRightSuccessDemo">Settings</a> -->







            <div class="card">







              <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">







                <div class="head">







                  <i class="fa fa-info"></i>







                  <h4>Matrix Chart</h4>







                </div>







                <div class="head select-box-head">







                  <div class="form-inline display-flex" style="gap: 5px">







                    <label for="" style="color: #fff;">Rate Weight</label>







                    <select id="rateId" name="weightName" class="form-control" style="width: 62%;">







                      <option value="">Select Weight</option>







                      <option value="1">1</option>







                      <option value="2">2</option>







                      <option value="3">3</option>







                      <option value="4">4</option>







                      <option value="5">5</option>







                      <option value="6">6</option>







                      <option value="7">7</option>







                      <option value="8">8</option>







                      <option value="9">9</option>







                      <option value="10" selected>10</option>







                    </select>


                    <label for="" style="color: #fff;" id="rate_percent_id"> 58.8% </label>




                  </div>








                  <div class="form-inline display-flex" style="gap: 5px">







                    <label for="" style="color: #fff;">MOQ Weight</label>







                    <select id="moqId" name="weightName" class="form-control" style="width: 62%;">







                      <option value="">Select Weight</option>







                      <option value="1">1</option>







                      <option value="2" selected>2</option>







                      <option value="3">3</option>







                      <option value="4">4</option>







                      <option value="5">5</option>







                      <option value="6">6</option>







                      <option value="7">7</option>







                      <option value="8">8</option>







                      <option value="9">9</option>







                      <option value="10">10</option>







                    </select>


                    <label for="" style="color: #fff;" id="moq_percent_id">11.8%</label>




                  </div>







                  <div class="form-inline display-flex" style="gap: 5px">







                    <label for="" style="color: #fff;">Lead Time Weight</label>







                    <select id="leadId" name="weightName" class="form-control" style="width: 54%;">







                      <option value="">Select Weight</option>







                      <option value="1">1</option>







                      <option value="2">2</option>







                      <option value="3">3</option>







                      <option value="4">4</option>







                      <option value="5" selected>5</option>







                      <option value="6">6</option>







                      <option value="7">7</option>







                      <option value="8">8</option>







                      <option value="9">9</option>







                      <option value="10">10</option>







                    </select>


                    <label for="" style="color: #fff;" id="lead_percent_id">29.4%</label>




                  </div>







                  <button id="clickButton" class="btn btn-primary click-button-rfq float-right">Submit</button>







                </div>







                <!-- <div class="row others-info-head">







                <div class="row m-0 p-0">















                  <div class="col-6">







                    <i class="fa fa-info"></i>







                    <span class="h6 text-light ml-1">Matrix Chart</span>







                  </div>















                </div>







              </div> -->







              </div>







              <div class="card-body others-info canvas-info" style="height: auto;">







                <div class="row">







                  <div class="col-lg-12 col-md-12 col-sm-12">







                    <div class="row" style="flex-direction :column-reverse;">







                      <div class="col-lg-12 col-md-12 col-sm-12">










                        <div class="accordion accordion-flush matrix-accordion p-0" id="response">
                          <?php
                          foreach ($vendor_array_1 as $vd) {
                          ?>
                            <form action="purchase-order-creation.php" method="POST" id="" name="submitPoForm">
                              <!-- <form action="purchase-order-creation.php" method="POST" id="" name="submitPoForm"> -->
                              <input type='hidden' name='erp_v_id' value='<?= $vd['vendor_id'] ?>'>
                              <input type='hidden' name='date' value='<?= $date ?>'>
                              <div class="accordion-item" id="html_data">
                                <h2 class="accordion-header mb-0" id="flush-headingOne">
                                  <button class="accordion-button btn btn-primary collapsed mt-0 mb-0" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $vd['vendor_id'] ?>" aria-expanded="true" aria-controls="flush-collapseOne">
                                    <p class="vendor-name mb-0"><?= $vd['vendor'] ?></p>
                                  </button>
                                  <?php
                                  if (isset($vd['vendor_code']) && $vd['vendor_code'] != "") {
                                  ?>
                                    <div class="create-po-btn btn-create-po<?= $vd['vendor_id'] ?>">
                                      <button class="btn btn-primary button-create-PO m-0" type="submit" id="rfq_po" name="rfq_po">Create PO</button>
                                    </div>
                                  <?php
                                  } else {
                                  ?>
                                    <div class="create-po-btn btn-create-po<?= $vd['vendor_id'] ?>">
                                      <button type="button" class="btn btn-primary button-create-PO m-0 vendorRegs" id="vendorRegs_<?= $vd['vendor_id'] ?>" data-toggle="modal" data-target="#exampleModalRegister">Register</button>
                                      <!-- <a class="btn btn-primary button-create-PO m-0"  href="" name="">Register</a> -->
                                    </div>
                                  <?php
                                  }
                                  ?>
                                </h2>

                                <div id="flush-collapse<?= $vd['vendor_id'] ?>" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                  <div class="accordion-body vendor-accordion p-0">

                                    <table class="table defaultDataTable table-hover table-nowrap">
                                      <thead>
                                        <tr>
                                          <th>Item Name</th>
                                          <th>Item MOQ</th>
                                          <th>Item Rate</th>
                                          <th>Item Total Price</th>
                                          <th>Delivery Mode</th>
                                          <th>Lead Time</th>
                                          <th>Order Quantity</th>
                                        </tr>
                                      </thead>
                                      <tbody>

                                        <?php



                                        $id = $vd['vendor_id'];



                                        $items_list = "SELECT * FROM erp_vendor_item WHERE `erp_v_id` = '$id'";



                                        $item_dataset = queryGet($items_list, true);

                                  

                                        foreach ($item_dataset['data'] as $item) {



                                        ?>

                                          <tr>
                                            <td>
                                              <p class="pre-normal"><?= $item['item_name'] ?></p>
                                            </td>
                                            <td><?= decimalQuantityPreview($item['moq']) ?></td>
                                            <td><?= decimalValuePreview($item['price']) ?></td>
                                            <td><?= decimalValuePreview($item['total']) ?></td>

                                            <?php







                                            if ($item['delivery_mode'] == 1) {







                                              echo " <td> EX WORK </td>";
                                            } elseif ($item['delivery_mode'] == 2) {







                                              echo "<td> FOR </td>";
                                            } elseif ($item['delivery_mode'] == 3) {







                                              echo "<td> FOB </td>";
                                            } elseif ($item['delivery_mode'] == 4) {







                                              echo "<td> CIF </td>";
                                            }







                                            ?>

                                            <td><?= $item['lead_time'] ?></td>
                                            <td>
                                              <input type="text" class="form-control input-matrix matrix-class-<?= $vd['vendor_id'] ?>" name="items[<?= $item['erp_vi_id'] ?>][item_qty]" value="0">


                                              <input type="hidden" name="items[<?= $item['erp_vi_id'] ?>][id]" value="<?= $item['item_id'] ?>">
                                              <input type="hidden" name="items[<?= $item['erp_vi_id'] ?>][price]" value="<?= $item['price'] ?>">



                                              <input type="hidden" name="items[<?= $item['erp_vi_id'] ?>][moq]" value="<?= $item['moq'] ?>">



                                              <input type="hidden" name="items[<?= $item['erp_vi_id'] ?>][delivery_mode]" value="<?= $item['delivery_mode'] ?>">
                                            </td>
                                          </tr>
                                        <?php



                                        }



                                        ?>

                                      </tbody>
                                    </table>

                                    <!-- <div class="card list-view-div matrix-card">



                                      <div class="card-body">



                                        <div class="row">



                                          <div class="col">Item Name</div>



                                          <div class="col">Item MOQ</div>



                                          <div class="col">Item Rate</div>



                                          <div class="col">Item Total Price</div>



                                          <div class="col">Delivery Mode</div>



                                          <div class="col">Lead Time</div>



                                          <div class="col">Order Quantity</div>



                                        </div>



                                        <?php



                                        $id = $vd['vendor_id'];



                                        $items_list = "SELECT * FROM erp_vendor_item WHERE `erp_v_id` = '$id'";



                                        $item_dataset = queryGet($items_list, true);



                                        foreach ($item_dataset['data'] as $item) {



                                        ?>











                                          <div class="row">



                                            <div class="col"><?= $item['item_name'] ?></div>



                                            <div class="col"><?= $item['moq'] ?></div>



                                            <div class="col"><?= $item['price'] ?></div>



                                            <div class="col"><?= round($item['total'], 2) ?></div>



                                            <?php







                                            if ($item['delivery_mode'] == 1) {







                                              echo " <div class='col'> EX WORK </div>";
                                            } elseif ($item['delivery_mode'] == 2) {







                                              echo "<div class='col'> FOR </div>";
                                            } elseif ($item['delivery_mode'] == 3) {







                                              echo "<div class='col'> FOB </div>";
                                            } elseif ($item['delivery_mode'] == 4) {







                                              echo "<div class='col'> CIF </div>";
                                            }







                                            ?>



                                            <div class="col"><?= $item['lead_time'] ?></div>



                                            <div class="col">



                                              <input type="text" class="form-control input-matrix matrix-class-<?= $vd['vendor_id'] ?>" name="items[<?= $item['item_id'] ?>][item_qty]" value="0">



                                              <input type="hidden" name="items[<?= $item['item_id'] ?>][price]" value="<?= $item['price'] ?>">



                                              <input type="hidden" name="items[<?= $item['item_id'] ?>][moq]" value="<?= $item['moq'] ?>">



                                              <input type="hidden" name="items[<?= $item['item_id'] ?>][delivery_mode]" value="<?= $item['delivery_mode'] ?>">



                                            </div>



                                          </div>





                                        <?php



                                        }



                                        ?>



                                      </div>



                                    </div> -->
                                  </div>
                                </div>
                              </div>

                            </form>
                          <?php
                          }
                          ?>


                        </div>


                      </div>







                      <div class="col-lg-12 col-md-12 col-sm-12">



                        <div class="canvas">



                          <div id="chartDivPieChartAsBullet" class="chartContainer"></div>



                        </div>



                      </div>







                    </div>







                  </div>







                </div>







              </div>







            </div>







          </div>







        </div>







      <?php } else { ?>







        <div class="row">







          <div class="col-lg-12 col-md-12 col-sm-12">







            <div class="no-response">
              <img src="../../public/assets/gif/no-response.gif" alt="">
            </div>

            <h6 class="no-response-text text-xl font-bold">No Response Found</h6>

            <p class="text-sm text-center"><a onclick="backButton()" style="cursor: pointer; text-decoration: underline;">Go back to previous page</a></p>







          </div>







        </div>















      <?php } ?>







    </div>















</div>















</div>







</section>







</div>







</div>







</section>







</div>







<!-- End Pegination from------->

<!-- Modal -->
<div class="modal fade zoom-in matrix-modal" id="exampleModalRegister" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Vendor Registration</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="overflow: hidden; height: 570px;">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 mr-auto mb-4">
            <div class="multisteps-form__progress">
              <button class="multisteps-form__progress-btn js-active" type="button" title="User Info">Basic Details</button>
              <button class="multisteps-form__progress-btn" type="button" title="Address" disabled>Others Address</button>
              <button class="multisteps-form__progress-btn" type="button" title="Order Info" disabled>Accounting</button>
              <button class="multisteps-form__progress-btn" type="button" title="Comments" disabled>POC Details</button>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 m-auto">
            <form class="multisteps-form__form" action="<?php $_SERVER['PHP_SELF']; ?>" method="POST" id="add_frm" name="add_frm">
              <input type="hidden" name="createdata" id="createdata" value="">
              <input type="hidden" name="company_id" id="company_id" value="<?= $company_id; ?>">
              <input type="hidden" name="company_branch_id" id="company_branch_id" value="<?= $branch_id; ?>">
              <input type="hidden" name="company_location_id" id="company_location_id" value="<?= $location_id; ?>">
              <input type="hidden" name="erp_v_id" id="erp_v_id">
              <input type="hidden" name="createtype" id="createtype" value="withgst">
              

              <!--single form panel-->
              <div class="multisteps-form__panel js-active" data-animation="scaleIn">
                <div class="card vendor-details-card">
                  <div class="card-header">
                    <div class="display-flex">
                      <div class="head">
                        <i class="fa fa-info"></i>
                        <h4>Basic Details</h4>
                      </div>
                      <div class="head">
                        <button class="btn btn-primary" id="getGstinReturnFiledStatusBtn" data-gstin="<?= $_GET["gstin"] ?>" style="" data-toggle="modal" data-target="#gst-field-status-modal"><i class="fa fa-file"></i>&nbsp;&nbsp;GST Filed Status</button>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <div class="forform-control">
                            <label for="">GST</label>
                            <input type="text" class="form-control" name="vendor_gstin" id="vendor_gstin" value="<?php echo $_GET["gstin"]; ?>">
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <div class="forform-control">
                            <label for="">Pan *</label>
                            <input type="text" class="form-control" name="vendor_pan" id="vendor_pan" value="<?php echo $vendorPan; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">Trade Name</label>
                            <input type="text" class="form-control" name="trade_name" id="trade_name" value="<?php echo $gstDetails['tradeNam']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">Legal Name</label>
                            <input type="text" class="form-control" name="legal_name" id="legal_name" value="<?php echo $gstDetails['tradeNam']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">Constitution of Business</label>
                            <input type="text" class="form-control" name="con_business" id="con_business" value="<?php echo $gstDetails['ctb']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">Flat Number</label>
                            <input type="text" class="form-control" name="flat_no" id="flat_no" value="<?php echo $gstDetails['pradr']['addr']['flno']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">Building Number</label>
                            <input type="text" class="form-control" name="build_no" id="build_no" value="<?php echo $gstDetails['pradr']['addr']['bno']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">Street Name</label>
                            <input type="text" class="form-control" name="street_name" id="street_name" value="<?php echo $gstDetails['pradr']['addr']['st']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">Location</label>
                            <input type="text" class="form-control" name="location" id="location" value="<?php echo $gstDetails['pradr']['addr']['loc']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">City</label>
                            <input type="text" class="form-control" name="city" id="city" value="<?php echo $city; ?>">
                          </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                          <div class="forform-control">
                            <label for="">Pin Code</label>
                            <input type="number" class="form-control" name="pincode" id="pincode" value="<?php echo $gstDetails['pradr']['addr']['pncd']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <div class="forform-control">
                            <label for="">State</label>
                            <input type="text" class="form-control" name="state" id="state" value="<?php echo $gstDetails['pradr']['addr']['stcd']; ?>">
                          </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                          <div class="forform-control">
                            <label for="">District</label>
                            <input type="text" class="form-control" name="district" id="district" value="<?php echo $gstDetails['pradr']['addr']['dst']; ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex mt-2 mb-2">
                      <button class="btn btn-primary ml-auto js-btn-next" type="button" title="Next">Next</button>
                    </div>
                  </div>
                </div>
                <!-- <h4 class="multisteps-form__title">Basic Details</h4> -->
                <!-- <div class="btn btn-primary" id="getGstinReturnFiledStatusBtn" data-gstin="<?= $_GET["gstin"] ?>" style="" data-toggle="modal" data-target="#fluidModalRight">GST Filed Status</div> -->

              </div>
              <!--single form panel-->
              <div class="multisteps-form__panel step2" data-animation="scaleIn">
                <div class="card">
                  <div class="card-header">
                    <div class="head">
                      <h4>Other Address</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="form-table" id="customFields">
                        <?php
                        if ($othersaddress_count > 0) {
                          foreach ($resultGstData['data']['adadr'] as $key => $valaddress) {
                            $valaddress_addr = $valaddress['addr'];
                        ?>
                            <div class="row">
                              <?php if ($key == 0) { ?>
                                <div class="col-md-12">
                                  <a href="javascript:void(0);" class="addCF btn btn-primary float-right" value="5"><i class="fa fa-plus"></i></a>
                                </div>
                              <?php } else { ?>
                                <div class="col-md-12"><a href="javascript:void(0);" class="remCF btn btn-danger mt-5 mb-2 float-right"><i class="fa fa-minus"></i></a></div>
                              <?php } ?>
                              <div class="col-md-6">
                                <div class="form-input">
                                  <label>Flat Number</label>
                                  <input type="text" name="vendorOtherAddress[<?= $key ?>][vendor_business_flat_no]" class="form-control" id="vendor_business_flat_no" value="<?php echo $valaddress_addr['flno']; ?>">
                                </div>
                                <div class="form-input">
                                  <label>Pin Code</label>
                                  <input type="text" name="vendorOtherAddress[<?= $key ?>][vendor_business_pin_code]" class="form-control" id="vendor_business_pin_code" value="<?php echo $valaddress_addr['pncd']; ?>">
                                </div>
                                <div class="form-input">
                                  <label>District</label>
                                  <input type="text" name="vendorOtherAddress[<?= $key ?>][vendor_business_district]" class="form-control" id="vendor_business_district" value="<?php echo $valaddress_addr['dst']; ?>">
                                </div>
                                <div class="form-input">
                                  <label>Location</label>
                                  <input type="text" name="vendorOtherAddress[<?= $key ?>][vendor_business_location]" class="form-control" id="vendor_business_location" value="<?php echo $valaddress_addr['loc']; ?>">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-input">
                                  <label>Building Number</label>
                                  <input type="text" name="vendorOtherAddress[<?= $key ?>][vendor_business_building_no]" class="form-control" id="vendor_business_building_no" value="<?php echo $valaddress_addr['bnm']; ?>">
                                </div>

                                <div class="form-input">
                                  <label>Street Name</label>
                                  <input type="text" name="vendorOtherAddress[<?= $key ?>][vendor_business_street_name]" class="form-control" id="vendor_business_street_name" value="<?php echo $valaddress_addr['st']; ?>">
                                </div>

                                <div class="form-input">
                                  <label>City</label>
                                  <input type="text" name="vendorOtherAddress[<?= $key ?>][vendor_business_city]" class="form-control" id="vendor_business_city" value="<?php echo $valaddress_addr['city']; ?>">
                                </div>

                                <div class="form-input">
                                  <label>State</label>
                                  <input type="text" name="vendorOtherAddress[<?= $key ?>][vendor_business_state]" class="form-control" id="vendor_business_state" value="<?php echo $valaddress_addr['stcd']; ?>">
                                </div>

                              </div>
                            </div>
                          <?php }
                        } else { ?>
                          <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6"></div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <button href="javascript:void(0);" class="addCF btn btn-primary float-right"><i class="fa fa-plus" style="margin-right: 0;"></i></button>
                            </div>
                            <div class="col-md-6">
                              <div class="form-input">
                                <label>Flat Number</label>
                                <input type="text" name="vendorOtherAddress[0][vendor_business_flat_no]" class="form-control" id="vendor_business_flat_no">

                              </div>
                              <div class="form-input">
                                <label>Pin Code</label>

                                <input type="text" name="vendorOtherAddress[0][vendor_business_pin_code]" class="form-control" id="vendor_business_pin_code">
                              </div>
                              <div class="form-input">
                                <label>District</label>

                                <input type="text" name="vendorOtherAddress[0][vendor_business_district]" class="form-control" id="vendor_business_district">
                              </div>
                              <div class="form-input">
                                <label>Location</label>

                                <input type="text" name="vendorOtherAddress[0][vendor_business_location]" class="form-control" id="vendor_business_location">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-input">
                                <label>Building Number</label>

                                <input type="text" name="vendorOtherAddress[0][vendor_business_building_no]" class="form-control" id="vendor_business_building_no">
                              </div>

                              <div class="form-input">
                                <label>Street Name</label>

                                <input type="text" name="vendorOtherAddress[0][vendor_business_street_name]" class="form-control" id="vendor_business_street_name">
                              </div>

                              <div class="form-input">
                                <label>City</label>

                                <input type="text" name="vendorOtherAddress[0][vendor_business_city]" class="form-control" id="vendor_business_city">
                              </div>

                              <div class="form-input">
                                <label>State</label>

                                <input type="text" name="vendorOtherAddress[0][vendor_business_state]" class="form-control" id="vendor_business_state">
                              </div>

                            </div>
                          </div>
                        <?php } ?>
                      </div>


                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex mt-2 mb-2">
                      <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                      <button class="btn btn-primary ml-auto js-btn-next" type="button" data-toggle="modal" data-target="#checkUpload" title="Next">Next</button>
                    </div>
                  </div>
                </div>
              </div>
              <!--single form panel-->
              <!-- <div class="modal fade" id="checkUpload" style="z-index: 999999;" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content m-auto" style="max-width: 375px; border-radius: 20px;">
                    <div class="modal-body p-0" style="overflow: hidden; border-radius: 20px;">
                      <div id="uploadGrnInvoiceDiv" class="create-grn">
                        <div class="upload-files-container">
                          <div class="card check-upload">
                            <div class="card-header">
                              <div class="head">
                                <h4>Upload Cancel Check</h4>
                              </div>
                            </div>
                            <div class="card-body">
                              <div class="drag-file-area">
                                <i class="fa fa-arrow-up po-list-icon text-center m-auto"></i>
                                <br>
                                <input type="file" class="form-control" id="invoiceFileInput" name="" placeholder="Invoice Upload" required />
                              </div>
                              <div class="file-block">
                                <div class="progress-bar"> </div>
                              </div>
                              <button type="button" class="upload-button btn btn-primary vendor_bank_cancelled_cheque_btn" name="" id="vendor_bank_cancelled_cheque_btn"> Upload </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div> -->

              <div class="modal fade cancelled-check-modal" id="checkUpload" style="z-index: 999999;" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content m-auto" style="border-radius: 20px;">
                    <div class="modal-body p-0" style="overflow: hidden; border-radius: 20px;">
                      <div id="uploadGrnInvoiceDiv" class="create-grn">
                        <div class="upload-files-container">
                          <div class="card check-upload">
                            <div class="card-body">
                              <div class="head text-center">
                                <h4 class="mb-0">Upload Cancel Check</h4>
                              </div>
                              <div class="drag-file-area">
                                <i class="fa fa-file-upload po-list-icon text-center m-auto"></i>
                                <br>

                                <div class="drag-drop-text mb-5 mt-4">
                                  <!-- <p class="text-sm"> Drag & Drop Cancelled Check here</p> -->
                                  <div class="check-sample-section mt-4">
                                    <p class="text-xs text-left">Check Sample :</p>
                                    <hr class="mt-1 mb-2">
                                    <img class="check-img" src="../../public/assets/img/cheque-book.jpg" alt="check-sample">
                                  </div>
                                </div>





                                <!-- <div class="notes">
                                  <ul>
                                    <p class="font-bold text-sm">Notes:</p>
                                    <li>
                                        <p class="text-xs">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
                                    </li>
                                    <li>
                                        <p class="text-xs">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
                                    </li>
                                    <li>
                                        <p class="text-xs">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
                                    </li>
                                  </ul>
                                </div> -->
                                <!-- <div class="upload-btn m-auto mt-3 mb-3">
                                  <button class="btn btn-primary upload" id="invoiceFileInput">Upload</button>
                                </div> -->
                                <input type="file" class="form-control" id="invoiceFileInput" name="" placeholder="Invoice Upload" required />

                                <div class="file-block">
                                  <div class="progress-bar"> </div>
                                </div>
                                <button type="button" class="upload-button btn btn-primary vendor_bank_cancelled_cheque_btn mt-4 mb-2" name="" id="vendor_bank_cancelled_cheque_btn"> Upload </button>

                              </div>

                              <div class="grn-notes">
                                <h4 class="text-xs">Note:</h4>
                                <hr>
                                <ul class="pl-0 mb-0">
                                  <li>
                                    <p class="text-xs">You can upload Cancelled Check here</p>
                                  </li>
                                  <li>
                                    <p class="text-xs">Your maximum file size should be <span class="font-bold text-xs">2 mb/file</span></p>
                                  </li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="multisteps-form__panel" data-animation="scaleIn">
                <div class="card">
                  <div class="card-header">
                    <div class="head">
                      <h4>
                        Accounting
                      </h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="">Company Currency</label>
                            <select id="company_currency" name="currency" class="form-control mt-0">
                              <?php
                              $listResult = getAllCurrencyType();
                              if ($listResult["status"] == "success") {
                                foreach ($listResult["data"] as $listRow) {
                              ?>
                                  <option value="<?php echo $listRow['currency_id']; ?>"><?php echo $listRow['currency_name']; ?></option>
                              <?php }
                              } ?>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Credit Period(In Days)</label>
                            <input type="text" class="form-control" name="credit_period" id="vendor_credit_period" value="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label for="vendor_bank_cancelled_cheque"> Upload Cancled Cheque <span class="Ckecked_loder"></span> </label>
                            <input class="vendor_bank_cancelled_cheque form-control" type="file" name="vendor_bank_cancelled_cheque" id="vendor_bank_cancelled_cheque">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>IFSC</label>
                            <input type="text" class="form-control" name="vendor_bank_ifsc" id="vendor_bank_ifsc" value="">
                            <div>
                              <span style="font-size: 0.7em; " class="tick-icon"></span>
                              <span class="text-xs" id="ifscCodeMsg"></span>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" name="vendor_bank_name" id="vendor_bank_name" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Branch Name</label>
                            <input type="text" class="form-control" name="vendor_bank_branch" id="vendor_bank_branch" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Address</label>
                            <input type="text" class="form-control" name="vendor_bank_address" id="vendor_bank_address" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Account Number</label>
                            <input type="text" class="form-control account_number" name="vendor_bank_account_no" id="account_number" value="">

                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Bank Account Holder</label>
                            <input type="text" class="form-control" name="account_holder" id="account_holder" value="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Vendor Opening Balance</label>
                            <input type="text" class="form-control" name="vendor_opening_balance" id="vendor_opening_balance" value="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label id="bank_detail_error">Bank Account Holder</label>

                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="row">
                      <div class="button-row d-flex mt-2 mb-2">
                        <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                        <button class="btn btn-primary ml-auto js-btn-next" type="button" data-toggle="modal" data-target="#visitingCard" title="Next">Next</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--single form panel-->

              <div class="modal fade" id="visitingCard" style="z-index: 999999;" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content m-auto" style="max-width: 375px; border-radius: 20px;">

                    <div class="modal-body p-0" style="overflow: hidden; border-radius: 20px;">
                      <div id="uploadGrnInvoiceDiv" class="create-grn">
                        <div class="upload-files-container">
                          <div class="card visiting-card-upload">
                            <div class="card-header">
                              <div class="head">
                                <h4>Upload Visiting Card</h4>
                              </div>
                            </div>
                            <div class="card-body">
                              <div class="drag-file-area">
                                <i class="fa fa-arrow-up po-list-icon text-center m-auto"></i>
                                <br>
                                <input type="file" class="form-control" id="visitingFileInput" name="" placeholder="Visiting Card Upload" required />
                              </div>
                              <div class="file-block">
                                <div class="progress-bar"> </div>
                              </div>
                              <button type="button" class="upload-button btn btn-primary visiting_card_btn" name="" id="visiting_card_btn"> Upload </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="multisteps-form__panel" data-animation="scaleIn">

                <div class="card">
                  <div class="card-header">
                    <div class="head">
                      <h4>POC Details</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="multisteps-form__content">
                      <div class="modal" id="checkupload"></div>
                      <div class="row">

                        <div class="col-md-12">
                          <div class="form-input">
                            <label for="visiting_card"> Upload Visiting Card <span class="visiting_loder"></span></label>
                            <input class="visiting_card form-control" type="file" name="visiting_card" id="visiting_card">
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Name of Person*</label>
                            <input type="text" class="form-control" name="vendor_authorised_person_name" id="adminName" value="" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Designation</label>
                            <input type="text" class="form-control" name="vendor_authorised_person_designation" id="vendor_authorised_person_designation" value="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Phone Number*</label>
                            <input type="text" class="form-control" name="vendor_authorised_person_phone" id="adminPhone" value="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Phone </label>
                            <input type="number" class="form-control" name="vendor_authorised_alt_phone" id="vendor_authorised_person_phone" value="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Email*</label>
                            <input type="text" class="form-control" name="vendor_authorised_person_email" id="adminEmail" value="" readonly>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Alternative Email</label>
                            <input type="email" class="form-control" name="vendor_authorised_alt_email" id="vendor_authorised_person_email" value="">
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-input">
                            <label>Login Password [Will be send to the POC email]</label>
                            <input type="text" class="form-control" name="adminPassword" id="adminPassword" value="<?php echo rand(00000, 999999) ?>">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-input">
                            <label for="">Vendor Picture</label>
                            <input type="file" class="form-control" name="vendor_picture" id="vendor_picture">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-input">
                            <label for="" style="visibility: hidden;">Visible For All</label>
                            <select id="vendor_visible_to_all" name="vendor_visible_to_all" class="select2 form-control mt-0 form-control-border borderColor">
                              <option value="" selected>Visible For All</option>
                              <option value="No">No</option>
                              <option value="Yes" selected>Yes</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="button-row d-flex mt-2 mb-2">
                      <button class="btn btn-primary js-btn-prev" type="button" title="Prev">Prev</button>
                      <!-- <button class="btn ml-auto btn-danger add_data" type="button" title="Save As Draft" value="add_draft">Save As Draft</button> -->
                      <!-- <button class="btn btn-primary ml-auto add_data" type="button" title="Final Submit" value="add_post" id="add_frm">Final Submit</button> -->
                      <button class="btn btn-primary ml-auto add_data" type="button" title="Final Submit" value="add_post">Final Submit</button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>




      </div>

    </div>
  </div>
</div>


<!-- right modal start here  -->
<div class="modal fade gst-field-status-modal" id="gst-field-status-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
  <div class="modal-dialog field-status modal-dialog-centered modal-dialog-scrollable" role="document">
    <div class="modal-content p-0">
      <div class="modal-header">
        <div class="head p-2">
          <h4 class="mb-0">
            <ion-icon name="document-text-outline" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>
            GST Filed Status
          </h4>
        </div>
        <div class="gst-number d-flex gap-2">
          <span class="text-xs font-bold">GSTIN :</span>
          <p id="mdl_gstin_span" class="text-xs">XXXXXXXXXXXXXXXX</p>
        </div>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card mb-0 bg-transparent">
              <div class="card-header p-0 rounded mb-2">
                <div class="head p-2">
                  <h4>
                    <ion-icon name="document-text-outline" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>&nbsp; GST Filed Status For GSTR1
                  </h4>
                </div>
              </div>
              <div class="card-body">
                <div class="row">


                  <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span29ACJFS5232R1ZA" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp_Div">

                  </div>
                </div>
              </div>
            </div>

            <div class="card mb-0 bg-transparent">
              <div class="card-header p-0 rounded mb-2">
                <div class="head p-2">
                  <h4>
                    <ion-icon name="document-text-outline" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>&nbsp; GST Filed Status For GSTR3B
                  </h4>
                </div>
              </div>
              <div class="card-body">
                <div class="row">


                  <!-- <div class="col-lg-3 col-md-3 col-sm-6 mb-2">
                                                <span class="text-xs font-bold">Last Update&nbsp;</span>
                                                <p id="mdl_gstin_last_update_comp_span29ACJFS5232R1ZA" class="text-xs">XX/XX/XXXX</p>
                                              </div> -->

                </div>
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-sm-12 gst-return-data" id="gstinReturnsDatacomp3b_Div">

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
<!-- right modal end here  -->

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

<script>
  $(document).ready(function() {
    // $(document).on('click', '#getGstinReturnFiledStatusBtn', function() {
    //   // url: `ajaxs/vendor/ajax-gst-filed-status.php?gstin=${gstin}`,
    //   // let gstin = $(this).data('gstin');
    //   let gstin = $(this).val();
    //   console.log("Getting gstin return status of", gstin);
    //   $.ajax({
    //     url: `ajaxs/vendor/ajax-gst-review.php?gstin=${gstin}`,
    //     type: 'get',
    //     beforeSend: function() {
    //       $("#gstinReturnsDataDiv").html(`Loding...`);
    //     },
    //     success: function(response) {
    //       responseObj = JSON.parse(response);
    //       console.log(responseObj);
    //       responseData = responseObj["data"];
    //       $("#mdl_gstin_span").html(responseData["gstin"]);
    //       $("#mdl_gstin_reg_span").html(responseData["rgdt"]);
    //       $("#mdl_gstin_status_span").html(responseData["sts"]);

    //       let gstinReturnsDataDivHtml = `
    //         <table class="table table-striped table-bordered w-100">
    //         <thead>
    //           <tr>
    //             <th>Month</th>
    //             <th>GSTR1</th>
    //             <th>GSTR3B</th>
    //             <th>FY</th>
    //           </tr>
    //         </thead>
    //         <tbody>`;
    //       responseData["returns"].forEach(function(rowVal, rowId) {

    //         gstinReturnsDataDivHtml += `
    //             <tr>
    //               <td>${rowVal["month"]}</td>
    //               <td>${rowVal["gstr1"]["dof"] ? '<i class="fa fa-check" style="color: green;"></i>' : '<i class="fa fa-window-close" style="color: red;"></i>'}</td>
    //               <td>${rowVal["gstr3b"]["dof"] ? '<i class="fa fa-check" style="color: green;"></i>' : '<i class="fa fa-window-close" style="color: red;"></i>'}</td>
    //               <td>${rowVal["gstr1"]["fy"] ?? "-"}</td>
    //             </tr>
    //           `;
    //       });
    //       gstinReturnsDataDivHtml += `</tbody></table>`;
    //       $("#gstinReturnsDataDiv").html(gstinReturnsDataDivHtml);


    //       console.log(responseData);

    //       //console(gstinReturnsDataDivHtml);
    //     }
    //   });
    // });


    $(document).on('click', '#getGstinReturnFiledStatusBtn', function() {
      // url: `ajaxs/vendor/ajax-gst-filed-status.php?gstin=${gstin}`,
      let gstin = $(this).val();
      let gstin_status = $(this).data('gstin_status');
      let gstin_reg_date = $(this).data('gstin_reg_date');
      let gstin_last_update = $(this).data('gstin_last_update');
      console.log("Getting gstin return status of", gstin);
      $.ajax({
        url: `ajaxs/vendor/ajax-gst-review.php?gstin=${gstin}`,
        type: 'get',
        beforeSend: function() {
          $("#gstinReturnsDataDiv").html(`Loading...`);
        },

        success: function(response) {

          responseObj = JSON.parse(response);
          let fy = responseObj['fy'];
          responseData = responseObj["data"];
          //  console.log(responseData["fillingFreq"][Object.keys(responseData["fillingFreq"])[0]]);
          // alert(responseData["lstupdt"]);
          // $("#mdl_gstin_comp_span" + vendorGst).html(responseData["gstin"]); 

          // $("#mdl_gstin_reg_comp_span" + vendorGst).html(responseData["rgdt"]);

          // var gstin_status = responseData["sts"];

          // $("#mdl_gstin_last_update_comp_span" + vendorGst).html(responseData["lstupdt"]);

          // $("#mdl_gstin_reg_comp_span" + vendorGst).html(responseData["rgdt"]);

          // mdl_gstin_status_comp_span

          // $("#mdl_gstin_last_update_comp_span"+vendorGst).html(gstin_last_update);

          // if (gstin_status == "Active") {
          //   $("#mdl_gstin_status_comp_span" + vendorGst).html(`<span class="bg-success text-light rounded px-1">${gstin_status}</span>`);
          // } else {
          //   $("#mdl_gstin_status_comp_span" + vendorGst).html(`<span class="bg-warning text-light rounded px-1">${gstin_status}</span>`);
          // }
          // let returnType = responseData["fillingFreq"][Object.keys(responseData["fillingFreq"])[0]] ?? "M";

          // alert(returnType);

          let gstinReturnsDataDivHtml = `
    <table class="table table-striped table-bordered w-100">
    <thead>
      <tr>
        <th>Financial Year</th>
        <th>Tax Period</th>
        <th>Date of Filing</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>`;


          responseData["EFiledlist"].forEach(function(rowVal, rowId) {
            //  console.log(rowVal);


            if (rowVal['rtntype'] == 'GSTR1') {
              var dateString = rowVal["ret_prd"];

              // Extract the first two characters as the month
              var monthString = dateString.substr(0, 2);

              // Convert the month string to an integer
              var month = parseInt(monthString, 10);

              // Array of month names
              var monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
              ];

              // Get the month name based on the numeric month
              var monthName = monthNames[month - 1]; // Subtract 1 because arrays are 0-based




              gstinReturnsDataDivHtml += `
          <tr>
            <td>${fy}</td>
            <td>${monthName ?? "-"}</td>
            <td>${rowVal["dof"] ?? "-"}</td>
            <td>${rowVal["status"] ? '<i class="fa fa-check" style="color: green;"> FILED</i>' : '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'}</td>
         
          </tr>
        `;
            }
          });


          gstinReturnsDataDivHtml += `</tbody></table>`;


          //3b


          let gstinReturnsDataDivHtml3b = `
    <table class="table table-striped table-bordered w-100">
    <thead>
      <tr>
        <th>Financial Year</th>
        <th>Tax Period</th>
        <th>Date of Filing</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>`;


          responseData["EFiledlist"].forEach(function(rowVal, rowId) {
            //  console.log(rowVal);


            if (rowVal['rtntype'] == 'GSTR3B') {
              var dateString = rowVal["ret_prd"];

              // Extract the first two characters as the month
              var monthString = dateString.substr(0, 2);

              // Convert the month string to an integer
              var month = parseInt(monthString, 10);

              // Array of month names
              var monthNames = [
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
              ];

              // Get the month name based on the numeric month
              var monthName = monthNames[month - 1]; // Subtract 1 because arrays are 0-based




              gstinReturnsDataDivHtml3b += `
          <tr>
            <td>${fy}</td>
            <td>${monthName ?? "-"}</td>
            <td>${rowVal["dof"] ?? "-"}</td>
            <td>${rowVal["status"] ? '<i class="fa fa-check" style="color: green;"> FILED</i>' : '<i class="fa fa-window-close" style="color: red;"> NOT FILED</i>'}</td>
         
          </tr>
        `;
            }
          });


          gstinReturnsDataDivHtml3b += `</tbody></table>`;




          $("#gstinReturnsDatacomp_Div").html(gstinReturnsDataDivHtml);
          $("#gstinReturnsDatacomp3b_Div").html(gstinReturnsDataDivHtml3b);
          $("#mdl_gstin_span").html(gstin);
          console.log(gstinReturnsDataDivHtml);
        }
      });
    });




  });
</script>







<script>
  // ====================================== Pie Charts as Bullets ======================================



  $(document).ready(function() {



    var chart;







    function loadRfqMatrix(data) {



      am4core.ready(function() {



        // Themes 



        am4core.useTheme(am4themes_animated);



        // Create chart instance



        chart = am4core.create("chartDivPieChartAsBullet", am4charts.XYChart);



        chart.logo.disabled = true;



        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in







        // Add data



        chart.data = data;







        // Create axes



        var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());



        categoryAxis.dataFields.category = "vendor";



        categoryAxis.renderer.grid.template.disabled = true;







        var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());



        valueAxis.title.text = "Units sold (M)";



        valueAxis.min = 0;



        valueAxis.renderer.baseGrid.disabled = true;



        valueAxis.renderer.grid.template.strokeOpacity = 0.07;







        // Create series



        var series = chart.series.push(new am4charts.ColumnSeries());



        series.dataFields.valueY = "units";



        series.dataFields.categoryX = "vendor";



        series.tooltip.pointerOrientation = "vertical";











        var columnTemplate = series.columns.template;



        // add tooltip on column, not template, so that slices could also have tooltip



        columnTemplate.column.tooltipText = "Vendor Name: {categoryX}\nUnits: {valueY}";



        columnTemplate.column.tooltipY = 0;



        columnTemplate.column.cornerRadiusTopLeft = 20;



        columnTemplate.column.cornerRadiusTopRight = 20;



        columnTemplate.strokeOpacity = 0;











        // as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set



        columnTemplate.adapter.add("fill", function(fill, target) {



          var color = chart.colors.getIndex(target.dataItem.index * 3);



          return color;



        });







        // create pie chart as a column child



        var pieChart = series.columns.template.createChild(am4charts.PieChart);



        pieChart.width = am4core.percent(80);



        pieChart.height = am4core.percent(80);



        pieChart.align = "center";



        pieChart.valign = "middle";



        pieChart.dataFields.data = "pie";







        var pieSeries = pieChart.series.push(new am4charts.PieSeries());



        pieSeries.dataFields.value = "value";



        pieSeries.dataFields.category = "title";



        pieSeries.labels.template.disabled = true;



        pieSeries.ticks.template.disabled = true;



        pieSeries.slices.template.stroke = am4core.color("#ffffff");



        pieSeries.slices.template.strokeWidth = 1;



        pieSeries.slices.template.strokeOpacity = 0;







        pieSeries.slices.template.adapter.add("fill", function(fill, target) {



          return am4core.color("#ffffff")



        });







        pieSeries.slices.template.adapter.add("fillOpacity", function(fillOpacity, target) {



          return (target.dataItem.index + 1) * 0.2;



        });







        pieSeries.hiddenState.properties.startAngle = -90;



        pieSeries.hiddenState.properties.endAngle = 270;







        // this moves the pie out of the column if column is too small



        pieChart.adapter.add("verticalCenter", function(verticalCenter, target) {



          var point = am4core.utils.spritePointToSprite({



            x: 0,



            y: 0



          }, target.seriesContainer, chart.plotContainer);



          point.y -= target.dy;







          if (point.y > chart.plotContainer.measuredHeight - 15) {



            target.dy = -target.seriesContainer.measuredHeight - 15;



          } else {



            target.dy = 0;



          }



          return verticalCenter



        })







      });



    }



    loadRfqMatrix(<?= json_encode($vendor_array_1) ?>);











    $(document).on("click", "#clickButton", function() {



      $.ajax({



        type: "POST",



        url: `ajaxs/pr/ajax-vendor-matrix.php`,



        data: {



          rfq: <?= json_encode($_GET['rfq']) ?>,



          rate: $("#rateId").val(),



          lead: $("#leadId").val(),



          moq: $("#moqId").val(),



          date: <?= json_encode($date); ?>



        },



        beforeSend: function() {



          //   $("#customerDropDown").html(`<option value="">Loding...</option>`);



        },



        success: function(response) {



          var proper_response = JSON.parse(response);



          var rfq_id = <?= json_encode($_GET['rfq']) ?>;



          console.log(proper_response);



          // chart.data = proper_response['graph'];



          // chart.appear();



          chart.dispose();



          loadRfqMatrix(proper_response["graph"]);







          $("#response").html(proper_response["item"]);

          $("#rate_percent_id").html(proper_response["rate_percent_id"] + "%");
          $("#moq_percent_id").html(proper_response["moq_percent_id"] + "%");
          $("#lead_percent_id").html(proper_response["lead_percent_id"] + "%");







        }



      });







    });



  });







  $(document).ready(function() {



    $('.tab-label').click(function() {



      $('.tab-content').slideToggle();



    })



  });







  // ++++++++++++++++++++++++++++++++++++++ Pie Charts as Bullets ++++++++++++++++++++++++++++++++++++++
</script>

<script src="<?= BASE_URL; ?>public/validations/vendorValidation.js"></script>

<script>
  $(document).ready(function() {


    function check_account(ifsc, acc) {
      // console.log(ifsc);
      // console.log(acc);
      $.ajax({
        url: `ajaxs/vendor/ajax-account.php`,
        type: 'POST',
        data: {
          ifsc: ifsc,
          acc: acc

        },
        beforeSend: function() {

        },
        success: function(response) {
          // alert(response);
          console.log(response);
          if (response > 0) {

            $('#bank_detail_error').html('bank account already exists');

            document.getElementById("next_last").disabled = true;

          } else {
            $('#bank_detail_error').html(``);

            document.getElementById("next_last").disabled = false;
          }
        }

      });
    }



    $(document).on("keyup blur", "#vendor_bank_ifsc", function() {
      let ifsc = $(this).val();
      let acc = $('.account_number').val();
      // alert(acc);
      check_account(ifsc, acc);
      $.ajax({
        url: `https://ifsc.razorpay.com/${ifsc}`,
        method: "GET",
        success: function(response) {
          $(".IFSClass").addClass(`border border-success`);
          $(".tick-icon").text(`✅`);
          $(".IFSClass").removeClass(`border-danger`);
          $("#ifscCodeMsg").html(`<span class="text-success">ifsc code is valid!</span>`);

          $("#vendor_bank_address").val(response.ADDRESS);
          $("#vendor_bank_name").val(response.BANK);
          $("#vendor_bank_branch").val(response.BRANCH);
        },
        error: function(xhr, status, error) {
          $(".IFSClass").addClass(`border border-danger`);
          $(".tick-icon").text(`❌`);
          $(".IFSClass").removeClass(`border-success`);
          $("#ifscCodeMsg").html(`<span class="text-danger">ifsc code is not valid!</span>`);

          $("#vendor_bank_address").val('');
          $("#vendor_bank_name").val('');
          $("#vendor_bank_branch").val('');
        }
      });


    });


    $(document).on("click", ".addCF", function() {
      let addressRandNo = Math.ceil(Math.random() * 100000);
      $("#customFields").append(`<div class="row">
    <div class="col-md-12 mt-5 mb-2"><a href="javascript:void(0);"
            class="remCF btn btn-danger float-right"><i class="fa fa-minus"></i></a></div>
    <div class="col-md-6">
        <div class="form-input">
        <label>Flat Number</label>    
        <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_flat_no']" class="form-control"
                id="vendor_business_flat_no"/>
            
        </div>
        <div class="form-input">
        <label>Pin Code</label>    
        <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_pin_code']" class="form-control"
                id="vendor_business_pin_code"/>
            
        </div>
        <div class="form-input">
        <label>District</label>    
        <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_district']" class="form-control"
                id="vendor_business_district"/>
            
        </div>
        <div class="form-input">
        <label>Location</label>    
        <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_location']" class="form-control"
                id="vendor_business_location"/>
            
        </div>
    </div>
    <div class="col-md-6">

        <div class="form-input">
        <label>Building Number</label>    
        <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_building_no']"
                class="form-control" id="vendor_business_building_no"/>
            
        </div>

        <div class="form-input">
        <label>Street Name</label>    
        <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_street_name']"
                class="form-control" id="vendor_business_street_name"/>
            
        </div>

        <div class="form-input">
        <label>City</label>    
        <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_city']" class="form-control"
                id="vendor_business_city"/>
            
        </div>

        <div class="form-input">
        <label>State</label>    
        <input type="text" name="vendorOtherAddress[${addressRandNo}]['vendor_business_state']" class="form-control"
                id="vendor_business_state"/>
            
        </div>

    </div>
</div>`);
    });

    $(document).on("click", '.remCF', function() {
      $(this).parent().parent().remove();
    });

    $(document).on("click", '.updateRemCF', function() {

      let otherAddressId = ($(this).attr("id")).split("_")[1];
      console.log(otherAddressId);

      $.ajax({
        url: 'ajaxs/ajax_other_address.php',
        data: {
          otherAddressId
        },
        type: 'POST',
        beforeSend: function() {
          // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
        },
        success: function(responseData) {
          // responseObj = JSON.parse(responseData);
          console.log(responseData);
          $(".removeID").html(responseData);
        }
      });


      $(this).parent().parent().remove();
    });



    $(document).on('change', '.vendor_bank_cancelled_cheque', function() {
      var file_data = $('.vendor_bank_cancelled_cheque').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_cancelled_cheque_upload.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.Ckecked_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".Ckecked_loder").toggleClass("disabled");
        },
        success: function(responseData) {
          $('.Ckecked_loder').html('<i class="fa fa-upload"></i>');
          $(".Ckecked_loder").toggleClass("enabled");
          responseObj = JSON.parse(responseData);
          console.log(responseObj);
          $("#vendor_bank_ifsc").val(responseObj["payload"]["cheque_details"]["ifsc"]["value"]);
          $("#account_number").val(responseObj["payload"]["cheque_details"]["acc no"]["value"]);
          $("#account_holder").val(responseObj["payload"]["cheque_details"]["acc holder"]["value"]);

          $("#vendor_bank_address").val(responseObj["payload"]["bank_details"]["ADDRESS"]);
          $("#vendor_bank_name").val(responseObj["payload"]["bank_details"]["BANK"]);
          $("#vendor_bank_branch").val(responseObj["payload"]["bank_details"]["BRANCH"]);
          var ifsc = responseObj["payload"]["cheque_details"]["ifsc"]["value"];
          var acc = responseObj["payload"]["cheque_details"]["acc no"]["value"];
          check_account(ifsc, acc);
        }
      });
    });


    $(document).on('click', '.vendor_bank_cancelled_cheque_btn', function() {
      var file_data = $('#invoiceFileInput').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_cancelled_cheque_upload.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.vendor_bank_cancelled_cheque_btn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".vendor_bank_cancelled_cheque_btn").toggleClass("disabled");
        },
        success: function(responseData) {
          responseObj = JSON.parse(responseData);
          //console.log(responseObj);
          $("#vendor_bank_ifsc").val(responseObj["payload"]["cheque_details"]["ifsc"]["value"]);
          $("#account_number").val(responseObj["payload"]["cheque_details"]["acc no"]["value"]);
          $("#account_holder").val(responseObj["payload"]["cheque_details"]["acc holder"]["value"]);

          $("#vendor_bank_address").val(responseObj["payload"]["bank_details"]["ADDRESS"]);
          $("#vendor_bank_name").val(responseObj["payload"]["bank_details"]["BANK"]);
          $("#vendor_bank_branch").val(responseObj["payload"]["bank_details"]["BRANCH"]);
          $('.vendor_bank_cancelled_cheque_btn').html('Upload');
          $(".vendor_bank_cancelled_cheque_btn").toggleClass("enabled");
          $('#checkUpload').hide();
          var ifsc = responseObj["payload"]["cheque_details"]["ifsc"]["value"];
          var acc = responseObj["payload"]["cheque_details"]["acc no"]["value"];
          check_account(ifsc, acc);
        }
      });
    });

    $(document).on('click', '.visiting_card_btn', function() {
      var file_data = $('#visitingFileInput').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_visiting_card.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.visiting_card_btn').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".visiting_card_btn").toggleClass("disabled");
        },
        success: function(responseData) {
          $('.visiting_card_btn').html('Submit');
          $(".visiting_card_btn").toggleClass("enabled");
          $("#visitingCard").css({
            "display": "none"
          });
          responseObj = JSON.parse(responseData);
          // console.log(responseObj);
          // $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
          // $("#vendor_authorised_person_designation").val('');
          // $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
          // $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);
          // $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
          // $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

          $("#adminName").val(responseObj["payload"]["ContactNames"]["value"][0]["content"]);
          $("#adminEmail").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          let designationArr = [];
          let jobTitle = responseObj["payload"]["JobTitles"]["value"][0]["content"] ?? "";
          let departments = responseObj["payload"]["Departments"]["value"][0]["content"] ?? "";
          if (jobTitle != "") {
            designationArr.push(jobTitle);
          }
          if (departments != "") {
            designationArr.push(departments);
          }
          $("#vendor_authorised_person_designation").val(designationArr.join(", "));
        }
      });
    });

    $(document).on('change', '.visiting_card', function() {
      var file_data = $('.visiting_card').prop('files')[0];
      var form_data = new FormData();
      form_data.append('file', file_data);
      // alert(form_data);
      $.ajax({
        url: 'ajaxs/ajax_visiting_card.php', // <-- point to server-side PHP script 
        dataType: 'text', // <-- what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        beforeSend: function() {
          $('.visiting_loder').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          $(".visiting_loder").toggleClass("disabled");
        },
        success: function(responseData) {
          $('.visiting_loder').html('<i class="fa fa-upload"></i>');
          $(".visiting_loder").toggleClass("enabled");
          responseObj = JSON.parse(responseData);
          // console.log(responseObj);
          // $("#adminName").val(responseObj["payload"]["ContactNames"]["value"]['0']["content"]);
          // $("#vendor_authorised_person_designation").val('');

          // $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"]['0']['value']);
          // $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"]['1']['value']);

          // $("#adminEmail").val(responseObj["payload"]["Emails"]["value"]['0']["content"]);
          // $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"]['1']["content"]);

          $("#adminName").val(responseObj["payload"]["ContactNames"]["value"][0]["content"]);
          $("#adminEmail").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          $("#adminPhone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#vendor_authorised_person_phone").val(responseObj["payload"]["WorkPhones"]["value"][0]['value']);
          $("#vendor_authorised_person_email").val(responseObj["payload"]["Emails"]["value"][0]["content"]);
          let designationArr = [];
          let jobTitle = responseObj["payload"]["JobTitles"]["value"][0]["content"] ?? "";
          let departments = responseObj["payload"]["Departments"]["value"][0]["content"] ?? "";
          if (jobTitle != "") {
            designationArr.push(jobTitle);
          }
          if (departments != "") {
            designationArr.push(departments);
          }
          $("#vendor_authorised_person_designation").val(designationArr.join(", "));

        }
      });
    });

    $(document).on("click", "#addOtherAddressBtn", function() {
      let vendor_idd = $('#vendor_idd').val();
      let vendor_business_flat_no_add = $('#vendor_business_flat_no_add').val();
      let vendor_business_pin_code_add = $('#vendor_business_pin_code_add').val();
      let vendor_business_district_add = $('#vendor_business_district_add').val();
      let vendor_business_location_add = $('#vendor_business_location_add').val();
      let vendor_business_building_no_add = $('#vendor_business_building_no_add').val();
      let vendor_business_street_name_add = $('#vendor_business_street_name_add').val();
      let vendor_business_city_add = $('#vendor_business_city_add').val();
      let vendor_business_state_add = $('#vendor_business_state_add').val();

      $.ajax({
        url: 'ajaxs/ajax_other_address_add.php',
        data: {
          vendor_id: vendor_idd,
          flatNo: vendor_business_flat_no_add,
          pinCode: vendor_business_pin_code_add,
          district: vendor_business_district_add,
          location: vendor_business_location_add,
          buildingNo: vendor_business_building_no_add,
          streetName: vendor_business_street_name_add,
          city: vendor_business_city_add,
          state: vendor_business_state_add
        },
        type: 'POST',
        beforeSend: function() {
          // $('.vendor_bank_cancelled_cheque').html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>');
          //$(".vendor_bank_cancelled_cheque").toggleClass("disabled");
        },
        success: function(responseData) {
          // responseObj = JSON.parse(responseData);
          // console.log(responseData);
          $(".insertOtherAddress").html(responseData);
          $(".otherAddressAddModal").modal('hide');
          // $("#addOtherForm").reset();
          $('#vendor_idd').val('');
          $('#vendor_business_flat_no_add').val('');
          $('#vendor_business_pin_code_add').val('');
          $('#vendor_business_district_add').val('');
          $('#vendor_business_location_add').val('');
          $('#vendor_business_building_no_add').val('');
          $('#vendor_business_street_name_add').val('');
          $('#vendor_business_city_add').val('');
          $('#vendor_business_state_add').val('');
        }
      });

    });

    $(document).keyup('.account_number', function() {
      //  alert(1);
      let acc = $('.account_number').val();
      // alert(acc);
      let ifsc = $('#vendor_bank_ifsc').val();
      // alert(ifsc);
      check_account(ifsc, acc);
    });

    $(document).keydown('.account_number', function() {
      //  alert(1);
      let acc = $('.account_number').val();
      // alert(acc);
      let ifsc = $('#vendor_bank_ifsc').val();
      // alert(ifsc);
      check_account(ifsc, acc);
    });




    $('#DataTables_Table_0')
      .dataTable({
        "responsive": true,
        "ajax": 'data.json'
      });


    $(document).on('click', `.vendorRegs`, function() {
      $('#add_frm').trigger("reset");

      let id = ($(this).attr('id')).split('_')[1];
      // console.log(id);

      $.ajax({
        type: "POST",
        url: `ajaxs/pr/ajax-vendor-getdata.php`,
        data: {
          id: id
        },
        beforeSend: function() {},
        success: function(response) {
          var proper_response = JSON.parse(response);
          $("#erp_v_id").val(id);
          $("#getGstinReturnFiledStatusBtn").val(proper_response.vendor_gst);
          $("#vendor_gstin").val(proper_response.vendor_gst);
          $("#vendor_pan").val(proper_response.vendor_pan);
          $("#trade_name").val(proper_response.vendor_tradename);
          $("#legal_name").val(proper_response.vendor_tradename);
          $("#con_business").val(proper_response.vendor_constofbusiness);
          $("#flat_no").val(proper_response.vendor_flatno);
          $("#build_no").val(proper_response.vendor_buildno);
          $("#street_name").val(proper_response.vendor_streetname);
          $("#location").val(proper_response.vendor_location);
          $("#city").val(proper_response.vendor_city);
          $("#pincode").val(proper_response.vendor_pin);
          $("#state").val(proper_response.vendor_state);
          $("#district").val(proper_response.vendor_district);
          $("#adminName").val(proper_response.vendor_name);
          $("#adminEmail").val(proper_response.vendor_email);
          $("#adminPhone").val(proper_response.vendor_phone);
          $("#account_holder").val(proper_response.vendor_tradename);
          console.log(proper_response);
        }
      });


    });

    $(document).on("click", ".add_data", function() {
      var data = this.value;
      $("#createdata").val(data);
      // confirm('Are you sure to Submit?')
      $("#add_frm").submit();
    });


  });
</script>



<script src="https://code.getmdl.io/1.2.0/material.min.js"></script>
<script>
  // *** multi step form *** //


  //DOM elements
  const DOMstrings = {
    stepsBtnClass: 'multisteps-form__progress-btn',
    stepsBtns: document.querySelectorAll(`.multisteps-form__progress-btn`),
    stepsBar: document.querySelector('.multisteps-form__progress'),
    stepsForm: document.querySelector('.multisteps-form__form'),
    stepsFormTextareas: document.querySelectorAll('.multisteps-form__textarea'),
    stepFormPanelClass: 'multisteps-form__panel',
    stepFormPanels: document.querySelectorAll('.multisteps-form__panel'),
    stepPrevBtnClass: 'js-btn-prev',
    stepNextBtnClass: 'js-btn-next'
  };


  //remove class from a set of items
  const removeClasses = (elemSet, className) => {

    elemSet.forEach(elem => {

      elem.classList.remove(className);

    });

  };

  //return exect parent node of the element
  const findParent = (elem, parentClass) => {

    let currentNode = elem;

    while (!currentNode.classList.contains(parentClass)) {
      currentNode = currentNode.parentNode;
    }

    return currentNode;

  };

  //get active button step number
  const getActiveStep = elem => {
    return Array.from(DOMstrings.stepsBtns).indexOf(elem);
  };

  //set all steps before clicked (and clicked too) to active
  const setActiveStep = activeStepNum => {

    //remove active state from all the state
    removeClasses(DOMstrings.stepsBtns, 'js-active');

    //set picked items to active
    DOMstrings.stepsBtns.forEach((elem, index) => {

      if (index <= activeStepNum) {
        elem.classList.add('js-active');
      }

    });
  };

  //get active panel
  const getActivePanel = () => {

    let activePanel;

    DOMstrings.stepFormPanels.forEach(elem => {

      if (elem.classList.contains('js-active')) {

        activePanel = elem;

      }

    });

    return activePanel;

  };

  //open active panel (and close unactive panels)
  const setActivePanel = activePanelNum => {

    //remove active class from all the panels
    removeClasses(DOMstrings.stepFormPanels, 'js-active');

    //show active panel
    DOMstrings.stepFormPanels.forEach((elem, index) => {
      if (index === activePanelNum) {

        elem.classList.add('js-active');

        setFormHeight(elem);

      }
    });

  };

  //set form height equal to current panel height
  const formHeight = activePanel => {

    const activePanelHeight = activePanel.offsetHeight;

    DOMstrings.stepsForm.style.height = `${activePanelHeight}px`;

  };

  const setFormHeight = () => {
    const activePanel = getActivePanel();

    formHeight(activePanel);
  };

  //STEPS BAR CLICK FUNCTION
  DOMstrings.stepsBar.addEventListener('click', e => {

    //check if click target is a step button
    const eventTarget = e.target;

    if (!eventTarget.classList.contains(`${DOMstrings.stepsBtnClass}`)) {
      return;
    }

    //get active button step number
    const activeStep = getActiveStep(eventTarget);

    //set all steps before clicked (and clicked too) to active
    setActiveStep(activeStep);

    //open active panel
    setActivePanel(activeStep);
  });

  //PREV/NEXT BTNS CLICK
  DOMstrings.stepsForm.addEventListener('click', e => {

    const eventTarget = e.target;

    //check if we clicked on `PREV` or NEXT` buttons
    if (!(eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`) || eventTarget.classList.contains(`${DOMstrings.stepNextBtnClass}`))) {
      return;
    }

    //find active panel
    const activePanel = findParent(eventTarget, `${DOMstrings.stepFormPanelClass}`);

    let activePanelNum = Array.from(DOMstrings.stepFormPanels).indexOf(activePanel);

    //set active step and active panel onclick
    if (eventTarget.classList.contains(`${DOMstrings.stepPrevBtnClass}`)) {
      activePanelNum--;

    } else {

      activePanelNum++;

    }

    setActiveStep(activePanelNum);
    setActivePanel(activePanelNum);

  });

  //SETTING PROPER FORM HEIGHT ONLOAD
  window.addEventListener('load', setFormHeight, false);

  //SETTING PROPER FORM HEIGHT ONRESIZE
  window.addEventListener('resize', setFormHeight, false);

  //changing animation via animation select !!!YOU DON'T NEED THIS CODE (if you want to change animation type, just change form panels data-attr)

  const setAnimationType = newType => {
    DOMstrings.stepFormPanels.forEach(elem => {
      elem.dataset.animation = newType;
    });
  };

  //selector onchange - changing animation
  const animationSelect = document.querySelector('.pick-animation__select');

  animationSelect.addEventListener('change', () => {
    const newAnimationType = animationSelect.value;

    setAnimationType(newAnimationType);
  });
</script>


<script>
  function backButton() {

    window.history.back();

  }
</script>


<?php







require_once("../common/footer.php");







?>