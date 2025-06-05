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
  

    $check = 0;
    foreach ($_POST['listItem'] as $key => $oneItem) {
        $itemStocks = $oneItem['itemStocks'];
        $enterQty = $oneItem['qty'];
        if ($enterQty > $itemStocks) {
            $check = 0;
        } else {
            $check++;
        }
    }

    if ($check > 0) {
        // add pos invoice
        $addGoodsInvoice = $BranchSoObj->insertBranchDirectInvoice($_POST);
       // console($addGoodsInvoice);
       // exit();
        if($addGoodsInvoice['status'] == "success"){
            $so_inv_id = $addGoodsInvoice['lastID'];
            $getInv = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id` = $so_inv_id");
            $totalAmount = $getInv['data']['all_total_amt'];
            $customerId = $addGoodsInvoice['customerDetails']['customerId'];
            
            
            $data = [
                'paymentDetails' => [
                    'paymentCollectType' => 'collect',
                    'customerId' => $customerId,
                    'collectPayment' => $totalAmount,
                    'bankId' => '',
                    'paymentAdviceImg' => '',
                    'documentDate' => '2024-09-03',
                    'postingDate' => '2024-09-03',
                    'tnxDocNo' => '',
                    'advancedPayAmt' => 0.00,
                ],
            
                'submitCollectPaymentBtn' => '',
                'modalDueamt' => ' ',
            
                'paymentInvDetails' => [
                    $customerId => [

                        [
                            'inputRoundOffInrWithSign' => -0.68,
                            'inputRoundOffWithSign' => -0.68,
                            'inputWriteBackInrWithSign' => 10,
                            'inputWriteBackWithSign' => 10,
                            'inputFinancialChargesInrWithSign' => 0,
                            'inputFinancialChargesWithSign' => 0,
                            'inputForexLossGainInrWithSign' => 0.00,
                            'inputForexLossGainWithSign' => 0.00,
                            'inputTotalTdsWithSign' => -5,
                            'invoiceId' => 35,
                            'invoiceNo' => 'GTI2023-1117',
                            'invoiceStatus' => 'sent',
                            'creditPeriod' => 30,
                            'invAmt' => 24987.68,
                            'dueAmt' => 24987.68,
                            'customer_id' => 7,
                            'recAmt' => 100,
                        ],
                        [
                            'inputRoundOffInrWithSign' => 0.00,
                            'inputRoundOffWithSign' => 0.00,
                            'inputWriteBackInrWithSign' => 0.00,
                            'inputWriteBackWithSign' => 0.00,
                            'inputFinancialChargesInrWithSign' => 0.00,
                            'inputFinancialChargesWithSign' => 0.00,
                            'inputForexLossGainInrWithSign' => 0.00,
                            'inputForexLossGainWithSign' => 0.00,
                            'inputTotalTdsWithSign' => 0.00,
                            'invoiceId' => 65,
                            'invoiceNo' => 'INV-0000000060',
                            'invoiceStatus' => 'sent',
                            'creditPeriod' => 30,
                            'invAmt' => 7848.28,
                            'dueAmt' => 7848.28,
                            'customer_id' => 7,
                            'recAmt' => '',
                        ],
                    ],
                ],
            ];
            
            

        }
        echo json_encode($addGoodsInvoice);
        // console($addGoodsInvoice);
    } else {
        $responseData['type'] = "pos_invoice";
        $responseData['status'] = "low";
        $responseData['message'] = "Stock is low";
        echo json_encode($responseData);
    }

} else if ($_POST['act'] === "pos_salesorder") {
  
    // add pos sales order
    $addGoodsInvoice = $BranchSoObj->addBranchSo($_POST);
    echo json_encode($addGoodsInvoice);
} else {
  
    $responseData['status'] = "error";
    $responseData['message'] = "Somthing went wrong!";
    echo json_encode($responseData);
}
?>