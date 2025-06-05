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
        $cond .= " AND `customer_address_created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`customer_address_street_name` like '%" . $_POST['keyword'] . "%' OR `customer_address_pin_code` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT * FROM `erp_customer_address` " . $cond . " ORDER BY `customer_address_id` desc limit " . $start . "," . $end . " ";
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];
        
        $data_array = [];
        foreach ($iv_data as $data) {
            
            $data_array[] = array("customer_address" => $data);
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
            "data2" => $sql_list,
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