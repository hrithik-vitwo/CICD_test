<?php
require_once("../../../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');
$returnData = [];

$customerId = $_POST['customerId'];
$payments = $_POST['payments'];
$paymentAmt = $_POST['paymentAmt'];
$inv_id = $_POST['inv_id'];
$value = json_decode($payments);

// $filterArr = array_filter($value, fn ($value) => !is_null($value) && $value !== '');
$filterArr = array_filter(array_map('trim', $value));

$flag = 0;
foreach ($filterArr as $key => $one) {
     $insPay = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                SET
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `payment_id`='$key',
                    `customer_id`='$customerId',
                    `invoice_id`='$inv_id',
                    `payment_type`='pay',
                    `payment_amt`='$one',
                    `remarks`='$inv_id'
    ";
    $dbrr = queryInsert($insPay);
    $enterAdvAmt = '-' . $one;
     $insAdv = "INSERT INTO `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` 
                    SET
                        `company_id`='$company_id',
                        `branch_id`='$branch_id',
                        `location_id`='$location_id',
                        `payment_id`='$key',
                        `customer_id`='$customerId',
                        `invoice_id`='$inv_id',
                        `payment_type`='advanced',
                        `payment_amt`='$enterAdvAmt',
                        `remarks`='$inv_id'
    ";
    $dbar = queryInsert($insAdv);
    if ($dbar['status'] != "success") {
        $flag++;
    }
}

if ($flag == 0) {
    $sql = "SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE `so_invoice_id`='$inv_id'";
    $invSql = queryGet($sql);
    // console('data');
    $invDueAmt = $invSql['data']['due_amount'];
    $status = 1;
    $updDueAmt = ($invDueAmt - $paymentAmt);
    if ($invDueAmt <= $paymentAmt) {
        $status = 4;
    } else {
        $status = 2;
    }
    $invUpdate = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` 
                        SET
                            `invoiceStatus`='$status',
                            `due_amount`='$updDueAmt' WHERE `so_invoice_id`='$inv_id'";
    $invUpdateData = queryUpdate($invUpdate);
} else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong!";
}

echo json_encode($returnData);
