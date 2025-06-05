<?php
include_once("../../../../app/v1/connection-branch-admin.php");


$headerData = array('Content-Type: application/json');
$responseData = [];



if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

    $sl = $_POST['sl'];
    $check = queryGet("SELECT * FROM `erp_discount_coupon` WHERE `discount_coupon_serial` = '".$sl."' AND `location_id` = $location_id");

  //  console($check);

      echo $check['numRows'];





}








?>