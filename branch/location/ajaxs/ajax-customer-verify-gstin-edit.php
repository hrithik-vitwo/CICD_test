<?php
require_once("../../../app/v1/connection-branch-admin.php");
$returnData[] = '';



function isCustomerExist($GSTIN = null)
{
  global $company_id;
  $check = queryGet("SELECT * FROM `" . ERP_CUSTOMER . "`  WHERE company_id=$company_id AND `customer_gstin`='" . $GSTIN . "'");

  if ($check['numRows'] >= 1) {

    return true;
  } else {
    return false;
    //exit(); 
  }
}

if (isset($_GET["gstin"]) && !empty($_GET["gstin"])) {

  if (isCustomerExist($_GET["gstin"])) {
    // echo "Customer already exists!";
    //console($check);
    swalAlert("warning", "Opps!", "Customer already exists!", LOCATION_URL . "manage-customers.php?create");
  } else {
    $customer_code = getRandCodeNotInTable(ERP_CUSTOMER, 'customer_code');
    if ($customer_code['status'] == 'success') {
      $customer_code = $customer_code['data'];
    } else {
      $customer_code = '';
    }



    if (isset($_GET["gstin"]) && !empty($_GET["gstin"])) {
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.mastergst.com/public/search?email=developer@vitwo.in&gstin=' . $_GET["gstin"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'client_id: GSPfc3af0aa-1ae5-45be-8f6f-34d0baa63594',
          'client_secret: GSP6e50f5dd-7787-4c7d-a576-a5f8d9ffe5f6',
          'Accept: application/json'
        ),
      ));

      $resultGst = curl_exec($curl);
      //console($resultGst);
      //exit();
      try {
        $resultGstData = json_decode($resultGst, true);
        if (isset($resultGstData["data"]) && count($resultGstData["data"]) > 0) {
          $gstDetails = $resultGstData["data"];

          $gstStatus = $resultGstData["data"]["sts"];
          $gstRegDate = $resultGstData["data"]["rgdt"];
          $legal_name = $resultGstData['data']['lgnm'];
          $gstLastUpdate = $resultGstData["data"]["lstupdt"];

          $customerPan = substr($_GET["gstin"], 2, 10);
          $othersaddress_count = count($resultGstData['data']['adadr']);
          if (empty($gstDetails['pradr']['addr']['city'])) {
            $city =  $gstDetails['pradr']['addr']['loc'];
          } else {
            $city = $gstDetails['pradr']['addr']['city'];
          }
           $customer_name = $gstDetails['tradeNam'] ?? $gstDetails['lgnm'];
          // return $resultGstData;

          $state_code = substr($_GET["gstin"], 0, 2);
          $state_sql = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode` = $state_code");
          $state_data = $state_sql['data'];


          $returnData['status'] = $gstStatus;
          $returnData['customer_name'] = $customer_name;
          $returnData['legal_name'] = $legal_name;
          $returnData['city'] = $city;
          $returnData['customerPan'] = $customerPan;
          $returnData['ctb'] = $gstDetails['ctb'];
          $returnData ['statename'] = $state_sql['data']['gstStateName'];
          $returnData['district'] = $gstDetails['pradr']['addr']['dst'];
          $returnData['loc'] = $gstDetails['pradr']['addr']['loc'];
          $returnData['flno'] = $gstDetails['pradr']['addr']['flno'];
          $returnData['st'] = $gstDetails['pradr']['addr']['st'];
          $returnData['pncd'] = $gstDetails['pradr']['addr']['pncd'];
          $returnData['country'] = 'India';
           echo json_encode($returnData);

        //  echo json_encode($resultGst);
?>


<?php
        } else {
          swalToast("warning", "Something went wrong try again!");
        }
      } catch (Exception $ee) {
        swalToast("warning", "Something went wrong try again!");
      }
    } else {
      swalToast("warning", "Please provide valid gstin number!");
    }
    curl_close($curl);
  }
}
