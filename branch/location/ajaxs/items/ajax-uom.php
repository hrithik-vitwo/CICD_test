<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController();




 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     //POST REQUEST

//console($_POST);
  
// echo "ok";
  $createNewGoodGroupObj = $goodsObj->addUOM($_POST);

//   console($createNewGoodGroupObj);
 
   // $responseData = $createNewGoodGroupObj;

    if($createNewGoodGroupObj["status"]=="success") {
        $getAllGoodGroupObj = $goodsObj->getUOM();
    //    console($getAllGoodGroupObj);
        if($getAllGoodGroupObj["status"]=="success"){
            $goodTypeList = $getAllGoodGroupObj["data"];
            $numGoodTypes = count($goodTypeList);
          //  echo '<option value="">Select UOM</option>';
            for($i=0; $i<$numGoodTypes; $i++){
                $oneGoodGroup = $goodTypeList[$i];
                if($i == $numGoodTypes-1){
                    echo '<option selected value="'.$oneGoodGroup["uomId"].'">'.$oneGoodGroup["uomName"].'||'.$oneGoodGroup["uomDesc"].'</option>';
                }else{
                    echo '<option value="'.$oneGoodGroup["uomId"].'">'.$oneGoodGroup["uomName"].'||'.$oneGoodGroup["uomDesc"].'</option>';
                }
            }
        }else{
            //echo 1;
            echo '<option value="">Select UOM</option>';
        }
    }else{
       // echo 2;
            echo '<option value="">Select UOM</option>';
    }



 }
elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
//     //GET REQUEST   

//     $getAllPurchaseGroupObj = $purchaseObj->getUOM(); 

    
//     if($getAllPurchaseGroupObj["status"]=="success"){
//         echo '<option value="">Select Purchase Group</option>';
//         foreach($getAllPurchaseGroupObj["data"] as $onePurchaseGroup){
//             ?>
//                 <option value="<?= $onePurchaseGroup["uomId"] ?>"><?= $onePurchaseGroup["uomName"] .'||'. $onePurchaseGroup["uomDesc"] ?></option>
//             <?php
//         }
//     }else{
//         echo '<option value="">Select Purchase Group</option>';
//     }

 }
else{
//     echo "Something wrong, try again!";
}

?>