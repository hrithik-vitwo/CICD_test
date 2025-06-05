<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-compliance-controller.php");

$queryParams = json_decode(base64_decode(($_GET['action'])));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
$period = $queryParams->period;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $resultObj = queryGet("SELECT `apiData` FROM `erp_compliance_gstr2b` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `gstr2b_return_period`='$period'");
    // $resultObj['data']['apiData'] = ""; test purpose
    if ($resultObj['data']['apiData']) {
        $res = [
            "status" => "pulled",
            "msg" => "Already data pulled!",

        ];
        echo json_encode($res);
    } else {
        if ($authObj['status'] != "success") {

            $res = [
                "status" => "authFailed",
                "msg" => $authObj['message'],
            ];
            echo json_encode($res);
        }else{
            $res = [
                "status" => "active",
                "msg" => "Already data not pulled!",
    
            ];
            echo json_encode($res);

        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($authObj['status'] == 'warning') {
        // if auth failed then return connect view
        require_once("./components/auth-connect_gstr2b.php");
    } else {
        // console($queryParams);
        $dbObj = new Database();
        $lastActivityObj = $dbObj->queryGet("SELECT `apiData` FROM `erp_compliance_gstr2b` WHERE `company_id`=$company_id AND `branch_id`=$branch_id AND `gstr2b_return_period`='$period'");

        $lastReturnFileStatus = $lastActivityObj["data"]["status"] ?? "active";

        switch ($lastReturnFileStatus) {
            case "active":
                require_once("./components/gstr2b-pullData.php");
                break;
            case "pulled":
                // require_once("./components/gstr1-reset-procced.php");
                break;
        }
    }
}
