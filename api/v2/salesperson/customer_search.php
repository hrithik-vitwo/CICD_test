<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $company_id = $authCustomer['company_id'];

    $requestPost = requestBody();
    $location_id = $requestPost['location_id'];
    $key = $requestPost['keyword'];

    $sql_list = "SELECT * FROM `" . ERP_CUSTOMER . "` WHERE company_id='" . $company_id . "' AND (customer_visible_to_all='Yes' OR location_id=$location_id) AND `customer_status`='active' AND trade_name LIKE '%".$key."%'";
//  exit();
    $iv_sql = queryGet($sql_list, true);

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
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "iv_sql" => $iv_sql,
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
