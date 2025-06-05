<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pan = $_POST['pan'];
    $pansql=queryGet("SELECT * FROM `erp_companies` WHERE `company_pan`='".$pan."' AND `company_id` =".$company_id."");
    $ispan=$pansql['numRows'];
    if($ispan>0){
        echo "same pan";
        exit();
    }

    
    $check = queryGet("SELECT * FROM `erp_vendor_details` WHERE `vendor_pan` = '".$pan."' AND `company_id`=$company_id");
    $count= $check['numRows'];
    if($count>0){
        echo "alredy exits";
        exit();
    }
    
    
//echo $check['numRows'];







}
?>