<?php
require_once("../../../../app/v1/connection-branch-admin.php");
// header("Content-Type: application/json");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');

$responseData = [];

//console($_POST);
// exit();

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();

if ($_POST['act'] === "pos_invoice") {

    $check = 1;  // Assume stock is available initially
    $lowStockItems = [];  // Array to hold items with insufficient stock

    // Loop through each item in the POST request
    foreach ($_POST['listItem'] as $key => $oneItem) {
        
        $enterQty = $oneItem['qty'];
        $itemId = $oneItem['itemId'];
        
        // Fetch available stock for the item
        $itemStocks = $BranchSoObj->deliveryCreateItemQty($itemId)['sumOfBatches'];
        
        // If the requested quantity exceeds available stock, mark as insufficient
        if (intval($enterQty) > intval($itemStocks)) {
            $check = 0;  // Set check to 0 if stock is insufficient
            // Add item to low stock list
            $lowStockItems[] = [
                'itemId' => $itemId,
                'itemName' => $oneItem['itemName'],
                'availableStock' => $itemStocks,
                'requestStock' => $enterQty,
            ];
        }
    }

    // If any item had insufficient stock (i.e., $check = 0)
    if ($check === 0) {
        // If stock is low, return the response with low stock details
        $responseData['type'] = "pos_invoice";
        $responseData['status'] = "low";
        $responseData['message'] = "Stock is low";
        $responseData['itemlist'] = $lowStockItems;
        echo json_encode($responseData);
    } else {
        // All stock is available, proceed with creating the POS invoice
        // Query to get "Walk In Customer" ID
        $query = "SELECT `customer_id`
                  FROM `erp_customer`
                  WHERE `company_id` = $company_id 
                  AND `location_id` = $location_id
                  AND `company_branch_id` = $branch_id 
                  AND `customer_authorised_person_name` = 'Walk In Customer'
                  LIMIT 1";

        $check_customer = queryGet($query);
        $customer_id = $check_customer['data']['customer_id'];
        
        // If "Walk In Customer" checkbox is checked, set customer ID
        if (isset($_POST['walkInCustomerCheckbox'])) {
            $_POST['customerId'] = $customer_id;
        }
        
        // Insert POS invoice into the system
        $addGoodsInvoice = $BranchSoObj->insertBranchDirectInvoice($_POST);
        
        // Check if the invoice was successfully added
        if ($addGoodsInvoice['status'] == "success") {
            $so_inv_id = $addGoodsInvoice['lastID'];
            
            // Fetch the invoice details
            $getInv = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id` = $so_inv_id");
            $totalAmount = $getInv['data']['all_total_amt'];
            $customerId = $addGoodsInvoice['customerDetails']['customerId'];
        }

        // Return success response with invoice details
        echo json_encode($addGoodsInvoice);
    }

}
 else if ($_POST['act'] === "pos_salesorder") {
  
    // add pos sales order
    $addGoodsInvoice = $BranchSoObj->addBranchSo($_POST);
    echo json_encode($addGoodsInvoice);
} else {
  
    $responseData['status'] = "error";
    $responseData['message'] = "Somthing went wrong!";
    echo json_encode($responseData);
}
?>