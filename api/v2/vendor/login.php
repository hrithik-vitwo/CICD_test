<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_code = $_POST['company_code'];
    $company_sql = queryGet("SELECT * FROM `" . ERP_COMPANIES . "` WHERE `company_code`=$company_code");

    if ($company_sql["status"] == "success") {
        $company_id = $company_sql["data"]["company_id"];
        $companyCountry = $company_sql["data"]["company_country"];
        
        $countrySql = "SELECT `code`, `name` FROM erp_countries WHERE id=$companyCountry";
        $fetchCountryDetails = queryGet($countrySql);
        $countryDetails = $fetchCountryDetails['data'];
        $sql = "SELECT `tbl_vendor_admin_details`.`fldAdminPassword`,  `tbl_vendor_admin_details`.`fldAdminName` ," . ERP_VENDOR_DETAILS . ".`vendor_id`, " . ERP_VENDOR_DETAILS . ".`vendor_code`," . ERP_VENDOR_DETAILS . ".`trade_name`, " . ERP_VENDOR_DETAILS . ".`vendor_gstin`," . ERP_VENDOR_DETAILS . ".`vendor_pan`, " . ERP_VENDOR_DETAILS . ".`vendor_picture`,  " . ERP_VENDOR_DETAILS . ".`vendor_authorised_person_email`, " . ERP_VENDOR_DETAILS . ".`mailOTPvalidationStatus` FROM `tbl_vendor_admin_details`," . ERP_VENDOR_DETAILS . " WHERE " . ERP_VENDOR_DETAILS . ".`vendor_id`= `tbl_vendor_admin_details`.`fldAdminVendorId` AND " . ERP_VENDOR_DETAILS . ".`vendor_code`='" . $_POST["user_code"] . "' AND " . ERP_VENDOR_DETAILS . ".`company_id`=$company_id AND " . ERP_VENDOR_DETAILS . ".`vendor_status`='active'  AND `fldAdminStatus`='active'";

        $vendorObj = queryGet($sql);
      //  console($vendorObj);
        if ($vendorObj["status"] == "success") {
           
            if ($_POST["pass"] == $vendorObj["data"]["fldAdminPassword"]) {

                //Insert FCM Token
                $fcm_code = $_POST["fcm"];
                if($fcm_code != "") {
                $sql_fcm = queryUpdate("UPDATE `tbl_vendor_admin_details` SET `fcm_token` = '".$fcm_code."' WHERE `fldAdminVendorId`='".$vendorObj["data"]["vendor_id"]."'");
                }

                $vendor_id = $vendorObj["data"]["vendor_id"];
                $vendor_code = $vendorObj["data"]["vendor_code"];
                $trade_name = $vendorObj["data"]["trade_name"];
                $vendor_gstin = $vendorObj["data"]["vendor_gstin"];
                $vendor_pan = $vendorObj["data"]["vendor_pan"];
                $vendor_name = $vendorObj["data"]["fldAdminName"];
                $vendor_profile = BASE_URL.'public/storage/picture/'.$vendorObj["data"]["vendor_picture"];
                $mailOTPvalidationStatus = $vendorObj["data"]["mailOTPvalidationStatus"];
                $mailId = $vendorObj["data"]["vendor_authorised_person_email"];

                $jwtObj = new JwtToken();
                $jwtToken = $jwtObj->createToken([
                    "vendor_id" => $vendor_id,
                    "vendor_code" => $vendor_code
                ]);

                sendApiResponse([
                    "status" => "success",
                    "message" => "Logged in success",
                    "token" => $jwtToken,
                    "data" => [
                        "company_code" => $company_code,
                        "user_code" => $vendor_code,
                        "trade_name" => $trade_name,
                        "vendor_gstin" => $vendor_gstin,
                        "vendor_pan" => $vendor_pan,
                        "vendor_profile" => $vendor_profile,
                        "vendor_poc_name" => $vendor_name,
                        "countryDetails" => $countryDetails,
                        "mailOTPvalidationStatus" => $mailOTPvalidationStatus,
                        "mailId" => $mailId,
                        "role"=>"vendor"
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

    // echo $jwtToken."<br>";
    // echo"<pre>";
    // print_r($jwtObj->verifyToken($jwtToken));

} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => []
    ], 405);
}