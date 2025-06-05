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


    $data = requestBody();
    $noOfItemsInCart = intval(queryGet("SELECT COUNT(id) as noOfItemsInCart FROM `erp_cart_item` WHERE `customer_id`=$customer_id")["data"]["noOfItemsInCart"] ?? 0);
    $sql_list = "SELECT * FROM `erp_cart_item` WHERE `customer_id`='" . $customer_id . "' AND `item_id` = '".$data['item_id']."' ORDER BY `id` desc ";
    $qry_list = queryGet($sql_list);
    $quantity=$data['quantity'];
    if ($qry_list['status'] == "success") {
        $sqll = "UPDATE `erp_cart_item` SET 
            `updated_by`='" . $trade_name . "',
            `quantity`=`quantity`+$quantity
            WHERE id = ".$qry_list['data']['id']."";

        $insItems = queryUpdate($sqll);
        if ($insItems['status'] == "success") {
            $insItems["noOfItemsInCart"] = $noOfItemsInCart;
            sendApiResponse([
                "status" => "success",
                "message" => "Updated Successfully",
                "insItems" => $insItems
            ], 200);
        } else {
            $insItems["noOfItemsInCart"] = $noOfItemsInCart;
            sendApiResponse([
                "status" => "warning",
                "message" => "Something went wrong!1",
                "insItems" => $insItems,
                "qry_list" => $qry_list
            ], 400);
        }
    } else {
        $sqll = "INSERT INTO `erp_cart_item` SET 
            `company_id`='" . $company_id . "',
            `branch_id`='" . $branch_id . "',
            `location_id`='" . $location_id . "',
            `customer_id`='" . $customer_id . "',
            `item_id`='" . $data['item_id'] . "',
            `created_by`='" . $trade_name . "',
            `updated_by`='" . $trade_name . "',
            `quantity`='" . $quantity. "'";
        $insItems = queryInsert($sqll);

        if ($insItems['status'] == "success") {
            $insItems["noOfItemsInCart"] = $noOfItemsInCart+1;
            sendApiResponse([
                "status" => "success",
                "message" => "Inserted Successfully",
                "insItems" => $insItems
            ], 200);
        } else {
            $insItems["noOfItemsInCart"] = $noOfItemsInCart;
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
