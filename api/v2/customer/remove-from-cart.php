<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $data = requestBody();

    $dltsql = "DELETE FROM `erp_cart_item` WHERE  `customer_id`='" . $customer_id . "' AND  `company_id`='" . $company_id . "' AND `item_id` = '" . $data['id'] . "'";

    $qry_obj = queryDelete($dltsql);
    if ($qry_obj['status'] == "success") {
        sendApiResponse([
            "status" => "success",
            "message" => "Item removed successfully"
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Something went wrong!"
        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
