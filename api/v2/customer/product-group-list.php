<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];

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

    // $sql_list = "SELECT `goodGroupId` as `groupId`, `companyId`, `goodGroupName`, `goodGroupDesc`, `groupParentId`, `goodType`, `goodGroupCreatedAt` as `createdAt`, `goodGroupCreatedBy` as `createdBy`, `goodGroupStatus` as `status` FROM `erp_inventory_mstr_good_groups` WHERE 1 " . $cond . " ORDER BY `goodGroupId` desc limit $start, $end";
    
    $sql_list = "SELECT DISTINCT grp.* FROM erp_inventory_mstr_good_groups AS grp JOIN erp_inventory_items AS item ON grp.goodGroupId = item.goodsGroup WHERE grp.companyId = $company_id";
          
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        $iv_data = $iv_sql["data"];
        // sendApiResponse($iv_sql, 200);
        sendApiResponse([
            "status" => "success",
            "message" => "Data found successfully",
            "count" => count($iv_data),
            "data" => $iv_data
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No not found",
            "data" => []
        ], 200);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}