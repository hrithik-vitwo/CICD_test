<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();

//console($getItemObj = $BranchSoObj->fetchItemSummaryDetails(69)['data'][0]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //POST REQUEST
    $_POST["companyId"] = 12;

    $createNewGoodTypeObj = $ItemsObj->createItems($_POST);

    if ($createNewGoodTypeObj["status"] == "success") {

        $getAllItemsObj = $ItemsObj->getAllItems();

        if ($getAllItemsObj["status"] == "success") {
            $goodTypeList = $getAllItemsObj["data"];
            $numItems = count($goodTypeList);
            echo '<option value="">Items Type</option>';
            for ($i = 0; $i < $numItems; $i++) {
                $oneGoodType = $goodTypeList[$i];
                if ($i == $numItems - 1) {
                    echo '<option selected value="' . $oneGoodType["goodTypeId"] . '">' . $oneGoodType["goodTypeName"] . '</option>';
                } else {
                    echo '<option value="' . $oneGoodType["goodTypeId"] . '">' . $oneGoodType["goodTypeName"] . '</option>';
                }
            }
        } else {
            echo '<option value="">Items Type</option>';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // console("imranali59059");
    $getServicesObj = $BranchSoObj->fetchServicesList();
    console($getServicesObj);
     if ($getServicesObj["status"] == "success") {     
   		 $services=$getServicesObj['data'];
        echo '<option value="">Services Type</option>';
        foreach ($services as $service) {
        ?>
            <option value="<?= $service["itemId"] ?>"><?= $service['itemName'] ?><small>(<?= $service['itemCode'] ?>)</small></option>
        <?php
        }
     } else {
         echo '<option value="">Services Type</option>';
     }
} else {
    echo "Something wrong, try again!";
} 
?>