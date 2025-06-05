<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-vendors-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

$VendorObj = new VendorController();
if (isset($_GET['v_id']) && $_GET['v_id'] != '') {
    $vendorId = $_GET['v_id'];
    $getVendorObj = $VendorObj->getDataVendorDetails($vendorId);
    $data = $getVendorObj['data'][0];
    //console($data);
    echo json_encode($data);
}
?>