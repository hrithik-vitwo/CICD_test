<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-journal.php");
require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../../../app/v1/functions/admin/func-company.php");
header('Content-Type: application/json');



class ProdTreeController
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by, $dbObj;
    private $masterBomProduceableList = [];
    private $masterBomNotProduceableList = [];
    private $masterItemStockList = [];

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

    // function generateTree($productionOrderId)
    // {
    //     $data = $this->dbObj->queryGet("SELECT * FROM `erp_production_order` WHERE `refNo` = '$productionOrderId'", true);
    //    // console($data);      
    //     $tempData = [];
    //      foreach ($data["data"] as $itemObj) {
    //         //console($itemObj);
    //        $so_por_id = $itemObj['so_por_id'];
         
    //         $itemId = $itemObj['itemId'];
    //         $porCode = $itemObj['porCode'];
    //         $itemCode = $itemObj['itemCode'];
    //      //   echo "Debug: Current so_por_id: $so_por_id, ParentId: $parentId\n";

    //         // Recursively call the function to get children
    //         $children = $this->generateTree($porCode);
    // //console($children);
    //         // Output debugging information
    //        // echo "Debug: Children for so_por_id $so_por_id: " . json_encode($children) . "\n";
    

    //     //     // Add current item details to the array
    //         $tempItem = [
    //             'so_por_id' => $so_por_id,
    //             'itemId' => $itemId,
    //             'porCode' => $porCode,
    //             'itemCode' => $itemCode,
    //             'childrens' => $children,
    //         ];

    //         $tempData[] = $tempItem;
    //      }

    //   return $tempData;
    // //    console($data);
    // }

    


    function generateTree($productionOrderId)
    {
        $data = $this->dbObj->queryGet("SELECT prod.*,items.itemName FROM `erp_production_order` AS prod LEFT JOIN `erp_inventory_items` AS items ON items.itemId = prod.itemId WHERE prod.`so_por_id` = '$productionOrderId'", true);
    //   return $data;
    
        $tempData = [];
        foreach ($data["data"] as $itemObj) {
            $so_por_id = $itemObj['so_por_id'];
            $itemId = $itemObj['itemId'];
            $porCode = $itemObj['porCode'];
            $itemCode = $itemObj['itemCode'];
            $itemName = $itemObj['itemName'];
            $qty = $itemObj['qty'];
            $remainingQty = $itemObj['remainQty'];
    
            $tempItem = [
                'so_por_id' => $so_por_id,
                'itemId' => $itemId,
                'porCode' => $porCode,
                'itemCode' => $itemCode,
                'itemName' => $itemName,
                'qty' => $qty,
                'remainingQty' => $remainingQty,
            ];
    
            $children = $this->buildChildren($porCode);
            if (!empty($children)) { 
                $tempItem['childrens'] = $children;
            }
            else{
                $tempItem['childrens'] = []; 
            }
    
            $tempData[] = $tempItem;
        }
    
        return $tempData;
    }
    
    function buildChildren($parentCode)
    {
        $data = $this->dbObj->queryGet("SELECT prod.*,items.itemName FROM `erp_production_order` AS prod LEFT JOIN `erp_inventory_items` AS items ON items.itemId = prod.itemId WHERE prod.`refNo` = '$parentCode'", true);
    
        $children = [];
        foreach ($data["data"] as $itemObj) {
            $so_por_id = $itemObj['so_por_id'];
            $itemId = $itemObj['itemId'];
            $porCode = $itemObj['porCode'];
            $itemCode = $itemObj['itemCode'];
            $itemName = $itemObj['itemName'];
            $qty = $itemObj['qty'];
            $remainingQty = $itemObj['remainQty'];
    
            $tempItem = [
                'so_por_id' => $so_por_id,
                'itemId' => $itemId,
                'porCode' => $porCode,
                'itemCode' => $itemCode,
                'itemName' => $itemName,
                'qty' => $qty,
                'remainingQty' => $remainingQty,
            ];
    
            $grandChildren = $this->buildChildren($porCode);
            if (!empty($grandChildren)) {
                $tempItem['childrens'] = $grandChildren;
            }
    
            $children[] = $tempItem;
        }
    
        return $children;
    }
}

$ProdTreeControllerObj = new ProdTreeController();
$productionOrderId = $_GET["production-order-id"] ?? 0;
$ProdTreeController = $ProdTreeControllerObj->generateTree($productionOrderId);
echo json_encode($ProdTreeController);
?>