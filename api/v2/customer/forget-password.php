<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $enteredOtp = isset($_POST['otp']) ? $_POST['otp'] : null;

    $customerCode = $_POST['customerCode'];
    $companyCode = $_POST['companyCode'];

    $adminDetails = queryGet("SELECT customer.*, company.* FROM `tbl_customer_admin_details` as customer, `erp_companies` as company WHERE customer.company_id = company.company_id AND customer.customer_code = '$customerCode' AND company.company_code = '$companyCode'");

    if ($adminDetails["status"] == "success") {
        $customerEmail = $adminDetails["data"]["fldAdminEmail"];
        $customerName = $adminDetails["data"]["fldAdminName"];

        // Check if OTP is provided in the request.
        if ($enteredOtp !== null) {
            
            $storedOtp = $adminDetails["data"]["otp"];

            if ($enteredOtp == $storedOtp) {

                sendApiResponse([
                    "status" => "success",
                    "message" => 'OTP validation successful. Proceed to reset your password.',
                    "data" => [
                        $adminDetails["data"]["fldAdminKey"],
                        $adminDetails["data"]["fldAdminEmail"]
                    ]
                ], 200);
            } else {
                sendApiResponse([
                    "status" => "error",
                    "message" => "Invalid OTP. Please enter the correct OTP.",
                    "data" => []
                ], 400);
            }
        } else {
            // Send OTP via email
            $otp = rand(1000, 9999);

            $adminDetails = queryGet("UPDATE `tbl_customer_admin_details` SET `otp` = '$otp' WHERE `fldAdminEmail` = '$customerEmail'");

            $to = $customerEmail;
            $sub = 'Password Reset Request';
            $msg = '
                    <div>
                    <div><strong>Dear ' . $customerName . ',</strong></div>
                    <p>
                        We received a request to reset your password.
                    </p>
                    <p>
                        Your One-Time Password (OTP) for password reset is <b>' . $otp . '</b>
                    </p>
                    </div>';
            $mail =  SendMailByMySMTPmailTemplate($to, $sub, $msg);

            sendApiResponse([
                "status" => "success",
                "message" => "Check your email for password reset instructions.",
                "data" => [
                    "key" => $adminDetails["data"]["fldAdminKey"],
                    "email" => $adminDetails["data"]["fldAdminEmail"],
                    "otp" => $otp
                ]
            ], 200);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Email not found. Please check the entered email address.",
            "sql" => $adminDetails
        ], 400);
    }
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed. Please use a valid HTTP method.",
        "data" => []
    ], 405);
}
