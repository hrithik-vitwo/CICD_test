<?php
require_once("../../../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');



function mapVendorItem($INPUTS){
  $returnData = [];
  
  global $company_id;
  global $branch_id;
  global $location_id;
  global $created_by;
  global $updated_by;

  if (!isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]) || $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] == "") {
    return [
      "status" => "error",
      "message" => "Please do login before continuing.",
      "vendorId" => ""
    ];
  }

  $itemId = $INPUTS["itemId"] ?? "";
  $itemCode = $INPUTS["itemCode"] ?? "";
  $itemHSN = $INPUTS["itemHSN"] ?? "";
  $itemTitle = filter_var($INPUTS["itemTitle"], FILTER_SANITIZE_STRING) ?? "";
  $itemType = $INPUTS["itemType"] ?? "";
  $vendorId = $INPUTS["vendorId"] ?? 0;
  $vendorCode = $INPUTS["vendorCode"] ?? "";
  $task = $INPUTS["taskType"];
  $uom = $INPUTS["itemUOM"] ?? "";

  //$vendorCode = $INPUTS[""];

  if($itemCode!="" || $itemTitle!="" || $vendorCode!="" || $itemType!=""){
    $mapSql = 'INSERT INTO `'.ERP_VENDOR_ITEM_MAP.'` SET `companyId`='.$company_id.',`branchId`='.$branch_id.',`locationId`='.$company_id.',`vendorId`='.$vendorId.', `vendorCode`="'.$vendorCode.'", `itemTitle`="'.$itemTitle.'",`itemType`="'.$itemType.'",`itemCode`="'.$itemCode.'",`itemId`="'.$itemId.'", `vendorItemMapCreatedBy`="'.$created_by.'",`vendorItemMapUpdatedBy`="'.$created_by.'"';
    $mapItemCodeObj = queryInsert($mapSql);
    
    if($mapItemCodeObj["status"] == "success"){

      if($task == "change")
      {
        $returnData = [
          "status" => "success",
          "message" => "Item Change successfully done",
          "data" => [
            "itemId" => $itemId,
            "itemCode" => $itemCode,
            "itemHSN" => $itemHSN,
            "itemType" => $itemType,
            "vendorId" => $vendorId,
            "vendorCode" => $vendorCode,
            "itemUom" => $uom
          ]
        ];
      }
      else
      {
      $returnData = [
        "status" => "success",
        "message" => "Item map successfully created",
        "data" => [
          "itemId" => $itemId,
          "itemCode" => $itemCode,
          "itemHSN" => $itemHSN,
          "itemType" => $itemType,
          "vendorId" => $vendorId,
          "vendorCode" => $vendorCode,
          "itemUom" => $uom
        ]
      ];
    }
    }else{
      $returnData = [
        "status" => "warning",
        "message" => "Mapping failed, please try again!!",
        "data" => [],
      	"sql" => $mapSql
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
