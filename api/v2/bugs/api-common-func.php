<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization,  X-Requested-With");
//header("Access-Control-Allow-Headers: Authorization");
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/lib/jwt/autoload.php");


function requestBody(){
    return json_decode(file_get_contents("php://input"), true);
}
function requestPost(){
    return $_POST;
}
function requestGet(){
    return $_GET;
}
function requestFiles(){
    return $_FILES;
}

function sendApiResponse($responseArray = [], $statusCode = 200){
    http_response_code($statusCode);
    echo json_encode($responseArray, true);
    exit();
}

function authCustomerApiRequest(){
    $bearerToken = apache_request_headers()["authorization"] ?? (apache_request_headers()["Authorization"] ?? "");
    if($bearerToken != ""){
        $tokenType=substr($bearerToken, 0,6);
        if($tokenType == "Bearer"){
            $token = substr($bearerToken,7);

            $jwtObj = new JwtToken();
            $jwtVerifyObj=$jwtObj->verifyToken($token);
            if($jwtVerifyObj["status"]=="success"){
                $jwtData = $jwtVerifyObj["data"];
                $fldAdminKey = $jwtData["user_id"] ?? "";

                $userDetailsObj = queryGet('SELECT `fldAdminKey`, `user_type`, `fldAdminName`, `fldAdminUserName`, `fldAdminEmail`, `fldAdminPhone`, `flAdminDesignation`, `fldAdminPassword`, `fldAdminAvatar`, `fldAdminCreatedAt`, `fldAdminUpdatedAt`, `fldAdminStatus`, `fldAdminNotes`,`fcm_token` FROM `erp_bug_user_details` WHERE `fldAdminKey`='.$fldAdminKey);
                if($userDetailsObj["status"]=="success"){
                    if($userDetailsObj["data"]["fldAdminStatus"]=="active"){
                        return $userDetailsObj["data"];
                    }else{
                        sendApiResponse([
                            "status" => "warning",
                            "message" => "Your account is inactive, contact us as soon as possible!",
                            "data" => []
                        ],401);
                    }
                }else{
                    sendApiResponse([
                        "status" => "warning",
                        "message" => "Authorization failed",
                        "sql" => $userDetailsObj,
                        "data" => []
                    ],401);
                }
            }else{
                sendApiResponse([
                    "status" => "warning",
                    "message" => "Invalid authorization token1",
                    "data" => []
                ],401);
            }
        }else{
            sendApiResponse([
                "status" => "warning",
                "message" => "Invalid authorization token2",
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