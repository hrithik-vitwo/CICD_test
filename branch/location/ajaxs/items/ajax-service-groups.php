<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
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
            echo '<option value="" data-goodType="">Goods Type</option>';
            for($i=0; $i<$numGoodTypes; $i++){
                $oneGoodType = $goodTypeList[$i];
                if($i == $numGoodTypes-1){
                    echo '<option selected value="'.$oneGoodType["goodTypeId"].'" data-goodType="'.$oneGoodType["type"].'">'.$oneGoodType["goodTypeName"].'</option>';
                }else{
                    echo '<option value="'.$oneGoodType["goodTypeId"].'" data-goodType="'.$oneGoodType["type"].'">'.$oneGoodType["goodTypeName"].'</option>';
                }
            }
        }else{
            echo '<option value="" data-goodType="">Goods Type</option>';
        }
    }



}elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
    $getAllServiceGroup = $goodsObj->getAllServiceGroup();
    
    if($getAllServiceGroup["status"]=="success"){
        echo '<option value="" data-goodType="non">Service Group</option>';
        foreach($getAllServiceGroup["data"] as $oneGoodType){
            ?>
                <option value="<?= $oneGoodType["serviceGroupId"] ?>" data-goodType=""><?= $oneGoodType["serviceGroupName"] ?></option>
            <?php
        }
    }else{
        echo '<option value="" data-goodType="non">Service Group</option>';
    }

}else{
    echo "Something wrong, try again!";
}