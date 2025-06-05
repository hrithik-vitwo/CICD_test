<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //POST REQUEST
    $_POST["companyId"] = $companyID;
    
    $createNewGoodTypeObj = $goodsObj->createGoodTypes($_POST);

    if($createNewGoodTypeObj["status"]=="success") {
        
        $getAllGoodTypesObj = $goodsObj->getAllGoodTypes();
        
        if($getAllGoodTypesObj["status"]=="success"){
            $goodTypeList = $getAllGoodTypesObj["data"];
            $numGoodTypes = count($goodTypeList);
            echo '<option value="" data-goodType="">Select Item Type</option>';
            for($i=0; $i<$numGoodTypes; $i++){
                $oneGoodType = $goodTypeList[$i];
                if($i == $numGoodTypes-1){
                    echo '<option selected value="'.$oneGoodType["goodTypeId"].'" data-goodType="'.$oneGoodType["type"].'">'.$oneGoodType["goodTypeName"].'</option>';
                }else{
                    echo '<option value="'.$oneGoodType["goodTypeId"].'" data-goodType="'.$oneGoodType["type"].'">'.$oneGoodType["goodTypeName"].'</option>';
                }
            }
        }else{
            echo '<option value="" data-goodType="">Select Item Type</option>';
        }
    }



}elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
    $getAllGoodTypesObj = $goodsObj->getAllGoodTypes();
    
    if($getAllGoodTypesObj["status"]=="success"){
        echo '<option value="" data-goodType="non">Select Item Type</option>';
        foreach($getAllGoodTypesObj["data"] as $oneGoodType){
            ?>
                <option value="<?= $oneGoodType["goodTypeId"] ?>" data-goodType="<?= $oneGoodType["type"]?>"><?= $oneGoodType["goodTypeName"] ?></option>
            <?php
        }
    }else{
        echo '<option value="" data-goodType="non">Select Item Type</option>';
    }

}else{
    echo "Something wrong, try again!";
}