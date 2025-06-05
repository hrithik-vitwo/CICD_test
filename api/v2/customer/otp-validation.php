<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'] ?? 0;
    $company_id = $authCustomer['company_id'] ?? 0;
    $branch_id = $authCustomer['branch_id'] ?? 0;
    $location_id = $authCustomer['location_id'] ?? 0;
    $otp = $_POST['otp']; 

        $getOtp = queryGet("SELECT * FROM ".ERP_CUSTOMER." WHERE `customer_id` = $customer_id AND `mailValidationOtp` = $otp");
      
        if($getOtp['status'] == "success"){
            $query = queryUpdate("UPDATE ".ERP_CUSTOMER." SET `mailOTPvalidationStatus` = 'yes' WHERE `customer_id` = $customer_id AND `company_id` = $company_id");
                    if($query['status'] == "success"){
                        sendApiResponse([
                            "status" => "success",
                            "message" => "OTP verified successfully",
                           
                        ], 200);
                    }
                    else{
                        sendApiResponse([
                            "status" => "error",
                            "message" => "Something went wrong",
                           
                        ], 400);
                    }
        }
        else{
            sendApiResponse([
                "status" => "error",
                "message" => "Invalid OTP",
               
            ], 400);
        }



 } else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
       
    ], 405);
}