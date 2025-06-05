<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
$headerData = array('Content-Type: application/json');

$CustomersObj = new CustomersController();
if ($_GET['act'] === "customersInvoiceLog") {
    $customerId = $_GET['customerId'];
    $getCustomerObj = $CustomersObj->getCustomersInvoiceLogDetails($customerId);
    // console($getCustomerObj);
    if($getCustomerObj['numRows']>0){
    echo json_encode($getCustomerObj);
    }else{
        $getCustomerObjadressTable=  $CustomersObj->getDataCustomerAddressDetails($customerId);
        echo json_encode($getCustomerObjadressTable);
    }
} else {
    echo "Something wrong, try again!";
}
