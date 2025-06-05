<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
if ($_GET['act'] === "service") {
    $itemId = $_GET['itemId'];
    $getItemObj = $ItemsObj->getItemById($itemId);
 //   console($getItemObj);
    $itemCode = $getItemObj['data']['itemCode'];
    $lastPricesql = "SELECT * FROM `erp_branch_purchase_order_items`as po_item JOIN `erp_branch_purchase_order` as po ON po_item.`po_id`=po.po_id WHERE `location_id`=$location_id AND `itemCode`=$itemCode ORDER BY po_item.`po_item_id` DESC LIMIT 1";

     
    $last = queryGet($lastPricesql);
    $lastRow = $last['data'] ?? "";
    $lastPrice = $lastRow['unitPrice'] ?? "0";

   $hsn = $getItemObj['data']['hsnCode'];
   $hsn_sql = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode`= $hsn");
  // console($hsn_sql);
   $percentage = $hsn_sql['data']['taxPercentage'];
   $tax_amount = ($lastPrice*$percentage)/100;
   $total = $tax_amount+$lastPrice;

   $responseData['price'] = round($lastPrice,2);
   $responseData['percentage'] = $percentage;
   $responseData['total'] = round($total,2);
    
   echo json_encode($responseData);




}


?>