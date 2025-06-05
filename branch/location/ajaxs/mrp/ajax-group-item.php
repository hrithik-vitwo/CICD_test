<?php
require_once("../../../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
 //   console($_GET);

    $type = $_GET['type'];

    
    $getAllItemsObj = queryGet("SELECT * FROM `erp_inventory_items` WHERE `goodsGroup` = $type",true);
   // console($getAllItemsObj);

    if ($getAllItemsObj["status"] == "success") {
        echo '<option value="">Items </option>';
        foreach ($getAllItemsObj["data"] as $oneGoodType) {
        ?> 
            <option value="<?= $oneGoodType["itemId"] ?>">[<?= $oneGoodType["itemCode"]?>]<?= $oneGoodType["itemName"] ?></option>
        <?php
        }
    } else {
        echo '<option value="">Items</option>';
    }

}


?>