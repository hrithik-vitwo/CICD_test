<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-branches.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../app/v1/functions/branch/func-grn-controller.php");

if (isset($_POST["add-table-settings"])) {
    $editDataObj = updateInsertTableSettings($_POST, $_SESSION["logedBranchAdminInfo"]["adminId"]);
    swalToast($editDataObj["status"], $editDataObj["message"]);
}

$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];

$grnObj = new GrnController();

// imranali59059
if (isset($_POST['submitPaymentForm'])) {
    // console($_POST);
    // exit;
    $addCollectPayment = $grnObj->insertMultiVendorPayment($_POST);
    // console($addCollectPayment);
    // exit;
    if ($addCollectPayment['status'] == "success") {
        swalToast($addCollectPayment["status"], $addCollectPayment["message"], LOCATION_URL . 'manage-vendor-payment.php');
    } else {
        swalToast($addCollectPayment["status"], $addCollectPayment["message"]);
    }
}

if (isset($_GET['code'])) {
    $encodedStrings = json_decode(urldecode($_GET['code']), true);
    // Decode twice
    $firstDecodedStrings = array_map('base64_decode', $encodedStrings);
    $secondDecodedStrings = array_map('base64_decode', $firstDecodedStrings);
    // echo "<pre>";
    // console($secondDecodedStrings);
    // echo "</pre>";
} else {
    echo "Code parameter not found in the URL.";
}


// $customerList = $grnObj->fetchCustomerList()['data'];
$vendorList = $grnObj->fetchAllVendor()['data'];
$invoiceData = $grnObj->fetchGRNByVendorId(14)['data'];
// $invoiceData = $grnObj->fetchBranchSoInvoiceBycustomerId(1)['data'];

