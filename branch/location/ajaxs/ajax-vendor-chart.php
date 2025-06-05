<?php
require_once("../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');

if (isset($_GET['id']) && $_GET['vend_id']) {
    $array_value = [];
    $vend_id = $_GET['vend_id'];

    $year_id = $_GET['id'];
    $year_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `year_variant_id`=$year_id");
    $data = $year_sql['data'];

    $start = explode('-', $data['year_start']);
    $end = explode('-', $data['year_end']);
    $startDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));
    $endDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));

    $toDate  = $startDate;
    $fromDate = $endDate;

    $sql_list_all_cust = queryGet("SELECT DATE_FORMAT(postingDate,'%Y-%m') AS date_, SUM(grnTotalAmount) AS total_payable,SUM(grnTotalAmount - dueAmt) AS total_paid FROM erp_grninvoice WHERE companyId=$company_id AND branchId=$branch_id AND locationId=$location_id AND postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND grnStatus='active' GROUP BY date_;", true);

    $sql_list_specific_cust = queryGet("SELECT DATE_FORMAT(postingDate,'%Y-%m') AS date_, SUM(grnTotalAmount) AS total_payable,SUM(grnTotalAmount - dueAmt) AS total_paid FROM erp_grninvoice WHERE companyId=$company_id AND branchId=$branch_id AND locationId=$location_id AND postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND grnStatus='active' AND vendorId=$vend_id GROUP BY date_;", true);

    $array_value['sql_list_all_cust'] = $sql_list_all_cust['data'];
    $array_value['sql_list_specific_cust'] = $sql_list_specific_cust['data'];

    echo json_encode($array_value, true);
} else if (isset($_GET['month']) && $_GET['vend_id']) {
    $array_value = [];
    $vend_id = $_GET['vend_id'];
    $month = $_GET['month'];

    // Calculate the start and end dates of the selected month
    $fromDate = date('Y-m-01', strtotime("{$month}-01"));
    $toDate = date('Y-m-t', strtotime("{$month}-01"));

    $sql_list_all_cust = queryGet("SELECT postingDate AS date_, SUM(grnTotalAmount) AS total_payable,SUM(grnTotalAmount - dueAmt) AS total_paid FROM erp_grninvoice WHERE companyId=$company_id AND branchId=$branch_id AND locationId=$location_id AND postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND grnStatus='active' GROUP BY date_;", true);

    $sql_list_specific_cust = queryGet("SELECT postingDate AS date_, SUM(grnTotalAmount) AS total_payable,SUM(grnTotalAmount - dueAmt) AS total_paid FROM erp_grninvoice WHERE companyId=$company_id AND branchId=$branch_id AND locationId=$location_id AND postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND grnStatus='active' AND vendorId=$vend_id GROUP BY date_;", true);

    $array_value['sql_list_all_cust'] = $sql_list_all_cust['data'];
    $array_value['sql_list_specific_cust'] = $sql_list_specific_cust['data'];

    echo json_encode($array_value, true);
} else if (isset($_GET['id']) && $_GET['vendor_id']) {
    $vend_id = $_GET['vendor_id'];

    $year_id = $_GET['id'];
    $year_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `year_variant_id`=$year_id");
    $data = $year_sql['data'];

    $start = explode('-', $data['year_start']);
    $end = explode('-', $data['year_end']);
    $startDate = date('Y-m-t', strtotime("$end[0]-$end[1]"));
    $endDate = date('Y-m-01', strtotime("$start[0]-$start[1]"));

    $toDate  = $startDate;
    $fromDate = $endDate;

    $payable_ageing = queryGet("SELECT vendorName,DATEDIFF(dueDate,CURDATE())AS due_days,COUNT(*) AS count_,SUM(dueAmt) AS total_due_amount FROM erp_grninvoice AS iv WHERE companyId=$company_id AND branchId=$branch_id AND locationId=$location_id AND postingDate BETWEEN '" . $fromDate . "' AND '" . $toDate . "' AND dueAmt!=0 AND dueDate>CURDATE() AND vendorId=$vend_id GROUP BY vendorName,due_days ORDER BY due_days;", true);

    echo json_encode($payable_ageing, true);
} 
