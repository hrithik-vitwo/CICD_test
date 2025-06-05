<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_POST['act'] === "batchCheck") {
    $batchNumber=$_POST['batchNumber'];
    
   
    $responseData=queryGet("SELECT logRef, DATE_FORMAT(bornDate, '%Y-%m-%d') AS bornDate FROM erp_inventory_stocks_log WHERE logRef = '".$batchNumber."' LIMIT 1");
    // print_r('$rowData');
    // print_r($rowData);
}
echo json_encode($responseData);
?>