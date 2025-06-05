<?php
require_once("bom.controller.php");
//require_once("../../app/v1/functions/branch/func-production-order-controller.php");
class MrpController
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by, $dbObj;
    private $mrpPreviewDataPool = [];
    private $mrpPreviewStoragePool = [];
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
        $this->dbObj = new Database(true);
    }

    private function checkVirtualStocks($itemId = 0, $itemType = 0)
    {
        $itemTypeStr = $itemType == 1 ? "rmWhOpen" : ($itemType == 2 ? "sfgStockOpen" : "fgWhOpen");
        if (!isset($this->mrpPreviewStoragePool[$itemTypeStr . $itemId])) {
            $stockObj = $this->dbObj->queryGet('SELECT COALESCE(SUM(`itemQty`),0) AS totalQty FROM `erp_inventory_stocks_log` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `storageType`="' . $itemTypeStr . '" AND `itemId`=' . $itemId);
            $this->mrpPreviewStoragePool[$itemTypeStr . $itemId] = $stockObj["data"]["totalQty"];
        }
        return $this->mrpPreviewStoragePool[$itemTypeStr . $itemId];
    }
    private function updateVirtualStocks($itemId = 0, $itemType = 0, $updatedQty = 0)
    {
        $itemTypeStr = $itemType == 1 ? "rmWhOpen" : ($itemType == 2 ? "sfgStockOpen" : "fgWhOpen");
        $this->mrpPreviewStoragePool[$itemTypeStr . $itemId] = $updatedQty;
        if ($this->mrpPreviewStoragePool[$itemTypeStr . $itemId] == $updatedQty) {
            return true;
        } else {
            return false;
        }
    }

    private function mrpPreviewDataPool($row = [])
    {
        if (count($row) > 0) {
            $matchedKey = -1;
            foreach ($this->mrpPreviewDataPool as $key => $item) {
                if ($item["item_id"] == $row["item_id"]) {
                    $matchedKey = $key;
                }
            }
            if ($matchedKey >= 0) {
                $this->mrpPreviewDataPool[$matchedKey]["totalConsumption"] += $row["totalConsumption"];
                $this->mrpPreviewDataPool[$matchedKey]["extraRequired"] += $row["extraRequired"];
            } else {
                $this->mrpPreviewDataPool[] = $row;
            }
        } else {
            return $this->mrpPreviewDataPool;
        }
    }

    private function getProductionOrderDetails($prodOrderIdList = [])
    {
        $prodOrderIds = implode(",", $prodOrderIdList);
        return $this->dbObj->queryGet('SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.item_sell_type FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder,`' . ERP_INVENTORY_ITEMS . '` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`="' . $this->location_id . '" AND pOrder.so_por_id IN (' . $prodOrderIds . ')', true);
    }

    private function getBomItemList($itemId, $requiredQty = 1)
    {
        $data = $this->dbObj->queryGet('SELECT `erp_bom_item_material`.*, ((`erp_bom_item_material`.`consumption`+(`erp_bom_item_material`.`consumption`*`erp_bom_item_material`.`extra`/100))*' . $requiredQty . ') AS totalConsumption, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`,`erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus` FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`="active" AND `erp_bom`.`companyId`=' . $this->company_id . ' AND `erp_bom`.`branchId`=' . $this->branch_id . ' AND `erp_bom`.`locationId`=' . $this->location_id . ' AND `erp_bom`.`itemId` = ' . $itemId, true);
        // console($data);
        foreach ($data["data"] as $row) {
            //checking stocks
            $totalConsumption = $row["totalConsumption"];
            $stock = $this->checkVirtualStocks($row["item_id"], $row["goodsType"]);
            $extraRequired = $totalConsumption;
            if ($stock >= $totalConsumption) {
                $extraRequired = 0;
                $avaialbleStock = $stock - $totalConsumption;
            } else {
                $extraRequired = $totalConsumption - $stock;
                $avaialbleStock = 0;
            }
            $this->updateVirtualStocks($row["item_id"], $row["goodsType"], $avaialbleStock);
            $this->mrpPreviewDataPool($row + ["stock" => $stock, "extraRequired" => $extraRequired]);
            if ($row["bomStatus"] == 2) {
                $this->getBomItemList($row["item_id"], $extraRequired);
            }
        }
    }

    function previewMrp($prodOrderIdList = [])
    {
        $prodOrderItemListObj = $this->getProductionOrderDetails($prodOrderIdList);
        foreach ($prodOrderItemListObj["data"] as $oneProd) {
            $this->getBomItemList($oneProd["itemId"], $oneProd["qty"]);
        }
        if (count($this->mrpPreviewDataPool()) > 0) {
            return [
                "status" => "success",
                "message" => "Data fetched successfully",
                "mrpItems" => $prodOrderIdList,
                "bomItems" => $this->mrpPreviewDataPool()
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Data fetching failed",
                "mrpItems" => $prodOrderIdList,
                "bomItems" => $this->mrpPreviewDataPool()
            ];
        }
    }

    function subProductionOrder($list)
    {
        //  $productionOrderController = new ProductionOrderController();

        $post = [];
        foreach ($list as $item) {
            $itemCode = $item['itemCode'];
            $item_id = $item['itemId'];
            $item_qty = $item['itemQty'];
            $wcId = $item['wcId'];
            $tableId = $item['tableId'];
            $expDate = $item['expDate'];
            $status = 13;
            $refNo = $item['refNo'];
            $mrp_status = 'created';
            $subPorCode = 1;

            $insert_sub =   queryInsert("INSERT INTO `erp_sub_production_order` SET `company_id`=$this->company_id,`branch_id`=$this->branch_id,`location_id`=$this->location_id,`subPorCode`=$subPorCode,`itemId`=$item_id ,`itemCode`= '" . $itemCode . "',`parentOrder`='" . $refNo . "',`qty`=' . $item_qty . ',`remainQty`=' . $item_qty . ',`expectedDate`='" . $expDate . "',`mrp_status`='" . $mrp_status . "',`created_by`='" . $this->created_by . "',`updated_by`='" . $this->updated_by . "',`status`=$status, `wc_id`= $wcId, `table_id`=$tableId");
        }
        // console($post);
        // exit();



        // return $prod;
    }

    function confirmAndReleaseMrp($POST = [])
    {

        $this->dbObj->setSuccessMsg("Mrp released successfully");
        $this->dbObj->setErrorMsg("Mrp released failed, please try again");

        $rmRequiredDate = $POST["rmRequiredDate"] ?? "";
        $sfgRequiredDate = $POST["sfgRequiredDate"] ?? "";
        $fgRequiredDate = $POST["fgRequiredDate"] ?? "";

        $list = $POST['listItem'];
        //  $subProd =  $this->subProductionOrder($list);
        // console($subProd);
        // exit();

        //  if ($subProd['status'] == 'Success') {



        //   exit();




        // console($post);
        $productionOrdersIdArr = explode(",", base64_decode($POST["releaseOrderData"] ?? ""));
        // console($productionOrdersIdArr);

        $previewDataObj = $this->previewMrp($productionOrdersIdArr);
        // console($previewDataObj);

        // exit();

        $resultTestObj = [];
        $mrpCode = "MRP" . time();
        $purchaseRequestId = 0;

        $mrp_table = queryInsert('INSERT INTO `erp_mrp` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ', `mrpCode`= "'.$mrpCode.'" , `created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
        // console($mrp_table);
        // exit();


        foreach ($previewDataObj["bomItems"] as $oneItem) {
            $fromStrorageLoc = $oneItem["goodsType"] == 1 ? "rmWhOpen" : ($oneItem["goodsType"] == 2 ? "sfgStockOpen" : "fgWhOpen");
            // $toStrorageLoc = $oneItem["goodsType"] == 1 ? "rmWhReserve" : ($oneItem["goodsType"] == 2 ? "sfgStockReserve" : "fgWhReserve");
            $toStrorageLoc = "rmProdOpen";
            $stockLogTransferQty = 0;
            if ($oneItem["extraRequired"] > 0) {
                $requiredDate = $oneItem["goodsType"] == 2 ? $sfgRequiredDate : ($oneItem["goodsType"] == 3 ? $fgRequiredDate : $rmRequiredDate);
                $productionOrderCode = "PR" . time();

                if (in_array($oneItem["goodsType"], [2, 3])) {
                    //FG PRODUCTION ORDER
                    $this->dbObj->queryInsert('INSERT INTO `erp_production_order` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`porCode`="' . $productionOrderCode . '",`itemId`=' . $oneItem["item_id"] . ',`itemCode`="' . $oneItem["itemCode"] . '",`refNo`="' . $mrpCode . '",`qty`=' . $oneItem["extraRequired"] . ',`remainQty`=' . $oneItem["extraRequired"] . ',`expectedDate`="' . $requiredDate . '",`description`="' . $oneItem["itemName"] . '",`mrp_status`="Created",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '",`status`=13');
                    //Update the stock or transfer the stock

                } else {
                    //RM PURCHASE REQUEST
                    if ($purchaseRequestId == 0) {
                        //RM PURCHASE REQUEST GENERATION
                        $pr_date = date("Y-m-d");
                        $prCode = "PR" . time();
                        $purchaseReqObj = $this->dbObj->queryInsert('INSERT INTO `erp_branch_purchase_request` SET `prCode`="' . $prCode . '",`company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`expectedDate`="' . $requiredDate . '", `pr_date`="' . $pr_date . '", `pr_type`="material", `refNo`="' . $mrpCode . '",`pr_status`=9,`description`="' . $oneItem["itemName"] . '",`created_by`="' . $this->created_by . '",`updated_by`="' . $this->updated_by . '"');
                        $purchaseRequestId = $purchaseReqObj["insertedId"];
                    }
                    // RM PURCHASE REQUEST ITEMS INSERTION
                    $this->dbObj->queryInsert('INSERT INTO `erp_branch_purchase_request_items` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`prId`=' . $purchaseRequestId . ',`itemId`=' . $oneItem["item_id"] . ',`itemCode`="' . $oneItem["itemCode"] . '",`itemName`="' . $oneItem["itemName"] . '",`itemQuantity`=' . $oneItem["extraRequired"] . ', `remainingQty`=' . $oneItem["extraRequired"] . ', `uom`="' . $oneItem["uom"] . '",`itemPrice`=' . $oneItem["rate"] . ',`itemDiscount`="",`itemTotal`=' . $oneItem["rate"] * $oneItem["extraRequired"]);
                    //Update the stock and transfer stock
                }
                $stockLogTransferQty = $oneItem["stock"];
            } else {
                $stockLogTransferQty = $oneItem["totalConsumption"];
            }



            $stockLogTransferedQty = 0;
            if ($stockLogTransferQty > 0) {
                $stockLogObj = itemQtyStockChecking($oneItem["item_id"], "'$fromStrorageLoc'", ($oneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"));
                $itemAvailableStocks = $stockLogObj['sumOfBatches'];
                foreach ($stockLogObj["data"] as $stockLogKey => $stockLogRow) {
                    $resultTestObj["stockLogRow"][] = $stockLogRow;

                    if ($stockLogTransferedQty == $stockLogTransferQty) {
                        break;
                    }
                    if ($stockLogRow['itemQty'] == 0) {
                        continue;
                    }

                    $usedQuantity = min($stockLogRow['itemQty'], $stockLogTransferQty - $stockLogTransferedQty);
                    $stockLogTransferedQty += $usedQuantity;

                    $reserveStorageSql = 'SELECT storage_location_id, warehouse_id, storageLocationTypeSlug FROM `erp_storage_location` WHERE company_id=' . $this->company_id . ' AND branch_id=' . $this->branch_id . ' AND location_id=' . $this->location_id . ' AND warehouse_id=' . $stockLogRow['warehouse_id'] . ' AND storageLocationTypeSlug="' . $toStrorageLoc . '"';
                    $reserveStorageObj = $this->dbObj->queryGet($reserveStorageSql)['data'];

                    $minusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET 
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            storageLocationId = '" . $stockLogRow['storage_location_id'] . "',
                            storageType ='" . $stockLogRow['storageLocationTypeSlug'] . "',
                            itemId = '" . $oneItem["item_id"] . "',
                            itemQty = '" . $usedQuantity * -1 . "',
                            itemUom = '" . $stockLogRow["itemUom"] . "',
                            itemPrice = '" . $oneItem["rate"] . "',
                            refActivityName='MRP',
                            logRef = '" . $stockLogRow['logRef'] . "',
                            refNumber='" . $mrpCode . "',
                            bornDate='" . $stockLogRow['bornDate'] . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";

                    $plusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            storageLocationId = '" . $reserveStorageObj['storage_location_id'] . "',
                            refActivityName='MRP',
                            logRef = '" . $stockLogRow['logRef'] . "',
                            refNumber='" . $mrpCode . "',
                            bornDate='" . $stockLogRow['bornDate'] . "',
                            storageType ='" . $reserveStorageObj['storageLocationTypeSlug'] . "',
                            itemId = '" . $oneItem["item_id"] . "',
                            itemQty = '" . $usedQuantity . "',
                            itemUom = '" . $stockLogRow["itemUom"] . "',
                            itemPrice = '" .  $oneItem["rate"] . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";

                    $this->dbObj->queryInsert($minusStockSql);
                    $this->dbObj->queryInsert($plusStockSql);
                }
            }
        }

        foreach ($previewDataObj["mrpItems"] as $prodOrderId) {
            $this->dbObj->queryUpdate('UPDATE `erp_production_order` SET `mrp_status`="Created", `status`=13, `updated_by`="' . $this->updated_by . '" WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `so_por_id`=' . $prodOrderId);
        }
        $resultObj = $this->dbObj->queryFinish();
        return $resultObj;
        // } else {
        //     return $subProd;
        // }
    }

    function getProductionOrder($prodOrderIdList = [])
    {

        return $this->getProductionOrderDetails($prodOrderIdList);
    }
}
