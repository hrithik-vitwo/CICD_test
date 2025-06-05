<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];

    $requestBody = requestBody();
    $customer_id = $requestBody['customer_id'];

    $insCustAddress = queryInsert("INSERT INTO `erp_customer_address` SET `customer_id`='$customer_id',`customer_address_primary_flag`='0',`customer_address_flat_no`='" . $requestBody['customer_address_flat_no'] . "',`customer_address_pin_code`='" . $requestBody['customer_address_pin_code'] . "',`customer_address_district`='" . $requestBody['customer_address_district'] . "',`customer_address_location`='" . $requestBody['customer_address_location'] . "',`customer_address_building_no`='" . $requestBody['customer_address_building_no'] . "',`customer_address_street_name`='" . $requestBody['customer_address_street_name'] . "',`customer_address_city`='" . $requestBody['customer_address_city'] . "',`customer_address_state`='" . $requestBody['customer_address_state'] . "'");

    if ($insCustAddress['status'] == "success") {

        $iv_data = $insCustAddress["data"];

        $data_array = [];

        sendApiResponse([
            "status" => $insCustAddress['status'],
            "message" => $insCustAddress['message'],
            "lastInsertedId" => $insCustAddress['insertedId']

        ], 200);
    } else {
        sendApiResponse([
            "status" => $insCustAddress['status'],
            "message" => $insCustAddress['message'],
            "data" => $insCustAddress

        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}