<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $company_id = $_GET['company_id'];

    if(isset($company_id) && $company_id != "")
    {
        $check_query = queryGet('SELECT * FROM `erp_branch_otherslocation` LEFT JOIN `erp_companies` ON erp_companies.`company_id` = erp_branch_otherslocation.`company_id` WHERE erp_branch_otherslocation.`company_id`=' . $company_id,true);

        if($check_query["numRows"] == 0)
        {
            sendApiResponse([
                "status" => "error",
                "message" => "No List"
    
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
