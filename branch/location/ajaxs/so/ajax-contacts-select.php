<?php
require_once("../../../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');
$responseData = [];
$start = 0;
$limit = 30;
$cond = '';

if (isset($_REQUEST['searchTerm']) && !empty($_REQUEST['searchTerm'])) {
    $searchTerm = $_REQUEST['searchTerm'];
    $cond .= " AND (`email` LIKE '%" . $searchTerm . "%' OR `phone` LIKE '%" . $searchTerm . "%' OR `name` LIKE '%" . $searchTerm . "%')";
    $limit = 50;
}

if (!empty($_REQUEST['page'])) {
    $start = $_REQUEST['page'] * $limit;
}

$sql = "SELECT config_id as id, CONCAT(name, ' | ', email, ' | ', phone) as text 
        FROM `erp_config_invoices` 
        WHERE `company_id` = '" . $company_id . "' 
        AND `branch_id` = '" . $branch_id . "' 
        AND `location_id` = '" . $location_id . "' 
        " . $cond . " 
        LIMIT $start, $limit";

$query = queryGet($sql, true);

if ($query['status'] == "success") {
    $returnData['status'] = "success";
    $returnData['message'] = "Data found";
    $returnData['data'] = $query['data'];
    $datalist = $query['data'];
} else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Something went wrong";
    $returnData['data'] = [];
    $datalist = [];
}

echo json_encode($datalist);
?>