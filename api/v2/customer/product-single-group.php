<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'] ?? 0;
    $company_id = $authCustomer['company_id'] ?? 0;
    $branch_id = $authCustomer['branch_id'] ?? 0;
    $location_id = $authCustomer['location_id'] ?? 0;

    $pageNo = $_POST['pageNo'] ?? 0;
    $show = $_POST['limit'] ?? 10;
    $goodGroupId = $_POST['goodGroupId'] ?? 0;
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `createdAt` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`itemCode` like '%" . $_POST['keyword'] . "%' OR `itemName` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT `companyId`, `goodGroupName`, `goodGroupDesc`, `groupParentId`, `goodType`, `goodGroupCreatedAt` as `createdAt`, `goodGroupCreatedBy` as `createdBy`, `goodGroupStatus` as `status` FROM `erp_inventory_mstr_good_groups` WHERE 1 " . $cond . "  AND `companyId`='" . $company_id . "' AND `groupParentId`= '" . $goodGroupId . "' ORDER BY `goodGroupId` desc limit $start, $end";
    
          
    $iv_sql = queryGet($sql_list, true);
    
    if ($iv_sql['status'] == "success") {
        
        $iv_data = $iv_sql["data"];
        
        sendApiResponse([
            "status" => $iv_sql['status'],
            "message" => $iv_sql['message'],
            "numRows" => $iv_sql['numRows'],
            "data" => $iv_data,
            "sql" => $iv_sql

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "data" => [],
            "sql" => $iv_sql
        ], 200);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}