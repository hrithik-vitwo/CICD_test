<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

if (isset($_GET['id']) && $_GET['cust_id']) {
    $array_value = [];
    $cust_id = $_GET['cust_id'];

    $year_id = $_GET['id'];
    $year_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `year_variant_id`=$year_id");
    $data = $year_sql['data'];

    $start = explode('-', $data['year_start']);
    $end = explode('-', $data['year_end']);
    $startDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));
    $endDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));

    $toDate  = $startDate;
    $fromDate = $endDate;

    $sql_list_all_cust = queryGet("SELECT DATE_FORMAT(invoices.invoice_date,'%Y-%m') AS date_,SUM(invoices.all_total_amt) AS total_receivable,SUM(invoices.all_total_amt - invoices.due_amount) AS total_received FROM erp_branch_sales_order_invoices AS invoices WHERE invoices.invoice_date BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND invoices.status='active' AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id  GROUP BY date_;", true);

    $sql_list_specific_cust = queryGet("SELECT DATE_FORMAT(invoices.invoice_date,'%Y-%m') AS date_,SUM(invoices.all_total_amt) AS total_receivable,SUM(invoices.all_total_amt - invoices.due_amount) AS total_received FROM erp_branch_sales_order_invoices AS invoices WHERE invoices.invoice_date BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND invoices.status='active' AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND invoices.customer_id=$cust_id GROUP BY date_;", true);

    $array_value['sql_list_all_cust'] = $sql_list_all_cust['data'];
    $array_value['sql_list_specific_cust'] = $sql_list_specific_cust['data'];

    echo json_encode($array_value, true);
} else if (isset($_GET['month']) && $_GET['cust_id']) {
    $array_value = [];
    $cust_id = $_GET['cust_id'];
    $month = $_GET['month'];

    // Calculate the start and end dates of the selected month
    $fromDate = date('Y-m-01', strtotime("{$month}-01"));
    $toDate = date('Y-m-t', strtotime("{$month}-01"));

    $sql_list_all_cust = queryGet("SELECT invoices.invoice_date AS date_,SUM(invoices.all_total_amt) AS total_receivable,SUM(invoices.all_total_amt - invoices.due_amount) AS total_received FROM erp_branch_sales_order_invoices AS invoices WHERE invoices.invoice_date BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND invoices.status='active' AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id GROUP BY date_;", true);

    $sql_list_specific_cust = queryGet("SELECT invoices.invoice_date AS date_,SUM(invoices.all_total_amt) AS total_receivable,SUM(invoices.all_total_amt - invoices.due_amount) AS total_received FROM erp_branch_sales_order_invoices AS invoices WHERE invoices.invoice_date BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND invoices.status='active' AND invoices.company_id=$company_id AND invoices.branch_id=$branch_id AND invoices.location_id=$location_id AND invoices.customer_id=$cust_id GROUP BY date_;", true);

    $array_value['sql_list_all_cust'] = $sql_list_all_cust['data'];
    $array_value['sql_list_specific_cust'] = $sql_list_specific_cust['data'];

    echo json_encode($array_value, true);
} else if (isset($_GET['id']) && $_GET['customer_id']) {
    $cust_id = $_GET['customer_id'];

    $year_id = $_GET['id'];
    $year_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `year_variant_id`=$year_id");
    $data = $year_sql['data'];

    $start = explode('-', $data['year_start']);
    $end = explode('-', $data['year_end']);
    $startDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));
    $endDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));

    $toDate  = $startDate;
    $fromDate = $endDate;

    $receivable_ageing = queryGet("SELECT erp_customer.trade_name,table1.due_days,table1.count_,total_due_amount FROM (SELECT customer_id,DATEDIFF((DATE_ADD(invoice_date, INTERVAL credit_period DAY)),CURDATE())AS due_days,COUNT(*) AS count_,SUM(due_amount) AS total_due_amount FROM erp_branch_sales_order_invoices WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND invoice_date BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND due_amount!=0 AND (DATE_ADD(invoice_date, INTERVAL credit_period DAY))>CURDATE() GROUP BY customer_id,due_days) AS table1 LEFT JOIN erp_customer ON table1.customer_id=erp_customer.customer_id WHERE erp_customer.customer_id=$cust_id ORDER BY table1.due_days;", true);

    echo json_encode($receivable_ageing, true);
} 