// console($vendorList);
// console($invoiceData);
?>
<link rel="stylesheet" href="../../public/assets/sales-order.css">
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
    .text {
        font-size: 1.2em;
    }

    .textColor {
        color: #0090ff;
        font-weight: bold;
    }

    .verticalAlign {
        text-align: right;
        vertical-align: bottom !important;
    }

    .tableStriped {
        background-color: #f2f2f2 !important;
    }

    .customPadding {
        padding-top: 180px !important;
    }

    .borderWhite {
        border: #fff;
    }

    .borderBlue {
        border-bottom: 3px solid #0090ff;
    }

    a.btn.shadow.waves-effect.waves-light:hover {
        background: #1a3a84db;
        color: white;
    }

    /* ######################################### */
    /* // design input type file STYLE  */

    .image-input input {
        display: none;
    }

    .image-input label {
        display: block;
        border: 2px dashed #dcdcdc;
        padding: 40px;
        cursor: pointer;
    }

    .image-input label i {
        font-size: 125%;
        margin-right: 0.3rem;
    }

    .image-input label:hover i {
        animation: shake 0.35s;
    }

    .image-input img {
        max-width: 175px;
        display: none;
    }

    .image-input span {
        display: none;
        cursor: pointer;
    }

    /******new****/

    .image-input label {
        display: flex;
        align-items: center;
        margin-top: 1em;
        justify-content: center;
        background: #fff;
        box-shadow: 6px 4px 11px -3px #00000070;
        padding: 20px;
        border-radius: 7px;
        border: 2px dashed #dcdcdc;
    }

    img.image-preview {
        object-fit: contain;
        aspect-ratio: 6/3;
        margin: auto;
    }

    .card.collect-payment-card {
        height: 323px;
        min-height: 100%;
    }

    .inputTableRow {
        overflow: auto;
    }

    /*******settlement*******/

    .settlement-card {
        min-height: 90%;
    }

    .settlement-card .image-input {
        overflow: auto;
        height: auto;
        background: #FFF;
        padding: 10px;
        border-radius: 12px;
        margin-top: 15px;
        box-shadow: 0px 3px 9px -5px #000;
    }

    @media (max-width: 575px) {
        .card.collect-payment-card {
            height: max-content;
            min-height: auto;
        }

        .card.collect-payment-card select {
            margin-top: 2em;
        }
    }


    @keyframes shake {
        0% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(10deg);
        }

        50% {
            transform: rotate(0deg);
        }

        75% {
            transform: rotate(-10deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }
</style>
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <div>
                <?php
                $fetchCOADetails = get_acc_bank_cash_accounts()['data'];
                // console($fetchCOADetails);
                $vendors = [];
                foreach ($secondDecodedStrings as $line) {
                    $parts = explode('|', $line);
                    $code = $parts[0];
                    $vendorId = $parts[1];
                    if (!isset($vendors[$vendorId])) {
                        $vendors[$vendorId] = $code;
                    }
                }
                // console($_POST);
                ?>
            </div>

            <form action="" method="POST" id="vendorPaymentForm">
                <!--Header-->
                <input type="hidden" name="submitPaymentForm">
                <input type="hidden" name="paymentDetails[paymentCollectType]" value="collect">
                <div class="row m-0 p-0 py-2 my-2">
                    <div class="col-6">
                        <h5><strong>Vendors Payment</strong></h5>
                    </div>
                    <div class="col-6">
                        <div class="float-right d-flex">
                            <div class="mx-2"><button class="btn btn-success" type="submit" id="submitPaymentFormBtn">Save Payment</button></div>
                        </div>
                    </div>
                </div>
                <!-- Collect Payment Modal -->
                <div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Payment</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="totalPaidAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalReceiveAmt">0</span> amount paid against invoice</div>
                                <div class="totalCaptureAmtDiv"><span style="font-family: 'Font Awesome 5 Free';" id="totalCaptureAmt">0</span> amount captured as an advance</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="submitCollectPaymentBtn" class="btn btn-primary">Confirm</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Body-->
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="card collect-payment-card">
                            <div class="card-header p-3">
                                <h4>Info</h4>
                            </div>
                            <div class="card-body">
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label for="" class="">Payment Bank Details</label>
                                        <select name="paymentDetails[bankId]" class="form-control" id="paymentDetailsBankDropDown">
                                            <option value="0">Select Bank</option>
                                            <?php
                                            foreach ($fetchCOADetails as $one) {
                                                if ($one['bank_name'] != "") {
                                            ?>
                                                    <option value="<?= $one['id'] ?>" data-is-icici-cib-enabled="<?= $one["isIciciCibEnabled"] ?>"><?= $one['bank_name'] ?><?= $one['account_no'] != "" ? "(" . $one['account_no'] . ")" : "" ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="text-xs text-danger" id="paymentDetailsBankDropDownSpan"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="">Total Payment Amount</label>
                                        <div class="input-group input-group-sm m-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><?= $companyCurrencyData["currency_name"] ?></span>
                                            </div>
                                            <input type="text" name="paymentDetails[collectPayment]" id="inputTotalPaymentAmount" class="form-control border py-3 collectTotalAmt text-right" placeholder="0.00" readonly>
                                        </div>
                                        <span class="text-xs text-danger" id="spanTotalPaymentAmount"></span>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="">Total Remain Amount</label>
                                        <div class="input-group input-group-sm m-0">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><?= $companyCurrencyData["currency_name"] ?></span>
                                            </div>
                                            <input type="text" name="paymentDetails[TotalRemainAmount]" id="inputTotalRemainAmount" class="form-control border py-3 text-right" placeholder="0.00" readonly>
                                        </div>
                                    </div>
                                    <div class="vendor-info">
                                        <label for="" class="">Vendor/s:</label>
                                        <div class="vendor-list" style="overflow-x: auto; max-height: 160px;">
                                            <ol>
                                                <?php
                                                foreach ($vendors as $vendorId => $code) {
                                                    $vendorDetails = $grnObj->fetchVendorDetails($vendorId)['data'][0];
                                                ?>
                                                    <input type="hidden" name="vendorDetails[<?= $vendorId ?>][vendorId]" value="<?= $vendorId ?>">
                                                    <li><?= $vendorDetails['trade_name']; ?></li>
                                                <?php
                                                }
                                                ?>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="card collect-payment-card">
                            <div class="card-header p-3">
                                <h4>Info</h4>
                            </div>
                            <div class="card-body" id="paymentDetailsDiv">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="image-input">
                                            <input type="file" name="paymentDetails[paymentAdviceImg]" accept="image/*" id="imageInput">
                                            <label for="imageInput" class="image-button"><i class="fa fa-image po-list-icon mr-2"></i> Upload Payment Advice</label>
                                            <img src="" class="image-preview">
                                            <span class="change-image float-right mt-3"><button class=" btn btn-danger"><i class="fa fa-times mr-2"></i>Remove</button></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input totalamount">
                                            <label for="">Document Date</label>
                                            <input type="date" name="paymentDetails[documentDate]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input totaldueamount">
                                            <label for="">Posting Date</label>
                                            <input type="date" name="paymentDetails[postingDate]" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="form-input totaloverdue">
                                            <label for="">Transaction Id / Doc. No.</label>
                                            <input type="text" placeholder="Tnx. Id / Doc. No." name="paymentDetails[tnxDocNo]" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="form-input totaloverdue">
                                            <label for="">Remarks</label>
                                            <textarea name="paymentDetails[remarks]" cols="10" rows="3" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="inputTableRow mt-3">
                            <table class="table">
                                <tr>
                                    <th>IV Doc. No.</th>
                                    <th>Vendor</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Invoice Amt.</th>
                                    <th>Due Amt.</th>
                                    <th>Pay Amt. (<?= $companyCurrencyData["currency_name"] ?>)</th>
                                    <th>Adjusted Amt. (<?= $companyCurrencyData["currency_name"] ?>)</th>
                                    <th>Remain Amt. (<?= $companyCurrencyData["currency_name"] ?>)</th>
                                    <th>Action</th>
                                </tr>
                                <?php
                                $invRowNo = 0;
                                $paymentAmountDetails = [];
                                $totalPaymentAmountInCompanyCurrency = 0;
                                foreach ($secondDecodedStrings as $line) {
                                    $parts = explode('|', $line);
                                    $code = $parts[0];
                                    $vendorId = $parts[1];
                                    $vendorInvoicesObj = queryGet("SELECT * FROM `erp_payment_initiate_request` WHERE code='$code' AND vendor_id=$vendorId", true);

                                    foreach ($vendorInvoicesObj['data'] as $key => $fetch) {
                                        $invRowNo += 1;
                                        $invoicesObj = $grnObj->fetchGRNInvoiceById($fetch['invoice_id']);
                                        $invoiceData = $invoicesObj['data'][0];

                                        $vendorBankAccountObj = queryGet('SELECT * FROM `erp_vendor_bank_details` WHERE `vendor_id`=' . $vendorId . ' AND `vendor_bank_default_flag`=1 AND `vendor_bank_active_flag`=1');
                                        $vendorBankAccountData = $vendorBankAccountObj["data"];

                                        $totalDueAmount += $invoiceData['dueAmt'];
                                        $statusLabel = fetchStatusMasterByCode($invoiceData['paymentStatus'])['data']['label'];

                                        if ($statusLabel != "") {
                                            $invoiceCurrencyObj = queryGet('SELECT currency.`currency_id`, currency.`currency_name`, currency.`currency_icon`, grn.`conversion_rate` FROM `erp_grn` AS grn LEFT JOIN `erp_currency_type` AS currency ON grn.`currency`=currency.`currency_id` WHERE grn.`grnId`=' . $invoiceData["grnId"]);
                                            $invoiceData = $invoiceData + $invoiceCurrencyObj["data"];

                                            if ($statusLabel == "paid") {
                                                $statusClass = "status";
                                            } elseif ($statusLabel == "partial paid") {
                                                $statusClass = "status-warning";
                                            } else {
                                                $statusClass = "status-danger";
                                            }

                                            $days = $invoiceData['credit_period'];
                                            $date = date_create($invoiceData['invoice_date']);
                                            date_add($date, date_interval_create_from_date_string($days . " days"));
                                            $creditPeriod = date_format($date, "d-m-Y");
                                            $due_amt = $invoiceData['dueAmt'];
                                            $inv_amt = $invoiceData['grnTotalAmount'];
                                            $duePercentage = ($due_amt / $inv_amt) * 100;
                                        } else {
                                            $statusClass = "";
                                            $creditPeriod = "";
                                            $due_amt = "";
                                            $inv_amt = "";
                                            $duePercentage = "";
                                            continue;
                                        }
                                        // console($fetch);
                                        // console($invoiceData);
                                        // console($invoiceData);
                                        if ($companyCurrencyData["currency_name"] != $invoiceData["currency_name"]) {
                                            $currencyConverstionObj = currency_conversion($companyCurrencyData["currency_name"], $invoiceData["currency_name"]);
                                            $currentConverstionRate = $currencyConverstionObj["quotes"][$companyCurrencyData["currency_name"] . $invoiceData["currency_name"]] ?? $invoiceData["conversion_rate"];
                                        } else {
                                            $currentConverstionRate = $invoiceData["conversion_rate"];
                                        }
                                        $currency_name = $invoiceData["currency_name"];
                                        $currency_check = queryGet('SELECT * FROM `erp_currency_type` WHERE `currency_name`="' . $currency_name . '"');
                                        $currency_id = $currency_check["data"]["currency_id"] ?? 0;

                                        $totalPaymentAmountInCompanyCurrency += $invoiceData['dueAmt'] / $currentConverstionRate;
                                        $vendor_id = $invoiceData['vendorId'];
                                        // console($currencyConverstionObj);
                                ?>
                                        <tr>

                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][currencyRate]" value="<?= $currentConverstionRate ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][currency_id]" value="<?= $currency_id ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][grnIvId]" value="<?= $invoiceData['grnIvId'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][grnCode]" value="<?= $invoiceData['grnIvCode'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][vendorId]" value="<?= $invoiceData['vendorId'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][paymentStatus]" value="<?= $statusLabel ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][creditPeriod]" value="<?= $invoiceData['credit_period'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][invAmt]" value="<?= $invoiceData['grnTotalAmount'] ?>">
                                            <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][dueAmt]" value="<?= $invoiceData['dueAmt'] ?>">

                                            <input type="hidden" id="inputPreviousCurrencyRate_<?= $invRowNo ?>" value="<?= $invoiceData["conversion_rate"] ?>">
                                            <input type="hidden" id="inputCurrentCurrencyRate_<?= $invRowNo ?>" value="<?= $currentConverstionRate ?>">
                                            <input type="hidden" id="inputInvoiceCurrencyName_<?= $invRowNo ?>" value="<?= $invoiceData["currency_name"] ?>">
                                            <input type="hidden" id="inputCompanyCurrencyName_<?= $invRowNo ?>" value="<?= $companyCurrencyData["currency_name"] ?>">

                                            <input type="hidden" value="<?= $vendorBankAccountData["vendor_bank_name"] ?>" id="vendorBankName_<?= $invRowNo ?>" class="vendorBankName">
                                            <input type="hidden" value="<?= $vendorBankAccountData["account_holder"] ?>" id="vendorBankAccHolderName_<?= $invRowNo ?>" class="vendorBankAccHolderName">
                                            <input type="hidden" value="<?= $vendorBankAccountData["vendor_bank_account_no"] ?>" id="vendorBankAccNumber_<?= $invRowNo ?>" class="vendorBankAccNumber">
                                            <input type="hidden" value="<?= $vendorBankAccountData["vendor_bank_ifsc"] ?>" id="vendorBankIfsc_<?= $invRowNo ?>" class="vendorBankIfsc">
                                            <input type="hidden" value="<?= $vendorBankAccountData["vendor_bank_branch"] ?>" id="vendorBankBranchName_<?= $invRowNo ?>" class="vendorBankBranchName">
                                            <input type="hidden" value="<?= $invoiceData["grnIvCode"] ?>" id="vendorGrnIvCode_<?= $invRowNo ?>" class="vendorGrnIvCode">

                                            <td><?= $invoiceData['grnIvCode'] ?? "<span class='text-danger'>Not Found!</span>"; ?></td>
                                            <td>
                                                <p class="pre-wrap"><?= $invoiceData['vendorName'] ?></p>
                                            </td>
                                            <td><span class="text-uppercase <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                                            <td>
                                                <?= $invoiceData['dueDate'] ?>
                                            </td>

                                            <td>
                                                <div class="input-group input-group-sm m-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text spanInvoiceCurrencyName" id="spanInvoiceCurrencyName_<?= $invRowNo ?>"><?= $invoiceData["currency_name"] ?></span>
                                                    </div>
                                                    <input type="number" class="form-control border py-3 text-right inputInvoiceAmt" id="inputInvoiceAmt_<?= $invRowNo ?>" value="<?= $invoiceData['grnTotalAmount'] * $currentConverstionRate ?>" placeholder="0.00" readonly>
                                                </div>
                                                <span class="text-small spanInvoiceAmt" id="spanInvoiceAmt_<?= $invRowNo ?>"></span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm m-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><?= $invoiceData["currency_name"] ?></span>
                                                    </div>
                                                    <input type="number" class="form-control border py-3 text-right inputInvoicePayableAmt" id="inputInvoicePayableAmt_<?= $invRowNo ?>" value="<?= $invoiceData['dueAmt'] * $currentConverstionRate ?>" placeholder="0.00" readonly>
                                                </div>
                                                <span class="text-small spanInvoicePayableAmt" id="spanInvoicePayableAmt_<?= $invRowNo ?>"></span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm m-0">
                                                    <?php if ($invoiceData['dueAmt'] <= 0) { ?>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">✅</span>
                                                        </div>
                                                        <input readonly type="number" class="form-control border py-3" style="background-color: #c6e5d4 !important;" placeholder="No Due Payment">
                                                    <?php } else { ?>
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><?= $invoiceData["currency_name"] ?></span>
                                                        </div>
                                                        <input type="number" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][recAmt]" class="form-control border py-3 text-right inputInvoicePayAmt" id="inputInvoicePayAmt_<?= $invRowNo ?>" value="<?= $invoiceData['dueAmt'] * $currentConverstionRate ?>" placeholder="Enter amount">
                                                    <?php } ?>
                                                </div>
                                                <span class="text-small spanInvoicePayAmt" id="spanInvoicePayAmt_<?= $invRowNo ?>"></span>
                                                <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][paymentINR]" id="hiddenInvoicePayAmt_<?= $invRowNo ?>" value="0">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm m-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><?= $invoiceData["currency_name"] ?></span>
                                                    </div>
                                                    <input type="number" step="any" id="inputInvoiceAdjustAmt_<?= $invRowNo ?>" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][paymentAdjustAMT]" class="form-control border py-3 text-right inputInvoiceAdjustAmt" placeholder="0.00" readonly>
                                                </div>
                                                <span id="spanInvoiceAdjustAmt_<?= $invRowNo ?>" class="text-small spanInvoiceAdjustAmt"></span>
                                                <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][paymentAdjustINR]" id="hiddenInvoicePayAmtAdjust_<?= $invRowNo ?>" value="0">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm m-0">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><?= $invoiceData["currency_name"] ?></span>
                                                    </div>
                                                    <input type="number" step="any" id="inputInvoiceRemainAmt_<?= $invRowNo ?>" class="form-control border py-3 text-right inputInvoiceRemainAmt" placeholder="0.00" readonly>
                                                </div>
                                                <span id="spanInvoiceRemainAmt_<?= $invRowNo ?>" class="text-small spanInvoiceRemainAmt"></span>
                                                <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][paymentRemainINR]" id="hiddenInvoicePayAmtRemain_<?= $invRowNo ?>" value="0">
                                            </td>
                                            <td>
                                                <a style="cursor:pointer" data-toggle="modal" data-target="#paymentActionModal_<?= $invRowNo ?>" class="btn btn-sm">
                                                    <i class="fa fa-cog po-list-icon"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <div class="modal fade right customer-modal classic-view-modal" id="paymentActionModal_<?= $invRowNo ?>" role="dialog" data-backdrop="true" aria-hidden="true">
                                            <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document" style="max-width: 30%;">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div class="text-light text-nowrap">
                                                            <p class="text-sm my-2"><?= $invoiceData['vendorName'] ?></p>
                                                            <p class="text-xs my-2"><span class="text-muted">GRN IV CODE:</span> <?= $invoiceData['grnIvCode'] ?></p>
                                                            <p class="text-xs my-2"><span class="text-muted">GRN Total:</span> <?= $invoiceData['currency_name'] . " " . number_format($invoiceData['grnTotalAmount'] * $currentConverstionRate, 2) ?> </p>
                                                            <p class="text-xs my-2"><span class="text-muted">Remaining Amt:</span> <?= $invoiceData['currency_name'] . " <span id='modalRemainAmt_" . $invRowNo . "'> " . number_format("0.00", 2) ?></span> <span class="text-muted"></p>
                                                            <p class="text-xs my-2"><span class="text-muted">Total Payable:</span> <?= $invoiceData['currency_name'] . " " . number_format($invoiceData['dueAmt'] * $currentConverstionRate, 2) ?></p>
                                                            <!-- <p class="text-xs my-2"><span class="text-muted">Total TDS:</span> <?= $invoiceData['currency_name'] . " " . decimalValuePreview($invoiceData['grnTotalTds']) ?></p> -->
                                                        </div>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Round Off</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><?= $companyCurrencyData["currency_name"] ?></span>
                                                                        </div>
                                                                        <select class="form-control inputRoundOffSign adjustmentInputSign" id="inputRoundOffSign_<?= $invRowNo ?>">
                                                                            <option value="+"> + </option>
                                                                            <option value="-"> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" step="any" id="inputRoundOffInr_<?= $invRowNo ?>" class="form-control border py-3 text-right inputRoundOffInr adjustmentInputValue" placeholder="0.00">
                                                                        <br><span class="text-small spanErrorAmtroundoff" id="spanErrorAmtroundoff_<?= $invRowNo ?>"></span>
                                                                        <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputRoundOffInrWithSign]" id="inputRoundOffInrWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputRoundOffInrWithSign" value="0.00">
                                                                        <input type="hidden" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputRoundOffWithSign]" id="inputRoundOffWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputRoundOffWithSign" value="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Write Back</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><?= $companyCurrencyData["currency_name"] ?></span>
                                                                        </div>
                                                                        <select id="inputWriteBackSign_<?= $invRowNo ?>" class="form-control inputWriteBackSign adjustmentInputSign">
                                                                            <option value="+"> + </option>
                                                                            <option value="-"> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" step="any" id="inputWriteBackInr_<?= $invRowNo ?>" class="form-control border py-3 text-right inputWriteBackInr adjustmentInputValue" placeholder="0.00">
                                                                        <br><span class="text-small spanErrorAmtWriteBack" id="spanErrorAmtWriteBack_<?= $invRowNo ?>"></span>
                                                                        <input type="hidden" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputWriteBackInrWithSign]" id="inputWriteBackInrWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputWriteBackInrWithSign" value="0.00">
                                                                        <input type="hidden" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputWriteBackWithSign]" id="inputWriteBackWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputWriteBackWithSign" value="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Financial Charges</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><?= $companyCurrencyData["currency_name"] ?></span>
                                                                        </div>
                                                                        <select id="inputFinancialChargesSign_<?= $invRowNo ?>" class="form-control inputFinancialChargesSign adjustmentInputSign">
                                                                            <option value="+"> + </option>
                                                                            <option value="-"> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" step="any" id="inputFinancialChargesInr_<?= $invRowNo ?>" class="form-control border py-3 text-right inputFinancialChargesInr adjustmentInputValue" placeholder="0.00">
                                                                        <br><span class="text-small spanErrorAmtFinancialCharges" id="spanErrorAmtFinancialCharges_<?= $invRowNo ?>"></span>
                                                                        <input type="hidden" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputFinancialChargesInrWithSign]" id="inputFinancialChargesInrWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputFinancialChargesInrWithSign" value="0.00">
                                                                        <input type="hidden" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputFinancialChargesWithSign]" id="inputFinancialChargesWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputFinancialChargesWithSign" value="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Forex Loss/Gain</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><?= $companyCurrencyData["currency_name"] ?></span>
                                                                        </div>
                                                                        <select id="inputForexLossGainSign_<?= $invRowNo ?>" class="form-control inputForexLossGainSign">
                                                                            <option value="+"> + </option>
                                                                            <option value="-"> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" step="any" id="inputForexLossGainInr_<?= $invRowNo ?>" class="form-control border py-3 text-right inputForexLossGainInr" placeholder="0.00">
                                                                        <br><span class="text-small spanErrorAmtForexLossGain" id="spanErrorAmtForexLossGain_<?= $invRowNo ?>"></span>
                                                                        <input type="hidden" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputForexLossGainInrWithSign]" id="inputForexLossGainInrWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputForexLossGainInrWithSign" value="0.00">
                                                                        <input type="hidden" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputForexLossGainWithSign]" id="inputForexLossGainWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputForexLossGainWithSign" value="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card mb-3">
                                                            <div class="card-header py-1 text-light">Total TDS</div>
                                                            <div class="card-body py-1">
                                                                <div class="d-flex gap-2 m-0 p-0">
                                                                    <div class="input-group input-group-sm w-50">
                                                                        <div class="input-group-prepend">
                                                                            <span class="input-group-text"><?= $companyCurrencyData["currency_name"] ?></span>
                                                                        </div>
                                                                        <select id="inputTotalTdsSign_<?= $invRowNo ?>" class="form-control inputTotalTdsSign">
                                                                            <option value="+"> + </option>
                                                                            <option value="-" selected="selected"> - </option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <input type="number" step="any" id="inputinputTotalTdsInr_<?= $invRowNo ?>" class="form-control border py-3 text-right inputinputTotalTdsInr" placeholder="0.00">
                                                                        <br><span class="text-small spanErrorAmtTOtalTds" id="spanErrorAmtTOtalTds_<?= $invRowNo ?>"></span>
                                                                        <input type="hidden" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputTotalTdsWithSign]" id="inputTotalTdsInrWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputForexLossGainInrWithSign" value="0.00">
                                                                        <input type="hidden" step="any" name="paymentInvoiceDetails[<?= $vendor_id ?>][<?= $invRowNo ?>][inputTotalTdsWithSign]" id="inputTotalTdsWithSign_<?= $invRowNo ?>" class="form-control border py-3 text-right inputForexLossGainWithSign" value="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<?php
