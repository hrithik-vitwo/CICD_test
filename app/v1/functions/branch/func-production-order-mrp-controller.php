<?php

class MrpController extends GoodsBomController
{
    private $status = "success";
    private $message = "success";
    private $prodOrderIdsArr = [];
    private $rmList = [];
    private $sfgList = [];
    private $virtualStocks = [];

    private function stockCheck($itemId)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $returnData = [];
        if (isset($this->virtualStocks[$itemId])) {
            $returnData = $this->virtualStocks[$itemId];
        } else {
            $queryObj = queryGet('SELECT `itemOpenStocks`,`itemReserveStocks`,`itemTotalQty`,`movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id` = ' . $branch_id . ' AND `location_id`= ' . $location_id . ' AND `itemId`=' . $itemId);
            if ($queryObj["numRows"] == 1) {
                $this->virtualStocks[$itemId]["itemOpenStocks"] = floatval($queryObj["data"]["itemOpenStocks"]);
                $this->virtualStocks[$itemId]["itemReserveStocks"] = floatval($queryObj["data"]["itemReserveStocks"]);
                $this->virtualStocks[$itemId]["itemTotalQty"] = floatval($queryObj["data"]["itemTotalQty"]);
                $this->virtualStocks[$itemId]["movingWeightedPrice"] = floatval($queryObj["data"]["movingWeightedPrice"]);
                $this->virtualStocks[$itemId]["virtualStock"] = floatval($queryObj["data"]["itemOpenStocks"]);
                $returnData = $this->virtualStocks[$itemId];
            } else {
                $this->virtualStocks[$itemId] = ["itemOpenStocks" => 0, "itemReserveStocks" => 0, "itemTotalQty" => 0, "movingWeightedPrice" => 0, "virtualStock" => 0];
                $returnData = [
                    "itemOpenStocks" => 0, "itemReserveStocks" => 0, "itemTotalQty" => 0, "movingWeightedPrice" => 0, "virtualStock" => 0
                ];
            }
        }
        //console($returnData);
        return $returnData;
    }


    private function generateRmSfgItemList($bomItemList = [], $requiredItemQty = 1)
    {
        foreach ($bomItemList as $oneBomItem) {
            $itemId = $oneBomItem["itemId"];
            $itemCode = $oneBomItem["itemCode"];
            $itemConsumptionQty = $oneBomItem["itemConsumption"];
            $itemTotalConsumptionQty = $itemConsumptionQty * $requiredItemQty;

            $oneBomItem["totalItemConsumption"] = $itemTotalConsumptionQty;

            $stockData = $this->stockCheck($itemId);
            //console([$itemId, $stockData["virtualStock"], $itemTotalConsumptionQty, $stockData["virtualStock"]-$itemTotalConsumptionQty]);

            if ($oneBomItem["materialType"] == "RM") {

                if ($stockData["virtualStock"] >= $itemTotalConsumptionQty) {

                    $availableStock = $stockData["virtualStock"] - $itemTotalConsumptionQty;
                    $this->virtualStocks[$itemId]["virtualStock"] = $availableStock;
                } else {
                    $this->virtualStocks[$itemId]["virtualStock"] = 0;
                    $extraRequiredStocks = $itemTotalConsumptionQty - $stockData["virtualStock"];

                    // $this->rmList[$itemId] = $oneBomItem;
                    // $this->rmList[$itemId]["extraRequiredStocks"] = $extraRequiredStocks;
                    // $this->rmList[$itemId]["virtualStock"] = 0;
                    // $this->rmList[$itemId]["itemOpenStocks"] = $stockData["itemOpenStocks"];
                    $oneBomItem["extraRequiredStocks"] = $extraRequiredStocks;
                    $oneBomItem["virtualStock"] = 0;
                    $oneBomItem["itemOpenStocks"] = $stockData["itemOpenStocks"];
                    $oneBomItem["itemReserveStocks"] = $stockData["itemReserveStocks"];
                    if (isset($this->rmList[$itemId])) {
                        $this->rmList[$itemId]["totalItemConsumption"] += $itemTotalConsumptionQty;
                        $this->rmList[$itemId]["extraRequiredStocks"] += $extraRequiredStocks;
                        $this->rmList[$itemId]["virtualStock"] = 0;
                    } else {
                        $this->rmList[$itemId] = $oneBomItem;
                    }
                }
            } else {

                if ($stockData["virtualStock"] >= $itemTotalConsumptionQty) {
                    $availableStock = $stockData["virtualStock"] - $itemTotalConsumptionQty;
                    $this->virtualStocks[$itemId]["virtualStock"] = $availableStock;
                } else {
                    $this->virtualStocks[$itemId]["virtualStock"] = 0;
                    $extraRequiredStocks = $itemTotalConsumptionQty - $stockData["virtualStock"];

                    // $this->sfgList[$itemId] = $oneBomItem;
                    // $this->sfgList[$itemId]["extraRequiredStocks"] = $extraRequiredStocks;
                    // $this->sfgList[$itemId]["virtualStock"] = 0;
                    // $this->sfgList[$itemId]["itemOpenStocks"] = $stockData["itemOpenStocks"];

                    $oneBomItem["extraRequiredStocks"] = $extraRequiredStocks;
                    $oneBomItem["virtualStock"] = 0;
                    $oneBomItem["itemOpenStocks"] = $stockData["itemOpenStocks"];
                    $oneBomItem["itemReserveStocks"] = $stockData["itemReserveStocks"];
                    if (isset($this->sfgList[$itemId])) {
                        $this->sfgList[$itemId]["totalItemConsumption"] += $itemTotalConsumptionQty;
                        $this->sfgList[$itemId]["extraRequiredStocks"] += $extraRequiredStocks;
                        $this->sfgList[$itemId]["virtualStock"] = 0;
                    } else {
                        $this->sfgList[$itemId] = $oneBomItem;
                    }
                }


                $bomDetailsAndItemsObj = $this->getBomAndItemDetails($itemId);
                if (count($bomDetailsAndItemsObj["data"]) > 0) {
                    $bomItemDetails = $bomDetailsAndItemsObj["data"]["bomItemDetails"];
                    //$this->generateRmSfgItemList($bomItemDetails, $itemTotalConsumptionQty);
                    $this->generateRmSfgItemList($bomItemDetails, $extraRequiredStocks);
                } else {
                    $this->status = "warning";
                    $this->message = "Bom Details not found for item " . $itemCode;
                }
            }
        }
    }

    private function getProductionOrderDetails($prodOrderIdList = [])
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $prodOrderIds = implode(",", $prodOrderIdList);
        return queryGet('SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.itemOpenStocks, items.itemBlockStocks FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder,`' . ERP_INVENTORY_ITEMS . '` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`="' . $location_id . '" AND pOrder.so_por_id IN (' . $prodOrderIds . ')', true);
    }

    public function generateMrpPreview($productionOrderIdArr = [])
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $this->prodOrderIdsArr = $productionOrderIdArr;

        $prodOrderDetailsObj = $this->getProductionOrderDetails($productionOrderIdArr);

        if ($prodOrderDetailsObj["status"] != "success") {
            return [
                "status" => "warning",
                "message" => "Invalid production order id",
            ];
        }

        //console($prodOrderDetailsObj);
        foreach ($prodOrderDetailsObj["data"] as $oneProdOrderDetails) {
            $itemId = $oneProdOrderDetails["itemId"];
            $requiredItemQty = $oneProdOrderDetails["qty"];
            $bomDetailsAndItemsObj = $this->getBomAndItemDetails($itemId);
            $bomItemDetails = $bomDetailsAndItemsObj["data"]["bomItemDetails"];
            $this->generateRmSfgItemList($bomItemDetails, $requiredItemQty);
        }
    }

    public function mrpPreview()
    {

        if ($this->status == "success") {
            return [
                "status" => $this->status,
                "message" => $this->message,
                "prodOrderIdsArr" => $this->prodOrderIdsArr,
                "sfgItemsList" => $this->sfgList,
                "rmItemsList" => $this->rmList,
                "stockDetails" => $this->virtualStocks
            ];
        } else {
            return [
                "status" => $this->status,
                "message" => $this->message,
                "prodOrderIdsArr" => $this->prodOrderIdsArr,
                "sfgItemsList" => $this->sfgList,
                "rmItemsList" => $this->rmList
            ];
        }
    }




    private function updateComfirmMrpOpenStocks($item)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $itemId = $item["itemId"];

        $checkStocksSummaryObj = queryGet('SELECT `stockSummaryId`,`itemOpenStocks`, `itemReserveStocks`, `itemTotalQty` FROM `erp_inventory_stocks_summary` WHERE `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);

        if ($checkStocksSummaryObj["numRows"] == 1) {

            $stockSummaryId = $checkStocksSummaryObj["data"]["stockSummaryId"];
            $itemOpenStocks = $checkStocksSummaryObj["data"]["itemOpenStocks"];
            $itemReserveStocks = $checkStocksSummaryObj["data"]["itemReserveStocks"];
            $itemTotalQty = $checkStocksSummaryObj["data"]["itemTotalQty"];

            $updatedReserveStocks = $itemReserveStocks + $itemOpenStocks;
            $updatedOpenStocks = 0;
            $updatedTotalQty = floatval($itemTotalQty) + $itemOpenStocks;

            $updateStockSummaryObj = queryUpdate('UPDATE `erp_inventory_stocks_summary` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`itemId`=' . $itemId . ',`itemOpenStocks`=' . $updatedOpenStocks . ',`itemReserveStocks`=' . $updatedReserveStocks . ',`itemTotalQty`=' . $updatedTotalQty . ', `updatedBy`="' . $updated_by . '" WHERE `stockSummaryId`=' . $stockSummaryId);

            if ($updateStockSummaryObj["status"] == "success") {
                return true;
            }
        } else {
            $insertStockSummaryObj = queryInsert('INSERT INTO `erp_inventory_stocks_summary` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`itemId`=' . $itemId . ',`itemOpenStocks`=0,`itemReserveStocks`=0,`itemTotalQty`=0,`movingWeightedPrice`=0,`bomStatus`=0,`createdBy`="' . $created_by . '",`updatedBy`="' . $updated_by . '"');
            if ($insertStockSummaryObj["status"] == "success") {
                return true;
            }
        }

        return false;
    }

    public function confirmMrp($mrpPreviewData = [], $requiredDate = null)
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        // console($mrpPreviewData);
        // exit();

        $prodOrderIdsArr = $mrpPreviewData["prodOrderIdsArr"];
        $sfgItemsList = $mrpPreviewData["sfgItemsList"];
        $rmItemsList = $mrpPreviewData["rmItemsList"];

        $mrpCode = "MRP" . time();


        $errorsInSfgItem = 0;
        $errorsInRmItem = 0;
        $errorsInPurchaseRequest = 0;

        //prepare for production request
        if (count($sfgItemsList) > 0) {
            foreach ($sfgItemsList as $oneSfg) {
                $itemId = $oneSfg["itemId"];
                $itemCode = $oneSfg["itemCode"];
                $itemName = $oneSfg["itemName"];
                $goodsType = $oneSfg["goodsType"];
                $itemUOM = $oneSfg["itemUOM"];
                $extraRequiredStocks = $oneSfg["extraRequiredStocks"];

                $expectedDate = $requiredDate;
                $productionOrderCode = "PR" . time();

                if ($extraRequiredStocks > 0) {
                    $insertProductionOrderObj = queryInsert('INSERT INTO `erp_production_order` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`porCode`="' . $productionOrderCode . '",`itemId`=' . $itemId . ',`itemCode`="' . $itemCode . '",`refNo`="' . $mrpCode . '",`qty`=' . $extraRequiredStocks . ',`expectedDate`="' . $expectedDate . '",`description`="' . $itemName . '",`mrp_status`="Created",`created_by`="' . $created_by . '",`updated_by`="' . $updated_by . '",`status`=13');
                    if ($insertProductionOrderObj["status"] == "success") {
                        $this->updateComfirmMrpOpenStocks($oneSfg);
                    } else {
                        $errorsInSfgItem++;
                    }
                }
            }
        }


        //prepare for purchase request
        if (count($rmItemsList) > 0) {
            $expectedDate = $requiredDate;
            $pr_date = date("Y-m-d");
            $prCode = "PR" . time();
            $itemName = "";

            $purchaseRequestInsertObj = queryInsert('INSERT INTO `erp_branch_purchase_request` SET `prCode`="' . $prCode . '",`company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`expectedDate`="' . $expectedDate . '", `pr_date`="' . $pr_date . '", `pr_type`="material", `refNo`="' . $mrpCode . '",`pr_status`=9,`description`="' . $itemName . '",`created_by`="' . $created_by . '",`updated_by`="' . $updated_by . '"');

            if ($purchaseRequestInsertObj["status"] == "success") {
                $purchaseRequestId = $purchaseRequestInsertObj["insertedId"];
                foreach ($rmItemsList as $oneRm) {
                    $itemId = $oneRm["itemId"];
                    $itemCode = $oneRm["itemCode"];
                    $itemName = $oneRm["itemName"];
                    $goodsType = $oneRm["goodsType"];
                    //$itemUOM = $oneRm["itemUOM"];
                    $itemUOM = 1;
                    $extraRequiredStocks = $oneRm["extraRequiredStocks"];
                    $itemRate = $oneRm["itemRate"];
                    $totalAmount = $oneRm["itemRate"] * $oneRm["extraRequiredStocks"];

                    if ($extraRequiredStocks > 0) {
                        $prItemInsertObj = queryInsert('INSERT INTO `erp_branch_purchase_request_items` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`location_id`=' . $location_id . ',`prId`=' . $purchaseRequestId . ',`itemId`=' . $itemId . ',`itemCode`="' . $itemCode . '",`itemName`="' . $itemName . '",`itemQuantity`=' . $extraRequiredStocks . ', `remainingQty`=' . $extraRequiredStocks . ', `uom`=' . $itemUOM . ',`itemPrice`=' . $itemRate . ',`itemDiscount`="",`itemTotal`=' . $totalAmount);

                        // INSERT INTO `erp_branch_purchase_request_items` SET `prItemId`='[value-1]',`company_id`='[value-2]',`branch_id`='[value-3]',`location_id`='[value-4]',`prId`='[value-5]',`itemId`='[value-6]',`itemCode`='[value-7]',`itemName`='[value-8]',`itemQuantity`='[value-9]',`remainingQty`='[value-10]',`uom`='[value-11]',`itemNote`='[value-12]',`itemPrice`='[value-13]',`itemDiscount`='[value-14]',`itemTotal`='[value-15]',`createdAt`='[value-16]',`updatedAt`='[value-17]' WHERE 1

                        if ($prItemInsertObj["status"] == "success") {
                            $this->updateComfirmMrpOpenStocks($oneRm);
                        } else {
                            $errorsInRmItem++;
                        }
                    }
                }
            } else {
                $errorsInPurchaseRequest++;
            }
        }


        if ($errorsInSfgItem == 0 && $errorsInRmItem == 0 && $errorsInPurchaseRequest == 0) {

            $prodOrderIdsString = implode(",", $prodOrderIdsArr);

            $finalProdOrdUpdateObj = queryUpdate('UPDATE `erp_production_order` SET `mrp_status`="Created", `status`=13, `updated_by`="' . $updated_by . '" WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `so_por_id` IN ("' . $prodOrderIdsString . '")');
            if ($finalProdOrdUpdateObj["status"] == "success") {
                return [
                    "status" => "success",
                    "message" => "Production order successfully released"
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "Production order released failed, please try again!"
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "Production order released failed, please try again!"
            ];
        }

        // console($sfgItemsList);
        // console($rmItemsList);

    }
}
