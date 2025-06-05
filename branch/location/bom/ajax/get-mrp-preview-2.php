<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
header('Content-Type: application/json');



class MrpControllerTest
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by, $dbObj;
    private $masterBomTreeObj = [];
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


    // function generateBomTree($itemId, $requiredQty = 1)
    // {
    //     $data = $this->dbObj->queryGet('SELECT `erp_bom_item_material`.*, FORMAT((`erp_bom_item_material`.`consumption` + (`erp_bom_item_material`.`consumption` * `erp_bom_item_material`.`extra` / 100)) * ' . $requiredQty . ', 2) AS totalConsumption, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`,`erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus` FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`="active" AND `erp_bom`.`companyId`=' . $this->company_id . ' AND `erp_bom`.`branchId`=' . $this->branch_id . ' AND `erp_bom`.`locationId`=' . $this->location_id . ' AND `erp_bom`.`itemId` = ' . $itemId, true);

    //     $tempData = [];
    //     foreach ($data["data"] ?? [] as $key => $itemObj) {
    //         $tempItem["data"] = $itemObj;
    //         if ($itemObj["goodsType"] == 2) {
    //             $tempItem["children"] = $this->generateBomTree($itemObj["item_id"], 1);
    //         } else {
    //             $tempItem["children"] = [];
    //         }
    //         $tempData[] = $tempItem;
    //     }
    //     return $tempData;
    // }

    function generateBomTree($itemId, $prodQty = 1)
    {
        // $data = $this->dbObj->queryGet('SELECT `erp_bom_item_material`.*, FORMAT((`erp_bom_item_material`.`consumption` + (`erp_bom_item_material`.`consumption` * `erp_bom_item_material`.`extra` / 100)), 5) AS consumptionRate, 0 as availableQty, 0 as requiredQty, 0 as productionQty, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`,`erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus` FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`="active" AND `erp_bom`.`companyId`=' . $this->company_id . ' AND `erp_bom`.`branchId`=' . $this->branch_id . ' AND `erp_bom`.`locationId`=' . $this->location_id . ' AND `erp_bom`.`itemId` = ' . $itemId, true);
        $data = $this->dbObj->queryGet('SELECT `erp_bom_item_material`.*, FORMAT((`erp_bom_item_material`.`consumption` + (`erp_bom_item_material`.`consumption` * `erp_bom_item_material`.`extra` / 100)) * ' . $prodQty . ', 5) AS totalConsumption, `erp_inventory_items`.`itemCode`,`erp_inventory_items`.`itemName`,`erp_inventory_items`.`goodsType`,`erp_inventory_items`.`item_sell_type`, `erp_inventory_stocks_summary`.`bomStatus` FROM `erp_bom` LEFT JOIN `erp_bom_item_material` ON `erp_bom`.`bomId`=`erp_bom_item_material`.`bom_id` LEFT JOIN `erp_inventory_items` ON `erp_bom_item_material`.`item_id`=`erp_inventory_items`.`itemId` LEFT JOIN `erp_inventory_stocks_summary` ON `erp_bom_item_material`.`item_id`=`erp_inventory_stocks_summary`.`itemId` WHERE `erp_bom`.`bomStatus`="active" AND `erp_bom`.`companyId`=' . $this->company_id . ' AND `erp_bom`.`branchId`=' . $this->branch_id . ' AND `erp_bom`.`locationId`=' . $this->location_id . ' AND `erp_bom`.`itemId` = ' . $itemId, true);

        $tempData = [];
        foreach ($data["data"] ?? [] as $key => $itemObj) {
            $itemObj["consumptionRate"] = $itemObj["consumption"]+($itemObj["consumption"]*$itemObj["extra"]/100);
            if ($itemObj["goodsType"] == 2) {
                $itemObj["childrens"] = $this->generateBomTree($itemObj["item_id"], $prodQty*$itemObj["consumptionRate"]);
            }
            $tempData[] = $itemObj;
        }
        return $tempData;
    }


    function runMrp($productionOrderId)
    {

        $productionOrderObj =  $this->dbObj->queryGet('SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.item_sell_type FROM `' . ERP_PRODUCTION_ORDER . '` AS pOrder,`' . ERP_INVENTORY_ITEMS . '` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`="' . $this->location_id . '" AND pOrder.so_por_id=' . $productionOrderId);

        $itemId = $productionOrderObj["data"]["itemId"] ?? 0;
        $remainQty = $productionOrderObj["data"]["remainQty"] ?? 0;

        $treeObj = $this->generateBomTree($itemId, floatval($remainQty));

        if(count($treeObj)>0){
            return [
                "status" => "success",
                "message" => "MRP preview generated successfully",
                "productionOrder" => $productionOrderObj["data"],
                "bomDetails"=> $treeObj
            ];

        }else{
            return [
                "status" => "warning",
                "message" => "MRP preview generation failed",
                "productionOrder" => $productionOrderObj["data"] ?? [],
                "bomDetails"=> $treeObj
            ];

        }
        // console("Production id: " . $productionOrderId);
        // console($productionOrderObj);
        // console("================================================");
    }
}
$productionOrderId = $_GET["production-order-id"] ?? 0;
$mrpControllerTestObj = new MrpControllerTest();

$mrpPreviewObj = $mrpControllerTestObj->runMrp($productionOrderId);
echo json_encode($mrpPreviewObj);

?>