require_once("../common/footer.php");
?>
<script>
    $(document).ready(function() {

        function parseNumberWithDefault(value, defaultValue = 0) {
            let parsedValue = Number(value);
            if (isNaN(parsedValue) || !isFinite(parsedValue)) {
                parsedValue = defaultValue;
            }
            return parsedValue;
        }

        // function addAllAdjustedAmt(rowNo) {
        //     // let companyCurrencyName = $(`#inputCompanyCurrencyName_${rowNo}`).val();
        //     // let invoiceCurrencyName = $(`#inputInvoiceCurrencyName_${rowNo}`).val();
        //     // let previousCurrencyRate = $(`#inputPreviousCurrencyRate_${rowNo}`).val();
        //     // let currentCurrencyRate = $(`#inputCurrentCurrencyRate_${rowNo}`).val();

        //     // let round_off = parseFloat($(`#inputRoundOffWithSign_${rowNo}`).val());
        //     // let round_off_inr = parseFloat($(`#inputRoundOffInr_${rowNo}`).val());
        //     // let write_back = parseFloat($(`#inputWriteBackWithSign_${rowNo}`).val());
        //     // let write_back_inr = parseFloat($(`#inputWriteBackInr_${rowNo}`).val());
        //     // let fin_charge = parseFloat($(`#inputFinancialChargesWithSign_${rowNo}`).val());
        //     // let fin_charge_inr = parseFloat($(`#inputFinancialChargesInr_${rowNo}`).val());
        //     // let forex = parseFloat($(`#inputForexLossGainWithSign_${rowNo}`).val());
        //     // let forex_inr = parseFloat($(`#inputForexLossGainInr_${rowNo}`).val());

        //     // let total = round_off + write_back + fin_charge + forex;
        //     // let total_inr = round_off_inr + write_back_inr + fin_charge_inr + forex_inr;

        //     // console.log(`total: ${total}, total_inr: ${total_inr}`);

        //     // $(`#hiddenInvoicePayAmtAdjust_${rowNo}`).val(total_inr.toFixed(2));
        //     // if (companyCurrencyName != invoiceCurrencyName) {
        //     //     $(`#spanInvoiceAdjustAmt_${rowNo}`).html(total_inr.toFixed(2));
        //     // }
        //     // $(`#inputInvoiceAdjustAmt_${rowNo}`).val(total.toFixed(2));
        // }


        function updateAdjustmentAmount(rowNo) {

            let companyCurrencyName = $(`#inputCompanyCurrencyName_${rowNo}`).val();
            let invoiceCurrencyName = $(`#inputInvoiceCurrencyName_${rowNo}`).val();
            let previousCurrencyRate = $(`#inputPreviousCurrencyRate_${rowNo}`).val();
            let currentCurrencyRate = $(`#inputCurrentCurrencyRate_${rowNo}`).val();

            let inputInvoicePayableAmt = parseNumberWithDefault($(`#inputInvoicePayableAmt_${rowNo}`).val());
            let inputInvoicePayAmt = parseNumberWithDefault($(`#inputInvoicePayAmt_${rowNo}`).val());
            // let inputInvoiceRemainAmt = parseNumberWithDefault($(`#inputInvoiceRemainAmt_${rowNo}`).val());
            let inputInvoiceRemainAmt = inputInvoicePayableAmt - inputInvoicePayAmt;
            let inputInvoiceRemainAmtInr = inputInvoiceRemainAmt / currentCurrencyRate;

            let round_off_sign = $(`#inputRoundOffSign_${rowNo}`).val();
            let round_off_value_inr = parseNumberWithDefault($(`#inputRoundOffInr_${rowNo}`).val());
            let write_back_sign = $(`#inputWriteBackSign_${rowNo}`).val();
            let write_back_value_inr = parseNumberWithDefault($(`#inputWriteBackInr_${rowNo}`).val());
            let fin_charge_sign = $(`#inputFinancialChargesSign_${rowNo}`).val();
            let fin_charge_value_inr = parseNumberWithDefault($(`#inputFinancialChargesInr_${rowNo}`).val());
            // let forex_sign = $(`#inputForexLossGainSign_${rowNo}`).val();
            // let forex_value_inr = parseNumberWithDefault($(`#inputForexLossGainInr_${rowNo}`).val());


            let round_off_value_inr_with_sign = round_off_sign == "+" ? round_off_value_inr : round_off_value_inr * -1;
            let write_back_value_inr_with_sign = write_back_sign == "+" ? write_back_value_inr : write_back_value_inr * -1;
            let fin_charge_value_inr_with_sign = fin_charge_sign == "+" ? fin_charge_value_inr : fin_charge_value_inr * -1;
            // let forex_value_inr_with_sign = fin_charge_sign == "+" ? forex_value_inr : forex_value_inr * -1;

            let round_off_value_with_sign = round_off_value_inr_with_sign * currentCurrencyRate;
            let write_back_value_with_sign = write_back_value_inr_with_sign * currentCurrencyRate;
            let fin_charge_value_with_sign = fin_charge_value_inr_with_sign * currentCurrencyRate;
            // let forex_value_with_sign = forex_value_inr_with_sign * currentCurrencyRate;

            $(`#inputRoundOffInrWithSign_${rowNo}`).val(round_off_value_inr_with_sign);
            $(`#inputRoundOffWithSign_${rowNo}`).val(round_off_value_with_sign);

            $(`#inputWriteBackInrWithSign_${rowNo}`).val(write_back_value_inr_with_sign);
            $(`#inputWriteBackWithSign_${rowNo}`).val(write_back_value_with_sign);

            $(`#inputFinancialChargesInrWithSign_${rowNo}`).val(fin_charge_value_inr_with_sign);
            $(`#inputFinancialChargesWithSign_${rowNo}`).val(fin_charge_value_with_sign);

            // $(`#inputForexLossGainInrWithSign_${rowNo}`).val(forex_value_inr_with_sign);
            // $(`#inputForexLossGainWithSign_${rowNo}`).val(forex_value_with_sign);

            // let totalAdjustedAmount = forex_value_with_sign+fin_charge_value_with_sign+write_back_value_with_sign+round_off_value_with_sign;
            let totalAdjustedAmount = fin_charge_value_with_sign + write_back_value_with_sign + round_off_value_with_sign;
            // let totalAdjustedAmountInr = forex_value_inr+fin_charge_value_inr+write_back_value_inr+round_off_value_inr;
            let totalAdjustedAmountInr = fin_charge_value_inr_with_sign + write_back_value_inr_with_sign + round_off_value_inr_with_sign;

            $(`#hiddenInvoicePayAmtAdjust_${rowNo}`).val(totalAdjustedAmountInr.toFixed(2));
            if (companyCurrencyName != invoiceCurrencyName) {
                $(`#spanInvoiceAdjustAmt_${rowNo}`).html(`${companyCurrencyName}: ${totalAdjustedAmountInr.toFixed(2)}`);
            }
            $(`#inputInvoiceAdjustAmt_${rowNo}`).val(totalAdjustedAmount.toFixed(2));

            console.log()
            $(`#inputInvoiceRemainAmt_${rowNo}`).val((inputInvoiceRemainAmt + totalAdjustedAmount).toFixed(2));
            $(`#modalRemainAmt_${rowNo}`).html((inputInvoiceRemainAmt + totalAdjustedAmount).toFixed(2));
            $(`#hiddenInvoicePayAmtRemain_${rowNo}`).val((inputInvoiceRemainAmtInr + totalAdjustedAmountInr).toFixed(2));

        }


        $(document).on("keyup", ".adjustmentInputValue", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            updateAdjustmentAmount(rowNo);
        });
        $(document).on("change", ".adjustmentInputSign", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            updateAdjustmentAmount(rowNo);
        });

        function calculateVendorPaymentForm() {
            console.log("Updating all fields");
            let grandTotalRemainAmt = 0;
            let grandTotalPayAmt = 0;
            //reading each row
            $(".inputInvoicePayAmt").each(function() {

                let invRowNo = ($(this).attr("id")).split("_")[1];

                let companyCurrencyName = $(`#inputCompanyCurrencyName_${invRowNo}`).val();
                let invoiceCurrencyName = $(`#inputInvoiceCurrencyName_${invRowNo}`).val();
                let previousCurrencyRate = $(`#inputPreviousCurrencyRate_${invRowNo}`).val();
                let currentCurrencyRate = $(`#inputCurrentCurrencyRate_${invRowNo}`).val();

                let invAmt = parseNumberWithDefault($(`#inputInvoiceAmt_${invRowNo}`).val(), 0);
                let invPayableAmt = parseNumberWithDefault($(`#inputInvoicePayableAmt_${invRowNo}`).val(), 0);
                let invPayAmt = parseNumberWithDefault($(`#inputInvoicePayAmt_${invRowNo}`).val(), 0);

                let invAdjustAmt = parseNumberWithDefault($(`#inputInvoiceAdjustAmt_${invRowNo}`).val(), 0);
                // let invINRPayAmt = parseNumberWithDefault($(`#hiddenInvoicePayAmt_${invRowNo}`).val(), 0);
                let invRemainAmt = (invPayableAmt - invPayAmt) + invAdjustAmt;
                let invINRPayAmt = parseNumberWithDefault(invPayAmt / currentCurrencyRate, 0);
                let invINRPayaybleAmt = parseNumberWithDefault(invPayableAmt / currentCurrencyRate, 0);
                let invINRPayAmtRemain = parseNumberWithDefault((invRemainAmt / currentCurrencyRate), 0);

                if (invPayAmt > invPayableAmt) {
                    $(`#spanInvoicePayAmt_${invRowNo}`).html(`<span class="text-danger">Amount exist!</span>`);
                    $(`#inputInvoicePayAmt_${invRowNo}`).val(0);
                    $(`#inputInvoiceRemainAmt_${invRowNo}`).val(invPayableAmt);
                    $(`#modalRemainAmt_${invRowNo}`).html(invPayableAmt);
                    invPayAmt = 0;
                    invINRPayAmt = 0;
                    invRemainAmt = invPayableAmt;
                    invINRPayAmtRemain = invINRPayaybleAmt;
                } else {
                    $(`#spanInvoicePayAmt_${invRowNo}`).html('');
                    $(`#inputInvoiceRemainAmt_${invRowNo}`).val(invRemainAmt.toFixed(2));
                    $(`#modalRemainAmt_${invRowNo}`).html(invRemainAmt.toFixed(2));
                }
                grandTotalPayAmt += invINRPayAmt;
                grandTotalRemainAmt += invINRPayAmtRemain;

                //Calculating the round of, write back and forex loss gain.
                if (companyCurrencyName == invoiceCurrencyName) {
                    // inputRoundOff_
                    // inputRoundOffSign_
                    // inputWriteBack_
                    // inputWriteBackSign_
                    // inputFinancialCharges_
                    // inputFinancialChargesSign_
                    // inputForexLossGain_
                    // inputForexLossGainSign_

                    // spanInvoiceAmt_
                    // spanInvoicePayableAmt_
                    // spanInvoicePayAmt_
                    // spanInvoiceRemainAmt_
                    $(`#inputForexLossGain_${invRowNo}`).prop("disabled", true);
                    $(`#inputForexLossGainSign_${invRowNo}`).prop("disabled", true);
                    $(`#hiddenInvoicePayAmt_${invRowNo}`).val(`${invINRPayAmt}`);
                    $(`#hiddenInvoicePayAmtRemain_${invRowNo}`).val(`${invINRPayAmtRemain}`);

                    if (invRemainAmt > 0 && invRemainAmt < 1) {
                        $(`#inputRoundOff_${invRowNo}`).val(invRemainAmt.toFixed(2));
                        $(`#inputRoundOffSign_${invRowNo}`).val("+");
                    }

                } else {
                    $(`#inputForexLossGain_${invRowNo}`).prop("disabled", false);
                    $(`#inputForexLossGainSign_${invRowNo}`).prop("disabled", false);

                    $(`#spanInvoiceAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(invAmt/previousCurrencyRate).toFixed(2)}`);
                    $(`#spanInvoicePayableAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(invPayableAmt/previousCurrencyRate).toFixed(2)}`);
                    $(`#spanInvoicePayAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(invINRPayAmt).toFixed(2)}`);
                    $(`#spanInvoiceRemainAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(invINRPayAmtRemain).toFixed(2)}`);

                    $(`#hiddenInvoicePayAmt_${invRowNo}`).val(`${invINRPayAmt}`);
                    $(`#hiddenInvoicePayAmtRemain_${invRowNo}`).val(`${invINRPayAmtRemain}`);

                    if (previousCurrencyRate != currentCurrencyRate) {
                        let forexLossGainAmt = (invPayAmt / currentCurrencyRate) - (invPayAmt / previousCurrencyRate);
                        $(`#inputForexLossGain_${invRowNo}`).val(Math.abs(forexLossGainAmt).toFixed(2));
                        $(`#inputForexLossGainInr_${invRowNo}`).val(Math.abs(forexLossGainAmt).toFixed(2));
                        $(`#inputForexLossGainInrWithSign_${invRowNo}`).val(Math.abs(forexLossGainAmt).toFixed(2));
                        let forex_other_currency = forexLossGainAmt * currentCurrencyRate;
                        $(`#inputForexLossGainWithSign_${invRowNo}`).val(Math.abs(forex_other_currency).toFixed(2));
                        if (forexLossGainAmt > 0) {
                            //loss
                            $(`#inputForexLossGainSign_${invRowNo}`).val("-");
                            // $(`#inputInvoiceAdjustAmt_${invRowNo}`).val(in_current_currency_adj * (-1));
                            // $(`#hiddenInvoicePayAmtAdjust_${invRowNo}`).val(forexLossGainAmt * (-1));
                            // $(`#spanInvoiceAdjustAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(forexLossGainAmt * (-1)).toFixed(2)}`);

                        } else if (forexLossGainAmt < 0) {
                            //gain
                            $(`#inputForexLossGainSign_${invRowNo}`).val("+");
                            // $(`#inputInvoiceAdjustAmt_${invRowNo}`).val(in_current_currency_adj);
                            // $(`#hiddenInvoicePayAmtAdjust_${invRowNo}`).val(forexLossGainAmt);
                            // $(`#spanInvoiceAdjustAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(forexLossGainAmt).toFixed(2)}`);
                        } else {
                            $(`#inputForexLossGain_${invRowNo}`).prop("disabled", true);
                            $(`#inputForexLossGainSign_${invRowNo}`).prop("disabled", true);
                        }
                    }
                }

            });

            console.log(grandTotalPayAmt.toFixed(2), grandTotalRemainAmt);
            $(`#inputTotalPaymentAmount`).val(grandTotalPayAmt.toFixed(2));
            $(`#inputTotalRemainAmount`).val(grandTotalRemainAmt.toFixed(2));
        }
        calculateVendorPaymentForm();

        $(document).on('change', '#paymentDetailsBankDropDown', function() {
            let isAllPayCurrencyINR = true;
            const bankId = parseNumberWithDefault($(this).val(), 0);
            const isIciciCibEnabled = parseInt($(this).find(':selected').data('is-icici-cib-enabled'));
            $(".spanInvoiceCurrencyName").each(function() {
                let invCurrencyName = $(this).html().trim();
                if (invCurrencyName != "INR") {
                    isAllPayCurrencyINR = false;
                }
            });


            $(`#paymentDetailsDiv`).html(`<div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="image-input">
                                                        <input type="file" name="paymentDetails[paymentAdviceImg]" accept="image/*" id="imageInput">
                                                        <label for="imageInput" class="image-button"><i class="fa fa-image po-list-icon mr-2"></i> Upload Payment Advice</label>
                                                        <img src="" class="image-preview">
                                                        <span class="change-image float-right mt-3"><button class=" btn btn-danger"><i class="fa fa-times mr-2"></i>Remove</button></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="form-input totalamount">
                                                        <label for="">Document Date</label>
                                                        <input type="date" name="paymentDetails[documentDate]" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="form-input totaldueamount">
                                                        <label for="">Posting Date</label>
                                                        <input type="date" name="paymentDetails[postingDate]" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <div class="form-input totaloverdue">
                                                        <label for="">Transaction Id / Doc. No.</label>
                                                        <input type="text" placeholder="Tnx. Id / Doc. No." name="paymentDetails[tnxDocNo]" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 col-md-12 col-sm-12">
                                                    <div class="form-input totaloverdue">
                                                        <label for="">Remarks</label>
                                                        <textarea name="paymentDetails[remarks]" cols="10" rows="3" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>`);

            $(`#submitPaymentFormBtn`).prop('disabled', false);
            if (bankId > 0 && isIciciCibEnabled === 1) {
                if (isAllPayCurrencyINR) {

                    $(`#submitPaymentFormBtn`).prop('disabled', true);
                    console.log("Hello payment banks! ", bankId, isIciciCibEnabled);

                    $(`#paymentDetailsDiv`).html(`
                        <div class="row mt-3">
                            <p>ICICI CIB Payment</p>
                            <div id="iciciCibDiv" class="col-lg-12 col-md-12 col-sm-12 text-center">
                                <div id="iciciCibInitiateOtpDiv">
                                    <button type="button" class="btn btn-sm btn-primary" id="iciciCibInitiateOtp">Initiate Otp</button>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    console.log("You cannot pay by our integreted banking system for this payment! only INR payment is supported!");
                }
            }
        });

        $(document).on("keyup", ".inputInvoicePayAmt", function() {
            let inputInvoicePayAmount = parseNumberWithDefault($(this).val(), 0);
            console.log("Pay amount: ", inputInvoicePayAmount);
            calculateVendorPaymentForm();
        });

        function validateVendorPaymentForm() {
            let isValidated = true;
            $(`#paymentDetailsBankDropDownSpan`).html("");
            if (parseNumberWithDefault($("#paymentDetailsBankDropDown").val(), 0) < 1) {
                isValidated = false;
                $(`#paymentDetailsBankDropDownSpan`).html("Please select bank details!");
            }

            $(`#spanTotalPaymentAmount`).html("");
            if (parseNumberWithDefault($("#inputTotalPaymentAmount").val(), 0) < 1) {
                isValidated = false;
                $(`#spanTotalPaymentAmount`).html("Pay amount can't be zero or less!");
            }
            console.log("Validating the form...");
            return isValidated;
        }

        $(document).on("submit", "#vendorPaymentForm", function(e) {
            if (!validateVendorPaymentForm()) {
                e.preventDefault();
                console.log("Validating failed! Try again...");
            }
        });

        $(document).on("click", "#iciciCibInitiateOtp", function(e) {
            let inputTotalPaymentAmount = parseNumberWithDefault($(`#inputTotalPaymentAmount`).val(), 0);
            let bankTnxId = `T<?= time() . rand(100, 999) ?>`;
            // getting bank details
            let bankTnxTotalAmount = 0;
            let bankAccDetails = {
                withinBankData: [],
                otherBankData: []
            };

            $(".inputInvoicePayAmt").each(function() {
                let invRowNo = ($(this).attr("id")).split("_")[1];
                let vendorBankName = $(`#vendorBankName_${invRowNo}`).val();
                // let vendorPayAmount = parseNumberWithDefault($(`#inputInvoicePayAmt_${invRowNo}`).val(), 0);
                let vendorPayAmount = parseNumberWithDefault($(`#hiddenInvoicePayAmt_${invRowNo}`).val(), 0);
                let vendorPaymentInfo = {
                    ACCOUNT_ID: $(`#vendorBankAccNumber_${invRowNo}`).val(),
                    ACCOUNT_IFSC: $(`#vendorBankIFSC_${invRowNo}`).val(),
                    PAYEE_NAME: $(`#vendorBankAccHolderName_${invRowNo}`).val(),
                    AMOUNT: vendorPayAmount,
                    REMARKS: $(`#vendorGrnIvCode_${invRowNo}`).val(),
                };
                bankTnxTotalAmount += vendorPayAmount;
                if (vendorBankName === 'ICICI Bank') {
                    bankAccDetails.withinBankData.push(vendorPaymentInfo);
                } else {
                    bankAccDetails.otherBankData.push(vendorPaymentInfo);
                }
            });

            // send OTP with bankTnxId and bankTnxTotalAmount
            console.log("Initiating payment...");
            if (bankTnxTotalAmount === inputTotalPaymentAmount) {
                console.log("Bank amount verified!");
                console.log(bankTnxId);
                console.log(bankTnxTotalAmount);
                console.log(bankAccDetails);
            }

            $(`#paymentDetailsDiv`).html(`
                <div class="row mt-3">
                    <p>ICICI CIB Payment</p>
                    <div id="iciciCibDiv" class="col-lg-12 col-md-12 col-sm-12 text-center">
                        <div id="iciciCibVerifyOtpDiv">
                            <input type="text"  placeholder="Enter OTP Here!" id="iciciCibVerifyOtpInput" class="form-control col-lg-6 col-md-12 col-sm-12 ml-auto mr-auto">
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="iciciCibVerifyOtpBtn">Verify Otp</button>
                            <input type="hidden" name="paymentDetails[tnxDocNo]" value="${bankTnxId}" class="form-control">
                            <input type="hidden" name="paymentDetails[postingDate]" value="<?= date("Y-m-d") ?>" class="form-control">
                            <input type="hidden" name="paymentDetails[documentDate]" value="<?= date("Y-m-d") ?>" class="form-control">
                        </div>
                    </div>
                </div>
            `);
        });

        $(document).on("click", "#iciciCibVerifyOtpBtn", function(e) {
            let iciciCibVerifyOtp = parseNumberWithDefault($(`#iciciCibVerifyOtpInput`).val(), 0);
            if (iciciCibVerifyOtp >= 100000) {
                console.log("Verify the Otp and complete the Tnx!", iciciCibVerifyOtp);
                // Calling verify api for ICICI CIB
                // Then submit the payment request
                $(`#vendorPaymentForm`).submit();
            } else {
                console.log("Invalid Otp!");
            }
        });
    })
</script>