<?php
require_once("../../../../app/v1/connection-branch-admin.php");
 $headerData = array('Content-Type: application/json');
 $returnData = [];


// Retrieve the table data from the AJAX request
$tableData = json_decode($_POST['tableData']);
 $id = $_POST['id'];
// console($tableData);
//exit();
// $response = array('tableData' => $tableData);
// echo json_encode($response);
$columnIndexes = array_keys($tableData[0]);
$columnNames = array_values($tableData[0]);

$DateColumnName = "DATE"; // Set the desired column name for the count
$TransactionColumnName = "TRANSACTION TYPE"; 
$DetailsColumnName = "TRANSACTION DESCRIPTION"; 
$InvoiceAmountColumnName = "Invoice Amount"; 
$PaymentsColumnName = "Payments"; 
$OpeningColumnName = "OPENING"; 

 $columnNamesStr = implode(",", $columnNames);
 $placeholdersStr = implode(",", array_fill(0, count($columnNames), "?"));


$batch = 0;

foreach (array_slice($tableData, 1)  as $row) {
  // console($row);
 
 
   // $itemcode = $row[1];
     
    
   //echo  $batchNo = $row[2];
//    $dateIndex = array_search($DateColumnName, $columnNames);
//     $transactionIndex = array_search($TransactionColumnName, $columnNames);
//     $detailsIndex = array_search($DetailsColumnName, $columnNames);
//     $invoiceAmountIndex = array_search($InvoiceAmountColumnName, $columnNames);
//     $paymentIndex = array_search($PaymentsColumnName, $columnNames);
//     $balanceIndex = array_search($BalanceColumnName, $columnNames);
   
//echo 0;
     $date = ($row[0] ?? 0) > 0 ? $row[0] : 0;
    //  "<br>";
     $transaction =$row[1] ?? 0;
    //  "<br>";
     $doc_no = $row[2];
  //  echo  "<br>";
     $transaction_desc = $row[3] ?? 0;
    // echo "<br>";
     $opening = $row[4] ?? 0;
    //  "<br>";
     $invoiceAmount = ($row[5] ?? 0) > 0 ? $row[5] : 0;
    //  "<br>";
     $payment = ($row[6] ?? 0) > 0 ? $row[6] : 0;
     $balance=0;

  // exit();

    // console($row[2]);




$check = queryGet("SELECT * FROM `erp_vendor_customer_reconciliation` WHERE `date`='".$date."' AND `transaction`='".$transaction."' AND `document_number` = '".$doc_no."' AND `invoice` = '".$invoiceAmount."' AND `payments` = '".$payment."'");

//console($check);



if($check['numRows'] > 0){

  // echo 0;
    $returnData['status'] == "warning";
    $returnData['message'] == "Data Already Inserted";
    // echo "Data Already Inserted";
   // echo json_encode($returnData);

}
else{
            //echo 1;

             $insert = queryInsert("INSERT INTO `erp_vendor_customer_reconciliation` SET
              `date`='".trim($date)."',
              `transaction` = '".trim($transaction)."',
              `details` = '".trim($transaction_desc)."',
              `recon_file_id` = $id,
              `invoice` = '".$invoiceAmount."',
              `payments` = '".$payment."',
              `document_number` = '".addslashes(trim($doc_no))."',
              `opening_amount` = '".$opening."',
              `balance` = '".$balance."',
              `created_by`='".$created_by."',
              `updated_by`='".$created_by."'
              ");
             // exit();

          // console($insert);
            //exit();

}

}

// console($insert);    
  
if($insert['status'] == "success"){
    $returnData['status'] == "success";
    $returnData['message'] == " Inserted";
    echo " Inserted";
}
else{
    $returnData['status'] == "warning";
    $returnData['message'] == "Something went wrong";
    echo "Something went wrong";
}
 //echo json_encode($returnData);
?>