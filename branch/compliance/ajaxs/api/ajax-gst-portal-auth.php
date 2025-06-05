<?php
header("content-type: application/json");
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-compliance-controller.php");
$response = [];
// this ajax will receive action in query params with value either sendOtp or verifyOtp
$action = $_GET["action"] ?? "";
if (in_array($action, ["sendOtp", "verifyOtp"])) {
    //valid request
    $authGstinPortalObj = new AuthGstinPortal();
    if($action === "sendOtp"){
        //send otp
        $response = $authGstinPortalObj->sendOtp();
        // $response = [
        //     "status" => "success",
        //     "message" => "Invalid request!"
        // ];
    }elseif(isset($_POST["otp"]) && $_POST["otp"]!=""){
        //verify otp
        $response = $authGstinPortalObj->verifyOtp($_POST["otp"]);
    }else{
        $response = [
            "status" => "warning",
            "message" => "Invalid request!"
        ];
    }
} else {
    http_response_code(401);
    $response = [
        "status" => "warning",
        "message" => "Invalid request, try with valid one!"
    ];
}
echo json_encode($response, true);