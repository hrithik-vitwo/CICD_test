<?php
function post_stock($id){

    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
     $returnData = [];

   
    $get_stock = queryGet("SELECT * FROM `erp_stock_count` WHERE `parent_id`=$id",true);
    foreach($get_stock['data'] as $stock){

        $storage = $stock['storageType'];
        $itemid = $stock['itemId'];
        $qty = $stock['stockCount'];
        $uom = $stock['itemUom'];
        $price = $stock['itemPrice'];
        $batch = $stock['batchNumber'];
        $rand = rand(100,1000);


        

         $insert_log = queryInsert("INSERT INTO  `erp_inventory_stocks_log` SET 
                            `companyId`=$company_id,
                            `branchId` = $branch_id,
                            `locationId` = $location_id,
                            `storageLocationId` = 1,
                            `storageType` = '".$storage."',
                            `itemId` = $itemid,
                            `itemQty` = $qty,
                            `remainingQty` = $qty,
                            `itemUom` = $uom,
                            `itemPrice`=$price,
                            `refNumber`=$rand,
                            `refActivityName` = 'STOCK-DIFF',
                            `logRef` = '".$batch."',
                            `createdBy` = '".$created_by."',
                            `updatedBy` = '".$updated_by."'");
                           // console($insert_log);


    }
//$update = ""

    if($insert_log['status'] == "success")
{

   // $update = queryUpdate("UPDATE `erp_stock_count_parent` SET `status` = 'posted' WHERE `count_id`=$id");
    $returnData['status'] == "success";
    $returnData['message'] == "Stock Posted";


}
else{
    $returnData['status'] == "warning";
    $returnData['message'] == "Stock Posting Failed";
    
}

return $returnData;

}


function delete_stock($id){

    $returnData =[];

    $id = $_GET['id'];
    
    $delete = queryUpdate("UPDATE `erp_stock_count_parent` SET `status` = 'delete' WHERE `count_id`=$id");
    if($delete['status'] == 'success'){
        $returnData['status'] == "success";
        $returnData['message'] == "Deleted Successfully";
    }
    else{
        $returnData['message'] == "Failed";

    }
    return $returnData;

}

?>