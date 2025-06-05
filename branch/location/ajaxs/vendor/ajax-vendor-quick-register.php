<?php
require_once("../../../../app/v1/connection-branch-admin.php");

require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");

$headerData = array('Content-Type: application/json');



function quickRegVendor($INPUTS)
{

  global $company_id;
  global $branch_id;
  global $location_id;
  global $created_by;
  global $updated_by;

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
  $accMapp = getAllfetchAccountingMappingTbl($company_id);
  if ($accMapp["status"] == "success") {
    $paccdetails = getChartOfAccountsDataDetails($accMapp['data']['0']['vendor_gl']);
    $parentGlId = $paccdetails['data']['id'];
    $loginCompanyId = $_SESSION["logedBranchAdminInfo"]["fldAdminCompanyId"];
    $loginBranchId = $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"];
    $loginAdminId = $_SESSION["logedBranchAdminInfo"]["adminId"];
    $loginAdminType = $_SESSION["logedBranchAdminInfo"]["adminType"];

    $vendorName = $INPUTS["trade_name"] ?? "";
    $vendorGstin = $INPUTS["vendorGstin"] ?? "";
    $notifyConcernPerson = $INPUTS["notifyConcernPerson"] ?? "";
    $creditPeriod = $INPUTS["creditPeriod"] ?? "";
    $vendorPan = substr($vendorGstin, 2, 10);
    $pendingGrnId = $INPUTS["pendingGrnId"] ?? 0;
    $legalName = $INPUTS["legal_name"] ?? "";
    $constitution_of_business = $INPUTS["con_business"] ?? "";
    $build_no =  $INPUTS["build_no"] ?? "";
    $flat_no =  $INPUTS["flat_no"] ?? "";
    $street_name =  $INPUTS["street_name"] ?? "";
    $pincode =  $INPUTS["pincode"] ?? "";
    $location =  $INPUTS["location"] ?? "";
    $city =  $INPUTS["city"] ?? "";
    $district =  $INPUTS["district"] ?? "";
    // $state =  $INPUTS["state"] ?? "";
    
    $country =  $INPUTS["country"] ?? "";

    $state_value = str_split($INPUTS["state"]);
    $state_code =  $state_value[0];
    $state =  $state_value[1] ?? "";


    $lastlQuery = "SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id` = '" . $loginCompanyId . "' AND vendor_code!=''  ORDER BY `vendor_id` DESC LIMIT 1";
    $resultLast = queryGet($lastlQuery);
    $rowLast = $resultLast["data"];
    $lastsl = $rowLast['vendor_code'] ?? 0;

    $vendorCode = getVendorSerialNumber($lastsl);

    if ($vendorGstin != "") {
      $vendorCheckIfExistsObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $loginCompanyId . "' AND `company_branch_id`='" . $loginBranchId . "' AND `vendor_gstin`='" . $vendorGstin . "'", false);

      if ($vendorCheckIfExistsObj["numRows"] > 0) {
        $tradeName= $vendorCheckIfExistsObj["data"]["trade_name"] ??$INPUTS['trade_name'];
        queryUpdate('UPDATE `erp_grn_multiple` SET `vendor_id`=' . $vendorCheckIfExistsObj["data"]["vendor_id"] . ', `vendor_code`="' . $vendorCheckIfExistsObj["data"]["vendor_code"] . '",`trade_name`="' . $tradeName . '",`vendor_credit_period`="' . $vendorCheckIfExistsObj["data"]["vendor_credit_period"] . '" WHERE `grn_mul_id`=' . $pendingGrnId);
        return [
          "status" => "success",
          "message" => "Vendor already exists",
          "vendorId" => $vendorCheckIfExistsObj["data"]["vendor_id"],
          "vendorCode" => $vendorCheckIfExistsObj["data"]["vendor_code"],
          "creditPeriod" => $vendorCheckIfExistsObj["data"]["vendor_credit_period"],
          "vendorName" => $tradeName
        ];
        exit();
      }
    }

    $sql = "INSERT INTO `" . ERP_VENDOR_DETAILS . "` SET `company_id`='" . $loginCompanyId . "',`company_branch_id`='" . $loginBranchId . "', `location_id`='" . $location_id . "', `vendor_code`='" . $vendorCode . "', `trade_name`='" . $vendorName . "',`vendor_pan`='" . $vendorPan . "',`parentGlId`='" . $parentGlId . "', `vendor_gstin`='" . $vendorGstin . "', `vendor_credit_period`='" . $creditPeriod . "', `vendor_status`='guest', `legal_name`='".$legalName."', `constitution_of_business`='".$constitution_of_business."', `vendor_created_by`='" . $loginAdminId . "', `vendor_updated_by`='" . $loginAdminId . "'";

    $vendorResObj = queryInsert($sql);

    if ($vendorResObj["status"] == "success") {


      $vendorId = $vendorResObj["insertedId"];

      $ins = "INSERT INTO `" . ERP_VENDOR_BUSINESS_PLACES . "`
                                SET 
                                    `vendor_id`='$vendorId',
                                    `vendor_business_primary_flag`='1',
                                    `vendor_business_building_no`='$build_no',
                                    `vendor_business_flat_no`='$flat_no',
                                    `vendor_business_street_name`='$street_name',
                                    `vendor_business_pin_code`='$pincode',
                                    `vendor_business_location`='$location',
                                    `vendor_business_city`='$city',
                                    `vendor_business_district`='$district',
                                    `vendor_business_state`='$state',
                                    `state_code`='$state_code',
                                    `vendor_business_country` = '$country',
                                    `vendor_business_created_by`='$created_by',
                                    `vendor_business_updated_by`='$created_by' 
                                    ";

      // vendorQuickAddForm

      $vendorAddressObj = queryInsert($ins);

      queryUpdate('UPDATE `erp_grn_multiple` SET `vendor_id`=' . $vendorResObj["insertedId"] . ', `vendor_code`="' . $vendorCode . '",`vendor_name`="' . $vendorName . '",`vendor_credit_period`="' . $creditPeriod . '" WHERE `grn_mul_id`=' . $pendingGrnId);

    

      $returnData = [
        "status" => "success",
        "message" => "Vendor has been successfully created.",
        "vendorId" => $vendorResObj["insertedId"],
        "vendorCode" => $vendorCode,
        "creditPeriod" => $creditPeriod,
        "vendorName" => $vendorName
      ];
    } else {
      $returnData = [
        "status" => "warning",
        "message" => "Vendor created failed, please try again.",
        "vendorId" => "",
        "sql" => $sql
      ];
    }
  } else {
    $returnData = [
      "status" => "warning",
      "message" => "Acc Mapp pending, please Mapp first and try again.",
      "vendorId" => "",
      "sql" => ""
    ];
  }
  return $returnData;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //POST REQUEST
  $responseData = quickRegVendor($_POST);
} else {
  $responseData = [
    "status" => "error",
    "message" => "Invalid request method",
    "vendorId" => ""
  ];
}

echo json_encode($responseData);
