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
  $branchGstinCode = $_GET['loc_state'];
  $vendorGstinCode = $_GET['vendor_state'];
  $colspanValue=$_GET['colspanValue'];


  $getItemTaxRule = getItemTaxRule($companyCountry, $branchGstinCode, $vendorGstinCode);
  $json_data = $getItemTaxRule['data'];

  $data = json_decode($json_data, true);
  // console($data);

  foreach ($data['tax'] as $tax) {
    echo $tr = '
   <tr class="p-2 gst1 ' . $tax['taxComponentName'] . '"tr"  id="' . $tax['taxComponentName'] . '"Col">
   <td colspan="'.$colspanValue.'" class="text-right p-2" style="border: none; background: none;"> </td>
                    
                      <td colspan="0" class="text-left p-2 totalCal" style="border: none; background: none;padding: 0px !important;">' . $tax['taxComponentName'] . '</td>
                      <input type="hidden" id="taxPer_' . $tax['taxComponentName'] . '" value="' . $tax['taxPercentage'] . '">
                      
                     
                      <td class="text-right pr-2" style="border: none; background: none;">
                        <small class="text-large font-weight-bold text-success">
                          <span class="rupee-symbol"></span><span  id="grandTaxAmt_' . $tax['taxComponentName'] . '">0.00</span>
                          <input type="hidden" name="grandTaxAmtval_' . $tax['taxComponentName'] . '" id="grandTaxAmtval_' . $tax['taxComponentName'] . '" value="0.00">
                        </small>
                        
                      </td>
                      </tr>
   
   ';
  }
}
