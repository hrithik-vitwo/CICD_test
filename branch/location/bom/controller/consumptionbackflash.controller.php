<?php
require_once("bom.controller.php");
class ConsumptionBackFlashController extends Accounting
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by, $dbObj, $bomControllerObj;
    private $mainProductionOrderId;
    private $productionDeclareDate;
    private $declearItemMfgDate;
    private $PROD_DECLARE_CODE;
    private $isAllRmStockAvailable = true;
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
        $this->dbObj->setSuccessMsg("Production order back flash successfull");
        $this->dbObj->setErrorMsg("Production order back flash failed");
        $this->bomControllerObj = new BomController();
    }

    private function check($productionOrderId)
    {
        $productionOrderObj =  $this->dbObj->queryGet("SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.item_sell_type FROM `" . ERP_PRODUCTION_ORDER . "` AS pOrder,`erp_inventory_items` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`=$this->location_id AND pOrder.so_por_id=$productionOrderId");

        $productionOrderCode = $productionOrderObj["data"]["porCode"];
        $productionOrderItemId = $productionOrderObj["data"]["itemId"];
        $productionOrderRefNo = $productionOrderObj["data"]["refNo"];
        $productionOrderMrpStatus = $productionOrderObj["data"]["mrp_status"];
        $productionOrderStatus = $productionOrderObj["data"]["status"];

        // console($productionOrderObj);

        if ($productionOrderMrpStatus != "Created" && $productionOrderStatus != 13) {
            return [
                "status" => "warning",
                "message" => "You can't do consumption posting, production order is not released",
                "data" => $productionOrderObj
            ];
        }

        $subProductionCountObj = $this->dbObj->queryGet("SELECT COUNT(`sub_prod_id`) totalSubProductionNo FROM `erp_production_order_sub` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `location_id`=$this->location_id AND `prodCode`='$productionOrderRefNo'");
        // console($subProductionCountObj);
        $subProductionCount = $subProductionCountObj["data"]["totalSubProductionNo"] ?? 0;
        if ($subProductionCount > 0) {
            return [
                "status" => "warning",
                "message" => "You can't do the backflash on subproduction.",
                "count" => $subProductionCount,
                "data" => $subProductionCountObj
            ];
        }

        $splitedCountObj = $this->dbObj->queryGet("SELECT COUNT(`sub_prod_id`) totalSubProductionNo FROM `erp_production_order_sub` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `location_id`=$this->location_id AND `itemId`=$productionOrderItemId");
        // console($splitedCountObj);
        $splitedCount = $splitedCountObj["data"]["totalSubProductionNo"] ?? 0;
        if ($splitedCount > 1) {
            return [
                "status" => "warning",
                "message" => "You can't do the backflash on splited production order.",
                "data" => $splitedCountObj
            ];
        }


        return [
            "status" => "success",
            "message" => "Basic production order check successful",
            "data" => $productionOrderObj["data"]
        ];
    }


    private function consumeAndProduce($subProductionOrder, $bomDetails, $bomItemsList, $stockCheckFrom, $stockCheckTo)
    {
        $soProdId = $subProductionOrder["prod_id"];
        $soSubProdId = $subProductionOrder["sub_prod_id"];
        $declearItemId = $subProductionOrder["itemId"];
        $declearItemQty = $subProductionOrder["remainQty"];
        $declearItemUom = $subProductionOrder["uom"] ?? "";
        $$declearItemPrice = $subProductionOrder["itemPrice"] ?? 0;
        // console([
        //     "subProductionOrder" => $subProductionOrder,
        //     "BomDetails" => $bomDetails,
        //     "BomItemsList" => $bomItemsList
        // ]);

        // console("====================STOCK DETAILS==================");
        foreach ($bomItemsList as $keyss => $bomOneItem) {
            $stockLogObj = itemQtyStockChecking($bomOneItem["item_id"], "'$stockCheckFrom'", ($bomOneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"), null, $this->productionDeclareDate);
            $stockLogTransferQty = $bomOneItem["totalConsumption"] * $declearItemQty;
            // console($stockLogObj);
            if ($bomOneItem["priceType"] == "V") {
                $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
                $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
                $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
                $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
                $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
                $consumpProductData[$keyss]["unitprice"] = $bomOneItem['movingWeightedPrice'];
                $consumpProductData[$keyss]["price"] = $bomOneItem['movingWeightedPrice'] * $stockLogTransferQty;
            } else {

                $consumpSfgProductSql = "SELECT bom.`cogm` as cogmprice FROM `erp_bom` WHERE `locationId`=" . $this->location_id . " AND bomStatus` = 'active' AND `itemId`=" . $bomOneItem["item_id"] . " ORDER BY bomId DESC";

                $consumpSfgProductObj = $this->dbObj->queryGet($consumpSfgProductSql);

                if ($consumpSfgProductObj["status"] == "success") {
                    $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
                    $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
                    $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
                    $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
                    $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
                    $consumpProductData[$keyss]["unitprice"] = $consumpSfgProductObj['data']['cogmprice'];
                    $consumpProductData[$keyss]["price"] = $consumpSfgProductObj['data']['cogmprice'] * $stockLogTransferQty;
                } else {
                    $consumpProductData[$keyss]["type"] = $bomOneItem['type'];
                    $consumpProductData[$keyss]["stockLogTransferQty"] = $stockLogTransferQty;
                    $consumpProductData[$keyss]["parentGlId"] = $bomOneItem['parentGlId'];
                    $consumpProductData[$keyss]["itemCode"] = $bomOneItem['itemCode'];
                    $consumpProductData[$keyss]["itemName"] = $bomOneItem['itemName'];
                    $consumpProductData[$keyss]["unitprice"] = $bomOneItem['movingWeightedPrice'];
                    $consumpProductData[$keyss]["price"] = $bomOneItem['movingWeightedPrice'] * $stockLogTransferQty;
                }
            }


            $stockLogTransferedQty = 0;
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
                $minusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET 
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            storageLocationId = '" . $stockLogRow['storage_location_id'] . "',
                            storageType ='" . $stockLogRow['storageLocationTypeSlug'] . "',
                            itemId = '" . $bomOneItem["item_id"] . "',
                            itemQty = '" . $usedQuantity * -1 . "',
                            itemUom = '" . $stockLogRow["itemUom"] . "',
                            itemPrice = '" . $bomOneItem['movingWeightedPrice'] . "',
                            refActivityName='PROD-OUT',
                            logRef = '" . $stockLogRow['logRef'] . "',
                            refNumber='" . $this->PROD_DECLARE_CODE . "',
                            bornDate='" . $stockLogRow['bornDate'] . "',
                            postingDate ='" . $this->productionDeclareDate . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";
                $this->dbObj->queryInsert($minusStockSql);
            }

            if ($stockLogTransferQty != $stockLogTransferedQty) {
                $this->isAllRmStockAvailable = false;
            }
        }

        $reserveStorageObj = $this->dbObj->queryGet("SELECT `storage_location_id`, `warehouse_id`, `storageLocationTypeSlug` FROM `erp_storage_location` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `location_id`=$this->location_id AND `storageLocationTypeSlug`='$stockCheckTo' AND `status`='active'")['data'];

        $plusStockSql = "INSERT INTO `" . ERP_INVENTORY_STOCKS_LOG . "` 
                        SET
                            companyId = '" . $this->company_id . "',
                            branchId = '" . $this->branch_id . "',
                            locationId = '" . $this->location_id . "',
                            storageLocationId = '" . $reserveStorageObj['storage_location_id'] . "',
                            refActivityName = 'PROD-IN',
                            logRef = '" . $this->PROD_DECLARE_CODE . "',
                            refNumber = '" . $this->PROD_DECLARE_CODE . "',
                            bornDate = '" . $this->productionDeclareDate . "',
                            postingDate = '" . $this->productionDeclareDate . "',
                            storageType = '" . $reserveStorageObj['storageLocationTypeSlug'] . "',
                            itemId = '" . $declearItemId . "',
                            itemQty = '" . $declearItemQty . "',
                            itemUom = '" . $declearItemUom . "',
                            itemPrice = '" .  $declearItemPrice . "',
                            createdBy = '" . $this->created_by . "',
                            updatedBy = '" . $this->updated_by . "'";

        $this->dbObj->queryInsert($plusStockSql);
        $this->dbObj->queryUpdate("UPDATE `erp_production_order_sub`
            SET
              `remainQty`=`remainQty`-$declearItemQty,
              `updated_by`='$this->updated_by',
              `status` = CASE WHEN `remainQty` = 0 THEN 10 ELSE `status` END
            WHERE 1
                AND `sub_prod_id`=$soSubProdId
                AND `company_id`=$this->company_id
                AND `branch_id`=$this->branch_id
                AND `location_id`=$this->location_id
        ");

        $this->dbObj->queryUpdate("UPDATE `erp_production_order`
                SET
                `remainQty`=`remainQty`-$declearItemQty,
                `updated_by`='$this->updated_by',
                `status` = CASE WHEN `remainQty`=0 THEN 10 ELSE `status` END
                WHERE 1
                    AND `so_por_id`=$soProdId
                    AND `company_id`=$this->company_id
                    AND `branch_id`=$this->branch_id
                    AND `location_id`=$this->location_id
        ");
        //console("====================END STOCK DETAILS==================");


    }


    public function walkAllOrdersAndItems($productionOrderRefNo)
    {
        // `so_por_id`, `company_id`, `branch_id`, `location_id`, `porCode`, `itemId`, `itemCode`, `refNo`, `qty`, `remainQty`, `expectedDate`, `description`, `mrp_status`, `wc_id`, `table_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`
        $productionOrderObj = $this->dbObj->queryGet("SELECT *
        FROM
            `erp_production_order`
        WHERE 1
            AND `refNo`='$productionOrderRefNo'
            AND `company_id`=$this->company_id
            AND `branch_id`=$this->branch_id
            AND `location_id`=$this->location_id", true);

        foreach ($productionOrderObj["data"] as $productionOrder) {
            // `sub_prod_id`, `prod_id`, `grand_prod_id`, `company_id`, `branch_id`, `location_id`, `subProdCode`, `prodCode`, `itemId`, `itemCode`, `prodQty`, `remainQty`, `expectedDate`, `mrp_status`, `wc_id`, `table_id`, `created_at`, `created_by`, `updated_at`, `updated_by`, `status`
            // console("\n==============Calling: ".$productionOrder["porCode"]."=================");
            $this->walkAllOrdersAndItems($productionOrder["porCode"]);
            $subProductionOrderObj = $this->dbObj->queryGet("SELECT *
            FROM
                `erp_production_order_sub`
            WHERE 1
                AND `prodCode`='" . $productionOrder["porCode"] . "'
                AND `company_id`=$this->company_id
                AND `branch_id`=$this->branch_id
                AND `location_id`=$this->location_id", true);
            foreach ($subProductionOrderObj["data"] as $subProductionOrder) {
                $stockCheckFrom = "rmProdOpen";
                $stockTransferTo = "rmProdOpen";
                if ($subProductionOrder["prod_id"] == $this->mainProductionOrderId) {
                    $stockCheckFrom = "rmProdOpen";
                    $stockTransferTo = "fgWhOpen";
                    // echo "<br>" . $subProductionOrder["itemCode"] . "============Working Final Prod: " . $subProductionOrder["subProdCode"];
                } else {
                    // echo "<br>" . $subProductionOrder["itemCode"] . "============Working: " . $subProductionOrder["subProdCode"];
                }

                // **comsumption done here**
                // 1. fecth the bom data
                // $bomObj = $this->bomControllerObj->getBomDetails($subProductionOrder["itemId"]);
                $bomObj = $this->bomControllerObj->getBomDetailsByItemId($subProductionOrder["itemId"]);
                $bomDetails = $bomObj["data"]["bom_data"] ?? [];
                $bomItemsList = $bomObj["data"]["bom_material_data"] ?? [];
                $this->consumeAndProduce($subProductionOrder, $bomDetails, $bomItemsList, $stockCheckFrom, $stockTransferTo);
                // 2. consumption of rm
                // 3. create sfg or fg
                // 4. stock transfer
                // 5. close the sub production order
                // 6. update the production order quantity
            }
        }
    }


    public function confirmConsumption($POST)
    {
        $soProdId = $POST["soProdId"] ?? 0;
        $this->mainProductionOrderId = $soProdId;
        $soProdCode = $POST["soProdCode"] ?? 0;
        $soProdRefNo = $POST["soProdRefNo"] ?? "";
        $declearItemId = $POST["itemId"] ?? 0;
        $declearItemCode = $POST["itemCode"] ?? "";
        $declearItemQty = $POST["productionQuantity"] ?? 0;
        $declearItemUom = $POST["itemUom"] ?? 0;
        $declearItemPrice = $POST["itemRate"] ?? 0;

        $this->PROD_DECLARE_CODE = "PROD" . time();
        $productionDeclareDate = $POST["productionDeclareDate"] ?? date("Y-m-d");
        $this->productionDeclareDate = $productionDeclareDate;
        $this->declearItemMfgDate = $productionDeclareDate;

        $checkObj = $this->check($soProdId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        // $this->walkAllOrdersAndItems($soProdCode);
        $this->walkAllOrdersAndItems($soProdRefNo);

        // console($this->isAllRmStockAvailable);
        // exit();

        if (!$this->isAllRmStockAvailable) {

            return [
                "status" => "warning",
                "message" => "Stock not available, please transfer all RM stock to Prod Open!",
                "data" => $this->dbObj->queryRollBack()
            ];
        } else {

            $getLastProdDeclareSlNoObj = $this->dbObj->queryGet("SELECT `itemSlno` FROM `erp_inventory_stocks_fg_barcodes` WHERE `company_id`=$this->company_id AND `branch_id`=$this->branch_id AND `location_id`=$this->location_id AND `itemId`=$declearItemId ORDER BY `itemSlno` DESC LIMIT 1");
            if ($getLastProdDeclareSlNoObj["status"] == "success" && $getLastProdDeclareSlNoObj["data"]["itemSlno"] > 0) {
                $lastDeclearQtySl = ($getLastProdDeclareSlNoObj["data"]["itemSlno"] ?? 0) + 1;
            } else {
                $lastDeclearQtySl = 1;
            }

            $barCodeList = [];
            for ($declearQtySl = $lastDeclearQtySl; $declearQtySl < $lastDeclearQtySl + $declearItemQty; $declearQtySl++) {
                $lotNumber = date("YmdHms");
                $barCodeList[] = $oneBarcode = ["declearItemCode" => $declearItemCode, "lotNumber" => $lotNumber, "declearQtySl" => $declearQtySl, "barcode" => $declearItemCode . "/" . $lotNumber . "/" . $declearQtySl];

                $barCodeSql = 'INSERT INTO `erp_inventory_stocks_fg_barcodes` SET `company_id`=' . $this->company_id . ',`branch_id`=' . $this->branch_id . ',`location_id`=' . $this->location_id . ',`itemId`=' . $declearItemId . ',`itemCode`="' . $declearItemCode . '",`itemLotNumber`="' . $oneBarcode["lotNumber"] . '",`itemSlno`=' . $oneBarcode["declearQtySl"] . ',`itemBarcode`="' . $oneBarcode["barcode"] . '",`mfgDate`="' . $this->declearItemMfgDate . '",`qcDate`="' . date("Y-m-d") . '"';

                $this->dbObj->queryInsert($barCodeSql);
            }
            return $this->dbObj->queryFinish();
        }
    }
}
