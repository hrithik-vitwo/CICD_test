<?php
require_once("api-common-func.php");
// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $isValidate = validate($_POST, [
        "company_id" => "required",
        "user_id" => "required"

    ]);
    if ($isValidate["status"] != "success") {
        sendApiResponse([
            "status" => "error",
            "message" => "Invalid inputs"

        ], 405);
    }

    $company_id = $_POST['company_id'];
    $user_id = $_POST['user_id'];
    $created_by = $_POST['user_id']."|location";

    $otp = rand(110001, 999999);

    $company_query = queryGet('SELECT * FROM `tbl_company_admin_details` WHERE `fldAdminCompanyId`=' . $company_id .' ORDER BY `fldAdminKey` ASC LIMIT 1');

    if($company_query["numRows"] == 0)
    {
        sendApiResponse([
            "status" => "error",
            "message" => "Company Email not exists"
        ], 405);
    }
    else
    {
        $ins_query = "INSERT INTO `erp_migration_otp` SET `otp`='".$otp."', `company_id`=$company_id, `created_by`='".$created_by."', `updated_by`='".$created_by."', `user_id`=$user_id ";
        $data = queryInsert($ins_query);

        $company_email = $company_query["data"]["fldAdminEmail"];

        $sub = "Migration Resend OTP Mail";
        $msg = "OTP: ".$otp;
        SendMailByMySMTPmailTemplate($company_email,$sub,$msg);

        sendApiResponse([
            "status" => "success",
            "message" => "OTP Sent Successfuly"
    
        ], 200);
    }

    
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"

    ], 405);
}
