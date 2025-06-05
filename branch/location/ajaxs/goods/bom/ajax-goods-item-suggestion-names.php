<?php
require_once("../../../../../app/v1/connection-branch-admin.php");
require_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
$headerData = array('Content-Type: application/json');

// queryGet
if (isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]) && $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] != "") {
    $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $companyID = $_SESSION['logedBranchAdminInfo']['fldAdminCompanyId'];
    $keyWord = $_GET["keyWord"] ?? "";
    if($keyWord!=""){
        
        // $sql = "SELECT items.*, types.type FROM `erp_inventory_items` as items ,`erp_inventory_mstr_good_types` as types WHERE items.goodsType=types.goodTypeId AND items.`company_id`=".$companyID." AND (types.type='RM' OR types.type='SFG') AND (items.`itemName` LIKE '%".$keyWord."%' OR items.`itemCode` LIKE '%".$keyWord."%' OR types.`type`='".$keyWord."')";

        $sql = 'SELECT
                items.itemId,
                items.itemName,
                items.itemCode,
                types.type
            FROM
                `erp_inventory_stocks_summary` AS summary,
                `erp_inventory_items` AS items,
                `erp_inventory_mstr_good_types` AS types
            WHERE
                summary.`itemId`=items.`itemId` AND 
                items.`goodsType` = types.`goodTypeId` AND 
                summary.`location_id` = '.$location_id.' AND 
                (types.`type` = "RM" OR types.`type` = "SFG") AND 
                ( items.`itemName` LIKE "%'.$keyWord.'%" OR items.`itemCode` LIKE "%'.$keyWord.'%" OR types.`type` = "'.$keyWord.'")';

        $rmGoodsObj = queryGet($sql, true);
        //console($rmGoodsObj);
        if($rmGoodsObj["status"] == "success"){
            foreach($rmGoodsObj["data"] as $oneRmGood) {
                echo '<span class="dropdown-item btn dropdownGoodItem" data-id="'.$oneRmGood["itemId"].'">'.$oneRmGood["itemName"]." (".$oneRmGood["itemCode"].') - '.$oneRmGood["type"].'</span>';
            }
        }else{
            echo '<span class="dropdown-item btn dropdownGoodItem" data-id="0">Not found...</span>';
        }
    }else{
        echo '<span class="dropdown-item btn dropdownGoodItem" data-id="0">Enter keyword for search...</span>';
    }
} else {
    echo "Please do login first";
}
