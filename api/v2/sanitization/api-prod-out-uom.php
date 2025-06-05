<?php
include("../../../app/v1/functions/common/func-common.php");
require_once("../company/api-common-func.php");
 


$check_prod_out = queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE 1 AND (`refActivityName` = 'PROD-OUT' OR `refActivityName` = 'REV-PROD-OUT')  AND companyId = $company_id AND (itemUom = ' ' OR itemUom IS NULL)",true);

// console($check_prod_out);
// exit();



foreach($check_prod_out['data'] as $data)
{ 
    $id = $data['stockLogId'];
 
    $itemId = $data['itemId'];
  
     $uom = $data['itemUom'];
    $check_uom = queryGet("SELECT * FROM `erp_inventory_items` WHERE itemId = $itemId");
   // console($check_uom);
    $buom = $check_uom['data']['baseUnitMeasure'];
    if($buom != $uom){

      

      //  console($check_uom);

       $update_log_uom = queryUpdate("UPDATE `erp_inventory_stocks_log` SET `itemUom` = $buom WHERE `stockLogId` = $id");

       console($update_log_uom);

    }
}

?>