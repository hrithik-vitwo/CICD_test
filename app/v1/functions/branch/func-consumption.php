<?php

function consumption($POST){

    global $dbCon;
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    $returnData = [];

   console($POST);
    // exit();

    $cost_center = $POST['cost_center'];
    $post_date = $POST['post_date'];

    $items = $POST['listItem'];
    foreach($items as $item){
        $log_id = $item['log_id'];
        $item_id = $item['itemId'];

        if(isset($item['activeBatch']) && $item['activeBatch'] == 1){
            $refNumber = $item['productionDeclareBatch'];
        }
        else
        {
            $refNumber = $item['refNumber'];
        }

        $itemCode = $item['itemCode'];
        $itemName = $item['itemName'];
        $qty = '-'.$item['qty'];
        $uom = $item['uom'];
        $unitPrice = $item['unitPrice'];
        $totalPrice = $item['totalPrice'];

        $log = queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE `stockLogId` = $log_id");
        $storageLocationId = $log['data']['storageLocationId'];
        $storageType = $log['data']['storageType'];

        $insert_log = queryInsert("INSERT INTO `erp_inventory_stocks_log` SET `companyId` = $company_id,`branchId`=$branch_id,`locationId`=$location_id,`storageLocationId`=$storageLocationId,`storageType`='".$storageType."',`itemId`=$item_id,`itemQty`= '".$qty."',`itemUom`= '".$uom."',`itemPrice`='".$unitPrice."',`refActivityName`='CONSUMPTION POSTING',`logRef`='".$refNumber."',`createdBy`='".$created_by."',`updatedBy`='".$created_by."'");
      //  console($insert_log);
    }

    if($insert_log['status'] == "success"){
        $returnData['status'] = "Success";
        $returnData['message'] = "Consumption posting successful";
    } 
    else{
        $returnData['status'] = "Warning";
        $returnData['message'] = "Consumption posting unsuccessful";
    }


   


}



?>