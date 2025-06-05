<?php
class GoodsBomController
{

    function getBomFullDetails($itemId = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        $bomDeatailsObj = queryGet('SELECT `erp_bom`.* FROM `erp_bom` WHERE `locationId`=' . $location_id . ' AND `itemId`=' . $itemId . ' AND `bomStatus`!="deleted" ORDER BY `bomId` DESC');
        if ($bomDeatailsObj["status"] == 'success') {
            if ($bomDeatailsObj["data"]["itemType"] == "goods") {
                $bomGoodDeatailsObj = queryGet('SELECT * FROM `erp_inventory_items` WHERE `itemId`=' . $itemId . ' ORDER BY `itemId` DESC');
                $bomDetails = $bomDeatailsObj["data"];
                $bomDetails["itemCode"] = $bomGoodDeatailsObj["data"]["itemCode"] ?? "";
                $bomDetails["itemName"] = $bomGoodDeatailsObj["data"]["itemName"] ?? "";
                $bomDetails["parentGlId"] = $bomGoodDeatailsObj["data"]["parentGlId"] ?? "";
            }

            // $bomItemsObj = queryGet('');

            return [
                "status" => "success",
                "message" => "Bom Details found successfully",
                "data" => [
                    "bomDetails" => $bomDetails,
                    "bomItems" => [],
                    "bomActivities" => [],
                    "bomCogsItems" => [],
                    "bomDiscount" => "",
                    "bomMargins" => ""
                ]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Bom not found, or not created",
                "bomStatus" => "notCreated",
                "data" => [],
            ];
        }
    }

    function getBomItemsAndDetails($bomId = null)
    {
        return queryGet('SELECT * FROM `' . ERP_BOM_ITEMS . '` WHERE `bomId`="' . $bomId . '"', true);
    }
    function getBomAndAllItems($itemId = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        $bomDeatailsObj = queryGet('SELECT `erp_bom`.* FROM `erp_bom` WHERE `locationId`=' . $location_id . ' AND `itemId`=' . $itemId . ' AND `bomStatus`!="deleted" ORDER BY `bomId` DESC');
        if ($bomDeatailsObj["status"] == 'success') {
            if ($bomDeatailsObj["data"]["itemType"] == "goods") {
                $bomGoodDeatailsObj = queryGet('SELECT * FROM `erp_inventory_items` WHERE `itemId`=' . $itemId . ' ORDER BY `itemId` DESC');
                $bomDetails = $bomDeatailsObj["data"];
                $bomDetails["itemCode"] = $bomGoodDeatailsObj["data"]["itemCode"] ?? "";
                $bomDetails["itemName"] = $bomGoodDeatailsObj["data"]["itemName"] ?? "";
                $bomDetails["parentGlId"] = $bomGoodDeatailsObj["data"]["parentGlId"] ?? "";
            }


            $bomItemsSql = 'SELECT
                                bomItems.*,
                                items.itemName,
                                items.itemCode,
                                costCenters.CostCenter_code,
                                costCenters.CostCenter_desc,
                                costCenters.gl_code AS costCenterGlCode,
                                costCenters.parent_id AS costCenterGl
                            FROM
                                `erp_bom_items` AS bomItems
                            LEFT JOIN `erp_inventory_items` AS items
                            ON
                                bomItems.itemId = items.itemId
                            LEFT JOIN `erp_cost_center` AS costCenters
                            ON
                                bomItems.activityId = costCenters.CostCenter_id
                            WHERE
                                bomItems.bomId='.$bomDetails["bomId"];
            

            return [
                "status" =>"success",
                "message" =>"Bom Details fetched successfully",
                "bomStatus" =>"created",
                "data" =>[
                    "bomDetails" =>$bomDetails,
                    "bomItemDetails" =>queryGet($bomItemsSql, true)["data"]
                ]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Bom not found, or not created",
                "bomStatus" => "notCreated",
                "data" => [],
            ];
        }
    }
    function getBomAndAllItemsByBomId($bomId = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        $bomDeatailsObj = queryGet('SELECT `erp_bom`.* FROM `erp_bom` WHERE `locationId`=' . $location_id . ' AND `bomId`=' . $bomId . ' AND `bomStatus`!="deleted" ORDER BY `bomId` DESC');
        if ($bomDeatailsObj["status"] == 'success') {
            if ($bomDeatailsObj["data"]["itemType"] == "goods") {
                $bomDetails = $bomDeatailsObj["data"];

                $bomGoodDeatailsObj = queryGet('SELECT * FROM `erp_inventory_items` WHERE `itemId`=' . $bomDetails["itemId"] . ' ORDER BY `itemId` DESC');
                $bomDetails["itemCode"] = $bomGoodDeatailsObj["data"]["itemCode"] ?? "";
                $bomDetails["itemName"] = $bomGoodDeatailsObj["data"]["itemName"] ?? "";
                $bomDetails["parentGlId"] = $bomGoodDeatailsObj["data"]["parentGlId"] ?? "";
            }


            $bomItemsSql = 'SELECT
                                bomItems.*,
                                items.itemName,
                                items.itemCode,
                                costCenters.CostCenter_code,
                                costCenters.CostCenter_desc,
                                costCenters.gl_code AS costCenterGlCode,
                                costCenters.parent_id AS costCenterGl
                            FROM
                                `erp_bom_items` AS bomItems
                            LEFT JOIN `erp_inventory_items` AS items
                            ON
                                bomItems.itemId = items.itemId
                            LEFT JOIN `erp_cost_center` AS costCenters
                            ON
                                bomItems.activityId = costCenters.CostCenter_id
                            WHERE
                                bomItems.bomId='.$bomDetails["bomId"];
            

            return [
                "status" =>"success",
                "message" =>"Bom Details fetched successfully",
                "bomStatus" =>"created",
                "data" =>[
                    "bomDetails" =>$bomDetails,
                    "bomItemDetails" =>queryGet($bomItemsSql, true)["data"]
                ]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Bom not found, or not created",
                "bomStatus" => "notCreated",
                "data" => [],
            ];
        }
    }


