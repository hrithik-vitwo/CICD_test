<?php
require_once("../../../../app/v1/connection-branch-admin.php");

$headerData = array('Content-Type: application/json');
$returnData = [];

$vendorId = $_POST['customerId'];
$payments = $_POST['payments'];
$paymentAmt = $_POST['paymentAmt'];
$grn_id = $_POST['inv_id'];
$value = json_decode($payments);

// $filterArr = array_filter($value, fn ($value) => !is_null($value) && $value !== '');
$filterArr = array_filter(array_map('trim', $value));

// console($value);
// console('filterArr');
// console($filterArr);

$flag = 0;
foreach ($filterArr as $key => $one) {
    $insPay = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                SET
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `payment_id`='$key',
                    `vendor_id`='$vendorId',
                    `grn_id`='$grn_id',
                    `payment_type`='pay',
                    `payment_amt`='$one',
                    `remarks`='$grn_id'
    ";
    $dbrr = queryInsert($insPay);
    $enterAdvAmt = '-' . $one;
    $insAdv = "INSERT INTO `" . ERP_GRN_PAYMENTS_LOG . "` 
                SET
                    `company_id`='$company_id',
                    `branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `payment_id`='$key',
                    `vendor_id`='$vendorId',
                    `grn_id`='0',
                    `payment_type`='advanced',
                    `payment_amt`='$enterAdvAmt',
                    `remarks`='$grn_id'
    ";
    $dbar = queryInsert($insAdv);
    if ($dbar['status'] != "success") {
        $flag++;
    }
}

if ($flag == 0) {
    $sql = "SELECT * FROM `" . ERP_GRNINVOICE . "` WHERE `grnIvId`='$grn_id'";
    $invSql = queryGet($sql);
    // console('data');
    $invDueAmt = $invSql['data']['dueAmt'];
    $status = 1;
    $updDueAmt = ($invDueAmt - $paymentAmt);
    if ($invDueAmt <= $paymentAmt) {
        $status = 4;
    } else {
        $status = 2;
    }
    $invUpdate = "UPDATE `" . ERP_GRNINVOICE . "` 
                        SET
                            `paymentStatus`='$status',
                            `dueAmt`='$updDueAmt' WHERE `grnIvId`='$grn_id'";
    $invUpdateData = queryUpdate($invUpdate);
} else {
    $returnData['status'] = "warning";
    $returnData['message'] = "Somthing went wrong!";
}

echo json_encode($returnData);
