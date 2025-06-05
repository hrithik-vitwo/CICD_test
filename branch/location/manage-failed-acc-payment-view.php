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
include_once("../../app/v1/functions/branch/func-grn-controller.php");


$dbObj = new Database();
$accountObj = new Accounting();
$grnObj = new GrnController();
if (isset($_GET['pay_id'])) {
    $decoded_pay_id = base64_decode($_GET['pay_id']);
}


if (isset($_POST['act'])) {

    //************************START ACCOUNTING ******************** */
    //-----------------------------payment ACC Start ----------------

    $postingDate = $_POST['invoicePostingDate'] ?? date("Y-m-d");
    $grnPostingJournalId = $ivPostingData["grnDetails"]["grnPostingJournalId"];
    $grnId = $ivPostingData["grnDetails"]["grnId"];
    $tnxDocNo = $_POST['BasicDetails']['documentNo'];
    $documentDate = $_POST['BasicDetails']['documentDate'];
    $invoiceConcadinate = $_POST['invoiceConcadinate'];
    $paymentInputData = [
        "BasicDetails" => [
            "documentNo" => $tnxDocNo,
            "documentDate" => $documentDate,
            "postingDate" =>  $postingDate,
            "reference" => $tnxDocNo,
            "remarks" => "Payment for - " . $invoiceConcadinate,
            "journalEntryReference" => "Payment/Expenses"
        ],
        "paymentDetails" => $_POST['paymentDetails'],
    ];
    $check_Journal = queryGet("SELECT * FROM `erp_acc_journal` WHERE `documentNo`='" . $tnxDocNo . "' AND `parent_slug`='Payment' AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . "");
    // console($check_Journal);
    if ($check_Journal['status'] == 'success') {
        $JournalId = $check_Journal['data']['id'];
        $sqliv = "UPDATE `erp_grn_payments` SET `journal_id` = '" . $JournalId . "' WHERE `payment_id` = " . $decoded_pay_id . "";
        $invoic = queryUpdate($sqliv);
        // console($invoic);
         if ($invoic['status'] == 'success') {
             swalAlert("success", 'Success', "Payment Accounting Posted Successfully", 'failed-accounting-payment.php');
         } else {
             swalAlert("warning", 'Failed', "Accounting Posting Failed!");
         }
    } else {
         $paymentPostingObj = $accountObj->multipaymentAccountingPosting($paymentInputData, "Payment", $_POST['paymentDetails']['paymentId']);

         if ($paymentPostingObj['status'] == 'success') {
             swalAlert("success", 'Success', "Payment Accounting Posted Successfully", 'failed-accounting-payment.php');
         } else {
             swalAlert("warning", 'Failed', "Accounting Posting Failed!");
         }
     }
}


// if (isset($_GET['pay_id'])) {
//     $decoded_pay_id = base64_decode($_GET['pay_id']);
// }

$cond = "AND pay.payment_id =" . $decoded_pay_id . "";

$sql_Mainqry = "SELECT
                    pay.*,
                    bank.bank_name,
                    bank.account_no,
                    vendor.vendor_code,
                    vendor.trade_name
                    FROM
                    erp_acc_bank_cash_accounts AS bank
                    LEFT JOIN erp_grn_payments AS pay
                    ON
                    bank.id = pay.bank_id
                    LEFT JOIN `erp_vendor_details` as vendor
                    ON pay.vendor_id=vendor.vendor_id
                    WHERE pay.company_id = '" . $company_id . "' AND pay.branch_id ='" . $branch_id . "' AND pay.location_id ='" . $location_id . "' " . $cond . " AND (pay.journal_id=0 OR pay.journal_id IS NULL) ORDER BY pay.payment_id DESC";


$sqlMainQryObj =  $dbObj->queryGet($sql_Mainqry);

$num_row = $sqlMainQryObj['numRows'];
$paymentMainData = $sqlMainQryObj['data'];
$bank_id = $paymentMainData['bank_id'];
// console($paymentMainData);


$collect_payment = $paymentMainData['collect_payment'];
$vendor_id = $paymentMainData['vendor_id'];
$fetchVendorDetailsObj = $grnObj->fetchVendorDetails($vendor_id);
$fetchVendorDetails = $fetchVendorDetailsObj['data'][0];
// console($fetchVendorDetails);

// $sql_Mainqry_paylogObj = $grnObj->fetchPaymentLogDetails($decoded_pay_id);
$sql_Mainqry_paylogObj = $dbObj->queryGet("SELECT * FROM `" . ERP_GRN_PAYMENTS_LOG . "` WHERE company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND payment_id='$decoded_pay_id' AND  status!='deleted' AND payment_type='pay'", true);

$total_log_payAmt = 0;
foreach ($sql_Mainqry_paylogObj['data'] as $index => $oneLog) {
    $total_log_payAmt += $oneLog['payment_amt'];
}

// console($sql_Mainqry_paylogObj);

$type = "Payment";
$grnDebitCreditAccListObj =  $accountObj->getCreditDebitAccountsList($type);

if ($grnDebitCreditAccListObj["status"] != "success") {
    return [
        "status" => "warning",
        "message" => "GRN Debit & Credit Account list is not available"
    ];
    die();
}

$paymentDebitAccList = $grnDebitCreditAccListObj["debitAccountsList"];
$paymentCreditAccList = $grnDebitCreditAccListObj["creditAccountsList"];
// console($paymentCreditAccList);


$accMapp = getAllfetchAccountingMappingTbl($company_id);
// console($accMapp);


$roundOffGL = $accMapp['data']['0']['roundoff_gl'];

$roundOff = getChartOfAccountsDataDetails($roundOffGL)['data'];
$roundoffGlCode = $roundOff['gl_code'];
$roundoffGlName = $roundOff['gl_label'];


$postingDate = $paymentMainData['postingDate'];
$date_msg = '';
if (new DateTime(date("Y-m-d", strtotime($compOpeningDate))) > new DateTime(date("Y-m-d", strtotime($paymentMainData['postingDate'] ?? "")))) {
    $postingDate = $compOpeningDate;
    $date_msg = "Payment Posting Date changed by Company Openings date.";
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
            <li class="breadcrumb-item active"><a href="failed-accounting-payment.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed Accounting List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Accounting Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-payment.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
    </div>

    <form method="post" action="">
        <input type="hidden" name="act">
        <input type="hidden" name="BasicDetails[reference]" value="<?= $paymentMainData['transactionId']; ?>">
        <input type="hidden" name="BasicDetails[remarks]" value="<?= $paymentMainData['remarks']; ?>">
        <input type="hidden" name="BasicDetails[journalEntryReference]" value="Payment/Expenses">
        <input type="hidden" name="BasicDetails[documentNo]" value="<?= $paymentMainData['transactionId']; ?>">
        <input type="hidden" name="BasicDetails[documentDate]" value="<?= $paymentMainData['documentDate']; ?>">
        <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>]">
        <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][vendor_id]" value="<?= $paymentMainData['vendor_id']; ?>">
        <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][vendorParentGl]" value="<?= $fetchVendorDetails['parentGlId']; ?>">
        <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][vendor_code]" value="<?= $fetchVendorDetails['vendor_code']; ?>">
        <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][vendor_name]" value="<?= $fetchVendorDetails['trade_name']; ?>">
        <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentCode]" value="<?= $paymentMainData['paymentCode']; ?>">
        <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentId]" value="<?= $paymentMainData['payment_id']; ?>">
        <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][bankId]" value="<?= $paymentMainData['bank_id']; ?>">


        <div class="wrapper-account">
            <div class="header-block">

                <h2>Failed Payment Acconting For : <b><?= $paymentMainData['transactionId'] ?></b>

                    <?php if (decimalValuePreview($total_log_payAmt) != decimalValuePreview($collect_payment)) {
                        swalAlert("warning", 'Reverse', "Amount Issue in this payment Id."); ?>
                        <span class="status-bg status-closed">Amount Issue in this payment Id.</span>
                    <?php } ?>
                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Posting Date : <p><?= formatDateWeb($postingDate); ?></p>
                </h2>
            </div>
            <div class="account-list dedit-acc-list">
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
                            $roundOfff = $paymentMainData['adjusted_amount'];
                            $totalcr = 0;
                            $totaldr = 0;

                            ?>

                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $paymentDebitAccList[0]['gl_code'] ?>||<?= $paymentDebitAccList[0]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        <?= $paymentMainData['vendor_code'] ?> || <?= $paymentMainData['trade_name'] ?>
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($total_log_payAmt) ?></td>

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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($total_log_payAmt); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="account-list credit-acc-list">
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
                            $roundOfff = $invoiceMaindata['adjusted_amount'];
                            $totalcr = 0;
                            $totaldr = 0;
                            $pgiitem = [];
                            $invoiceConcadinate = '';
                            foreach ($sql_Mainqry_paylogObj['data'] as $index => $oneLog) {
                                $totalAmount += $oneLog['payment_amt'];
                                $inventory = getChartOfAccountsDataDetails($oneLog['parentGlId'])['data'];
                                $getGrnDetails = $grnObj->getGrnDetails($oneLog['grn_id']);
                                $invoiceConcadinate .= $getGrnDetails['data']['grnCode'] . '| ';

                            ?>
                                <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentItems][<?= $index ?>][grnId]" value="<?= $oneLog['grn_id']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentItems][<?= $index ?>][grnCode]" value="<?= $getGrnDetails['data']['grnCode']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentItems][<?= $index ?>][roundoff]" value="<?= $oneLog['roundoff']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentItems][<?= $index ?>][writeback]" value="<?= $oneLog['writeback']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentItems][<?= $index ?>][financial_charge]" value="<?= $oneLog['financial_charge']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentItems][<?= $index ?>][forex]" value="<?= $oneLog['forex']; ?>">
                                <input type="hidden" name="paymentDetails[<?= $paymentMainData['vendor_id'] ?>][paymentItems][<?= $index ?>][recAmt]" value="<?= $oneLog['payment_amt']; ?>">

                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?php echo $getparentgLInfosqlObj['data']['gl_code']; ?> || <?php echo $getparentgLInfosqlObj['data']['gl_label']; ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            <?php echo $paymentMainData['account_no']; ?> || <?php echo $paymentMainData['bank_name']; ?>
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($oneLog['payment_amt']); ?></td>
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
        if (decimalValuePreview($total_log_payAmt) == decimalValuePreview($totalAmount)) {
            if ($paymentMainData['journal_id'] > 0) { ?>
                <div class="paid-btn">
                    Already Posted
                </div>
            <?php } else { ?>
                <div class="paid-btn">
                    <button type="submit" class="btn btn-primary float-right">Post</button>
                </div>

            <?php }
        } else { ?>
            <!-- <div class="paid-btn">
            <label for="" style="background-color:red;">Item Issue Please reverse and repost this invoice.</label>
 </div> -->
        <?php } ?>
    </form>
</div>


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
</script>