<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $trade_name = $authCustomer['trade_name'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];
    $orederNo = 'ORDER' . time();

    $data = requestBody();

    $sql_list = "SELECT * FROM `erp_party_order`   WHERE `order_code`='" . $orederNo . "'";
    $qry_list = queryGet($sql_list);
    if ($qry_list['status'] != "success") {

        $sqll = "INSERT INTO `erp_party_order` SET 
            `company_id`='" . $company_id . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `customer_id`='" . $customer_id . "',
            `order_code`='" . $orederNo . "',
            `order_type`='" . $data['order_type'] . "',
            `created_by`='" . $trade_name . "',
            `updated_by`='" . $trade_name . "'";
        $insOrder = queryInsert($sqll);

        if ($insOrder['status'] == "success") {
            $order_id = $insOrder['insertedId'];
            $items = $data['itemDetails'];

            foreach ($items as $key => $item) {
                $sqlitem = "INSERT INTO `erp_party_order_item` SET 
                `order_id`='" . $order_id . "',
                `item_id`='" . $item['item_id'] . "',
                `created_by`='" . $trade_name . "',
                `updated_by`='" . $trade_name . "',
                `quantity`='" . $item['quantity'] . "'";
                $insItems = queryInsert($sqlitem);
            }

            $dlt="DELETE FROM `erp_cart_item` WHERE customer_id=$customer_id";
            $dltCart= queryDelete($dlt);

            sendApiResponse([
                "status" => "success",
                "message" => "Inserted Successfully",
                "insItems" => $insItems
            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Something went wrong!2",
                "insItems" => $insItems,
                "qry_list" => $qry_list
            ], 400);
        }
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
