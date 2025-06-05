<?php
class BoqController
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


    public function getBoqList()
    {
        return queryGet('SELECT itemSummary.itemId, itemDetails.itemCode, itemDetails.itemName, itemSummary.bomStatus AS boqCreateStatus, boqDetails.preparedDate, boqDetails.cogm, boqDetails.cogs, boqDetails.msp, boqDetails.boqProgressStatus, boqDetails.createdAt, boqDetails.createdBy, boqDetails.updatedAt, boqDetails.updatedBy, boqDetails.boqStatus FROM `erp_inventory_stocks_summary` AS itemSummary LEFT JOIN `erp_inventory_items` AS itemDetails ON itemSummary.itemId = itemDetails.itemId LEFT JOIN `erp_boq` AS boqDetails ON itemSummary.itemId = boqDetails.itemId AND itemSummary.location_id=boqDetails.locationId WHERE itemSummary.location_id=' . $this->location_id . ' AND itemDetails.goodsType=5 ORDER BY boqDetails.boqId DESC', true);
    }

    public function createBoq($data)
    {
        // return $data;
        $cogm = $data["grandServiceCost"] + $data["grandMaterialCost"] + $data["grandActivityCost"];

        $cosp_a = $data["grandActivityCost"];
        $cosp_i = $data["grandServiceCost"];
        $cosp_m = $data["grandMaterialCost"];

        $cogs = $cogm;
        $msp = 0;
        $boqProgressStatus = $cogs > 0 ? "cogs" : "cogm";


        $dbObj = new Database(true);
        $dbObj->setSuccessMsg("BOQ created successfully.");
        $dbObj->setErrorMsg("BOQ creation failed, please try again!");

        $dbObj->queryUpdate('UPDATE `erp_boq` SET `boqStatus` = "inactive" WHERE `itemId` = ' . $data["itemId"] . ' AND `locationId`=' . $this->location_id); // inactive previous boq

        $boqObj = $dbObj->queryInsert('INSERT INTO `erp_boq` SET `companyId`=' . $this->company_id . ',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`itemId`=' . $data["itemId"] . ',`itemType`="goods",`preparedBy`="' . $data["preparedBy"] . '",`preparedDate`="' . $data["preparedDate"] . '",`cogm`=' . $cogm . ', `cosp_a`='.$cosp_a.', `cosp_i`='.$cosp_i.', `cosp_m`='.$cosp_m.', `cogs`=' . $cogs . ',`msp`=' . $msp . ', `boqProgressStatus`="' . $boqProgressStatus . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');

        foreach ($data["boqService"] as $row) {
            $row["Rate"] = $row["Rate"] > 0 ? $row["Rate"] : 0;
            $row["Amount"] = $row["Amount"] > 0 ? $row["Amount"] : 0;
            $row["ExtraPurchage"] = $row["ExtraPurchage"] > 0 ? $row["ExtraPurchage"] : 0;
            $row["Consumption"] = $row["Consumption"] > 0 ? $row["Consumption"] : 0;

            $dbObj->queryInsert('INSERT INTO `erp_boq_item` SET `boq_id`=' . $boqObj["insertedId"] . ',`item_id`=' . $row["ItemId"] . ', `isService`=1, `consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '", `created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
        }
        foreach ($data["boqMaterial"] as $row) {
            $row["Rate"] = $row["Rate"] > 0 ? $row["Rate"] : 0;
            $row["Amount"] = $row["Amount"] > 0 ? $row["Amount"] : 0;
            $row["ExtraPurchage"] = $row["ExtraPurchage"] > 0 ? $row["ExtraPurchage"] : 0;
            $row["Consumption"] = $row["Consumption"] > 0 ? $row["Consumption"] : 0;

            $dbObj->queryInsert('INSERT INTO `erp_boq_item` SET `boq_id`=' . $boqObj["insertedId"] . ',`item_id`=' . $row["ItemId"] . ', `isService`=0, `consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '", `created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
        }
        foreach ($data["boqHd"] as $row) {
            $row["Rate"] = $row["Rate"] > 0 ? $row["Rate"] : 0;
            $row["Amount"] = $row["Amount"] > 0 ? $row["Amount"] : 0;
            $row["ExtraPurchage"] = $row["ExtraPurchage"] > 0 ? $row["ExtraPurchage"] : 0;
            $row["Consumption"] = $row["Consumption"] > 0 ? $row["Consumption"] : 0;

            // $dbObj->queryInsert('INSERT INTO `erp_boq_item_other` SET `boq_id`=' . $boqObj["insertedId"] . ',`cost_center_id`=' . $row["CostCenterId"] . ',`head_type`="hd",`consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');

            $dbObj->queryInsert('INSERT INTO `erp_boq_item_other` SET `boq_id`=' . $boqObj["insertedId"] . ',`cost_center_id`=' . $row["CostCenterId"] . ',`head_type`="'.$row["ItemHdType"].'",`consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
        }
        foreach ($data["boqOtherHead"] as $row) {
            $row["Rate"] = $row["Rate"] > 0 ? $row["Rate"] : 0;
            $row["Amount"] = $row["Amount"] > 0 ? $row["Amount"] : 0;
            $row["ExtraPurchage"] = $row["ExtraPurchage"] > 0 ? $row["ExtraPurchage"] : 0;
            $row["Consumption"] = $row["Consumption"] > 0 ? $row["Consumption"] : 0;

            $dbObj->queryInsert('INSERT INTO `erp_boq_item_other` SET `boq_id`=' . $boqObj["insertedId"] . ',`cost_center_id`=' . $row["CostCenterId"] . ',`head_id`=' . $row["Head"] . ',`head_type`="other",`consumption`=' . $row["Consumption"] . ',`extra`=' . $row["ExtraPurchage"] . ',`uom`="' . $row["Uom"] . '",`rate`=' . $row["Rate"] . ',`amount`=' . $row["Amount"] . ',`remarks`="' . $row["Remark"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
        }

        $dbObj->queryUpdate('UPDATE `erp_inventory_stocks_summary` SET `bomStatus` = 2 WHERE `itemId` = ' . $data["itemId"] . ' AND `location_id`=' . $this->location_id);
        $result = $dbObj->queryFinish();
        return $result;
    }

    function getBoqDetails($itemId)
    {
        $dbObj = new Database();

        $boqObj = $dbObj->queryGet('SELECT boqs.*,items.`parentGlId`,items.`itemCode`,items.`itemName` FROM `erp_boq` AS boqs LEFT JOIN `erp_inventory_items` AS items ON boqs.`itemId`= items.`itemId` WHERE boqs.`companyId`=' . $this->company_id . ' AND boqs.`branchId`=' . $this->branch_id . ' AND boqs.`locationId`=' . $this->location_id . ' AND boqs.`itemId`=' . $itemId . ' AND boqs.`boqStatus` = "active"');
        if ($boqObj['status'] != "success") {
            return [
                "status" => $boqObj["status"],
                "message" => $boqObj["message"],
                "data" => [
                    "boq_data" => [],
                    "boq_service_data" => [],
                    "boq_material_data" => [],
                    "boq_hd_data" => [],
                    "boq_other_head_data" => [],
                ]
            ];
        } else {
            $boqId = $boqObj["data"]["boqId"];
            $boqServiceObj = $dbObj->queryGet('SELECT `boqItems`.*, FORMAT((`boqItems`.`consumption`+(`boqItems`.`consumption`*`boqItems`.`extra`/100)),2) AS totalConsumption, items.`parentGlId`, items.`itemCode`, items.`itemName`, items.`goodsType`, items.`item_sell_type`,CASE  WHEN items.goodsType = 1 THEN "RM" WHEN items.goodsType = 2 THEN "SFG" ELSE "FG" END AS type, item_summary.`movingWeightedPrice`, item_summary.`priceType` FROM `erp_boq_item` AS boqItems LEFT JOIN `erp_inventory_items` AS items ON `boqItems`.`item_id`= items.`itemId` LEFT JOIN `erp_inventory_stocks_summary` AS item_summary ON boqItems.`item_id`= item_summary.`itemId` WHERE `isService`=1 AND `boq_id`=' . $boqId, true);

            $boqMaterialObj = $dbObj->queryGet('SELECT `boqItems`.*, FORMAT((`boqItems`.`consumption`+(`boqItems`.`consumption`*`boqItems`.`extra`/100)),2) AS totalConsumption, items.`parentGlId`, items.`itemCode`, items.`itemName`, items.`goodsType`, items.`item_sell_type`,CASE  WHEN items.goodsType = 1 THEN "RM" WHEN items.goodsType = 2 THEN "SFG" ELSE "FG" END AS type, item_summary.`movingWeightedPrice`, item_summary.`priceType` FROM `erp_boq_item` AS boqItems LEFT JOIN `erp_inventory_items` AS items ON `boqItems`.`item_id`= items.`itemId` LEFT JOIN `erp_inventory_stocks_summary` AS item_summary ON boqItems.`item_id`= item_summary.`itemId` WHERE `isService`=0 AND `boq_id`=' . $boqId, true);

            // $boqHdObj = $dbObj->queryGet('SELECT `boqItem`.*, `costCenter`.* FROM `erp_boq_item_other` AS boqItem JOIN `erp_cost_center` AS costCenter ON costCenter.CostCenter_id = boqItem.cost_center_id WHERE boqItem.`head_type` != "other" AND boqItem.`boq_id` = ' . $boqId, true);
            $boqHdObj = $dbObj->queryGet('SELECT `boqItem`.*, costCenter.CostCenter_code, costCenter.CostCenter_desc, costCenter.gl_code FROM `erp_boq_item_other` AS boqItem JOIN `erp_cost_center` AS costCenter ON costCenter.CostCenter_id = boqItem.cost_center_id WHERE boqItem.`head_type` != "other" AND boqItem.`boq_id` = ' . $boqId, true);

            // $boqOtherHeadObj = $dbObj->queryGet('SELECT `costCenter`.*, `boqItem`.* FROM  `erp_boq_item_other` AS boqItem JOIN `erp_cost_center` AS costCenter ON costCenter.CostCenter_id = boqItem.cost_center_id WHERE boqItem.`head_type`="other" AND boqItem.`boq_id`=' . $boqId, true);
            $boqOtherHeadObj = $dbObj->queryGet('SELECT `boqItem`.*, expenseOtherHead.head_name, expenseOtherHead.head_code, expenseOtherHead.head_gl, costCenter.CostCenter_code, costCenter.CostCenter_desc, costCenter.gl_code FROM  `erp_boq_item_other` AS boqItem JOIN `erp_cost_center` AS costCenter ON costCenter.CostCenter_id = boqItem.cost_center_id LEFT JOIN `erp_master_expense_other_head` AS expenseOtherHead ON boqItem.`head_id`=expenseOtherHead.`head_id` WHERE boqItem.`head_type`="other" AND boqItem.`boq_id`=' . $boqId, true);

            

            $result = [
                "status" => $boqObj["status"],
                "message" => $boqObj["message"],
                "data" => [
                    "boq_data" => $boqObj["data"],
                    "boq_service_data" => $boqServiceObj["data"],
                    "boq_material_data" => $boqMaterialObj["data"],
                    "boq_hd_data" => $boqHdObj['data'],
                    "boq_other_head_data" => $boqOtherHeadObj["data"],
                ]
            ];
            return $result;
        }
    }
}
