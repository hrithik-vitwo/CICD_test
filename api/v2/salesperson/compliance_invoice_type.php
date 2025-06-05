<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $iv_sql = queryGet("SELECT * FROM `" . ERP_INVOICE_TYPE . "` ORDER BY id DESC", true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];
        
        $data_array = [];
        foreach ($iv_data as $data) {
            $data_array[] = array("items" => $data);
        }
        sendApiResponse([
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "data" => $iv_sql['data']
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "sql" => $sql_list
        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}