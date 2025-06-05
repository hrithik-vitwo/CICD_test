<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$purchaseObj = new GoodsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo 1;
    //POST REQUEST
    console($_POST);
   $id = $_POST['id'];
   echo $priceOn = $_POST['price'];

 echo  $update = "UPDATE `erp_inventory_stocks_summary` SET `priceSetOn`= '".$priceOn."' WHERE `stockSummaryId`=$id";
   $returnData = queryUpdate($update);
   

  //  $createNewPurchaseGroupObj = $purchaseObj->changePrice($_POST);





}
else{

}
?>