<?php
require_once("../../../../app/v1/connection-branch-admin.php");

require_once("../../../../app/v1/functions/company/func-ChartOfAccounts.php");

$headerData = array('Content-Type: application/json');

if(isset($_GET["vendor"]) && isset($_GET["grn"]))

{
    $vendor_id = $_GET["vendor"];
    $grn_id = $_GET["grn"];

    $vendor = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id` = '" . $vendor_id . "'");

    if($vendor["status"] == "success")
    {
        $vendor_code = $vendor["data"]["vendor_code"];
        $vendor_name = $vendor["data"]["trade_name"];
        $creditPeriod = $vendor["data"]["vendor_credit_period"];

        $sql = "UPDATE `erp_grn_multiple` SET `vendor_id`='" . $vendor_id . "', `vendor_code`='".$vendor_code."' WHERE `grn_mul_id`='" . $grn_id."'";

        $update = queryUpdate($sql);

    $returnData = [
        "status" => "success",
        "message" => "Vendor Mapped successfully",
        "code"=>$vendor_code,
        "name" => $vendor_name,
        "creditPeriod" => $creditPeriod
      ];
    }
    else
    {
        $returnData = [
            "status" => "warning",
            "message" => "Vendor not found"
          ];
    }

    echo json_encode($returnData);

}

?>
