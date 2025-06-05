<?php
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-compliance-controller.php");

$authGstinPortalObj = new AuthGstinPortal();
if(isset($_REQUEST["act"]) && $_REQUEST["act"]=="sendOtp"){
    $sendOtpObj=$authGstinPortalObj->sendOtp();
    echo json_encode($sendOtpObj, true);

}elseif(isset($_REQUEST["act"]) && $_REQUEST["act"]=="verifyOtp"){
    $authOtp=$_REQUEST["authOtp"] ?? "";
    $verifyOtpObj=$authGstinPortalObj->verifyOtp($authOtp);
    echo json_encode($verifyOtpObj, true);
}else{
    http_response_code(401);
    echo json_encode([
        "status" => "warning",
        "message" => "Invalid request"
    ],true);
}
?>