    function updateCurrentBomItemPrice($bomIn=null){
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        $bomItemDetailSql = 'SELECT
                                *
                            FROM
                                `erp_bom_items` AS bomItems
                            LEFT JOIN `erp_inventory_stocks_summary` AS itemStocks
                            ON
                                bomItems.itemId = itemStocks.itemId AND itemStocks.location_id = '.$location_id.'
                            WHERE
                                bomItems.`bomId` = '.$bomIn;
        
        $bomItemDetailsObj = queryGet($bomItemDetailSql, true);

        $updateErr = 0;
        foreach($bomItemDetailsObj["data"] as $oneItem){
            if($oneItem["bomItemType"] == "goods"){
                $itemConsumption = $oneItem["itemConsumption"];
                $itemExtraPurchage = $oneItem["itemExtraPurchage"];
                $totalQty = $itemConsumption+($itemConsumption*$itemExtraPurchage/100);

                $prevLog = json_decode(base64_decode($oneItem["amountLogs"]), true) ?? [];
                array_push($prevLog,[
                    "date" => date("Y-m-d H:i:s"),
                    "user" => $created_by,
                    "totalQty" => $totalQty,
                    "priceRate" => $oneItem["movingWeightedPrice"],
                    "totalPrice" => ($oneItem["movingWeightedPrice"]*$totalQty)
                ]);

                $updateSql = 'UPDATE
                                `erp_bom_items`
                            SET
                                `itemRate` = '.($oneItem["movingWeightedPrice"]).',
                                `amount` = '.($oneItem["movingWeightedPrice"]*$totalQty).',
                                `amountLogs` = "'.base64_encode(json_encode($prevLog, true)).'",
                                `updatedBy` = "'.$created_by.'"
                            WHERE
                                `bomItemId` = '.$oneItem["bomItemId"];
                $updateObj = queryUpdate($updateSql);
                if($updateObj["status"]!="success"){
                    $updateErr++;
                }
            }
        }

        if($updateErr==0){
            return [
                "status" => "success",
                "message" => "Updated successfully"
            ];
        }else{
            return [
                "status" => "warning",
                "message" => "Something went wrong, try again"
            ];
        }
    }   


