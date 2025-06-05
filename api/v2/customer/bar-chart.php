<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $approvalStatus = $_POST['approvalStatus'] ?? 0;
    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (!empty($_POST['formDate']) && !empty($_POST['toDate'])) {
        $startDate = $_POST['formDate'];
        $endDate = $_POST['toDate'];
    } else {
        $startDate = date('Y-01-01');
        $endDate = date('Y-12-31');
    }

    // Generate all months in the date range
    $allMonths = [];
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    while ($start <= $end) {
        $allMonths[] = $start->format('Y-m');
        $start->modify('+1 month');
    }

    if (!empty($_POST['formDate'])) {
        $cond .= " AND inv.`invoice_date` BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    }

    if (!empty($_POST['keyword'])) {
        $keyword = $_POST['keyword'];
        $cond .= " AND (inv.`invoice_no` LIKE '%$keyword%' OR inv.`invoice_date` LIKE '%$keyword%')";
    }

    // Query to get invoice data grouped by month
    $sql_list = "SELECT 
    DATE_FORMAT(inv.invoice_date, '%Y-%m') AS month, 
    COUNT(DISTINCT inv.so_invoice_id) AS total_invoices, 
    SUM(inv.all_total_amt) AS total_amount,
    SUM(inv.due_amount) AS total_due_amount,
    COALESCE(
        (SELECT SUM(pay.collect_payment) 
         FROM `erp_branch_sales_order_payments` AS pay 
         WHERE pay.customer_id = inv.customer_id 
           AND DATE_FORMAT(pay.documentDate, '%Y-%m') = DATE_FORMAT(inv.invoice_date, '%Y-%m')
        ), 0) AS total_collection
FROM `erp_branch_sales_order_invoices` AS inv
WHERE 
    inv.`company_id` = $company_id
    AND inv.`branch_id` = $branch_id
    AND inv.`location_id` = $location_id
    AND inv.`customer_id` = $customer_id
    $cond
GROUP BY month
ORDER BY month";

    $iv_sql = queryGet($sql_list, true);

    // Initialize an array with all months having 0 values
    $monthlyData = [];
    foreach ($allMonths as $month) {
        $monthlyData[$month] = [
            "month" => $month,
            "total_invoices" => 0,
            "total_amount" => 0,
            "total_due_amount" => 0,
            "total_collection" => 0
        ];
    }

    // If there are results, replace the default values with actual data
    if ($iv_sql['status'] == "success") {
        foreach ($iv_sql["data"] as $row) {
            $monthlyData[$row['month']] = $row;
        }
    }

    $response = array_values($monthlyData);

    sendApiResponse([
        "status" => "success",
        "message" => count($response) . " data found",
        "data" => $response
    ], 200);
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
