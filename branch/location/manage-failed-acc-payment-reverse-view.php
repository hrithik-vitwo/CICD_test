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
    $journal_id = $_POST['BasicDetails']['journal_id'];
    $payment_id = $_POST['BasicDetails']['payment_id'];
    $flug=0;
    $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $branch_id);
            // console($journalObj);
            if ($journalObj["status"] == 'success') {
                $journalData = $journalObj["data"];
                $reversePostingDate = $journalData["postingDate"];

                $accounting = array();
                $accounting['journal']['parent_id'] = $journalData["parent_id"];
                $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
                $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['party_code'] = $journalData["party_code"];
                $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
                $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
                $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['documentDate'] = $journalData["documentDate"];
                $accounting['journal']['postingDate'] = $reversePostingDate;


                //credit details
                $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                'subGlName' => $debitRow["subGlName"],
                'credit_amount' => $debitRow["debit_amount"],
                'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
            ];
        }

        //debit details
        $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
        foreach ($creditObj["data"] as $creditRow) {
            $accounting['debit'][] = [
                'glId' => $creditRow["glId"],
                'subGlCode' => $creditRow["subGlCode"],
                'subGlName' => $creditRow["subGlName"],
                'debit_amount' => $creditRow["credit_amount"],
                'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
            ];
        }
        $check_Journal = queryGet("SELECT * FROM `erp_acc_journal` WHERE `parent_id`=".$payment_id." AND `documentNo`='" . $tnxDocNo . "' AND `parent_slug`='Payment' AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . "", true);
        // console($check_Journal);
        if ($check_Journal['numRows'] == 1) {
            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
                $dbObj->queryUpdate("UPDATE `" . ERP_GRN_PAYMENTS . "` SET `journal_id` = 0, `reverse_journal_id` = " . $newJournalId . " WHERE `payment_id`=$payment_id");
            } else {
                $flug = 1;
            }
        } else if ($check_Journal['numRows'] == 2) {
            $newid = $check_Journal['data'][1]['id'];
            $dbObj->queryUpdate("UPDATE `" . ERP_GRN_PAYMENTS . "` SET `journal_id` = 0, `reverse_journal_id` = " . $newid . " WHERE `payment_id`=$payment_id");
        } else {
            $flug = 1;
        }
    }
     if ($flug == 0) {
        $logAccFailedResponce = updatelogAccountingFailure($tnxDocNo);
        swalAlert("success", 'Success', "Payment Reverse Accounting Success !", 'failed-accounting-payment.php?reverse');
    } else {
        swalAlert("warning", 'Failed', "Payment  Reverse Accounting Failed !");
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
                    WHERE pay.company_id = '" . $company_id . "' AND pay.branch_id ='" . $branch_id . "' AND pay.location_id ='" . $location_id . "' " . $cond . " AND (pay.journal_id != 0 AND pay.journal_id IS NOT NULL) AND (pay.reverse_journal_id = 0 OR pay.reverse_journal_id IS NULL) ORDER BY pay.payment_id DESC";


$sqlMainQryObj =  $dbObj->queryGet($sql_Mainqry);

$num_row = $sqlMainQryObj['numRows'];
$paymentMainData = $sqlMainQryObj['data'];
$bank_id = $paymentMainData['bank_id'];
$postingDate = $paymentMainData['postingDate'];
$payment_id = $paymentMainData['payment_id'];
$journal_id = $paymentMainData['journal_id'];
//console($paymentMainData);



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
            <li class="breadcrumb-item active"><a href="failed-accounting-payment.php?reverse" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed Accounting List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Accounting Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-payment.php?reverse">
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
        <input type="hidden" name="BasicDetails[journal_id]" value="<?= $journal_id; ?>">
        <input type="hidden" name="BasicDetails[payment_id]" value="<?= $payment_id; ?>">


        <div class="wrapper-account">
            <div class="header-block">

                <h2>Failed Payment Acconting For : <b><?= $paymentMainData['transactionId'] ?></b>

                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Posting Date : <p><?= formatDateWeb($postingDate); ?></p>
                </h2>
            </div>
            <div class="account-list debit-acc-list">
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
                            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
                            foreach ($creditObj["data"] as $creditRow) {
                                $gl = $dbObj->queryGet("SELECT * FROM erp_acc_coa_" . $company_id . "_table WHERE `id`=" . $creditRow['glId'] . "")['data'];
                                $totaldr += $creditRow['credit_amount']; ?>


                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $gl['gl_code'] ?>||<?= $gl['gl_label'] ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $creditRow['subGlCode'] ?> || <?= $creditRow['subGlName'] ?>
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($creditRow['credit_amount']) ?></td>

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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totaldr); ?></td>
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
                            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
                            foreach ($debitObj["data"] as $debitRow) {
                                $gl = $dbObj->queryGet("SELECT * FROM erp_acc_coa_" . $company_id . "_table WHERE `id`=" . $debitRow['glId'] . "")['data'];
                                // console($gl);
                                $totalcr += $debitRow['debit_amount']; ?>


                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $gl['gl_code'] ?>||<?= $gl['gl_label'] ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $debitRow['subGlCode'] ?> || <?= $debitRow['subGlName'] ?>
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($debitRow['debit_amount']) ?></td>

                                </tr>
                            <?php } 
                            $diffAmount = $totaldr - $totalcr;
                            ?>

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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totalcr); ?></td>
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


        </div>
        <div class="paid-btn">
            <button type="submit" class="btn btn-primary float-right">Post</button>
        </div>
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