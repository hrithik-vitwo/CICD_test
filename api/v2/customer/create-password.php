<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $authCustomer = authCustomerApiRequest();

    $newPassword = $_POST['newPassword'] ?? 0;
    $confirmPassword = $_POST['confirmPassword'] ?? 0;
    $adminKey = $_POST['adminKey'] ?? 0;

    if (!empty($newPassword) && !empty($confirmPassword) && !empty($adminKey)) {
        if ($newPassword != $confirmPassword) {
            sendApiResponse([
                "status" => "error",
                "message" => "Passwords do not match. Please try again."
            ], 400);
        }
        $sql = "UPDATE `tbl_customer_admin_details` SET `fldAdminPassword` = '$newPassword' WHERE `fldAdminKey` = '$adminKey'";
        $adminDetails = queryUpdate($sql);
        if($adminDetails['status'] == "success") {
            sendApiResponse([
                "status" => "success",
                "message" => "Password changed successfully.",
            ], 200);
        }else{
            sendApiResponse([
                "status" => "warning",
                "message" => "Something went wrong. Please try again."
            ], 400);
        }
    } else {
        sendApiResponse([
            "status" => "error",
            "message" => "Please provide all required fields."
        ], 500);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed. Please use a valid HTTP method."
    ], 405);
}
