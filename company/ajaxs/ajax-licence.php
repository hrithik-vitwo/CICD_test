<?php
require_once("../../app/v1/connection-company-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // console($_POST);
   $user_id = $_POST['user_id'];
   $licence = $_POST['licence'];
   $update_user = queryUpdate("UPDATE `tbl_branch_admin_details` SET `licence_id`=$licence WHERE `fldAdminKey`=$user_id");
  // console($update_user);
   $update_licence = queryUpdate("UPDATE `erp_company_licence` SET `user_id`=$user_id WHERE `licence_id`=$licence");
  // console($update_licence);
  // echo $licence;

}
else{

}



?>