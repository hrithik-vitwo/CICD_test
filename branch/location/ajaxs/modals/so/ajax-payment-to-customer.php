<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-grn-controller.php");

$headerData = array('Content-Type: application/json');
$grnObj = new GrnController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $addCollectPayment = $grnObj->insertVendorPayment($_POST, $_FILES);
    echo  json_encode($addCollectPayment);
}
