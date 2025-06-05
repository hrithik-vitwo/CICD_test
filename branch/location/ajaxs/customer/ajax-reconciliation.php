<?php
include_once("../../../../app/v1/connection-branch-admin.php");

$responseData = [];

$reconMonth = date('m');
$reconYear = date('Y');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $created_by;

    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $party_code = $_POST['party_code'];

    $party_sql = queryGet("SELECT * FROM `erp_customer` WHERE `location_id` = $location_id AND `customer_code` = '" . $party_code . "'");
    $party_id = $party_sql['data']['customer_id'];

    $statement_sql = queryGet("SELECT * FROM (
        SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,SUM(credit.credit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status = 0 AND journal.parent_slug='Collection' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
        UNION
        SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(debit.debit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status = 0 AND journal.parent_slug='SOInvoicing' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
        UNION
        SELECT journal.id AS id,journal.journalEntryReference AS type,journal.remark AS remark,journal.postingDate,0 AS amount,SUM(credit.credit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status = 0 AND journal.parent_slug='journal' AND journal.journalEntryReference='Collection' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND credit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
        UNION
        SELECT journal.id AS id,'SOInvoicing' AS type,journal.remark AS remark,journal.postingDate,SUM(debit.debit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status = 0 AND journal.parent_slug='journal' AND journal.journalEntryReference='Sales' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND debit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id) AS temp_table
        ORDER BY temp_table.postingDate desc;", true);

    $data = $statement_sql['data'];
    //  console($statement_sql);
    $total_transaction_debit = 0;
    $total_transaction_credit = 0;
    $total_transaction = 0;
    foreach ($statement_sql['data'] as $transaction) {
        // console($transaction);

        $total_transaction_credit += $transaction['payment'];
        //   echo '<br>';

        $total_transaction_debit += $transaction['amount'];
        // echo '<br>';




    }

    $total_pending_transaction += $total_transaction_debit - $total_transaction_credit;

    $statement_sql_reconciled = queryGet("SELECT * FROM (
            SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,0 AS amount,SUM(credit.credit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='Collection' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT journal.id AS id,journal.parent_slug AS type,journal.remark,journal.postingDate,SUM(debit.debit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='SOInvoicing' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND journal.party_code=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT journal.id AS id,journal.journalEntryReference AS type,journal.remark AS remark,journal.postingDate,0 AS amount,SUM(credit.credit_amount) AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_credit AS credit ON journal.id=credit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='journal' AND journal.journalEntryReference='Collection' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND credit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id
            UNION
            SELECT journal.id AS id,'SOInvoicing' AS type,journal.remark AS remark,journal.postingDate,SUM(debit.debit_amount) AS amount,0 AS payment FROM erp_acc_journal AS journal LEFT JOIN erp_acc_debit AS debit ON journal.id=debit.journal_id WHERE journal.reconcile_status != 0 AND journal.parent_slug='journal' AND journal.journalEntryReference='Sales' AND journal.company_id=$company_id AND journal.branch_id=$branch_id AND journal.location_id=$location_id AND journal.postingDate BETWEEN '" . $from_date . "' AND '" . $to_date . "' AND journal.journal_status='active' AND debit.subGlCode=$party_code GROUP BY journal.parent_slug,journal.remark,journal.postingDate,journal.id) AS temp_table
            ORDER BY temp_table.postingDate desc;", true);
    // console($statement_sql_reconciled);

    $total_recon_credit = 0;
    $total_recon_debit = 0;
    $total_reonciled = 0;
    foreach ($statement_sql_reconciled['data'] as $recon_transaction) {

        $total_recon_credit += $recon_transaction['payment'];
        $total_recon_debit += $recon_transaction['amount'];;
    }
    $total_reonciled += $total_recon_debit - $total_recon_credit;
}


?>

<div class="recon-amount-section">
    <div class="tranasction total-transaction">
        <div class="form-inline">
            <ion-icon name="repeat-outline"></ion-icon>
            <div class="form-input">
                <label for="">Total Transaction</label>
                <p class="amount"><?= round($total_reonciled + $total_pending_transaction, 2) ?></p>
            </div>
        </div>
    </div>

    <div class="tranasction total-transaction">
        <div class="form-inline">
            <ion-icon name="repeat-outline"></ion-icon>
            <div class="form-input">
                <label for="">Total Reconciled</label>
                <p class="amount"><?= round($total_reonciled, 2) ?></p>
            </div>
        </div>
    </div>

    <div class="tranasction total-transaction">
        <div class="form-inline">
            <ion-icon name="repeat-outline"></ion-icon>
            <div class="form-input">
                <label for="">Pending </label>
                <p class="amount"><?= round($total_pending_transaction, 2) ?></p>
            </div>
        </div>
    </div>
</div>
<div class="reconcile-btn">
    <button class="btn btn-primary float-right my-2" target="_blank" href="recon.php?type=customer&party_id=<?= $party_id ?>&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>">Proceed For Reconcilation</button>
</div>