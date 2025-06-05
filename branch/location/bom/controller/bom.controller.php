<?php
class BomController
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by;
    function __construct()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }
    public function getGoodMasterList(){
        return queryGet('SELECT items.itemId, items.itemName, items.itemCode, items.parentGlId, itemTypes.type, itemUom.uomName, COALESCE(summary.movingWeightedPrice, 0.00) AS movingWeightedPrice, COALESCE(itemBom.cogm, 0.00) AS itemBomPrice, summary.bomStatus FROM `erp_inventory_stocks_summary` AS summary INNER JOIN `erp_inventory_items` AS items ON summary.`itemId` = items.`itemId` INNER JOIN `erp_inventory_mstr_good_types` AS itemTypes ON items.`goodsType` = itemTypes.`goodTypeId` LEFT JOIN `erp_inventory_mstr_uom` AS itemUom ON items.`baseUnitMeasure` = itemUom.`uomId` LEFT JOIN `erp_bom` AS itemBom ON items.itemId = itemBom.itemId WHERE summary.`location_id` = ' . $this->location_id . ' AND itemTypes.`goodTypeId` IN (1,2,3)', true);
    }
    public function getBomList()
    {
        return queryGet('SELECT itemSummary.itemId,itemSummary.location_id, itemDetails.itemCode, itemDetails.itemName, itemSummary.bomStatus AS bomCreateStatus, bomDetails.preparedDate, bomDetails.cogm, bomDetails.cogm_m, bomDetails.cogm_a, bomDetails.cogs, bomDetails.msp, bomDetails.bomProgressStatus, bomDetails.createdAt, bomDetails.createdBy, bomDetails.updatedAt, bomDetails.updatedBy, bomDetails.bomStatus, bomDetails.bomId  FROM `erp_inventory_stocks_summary` AS itemSummary LEFT JOIN `erp_inventory_items` AS itemDetails ON itemSummary.itemId = itemDetails.itemId LEFT JOIN `erp_bom` AS bomDetails ON itemSummary.itemId = bomDetails.itemId WHERE itemSummary.location_id = ' . $this->location_id . ' AND itemDetails.goodsType IN (2,3) ORDER BY itemSummary.stockSummaryId DESC', true);
    }

    public function createBom($data)
    {

        // console($data);
        // exit();
        $cogm = $data["grandMaterialCost"] + $data["grandHourlyDeploymentCost"]+$data["grandOtherHeadCost"];
        $cogm_m = $data["grandMaterialCost"] ?? 0;
        $cogm_a = $data["grandHourlyDeploymentCost"]+$data["grandOtherHeadCost"];
        $cogs = $cogm;
        $msp = 0;
        $bomProgressStatus = $cogs > 0 ? "cogs" : "cogm";
        $wc_id =$data['workCenter'] ?? 0;


        $dbObj = new Database(true);
        $dbObj->setSuccessMsg("BOM created successfully.");
        $dbObj->setErrorMsg("BOM creation failed, please try again!");

        $dbObj->queryUpdate('UPDATE `erp_bom` SET `bomStatus` = "inactive" WHERE `itemId` = ' . $data["itemId"] . ' AND `locationId`=' . $this->location_id); // inactive previous bom

        $bomObj = $dbObj->queryInsert('INSERT INTO `erp_bom` SET `companyId`=' . $this->company_id . ',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`itemId`=' . $data["itemId"] . ',`itemType`="goods",`preparedBy`="' . $data["preparedBy"] . '",`preparedDate`="' . $data["preparedDate"] . '",`cogm`=' . $cogm . ', `cogm_m`='.$cogm_m.', `cogm_a`='.$cogm_a.', `cogs`=' . $cogs . ',`msp`=' . $msp . ',`wc_id`="'.$wc_id.'" ,`bomProgressStatus`="' . $bomProgressStatus . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');

        foreach ($data["bomMaterial"] as $row) {
            $row["Rate"] = $row["Rate"]>0 ? $row["Rate"] : 0;
            $row["Amount"] = $row["Amount"]>0 ? $row["Amount"] : 0;
            $row["ExtraPurchage"] = $row["ExtraPurchage"]>0 ? $row["ExtraPurchage"] : 0;
            $row["Consumption"] = $row["Consumption"]>0 ? $row["Consumption"] : 0;

            $dbObj->queryInsert('INSERT INTO `erp_bom_item_material` SET `bom_id`=' . $bomObj["insertedId"] . ',`item_id`=' . $row["ItemId"] . ',`consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '", `created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
        }
        foreach ($data["bomHd"] as $row) {
            $row["Rate"] = $row["Rate"]>0 ? $row["Rate"] : 0;
            $row["Amount"] = $row["Amount"]>0 ? $row["Amount"] : 0;
            $row["ExtraPurchage"] = $row["ExtraPurchage"]>0 ? $row["ExtraPurchage"] : 0;
            $row["Consumption"] = $row["Consumption"]>0 ? $row["Consumption"] : 0;

            // $dbObj->queryInsert('INSERT INTO `erp_bom_item_other` SET `bom_id`=' . $bomObj["insertedId"] . ',`cost_center_id`=' . $row["CostCenterId"] . ',`head_type`="hd",`consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');

            $dbObj->queryInsert('INSERT INTO `erp_bom_item_other` SET `bom_id`=' . $bomObj["insertedId"] . ',`cost_center_id`=' . $row["CostCenterId"] . ',`head_type`="'.$row["ItemHdType"].'",`consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');

        }
        
        foreach ($data["bomOtherHead"] as $row) {

            $row["Rate"] = $row["Rate"]>0 ? $row["Rate"] : 0;
            $row["Amount"] = $row["Amount"]>0 ? $row["Amount"] : 0;
            $row["ExtraPurchage"] = $row["ExtraPurchage"]>0 ? $row["ExtraPurchage"] : 0;
            $row["Consumption"] = $row["Consumption"]>0 ? $row["Consumption"] : 0;

            $dbObj->queryInsert('INSERT INTO `erp_bom_item_other` SET `bom_id`=' . $bomObj["insertedId"] . ',`cost_center_id`=' . $row["CostCenterId"] . ',`head_id`=' . $row["Head"] . ',`head_type`="other",`consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
        }

        $dbObj->queryUpdate('UPDATE `erp_inventory_stocks_summary` SET `bomStatus` = 2, `movingWeightedPrice` = '.$cogm.' WHERE `itemId` = ' . $data["itemId"] . ' AND `location_id`=' . $this->location_id);
        $result = $dbObj->queryFinish();
        return $result;
    }

    function getBomDetailsByItemId($itemId)
    {
        $dbObj = new Database();
        $bomSql = 'SELECT boms.*,items.`parentGlId`,items.`itemCode`,items.`itemName` FROM `erp_bom` AS boms LEFT JOIN `erp_inventory_items` AS items ON boms.`itemId`= items.`itemId` WHERE boms.`companyId`=' . $this->company_id . ' AND boms.`branchId`=' . $this->branch_id . ' AND boms.`locationId`=' . $this->location_id . '  AND boms.`itemId`=' . $itemId . ' AND boms.`bomStatus` = "active"';
        $bomObj = $dbObj->queryGet($bomSql);
       // console($bomObj);
        
        if ($bomObj['status'] != "success") {
            return [
                "status" => $bomObj["status"],
                "message" => $bomObj["message"],
                "data" => [
                    "bom_data" => [],
                    "bom_material_data" => [],
                    "bom_hd_data" => [],
                    "bom_other_head_data" => [],
                    "bomSql"=>$bomSql
                ]
            ];
        } else {
            $bomId = $bomObj["data"]["bomId"];
            $bomMaterialObj = $dbObj->queryGet('SELECT materials.*, (`materials`.`consumption`+(`materials`.`consumption`*`materials`.`extra`/100)) AS totalConsumption, items.`parentGlId`, items.`itemCode`,items.`baseUnitMeasure`, items.`itemName`, items.`goodsType`, items.`item_sell_type`,CASE  WHEN items.goodsType = 1 THEN "RM" WHEN items.goodsType = 2 THEN "SFG" ELSE "FG" END AS type, item_summary.`movingWeightedPrice`, item_summary.`priceType` FROM `erp_bom_item_material` AS materials LEFT JOIN `erp_inventory_items` AS items ON materials.`item_id`= items.`itemId` LEFT JOIN `erp_inventory_stocks_summary` AS item_summary ON materials.`item_id`= item_summary.`itemId` WHERE `bom_id`=' . $bomId, true);
            // $bomMaterialObj = $dbObj->queryGet('SELECT materials.*, FORMAT((`materials`.`consumption`+(`materials`.`consumption`*`materials`.`extra`/100)),2) AS totalConsumption, items.`parentGlId`, items.`itemCode`, items.`itemName`, items.`goodsType`, items.`item_sell_type`,CASE  WHEN items.goodsType = 1 THEN "RM" WHEN items.goodsType = 2 THEN "SFG" ELSE "FG" END AS type, item_summary.`movingWeightedPrice`, item_summary.`priceType` FROM `erp_bom_item_material` AS materials LEFT JOIN `erp_inventory_items` AS items ON materials.`item_id`= items.`itemId` LEFT JOIN `erp_inventory_stocks_summary` AS item_summary ON materials.`item_id`= item_summary.`itemId` WHERE `bom_id`=' . $bomId, true);

            // $bomHdObj = $dbObj->queryGet('SELECT * FROM  `erp_bom_item_other` WHERE `head_type`!="other" AND `bom_id`=' . $bomId, true);
            // $bomOtherHeadObj = $dbObj->queryGet('SELECT * FROM  `erp_bom_item_other` WHERE `head_type`="other" AND `bom_id`=' . $bomId, true);

            // $bomHdObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, costCenter.CostCenter_code, costCenter.CostCenter_desc, costCenter.gl_code AS CostCenter_gl_code FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_cost_center` AS costCenter ON bomOtherItem.`cost_center_id` = costCenter.`CostCenter_id` WHERE bomOtherItem.`head_type`!="other" AND bomOtherItem.`bom_id`=' . $bomId, true);
            
            // $bomOtherHeadObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, costCenter.CostCenter_code, costCenter.CostCenter_desc, costCenter.gl_code AS CostCenter_gl_code FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_cost_center` AS costCenter ON bomOtherItem.`cost_center_id` = costCenter.`CostCenter_id` WHERE bomOtherItem.`head_type`="other" AND bomOtherItem.`bom_id`=' . $bomId, true);


            $bomHdObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, work_center.work_center_code, work_center.work_center_description FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_work_center` AS work_center ON bomOtherItem.`cost_center_id` = work_center.`work_center_id` WHERE bomOtherItem.`head_type`!="other" AND bomOtherItem.`bom_id`=' . $bomId, true);
            
            $bomOtherHeadObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, work_center.work_center_code, work_center.work_center_description FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_work_center` AS work_center ON bomOtherItem.`cost_center_id` = work_center.`work_center_id` WHERE bomOtherItem.`head_type`="other" AND bomOtherItem.`bom_id`=' . $bomId, true);


          


            $result = [
                "status" => $bomObj["status"],
                "message" => $bomObj["message"],
                "data" => [
                    "bom_data" => $bomObj["data"],
                    "bom_material_data" => $bomMaterialObj["data"],
                    "bom_hd_data" => $bomHdObj["data"],
                    "bom_other_head_data" => $bomOtherHeadObj["data"],
                ]
            ];
            return $result;
        }
    }

    function getBomDetails($bomId)
    {
        $dbObj = new Database();
       
        $bomSql = 'SELECT boms.*,items.`parentGlId`,items.`itemCode`,items.`itemName` FROM `erp_bom` AS boms LEFT JOIN `erp_inventory_items` AS items ON boms.`itemId`= items.`itemId` WHERE boms.`companyId`=' . $this->company_id . ' AND boms.`branchId`=' . $this->branch_id . ' AND boms.`locationId`=' . $this->location_id . ' AND boms.`bomId`=' . $bomId;
        $bomObj = $dbObj->queryGet($bomSql);
       // console($bomObj);
        
        if ($bomObj['status'] != "success") {
            return [
                "status" => $bomObj["status"],
                "message" => $bomObj["message"],
                "data" => [
                    "bom_data" => [],
                    "bom_material_data" => [],
                    "bom_hd_data" => [],
                    "bom_other_head_data" => [],
                    "bomSql"=>$bomSql
                ]
            ];
        } else {
            $bomId = $bomObj["data"]["bomId"];
            $bomMaterialObj = $dbObj->queryGet('SELECT materials.*, (`materials`.`consumption`+(`materials`.`consumption`*`materials`.`extra`/100)) AS totalConsumption, items.`parentGlId`, items.`itemCode`, items.`itemName`, items.`goodsType`, items.`item_sell_type`,CASE  WHEN items.goodsType = 1 THEN "RM" WHEN items.goodsType = 2 THEN "SFG" ELSE "FG" END AS type, item_summary.`movingWeightedPrice`, item_summary.`priceType` FROM `erp_bom_item_material` AS materials LEFT JOIN `erp_inventory_items` AS items ON materials.`item_id`= items.`itemId` LEFT JOIN `erp_inventory_stocks_summary` AS item_summary ON materials.`item_id`= item_summary.`itemId` WHERE `bom_id`=' . $bomId, true);
            // $bomMaterialObj = $dbObj->queryGet('SELECT materials.*, FORMAT((`materials`.`consumption`+(`materials`.`consumption`*`materials`.`extra`/100)),2) AS totalConsumption, items.`parentGlId`, items.`itemCode`, items.`itemName`, items.`goodsType`, items.`item_sell_type`,CASE  WHEN items.goodsType = 1 THEN "RM" WHEN items.goodsType = 2 THEN "SFG" ELSE "FG" END AS type, item_summary.`movingWeightedPrice`, item_summary.`priceType` FROM `erp_bom_item_material` AS materials LEFT JOIN `erp_inventory_items` AS items ON materials.`item_id`= items.`itemId` LEFT JOIN `erp_inventory_stocks_summary` AS item_summary ON materials.`item_id`= item_summary.`itemId` WHERE `bom_id`=' . $bomId, true);

            // $bomHdObj = $dbObj->queryGet('SELECT * FROM  `erp_bom_item_other` WHERE `head_type`!="other" AND `bom_id`=' . $bomId, true);
            // $bomOtherHeadObj = $dbObj->queryGet('SELECT * FROM  `erp_bom_item_other` WHERE `head_type`="other" AND `bom_id`=' . $bomId, true);

            // $bomHdObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, costCenter.CostCenter_code, costCenter.CostCenter_desc, costCenter.gl_code AS CostCenter_gl_code FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_cost_center` AS costCenter ON bomOtherItem.`cost_center_id` = costCenter.`CostCenter_id` WHERE bomOtherItem.`head_type`!="other" AND bomOtherItem.`bom_id`=' . $bomId, true);
            
            // $bomOtherHeadObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, costCenter.CostCenter_code, costCenter.CostCenter_desc, costCenter.gl_code AS CostCenter_gl_code FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_cost_center` AS costCenter ON bomOtherItem.`cost_center_id` = costCenter.`CostCenter_id` WHERE bomOtherItem.`head_type`="other" AND bomOtherItem.`bom_id`=' . $bomId, true);


            $bomHdObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, work_center.work_center_code, work_center.work_center_description FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_work_center` AS work_center ON bomOtherItem.`cost_center_id` = work_center.`work_center_id` WHERE bomOtherItem.`head_type`!="other" AND bomOtherItem.`bom_id`=' . $bomId, true);
            
            $bomOtherHeadObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, work_center.work_center_code, work_center.work_center_description FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_work_center` AS work_center ON bomOtherItem.`cost_center_id` = work_center.`work_center_id` WHERE bomOtherItem.`head_type`="other" AND bomOtherItem.`bom_id`=' . $bomId, true);


          


            $result = [
                "status" => $bomObj["status"],
                "message" => $bomObj["message"],
                "data" => [
                    "bom_data" => $bomObj["data"],
                    "bom_material_data" => $bomMaterialObj["data"],
                    "bom_hd_data" => $bomHdObj["data"],
                    "bom_other_head_data" => $bomOtherHeadObj["data"],
                ]
            ];
            return $result;
        }
    }
    // function getBomDetails($itemId)
    // {
    //     $dbObj = new Database();
       
    //     $bomSql = 'SELECT boms.*,items.`parentGlId`,items.`itemCode`,items.`itemName` FROM `erp_bom` AS boms LEFT JOIN `erp_inventory_items` AS items ON boms.`itemId`= items.`itemId` WHERE boms.`companyId`=' . $this->company_id . ' AND boms.`branchId`=' . $this->branch_id . ' AND boms.`locationId`=' . $this->location_id . ' AND boms.`itemId`=' . $itemId . '  AND boms.`bomStatus`="active"';
    //     $bomObj = $dbObj->queryGet($bomSql);
    //    // console($bomObj);
        
    //     if ($bomObj['status'] != "success") {
    //         return [
    //             "status" => $bomObj["status"],
    //             "message" => $bomObj["message"],
    //             "data" => [
    //                 "bom_data" => [],
    //                 "bom_material_data" => [],
    //                 "bom_hd_data" => [],
    //                 "bom_other_head_data" => [],
    //                 "bomSql"=>$bomSql
    //             ]
    //         ];
    //     } else {
    //         $bomId = $bomObj["data"]["bomId"];
    //         $bomMaterialObj = $dbObj->queryGet('SELECT materials.*, FORMAT((`materials`.`consumption`+(`materials`.`consumption`*`materials`.`extra`/100)),2) AS totalConsumption, items.`parentGlId`, items.`itemCode`, items.`itemName`, items.`goodsType`, items.`item_sell_type`,CASE  WHEN items.goodsType = 1 THEN "RM" WHEN items.goodsType = 2 THEN "SFG" ELSE "FG" END AS type, item_summary.`movingWeightedPrice`, item_summary.`priceType` FROM `erp_bom_item_material` AS materials LEFT JOIN `erp_inventory_items` AS items ON materials.`item_id`= items.`itemId` LEFT JOIN `erp_inventory_stocks_summary` AS item_summary ON materials.`item_id`= item_summary.`itemId` WHERE `bom_id`=' . $bomId, true);

    //         // $bomHdObj = $dbObj->queryGet('SELECT * FROM  `erp_bom_item_other` WHERE `head_type`!="other" AND `bom_id`=' . $bomId, true);
    //         // $bomOtherHeadObj = $dbObj->queryGet('SELECT * FROM  `erp_bom_item_other` WHERE `head_type`="other" AND `bom_id`=' . $bomId, true);

    //         // $bomHdObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, costCenter.CostCenter_code, costCenter.CostCenter_desc, costCenter.gl_code AS CostCenter_gl_code FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_cost_center` AS costCenter ON bomOtherItem.`cost_center_id` = costCenter.`CostCenter_id` WHERE bomOtherItem.`head_type`!="other" AND bomOtherItem.`bom_id`=' . $bomId, true);
            
    //         // $bomOtherHeadObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, costCenter.CostCenter_code, costCenter.CostCenter_desc, costCenter.gl_code AS CostCenter_gl_code FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_cost_center` AS costCenter ON bomOtherItem.`cost_center_id` = costCenter.`CostCenter_id` WHERE bomOtherItem.`head_type`="other" AND bomOtherItem.`bom_id`=' . $bomId, true);


    //         $bomHdObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, work_center.work_center_code, work_center.work_center_description FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_work_center` AS work_center ON bomOtherItem.`cost_center_id` = work_center.`work_center_id` WHERE bomOtherItem.`head_type`!="other" AND bomOtherItem.`bom_id`=' . $bomId, true);
            
    //         $bomOtherHeadObj = $dbObj->queryGet('SELECT bomOtherItem.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, work_center.work_center_code, work_center.work_center_description FROM `erp_bom_item_other` AS bomOtherItem LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON bomOtherItem.`head_id`=expenseOtherHead.`head_id` LEFT JOIN `erp_work_center` AS work_center ON bomOtherItem.`cost_center_id` = work_center.`work_center_id` WHERE bomOtherItem.`head_type`="other" AND bomOtherItem.`bom_id`=' . $bomId, true);


          


    //         $result = [
    //             "status" => $bomObj["status"],
    //             "message" => $bomObj["message"],
    //             "data" => [
    //                 "bom_data" => $bomObj["data"],
    //                 "bom_material_data" => $bomMaterialObj["data"],
    //                 "bom_hd_data" => $bomHdObj["data"],
    //                 "bom_other_head_data" => $bomOtherHeadObj["data"],
    //             ]
    //         ];
    //         return $result;
    //     }
    // }
}
