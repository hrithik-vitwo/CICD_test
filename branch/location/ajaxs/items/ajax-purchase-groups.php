<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$purchaseObj = new GoodsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //POST REQUEST
    $_POST["companyId"] = $companyID;

    $createNewPurchaseGroupObj = $purchaseObj->createPurchaseGroup($_POST);

    $responseData = $createNewPurchaseGroupObj;

    if($responseData["status"]=="success") {
        $getAllPurchaseGroupObj = $purchaseObj->getAllPurchaseGroups();
        
        if($getAllPurchaseGroupObj["status"]=="success"){
            $purchaseTypeList = $getAllPurchaseGroupObj["data"];
            $numPurchaseTypes = count($purchaseTypeList);
            echo '<option value="">Select Purchase Group</option>';
            for($i=0; $i<$numPurchaseTypes; $i++){
                $onePurchaseGroup = $purchaseTypeList[$i];
                if($i == $numPurchaseTypes-1){
                    echo '<option selected value="'.$onePurchaseGroup["purchaseGroupId"].'">'.$onePurchaseGroup["purchaseGroupName"].'</option>';
                }else{
                    echo '<option value="'.$onePurchaseGroup["purchaseGroupId"].'">'.$onePurchaseGroup["purchaseGroupName"].'</option>';
                }
            }
        }else{
            echo '<option value="">Select Purchase Group</option>';
        }
    }

}
elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST   

    $getAllPurchaseGroupObj = $purchaseObj->getAllPurchaseGroups();

    
    if($getAllPurchaseGroupObj["status"]=="success"){
        echo '<option value="">Select Purchase Group</option>';
        foreach($getAllPurchaseGroupObj["data"] as $onePurchaseGroup){
            ?>
                <option value="<?= $onePurchaseGroup["purchaseGroupId"] ?>"><?= $onePurchaseGroup["purchaseGroupName"] ?></option>
            <?php
        }
    }else{
        echo '<option value="">Select Purchase Group</option>';
    }

}else{
    echo "Something wrong, try again!";
}