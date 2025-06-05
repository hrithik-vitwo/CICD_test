<?php
class ProductionOrderController
{   
    function getProductionOrderList(){
        $loginCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
        $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
        $loginLocationId = $_SESSION["logedBranchAdminInfo"]["fldAdminLocationId"];
        $loginAdminId = $_SESSION["logedBranchAdminInfo"]["adminId"];
        $loginAdminType = $_SESSION["logedBranchAdminInfo"]["adminType"];

        return queryGet('SELECT pOrder.*,items.itemId, items.itemName, items.itemCode, items.itemDesc, items.itemOpenStocks, items.itemBlockStocks FROM `'.ERP_PRODUCTION_ORDER.'` AS pOrder,`'.ERP_INVENTORY_ITEMS.'` AS items WHERE pOrder.`itemCode`=items.`itemCode` AND pOrder.`location_id`="'.$loginLocationId.'" ORDER BY pOrder.so_por_id DESC', true);
    }
    
}
