<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $formDate = $_POST['formDate'] . " 00:00:00";
    $toDate = $_POST['toDate'] . " 23:59:59";

    $sql_list = "SELECT
        COALESCE(SUM(`all_total_amt`), 0) AS totalInvoiceAmt,
        COALESCE(SUM(`due_amount`), 0) AS totalDueAmount
    FROM
        (SELECT 0 AS `dummy`) AS `dummy_table`
    LEFT JOIN
        `erp_branch_sales_order_invoices` AS `invoices`
        ON `invoices`.`customer_id` = $customer_id
        AND `invoices`.`company_id` = $company_id
        AND `invoices`.`branch_id` = $branch_id
        AND `invoices`.`location_id` = $location_id
        AND `invoiceStatus` != 4
        AND `invoices`.`created_at` BETWEEN '".$formDate."' AND '".$toDate."'
    GROUP BY
        `dummy_table`.`dummy`
    ";
    $iv_sql = queryGet($sql_list);

    if ($iv_sql['status'] == "success") {
        
        $iv_data = $iv_sql["data"];
        $data_array = [$iv_data];

        sendApiResponse([
            "status" => "success",
            "message" => "Data fetch successfully",
            "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Data Not Found!",
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