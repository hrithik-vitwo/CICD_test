<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_id = $_GET['company_id'];

    if(isset($company_id) && $company_id != "")
    {
        $check_query = queryGet('SELECT `section`,`TDSRate`,`natureOfTransaction` FROM `erp_tds_details`',true);

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
