<?php
require_once("api-common-func.php");



// API CODE
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    //  echo 1;
    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];

   

    $sql_list = "SELECT SUM(`grnTotalAmount`) as totalInvoiceAmt, SUM(`dueAmt`) as totalDueAmount FROM `erp_grninvoice`   WHERE  `vendorId`='" . $vendor_id . "'  AND `companyId`='" . $company_id . "' AND `branchId` = '$branch_id' AND `locationId` = '$location_id' ";
    $iv_sql = queryGet($sql_list, true);

    //console($sql_list);
    

    if ($iv_sql['status'] == "success") {

        $data_array = [$iv_sql['data']];
        // console($data_array);
        sendApiResponse([
            "status" => "success",
            "message" => "success",
           "data" => $data_array

        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "No Invoice found",
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
//echo "ok";