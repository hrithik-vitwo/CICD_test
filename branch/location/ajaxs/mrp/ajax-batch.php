<?php
include_once("../../../../app/v1/connection-branch-admin.php");


$headerData = array('Content-Type: application/json');
$responseData = [];


if ($_SERVER['REQUEST_METHOD'] === 'GET') { 

// console($_GET);
$itemId = $_GET['itemId'];

$stock_log = queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE `itemId` = $itemId AND `locationId` = $location_id",true);
// console($stock_log);

foreach($stock_log['data'] as $stock_log){
    echo '<option value="'.$stock_log['logRef'].'">'.$stock_log['logRef'].'</option>';

}

}


?>