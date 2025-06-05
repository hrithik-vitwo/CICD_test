<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id']; 
    $company_id = $authCustomer['company_id'];

    $id = $_POST['id'];    
    $cond = '';

    $sql_order = "SELECT *
    FROM `erp_party_order`
    WHERE `customer_id` = '" . $customer_id . "' AND company_id=$company_id AND id=$id
    ORDER BY `id` DESC";
    $qry_order = queryGet($sql_order);

    $sql_item_list_detail = "SELECT `erp_inventory_items`.*, `erp_party_order_item`.`quantity`, true AS 'isClicked'
            FROM `erp_party_order_item`
            LEFT JOIN `erp_inventory_items` ON `erp_party_order_item`.`item_id` = `erp_inventory_items`.`itemId`
            WHERE `erp_party_order_item`.`order_id` = '" . $id . "'
            ORDER BY `erp_party_order_item`.`id` DESC";
    $qry_item_detail = queryGet($sql_item_list, true);

    if ($qry_item_detail['status'] == "success") {
            $data_array['ordermain'] = $qry_order['data'];
            $data_array['ordermain']['items'] = $qry_item_detail['data'];

        // console($data_array);
        sendApiResponse([
            "status" => "success",
            "message" => "Data found",
            "data" => $data_array,
            "customer_detail" => $authCustomer

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "data" => []

        ], 200);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}