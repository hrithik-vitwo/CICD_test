<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_POST['act'] === "itemStockCheck") {
    $asondate=$_POST['invoicedate'];
    $rowData = json_decode($_POST['rowData']);
    $returnarray=[];
    foreach($rowData as $key=>$data){
       $stock= itemQtyTotalStockChecking($data,"'rmWhOpen', 'fgWhOpen'",$asondate);
       $getStockqty = $stock['data']['itemQty']??0;
        $returnarray[$key]=$getStockqty;
        $responseData['stock']= $stock;
    }
    $responseData['status']="success";
    $responseData['data']= $returnarray;
    // print_r('$rowData');
    // print_r($rowData);
}
elseif ($_POST['act'] === "singleItemStockCheck") {
    $asondate=$_POST['invoicedate'];
    $itemId = $_POST['itemId'];

    $stock= itemQtyTotalStockChecking($itemId,"'rmWhOpen', 'fgWhOpen'",$asondate);
    $getStockqty = $stock['data']['itemQty']??0;
    $returnarray['sumofbatch']=$getStockqty;
    $responseData['stock']= $stock;

    $responseData['status']="success";
    $responseData['data']= $returnarray;

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