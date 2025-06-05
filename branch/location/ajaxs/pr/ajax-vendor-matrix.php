<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-SendEmailToRFQvendor.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!isset($_POST['rate']) || $_POST['rate'] == "") {
    $_POST['rate'] = 0;
  }

  if (!isset($_POST['moq']) || $_POST['moq'] == "") {
    $_POST['moq'] = 0;
  }

  if (!isset($_POST['lead']) || $_POST['lead'] == "") {
    $_POST['lead'] = 0;
  }

  // print_r($_POST);
  $rfq = $_POST['rfq'];

  $vendor_query = "SELECT * FROM erp_vendor_response WHERE rfqId = '$rfq'";

  $dataset = queryGet($vendor_query, true);


  $closing_date_query = "SELECT * FROM erp_rfq_list WHERE rfqId = '$rfq'";
      $closing_date_data = queryGet($closing_date_query, false);
      $closing_date = $closing_date_data["data"]["closing_date"];


      $required_date_query = "SELECT * FROM erp_rfq_list LEFT JOIN erp_branch_purchase_request ON erp_rfq_list.prId = erp_branch_purchase_request.purchaseRequestId WHERE erp_rfq_list.rfqId = '$rfq'";
      $required_date_data = queryGet($required_date_query, false);
      $required_date = $required_date_data["data"]["expectedDate"];

      $closing_date_plus_x = date('Y-m-d', strtotime($closing_date . ' +1 day'));
      $date1 = new DateTime($closing_date_plus_x);
      $date2 = new DateTime($required_date);
      $interval = $date2->diff($date1);


      $expected_lead_time = ($interval->days)+1;


  //   print_r($dataset['data']);

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

  while ($i < count($moq_array) && $j < count($rate_array) && $k < count($lead_time_array) && $v < count($vendor_array) && $vi < count($vendor_id_array)  && $vc < count($vendor_code_array)) {
    $moq_value = (10 * $moq_array[$i++]) / $min_moq_array;
    $rate_value = (10 * $min_rate_array) / $rate_array[$j++];

    if($lead_time_array[$k] < $expected_lead_time)
    {
      $lead_time_value = 10;
    }
    else{
      $lead_time_value = (10 * $min_lead_time_array) / $lead_time_array[$k];
    }


    // $lead_time_value = (10 * $min_lead_time_array) / $lead_time_array[$k++];

    $rate_weight = $_POST['rate'];
    $rate_weight_array = array("value" => $rate_weight * $rate_value, "title" => "Rate Weight");
    $moq_weight = $_POST['moq'];
    $moq_weight_array = array("value" => $moq_weight * $moq_value, "title" => "MOQ Weight");
    $lead_time_weight = $_POST['lead'];
    $lead_time_weight_array = array("value" => $lead_time_weight * $lead_time_value, "title" => "LEAD TIME Weight");
    $pie = array($rate_weight_array, $moq_weight_array, $lead_time_weight_array);
    $sum[$l] = $moq_weight * $moq_value + $rate_weight * $rate_value + $lead_time_weight * $lead_time_value;
    $vendor_array_1[] = array("vendor_id" => $vendor_id_array[$vi++], "vendor_code" => $vendor_code_array[$vc++], "vendor" => $vendor_array[$v++], "units" => $sum[$l], "pie" => $pie);
    $l++;
    $k++;
  }

  // krsort($vendor_array_1);
  usort($vendor_array_1, function ($item1, $item2) {
    return $item2['units'] <=> $item1['units'];
  });

  $response_array = array();
  $response_array["graph"] = $vendor_array_1;

  $item_result = "";

  foreach ($vendor_array_1 as $vd) {

    $item_result .="<form action='manage-purchases-orders.php' method='POST' id='' name='submitPoForm'>
                                <input type='hidden' name='erp_v_id' value='" . $vd['vendor_id'] . "'>
                                <input type='hidden' name='date' value='" . $_POST['date'] . "'>
                                <div class='accordion-item' id='html_data'>
                                  <h2 class='accordion-header' id='flush-headingOne'>
                                    <button class='accordion-button btn btn-primary collapsed mt-0 mb-0' type='button' data-bs-toggle='collapse' data-bs-target='#flush-collapse" . $vd['vendor_id'] . "' aria-expanded='true' aria-controls='flush-collapse'>
                                      <p class='vendor-name'>" . $vd['vendor'] . "</p>
                                    </button>";

    if (isset($vd['vendor_code']) && $vd['vendor_code'] != "") {
      $item_result .=               "<div class='create-po-btn btn-create-po" . $vd['vendor_id'] . "'>
                                      <button class='btn btn-primary button-create-PO m-0' type='submit' id='rfq_po' name='rfq_po'>Create PO</button>
                                    </div>";
    }
    else
    {
        $item_result .=               "<div class='create-po-btn btn-create-po".$vd['vendor_id']."'>
                                      <button type='button' class='btn btn-primary button-create-PO m-0 vendorRegs' id='vendorRegs_".$vd['vendor_id']."' data-toggle='modal' data-target='#exampleModal'>Register</button>
                                    </div>";
    }
      $item_result .=             "</h2>
                                  <div id='flush-collapse" . $vd['vendor_id'] . "' class='accordion-collapse collapse show' aria-labelledby='flush-headingOne' data-bs-parent='#accordionFlushExample'>
                                    <div class='accordion-body vendor-name-accordion-body p-0'>
                                          <table class='table defaultDataTable table-hover table-nowrap'>
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
                                            <tbody>";

                                            $id = $vd['vendor_id'];
                                            $items_list = "SELECT * FROM erp_vendor_item WHERE `erp_v_id` = '$id'";
                                            $item_dataset = queryGet($items_list, true);
                                            foreach ($item_dataset['data'] as $item) {

                                                $item_result .=                        "<tr>
                                                                                        <td>" . $item['item_name'] . "</td>
                                                                                        <td>" . $item['moq'] . "</td>
                                                                                        <td>" . $item['price'] . "</td>
                                                                                        <td>" . round($item['total'], 2) . "</td>";
                                                                                        if ($item['delivery_mode'] == 1) {

                                                                                          $item_result .= " <td> EX WORK </td>";
                                                                                        } elseif ($item['delivery_mode'] == 2) {

                                                                                          $item_result .= "<td> FOR </td>";
                                                                                        } elseif ($item['delivery_mode'] == 3) {

                                                                                          $item_result .= "<td> FOB </td>";
                                                                                        } elseif ($item['delivery_mode'] == 4) {

                                                                                          $item_result .= "<td> CIF </td>";
                                                                                        }
                                                                      $item_result .="<td>" . $item['lead_time'] . "</td>
                                                                                      <td>
                                                                                        <input type='text' class='form-control input-matrix matrix-class-" . $vd['vendor_id'] . "' name='items[" . $item['item_id'] . "][item_qty]' value='0'>
                                                                                        <input type='hidden' name='items[" . $item['item_id'] . "][price]' value='" . $item['price'] . "'>
                                                                                        <input type='hidden' name='items[" . $item['item_id'] . "][moq]' value='" . $item['moq'] . "'>
                                                                                        <input type='hidden' name='items[" . $item['item_id'] . "][delivery_mode]' value='" . $item['delivery_mode'] . "'>
                                                                                      </td>
                                                                                    </tr>";
                                            }

    $item_result .="                    </tbody>
                                      </table>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </form>";


    $response_array["item"] = $item_result;
  }
  
  $rate_value = $_POST['rate'];
  $moq_value = $_POST['moq'];
  $lead_value = $_POST['lead'];
  
  $response_array["rate_percent_id"] = round(($rate_value/($rate_value+$moq_value+$lead_value))*100,2);
  $response_array["moq_percent_id"] = round(($moq_value/($rate_value+$moq_value+$lead_value))*100,2);
  $response_array["lead_percent_id"] = round(($lead_value/($rate_value+$moq_value+$lead_value))*100,2);
  
  
  echo json_encode($response_array);

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

  if (isset($_GET["id"]) && $_GET["id"] != "") {
    $id = $_GET["id"];
    $result = "<ul>";
    // echo json_decode($id);
    $items_list = "SELECT * FROM erp_vendor_item WHERE `erp_v_id` = '$id'";
    $item_dataset = queryGet($items_list, true);
    foreach ($item_dataset['data'] as $item) {

      ?>

      <li><b>ITEM NAME - </b> <?= $item['item_name'] ?></li>
      <li><b>ITEM MOQ - </b> <?= $item['moq'] ?></li>
      <li><b>ITEM RATE - </b> <?= $item['price'] ?></li>
      <li><b>ITEM TOTAL PRICE - </b> <?= $item['total'] ?></li>
      <?php
      if ($item['delivery_mode'] == 1) {
        echo "<li><b>DELIVERY MODE - </b> EX WORK </li>";
      } elseif ($item['delivery_mode'] == 2) {
        echo "<li><b>DELIVERY MODE - </b> FOR </li>";
      } elseif ($item['delivery_mode'] == 3) {
        echo "<li><b>DELIVERY MODE - </b> FOB </li>";
      } elseif ($item['delivery_mode'] == 4) {
        echo "<li><b>DELIVERY MODE - </b> CIF </li>";
      }
      ?>

      <li><b>LEAD TIME - </b> <?= $item['lead_time'] ?></li>

      <?php
    }
    $result .= "</ul>";

    echo json_encode($result);
    // echo $result;

  }
}

?>