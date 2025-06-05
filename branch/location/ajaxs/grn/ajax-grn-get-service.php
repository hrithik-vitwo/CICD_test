<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-bills-controller.php");
require_once("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");


if(isset($_GET["data"]) && $_GET["data"] == "service")

{
    $goodsController = new GoodsController();
    $rmServiceObj = $goodsController->getAllGRNServices();
    if ($rmServiceObj["status"] == "success") {
        $options = "";
        $options .= '<option value="" data-hsncode="" data-itemtitle="">Select Service</option>';
        foreach ($rmServiceObj["data"] as $oneService) {
    
           $options .= "<option value='".$oneService["itemCode"]."' data-itemid='".$oneService["itemId"]."' data-hsncode='". $oneService["hsnCode"]."' data-itemtitle='". $oneService["itemName"]."'>".$oneService["itemCode"]." | ".$oneService["itemName"]." | ". $oneService["itemDesc"]."</option>";
    
        }
    }
    else
    {
        $options .= "<option value=''>".$rmServiceObj["message"]."</option>";
    }

    

    echo json_encode($options);
}
else
{
    $goodsController = new GoodsController();
    $rmGoodsObj = $goodsController->getAllRMGoods();
    if ($rmGoodsObj["status"] == "success") {
        $options = "";
        $options .= '<option value="" data-hsncode="" data-itemtitle="">Select Item</option>';
        foreach ($rmGoodsObj["data"] as $oneRmGoods) {
    
           $options .= "<option value='".$oneRmGoods["itemCode"]."' data-itemid='".$oneRmGoods["itemId"]."' data-hsncode='". $oneRmGoods["hsnCode"]."' data-itemtitle='". $oneRmGoods["itemName"]."'>".$oneRmGoods["itemCode"]." | ".$oneRmGoods["itemName"]." | ". $oneRmGoods["itemDesc"]."</option>";
    
        }
    }
    else
    {
        $options .= "<option value=''>".$rmGoodsObj["message"]."</option>";
    }

    echo json_encode($options);
}



?>