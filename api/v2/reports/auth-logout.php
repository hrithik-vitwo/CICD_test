<?php
require_once("api.php");


if(requestMethod()==="POST"){
    $authorizationToken = requestHeaders("Authorization");
    if($authorizationToken!==""){
        sendApiResponse([
            "status" => "success",
            "message" => "Logged out successfully",
            "redirectUrl" => "https://www.devalpha.vitwo.ai/branch/location/"
        ], 200);   
    }else{
        sendApiResponse([
            "status" => "failed",
            "message" => "Authorization token is required"
        ], 401);
    }
}else{
    sendApiResponse([
        "status" => "failed",
        "message" => "Request method not allowed"
    ], 405);
}
?>