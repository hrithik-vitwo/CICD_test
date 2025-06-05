<?php
require_once("api-common-func.php");



// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  //  echo 1;

    $authVendor = authVendorApiRequest();
    $vendor_id = $authVendor['vendor_id'];
    $company_id = $authVendor['company_id'];
   // console($authVendor);
   $isValidate = validate($_POST, [
    "po_id" => "required"

]);
if ($isValidate["status"] != "success") {
    sendApiResponse([
        "status" => "error",
        "message" => "Invalid inputs"
       
    ], 405);
   
}

    $po_id = $_POST['po_id'];
   
  $invoice = "";
  $invoice = uploadFile($_FILES["invoice"], "../../../public/storage/logo/", ["jpg", "jpeg", "png"]); 
  if ($invoice['status'] == 'success') {
   //console(1);
      $invoice = $invoice['data'];
  } else {
 // console(2);
      $invoice = '';
  }

 
  $ins = queryInsert("INSERT INTO `erp_po_invoice` SET `po_invoice`='$invoice', `po_id`=$po_id,`created_by`=$vendor_id");

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