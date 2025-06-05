<?php
require_once("../../../../app/v1/connection-branch-admin.php");
// $headerData = array('Content-Type: application/json');
// $returnData = [];
// $dbObj = new Database(true);
// $dbObj->setSuccessMsg("Stock count posted successfully");
// $dbObj->setErrorMsg("Stock count posting failed!");


// Retrieve the table data from the AJAX request
$tableData = json_decode($_POST['tableData']);
// $response = array('tableData' => $tableData);
// echo json_encode($response);
$columnIndexes = array_keys($tableData[0]);
$columnNames = array_values($tableData[0]);

$countColumnName = "Physical Quantity"; // Set the desired column name for the count
$qtyColumnName = "Item Quantity";
$batchColumnName = "Batch Number";
$storageColumnName = "Storage Type";
$uomColumnName = "Item UOM";
$priceColumnName = "Item Price";
$itemCodeColumnName = "Item Code";
$itemBornDateColoumnName = "Born Date";

$columnNamesStr = implode(",", $columnNames);
$placeholdersStr = implode(",", array_fill(0, count($columnNames), "?"));


$batch = 0;

echo $today = date('Y-m-d');

$insert_stock_parent = queryInsert("INSERT INTO `erp_stock_count_parent` 
SET 
`company_id`=$company_id,
`branch_id` = $branch_id,
`location_id`=$location_id,
`from_date`='" . $today . "',
`to_date`='" . $today . "',
`created_by`='" . $created_by . "', 
`updated_by`='" . $created_by . "'
");
//console($insert_stock_parent);


 $parent_id = $insert_stock_parent['insertedId'];


foreach (array_slice($tableData, 1)  as $row) {

  // console($row);
  // $itemcode = $row[1];


  //echo  $batchNo = $row[2];
  $countIndex = array_search($countColumnName, $columnNames);
  // console($countIndex);
  $qtyIndex = array_search($qtyColumnName, $columnNames);
  $batchIndex = array_search($batchColumnName, $columnNames);
  $storageIndex = array_search($storageColumnName, $columnNames);
  $uomIndex = array_search($uomColumnName, $columnNames);
  $priceIndex = array_search($priceColumnName, $columnNames);
  $itemCodeIndex = array_search($itemCodeColumnName, $columnNames);
  
  $itemBornDateIndex = array_search($itemBornDateColoumnName, $columnNames);
  

  // console("==============================");
  // console($row);
  // console(["countIndex" => $countIndex, "qtyIndex" => $qtyIndex, "uomIndex" => $uomIndex, "priceIndex" => $priceIndex, "itemCodeIndex" => $itemCodeIndex, "storageIndex" => $storageIndex, "batchIndex" => $batchIndex]);

  $count = ($row[$countIndex] ?? 0) > 0 ? $row[$countIndex] : 0;
  $qty = ($row[$qtyIndex] ?? 0) > 0 ? $row[$qtyIndex] : 0;
  $uom = ($row[$uomIndex] ?? 0) > 0 ? $row[$uomIndex] : 0;
  $price = ($row[$priceIndex] ?? 0) > 0 ? $row[$priceIndex] : 0;
  $itemcode = ($row[$itemCodeIndex] ?? 0) > 0 ? $row[$itemCodeIndex] : 0;
  $storage = ($row[$storageIndex] ?? "") !="" ? $row[$storageIndex] : "";
  $batch = ($row[$batchIndex] ?? "") != "" ? $row[$batchIndex] : "";
  $born = ($row[$itemBornDateIndex] ?? "") != "" ? $row[$itemBornDateIndex] : "";

 // console(["count" => $count, "qty" => $qty, "uom" => $uom, "price" => $price, "itemcode" => $itemcode, "storage" => $storage, "batch" => $batch]);

    if($batch != ""){



         $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemCode` = $itemcode AND `company_id` = $company_id AND `location_id` = $location_id");
       //  console($item_sql);
         $itemid = $item_sql['data']['itemId'];
        // Find the difference between the second and third column values
        $difference = $count - $qty ;

        if($difference < 0){


          $insert_stock_count = queryInsert("INSERT INTO `erp_stock_count` SET
          `itemId`=$itemid,
          `itemCode`='" . $itemcode . "',
          `batchNumber` = '" . $batch . "',
          `stockCount` = $count,
          `itemQty`=$qty,
          `storageType`='" . $storage . "', 
          `itemUom`= '" . $uom . "',
          `itemPrice`='" . $price . "',
          `stockDifference`=$difference,
          `created_by` = '" . $created_by . "',
          `parent_id`=$parent_id,
          `batchBornDate`='".$born."',
          `updated_by` = '" . $updated_by . "'");
          //console($insert_stock_count);


  // $insert_log = queryInsert("INSERT INTO `erp_stock_log` SET `erp_inventory_stocks_log` SET 
  //                           `companyId`=$company_id,
  //                           `branchId` = $branch_id,
  //                           `locationId` = $location_id,
  //                           `storageLocationId` = 1,
  //                           `storageType` = '".$storage."',
  //                           `itemId` = $itemid,
  //                           `itemQty` = $qty,
  //                           `remainingQty` = $qty,
  //                           `itemUom` = $uom,
  //                           `itemPrice`=$price,
  //                           `logRef` = 'stock count',
  //                           `refNumber` = $batch,
  //                           `created_by` = '".$created_by."',
  //                           `updated_by` = '".$updated_by."'");
  // console($insert_stock_count);
  }
  else if($difference > 0){

    $insert_stock_count = queryInsert("INSERT INTO `erp_stock_count` SET
    `itemId`=$itemid,
    `itemCode`='" . $itemcode . "',
    `batchNumber` = '" . $batch . "',
    `stockCount` = $count,
    `itemQty`=$qty,
    `storageType`='" . $storage . "', 
    `itemUom`= '" . $uom . "',
    `itemPrice`='" . $price . "',
    `stockDifference`=$difference,
    `created_by` = '" . $created_by . "',
    `parent_id`=$parent_id,
    `batchBornDate`='".$born."',
    `updated_by` = '" . $updated_by . "'");

    // console($insert_stock_count);

        // $insert_log = queryInsert("INSERT INTO `erp_stock_log` SET `erp_inventory_stocks_log` SET 
        // `companyId`=$company_id,
        // `branchId` = $branch_id,
        // `locationId` = $location_id,
        // `storageLocationId` = 1, 
        // `storageType` = '".$storage."',
        // `itemId` = $itemid, 
        // `itemQty` = $qty,
        // `remainingQty` = $qty,
        // `itemUom` = $uom, 
        // `itemPrice`=$price,
        // `logRef` = 'stock count',
        // `refNumber` = $batch,
        // `created_by` = '".$created_by."',    
        // `updated_by` = '".$updated_by."'");


                           }
                          else{

                          }


  }


}

if($insert_stock_count['status'] == "success"){
    $returnData['status'] == "success";
    $returnData['message'] == "Stock Count Inserted";
}
else{
    $returnData['status'] == "warning";
    $returnData['message'] == "Something went wrong";
}
return $returnData;
// echo json_encode($returnData);
