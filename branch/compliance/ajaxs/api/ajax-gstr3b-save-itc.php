<?php
header("content-type: application/json");
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-compliance-controller.php");
require_once("../../controller/gstr3b.controller.php");
require_once("../../controller/gstr3b-json-repositary-controller.php");

$response = [];
$jsonObj = "";
$gstr3bJsonRepoObj = "";

// Decode the 'action' parameter from base64 and then decode JSON into an associative array
$queryParams = json_decode(base64_decode($_GET['action']), true);
$jsonObj = $_GET['jsonString'];

$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();

if ($authObj["status"] == "success") {
    // // Pass decoded query parameters to the ComplianceGSTR3b and Gstr3bJsonRepository objects
    $complianceGSTR3bFileObj = new ComplianceGSTR3b($authObj, $queryParams['period']);
    // $gstr3bJsonRepoObj = new Gstr3bJsonRepository($queryParams['period'], date("Y-m-d", strtotime($queryParams['startDate'])), date("Y-m-d", strtotime($queryParams['endDate'])));
    // $jsonObj = $gstr3bJsonRepoObj->generate();
    
    // Call the method to save GSTR3B data and store the response
    $response = $complianceGSTR3bFileObj->saveGstr3bITCData($jsonObj);
} else {
    $response = $authObj;
}

// Encode the response in JSON and output it
echo json_encode($jsonObj);
