<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];

    $requestBody = requestBody();

    $customerAddressId = $requestBody['customerAddressId'];

    $sql_list = "SELECT * FROM `" . ERP_CUSTOMER_ADDRESS . "` WHERE `customer_address_id`='$customerAddressId'";
    $iv_sql = queryGet($sql_list);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];
        foreach ($iv_data as $data) {

            $data_array[] = array("items" => $data);
        }
        // console($data_array);
        sendApiResponse([
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "data" => $iv_sql['data']
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Data not found",
            "sql" => $iv_sql,
            "data" => []

        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}