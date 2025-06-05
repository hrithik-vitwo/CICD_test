<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_id = $_GET['company_id'];
    $branch_id = $_GET['branch_id'];
    $location_id = $_GET['location_id'];

    if(isset($company_id) && $company_id != "" && isset($branch_id) && $branch_id != "" && isset($location_id) && $location_id != "")
    {
        $check_query = queryGet('SELECT item.`itemCode`, item.`itemName`, uom.`uomName` AS `oneItemUom` FROM `erp_inventory_items` AS item LEFT JOIN `erp_inventory_mstr_uom` AS uom ON uom.`uomId`= item.`baseUnitMeasure` WHERE item.`company_id`=' . $company_id . ' AND item.`branch`='.$branch_id.' AND item.`location_id`='.$location_id.' AND item.`goodsType`="9" AND item.`status`= "active" ORDER BY item.`itemCode` ASC',true);

        if($check_query["numRows"] == 0)
        {
            sendApiResponse([
                "status" => "error",
                "message" => "NO List"
    
            ], 405);
        }
        else
        {
            sendApiResponse([
                "status" => "success",
                "message" => $check_query["data"]
    
            ], 200);
        }

    }
    else
    {
        sendApiResponse([
            "status" => "error",
            "message" => "Something went Wrong"

        ], 405);
    }
    
    
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"

    ], 405);
}
