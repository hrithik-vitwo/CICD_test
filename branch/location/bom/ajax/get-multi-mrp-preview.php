<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
header('Content-Type: application/json');



class MrpControllerTest
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by, $dbObj;
    private $masterBomProduceableList = [];
    private $masterBomPurchasableList = [];
    private $masterItemStockList = [];

    private $masterCheckStockLocations = [
        1 => "rmWhOpen",
        2 => "sfgStockOpen",
        "other" => "fgWhOpen"
    ];

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


    public function setCheckStockLocation($arr = [])
    {
        $this->masterCheckStockLocations = $arr;
    }

    private function checkVirtualStocks($itemId = 0, $itemType = 0)
    {
        // $itemTypeStr = $itemType == 1 ? "rmWhOpen" : ($itemType == 2 ? "sfgStockOpen" : "fgWhOpen");
        $itemTypeStr = $this->masterCheckStockLocations[$itemType] ?? $this->masterCheckStockLocations["other"];


        if (!isset($this->masterItemStockList[$itemTypeStr . $itemId])) {
            $stockObj = $this->dbObj->queryGet('SELECT COALESCE(SUM(`itemQty`),0) AS totalQty FROM `erp_inventory_stocks_log` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `storageType`="' . $itemTypeStr . '" AND `itemId`=' . $itemId);
            $this->masterItemStockList[$itemTypeStr . $itemId] = $stockObj["data"]["totalQty"];
        }
        return $this->masterItemStockList[$itemTypeStr . $itemId];
    }

    private function updateVirtualStocks($itemId = 0, $itemType = 0, $updatedQty = 0)
    {
        // $itemTypeStr = $itemType == 1 ? "rmWhOpen" : ($itemType == 2 ? "sfgStockOpen" : "fgWhOpen");
        $itemTypeStr = $this->masterCheckStockLocations[$itemType] ?? $this->masterCheckStockLocations["other"];

        $this->masterItemStockList[$itemTypeStr . $itemId] = $updatedQty;
        if ($this->masterItemStockList[$itemTypeStr . $itemId] == $updatedQty) {
            return true;
        } else {
            return false;
        }
    }

    private function bomTree($itemId, $productionOrderId = "", $productionOrderCode = "")
    {
        $data = $this->dbObj->queryGet("SELECT `erp_bom_item_material`.*, `erp_bom_item_material`.`item_id` as itemId, FORMAT((`erp_bom_item_material`.`consumption` + (`erp_bom_item_material`.`consumption` * `erp_bom_item_material`.`extra` / 100)), 5) AS consumptionRate, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`, `erp_inventory_items`.`baseUnitMeasure` as uomId, `erp_bom`.`itemId` as parentId, `erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus`, $productionOrderId as productionOrderId, '$productionOrderCode' as productionOrderCode FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`='active' AND `erp_bom`.`companyId` =$this->company_id AND `erp_bom`.`branchId`=$this->branch_id AND `erp_bom`.`locationId`=$this->location_id AND `erp_bom`.`itemId` = $itemId", true);
        $tempData = [];
        foreach ($data["data"] ?? [] as $key => $itemObj) {
            $itemObj["consumptionRate"] = $itemObj["consumption"] + ($itemObj["consumption"] * $itemObj["extra"] / 100);
            $itemId = $itemObj["item_id"];
            $itemType = $itemObj["goodsType"];
            if ($itemType == 2) {
                $itemObj["childrens"] = $this->bomTree($itemId, $productionOrderId, $productionOrderCode);
            }
            $tempData[] = $itemObj;
        }
        return $tempData;
    }

    private function generateTree(array $productionOrders = [])
    {
        $productionOrderList = [];
        $productionOrderBomTree = [];
        foreach ($productionOrders as $productionOrderId) {
            $productionOrderObj =  $this->dbObj->queryGet('SELECT pOrder.*, pOrder.remainQty as requiredQty, items.itemId, items.itemName, items.itemCode, items.itemDesc, 1 as consumptionRate, items.item_sell_type, items.goodsType, Uoms.uomName as uom, items.itemId as parentId, pOrder.`so_por_id` as productionOrderId, pOrder.`porCode` as productionOrderCode FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder LEFT JOIN `' . ERP_INVENTORY_ITEMS . '` AS items ON pOrder.`itemId`=items.`itemId` LEFT JOIN `erp_inventory_mstr_uom` as Uoms ON items.baseUnitMeasure = Uoms.uomId WHERE pOrder.`location_id`="' . $this->location_id . '" AND pOrder.so_por_id=' . $productionOrderId);

            $treeObj = $this->bomTree($productionOrderObj["data"]["itemId"], $productionOrderObj["data"]["so_por_id"], $productionOrderObj["data"]["porCode"]);
            $fullTree = $productionOrderObj["data"];
            $fullTree["childrens"] = $treeObj;
            $productionOrderObj["bomTree"] = $fullTree;
            $productionOrderList[] = $productionOrderObj["data"];
            $productionOrderBomTree[] = $fullTree;
        }
        if (count($productionOrderList) > 0 && count($productionOrderBomTree) > 0) {
            return [
                "status" => "success",
                "message" => "Tree generation completed successfully",
                "data" => [
                    "productionOrderList" => $productionOrderList,
                    "productionOrderBomTree" => $productionOrderBomTree
                ]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Tree generation failed",
                "data" => [],

            ];
        }
    }




    private function separateProduceAndPurchasableItemList(array $bomTreeList = [], $parentQty = 1)
    {
        foreach ($bomTreeList as $item) {
            $singleItem = $item;
            if (isset($singleItem["childrens"])) {
                unset($singleItem["childrens"]);
            }
            if (isset($singleItem["so_por_id"])) {
                $totalConsumptionQty = floatval($singleItem["qty"]);
            } else {
                $totalConsumptionQty = floatval($parentQty) * floatval($singleItem["consumptionRate"]);
            }

            $availableQty = floatval($this->checkVirtualStocks($singleItem["itemId"], $singleItem["goodsType"]));
            $extraRequiredQty = $totalConsumptionQty - $availableQty; // (100-5) = 95 || (100-101) = -1
            if ($extraRequiredQty >= 0) {
                $this->updateVirtualStocks($singleItem["itemId"], $singleItem["goodsType"], 0);
            } else {
                // $extraRequiredQty = 0;
                $this->updateVirtualStocks($singleItem["itemId"], $singleItem["goodsType"], abs($extraRequiredQty));
            }

            $singleItem["totalConsumptionQty"] = $totalConsumptionQty;
            $singleItem["availableQty"] = $availableQty;
            $singleItem["extraRequiredQty"] = $extraRequiredQty;
            $singleItem["extraRequiredQty1"] = $totalConsumptionQty-$availableQty;

            if ($singleItem["goodsType"] == 1) {
                // $this->masterBomPurchasableList[$singleItem["productionOrderId"] . "_" . $singleItem["itemId"]] = $singleItem;
                if ($this->masterBomPurchasableList[$singleItem["productionOrderId"] . "_" . $singleItem["itemId"]]) {
                    $this->masterBomPurchasableList[$singleItem["productionOrderId"] . "_" . $singleItem["itemId"]]["totalConsumptionQty"] +=  $totalConsumptionQty;
                    $this->masterBomPurchasableList[$singleItem["productionOrderId"] . "_" . $singleItem["itemId"]]["extraRequiredQty"] += $extraRequiredQty;
                } else {
                    $this->masterBomPurchasableList[$singleItem["productionOrderId"] . "_" . $singleItem["itemId"]] = $singleItem;
                }
            } else {
                $this->masterBomProduceableList[$singleItem["productionOrderId"] . "_" . $singleItem["itemId"]] = $singleItem;
            }

            // print_r($singleItem);
            if (count($item["childrens"] ?? []) > 0) {
                $this->separateProduceAndPurchasableItemList($item["childrens"], $extraRequiredQty);
            }
        }
    }

    private function generateProduceAndPurchasableItemList(array $bomTreeList = [])
    {
        // print_r($bomTreeList);
        $this->separateProduceAndPurchasableItemList($bomTreeList);
        if (count($this->masterBomProduceableList) > 0 || count($this->masterBomProduceableList) > 0) {
            return [
                "status" => "success",
                "message" => "Produce and purchasable item list generated success",
                "data" => [
                    "produceableItems" => $this->masterBomProduceableList,
                    "purchasableItems" => $this->masterBomPurchasableList
                ]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Produce and purchasable item list generate failed",
                "data" => [
                    "produceableItems" => $this->masterBomProduceableList,
                    "purchasableItems" => $this->masterBomPurchasableList
                ]
            ];
        }
    }


    public function runMrp(array $productionOrders = [])
    {
        $treeObj = $this->generateTree($productionOrders);
        $produceAndPurchasableItemObj = $this->generateProduceAndPurchasableItemList($treeObj["data"]["productionOrderBomTree"] ?? []);
        // print_r($treeObj);
        $treeObj["data"]["produceableItems"] = $produceAndPurchasableItemObj["data"]["produceableItems"] ?? [];
        $treeObj["data"]["purchasableItems"] = $produceAndPurchasableItemObj["data"]["purchasableItems"] ?? [];
        return $treeObj;
    }
}

$mrpControllerTestObj = new MrpControllerTest();
if (isset($_GET["production-order-id-arr"])) {
    $productionOrderIdArr = explode(",", base64_decode($_GET["production-order-id-arr"]));
    $mrpPreviewObj = $mrpControllerTestObj->runMrp($productionOrderIdArr);
    echo json_encode($mrpPreviewObj);
}
