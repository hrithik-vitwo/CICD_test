<?php
require_once("../../../../app/v1/connection-branch-admin.php");
// header("Content-Type: application/json");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();

if ($_GET['act'] === "barcodescanner") {
    $itemCode = $_GET['itemCode'] ?? 0;
    $barcode = $_GET['barcode'] ?? 0;
    
    $getItemIdObj = queryGet("SELECT `itemId` FROM `erp_inventory_items` WHERE `itemCode`='$itemCode'");
    $itemId = $getItemIdObj['data']['itemId'];
    
    $itemListObj = $BranchSoObj->fetchItemByBarcodescanner($itemId, $barcode);
    
    $itemList = $itemListObj['data'];
    if ($itemListObj['numRows'] > 0) {
        $itemStocks = $BranchSoObj->deliveryCreateItemQty($itemId)['sumOfBatches'];
        if ($itemStocks > 0) {
            $responseData['status'] = "success";
            $responseData['message'] = 'data found';
            $responseData['stock'] = $itemStocks;
            $responseData['itemId'] = $itemId;
        } else {
            $responseData['status'] = "warning";
            $responseData['message'] = 'out of stock';
            $responseData['stock'] = $itemStocks;
            $responseData['itemId'] = $itemId;
        }
    } else {
        $responseData['status'] = "warning";
        $responseData['message'] = 'data not found';
    }
    echo json_encode($responseData);
} elseif ($_GET['act'] === "test") {
    echo "test results";
} else {
    echo "Something wrong, try again!";
}
