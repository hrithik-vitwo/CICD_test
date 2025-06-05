<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new GoodsController();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
    $getAllItemsObj = $ItemsObj->getAllGoodGroups();

    if ($getAllItemsObj["status"] == "success") {
        echo '<option value="">Items </option>';
        foreach ($getAllItemsObj["data"] as $oneGoodType) {
        ?>
            <option value="<?= $oneGoodType["goodGroupId"] ?>"><?= $oneGoodType["goodGroupName"] ?></option>
        <?php
        }
    } else {
        echo '<option value="">Items </option>';
    }
} else {
    echo "Something wrong, try again!";
} 
?>