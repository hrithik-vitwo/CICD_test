<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authvendor = authVendorApiRequest();
    $vendor_id = $authvendor['vendor_id'];
    $company_id = $authvendor['company_id'];
    $branch_id = $authvendor['branch_id'];
    $location_id = $authvendor['location_id'];

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && isset($_POST['toDate']) && $_POST['formDate'] != '' && $_POST['toDate'] != '') {
        $formDate = $_POST['formDate'] . " 00:00:00";
        $toDate = $_POST['toDate'] . " 00:00:00";
        $cond .= " AND `created_at` BETWEEN '$formDate' AND '$toDate'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $keyword = $_POST['keyword'];
        $cond .= " AND (`itemCode` LIKE '%$keyword%' OR `itemName` LIKE '%$keyword%')";
    }

    $sql = "SELECT
                IFNULL(SUM(CASE WHEN `payment_type` = 'pay' THEN `payment_amt` ELSE 0 END), 0) AS total_paid_amount,
                GREATEST(IFNULL(SUM(CASE WHEN `payment_type` = 'advanced' THEN `payment_amt` ELSE 0 END), 0) -
                        IFNULL(SUM(CASE WHEN `payment_type` = 'pay' THEN `payment_amt` ELSE 0 END), 0), 0) AS total_due_amount
            FROM
                `erp_grn_payments_log`
            WHERE
                `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `vendor_id` = '$vendor_id'$cond";

    $result = queryGet($sql, true);

    if ($result['status'] == "success") {
        $data = $result["data"];

        if (empty($data)) {
            sendApiResponse([
                "status" => "warning",
                "message" => "No data found this date range",
                "data" => [
                    "vendor_payment_details" => [
                        "total_paid_amount" => 0,
                        "total_due_amount" => 0
                    ]
                ]
            ], 400);
        } else {
            $row = $data[0];
            $total_paid_amount = $row['total_paid_amount'];
            $total_due_amount = $row['total_due_amount'];

            sendApiResponse([
                "status" => "success",
                "message" => "Data found",
                "data" => [
                    "vendor_payment_details" => [
                        "total_paid_amount" => $total_paid_amount,
                        "total_due_amount" => $total_due_amount
                    ]
                ]
            ], 200);
        }
    } else {
        sendApiResponse([
            "status" => "error",
            "message" => "Error retrieving data",
            "sql" => $sql,
            "data" => []
        ], 500);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
