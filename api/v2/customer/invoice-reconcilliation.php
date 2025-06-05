<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

     $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $fileUploaded = uploadFile($_FILES["invoice"], BUCKET_DIR . "uploads/$company_id/acc-statement/", ["pdf", "jpg","csv","xlsx"]);
    if ($fileUploaded["status"] == "success") {
        $file = $fileUploaded['data'];

        $invIns = queryInsert("INSERT INTO `erp_reconciliation` 
        SET 
            `company_id`='$company_id',
            `branch_id`='$branch_id',
            `location_id`='$location_id',
            `type`='customer',
            `reconciliationType`='invoice',
            `code`='$customer_id',
            `files`='$file'
        ");
        if ($invIns['status'] == "success") {
            sendApiResponse([
                "status" => "success",
                "message" => "Upload success",
                "data" => BUCKET_URL . "uploads/$company_id/acc-statement/" . $fileUploaded["data"]
            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Somthing went wrong!"
            ], 400);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Upload failed",
            "data" => ""
        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => ""
    ], 405);
}
