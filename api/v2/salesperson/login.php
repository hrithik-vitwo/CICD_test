<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $requestPost = requestBody();
    $kamCode = $requestPost['kamCode'];
    $user_id = $requestPost["user_id"];
    $otp = $requestPost["otp"];
    $fcm_code = $requestPost["fcm"];
  
    $sql = "SELECT * FROM `erp_kam` WHERE `status`='active' AND `kamCode`='" . $kamCode . "' AND `otp`='" . $otp . "' LIMIT 1";

    $userObj = queryGet($sql);
    if ($userObj["status"] == "success") {
        $user = $userObj["data"];

        if (!empty($fcm_code)) {

            //Insert FCM Token
            
            if ($fcm_code != "") {
                $sql_fcm = queryUpdate("UPDATE `erp_kam` SET `fcm_token` = '" . $fcm_code . "' WHERE `kamId`='" . $userObj["data"]["kamId"] . "'");
            }

            $kamId = $user["kamId"];
            $kamCode = $user["kamCode"];
            $company_id = $user["company_id"];
            $branch_id = $user["branch_id"];
            $location_id = $user["location_id"];
            $kamName = $user["kamName"];
            $jwtObj = new JwtToken();
            $jwtToken = $jwtObj->createToken([
                "kamId" => $kamId,
                "kamCode" => $kamCode,
                "company_id" => $company_id,
                "branch_id" => $branch_id,
                "location_id" => $location_id,
                "kamName" => $kamName,
                "user_id" => $user_id
            ]);

            sendApiResponse([
                "status" => "success",
                "message" => "Logged in success",
                "token" => $jwtToken,
                "data" => [
                    "kamId" => $kamId,
                    "kamCode" => $kamCode,
                    "company_id" => $company_id,
                    "branch_id" => $branch_id,
                    "location_id" => $location_id,
                    "kamName" => $kamName,
                    "user_id" => $user_id
                ]
            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Fcm code Missing",
                "token" => "",
                "data" => []
            ], 400);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "OTP Missmatch",
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
