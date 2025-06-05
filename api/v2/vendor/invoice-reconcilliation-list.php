<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // echo 1;
    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

    $pageNo = $_POST['pageNo'];
    $show = $_POST['limit'];
    $start = $pageNo * $show;
    $end = $show;

    $cond = '';

    if (isset($_POST['formDate']) && $_POST['formDate'] != '') {
        $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    if (isset($_POST['keyword']) && $_POST['keyword'] != '') {
        $cond .= " AND (`type` like '%" . $_POST['keyword'] . "%' OR `reconciliationType` like '%" . $_POST['keyword'] . "%')";
    }

    $sql_list = "SELECT company_id, branch_id, location_id, type, reconciliationType, code, CONCAT('" . BUCKET_URL . "','uploads/$company_id/acc-statement/', files) as files,files as filesName , created_at, created_by, updated_at, updated_by, status FROM erp_reconciliation 
    WHERE company_id=" . $company_id . " AND branch_id = " . $branch_id . " AND location_id = " . $location_id . " AND type='vendor' AND reconciliationType='invoice' " . $cond . " ORDER BY id desc limit " . $start . "," . $end . "";
    $iv_sql = queryGet($sql_list, true);

    if ($iv_sql['status'] == "success") {

        sendApiResponse([
            "status" => "success",
            "message" => "Data found",
            "data" =>  $iv_sql["data"]

        ], 200);
    } else {
        sendApiResponse([
            "status" => "success",
            "message" => "No data found",
            "data" => []

        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "warning",
        "message" => "Not found",
        "data" => []

    ], 400);
}
