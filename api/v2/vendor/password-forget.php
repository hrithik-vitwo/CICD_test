<?php
require_once("api-common-func.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_code = $_POST['company_code'];
    $user_code = $_POST['user_code']; // Assuming user_code is used to identify the vendor

    $company_sql = queryGet("SELECT * FROM `" . ERP_COMPANIES . "` WHERE `company_code`=$company_code");
    if ($company_sql["status"] == "success") {
        $company_id = $company_sql["data"]["company_id"];
        $sql = "SELECT 
            `tbl_vendor_admin_details`.`fldAdminEmail`, 
            `tbl_vendor_admin_details`.`fldAdminVendorId`, 
            `tbl_vendor_admin_details`.`fldAdminName`, 
            " . ERP_VENDOR_DETAILS . ".`vendor_code` 
            FROM 
                `tbl_vendor_admin_details`, " . ERP_VENDOR_DETAILS . " 
            WHERE 
            " . ERP_VENDOR_DETAILS . ".`vendor_id`= `tbl_vendor_admin_details`.`fldAdminVendorId` 
            AND 
            " . ERP_VENDOR_DETAILS . ".`vendor_code`='" . $user_code . "' 
            AND 
            " . ERP_VENDOR_DETAILS . ".`company_id`=$company_id 
            AND 
            " . ERP_VENDOR_DETAILS . ".`vendor_status`='active' 
            AND 
            `fldAdminStatus`='active'
        ";

        $vendorObj = queryGet($sql);
        if ($vendorObj["status"] == "success") {
            // Generate a password reset token
            $resetToken = bin2hex(random_bytes(16));
            $vendor_id = $vendorObj["data"]["fldAdminVendorId"];
            $otp = random_int(100000, 999999);

            // Save the reset token in the database with an expiration time (e.g., 1 hour)
            $expiryTime = date("Y-m-d H:i:s", strtotime('+1 hour'));
            $updateTokenSql = "UPDATE `tbl_vendor_admin_details` SET 
                `otp` = $otp, 
                `resetToken` = '$resetToken', 
                `expiryTime` = '$expiryTime' 
                WHERE `fldAdminVendorId` = $vendor_id
            ";
            queryUpdate($updateTokenSql);

            // $resetLink = BASE_URL . "/reset-password.php?token=" . $resetToken;
            $email = $vendorObj["data"]["fldAdminEmail"];
            $vendor_name = $vendorObj["data"]["fldAdminName"];

            // Assuming you have a function to send emails
            $mail = SendMailByMySMTPmailTemplate(
                $email,
                "Password Reset OTP",
                "Hello $vendor_name,\n\nYour OTP for password reset is: $otp\n\nThis OTP will expire in 1 hour."
            );

            sendApiResponse([
                "status" => "success",
                "message" => "OTP sent successfully to $email",
                "data" => []
            ], 200);
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
