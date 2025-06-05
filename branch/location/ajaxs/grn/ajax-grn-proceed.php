<?php
require_once("../../../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');



function proceedgrn($INPUTS)
{
  $returnData = [];

  global $company_id;
  global $branch_id;
  global $location_id;
  global $created_by;
  global $updated_by;

  if (!isset($_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"]) || $_SESSION["logedBranchAdminInfo"]["fldAdminBranchId"] == "") {
    return [
      "status" => "warning",
      "message" => "Please do login before continuing.",
      "vendorId" => ""
    ];
  }

  $branch_gst_no = $INPUTS["branch_gst"] ?? "";
  $inv_no = $INPUTS["inv_no"] ?? "";
  $po_no = $INPUTS["po_no"] ?? "";
  $gst_no = $INPUTS["gst_no"] ?? "";
  $vendor_name = $INPUTS["vendor_name"] != "" ? addslashes(base64_decode($INPUTS["vendor_name"])) : "";
  $CustomerName = $INPUTS["customername"] ?? 0;
  $grn_read_json = $INPUTS["grn_read_json"] != "" ? addslashes(base64_decode($INPUTS["grn_read_json"])) : "";
  $original_file_name = $INPUTS["original_file_name"];
  $uploaded_file_name = $INPUTS["uploaded_file_name"] ?? "";
  $total_amt = $INPUTS["total_amt"] ?? "";
  $vendorPan = $INPUTS["vendorpan"] ?? "";
  $CustomerTaxId = $INPUTS["concatenatedvalue"] ?? "";


  // $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $company_id . "' AND `vendor_pan` = '" . $vendorPan . "'");

  if (!empty($gst_no)) {
    $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $company_id . "' AND `vendor_gstin` = '" . $gst_no . "'");
  } else if (!empty($vendorPan)) {
      $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $company_id . "' AND `vendor_pan` = '" . $vendorPan . "'");
  } else {
      $vendorObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `company_id`='" . $company_id . "' AND (`trade_name`  LIKE '%" . $vendor_name . "%' OR `legal_name` = '%" . $vendor_name . "%')");
  }


  $vendorCode = "";
  $vendorId = "";
  $vendorCreditPeriod = "";
  $vendorSuggestionObj = [];
  if ($vendorObj["status"] == "success") {
    $vendorCode = $vendorObj["data"]["vendor_code"] ?? "";
    $vendorId = $vendorObj["data"]["vendor_id"] ?? "";
    $vendorCreditPeriod = $vendorObj["data"]["vendor_credit_period"] ?? "";
    $vendor_name = $vendorObj["data"]["trade_name"] ?? "";
  }


  if ($vendorId == "") {
    // Unregistered Vendor
    $status = "success";
    $remarks = "Invoice successfully processed.";

    $inserGrnObj = queryInsert("INSERT INTO `erp_grn_multiple` SET 
                    `company_id`='" . $company_id . "',
                    `branch_id`='" . $branch_id . "',
                    `location_id`='" . $location_id . "',
                    `inv_no`='" . $inv_no . "',
                    `po_no`='" . $po_no . "',
                    `vendor_id`='" . $vendorId . "',
                    `vendor_code`='" . $vendorCode . "',
                    `vendor_name`='" . $vendor_name . "',
                    `vendor_credit_period`='" . $vendorCreditPeriod . "',
                    `customer_gst`='" . $CustomerTaxId . "',
                    `gst_no`='" . $gst_no . "',
                    `grn_read_json`='" . $grn_read_json . "',
                    `original_file_name`='" . $original_file_name . "',
                    `uploaded_file_name`='" . $uploaded_file_name . "',
                    `branch_gst_no`='" . $branch_gst_no . "',
                    `total_amt`='" . $total_amt . "',
                    `created_by`='" . $created_by . "',
                    `updated_by`='" . $updated_by . "'");


    //Inster into Log

    $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
         `company_id`='" . $company_id . "',
         `branch_id`='" . $branch_id . "',
         `location_id`='" . $location_id . "',
         `status`='" . $status . "',
         `remarks`='" . $remarks . "',
         `vendor_gst`='" . $gst_no . "',
         `price`='" . $total_amt . "',
         `file_name`='" . $uploaded_file_name . "'");

    $returnData = [
      "status" => "success",
      "message" => "Invoice successfully processed.",
      "file" => $uploaded_file_name,
      "id" => $inserGrnObj["insertedId"]
    ];
  } else {

    $check = queryGet("SELECT * FROM `erp_grn_multiple` WHERE `company_id`='" . $company_id . "' AND `inv_no` = '" . $inv_no . "' AND `vendor_id`='" . $vendorId . "' AND `grn_active_status`='active'");

    if ($check["numRows"] == 0) {

      $status = "success";
      $remarks = "Invoice successfully processed.";

      $inserGrnObj = queryInsert("INSERT INTO `erp_grn_multiple` SET 
                `company_id`='" . $company_id . "',
                `branch_id`='" . $branch_id . "',
                `location_id`='" . $location_id . "',
                `inv_no`='" . $inv_no . "',
                `po_no`='" . $po_no . "',
                `vendor_id`='" . $vendorId . "',
                `vendor_code`='" . $vendorCode . "',
                `vendor_name`='" . $vendor_name . "',
                `vendor_credit_period`='" . $vendorCreditPeriod . "',
                `customer_gst`='" . $CustomerTaxId . "',
                `gst_no`='" . $gst_no . "',
                `grn_read_json`='" . $grn_read_json . "',
                `original_file_name`='" . $original_file_name . "',
                `uploaded_file_name`='" . $uploaded_file_name . "',
                `branch_gst_no`='" . $branch_gst_no . "',
                `total_amt`='" . $total_amt . "',
                `created_by`='" . $created_by . "',
                `updated_by`='" . $updated_by . "'");


      //Inster into Log

      $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
     `company_id`='" . $company_id . "',
     `branch_id`='" . $branch_id . "',
     `location_id`='" . $location_id . "',
     `status`='" . $status . "',
     `remarks`='" . $remarks . "',
     `vendor_gst`='" . $gst_no . "',
     `price`='" . $total_amt . "',
     `file_name`='" . $uploaded_file_name . "'");

      $returnData = [
        "status" => "success",
        "message" => "Invoice successfully processed.",
        "file" => $uploaded_file_name,
        "id" => $inserGrnObj["insertedId"]
      ];
    } else {
      $status = "warning";
      $remarks = "This Invoice already exists";

      //Inster into Log

      $inserGrnlog = queryInsert("INSERT INTO `erp_grn_log` SET 
    `company_id`='" . $company_id . "',
    `branch_id`='" . $branch_id . "',
    `location_id`='" . $location_id . "',
    `status`='" . $status . "',
    `remarks`='" . $remarks . "',
    `vendor_gst`='" . $gst_no . "',
    `price`='" . $total_amt . "',
    `file_name`='" . $uploaded_file_name . "'");

      $returnData = [
        "status" => "warning",
        "message" => "This Invoice already exists",
        "file" => $uploaded_file_name,
        "gst_check" => false
      ];
    }
  }


  return $returnData;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //POST REQUEST
  $responseData = proceedgrn($_POST);
} else {
  $responseData = [
    "status" => "warning",
    "message" => "Invalid request method",
    "data" => []
  ];
}

echo json_encode($responseData);
