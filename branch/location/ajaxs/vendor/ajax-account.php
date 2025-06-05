<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


 $ifsc = $_POST['ifsc'];
 $acc = $_POST['acc'];

 $check = queryGet("SELECT * FROM `erp_vendor_bank_details` as bank LEFT JOIN `erp_vendor_details` as vendor ON vendor.vendor_id=bank.vendor_id WHERE bank.vendor_bank_ifsc='".$ifsc."' AND bank.vendor_bank_account_no= '".$acc."' AND `company_id`=$company_id ");
 
echo $check['numRows'];






}
?>