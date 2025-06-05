<?php 
require_once("../../../app/v1/connection-branch-admin.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
$soObj = new BranchSo();


if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $response=$soObj->insertCollectFromVendor($_POST);
    echo json_encode($response);
}