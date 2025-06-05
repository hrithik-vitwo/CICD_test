<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
    $branch_id = $authCustomer['branch_id'];
    $location_id = $authCustomer['location_id'];

    $quotation_id = $_POST['quotation_id'] ?? 0;
    $quotation_status = $_POST['quotation_status'] ?? "";
    $remarks = $_POST['remarks'] ?? "";
    
    if($quotation_status == 'accepted'){
        $status = 16;
        $msg = "Quotation accepted successfully";
    }else{
        $status = 19;
        $msg = "Quotation rejected successfully";
    }

    $updateSql = "UPDATE `erp_branch_quotations` SET `approvalStatus`=$status, `remarks`='$remarks' WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id AND `quotation_id`=$quotation_id";
    $update = queryUpdate($updateSql);

    if ($update['status'] == 'success') {
        sendApiResponse([
            "status" => "success",
            "message" => $msg
        ], 200);
    } else {
        sendApiResponse([
            "status" => "warning",
            "updateSql" => $updateSql,
            "message" => "Somthing went wrong!"
        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}
