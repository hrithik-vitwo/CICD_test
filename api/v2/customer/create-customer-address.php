<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $insCustAddress = queryInsert("INSERT INTO `erp_customer_address` SET `customer_id`='$customer_id',`customer_address_primary_flag`='0',`customer_address_flat_no`='" . $_POST['customer_address_flat_no'] . "',`customer_address_pin_code`='" . $_POST['customer_address_pin_code'] . "',`customer_address_district`='" . $_POST['customer_address_district'] . "',`customer_address_location`='" . $_POST['customer_address_location'] . "',`customer_address_building_no`='" . $_POST['customer_address_building_no'] . "',`customer_address_street_name`='" . $_POST['customer_address_street_name'] . "',`customer_address_city`='" . $_POST['customer_address_city'] . "',`customer_address_state`='" . $_POST['customer_address_state'] . "'");

    if ($insCustAddress['status'] == "success") {

        $iv_data = $insCustAddress["data"];

        $data_array = [];

        // console($data_array);
        sendApiResponse([
            "status" => "success",
            "message" => "Inserted Successfully",
            "data" => $data_array,
            "sql" => $insCustAddress,

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Somthing went wrong!",
            "data2" => $insCustAddress,
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
//echo "ok";