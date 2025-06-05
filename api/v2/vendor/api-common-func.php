<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization,  X-Requested-With");
//header("Access-Control-Allow-Headers: Authorization");
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/lib/jwt/autoload.php");

function sendApiResponse($responseArray = [], $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($responseArray, true);
    exit();
}

function authVendorApiRequest()
{
    $bearerToken = apache_request_headers()["authorization"] ?? (apache_request_headers()["Authorization"] ?? "");
    if ($bearerToken != "") {
        $tokenType = substr($bearerToken, 0, 6);
        if ($tokenType == "Bearer") {
            $token = substr($bearerToken, 7);

            $jwtObj = new JwtToken();
            $jwtVerifyObj = $jwtObj->verifyToken($token);
            if ($jwtVerifyObj["status"] == "success") {
                $jwtData = $jwtVerifyObj["data"];
                $vendorId = $jwtData["vendor_id"] ?? "";
                $vendorCode = $jwtData["vendor_code"] ?? "";

                $vendorDetailsObj = queryGet('SELECT `vendor_id`, `company_id`, `company_branch_id` as branch_id, `location_id`, `parentGlId`, `vendor_code`, `vendor_pan`, `vendor_gstin`, `trade_name`, `constitution_of_business`, `vendor_opening_balance`, `vendor_currency`, `vendor_visible_to_all`, `vendor_credit_period`, `vendor_authorised_person_phone` as phone, `vendor_authorised_person_email` as email, `vendor_status` FROM `erp_vendor_details` WHERE `vendor_id`=' . $vendorId);
                if ($vendorDetailsObj["status"] == "success") {
                    if ($vendorDetailsObj["data"]["vendor_status"] == "active") {
                        return $vendorDetailsObj["data"];
                    } else {
                        sendApiResponse([
                            "status" => "warning",
                            "message" => "Your account is inactive, contact us as soon as possible!",
                            "data" => []
                        ], 401);
                    }
                } else {
                    sendApiResponse([
                        "status" => "warning",
                        "message" => "Authorization failed",
                        "data" => []
                    ], 401);
                }
            } else {
                sendApiResponse([
                    "status" => "warning",
                    "message" => "Invalid authorization token1",
                    "data" => []
                ], 401);
            }
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Invalid authorization token2",
                "data" => []
            ], 401);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Missing authorization token",
            "data" => []
        ], 401);
    }
    exit();
}
