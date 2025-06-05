<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "SELECT `fldAdminKey`, `user_type`, `fldAdminName`, `fldAdminUserName`, `fldAdminEmail`, `fldAdminPhone`, `flAdminDesignation`, `fldAdminPassword`, `fldAdminAvatar`, `fldAdminCreatedAt`, `fldAdminUpdatedAt`, `fldAdminStatus`, `fldAdminNotes`,`fcm_token` FROM `erp_bug_user_details` WHERE `fldAdminStatus`='active' AND `fldAdminEmail`='".$_POST["fldAdminEmail"]."'";

    $userObj = queryGet($sql);
    if ($userObj["status"] == "success") {
        $user = $userObj["data"];

        if ($_POST["pass"] == $user["fldAdminPassword"]) {

            //Insert FCM Token
            $fcm_code = $_POST["fcm"];
            if ($fcm_code != "") {
                $sql_fcm = queryUpdate("UPDATE `erp_bug_user_details` SET `fcm_token` = '" . $fcm_code . "' WHERE `fldAdminKey`='" . $userObj["data"]["fldAdminKey"] . "'");
            }

            $user_id = $user["fldAdminKey"];
            $user_type = $user["user_type"];
            $user_name = $user["fldAdminName"];
            $user_profile = BASE_URL . 'public/storage/avatar/' . $user["fldAdminAvatar"];

            $jwtObj = new JwtToken();
            $jwtToken = $jwtObj->createToken([
                "user_id" => $user_id,
                "user_type" => $user_type
            ]);

            sendApiResponse([
                "status" => "success",
                "message" => "Logged in success",
                "token" => $jwtToken,
                "data" => [
                    "user_id" => $user_id,
                    "user_type" => $user_type,
                    "user_profile" => $user_profile,
                    "user_name" => $user_name
                ]
            ], 200);
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Password incorrect",
                "token" => "",
                "data" => []
            ], 400);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Invalid customer",
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
