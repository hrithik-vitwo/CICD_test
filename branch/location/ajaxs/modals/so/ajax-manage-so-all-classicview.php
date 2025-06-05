<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/admin/func-company.php");
require_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../../app/v1/functions/common/templates/template-sales-order.controller.php");
$headerData = array('Content-Type: application/json');


$templateSalesOrderControllerObj = new TemplateSalesOrderController();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $so_id = base64_decode($_GET['so_id']);
    // $templateSalesOrderControllerObj->printSalesOrder($so_id);
    echo $templateSalesOrderControllerObj->printSalesOrder($so_id);
   
}