<?php
require_once("api-common-func.php");



// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  //  echo 1;

    $authCustomer = authCustomerApiRequest();
    $customer_id = $authCustomer['customer_id'];
    $company_id = $authCustomer['company_id'];
   // console($authcustomer);
   $isValidate = validate($_POST, [
    "so_id" => "required"

]);
if ($isValidate["status"] != "success") {
    sendApiResponse([
        "status" => "error",
        "message" => "Invalid inputs"
       
    ], 405);
   
}

    $so_id = $_POST['so_id'];
   
  $invoice = "";
  $invoice = uploadFile($_FILES["invoice"], "../../../public/storage/logo/", ["jpg", "jpeg", "png"]); 
  if ($invoice['status'] == 'success') {
   //console(1);
      $invoice = $invoice['data'];
  } else {
 // console(2);
      $invoice = '';
  }

 
  $ins = queryInsert("INSERT INTO `erp_po_invoice` SET `po_invoice`='$invoice', `so_id`=$so_id,`created_by`=$customer_id");

if($ins["status"]=="success"){
    sendApiResponse([
        "status" => "success",
        "message" => "uploaded successfully"
     
    ], 200);
}
else{
    sendApiResponse([
        "status" => "error",
        "message" => "something went wrong"
    ], 405);
}
   
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed"
       
    ], 405);
}
//echo "ok";