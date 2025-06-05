<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];

    $funcSql = "SELECT * FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE company_id=$company_id AND othersLocation_status='active'";
    $funcObj = queryGet($funcSql, true);
    
    if ($funcObj['status'] == "success") {

        $iv_data = $funcObj["data"];
        sendApiResponse([
            "status" => $funcObj['status'],
            "message" => $funcObj['message'],
            "data" => $iv_data
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "sql" => $funcSql
        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
//echo "ok";