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

    $createNewGoodGroupObj = $goodsObj->createGoodGroup($_POST);
 // echo $createNewGoodGroupObj;
    $responseData = $createNewGoodGroupObj;

    if($responseData["status"]=="success") {
        $getAllGoodGroupObj = $goodsObj->getAllGoodGroups($_POST['goodType_id']);
        //console($getAllGoodGroupObj);
        if($getAllGoodGroupObj["status"]=="success"){
            $goodTypeList = $getAllGoodGroupObj["data"];
            $numGoodTypes = count($goodTypeList);
            echo '<option value="">Select Group</option>';
            for($i=0; $i<$numGoodTypes; $i++){
                $oneGoodGroup = $goodTypeList[$i];
                if($i == $numGoodTypes-1){
                    echo '<option selected value="'.$oneGoodGroup["goodGroupId"].'">'.$oneGoodGroup["goodGroupName"].'</option>';
                }else{
                    echo '<option value="'.$oneGoodGroup["goodGroupId"].'">'.$oneGoodGroup["goodGroupName"].'</option>';
                }
            }
        }else{
            echo '<option value="">Select Group</option>';
        }
    }else{
            echo '<option value="">Select Group</option>';
    }



}elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
    $type = $_GET['typeId'];
    $getAllGoodGroupObj = $goodsObj->getAllGoodGroups($type);
   // console($getAllGoodGroupObj);
    
    if($getAllGoodGroupObj["status"]=="success"){
        echo '<option value="">Select Group</option>';
        foreach($getAllGoodGroupObj["data"] as $oneGoodGroup){
           // console($oneGoodGroup);
            ?>
            
                <option value="<?= $oneGoodGroup["goodGroupId"] ?>"><?= $oneGoodGroup["goodGroupName"] ?></option>
             
            <?php
        }
    }else{
        echo '<option value="">Select Group</option>';
    }

}else{
    echo "Something wrong, try again!";
}