<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../../../app/v1/functions/branch/func-vendors-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$customersObj = new CustomersController();
//GET REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Customer List
    if ($_GET['act'] === 'customer') {
        $returnObj = [];
        $customerId = $_GET['customerId'];
        $getAllCustomersObj = $customersObj->getAllDataCustomer();
        if ($getAllCustomersObj["status"] == "success") {
            $returnObj = [
                "status" => true,
                "message" => "Customer Fetched Successfully",
                "data" => $getAllCustomersObj['data']

            ];
        } else {
            $returnObj = [
                "status" => false,
                "message" => "Customer Fetched Failed",
            ];
        }
        echo json_encode($returnObj);
    }

    // Vendor List
    if ($_GET['act'] === 'vendor') {
        
    }
}
