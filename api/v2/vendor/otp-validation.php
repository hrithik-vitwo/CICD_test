<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //  echo 1;
    $authVendor = authVendorApiRequest(); 
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];
    $otp = $_POST['otp']; 

        $getOtp = queryGet("SELECT * FROM ".ERP_VENDOR_DETAILS." WHERE `vendor_id` = $vendor_id AND `mailValidationOtp` = $otp");
      
      
        if($getOtp['status'] == "success"){
            $query = queryUpdate("UPDATE ".ERP_VENDOR_DETAILS." SET `mailOTPvalidationStatus` = 'yes' WHERE `vendor_id` = $vendor_id AND `company_id` = $company_id");
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