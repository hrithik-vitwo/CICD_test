<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-vendors-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

$VendorObj = new VendorController();
if ($_GET['act'] === "vendorprev") {
    $vendorId = $_GET['vendorId'];
    $getVendorObj = $VendorObj->getDataVendorDetails($vendorId);
    $data = $getVendorObj['data'][0];
   // console($data);
    $vendor_bussiness =queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id` = '".$data['vendor_id']."' AND `vendor_business_primary_flag` = 1");
    //console($vendor_bussiness); 
    $prev_po = queryGet("SELECT * FROM `erp_branch_purchase_order` WHERE `vendor_id` = $vendorId ORDER BY `po_id` DESC");
   // console($prev_po);

    $headerData['country'] = $vendor_bussiness['data']['vendor_business_country'];
    $headerData['ref'] = $prev_po['data']['ref_no'];
    $headerData['func'] = $prev_po['data']['functional_area'];



    echo json_encode($headerData);


} else {
    echo "Something wrong, try again!";
}
?>