<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;

    $authcustomer = authCustomerApiRequest();
    $customer_id = $authcustomer['customer_id'];
    $company_id = $authcustomer['company_id'];

    // *************************************
    // $itemsPerPage = $_POST['limit']; // Number of items to display per page
    // $current = isset($_POST['pageNo']) ? $_POST['pageNo'] : 1; // Current page number
    
    // $offset = ($current - 1) * $itemsPerPage; // Offset for the database query
    // $limit = $itemsPerPage; // Limit for the database query
    
    // *************************************
    $pageNo = isset($_POST['pageNo']) ? $_POST['pageNo'] : 1;
    $show = isset($_POST['limit']) ? $_POST['limit'] : 1;
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND created_at between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (so_number like '%" . $_POST['keyword'] . "%' OR delivery_date like '%" . $_POST['keyword'] . "%' OR goodsType like '%" . $_POST['keyword'] . "%')";
    }



    $so_sql = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER . "` WHERE 1 " . $cond . " AND `company_id`=$company_id AND `customer_id`=$customer_id ORDER BY so_id DESC LIMIT " . $start . "," . $end . " ", true);
    // console($so_sql["data"]);
    // exit();
    if ($so_sql['status'] == "success") {


        $so_data = $so_sql["data"];
        $data_array = [];
        foreach ($so_data as $data) {
            // console($data);
            $so_id = $data["so_id"];
            $so_items = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_ITEMS . "` as so_item, `" . ERP_INVENTORY_ITEMS . "` as item WHERE so_item.inventory_item_id=item.itemId AND `so_id`=$so_id", true);
            $so_item_data = $so_items["data"];

            $data_array[] = array("so" => $data, "so_item" => $so_item_data);
        }
        // console($data_array);
        sendApiResponse([
            "status" => "success",
            "message" => "data found",
            "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No sales order found",
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