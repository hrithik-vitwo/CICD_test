<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $isValidate = validate($_POST, [
        "company_id" => "required",
        "user_id" => "required",
        "otp" => "required"

    ]);
    if ($isValidate["status"] != "success") {
        sendApiResponse([
            "status" => "error",
            "message" => "Invalid inputs"

        ], 405);
    }

    $company_id = $_POST['company_id'];
    $user_id = $_POST['user_id'];

    $otp = $_POST['otp'];

    $check_query = queryGet('SELECT * FROM `erp_migration_otp` WHERE `company_id`=' . $company_id . ' AND `user_id`=' . $user_id . ' AND `otp`=' . $otp . ' ORDER BY `id` DESC');
    unset($check_query["sql"]);

    if ($check_query["numRows"] == 0) {

        sendApiResponse([
            "status" => "error",
            "message" => "Give proper OTP"

        ], 405);

    }
    else
    {
        $date_data = $check_query["data"]["created_at"];

        $dateTime = new DateTime($date_data);
        $dateTime->modify('+5 minutes');
        $modifiedDate = $dateTime->format('Y-m-d H:i:s');

        $now = date("Y-m-d H:i:s");

        if($now > $modifiedDate)
        {
            sendApiResponse([
                "status" => "error",
                "message" => "OTP Expired"
        
            ], 405);
        }
        else
        {
            sendApiResponse([
                "status" => "success",
                "message" => "OTP Verified"
        
            ], 200);
        }

    }
    
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"

    ], 405);
}
