<?php
header("content-type: application/json");
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-compliance-controller.php");
require_once("../../controller/gstr1-file.controller.php");
require_once("../../controller/gstr1-json-repositary-controller.php");
$response = [];
$queryParams = json_decode(base64_decode($_GET['action']));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();

if ($authObj["status"] == "success") {

    $complianceGSTR1FileObj = new ComplianceGSTR1File($queryParams->period, $authObj);
    // return [
    //     "status"=> "warning",
    //     "message"=> "Checking from dev team!"
    // ];
    $gstr1JsonRepoObj = new Gstr1JsonRepository($queryParams->period, date("Y-m-d", strtotime($queryParams->startDate)), date("Y-m-d", strtotime($queryParams->endDate)));
    $jsonObj = $gstr1JsonRepoObj->generate();
    $response = $complianceGSTR1FileObj->saveGstr1Data(json_encode($jsonObj, true));
} else {
    $response = $authObj;
}
echo json_encode($response);