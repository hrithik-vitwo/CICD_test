<?php
require_once("../../../app/v1/connection-branch-admin.php");
// require '../vendor/autoload.php';

use Razorpay\Api\Api;

$api_key = 'rzp_test_SRNNcrFvhl0M3C';
$api_secret = 'qPl5RyOuMtPnLrLL3AwvpJ6v';
$api = new Api($api_key, $api_secret);

// Capture the payment details after checkout
$payment_id = $_POST['payment_id'];
$amount = $_POST['amount'];
$bank = $_POST['bank_id'];

try {

    
    // Capture the payment
    $payment = $api->payment->fetch($payment_id);
    echo "Payment Status: " . $payment->status;
    if ($payment->status == 'authorized' || $payment->status == 'captured') {
        if ($payment->status == 'authorized') {
            $payment = $payment->capture(array('amount' => $amount)); // Amount in paise
            echo "Payment Successfully Captured!<br>";
        } else {
            echo "Payment was already Captured!<br>";
        }

        $payout = $api->payout->create(array(
            "account_number" => "2222222222222222", // The account number to send the payout to
            "fund_account" => array(
                "account_type" => "bank_account",
                "bank_account" => array(
                    "name" => "XYZ",
                    "ifsc" => "ICIC0000002",
                    "account_number" => "2222222222222222"
                ),
                "contact" => array(
                    "name" => "Somdutta Sengupta",
                    "email" => "somdutta075@gmail.com",
                    "contact" => "8910533689"
                )
            ),
            "amount" => $amount,
            "currency" => "INR",
            "mode" => "IMPS",
            "purpose" => "payout",
            "queue_if_low_balance" => true
        ));
        // $paymentInputData = [
        //     "BasicDetails" => [
        //         "documentNo" => $tnxDocNo, // Invoice Doc Number
        //         "documentDate" => $documentDate, // Invoice number
        //         "postingDate" =>  $postingDate, // current date
        //         "reference" => $tnxDocNo, // T code
        //         "remarks" => "Payment for - " . $invoiceConcadinate,
        //         "journalEntryReference" => "Payment/Expenses"
        //     ],
        //     "paymentDetails" => $POST['paymentDetails'],
        //     "vendorDetails" => $this->fetchVendorDetails($vendorId)['data'][0],
        //     "paymentInvItems" => $paymentInvItems,
        //     "roundOffValue" => $roundOffValue
        // ];
        echo "success";
        // Redirect to success page
        header('Location: success.php');
        exit();
    } else {
        echo "Payment Failed!";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
