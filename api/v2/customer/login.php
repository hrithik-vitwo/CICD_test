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

        $variant_sql = queryGet("SELECT * FROM `erp_year_variant` WHERE `company_id`=$company_id ORDER BY `year_variant_id` DESC", true);
        $start = explode('-', $variant_sql['data'][0]['year_start']);
        $end = explode('-', $variant_sql['data'][0]['year_end']);
        $f_date = date('Y-m-01', strtotime("$start[0]-$start[1]"));
        $to_date = date('Y-m-t', strtotime("$end[0]-$end[1]"));

        $sql = "SELECT `tbl_customer_admin_details`.`fldAdminPassword`,  `tbl_customer_admin_details`.`fldAdminName` ," . ERP_CUSTOMER . ".`customer_id`, " . ERP_CUSTOMER . ".`customer_code`," . ERP_CUSTOMER . ".`trade_name`, " . ERP_CUSTOMER . ".`customer_gstin`," . ERP_CUSTOMER . ".`customer_pan`, " . ERP_CUSTOMER . ".`customer_picture`, " . ERP_CUSTOMER . ".`mailOTPvalidationStatus`, " . ERP_CUSTOMER . ".`customer_authorised_person_email`  FROM `tbl_customer_admin_details`," . ERP_CUSTOMER . " WHERE " . ERP_CUSTOMER . ".`customer_id`= `tbl_customer_admin_details`.`customer_id` AND " . ERP_CUSTOMER . ".`customer_code`='" . $_POST["user_code"] . "' AND " . ERP_CUSTOMER . ".`company_id`=$company_id AND " . ERP_CUSTOMER . ".`customer_status`='active' AND `fldAdminStatus`='active'";

        $customerObj = queryGet($sql);
        if ($customerObj["status"] == "success") {
            $customer = $customerObj["data"];

            if ($_POST["pass"] == $customer["fldAdminPassword"]) {

                //Insert FCM Token
                $fcm_code = $_POST["fcm"];
                if ($fcm_code != "") {
                    $sql_fcm = queryUpdate("UPDATE `tbl_customer_admin_details` SET `fcm_token` = '" . $fcm_code . "' WHERE `customer_id`='" . $customerObj["data"]["customer_id"] . "'");
                }

                $customer_id = $customer["customer_id"];
                $customer_code = $customer["customer_code"];
                $trade_name = $customer["trade_name"];
                $customer_gstin = $customer["customer_gstin"];
                $customer_pan = $customer["customer_pan"];
                $customer_name = $customer["fldAdminName"];
                $customer_profile = BASE_URL . 'public/storage/picture/' . $customer["customer_picture"];
                $mailOTPvalidationStatus = $customer["mailOTPvalidationStatus"];
                $mailId = $customer["customer_authorised_person_email"];

                $jwtObj = new JwtToken();
                $jwtToken = $jwtObj->createToken([
                    "customer_id" => $customer_id,
                    "customer_code" => $customer_code
                ]);

                sendApiResponse([
                    "status" => "success",
                    "message" => "Logged in success",
                    "token" => $jwtToken, 
                    "data" => [
                        "role" => "customer",
                        "company_code" => $company_code,
                        "customer_code" => $customer_code,
                        "trade_name" => $trade_name,
                        "customer_gstin" => $customer_gstin,
                        "customer_pan" => $customer_pan,
                        "customer_profile" => $customer_profile,
                        "customer_poc_name" => $customer_name,
                        "countryDetails" => $countryDetails,
                        "mailOTPvalidationStatus" => $mailOTPvalidationStatus,
                        "mailId" => $mailId,
                        "f_date" => $f_date,
                        "to_date" => $to_date
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
