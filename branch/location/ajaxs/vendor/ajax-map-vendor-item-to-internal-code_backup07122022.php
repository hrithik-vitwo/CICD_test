<?php
require_once("../../../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');



function mapVendorItem($INPUTS){
  $returnData = [];

  $loginCompanyId = "";
  $loginBranchId = "";
  $loginAdminId = "";

  if (!isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]) || $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] == "") {
    return [
      "status" => "error",
      "message" => "Please do login before continuing.",
      "vendorId" => ""
    ];
  }

  $loginCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
  $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
  $loginAdminId = $_SESSION["logedBranchAdminInfo"]["adminId"];
  $loginAdminType = $_SESSION["logedBranchAdminInfo"]["adminType"];


  $itemCode = $INPUTS["itemCode"] ?? "";
  $itemHSN = $INPUTS["itemHSN"] ?? "";
  $itemTitle = strip_tags($INPUTS["itemTitle"]) ?? "";
  $itemType = $INPUTS["itemType"] ?? "";
  $vendorId = $INPUTS["vendorId"] ?? 0;
  $vendorCode = $INPUTS["vendorCode"] ?? "";

  //$vendorCode = $INPUTS[""];

  if($itemCode!="" || $itemTitle!="" || $vendorCode!="" || $itemType!=""){
    $mapItemCodeObj = queryInsert('INSERT INTO `'.ERP_VENDOR_ITEM_MAP.'` SET `companyId`="'.$loginCompanyId.'",`branchId`="'.$loginBranchId.'",`locationId`="'.$loginCompanyId.'",`vendorId`='.$vendorId.', `vendorCode`="'.$vendorCode.'", `itemTitle`="'.$itemTitle.'",`itemType`="'.$itemType.'",`itemCode`="'.$itemCode.'", `vendorItemMapCreatedBy`="'.$loginAdminId.'",`vendorItemMapUpdatedBy`="'.$loginAdminId.'"');
    
    if($mapItemCodeObj["status"] == "success"){
      $returnData = [
        "status" => "success",
        "message" => "Item map successfully created",
        "data" => [
          "itemCode" => $itemCode,
          "itemHSN" => $itemHSN,
          "itemType" => $itemType,
          "vendorId" => $vendorId,
          "vendorCode" => $vendorCode
        ]
      ];
    }else{
      $returnData = [
        "status" => "warning",
        "message" => "Mapping failed, please try again!!",
        "data" => []
      ];
    }
  }else{
    $returnData = [
      "status" => "warning",
      "message" => "Mapping failed, try again!",
      "data" => []
    ];
  }
  
  return $returnData;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //POST REQUEST
  $responseData = mapVendorItem($_POST);
}else{
  $responseData = [
    "status" => "error",
    "message" => "Invalid request method",
    "data" => []
  ];
}

echo json_encode($responseData);

?>