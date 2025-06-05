<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_id = $_GET['company_id'];

    if(isset($company_id) && $company_id != "")
    {
        $check_query = queryGet('SELECT `parentGlId`,`sl_code`,`sl_name`, `sl_description` FROM `erp_extra_sub_ledger` WHERE `company_id`=' . $company_id . ' AND `status`= "active" ORDER BY `sl_id` ASC',true);
       
        

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
