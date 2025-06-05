<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");

// Add Functions
require_once("../../app/v1/functions/branch/func-customers.php");
require_once("../../app/v1/functions/branch/func-journal.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/admin/func-company.php");
include_once("../../app/v1/functions/branch/func-brunch-so-controller.php");


$dbObj = new Database();
$accountObj = new Accounting();
$soObj = new BranchSo();


if (isset($_POST['act'])) {

    //************************START ACCOUNTING ******************** */
    // console($_POST);
    // exit();
    //-----------------------------payment ACC Start ----------------
    $postingDate = $_POST['invoicePostingDate'] ?? date("Y-m-d");
    $grnPostingJournalId = $ivPostingData["grnDetails"]["grnPostingJournalId"];
    $grnId = $ivPostingData["grnDetails"]["grnId"];
    $tnxDocNo = $_POST['BasicDetails']['documentNo'];
    $documentDate = $_POST['BasicDetails']['documentDate'];
    $invoiceConcadinate = $_POST['invoiceConcadinate'];
    $collectionInputData = [
        "BasicDetails" => [
            "documentNo" => $tnxDocNo, // Invoice Doc Number
            "documentDate" => $documentDate, // Invoice number
            "postingDate" =>  $postingDate, // current date
            "reference" => $tnxDocNo, // T code
            "remarks" => "Payment collection for - " . $invoiceConcadinate,
            "journalEntryReference" => "Collection"
        ],
        "paymentDetails" => $_POST['paymentDetails'],
    ];

    $paymentId = $_POST['payId'];
    $check_JI = queryGet("SELECT * FROM `erp_acc_journal` WHERE `parent_id` LIKE '" . $paymentId . "' AND `parent_slug` LIKE 'Collection' AND `refarenceCode` LIKE '" . $tnxDocNo . "'");
    if ($check_JI['status'] == 'success') {
        $journalId = $check_JI['data']['id'];
        $sqlcollection = "UPDATE `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "`
                    SET
                        `journal_id`=$journalId 
                    WHERE `payment_id`='$paymentId'  ";
        $sqlcollectionObj = queryUpdate($sqlcollection);
        if ($sqlcollectionObj['status'] == 'success') {
            swalAlert("success", 'Success', "Colection Accounting Posted Successfully", 'failed-accounting-collectPayment.php');
        } else {
            swalAlert("warning", 'Failed', "Accounting Posting Failed!", 'failed-accounting-collectPayment.php');
        }
    } else {
        $collectionObj = $soObj->multicollectionAccountingPosting($collectionInputData, "Collection", $paymentId);
        if ($collectionObj['status'] == 'success') {
            $accountingPosting = reset($collectionObj['accountingPosting']); // reset() gets the first element
            // Get journalId
            // console($accountingPosting);
            $journalId = $accountingPosting['journalId'];
            // console($journalId);
            $sqlcollection = "UPDATE `" . ERP_BRANCH_SALES_ORDER_PAYMENTS . "`
                    SET
                        `journal_id`=$journalId 
                    WHERE `payment_id`='$paymentId'  ";
            $sqlcollectionObj = queryUpdate($sqlcollection);
            // console($sqlcollectionObj);
            // exit();

            if ($sqlcollectionObj['status'] == 'success') {
                swalAlert("success", 'Success', "Colection Accounting Posted Successfully", 'failed-accounting-collectPayment.php');
            } else {
                swalAlert("warning", 'Failed', "Accounting Posting Failed!", 'failed-accounting-collectPayment.php');
            }
        } else {
            swalAlert("warning", 'Failed', "Accounting Posting Failed!", 'failed-accounting-collectPayment.php');
        }
    }


    // console($collectionObj);
    // exit();


}


if (isset($_GET['collect_id'])) {
    $decoded_pay_id = base64_decode($_GET['collect_id']);
}

// console($decoded_pay_id);

$cond = "AND sopayment.payment_id =" . $decoded_pay_id . "";

$sql_Mainqry = "SELECT sopayment.*,cust.customer_code , cust.trade_name as customer_name FROM `erp_branch_sales_order_payments` as sopayment  LEFT JOIN erp_customer as cust ON cust.customer_id=sopayment.customer_id WHERE 1 " . $cond . "  AND sopayment.company_id='" . $company_id . "'  AND sopayment.branch_id=" . $branch_id . " AND sopayment.location_id=" . $location_id . " AND (sopayment.journal_id=0 OR sopayment.journal_id IS NULL) ORDER BY sopayment.payment_id DESC ";


$sqlMainQryObj =  $dbObj->queryGet($sql_Mainqry);

$num_row = $sqlMainQryObj['numRows'];
$collectionMainData = $sqlMainQryObj['data'];
$bank_id = $collectionMainData['bank_id'];
// console($collectionMainData);


$collect_payment = $collectionMainData['collect_payment'];
$customer_id = $collectionMainData['customer_id'];
$fetchcustDetailsObj = $soObj->fetchCustomerDetails($customer_id);
$fetchcustDetails = $fetchcustDetailsObj['data'][0];
// console($fetchcustDetails);

// $sql_Mainqry_paylogObj = $grnObj->fetchPaymentLogDetails($decoded_pay_id);
$sql_Mainqry_paylogObj = $dbObj->queryGet("SELECT * FROM  `erp_branch_sales_order_payments_log` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND payment_id='$decoded_pay_id' AND  status!='deleted' AND payment_type='pay'", true);

$total_log_payAmt = 0;
foreach ($sql_Mainqry_paylogObj['data'] as $index => $oneLog) {
    $total_log_payAmt += $oneLog['payment_amt'];
}


$type = "Collection";
$collectionDebitCreditAccListObj =  $accountObj->getCreditDebitAccountsList($type);

if ($collectionDebitCreditAccListObj["status"] != "success") {
    return [
        "status" => "warning",
        "message" => "Collection Debit & Credit Account list is not available"
    ];
    die();
}

$paymentDebitAccList = $collectionDebitCreditAccListObj["debitAccountsList"];
$paymentCreditAccList = $collectionDebitCreditAccListObj["creditAccountsList"];
// console($collectionDebitCreditAccListObj);
// console($sql_Mainqry_paylogObj);

$accMapp = getAllfetchAccountingMappingTbl($company_id);

$roundOffGL = $accMapp['data']['0']['roundoff_gl'];

$roundOff = getChartOfAccountsDataDetails($roundOffGL)['data'];
$roundoffGlCode = $roundOff['gl_code'];
$roundoffGlName = $roundOff['gl_label'];


$postingDate = $collectionMainData['postingDate'];
$date_msg = '';
if (new DateTime(date("Y-m-d", strtotime($compOpeningDate))) > new DateTime(date("Y-m-d", strtotime($collectionMainData['postingDate'] ?? "")))) {
    $postingDate = $compOpeningDate;
    $date_msg = "Collection Posting Date changed by Company Openings date.";
}

$bankParentglSql = "SELECT parent_gl FROM  `erp_acc_bank_cash_accounts` WHERE company_id=" . $company_id . " AND id=" . $bank_id;
$bankParentglSqlObj = $dbObj->queryGet($bankParentglSql);

$getparentgLInfosql = "SELECT * FROM `erp_acc_coa_" . $company_id . "_table` WHERE company_id=" . $company_id . " AND id =" .   $bankParentglSqlObj['data']['parent_gl'];

$getparentgLInfosqlObj = $dbObj->queryGet($getparentgLInfosql);

// console($getparentgLInfosqlObj);
$diffAmount = abs($collect_payment - $total_log_payAmt);

?>

<style>
    .is-failed-account-view .wrapper-account {
        background: #fff;
        margin: 17px;
        padding: 10px 15px;
        border-radius: 7px;
        height: 93%;
        overflow: auto;
    }

    .is-failed-account-view .wrapper-account h2 {
        font-size: 0.8rem;
        text-align: right;
        color: #787878;
    }

    .is-failed-account-view .wrapper-account h2 ion-icon {
        position: relative;
        font-size: 1rem;
        top: 3px;
        margin-right: 5px;
        font-weight: 700;
    }

    .is-failed-account-view .wrapper-account h2 p {
        margin: 8px 0;
        color: #000;
        font-weight: 600;
        font-size: 0.82rem;
    }

    .is-failed-account-view .wrapper-account .account-list {
        position: relative;
    }

    .is-failed-account-view .wrapper-account .account-list label {
        position: absolute;
        top: -10px;
        left: 13px;
        background: #eaeaea;
        padding: 5px 15px;
        border-radius: 5px;
    }

    .is-failed-account-view .wrapper-account .account-list.credit-acc-list label {
        position: absolute;
        top: -10px;
        left: 13px;
        background: #c9e3c8;
        color: #168506;
        padding: 5px 15px;
        border-radius: 5px;
        border: 1px solid #c9e3c8;
    }

    .is-failed-account-view .wrapper-account .account-list.debit-acc-list label {
        position: absolute;
        top: -10px;
        left: 13px;
        background: #edcaca;
        color: #d52d00;
        padding: 5px 15px;
        border-radius: 5px;
        border: 1px solid #edcaca;
    }

    .is-failed-account-view .wrapper-account .account-list .card-border-area {
        border: 1px solid #eaeaea;
        border-radius: 9px;
        padding: 15px 5px;
        margin-bottom: 30px;
    }

    .is-failed-account-view .wrapper-account .account-list .card-border-area table tr th {
        background: #fff;
        color: #000;
        font-size: 0.7rem;
        font-weight: 600;
        border-bottom: 1px solid #eaeaea;
        padding: 12px 10px 15px;
    }

    .is-failed-account-view .wrapper-account .account-list .card-border-area table tr td {
        background: #fff;
        font-size: 0.75rem;
        padding: 2px 10px;
        border-bottom: 1px solid #ececec;
    }

    .is-failed-account-view .wrapper-account .account-list .card-border-area table tr:nth-child(odd) td {
        background: #f2f2f229;
    }

    .is-failed-account-view .wrapper-account .header-block {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #eaeaea;
        margin-bottom: 30px;
        position: sticky;
        top: -11px;
        background: #fff;
        z-index: 9;
        padding: 5px 0;
    }

    .is-failed-account-view .wrapper-account .account-amount {
        border-radius: 6px;
        border: 1px solid #eaeaea;
        margin-bottom: 10px;
        padding: 7px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .is-failed-account-view .wrapper-account .account-amount .card-border-area {
        display: flex;
        align-items: center;
        max-width: 100%;
        gap: 8px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .is-failed-account-view .wrapper-account .account-amount label {
        background: #eaeaea;
        padding: 5px 15px;
        border-radius: 5px;
        font-weight: 600;
        margin-bottom: 0;
        width: auto;
    }

    .is-failed-account-view .wrapper-account .account-amount .card-border-area select {
        padding: 3px;
        text-align: center;
        height: 26px;
        width: 44px;
        font-size: 0.9rem;
    }

    .is-failed-account-view .wrapper-account .account-amount .card-border-area input {
        padding: 3px;
        text-align: center;
        height: 26px;
        width: 50px;
        font-size: 0.75rem;
    }

    .is-failed-account-view .wrapper-account .account-list.credit-acc-list .card-border-area {
        border: 1px solid #03a50052;
    }

    .is-failed-account-view .wrapper-account .account-list.debit-acc-list .card-border-area {
        border: 1px solid #be000052;
    }

    .is-failed-account-view .paid-btn {
        display: flex;
        justify-content: center;
    }
</style>

<link rel="stylesheet" href="../../public/assets/stock-report-new.css">

<!-- Content Wrapper detailed-view -->
<div class="content-wrapper is-failed-account-view vitwo-alpha-global">

    <div class="container-fluid mt-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL; ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="failed-accounting-collectPayment.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed Accounting List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Accounting Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-collectPayment.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
    </div>

    <form method="post" action="">
        <input type="hidden" name="act">
        <input type="hidden" name="payId" value="<?= $decoded_pay_id ?>">
        <input type="hidden" name="BasicDetails[documentNo]" value="<?= $collectionMainData['transactionId']; ?>">
        <input type="hidden" name="BasicDetails[documentDate]" value="<?= $collectionMainData['documentDate']; ?>">
        <input type="hidden" name="BasicDetails[postingDate]" value="<?= $collectionMainData['postingDate']; ?>">
        <input type="hidden" name="BasicDetails[reference]" value="<?= $collectionMainData['transactionId']; ?>">
        <input type="hidden" name="BasicDetails[remarks]" value="<?= $collectionMainData['remarks']; ?>">
        <input type="hidden" name="BasicDetails[journalEntryReference]" value="Collection">
        <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id']  ?>][customerId]" value="<?= $collectionMainData['customer_id']; ?>">
        <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id']  ?>][customer_parentGlId]" value="<?= $fetchcustDetails['parentGlId'] ?>">
        <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id']  ?>][customer_code]" value="<?= $fetchcustDetails['customer_code']; ?>">
        <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id']  ?>][customer_name]" value="<?= $fetchcustDetails['trade_name']; ?>">
        <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id']  ?>][paymentId]" value="<?= $collectionMainData['payment_id']; ?>">
        <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id']  ?>][bankId]" value="<?= $collectionMainData['bank_id']; ?>">

        <div class="wrapper-account">
            <div class="header-block">

                <h2>Failed Payment Acconting For : <b><a href="#" class="soModal" data-id="<?= $decoded_pay_id ?>" data-toggle="modal" data-target="#viewGlobalModal"><?= $collectionMainData['transactionId'] ?></a></b>

                    <?php if (decimalValuePreview($total_log_payAmt) != decimalValuePreview($collect_payment)) {
                        // swalToast("warning", 'Reverse', "Amount Issue in this payment Id."); 
                    ?>
                        <span class="status-bg status-closed">Amount Issue in this payment Id.</span>
                    <?php } ?>
                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Posting Date : <p><?= formatDateWeb($postingDate); ?></p>
                </h2>
            </div>
            <div class="account-list dedit-acc-list">
                <label for="">Credit account list</label>
                <div class="card-border-area">
                    <table>
                        <thead>
                            <tr>
                                <th width="25%">Ledger</th>
                                <th>Sub Ledger</th>
                                <th class="text-right">Amount(INR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalAmount = 0;
                            $roundOfff = $collectionMainData['adjusted_amount'];
                            $totalcr = 0;
                            $totaldr = 0;
                            $customer_id = $collectionMainData['customer_id'];
                            $customerDetailsObj = queryGet("SELECT customer_code,parentGlId,customer_pan,customer_gstin,trade_name as customer_name,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customer_id'")['data'];

                            $customer_name = $customerDetailsObj['customer_name'];
                            // console($customerDetailsObj);
                            ?>

                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $paymentCreditAccList[0]['gl_code'] ?>||<?= $paymentCreditAccList[0]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        <?= $customerDetailsObj['customer_code'] ?>||<?= $customer_name ?>
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($collect_payment) ?></td>

                            </tr>

                            <?php
                            // }
                            $totalcr = $totalAmount;
                            if ($roundOfff > 0) {
                                $totalcr = $totalAmount + $roundOfff;
                            ?>

                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $roundoffGlCode; ?> || <?= $roundoffGlName ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            --
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo abs($roundOfff); ?></td>
                                    <input type="hidden" name="roundOffValue" value="<?= $roundOfff ?>">
                                </tr>
                            <?php } ?>

                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <b>Total</b>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right text-bold"><?php echo decimalValuePreview($collect_payment); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="account-list credit-acc-list">
                <label for="">Debit account list</label>
                <div class="card-border-area">
                    <table>
                        <thead>
                            <tr>
                                <th width="25%">Ledger</th>
                                <th>Sub Ledger</th>
                                <th class="text-right">Amount(INR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalAmount = 0;
                            $roundOfff = $invoiceMaindata['adjusted_amount'];
                            $totalcr = 0;
                            $totaldr = 0;
                            $pgiitem = [];
                            $invoiceConcadinate = '';
                            $bank_id = $collectionMainData['bank_id'];
                            $getBankDetails = $dbObj->queryGet("SELECT * FROM `erp_acc_bank_cash_accounts` WHERE `company_id`=$company_id AND `id`=$bank_id;")['data'];
                            // console($getBankDetails);

                            foreach ($sql_Mainqry_paylogObj['data'] as $index => $oneLog) {
                                $totalAmount += $oneLog['payment_amt'];

                                // Assuming getInvoiceDetails function gets invoice details similar to GRN details
                                // $invoiceDetails = getInvoiceDetails($oneLog['invoice_id']);
                                $invoiceNo = $invoiceDetails['data']['invoiceNo'];

                            ?>
                                <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id'] ?>][paymentItems][<?= $index ?>][invoice_id]" value="<?= $oneLog['invoice_id']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id'] ?>][paymentItems][<?= $index ?>][invoiceNo]" value="<?= $oneLog['invoiceNo']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id'] ?>][paymentItems][<?= $index ?>][roundoff]" value="<?= $oneLog['roundoff']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id'] ?>][paymentItems][<?= $index ?>][writeoff]" value="<?= $oneLog['writeoff']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id'] ?>][paymentItems][<?= $index ?>][financial_charge]" value="<?= $oneLog['financial_charge']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id'] ?>][paymentItems][<?= $index ?>][forex]" value="<?= $oneLog['forex']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id'] ?>][paymentItems][<?= $index ?>][tds]" value="<?= $oneLog['tds']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $collectionMainData['customer_id'] ?>][paymentItems][<?= $index ?>][recAmt]" value="<?= $oneLog['payment_amt']; ?>">

                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $getparentgLInfosqlObj['data']['gl_code']; ?> || <?= $getparentgLInfosqlObj['data']['gl_label']; ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $getBankDetails['account_no']; ?> || <?= $getBankDetails['bank_name']; ?>
                                        </p>
                                    </td>
                                    <td class="text-right"><?= decimalValuePreview($oneLog['payment_amt']); ?></td>
                                </tr>
                            <?php }

                            $totalcr = $totalAmount;
                            if ($roundOfff > 0) {
                                $totalcr = $totalAmount + $roundOfff;
                            ?>

                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?php echo $roundoffGlCode; ?> || <?php echo $roundoffGlName; ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            --
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo abs($roundOfff); ?></td>
                                    <input type="hidden" name="roundOffValue" value="<?php echo $roundOfff; ?>">
                                </tr>
                            <?php } ?>

                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <b>Total</b>
                                        <input type="hidden" name="invoiceConcadinate" value="<?= $invoiceConcadinate; ?>">

                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totalAmount); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="account-amount deffrence-amount">
                <label for="">Amount Difference</label>
                <div class="card-border-area">
                    <p><?= $diffAmount; ?></p>
                </div>
            </div>
            <?php if ($diffAmount != 0) { ?>
                <div class="account-amount adjust-amount">
                    <label for="">Extra Adjustment Amount</label>
                    <div class="card-border-area">
                        <!-- <select name="" id="" class="form-control" readonly>
                            <option value="+" <?php if ($diffAmount > 0) {
                                                    echo "selected";
                                                } ?>>+</option>
                            <option value="-"<?php if ($diffAmount < 0) {
                                                    echo "selected";
                                                } ?>>-</option>
                        </select> -->
                        <input type="text" name="diffrenceAdjAmount" class="form-control" value="<?= ($diffAmount); ?>" readonly>
                    </div>
                </div>
            <?php } ?>

        </div>
        <?php
        if (decimalValuePreview($total_log_payAmt) == decimalValuePreview($collect_payment)) {
            if ($collectionMainData['journal_id'] > 0) { ?>
                <div class="paid-btn">
                    Already Posted
                </div>
            <?php } else { ?>
                <div class="paid-btn">
                    <button type="submit" class="btn btn-primary float-right">Post</button>
                </div>

            <?php }
        } else { ?>
            <div class="paid-btn">
                <a class="btn btn-primary float-right" id="reverseCollectionBtn" data-id="<?= $decoded_pay_id ?>" data-no="<?= $collectionMainData['transactionId'] ?>">Reverse</a>
            </div>
        <?php } ?>
    </form>
</div>

<!-- Global View start-->
<div class="modal right fade global-view-modal" id="viewGlobalModal" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" aria-modal="true">
    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
        <div class="modal-content">
            <!-- <div class="modal-header">
                                                            <div class="top-details">
                                                                <div class="left">
                                                                    <p class="info-detail amount" id="amounts">
                                                                        <ion-icon name="wallet-outline"></ion-icon>
                                                                        <span class="amount-value" id="amount"> </span>
                                                                    </p>
                                                                    <span class="amount-in-words" id="amount-words"></span>
                                                                    <p class="info-detail po-number"><ion-icon name="information-outline"></ion-icon><span id="po-numbers"> </span></p>
                                                                </div>
                                                                <div class="right">
                                                                    <p class="info-detail name"><ion-icon name="business-outline"></ion-icon><span id="cus_name"></span></p>
                                                                    <p class="info-detail default-address"><ion-icon name="location-outline"></ion-icon><span id="default_address">

                                                                        </span></p>
                                                                </div>
                                                            </div>
                                                        </div> -->
            <div class="modal-body">
                <nav>
                    <div class="nav nav-tabs global-view-navTabs" id="nav-tab" role="tablist">
                        <!-- <button class="nav-link ViewfirstTab active" id="nav-overview-tab" data-bs-toggle="tab" data-bs-target="#nav-overview" type="button" role="tab" aria-controls="nav-overview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Overview</button> -->
                        <!-- <button class="nav-link classicview-btn classicview-link active" id="nav-classicview-tab" data-id="" data-bs-toggle="tab" data-bs-target="#nav-classicview" type="button" role="tab" aria-controls="nav-classicview" aria-selected="true"><ion-icon name="apps-outline"></ion-icon>Preview</button> -->
                        <!-- <button class="nav-link auditTrail" id="nav-trail-tab" data-bs-toggle="tab" data-bs-target="#nav-trail" data-ccode="" type="button" role="tab" aria-controls="nav-trail" aria-selected="false"><ion-icon name="time-outline"></ion-icon>Trail</button> -->
                    </div>
                </nav>
                <div class="tab-content global-tab-content" id="nav-tabContent">
                    <div class="tab-pane classicview-pane show active" id="nav-classicview" role="tabpanel" aria-labelledby="nav-classicview-tab">
                        <div class="card classic-view bg-transparent" id='printMe'>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<!-- Global View end -->
<?php
require_once("../common/footer.php");
?>

<script>
    function initializeDataTable() {
        dataTable = $("#dataTable_detailed_view").DataTable({
            dom: '<"dt-top-container"<l><"dt-center-in-div"B><f>r>t<ip>',
            "lengthMenu": [10, 25, 50, 100, 200, 250],
            "ordering": false,
            info: false,
            "initComplete": function(settings, json) {
                $('#dataTable_detailed_view_filter input[type="search"]').attr('placeholder', 'Search....');
            },

            buttons: [],
            // select: true,
            "bPaginate": false,

        });

    }
    $('#dataTable_detailed_view thead tr').append('<th>Action</th>');

    initializeDataTable();
    // for reverse collection


    $(document).on('click', '#reverseCollectionBtn', function() {

        let dep_keys = $(this).data('id');
        let transId = $(this).data('no');

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: `You want to reverse this ${transId} ?`,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Reverse'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    data: {
                        dep_keys: dep_keys,
                        dep_slug: 'reverseCollectionFailedAccounting'
                    },
                    url: 'ajaxs/ajax-reverse-post.php',
                    beforeSend: function() {
                        $('#reverseCollectionBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        var responseObj = JSON.parse(response);
                        console.log(responseObj);

                        let Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1000
                        });
                        Toast.fire({
                            icon: responseObj.status,
                            title: '&nbsp;' + responseObj.message
                        }).then(function() {
                            if (responseObj.status == "success") {
                                window.location.href = "failed-accounting-collectPayment.php";
                            } else {
                                location.reload();
                            }
                        });
                    }
                });
            }
        });
    });
</script>

<script>
    $(document).on('click', '.soModal', function() {
        let paymentId = $(this).data('id');
        $.ajax({
            type: "GET",
            url: "ajaxs/modals/so/ajax-collect-payment-modal.php",
            data: {
                act: "classicView",
                paymentId
            },

            beforeSend: function() {
                let loader = `<div class="load-wrapp" id="globalModalLoader">
                                    <div class="load-1">
                                        <div class="line"></div>
                                        <div class="line"></div>
                                        <div class="line"></div>
                                    </div>
                                </div>`;
                // Append the new HTML to the modal-body element
                $('#viewGlobalModal .modal-body').append(loader);

            },
            success: function(response) {
                $(".classic-view").html(response);
                $("#globalModalLoader").remove();
            },
            complete: function() {
                $("#globalModalLoader").remove();
            },
            error: function(error) {
                console.log(error);
            }
        });

    });
</script>