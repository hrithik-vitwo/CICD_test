<?php
require_once("api-common-func.php");

// API CODE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $authVendor = authVendorApiRequest();
    // $vendor_id = $authVendor['vendor_id'];
    $vendor_id = 1;
    $company_id = $authVendor['company_id'];
    $branch_id = $authVendor['branch_id'];
    $location_id = $authVendor['location_id'];
    
    $cond = "";
    
    if(isset($_POST['formDate']) && $_POST['formDate'] != '' && isset($_POST['toDate']) && $_POST['toDate'] != ''){
            $cond .= " AND `created_at` between '" . $_POST['formDate'] . " 00:00:00' AND '" . $_POST['toDate'] . " 23:59:59'";
    }

    $fetchPayment = queryGet("SELECT * FROM `erp_grn_payments` WHERE `vendor_id`=$vendor_id AND `company_id`=$company_id AND `branch_id`=$branch_id AND `location_id`=$location_id".$cond, true);
    $fetchPaymentObj = $fetchPayment['data'];

    // fetch payment logs
    foreach ($fetchPaymentObj as $key => $value) {
        $fetchPaymentLog = queryGet("SELECT pl.payment_type, pl.payment_id, pl.payment_amt, pl.roundoff, pl.writeback, pl.financial_charge, pl.forex, pl.currency_rate, pl.remarks, pl.status FROM `erp_grn_payments_log` AS pl WHERE `payment_id`=" . $value['payment_id'], true);

        $fetchPaymentObj[$key]['paymentLogCount'] = $fetchPaymentLog['numRows'];
        $fetchPaymentObj[$key]['paymentLogs'] = $fetchPaymentLog['data'];
    }

    // fetch bank statement
    foreach ($fetchPaymentObj as $key => $value) {
        $fetchPaymentLog = queryGet("SELECT bs.tnx_date, bs.tnx_category, bs.particular, bs.particular_ocr, bs.utr_number, bs.withdrawal_amt, bs.deposit_amt, bs.balance_amt, bs.upload_type, bs.reconciled_status FROM `erp_bank_statements` AS bs WHERE `bank_id`=" . $value['bank_id'], true);

        $fetchPaymentObj[$key]['bankStatementCount'] = $fetchPaymentLog['numRows'];
        $fetchPaymentObj[$key]['bankStatements'] = $fetchPaymentLog['data'];
    }

    sendApiResponse([
        "status" => "success",
        "message" => $fetchPayment["message"],
        "data" => $fetchPaymentObj
    ]);
} else {
    sendApiResponse([
        "status" => "error",
        "message" => "Method not allowed",
        "data" => ""
    ], 405);
}
