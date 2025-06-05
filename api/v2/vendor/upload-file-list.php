<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    global $company_id;
    global $branch_id;
    global $location_id;
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

    $cond = "";

    if (isset($_POST['formDate']) && $_POST['formDate'] != '' && isset($_POST['toDate']) && $_POST['toDate'] != '') {
        $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
        $from_date = $_POST['formDate'];
        $to_date = $_POST['toDate'];
    }

    $files = $_FILES['fileToUpload'];
    $type = 'vendor';
    $reconcilation_type = $_POST['reconcilation_type'];

    $fetchFileList = queryGet("SELECT *, CONCAT('" . BUCKET_URL . "uploads/$company_id/others/', `files`) AS `files` FROM `erp_reconciliation` WHERE `reconciliationType` = '" . $reconcilation_type . "' AND `company_id` = '" . $company_id . "' AND `branch_id` = '" . $branch_id . "' AND `location_id` = '" . $location_id . "' AND `code` = '" . $vendor_id . "' AND `type` = '" . $type . "' " . $cond ." ORDER BY `id` DESC ", true);
    
    if ($fetchFileList['status'] == 'success') {
        sendApiResponse([
            "status" => "success",
            "count" => count($fetchFileList['data']),
            "message" => "Successfully fetch file list",
            "data" => $fetchFileList['data']
        ]);
    }else{
        sendApiResponse([
            "status" => "warning",
            "message" => "Data not found",
            "data" => ""
        ]);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => ""
    ], 405);
}
