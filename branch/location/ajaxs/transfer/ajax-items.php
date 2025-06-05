<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //POST REQUEST
    $_POST["companyId"] = 12;

    $createNewGoodTypeObj = $ItemsObj->createItems($_POST);

    if ($createNewGoodTypeObj["status"] == "success") {

        $getAllItemsObj = $ItemsObj->getAllItemsPo();

        if ($getAllItemsObj["status"] == "success") {
            $goodTypeList = $getAllItemsObj["data"];
            $numItems = count($goodTypeList);
            echo '<option value="">Items</option>';
            for ($i = 0; $i < $numItems; $i++) {
                $oneGoodType = $goodTypeList[$i];
                if ($i == $numItems - 1) {
                    echo '<option selected value="' . $oneGoodType["goodTypeId"] . '">' . $oneGoodType["goodTypeName"] . '</option>';
                } else {
                    echo '<option value="' . $oneGoodType["goodTypeId"] . '">' . $oneGoodType["goodTypeName"] . '</option>';
                }
            }
        } else {
            echo '<option value="">Items</option>';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
 
    $itemId = $_GET['itemId'];
    $getAllItemsObj = $ItemsObj->getAllItemsByGroupId($itemId);

    if ($getAllItemsObj["status"] == "success") {
        echo '<option value="">Items </option>';
        foreach ($getAllItemsObj["data"] as $oneGoodType) {
        ?>
            <option value="<?= $oneGoodType["itemId"] ?>"><?= $oneGoodType["itemName"] ?></option>
        <?php
        }
    } else {
        echo '<option value="">Items </option>';
    }
} else {
    echo "Something wrong, try again!";
} 
?>