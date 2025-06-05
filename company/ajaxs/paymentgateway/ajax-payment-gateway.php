<?php
include_once("../../../app/v1/connection-company-admin.php");
$created_by;
$updated_by;

if ($_POST['act'] == 'addPayment') {

  $bankID = $_POST['bankID'];
  $gatewaytype = $_POST['gatewaytype'];
  $accessToken = $_POST['accessToken'];
  $accessKey = $_POST['accessKey'];
  $urlType = $_POST['urltype'];
  $environmentType = $_POST['environmentType'];

  $ins = "INSERT INTO `erp_payment_gateway` SET `bank_id`=$bankID, `getway_type`='$gatewaytype', `access_token`='$accessToken', `access_key`='$accessKey', `url_type`='".$urlType."', `environment`='".$environmentType."', `status`='active', `branch_id`=1, `location_id`=1,`company_id`=".$company_id.", created_by='$created_by', updated_by='$updated_by'";
  $data = queryInsert($ins);

  echo JSON_encode($data);
}
elseif ($_POST['act'] == 'updatePayment') {
  $payment_gateway_id = $_POST['payment_gateway_id'];
  $bankID = $_POST['bankID'];
  $gatewaytype = $_POST['gatewaytype'];
  $accessToken = $_POST['accessToken'];
  $accessKey = $_POST['accessKey'];
  $urlType = $_POST['urltype'];
  $environmentType = $_POST['environmentType'];

  $ins = "UPDATE `erp_payment_gateway` SET `bank_id`=$bankID, `getway_type`='$gatewaytype', `access_token`='$accessToken', `access_key`='$accessKey', `url_type`='".$urlType."', `environment`='".$environmentType."', `status`='active', `branch_id`=1, `location_id`=1,`company_id`=".$company_id.", created_by='$created_by', updated_by='$updated_by' WHERE `payment_gateway_id`=" . $payment_gateway_id;
  $data = queryUpdate($ins);

  echo JSON_encode($data);
}

?>