    function getBomAndItemDetails($itemId = null)
    {

        global $company_id;
        global $branch_id;
        global $location_id;

        if ($itemId != "" && $itemId > 0) {

            $bomDetailSql = 'SELECT * FROM `' . ERP_BOM . '` WHERE `itemId`=' . $itemId . ' AND `locationId`="' . $location_id . '" AND `bomStatus`!="deleted"';
            $bomDetails = queryGet($bomDetailSql);

            if ($bomDetails["status"] == "success") {
                //$bomItemDetails = queryGet('SELECT bomItems.*,items.itemCode, items.itemName, items.itemOpenStocks FROM `'.ERP_BOM_ITEMS.'` as bomItems, `'.ERP_INVENTORY_ITEMS.'` as items WHERE bomItems.itemId=items.itemId AND bomItems.`bomItemType`="goods" AND bomItems.`bomId`="'.$bomDetails["data"]["bomId"].'"', true);
                $bomItemDetailSql = 'SELECT bomItems.*,items.itemCode, items.itemName, items.goodsType, itemTypes.type as materialType FROM `' . ERP_BOM_ITEMS . '` as bomItems, `' . ERP_INVENTORY_ITEMS . '` as items, `erp_inventory_mstr_good_types` as itemTypes WHERE bomItems.itemId=items.itemId AND bomItems.`bomItemType`="goods" AND items.`goodsType` = itemTypes.`goodTypeId` AND bomItems.`bomId`="' . $bomDetails["data"]["bomId"] . '"';
                $bomItemDetails = queryGet($bomItemDetailSql, true);
            }
            return [
                "status" => "success",
                "message" => "Records Found",
                "data" => [
                    "bomDetails" => $bomDetails["data"],
                    "bomItemDetails" => $bomItemDetails["data"]
                ],
                "SqlBom" => $bomDetailSql,
                "SqlBomItemSql" => $bomItemDetailSql
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Record not Found",
                "data" => []
            ];
        }
    }

