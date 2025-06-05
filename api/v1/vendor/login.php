<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_code = $_POST['company_code'];

    $company_sql = queryGet("SELECT * FROM `" . ERP_COMPANIES . "` WHERE `company_code`=$company_code");
    if ($company_sql["status"] == "success") {
        $company_id = $company_sql["data"]["company_id"];
        $sql = "SELECT `tbl_vendor_admin_details`.*," . ERP_VENDOR_DETAILS . ".vendor_code FROM `tbl_vendor_admin_details`," . ERP_VENDOR_DETAILS . " WHERE " . ERP_VENDOR_DETAILS . ".`vendor_id`= `tbl_vendor_admin_details`.`fldAdminVendorId` AND " . ERP_VENDOR_DETAILS . ".`vendor_code`='" . $_POST["vendor_code"] . "' AND " . ERP_VENDOR_DETAILS . ".`company_id`=$company_id  AND `fldAdminStatus`='active'";

        $vendorObj = queryGet($sql);
        if ($vendorObj["status"] == "success") {

            if ($_POST["pass"] == $vendorObj["data"]["fldAdminPassword"]) {
                $vendor_code = $vendorObj["data"]["vendor_code"];

                $jwtObj = new JwtToken();
                $jwtToken = $jwtObj->createToken([
                    "id" => $vendorObj["data"]["fldAdminKey"],
                    "vendor_code" => $vendorObj["data"]["vendor_code"]
                ]);

                sendApiResponse([
                    "status" => "success",
                    "message" => "Logged in success",
                    "token" => $jwtToken,
                    "data" => [
                        "vendorCode" => $vendor_code,
                        "company_code" => $company_code
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
                "message" => "Invalid vendor",
                "token" => "",
                "data" => []
            ], 400);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Invalid Company Code",
            "token" => "",
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
