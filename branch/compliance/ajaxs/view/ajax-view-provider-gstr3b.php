<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-compliance-controller.php");

$queryParams = json_decode(base64_decode(($_GET['action'])));
$authGstinPortalObj = new AuthGstinPortal();
$authObj = $authGstinPortalObj->checkAuth();
$period = $queryParams->period;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if ($authObj['status'] == 'warning') {
        // if auth failed then return connect view
        require_once("./components/auth-connect_gstr3b.php");
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($authObj['status'] == 'warning') {
        // if auth failed then return connect view
        $res = [
            "status" => "authFailed",
            "msg" => $authObj['message'],
        ];
        echo json_encode($res);
    } else {
        $res = [
            "status" => "success",
            "msg" => "success",
        ];
        echo json_encode($res);
    }
}
