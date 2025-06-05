<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


 $ifsc = $_POST['ifsc'];
 $acc = $_POST['acc'];
 $vendor_id = $_POST['vendor_id'];

  $check = queryGet("SELECT * FROM `erp_vendor_bank_details` as bank LEFT JOIN `erp_vendor_details` as vendor ON vendor.vendor_id=bank.vendor_id WHERE bank.vendor_bank_ifsc='".$ifsc."' AND bank.vendor_bank_account_no= '".$acc."' AND `company_id`=$company_id AND bank.`vendor_id` != $vendor_id");
 console($check);
echo $check['numRows'];






}
?>