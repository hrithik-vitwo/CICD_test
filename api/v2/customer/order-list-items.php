<?php
require_once("api-common-func.php");

// for test

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $order_type = $_POST['order_type'];
    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`order_code` like '%" . $_POST['keyword'] . "%' OR `created_at` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT * FROM `erp_party_order`
        WHERE 
            `customer_id` = '" . $customer_id . "'
        AND
            order_type='$order_type' " . $cond . "
    ORDER BY `id` DESC LIMIT " . $start . "," . $end . " ";

    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];
        foreach ($iv_data as $key => $data) {
            $sql_item_list = "SELECT 
                `erp_inventory_items`.*, 
                `erp_party_order_item`.`quantity`, 
                true AS 'isClicked',
                `erp_inventory_stocks_summary`.`itemPrice`
            FROM 
                `erp_party_order_item`
            LEFT JOIN 
                `erp_inventory_items` 
                ON `erp_party_order_item`.`item_id` = `erp_inventory_items`.`itemId`
            LEFT JOIN 
                `erp_inventory_stocks_summary` 
                ON `erp_inventory_stocks_summary`.`itemId` = `erp_inventory_items`.`itemId`
            WHERE 
                `erp_party_order_item`.`order_id` = '" . $data['id'] . "'
            ORDER BY 
                `erp_party_order_item`.`id` DESC;
            ";
            $qry_item = queryGet($sql_item_list, true);

            $data_array[$key] = array("ordermain" => $data);
            $data_array[$key]['ordermain']['items'] = $qry_item['data'];
        }
        
        sendApiResponse([
            "status" => "success",
            "message" => count($data_array) . " data found",
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
