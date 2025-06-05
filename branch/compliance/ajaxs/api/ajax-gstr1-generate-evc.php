<?php
header("content-type: application/json");
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-compliance-controller.php");
require_once("../../controller/gstr1-file.controller.php");
$response = [];
$queryParams = json_decode(base64_decode($_GET['action']));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();

if ($authObj["status"] == "success") {
    $pan = $_POST['pan']??"";
    if($pan !="" ){
        $complianceGSTR1FileObj = new ComplianceGSTR1File($queryParams->period, $authObj);
        $response = $complianceGSTR1FileObj->generateOtpForEvc($pan);
    }else{
        $response =[
            "status"=>"Warning",
            "message"=>"Pan not found!"
        ];
    }
}else
{
    $response = $authObj;
}
echo json_encode($response);
