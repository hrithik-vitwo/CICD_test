<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_id = $_GET['company_id'];
    $branch_id = $_GET['branch_id'];
    $location_id = $_GET['location_id'];

    if(isset($company_id) && $company_id != "" && isset($branch_id) && $branch_id != "" && isset($location_id) && $location_id != "")
    {
        $check_query = queryGet('SELECT `vendor_code`,`vendor_gstin`,`trade_name` FROM `erp_vendor_details` WHERE `company_id`=' . $company_id . ' AND `company_branch_id`='.$branch_id.' AND `location_id`='.$location_id.' AND `vendor_status`= "active" ORDER BY `vendor_code` ASC',true);

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