    function getAllBoms($itemId = null)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        if ($itemId != "" && $itemId > 0) {
            $sql = 'SELECT bom.*, items.itemCode, items.itemName  FROM `erp_bom` AS bom, `erp_inventory_items` AS items WHERE bom.`itemId`=' . $itemId . ' AND (bom.`locationId`="' . $location_id . '" OR bom.`branchId`="' . $branch_id . '" OR bom.`companyId`="' . $company_id . '") AND bom.`bomStatus`!="deleted" AND bom.itemId=items.itemId';
            // return queryGet('SELECT * FROM `'.ERP_BOM.'` WHERE `itemId`='.$itemId.' AND (`locationId`="'.$location_id.'"  OR `branchId`="'.$branch_id.'" OR `companyId`="'.$company_id.'") AND `bomStatus`!="deleted"', true);
            return queryGet($sql, true);
        } else {
            $sql = 'SELECT bom.*, items.itemCode, items.itemName  FROM `erp_bom` AS bom, `erp_inventory_items` AS items WHERE (bom.`locationId`="' . $location_id . '" OR bom.`branchId`="' . $branch_id . '" OR bom.`companyId`="' . $company_id . '") AND bom.`bomStatus`!="deleted" AND bom.itemId=items.itemId';
            // return queryGet('SELECT * FROM `'.ERP_BOM.'` WHERE (`locationId`="'.$location_id.'"  OR `branchId`="'.$branch_id.'" OR `companyId`="'.$company_id.'") AND `bomStatus`!="deleted"', true);
            return queryGet($sql, true);
        }
    }

    function isBomCreated($itemId = null)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $sql = 'SELECT `itemId` FROM `erp_bom` WHERE `itemId`=' . $itemId . ' AND `locationId`=' . $location_id . ' AND `bomStatus`="active"';
        $bomList = queryGet($sql, true);
        if ($bomList["numRows"] < 1) {
            return false;
        } else {
            return true;
        }
    }

    function createBom($INPUTS)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $returnData = [];
        $isValidate = validate($INPUTS, [
            "preparedBy" => "required",
            "preparedDate" => "required",
            "itemId" => "required",
            "goodItemId" => "array",
            "goodItemConsumption" => "array",
            "goodItemExtraPurchage" => "array",
            "goodItemUOM" => "array",
            "goodItemRate" => "array",
            "goodItemAmount" => "array",
            "goodItemRemark" => "array",
            "goodActivityId" => "array",
            "goodActivityConsumption" => "array",
            "goodActivityLhr" => "array",
            "goodActivityMhr" => "array",
            "goodActivityLhrMhr" => "array",
            "goodActivityAmount" => "array",
            "goodActivityRemark" => "array",
            "goodOthersItem" => "array",
            "goodOthersAmount" => "array",
            "goodOthersRemark" => "array"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        //$test = $INPUTS["test"];
        $itemId = $INPUTS["itemId"];
        $itemType = "goods";
        $preparedBy = $INPUTS["preparedBy"];
        $preparedDate = $INPUTS["preparedDate"];

        $goodItemGl = $INPUTS["goodItemGl"] ?? [];
        $goodItemId = $INPUTS["goodItemId"];
        $goodItemConsumption = $INPUTS["goodItemConsumption"];
        $goodItemExtraPurchage = $INPUTS["goodItemExtraPurchage"];
        $goodItemUOM = $INPUTS["goodItemUOM"];
        $goodItemRate = $INPUTS["goodItemRate"];
        $goodItemAmount = $INPUTS["goodItemAmount"];
        $goodItemRemark = $INPUTS["goodItemRemark"];

        $goodActivityItemGl = $INPUTS["goodActivityItemGl"] ?? [];
        $goodActivityId = $INPUTS["goodActivityId"];
        $goodActivityConsumption = $INPUTS["goodActivityConsumption"];
        $goodActivityLhr = $INPUTS["goodActivityLhr"];
        $goodActivityMhr = $INPUTS["goodActivityMhr"];
        $goodActivityAmount = $INPUTS["goodActivityAmount"];
        $goodActivityRemark = $INPUTS["goodActivityRemark"];

        $goodOthersItemGl = $INPUTS["goodOthersItemGl"] ?? [];
        $goodOthersItem = $INPUTS["goodOthersItem"];
        $goodOthersAmount = $INPUTS["goodOthersAmount"];
        $goodOthersRemark = $INPUTS["goodOthersRemark"];


        $cogm = "";
        $cogs = "";
        $msp = "";
        $bomProgressStatus = "COGM";

        // console($INPUTS);
        // echo "<br>";

        $createBomObj = queryInsert('INSERT INTO `' . ERP_BOM . '` SET `companyId`="' . $company_id . '",`branchId`="' . $branch_id . '",`locationId`="' . $location_id . '",`itemId`="' . $itemId . '",`itemType`="' . $itemType . '",`preparedBy`="' . $preparedBy . '",`preparedDate`="' . $preparedDate . '",`cogm`="' . $cogm . '",`cogs`="' . $cogs . '",`msp`="' . $msp . '",`createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '",`bomProgressStatus`="' . $bomProgressStatus . '"');


        if ($createBomObj["status"] == "success") {

            $grandTotalAmount = 0;

            $bomId = $createBomObj["insertedId"];

            foreach ($goodItemId as $key => $oneGood) {
                $bomItemType = "goods";
                $oneGoodGl = $goodItemGl[$key] ?? 0;
                $oneGoodId = $goodItemId[$key];
                $consumption = $goodItemConsumption[$key];
                $extraPurchage = $goodItemExtraPurchage[$key];
                $oneGoodsUOM = $goodItemUOM[$key];
                $oneGoodItemRate = $goodItemRate[$key];
                $totalAmount = $goodItemAmount[$key];
                $oneRemarks = $goodItemRemark[$key];

                $grandTotalAmount += $totalAmount;
                $sql = 'INSERT INTO `' . ERP_BOM_ITEMS . '` SET `bomId`="' . $bomId . '",`bomItemType`="' . $bomItemType . '",`itemId`="' . $oneGoodId . '",`itemConsumption`="' . $consumption . '",`itemExtraPurchage`="' . $extraPurchage . '",`itemUOM`="' . $oneGoodsUOM . '",`itemRate`="' . $oneGoodItemRate . '", `itemGl`='.$oneGoodGl.', `amount`="' . $totalAmount . '",`remarks`="' . $oneRemarks . '",`createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';
                //echo "<br>";
                queryInsert($sql);
            }

            foreach ($goodActivityId as $key => $oneGoodActivity) {
                $bomItemType = "activities";
                $oneActivityGl = $goodActivityItemGl[$key] ?? 0;
                $oneActivityId = $goodActivityId[$key];
                $consumption = $goodActivityConsumption[$key];
                $lhr = $goodActivityLhr[$key];
                $mhr = $goodActivityMhr[$key];
                $totalAmount = $goodActivityAmount[$key];
                $oneRemarks = $goodActivityRemark[$key];

                $grandTotalAmount += $totalAmount;
                $sql = 'INSERT INTO `' . ERP_BOM_ITEMS . '` SET `bomId`="' . $bomId . '",`bomItemType`="' . $bomItemType . '",`activityId`="' . $oneActivityId . '",`activityConsumption`="' . $consumption . '",`activityLhr`="' . $lhr . '",`activityMhr`="' . $mhr . '", `itemGl`='.$oneActivityGl.', `amount`="' . $totalAmount . '",`remarks`="' . $oneRemarks . '",`createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';
                // echo "<br>";
                queryInsert($sql);
            }

            foreach ($goodOthersItem as $key => $oneGoodOthersItem) {
                $bomItemType = "others";
                $otherItemGl = $goodOthersItemGl[$key] ?? 0;
                $otherItem = $goodOthersItem[$key];
                $totalAmount = $goodOthersAmount[$key];
                $oneRemarks = $goodOthersRemark[$key];

                $grandTotalAmount += $totalAmount;

                $sql = 'INSERT INTO `' . ERP_BOM_ITEMS . '` SET `bomId`="' . $bomId . '",`bomItemType`="' . $bomItemType . '",`othersItem`="' . $otherItem . '", `itemGl`='.$otherItemGl.', `amount`="' . $totalAmount . '",`remarks`="' . $oneRemarks . '",`createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"';
                //echo "<br>";
                queryInsert($sql);
            }

            $sql = 'UPDATE `' . ERP_BOM . '` SET `cogm`="' . $grandTotalAmount . '" WHERE `bomId`=' . $bomId;
            queryUpdate($sql);
            $updateMovingWeightedPriceSql = 'UPDATE
                                                `erp_inventory_stocks_summary`
                                            SET
                                                `movingWeightedPrice` = '.$grandTotalAmount.',
                                                `updatedBy` = "'.$updated_by.'"
                                            WHERE
                                                `company_id` = '.$company_id.' AND `branch_id` = '.$branch_id.' AND `location_id` = '.$location_id.' AND `itemId` = '.$itemId;
            queryUpdate($updateMovingWeightedPriceSql);
        }
        return $createBomObj;
    }

    function createBomCOGS($INPUTS)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        //console($INPUTS);

        $isValidate = validate($INPUTS, [
            "bomId" => "required",
            "bomOtherAddonItemName" => "array",
            "bomOtherAddonItemGl" => "array",
            "bomOtherAddonItemPrice" => "array",
            "bomOtherAddonItemRemarks" => "array"
        ]);

        if ($isValidate["status"] != "success") {
            $returnData['status'] = "warning";
            $returnData['message'] = "Invalid form inputes";
            $returnData['errors'] = $isValidate["errors"];
            return $returnData;
        }

        $nameList = $INPUTS["bomOtherAddonItemName"];
        $glList = $INPUTS["bomOtherAddonItemGl"];
        $priceList = $INPUTS["bomOtherAddonItemPrice"];
        $remarksList = $INPUTS["bomOtherAddonItemRemarks"];
        $bomId = $INPUTS["bomId"];




        $cogsTotalAmount = 0;
        foreach ($nameList as $key => $val) {
            $name = $nameList[$key];
            $itemGl = $glList[$key];
            $price = $priceList[$key];
            $remarks = $remarksList[$key];

            $addCogsItemObj = queryInsert('INSERT INTO `erp_bom_items` SET `bomId`="' . $bomId . '",`bomItemType`="othersCogs", `othersItem`="' . $name . '",`amount`="' . $price . '", `itemGl`='.$itemGl.', `remarks`="' . $remarks . '",`createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"');
            if ($addCogsItemObj["status"] == "success") {
                $cogsTotalAmount += $price;
            }
        }
        if ($cogsTotalAmount > 0) {
            $updateBomCogsObj = queryUpdate('UPDATE `erp_bom` SET `cogs`=`cogm`+' . $cogsTotalAmount . ', `bomProgressStatus`="COGS", `updatedBy`="' . $updated_by . '" WHERE `bomId`="' . $bomId . '"');
            if ($updateBomCogsObj["status"] == "success") {
                return [
                    "status" => "success",
                    "message" => "COGS created successfully"
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "COGS created failed, please try again"
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "COGS created failed, please try again!"
            ];
        }
    }
}
