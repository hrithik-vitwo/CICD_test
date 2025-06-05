<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization,  X-Requested-With");
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/lib/jwt/autoload.php");
require_once("../../../app/v1/lib/validator/autoload.php");


function requestBody($name = "")
{
    $bodyData = json_decode(file_get_contents("php://input"), true);
    if (!empty($name)) {
        return $bodyData[$name] ?? "";
    } else {
        return $bodyData;
    }
}

function requestPost($name = "", $default = null)
{
    if (!empty($name)) {
        return $_POST[$name] ?? ($default ? $default : null);
    } else {
        return $_POST;
    }
}
function requestGet($name = "", $default = null)
{
    if (!empty($name)) {
        return $_GET[$name] ?? ($default ? $default : null);
    } else {
        return $_GET;
    }
}
function requestFiles()
{
    return $_FILES;
}


function requestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}


function requestHeaders($headerName = '')
{
    if (!empty($headerName)) {
        return apache_request_headers()[$headerName] ?? "";
    } else {
        return apache_request_headers();
    }
}

function sendApiResponse($responseAny = null, $statusCode = 200)
{
    http_response_code($statusCode);
    if (gettype($responseAny) === 'array') {
        echo json_encode($responseAny, true);
    } else {
        echo $responseAny;
    }
    exit();
}

function authUser()
{
    $authBearerToken = requestHeaders("Authorization");
    $jwtToken = explode(" ", $authBearerToken)[1];
    if (!empty($jwtToken)) {
        $jwtObj = new JwtToken();
        $jwtTokenVerifyObj = $jwtObj->verifyToken($jwtToken);
        if ($jwtTokenVerifyObj["status"] === "success") {
            if (count($jwtTokenVerifyObj["data"]["data"] ?? []) > 0) {
                return $jwtTokenVerifyObj["data"]["data"];
            } else {
                sendApiResponse([
                    "status" => "warning",
                    "message" => "Invalid auth user, please do login first!"
                ], 401);
            }
        } else {
            sendApiResponse([
                "status" => "warning",
                "message" => "Invalid auth token provided, try again!"
            ], 401);
        }
    } else {
        sendApiResponse([
            "status" => "warning",
            "message" => "Unauthorized, please do login first!"
        ], 401);
    }
}
