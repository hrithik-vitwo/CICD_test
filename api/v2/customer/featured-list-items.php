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

    $sql_list = "SELECT
      summary.*,
      items.*,
      hsn.taxPercentage AS taxPercentage
      FROM  `" . ERP_INVENTORY_ITEMS . "` AS items
      LEFT JOIN `" . ERP_INVENTORY_STOCKS_SUMMARY . "` AS summary ON items.itemId = summary.itemId
      LEFT JOIN `" . ERP_HSN_CODE . "` AS hsn ON items.hsnCode = hsn.hsnCode
      WHERE items.goodsType IN (3, 4)
          AND items.status = 'active'
          AND (summary.company_id = $company_id OR summary.company_id IS NULL)
          AND (summary.status = 'active' OR summary.status IS NULL)
          AND items.hsnCode IN (SELECT hsnCode FROM `erp_hsn_code`)
          ORDER BY items.`itemId` desc limit " . $start . "," . $end . " ";
          
    // $sql_list = "SELECT * FROM `erp_inventory_items`   WHERE `company_id`='" . $company_id . "' AND `branch` = '$branch_id' AND `location_id` = '$location_id' " . $cond . " ORDER BY `itemId` desc limit " . $start . "," . $end . " ";




    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];

        $data_array = [];
        foreach ($iv_data as $key=> $data) {
            $data_array[$key] = array("items" => $data);
            
            $sql_cart_list = "SELECT * FROM `erp_cart_item`   WHERE `customer_id`='" . $customer_id . "' AND `item_id` = '" . $data['itemId'] . "' ORDER BY `id` desc ";
            $qry_cart_list = queryGet($sql_cart_list);

            $data_array[$key]['items']['isClicked'] = ($qry_cart_list['status'] == "success") ? true : false;

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
            "message" => "No not found",
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