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
    private $masterBomNotProduceableList = [];
    private $masterItemStockList = [];

    private $masterCheckStockLocations = [
        1 => "'rmWhOpen','rmProdOpen'",
        2 => "'sfgStockOpen'",
        "other" => "'fgWhOpen'"
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
            $stockObj = $this->dbObj->queryGet('SELECT COALESCE(SUM(`itemQty`),0) AS totalQty FROM `erp_inventory_stocks_log` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `storageType` IN ( '.$itemTypeStr.') AND `itemId`=' . $itemId);
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


    function generateBomTree($itemId, $prodQty = 1, $processedItems = [])
    {
        if (in_array($itemId, $processedItems)) {
            return [];  // Circular reference detected, stop recursion
        }

        $processedItems[] = $itemId;

        $data = $this->dbObj->queryGet('SELECT `erp_bom_item_material`.*, FORMAT((`erp_bom_item_material`.`consumption` + (`erp_bom_item_material`.`consumption` * `erp_bom_item_material`.`extra` / 100)), 5) AS consumptionRate, 0 as availableQty, 0 as requiredQty, 0 as currentRequiredQty, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`, `erp_inventory_items`.`baseUnitMeasure` as uomId, `erp_bom`.`itemId` as parentId,`erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus` FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`="active" AND `erp_bom`.`companyId` = ' . $this->company_id . ' AND `erp_bom`.`branchId`=' . $this->branch_id . ' AND `erp_bom`.`locationId`=' . $this->location_id . ' AND `erp_bom`.`itemId` = ' . $itemId, true);
        // $data = $this->dbObj->queryGet('SELECT `erp_bom_item_material`.*, FORMAT((`erp_bom_item_material`.`consumption` + (`erp_bom_item_material`.`consumption` * `erp_bom_item_material`.`extra` / 100)) * ' . $prodQty . ', 5) AS totalConsumption, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`,`erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus` FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`="active" AND `erp_bom`.`companyId`=' . $this->company_id . ' AND `erp_bom`.`branchId`=' . $this->branch_id . ' AND `erp_bom`.`locationId`=' . $this->location_id . ' AND `erp_bom`.`itemId` = ' . $itemId, true);

        $tempData = [];
        foreach ($data["data"] ?? [] as $key => $itemObj) {
            $itemObj["consumptionRate"] = $itemObj["consumption"] + ($itemObj["consumption"] * $itemObj["extra"] / 100);
            $itemId = $itemObj["item_id"];
            $itemType = $itemObj["goodsType"];
            $consumptionRate = $itemObj["consumptionRate"];
            $totalConsumptionQty = $prodQty * $consumptionRate;
            $availableQty = $this->checkVirtualStocks($itemId, $itemType);
            $requiredQty = $totalConsumptionQty - $availableQty; // (200-5) = 95 || (200-201) = -1
            if ($requiredQty >= 0) {
                $this->updateVirtualStocks($itemId, $itemType, 0);
            } else {
                $requiredQty = 0;
                $this->updateVirtualStocks($itemId, $itemType, abs($requiredQty));
            }

            $itemObj["totalConsumptionQty"] = $totalConsumptionQty;
            $itemObj["availableQty"] = $availableQty;
            $itemObj["requiredQty"] = $requiredQty;

            if ($itemType == 2) {
                $this->masterBomProduceableList[] = $itemObj;
                $itemObj["childrens"] = $this->generateBomTree($itemId, $requiredQty, $processedItems);
            } else {
                if ($this->masterBomNotProduceableList[$itemId]) {
                    $this->masterBomNotProduceableList[$itemId]["totalConsumptionQty"] += $itemObj["totalConsumptionQty"];
                    $this->masterBomNotProduceableList[$itemId]["requiredQty"] += $itemObj["requiredQty"];
                } else {
                    $this->masterBomNotProduceableList[$itemId] = $itemObj;
                }
            }
            $tempData[] = $itemObj;
        }
        return $tempData;
    }


    function runMrp($productionOrderId)
    {

        // $productionOrderObj =  $this->dbObj->queryGet('SELECT pOrder.*, pOrder.remainQty as requiredQty, items.itemId, items.itemName, items.itemCode, items.itemDesc, 1 as consumptionRate, items.item_sell_type, items.goodsType, Uoms.uomName as uom, 0 as parentId FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder LEFT JOIN `' . ERP_INVENTORY_ITEMS . '` AS items ON pOrder.`itemCode`=items.`itemCode` LEFT JOIN `erp_inventory_mstr_uom` as Uoms ON items.baseUnitMeasure = Uoms.uomId WHERE pOrder.`location_id`="' . $this->location_id . '" AND pOrder.so_por_id=' . $productionOrderId);

        $productionOrderObj =  $this->dbObj->queryGet('SELECT pOrder.*, pOrder.remainQty as requiredQty, items.itemId, items.itemName, items.itemCode, items.itemDesc, 1 as consumptionRate, items.item_sell_type, items.goodsType, Uoms.uomName as uom, 0 as parentId FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder LEFT JOIN `' . ERP_INVENTORY_ITEMS . '` AS items ON pOrder.`itemId`=items.`itemId` LEFT JOIN `erp_inventory_mstr_uom` as Uoms ON items.baseUnitMeasure = Uoms.uomId WHERE pOrder.`location_id`="' . $this->location_id . '" AND pOrder.so_por_id=' . $productionOrderId);



        $itemId = $productionOrderObj["data"]["itemId"] ?? 0;
        $remainQty = $productionOrderObj["data"]["remainQty"] ?? 0;
        $itemType = $productionOrderObj["data"]["goodsType"];
        $availableQty = $this->checkVirtualStocks($itemId, $itemType);
        // $requiredQty = $remainQty - $availableQty; // (200-5) = 95 || (200-201) = -1 
        $requiredQty = $remainQty; // if user want to make more product
        if ($requiredQty >= 0) {
            $this->updateVirtualStocks($itemId, $itemType, 0);
        } else {
            $requiredQty = 0;
            $this->updateVirtualStocks($itemId, $itemType, abs($requiredQty));
        }
        $productionOrderObj["data"]["availableQty"] = $availableQty;
        $productionOrderObj["data"]["requiredQty"] = $requiredQty;


        $treeObj = $this->generateBomTree($itemId, floatval($requiredQty));

        $cleanNonConsumableItemList = [];
        foreach ($this->masterBomNotProduceableList as $key => $item) {
            $cleanNonConsumableItemList[] = $item;
        }

        if (count($treeObj) > 0) {
            return [
                "status" => "success",
                "message" => "MRP preview generated successfully",
                "productionOrder" => $productionOrderObj["data"],
                "bomDetails" => $treeObj,
                "produceableItems" => array_merge([$productionOrderObj["data"]], $this->masterBomProduceableList),
                "notProduceableItems" => $cleanNonConsumableItemList
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "MRP preview generation failed",
                "productionOrder" => $productionOrderObj["data"] ?? [],
                "bomDetails" => $treeObj
            ];
        }
        // console("Production id: " . $productionOrderId);
        // console($productionOrderObj);
        // console("================================================");
    }





    // Note used
    function generateBomTree2($itemId,  $prodQty = 1)
    {
        $data = $this->dbObj->queryGet('SELECT `erp_bom_item_material`.*, FORMAT((`erp_bom_item_material`.`consumption` + (`erp_bom_item_material`.`consumption` * `erp_bom_item_material`.`extra` / 100)), 5) AS consumptionRate, 0 as availableQty, 0 as requiredQty, 0 as currentRequiredQty, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`,`erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus` FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`="active" AND `erp_bom`.`companyId`=' . $this->company_id . ' AND `erp_bom`.`branchId`=' . $this->branch_id . ' AND `erp_bom`.`locationId`=' . $this->location_id . ' AND `erp_bom`.`itemId` = ' . $itemId, true);
        // $data = $this->dbObj->queryGet('SELECT `erp_bom_item_material`.*, FORMAT((`erp_bom_item_material`.`consumption` + (`erp_bom_item_material`.`consumption` * `erp_bom_item_material`.`extra` / 100)) * ' . $prodQty . ', 5) AS totalConsumption, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`,`erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus` FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`="active" AND `erp_bom`.`companyId`=' . $this->company_id . ' AND `erp_bom`.`branchId`=' . $this->branch_id . ' AND `erp_bom`.`locationId`=' . $this->location_id . ' AND `erp_bom`.`itemId` = ' . $itemId, true);

        $tempData = [];
        foreach ($data["data"] ?? [] as $key => $itemObj) {
            if ($itemObj["goodsType"] == 2) {
                $tempData = $this->generateBomTree2($itemObj["item_id"], $prodQty * $itemObj["consumptionRate"]);
            }
            $tempData[] = $itemObj;
        }
        return $tempData;
    }


    function runMrp2($productionOrderId)
    {
        $productionOrderObj =  $this->dbObj->queryGet('SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.item_sell_type FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder,`' . ERP_INVENTORY_ITEMS . '` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`="' . $this->location_id . '" AND pOrder.so_por_id=' . $productionOrderId);

        $itemId = $productionOrderObj["data"]["itemId"] ?? 0;
        $remainQty = $productionOrderObj["data"]["remainQty"] ?? 0;

        $treeObj = $this->generateBomTree2($itemId, floatval($remainQty));

        if (count($treeObj) > 0) {
            return [
                "status" => "success",
                "message" => "MRP preview generated successfully",
                "productionOrder" => $productionOrderObj["data"],
                "bomDetails" => $treeObj
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "MRP preview generation failed",
                "productionOrder" => $productionOrderObj["data"] ?? [],
                "bomDetails" => $treeObj
            ];
        }
    }
}

$mrpControllerTestObj = new MrpControllerTest();
if (isset($_GET["production-order-id"])) {
    $productionOrderId = $_GET["production-order-id"] ?? 0;
    $mrpPreviewObj = $mrpControllerTestObj->runMrp($productionOrderId);
    echo json_encode($mrpPreviewObj);
} else {
    $productionOrderId = 87;
    $mrpPreviewObj = $mrpControllerTestObj->runMrp2($productionOrderId);
    echo json_encode($mrpPreviewObj);
}
