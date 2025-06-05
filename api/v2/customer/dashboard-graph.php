<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    if (isset($_POST['formDate']) && isset($_POST['toDate']) && $_POST['formDate'] != '' && $_POST['toDate'] != '') {
        $formDate = $_POST['formDate'] . " 00:00:00";
        $toDate = $_POST['toDate'] . " 00:00:00";
        $cond .= " AND `created_at` BETWEEN '$formDate' AND '$toDate'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $keyword = $_POST['keyword'];
        $cond .= " AND (`itemCode` LIKE '%$keyword%' OR `itemName` LIKE '%$keyword%')";
    }

    // Show only months
    // $sql = "SELECT
    //         MONTHNAME(created_at) AS month,
    //         IFNULL(SUM(CASE WHEN payment_type = 'pay' THEN payment_amt ELSE 0 END), 0) AS total_paid_amount,
    //         GREATEST(
    //             IFNULL(SUM(CASE WHEN payment_type = 'advanced' THEN payment_amt ELSE 0 END), 0) -
    //             IFNULL(SUM(CASE WHEN payment_type = 'pay' THEN payment_amt ELSE 0 END), 0),
    //             0
    //         ) AS total_due_amount
    //     FROM
    //         erp_branch_sales_order_payments_log
    //     WHERE
    //         company_id = 1
    //         AND branch_id = 1
    //         AND location_id = 8
    //         AND customer_id = 1
    //     GROUP BY
    //         MONTH(created_at),
    //         MONTHNAME(created_at)
    //     ORDER BY
    //         MONTH(created_at)";
    
    $sql = " SELECT
            months.month AS month,
            IFNULL(SUM(CASE WHEN payments.payment_type = 'pay' THEN payments.payment_amt ELSE 0 END), 0) AS total_paid_amount,
            GREATEST(
                IFNULL(SUM(CASE WHEN payments.payment_type = 'advanced' THEN payments.payment_amt ELSE 0 END), 0) -
                IFNULL(SUM(CASE WHEN payments.payment_type = 'pay' THEN payments.payment_amt ELSE 0 END), 0),
                0
            ) AS total_due_amount
        FROM
            (
                SELECT 1 AS month_num, 'JAN' AS month
                UNION SELECT 2 AS month_num, 'FEB' AS month
                UNION SELECT 3 AS month_num, 'MAR' AS month
                UNION SELECT 4 AS month_num, 'APR' AS month
                UNION SELECT 5 AS month_num, 'MAY' AS month
                UNION SELECT 6 AS month_num, 'JUN' AS month
                UNION SELECT 7 AS month_num, 'JUL' AS month
                UNION SELECT 8 AS month_num, 'AUG' AS month
                UNION SELECT 9 AS month_num, 'SEP' AS month
                UNION SELECT 10 AS month_num, 'OCT' AS month
                UNION SELECT 11 AS month_num, 'NOV' AS month
                UNION SELECT 12 AS month_num, 'DEC' AS month
            ) AS months
        LEFT JOIN
            erp_branch_sales_order_payments_log AS payments
            ON months.month_num = MONTH(payments.created_at)
                AND payments.company_id = $company_id
                AND payments.branch_id = $branch_id
                AND payments.location_id = $location_id
                AND payments.customer_id = $customer_id
        GROUP BY
            months.month, months.month_num
        ORDER BY
            FIELD(months.month, 'JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC')
    ";

    $result = queryGet($sql, true);

    if ($result['status'] == "success") {
        $data = $result["data"];
        $response_data = [];

        foreach ($data as $row) {
            $month = $row['month'];
            $total_paid_amount = $row['total_paid_amount'];
            $total_due_amount = $row['total_due_amount'];

            $response_data[$month] = [
                "total_paid_amount" => number_format((float)$total_paid_amount, 2, '.', ''),
                "total_due_amount" => number_format((float)$total_due_amount, 2, '.', '')
            ];
        }

        sendApiResponse([
            "status" => "success",
            "message" => "Data found",
            "data" => $response_data
        ], 200);
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

echo json_encode($response_data);
