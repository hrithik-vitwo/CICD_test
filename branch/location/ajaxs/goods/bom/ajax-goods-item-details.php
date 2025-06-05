<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');


$responseData = [];


//function getMovingWeightedAvarage($itemKey=)


if (isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]) && $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] != "") {
    $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
    $itemKey = $_GET["itemId"] ?? "";
    if($itemKey!=""){

        $sql = "SELECT items.*, itemUom.`uomName`, types.`type` FROM `erp_inventory_items` as items, `erp_inventory_mstr_uom` as itemUom, `erp_inventory_mstr_good_types` as types WHERE items.goodsType=types.goodTypeId AND items.`baseUnitMeasure` = itemUom.`uomId` AND items.`itemId` ='".$itemKey."' AND items.`company_id`=".$companyID;
        //$sql = "SELECT items.*, price.* FROM `erp_inventory_items` as items, `erp_inventory_item_price` as price WHERE items.`itemCode` = price.`ItemPrice` AND  items.`itemId` ='25'";

        $rmGoodsObj = queryGet($sql);
        //console($rmGoodsObj);
        if($rmGoodsObj["status"] == "success"){
            
            $movingWeightedPriceObj = queryGet('SELECT `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `location_id`='.$location_id.' AND `branch_id`='.$branch_id.' AND `company_id`='.$company_id.' AND `itemId`='.$itemKey);
            $rmGoodsObj["data"]["movingWeightedPrice"] = $movingWeightedPriceObj["data"]["movingWeightedPrice"];

            if($rmGoodsObj["data"]["movingWeightedPrice"]!=""){
                $cogmObj = queryGet('SELECT `cogm` FROM `erp_bom` WHERE `locationId`='.$location_id.' AND `branchId`='.$branch_id.' AND `companyId`='.$company_id.' AND `itemId`='.$itemKey);
                $rmGoodsObj["data"]["movingWeightedPrice"] = $cogmObj["data"]["cogm"];
            }

            $responseData = [
                "status" => "success",
                "message" => "Successfully fetched goods items",
                "data" => $rmGoodsObj["data"]
            ];
        }else{
            $responseData = [
                "status" => "warning",
                "message" => "Something went wrong in fetching goods items",
                "data" => [],
                "sql" => $sql
            ];
        }
    }else{
        $responseData = [
            "status" => "warning",
            "message" => "Something went wrong in fetching goods items",
            "data" => []
        ];
    }
} else {
    $responseData = [
        "status" => "warning",
        "message" => "login first to fetch the data",
        "data" => []
    ];
}
echo json_encode($responseData);