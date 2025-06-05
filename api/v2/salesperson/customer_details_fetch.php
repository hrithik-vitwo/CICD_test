<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];

    $requestBody = requestBody();

    $sqlAddress = "SELECT * FROM `" . ERP_CUSTOMER_ADDRESS . "` WHERE `customer_id`='".$requestBody['customerId']."' AND customer_address_primary_flag=1";
    $addressObj = queryGet($sqlAddress);

    $sql_list = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='" . $requestBody['customerId'] . "'";
    $iv_sql = queryGet($sql_list);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        sendApiResponse([
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "data" => [
                "customerDetails"=>$iv_data,
                "billingAddress"=>$addressObj['data'],
                "shippingAddress"=>$addressObj['data']
                ]
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
