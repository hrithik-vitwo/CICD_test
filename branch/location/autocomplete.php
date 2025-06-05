<?php
require_once("../../app/v1/connection-branch-admin.php");

administratorLocationAuth();

$q = $_GET['q'];
$my_data = mysql_real_escape_string($q);
$sql = queryGet("SELECT name FROM `erp_inventory_items` WHERE `itemName` LIKE '%$my_data%' ORDER BY itemName", true);

if ($sql['status'] == "success") {
    foreach ($sql['data'] as $data)
        echo $data['itemName'];
}
