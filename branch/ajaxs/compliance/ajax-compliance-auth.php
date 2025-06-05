<?php
include_once("../../../app/v1/connection-branch-admin.php");
include_once("../../../app/v1/functions/branch/func-compliance-controller.php");

$responseData = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authGstinPortalObj = new AuthGstinPortal();

    if ($_POST["authType"] == "authCheck") {
        $responseData = $authGstinPortalObj->checkAuth();
        echo json_encode($responseData, true);
    } else if ($_POST["authType"] == "sendOtp") {

        $otpSentObj = $authGstinPortalObj->sendOtp();

        echo json_encode($otpSentObj, true);

        // $newAuthDetailSql = 'INSERT INTO `erp_compliance_auth` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`gstinStateCode`="' . $gstinStateCode . '",`gstinUsername`="' . $gstinUsername . '",`ipAddress`="' . $ipAddress . '",`authTnxId`="' . $authTnxId . '",`createdBy`="' . $created_by . '"';

        // $newAuthDetailsObj = queryInsert($newAuthDetailSql);

        // if ($newAuthDetailsObj["status"] == "success") {
        //     $responseData = [
        //         "status" => "success",
        //         "message" => "Authentication successful",
        //         "data" => [
        //             "ipAddress" => $ipAddress,
        //             "gstinStateCode" => 18,
        //             "gstinUsername" => "gvbnjk",
        //             "authTnxId" => ""
        //         ],
        //     ];
        // } else {
        //     $responseData = [
        //         "status" => "warning",
        //         "message" => "Authentication failed",
        //         "data" => []
        //     ];
        // }

        // echo json_encode([
        //     "status" => "success",
        //     "message" => "Otp sent failed",
        //     "data" => []
        // ], true);

    } else if($_POST["authType"] == "verifyOtp"){

        $authOtp = $_POST["authOtp"];
        $verifyOtpObj = $authGstinPortalObj->verifyOtp($authOtp);
        echo json_encode($verifyOtpObj, true);


        // $newAuthDetailSql = 'INSERT INTO `erp_compliance_auth` SET `company_id`=' . $company_id . ',`branch_id`=' . $branch_id . ',`gstinStateCode`="' . $gstinStateCode . '",`gstinUsername`="' . $gstinUsername . '",`ipAddress`="' . $ipAddress . '",`authTnxId`="' . $authTnxId . '",`createdBy`="' . $created_by . '"';

        // $newAuthDetailsObj = queryInsert($newAuthDetailSql);

        // if ($newAuthDetailsObj["status"] == "success") {
        //     $responseData = [
        //         "status" => "success",
        //         "message" => "Authentication successful",
        //         "data" => [
        //             "ipAddress" => $ipAddress,
        //             "gstinStateCode" => 18,
        //             "gstinUsername" => "gvbnjk",
        //             "authTnxId" => ""
        //         ],
        //     ];
        // } else {
        //     $responseData = [
        //         "status" => "warning",
        //         "message" => "Authentication failed",
        //         "data" => []
        //     ];
        // }

        // echo json_encode([
        //     "status" => "success",
        //     "message" => "Verified successfully",
        //     "data" => $_POST
        // ], true);
        // echo json_encode([
        //     "status" => "success",
        //     "message" => "Verified failure",
        //     "data" => $_POST
        // ], true);
    }
}else{
    $responseData = [
        "status" => "warning",
        "message" => "Method not allowed",
        "data" => []
    ];
    echo json_encode($responseData, true);
}
