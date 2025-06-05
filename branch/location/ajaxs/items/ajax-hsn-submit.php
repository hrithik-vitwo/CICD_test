<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];
$companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
$goodsObj = new GoodsController();




 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $createNewGoodGroupObj = $goodsObj->addHSN($_POST);


 }
else{
//     echo "Something wrong, try again!";
}

?>