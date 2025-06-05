<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $requestPost = requestBody();
    $kamCode = $requestPost['kamCode'];
  
    $sql = "SELECT * FROM `erp_kam` WHERE `status`='active' AND `kamCode`='" . $kamCode . "' LIMIT 1";

    $userObj = queryGet($sql);
    if ($userObj["status"] == "success") {
        $user = $userObj["data"];

        if (!empty($user["email"])) {
            $otp = rand(1000, 9999);
            $sql_fcm = queryUpdate("UPDATE `erp_kam` SET `otp` = '" . $otp . "',otp_created_at=NOW() WHERE `kamId`='" . $userObj["data"]["kamId"] . "'");

            $email = $user["email"];

            $sub = "Claimz Sales Penson KAM verification OTP Mail";
            $msg = "OTP: " . $otp;
            $mailstatus = SendMailByMySMTPmailTemplate($email, $sub, $msg);
            if ($mailstatus) {
                sendApiResponse([
                    "status" => "success",
                    "message" => "OTP send success",
                    "email" => $email
                ], 200);
            } else {
                sendApiResponse([
                    "status" => "warning",
                    "message" => "OTP Not send.",
                    "token" => "",
                    "data" => []
                ], 400);
            }
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Profile Not configured properly.",
                "token" => "",
                "data" => []
            ], 400);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Invalid KAM Account",
            "token" => "",
            "sql" => $sql,
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