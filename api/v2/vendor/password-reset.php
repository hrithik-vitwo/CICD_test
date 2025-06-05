<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $user_code = $_POST['user_code'];
    $company_code = $_POST['company_code'];
    $otp = $_POST['otp'];

    $company_sql = queryGet("SELECT * FROM `" . ERP_COMPANIES . "` WHERE `company_code`='$company_code'");
    if ($company_sql["status"] == "success") {
        $company_id = $company_sql["data"]["company_id"];
        $sql = "SELECT 
            `tbl_vendor_admin_details`.`fldAdminVendorId`, 
            `tbl_vendor_admin_details`.`otp`, 
            `tbl_vendor_admin_details`.`expiryTime`
            FROM 
                `tbl_vendor_admin_details`, " . ERP_VENDOR_DETAILS . " 
            WHERE 
            " . ERP_VENDOR_DETAILS . ".`vendor_id`= `tbl_vendor_admin_details`.`fldAdminVendorId` 
            AND 
            " . ERP_VENDOR_DETAILS . ".`vendor_code`='$user_code' 
            AND 
            " . ERP_VENDOR_DETAILS . ".`company_id`=$company_id 
            AND 
            " . ERP_VENDOR_DETAILS . ".`vendor_status`='active' 
            AND 
            `fldAdminStatus`='active'
        ";

        $vendorObj = queryGet($sql);
        if ($vendorObj["status"] == "success") {
            $vendor_id = $vendorObj["data"]["fldAdminVendorId"];
            $storedOtp = $vendorObj["data"]["otp"];
            $otpExpiryTime = $vendorObj["data"]["expiryTime"];
            $resetToken = $vendorObj["data"]["resetToken"];

            // Check if OTP is valid and not expired
            if ($otp == $storedOtp && strtotime($otpExpiryTime) > time()) {

                // $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePasswordSql = queryUpdate("UPDATE `tbl_vendor_admin_details` 
                    SET 
                        `fldAdminPassword`='$newPassword',
                        `otp` = NULL, 
                        `expiryTime` = NULL,
                        `resetToken` = NULL
                    WHERE 
                        `fldAdminVendorId`='$vendor_id'
                ");

                if ($updatePasswordSql["status"] == "success") {
                    sendApiResponse([
                        "status" => "success",
                        "message" => "Password has been reset successfully.",
                        "data" => []
                    ], 200);
                } else {
                    sendApiResponse([
                        "status" => "error",
                        "message" => "Failed to reset password.",
                        "data" => []
                    ], 400);
                }
            } else {
                sendApiResponse([
                    "status" => "warning",
                    "message" => "Invalid or expired OTP.",
                    "data" => []
                ], 400);
            }
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Invalid vendor",
                "data" => []
            ], 400);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Invalid Company Code",
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
