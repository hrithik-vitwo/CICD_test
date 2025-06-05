<?php
include_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
 
    $movement_type = $_GET['value'];

    $result = "";

    if($movement_type == "storage_location")
    {
       $result .= "<label for=''>Destination Storage Location</label>
        <select name='destinationStorageLocation' class='select2 form-control' required>
            <option value=''>Select Storage Location</option>";
            
            $qrysrui= queryGet("SELECT loc.storage_location_id, loc.storage_location_code, loc.storage_location_name, loc.storage_location_type, loc.storageLocationTypeSlug, warh.warehouse_id, warh.warehouse_code, warh.warehouse_name FROM erp_storage_location AS loc LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id WHERE loc.storage_location_storage_type!='Reserve' AND loc.company_id=$company_id", true);
            $sldattaqe=$qrysrui['data'];
            foreach($sldattaqe as $datasllll)
            {
                $result .= "<option value=".$datasllll['storage_location_id'].'|'.$datasllll['storageLocationTypeSlug'].">".$datasllll['warehouse_code']." >> ".$datasllll['storage_location_code']." >> ".$datasllll['storage_location_name']."</option>";
            }
            

            $result .= "</select>";

    }
    elseif($movement_type == "production_order")
    {
        $result .= "<label for=''>Production Order</label>
        <select name='destinationStorageLocation' class='select2 form-control' required>
            <option value=''>Select Production Order</option>";
            
            $qrysrui= queryGet("SELECT prod.so_por_id, prod.porCode, prod.itemCode, item.itemName FROM erp_production_order AS prod LEFT JOIN erp_inventory_items AS item ON prod.itemId = item.itemId WHERE prod.company_id=$company_id", true);
            $sldattaqe=$qrysrui['data'];
            foreach($sldattaqe as $datasllll)
            {
                $result .= "<option value=".$datasllll['so_por_id'].'|'.">".$datasllll['porCode']." || ".$datasllll['itemCode']." || ".$datasllll['itemName']."</option>";
            }
            

            $result .= "</select>";
    }
    elseif($movement_type == "cost_center")
    {
        $result .= "<label for=''>Cost Center</label>
        <select name='destinationStorageLocation' class='select2 form-control' required>
            <option value=''>Select Cost Center</option>";
            
            $qrysrui= queryGet("SELECT cc.CostCenter_id, cc.CostCenter_code, cc.CostCenter_desc FROM erp_cost_center AS cc WHERE cc.company_id=$company_id", true);
            $sldattaqe=$qrysrui['data'];
            foreach($sldattaqe as $datasllll)
            {
                $result .= "<option value=".$datasllll['CostCenter_id'].'|'.">".$datasllll['CostCenter_code']." || ".$datasllll['CostCenter_desc']."</option>";
            }
            

            $result .= "</select>";
    }
    elseif($movement_type == "material_to_material")
    {
        $result .= "<input type='hidden' name='destinationStorageLocation' value = '0'>";
        // $result .= "<label for=''>Destination Items</label>
        // <select name='destinationStorageLocation' class='select2 form-control ' required>
        //     <option value=''>Select Item</option>";
            
        //     $itemsql = "SELECT
        //     summary.*,
        //     items.*,
        //     hsn.taxPercentage AS taxPercentage
        //     FROM  `" . ERP_INVENTORY_ITEMS . "` AS items
        //     LEFT JOIN `" . ERP_INVENTORY_STOCKS_SUMMARY . "` AS summary ON items.itemId = summary.itemId
        //     LEFT JOIN `" . ERP_HSN_CODE . "` AS hsn ON items.hsnCode = hsn.hsnCode
        //     WHERE items.goodsType IN (1,2,3,4,9)
        //         AND items.status = 'active'
        //         AND (summary.company_id = $company_id OR summary.company_id IS NULL)
        //         AND (summary.status = 'active' OR summary.status IS NULL)
        //         AND items.hsnCode IN (SELECT hsnCode FROM `erp_hsn_code`);
        //     ";
        //     $getAllMaterialItems = queryGet($itemsql, true);

        //     if ($getAllMaterialItems["status"] == "success") {
        //         $itemSummary = $getAllMaterialItems['data'];
        //         foreach ($itemSummary as $item) {
        //             $result .= '<option value="' . $item["itemId"] ."|". '">' . $item['itemName'] . '<small>(' . $item['itemCode'] . ')</small></option>';
        //         }
        //     } else {
        //         $result .= '<option value="">Items Type</option>';
        //     }

        //     $result .= "</select>";
    }
    elseif($movement_type == "book_to_physical")
    {
        $result .= "<input type='hidden' name='destinationStorageLocation' value = '0'>";
    }

    echo json_encode($result);
    
} else {
    echo "Something wrong, try again!";
} 
?>

