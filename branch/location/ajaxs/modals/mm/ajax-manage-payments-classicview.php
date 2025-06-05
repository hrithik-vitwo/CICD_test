<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/common/templates/template-manage-payment.php");
$headerData = array('Content-Type: application/json');


$templatepaymentObj = new TemplatePayment();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $pay_id = base64_decode($_GET['pay_id']);
    echo $templatepaymentObj->printManagePayment($pay_id);
   
}