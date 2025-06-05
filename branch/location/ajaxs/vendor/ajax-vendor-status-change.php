<?php

include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");

if(isset($_GET["ids"]) && $_GET["ids"] != "")
{
    global $company_id;
    global $branch_id;
    global $location_id;

    $unique_code = "PABL".time();

    $encoded_array = json_decode(stripslashes(($_GET["ids"])));

    foreach($encoded_array as $id)
    {
        $update = queryUpdate('UPDATE `'.ERP_GRNINVOICE.'` SET `paymentStatus`="18" WHERE `grnIvId`="'.$id.'"');
        if($update["status"]!="success"){
            $errors++;
        }
        else
        {

            $getQuery = queryGet('SELECT * FROM `'.ERP_GRNINVOICE.'` WHERE `grnIvId`="'.$id.'"');
            $vendor_id = $getQuery["data"]["vendorId"];

            $sql = "INSERT INTO `erp_payment_initiate_request` SET `company_id`='" . $company_id . "',`branch_id`='" . $branch_id . "', `location_id`='" . $location_id . "', `code`='" . $unique_code . "', `vendor_id`='" . $vendor_id . "',`invoice_id`='" . $id."'";

            $instQuery = queryInsert($sql);
            if($instQuery["status"] != "success")
            {
                $errors++;
            }
        }
    }

    if($errors == 0)
    {
        $returnData = [
            "status" => "success",
            "message" => "Successfully Initiated for payment",
            "code"=>$unique_code
          ];
    }
    else
    {
        $returnData = [
            "status" => "warning",
            "message" => "Something went wrong",
            "code" => ""
          ];
    }

    echo json_encode($returnData);
}

?>