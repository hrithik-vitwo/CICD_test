<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_id = $_GET['company_id'];

    if(isset($company_id) && $company_id != "")
    {
        $check_query = queryGet('SELECT uom.`uomName`,uom.`uomDesc` FROM `erp_inventory_mstr_uom` AS uom  WHERE (uom.`companyId`=' . $company_id . ' OR uom.`companyId`= 0) AND uom.`uomStatus`= "active" ORDER BY uom.`uomId` ASC',true);

        if($check_query["numRows"] == 0)
        {
            sendApiResponse([
                "status" => "error",
                "data" => "NO List"
    
            ], 405);
        }
        else
        {
            sendApiResponse([
                "status" => "success",
                "data" => $check_query["data"]
    
            ], 200);
        }

    }
    else
    {
        sendApiResponse([
            "status" => "error",
            "data" => "Something went Wrong"

        ], 405);
    }
    
    
} else {
    sendApiResponse([
        "status" => "error",
        "data" => "Method not allowed"

    ], 405);
}
