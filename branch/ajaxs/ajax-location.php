<?php
include_once("../../app/v1/connection-branch-admin.php");
include("../../app/v1/functions/branch/func-username.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
if ($_GET['act'] === "location") {
    $branch_id = $_GET['b_id'];
    $branch_sql = queryGet("SELECT * FROM `erp_branches` WHERE `branch_id`=$branch_id");
   // console($branch_sql);
    $flat_no = $branch_sql['data']['flat_no'];
    $build_no = $branch_sql['data']['build_no'];
    $street_name = $branch_sql['data']['street_name'];
    $pincode = $branch_sql['data']['pincode'];
    $location = $branch_sql['data']['location'];
    $city = $branch_sql['data']['city'];
    $district = $branch_sql['data']['district'];
    $state = $branch_sql['data']['state'];

$responseData['flat_no']=$flat_no;
$responseData['build_no']=$build_no;
$responseData['street_name']=$street_name;


$responseData['pincode']=$pincode;
$responseData['location']=$location;
$responseData['city']=$city;

$responseData['district']=$district;
$responseData['state']=$state;

echo json_encode($responseData);

}
else if($_GET['act'] === "user_id"){

 $email = $_GET['email'];
 
 $ar=explode("@",$email);

 //console($ar[0]);
 //echo check_userName($ar[0]);

   $responseData = check_userName($ar[0]);



}
else{

}

?>