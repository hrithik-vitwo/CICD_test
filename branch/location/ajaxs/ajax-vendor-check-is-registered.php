<?php
require_once("../../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  
  $companyId = (isset($_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]))?$_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"]:"";
  $companyBranchId = (isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]))?$_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]:"";

  //POST REQUEST
  $vendorName = (isset($_GET["vendorName"]))?$_GET["vendorName"]:"";
  $vendorGstin = (isset($_GET["vendorGstin"]))?$_GET["vendorGstin"]:"";
  if($vendorName!="" || $vendorGstin!=""){
    if($vendorGstin!=""){
      $selectSql = "SELECT * FROM `".ERP_VENDOR_DETAILS."` WHERE `vendor_gstin` = '".$vendorGstin."'";
    }else{
      $selectSql = "SELECT * FROM `".ERP_VENDOR_DETAILS."` WHERE `vendor_name` = '".$vendorName."'";
    }

    $selectSql.= " AND (`company_branch_id`='".$companyBranchId."' OR `company_id`='".$companyId."')";
    if($res = mysqli_query($dbCon, $createSql)){
      if(mysqli_num_rows($res)==1){
        $responseData["status"] = "success";
        $responseData["message"] = "Vendor is registered";
        $responseData["data"] = mysqli_fetch_assoc($res);
      }else{
        $responseData["status"] = "warning";
        $responseData["message"] = "Vendor is not registered";
      }

    }else{
        $responseData["status"] = "error";
        $responseData["message"] = "Something went wrong, try again!";
    }
  }else{
    $responseData["status"] = "error";
    $responseData["message"] = "Please enter a vendor name or vendor GSTIN!";
  }
}else{
  $responseData["status"] = "error";
  $responseData["message"] = "Something went wrong, try again!";
}
echo json_encode($responseData);
?>