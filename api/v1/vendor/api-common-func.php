<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/lib/jwt/autoload.php");


function sendApiResponse($responseArray = [], $statusCode = 200){
    http_response_code($statusCode);
    echo json_encode($responseArray, true);
    exit();
}

function authVendorApiRequest(){
    $bearerToken = apache_request_headers()["authorization"] ?? (apache_request_headers()["Authorization"] ?? "");
    if($bearerToken != ""){
        $tokenType=substr($bearerToken, 0,6);
        if($tokenType == "Bearer"){
            $token = substr($bearerToken,7);

            $jwtObj = new JwtToken();
            $jwtVerifyObj=$jwtObj->verifyToken($token);
            if($jwtVerifyObj["status"]=="success"){
                $jwtData = $jwtVerifyObj["data"];
                $vendorId = $jwtData["id"] ?? "";
                $vendorCode = $jwtData["vendor_code"] ?? "";

                $vendorDetailsObj = queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`='.$vendorId);
                if($vendorDetailsObj["status"]=="success"){
                    return $vendorDetailsObj["data"];
                }else{
                    sendApiResponse([
                        "status" => "warning",
                        "message" => "Authorization failed",
                        "data" => []
                    ],401);
                }
            }else{
                sendApiResponse([
                    "status" => "warning",
                    "message" => "Invalid authorization token",
                    "data" => []
                ],401);
            }
        }else{
            sendApiResponse([
                "status" => "warning",
                "message" => "Invalid authorization token",
                "data" => []
            ],401);
        }
    }else{
        sendApiResponse([
            "status" => "warning",
            "message" => "Missing authorization token",
            "data" => []
        ],401);
    }
    exit();
}