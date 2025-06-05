<?php
require_once("../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$returnData = [];

// Retrieve the table data from the AJAX request
$tableData = json_decode($_POST['tableData']);
// $response = array('tableData' => $tableData);
// echo json_encode($response);
$columnIndexes = array_keys($tableData[0]);
$columnNames = array_values($tableData[0]);


 $columnNamesStr = implode(",", $columnNames);
 $placeholdersStr = implode(",", array_fill(0, count($columnNames), "?"));


$batch = 0;

foreach (array_slice($tableData, 1)  as $row) {
   
 
    $itemcode = $row[1];
     
    
   //echo  $batchNo = $row[2];
    $count = $row[9];
 $batch = $row[2];
  if($batch != ""){
    
 

       $item_sql = queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemCode` = $itemcode AND `company_id` = $company_id AND `location_id` = $location_id");
     //  console($item_sql);
       $itemid = $item_sql['data']['itemId'];
      // Find the difference between the second and third column values
      $difference = $row[5] - $row[9];
      if($difference < 0){
        echo "n";
        $insert_stock_count = queryInsert("INSERT INTO `erp_stock_count` SET
        `itemId`=$itemid,
        `itemCode`='".$itemcode."',
        `batchNumber` = '".$batch."',
        `stockCount` = $count,
        `stockDifference`=$difference,
        `created_by` = '".$created_by."',
        `updated_by` = '".$updated_by."'
        ");
                        }
      else if($difference > 0){
       
                         $insert_stock_count = queryInsert("INSERT INTO `erp_stock_count` SET
                                    `itemId`=$itemid,
                                    `itemCode`='".$itemcode."',
                                    `batchNumber` = '".$batch."',
                                    `stockCount` = $count,
                                    `stockDifference`=$difference,
                                    `created_by` = '".$created_by."',
                                    `updated_by` = '".$updated_by."'
                                    ");
                                  //  console($insert_stock_count);

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
echo json_encode($returnData);
?>