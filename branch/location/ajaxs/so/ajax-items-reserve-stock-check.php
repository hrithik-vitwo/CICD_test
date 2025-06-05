<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_POST['act'] === "itemStockCheck") {
    $asondate=$_POST['invoicedate'];
    $sl=$_POST['sl'];
    $rowData = json_decode($_POST['rowData']);
    $returnarray=[];
    foreach($rowData as $key=>$data){
       $stock= $BranchSoObj->itemQtyTotalStockCheck($data,"'$sl'",$asondate);
       $getStockqty = $stock['data']['itemQty']??0;
        $returnarray[$key]=$getStockqty;
        $responseData['stock']= $stock;
    }
    $responseData['status']="success";
    $responseData['data']= $returnarray;
    // print_r('$rowData');
    // print_r($rowData);
}
elseif ($_POST['act'] === "approvalTab") {
    $responseData['status']="warning";
    $responseData['message']='Something wrong';
} else {
    $responseData['status']="warning";
    $responseData['message']='Something wrong, try again!';
}
echo json_encode($responseData);
?>