<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
// require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once(BASE_DIR . "app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-discount-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_GET['act'] === "getTaxComponent") {

  // console($_GET);

  $companyCountry = $_GET['country_id'];
  $bilingId = $_GET['bilingId'];
  $shippingId = $_GET['shippingId'];


  $getItemTaxRule = getItemTaxRule($companyCountry, $bilingId, $shippingId);
  $json_data = $getItemTaxRule['data'];

  $data = json_decode($json_data, true);
  // console($data);


  foreach ($data['tax'] as $tax) {
    echo $tr =  '<tr class="gst tax_amount">
                  <td colspan="5" class="text-right totalCal">' . $tax['taxComponentName'] . '</td>
                  <td colspan="2" class="text-right" id=' . $tax['taxComponentName'] . '></td>
                  <input type="hidden" class="taxPer"  id="taxPer_' . $tax['taxComponentName'] . '" value="' . $tax['taxPercentage'] . '">
                  <input type="hidden" name="' . htmlspecialchars($tax['taxComponentName']) . '" id="hidden_' . htmlspecialchars($tax['taxComponentName']) . '" value="">
                  <input type="hidden" name="grandTaxAmtval_' . $tax['taxComponentName'] . '" id="grandTaxAmtval_' . $tax['taxComponentName'] . '" value="0.00">
                 </tr>';
  }
}
