<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'] ?? 0;
    $company_id = $authCustomer['company_id'] ?? 0;
    $branch_id = $authCustomer['branch_id'] ?? 0;
    $location_id = $authCustomer['location_id'] ?? 0;

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `createdAt` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`itemCode` like '%" . $_POST['keyword'] . "%' OR `itemName` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT * FROM `erp_cart_item` WHERE `company_id` = $company_id AND `branch_id` = $branch_id AND `location_id` = $location_id AND `customer_id` = $customer_id $cond ORDER BY `id` DESC LIMIT $start, $end";
    
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];
        
        foreach ($iv_data as $key => $value) {
            $itemSql = "SELECT * FROM `erp_inventory_items` WHERE `itemId` = " . $value["item_id"];
            $itemSql = queryGet($itemSql);
            $iv_data[$key]["item"] = $itemSql["data"];
        }

        sendApiResponse([
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "numRows" => $iv_sql['numRows'],
            "data" => $iv_data,

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "sql" => $sql_list,
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