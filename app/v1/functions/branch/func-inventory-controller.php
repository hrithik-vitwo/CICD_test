<?php


class Inventory{


    function getInventorySummary(){
        global $company_id; global $branch_id; global $location_id; global $created_by; global $updated_by;

        // return queryGet('SELECT invSummary.*,goodTypes.`goodTypeName` as goodType, invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType` FROM `erp_inventory_stocks_summary` as invSummary, `erp_inventory_items` as invItems, `erp_inventory_mstr_good_types` as goodTypes WHERE invSummary.itemId=invItems.itemId AND invSummary.`company_id`='.$company_id.' AND invSummary.`branch_id`='.$branch_id.' AND invSummary.`location_id`='.$location_id.' AND invItems.`goodsType`=goodTypes.`goodTypeId` ' , true);
        return queryGet('SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invSummary.`company_id` ='.$company_id.' AND invSummary.`branch_id` ='.$branch_id.' AND invSummary.`location_id` ='.$location_id.' AND invItems.`goodsType` = goodTypes.`goodTypeId` AND invItems.`baseUnitMeasure`=goodUoms.`uomId` ORDER BY invSummary.`updatedAt` DESC' , true);
    }

    function getRmInventorySummary(){
        global $company_id; global $branch_id; global $location_id; global $created_by; global $updated_by;

        // return queryGet('SELECT invSummary.*,goodTypes.`goodTypeName` as goodType, invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType` FROM `erp_inventory_stocks_summary` as invSummary, `erp_inventory_items` as invItems, `erp_inventory_mstr_good_types` as goodTypes WHERE invSummary.itemId=invItems.itemId AND invSummary.`company_id`='.$company_id.' AND invSummary.`branch_id`='.$branch_id.' AND invSummary.`location_id`='.$location_id.' AND invItems.`goodsType`=goodTypes.`goodTypeId` ' , true);
        return queryGet('SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invSummary.`company_id` ='.$company_id.' AND invSummary.`branch_id` ='.$branch_id.' AND invSummary.`location_id` ='.$location_id.' AND invItems.`goodsType` = goodTypes.`goodTypeId` AND invItems.goodsType = 1 AND invItems.`baseUnitMeasure`=goodUoms.`uomId` ORDER BY invSummary.`updatedAt` DESC' , true);
    }
    function getFgInventorySummary(){
        global $company_id; global $branch_id; global $location_id; global $created_by; global $updated_by;

        // return queryGet('SELECT invSummary.*,goodTypes.`goodTypeName` as goodType, invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType` FROM `erp_inventory_stocks_summary` as invSummary, `erp_inventory_items` as invItems, `erp_inventory_mstr_good_types` as goodTypes WHERE invSummary.itemId=invItems.itemId AND invSummary.`company_id`='.$company_id.' AND invSummary.`branch_id`='.$branch_id.' AND invSummary.`location_id`='.$location_id.' AND invItems.`goodsType`=goodTypes.`goodTypeId` ' , true);
        return queryGet('SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invSummary.`company_id` ='.$company_id.' AND invSummary.`branch_id` ='.$branch_id.' AND invSummary.`location_id` ='.$location_id.' AND invItems.`goodsType` = goodTypes.`goodTypeId` AND (invItems.goodsType = 3 OR invItems.goodsType = 4) AND invItems.`baseUnitMeasure`=goodUoms.`uomId` ORDER BY invSummary.`updatedAt` DESC' , true);
    }
    function getSfgInventorySummary(){
        global $company_id; global $branch_id; global $location_id; global $created_by; global $updated_by;

        // return queryGet('SELECT invSummary.*,goodTypes.`goodTypeName` as goodType, invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType` FROM `erp_inventory_stocks_summary` as invSummary, `erp_inventory_items` as invItems, `erp_inventory_mstr_good_types` as goodTypes WHERE invSummary.itemId=invItems.itemId AND invSummary.`company_id`='.$company_id.' AND invSummary.`branch_id`='.$branch_id.' AND invSummary.`location_id`='.$location_id.' AND invItems.`goodsType`=goodTypes.`goodTypeId` ' , true);
        return queryGet('SELECT invSummary.*, goodTypes.`goodTypeName` AS goodType,  invItems.`itemCode`, invItems.`itemName`, invItems.`itemDesc`, invItems.`baseUnitMeasure`, invItems.`goodsType`, goodUoms.`uomName`, goodUoms.`uomDesc` FROM `erp_inventory_stocks_summary` AS invSummary, `erp_inventory_items` AS invItems, `erp_inventory_mstr_good_types` AS goodTypes, `erp_inventory_mstr_uom` AS goodUoms WHERE invSummary.itemId = invItems.itemId AND invSummary.`company_id` ='.$company_id.' AND invSummary.`branch_id` ='.$branch_id.' AND invSummary.`location_id` ='.$location_id.' AND invItems.`goodsType` = goodTypes.`goodTypeId` AND invItems.goodsType = 2 AND invItems.`baseUnitMeasure`=goodUoms.`uomId` ORDER BY invSummary.`updatedAt` DESC' , true);
    }


}



?>