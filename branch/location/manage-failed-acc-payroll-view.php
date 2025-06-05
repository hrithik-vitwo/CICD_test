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

global $created_by;
global $company_id;
global $branch_id;
global $location_id;

$dbObj = new Database();
$accountObj = new Accounting();


if (isset($_GET['pay_id'])) {
    $pay_id = base64_decode($_GET['pay_id']);
}
$cond = "AND payroll_main_id =" . $pay_id . "";

$sql_list = "SELECT * FROM `erp_payroll_main` WHERE 1 " . $cond . "  AND`branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . " AND `journal_id` is NULL ORDER BY payroll_main_id desc";

$sqlMainQryObj = $dbObj->queryGet($sql_list, true);
$MainData = $sqlMainQryObj['data'];
// console($MainData);
$debitCreditAccListObj = $accountObj->getCreditDebitAccountsList("payroll");
// console($debitCreditAccListObj);
$debitAccList = $debitCreditAccListObj["debitAccountsList"];
$creditAccList = $debitCreditAccListObj["creditAccountsList"];
$credit_total = $MainData[0]['sum_pf_employee'] + $MainData[0]['sum_pf_employeer'] + $MainData[0]['sum_esi_employee'] + $MainData[0]['sum_esi_employeer'] + $MainData[0]['sum_ptax'] + $MainData[0]['sum_tds'] + $MainData[0]['sum_gross'];
$debit_total = $MainData[0]['sum_gross'] + $MainData[0]['sum_pf_employee'] + $MainData[0]['sum_esi_employee'] + $MainData[0]['sum_ptax'] + $MainData[0]['sum_tds'] + $MainData[0]['sum_pf_employeer'] + $MainData[0]['sum_esi_employeer'];
$monthNumber = $MainData[0]['payroll_month'];
$dateObj = DateTime::createFromFormat('!m', $monthNumber);
$monthName = $dateObj->format('F');
if (isset($_POST['act'])) {
    $dateString = $MainData[0]['payroll_month'] . '-' . $MainData[0]['payroll_year'];
    $dateObj = DateTime::createFromFormat('m-Y', $dateString);
    $monthYear = $dateObj->format('F Y');
    $PostingInputData = [
        "BasicDetails" => [

            "documentNo" => $MainData[0]['payroll_code'],

            "documentDate" => date("Y-m-d"),

            "postingDate" => date("Y-m-d"),

            "reference" => $MainData[0]['payroll_code'],

            "remarks" => "Payroll Posting for - " . $monthYear,

            "journalEntryReference" => "payroll"

        ],
        "payrollDetails" => [

            "sum_pf_employee" => $MainData[0]['sum_pf_employee'],
            "sum_pf_employeer" => $MainData[0]['sum_pf_employeer'],
            "sum_esi_employee" => $MainData[0]['sum_esi_employee'],
            "sum_esi_employeer" => $MainData[0]['sum_esi_employeer'],
            "sum_ptax" => $MainData[0]['sum_ptax'],
            "sum_tds" => $MainData[0]['sum_tds'],
            "sum_gross" => $MainData[0]['sum_gross']

        ]

    ];
    // console($PostingInputData);
    $journal_check_sql = queryGet("SELECT * FROM `erp_acc_journal` WHERE `parent_id`='" . $pay_id . "' AND `documentNo`='" . $MainData[0]["payroll_code"] . "'");
    if ($journal_check_sql['numRows'] > 0) {
        $journal = $journal_check_sql['data']['id'];
        $queryObj = $dbObj->queryUpdate("UPDATE `erp_payroll_main` SET `journal_id`=" . $journal . ", `acconting_status`='Posted' WHERE `payroll_main_id`=" . $pay_id . "");
        if ($queryObj['status'] == 'success') {
            swalAlert("success", 'Success', "Payroll Accounting Posted Successfully", 'failed-accounting-payroll.php');
        } else {
            swalAlert("warning", 'Failed', "Payroll Accounting Posting Failed",'failed-accounting-payroll.php');
        }
    } else {
        $payrollPostingObj = $accountObj->payrollAccountingPosting($PostingInputData, "payroll", $pay_id);
        if ($payrollPostingObj['status'] == "success") {
            $queryObj = queryUpdate('UPDATE `erp_payroll_main` SET `journal_id`=' . $payrollPostingObj["journalId"] . ', `acconting_status`="Posted" WHERE `payroll_main_id`=' . $pay_id);

            $totalBsAmount = $MainData[0]['sum_gross'] + $MainData[0]['sum_pf_employeer'] + $MainData[0]['sum_esi_employeer'];
            $totalPLAmount = $MainData[0]['sum_pf_employee'] + $MainData[0]['sum_esi_employee'] + $MainData[0]['sum_ptax'] + $MainData[0]['sum_tds'];
            $totalSalryAmount = $totalBsAmount - $totalPLAmount;
            $insert_slry_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$pay_id, doc_no='" . $MainData[0]["payroll_code"] . "', `payroll_month`=" . $MainData[0]["payroll_month"] . ",`payroll_year`=" . $MainData[0]["payroll_year"] . ",`posting_date`=NOW(),`amount`=$totalSalryAmount,`due_amount`=$totalSalryAmount,`pay_type`='salary',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "',`status`='posted'");
            // console($insert_slry_peyroll);

            $totalPFAmount = $MainData[0]['sum_pf_employee'] + $MainData[0]['sum_pf_employeer'];
            $insert_pf_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$pay_id, doc_no='" . $MainData[0]["payroll_code"] . "', `payroll_month`=" . $MainData[0]["payroll_month"] . ",`payroll_year`=" . $MainData[0]["payroll_year"] . ",`posting_date`=NOW(),`amount`=$totalPFAmount,`due_amount`=$totalPFAmount, `pay_type`='pf',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "',`status`='posted'");
            // console($insert_pf_peyroll);
            $totalESIAmount = $MainData[0]['sum_esi_employee'] + $MainData[0]['sum_esi_employeer'];
            $insert_esi_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$pay_id, doc_no='" . $MainData[0]["payroll_code"] . "', `payroll_month`=" . $MainData[0]["payroll_month"] . ",`payroll_year`=" . $MainData[0]["payroll_year"] . ",`posting_date`=NOW(),`amount`=$totalESIAmount,`due_amount`=$totalESIAmount,`pay_type`='esi',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "',`status`='posted'");
            // console($insert_esi_peyroll);
            $sum_ptax = $MainData[0]['sum_ptax'];
            $insert_ptax_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$pay_id, doc_no='" . $MainData[0]["payroll_code"] . "', `payroll_month`=" . $MainData[0]["payroll_month"] . ",`payroll_year`=" . $MainData[0]["payroll_year"] . ",`posting_date`=NOW(),`amount`=$sum_ptax,`due_amount`=$sum_ptax,`pay_type`='ptax',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "',`status`='posted'");
            // console($insert_ptax_peyroll);
            $sum_tds = $MainData[0]['sum_tds'];
            $insert_tds_peyroll = queryInsert("INSERT INTO `erp_payroll_processing` SET payroll_main_id=$pay_id, doc_no='" . $MainData[0]["payroll_code"] . "', `payroll_month`=" . $MainData[0]["payroll_month"] . ",`payroll_year`=" . $MainData[0]["payroll_year"] . ",`posting_date`=NOW(),`amount`=$sum_tds,`due_amount`=$sum_tds,`pay_type`='tds',`location_id`=$location_id,`branch_id`=$branch_id,`company_id`=$company_id,`created_by` = '" . $created_by . "', `updated_by` = '" . $created_by . "',`status`='posted'");
            // console($insert_tds_peyroll);
            swalAlert("success", 'Success', "Payroll Accounting Posted Successfully", 'failed-accounting-payroll.php');
        } else {
            swalAlert("warning", 'Failed', "Payroll Accounting Posting Failed");
        }
    }
}

?>
<style>
    .wrapper,
    body,
    html {
        min-height: 0%;
    }

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
<div class="content-wrapper is-failed-account-view vitwo-alpha-global" style="overflow: auto;">

    <div class="container-fluid mt-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL; ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="failed-accounting-payroll.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed Payroll List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Payroll Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-payment.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
    </div>

    <form method="post" action="">
        <input type="hidden" name="act">
        <div class="wrapper-account">
            <div class="header-block">

                <h2>Failed Payroll For : <b><?= $MainData[0]['payroll_code'] ?></b>
                    <!-- <?php
                            if (decimalValuePreview($MainData[0]['total']) != decimalValuePreview($itemTotalAmt)) {
                                swalAlert("warning", 'Reverse', "Amount Issue in this debit note."); ?>
                        <span class="status-bg status-closed">Amount Issue in this debit note.</span>
                    <?php } ?> -->
                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Posting Date : <p><?= $monthName ?>,<?= $MainData[0]['payroll_year'] ?></p>
                </h2>
            </div>
            <div class="account-list credit-acc-list">
                <label for="">Credit account list</label>
                <div class="card-border-area">
                    <table>
                        <thead>
                            <tr>
                                <th width="25%">Ledger</th>
                                <th>Sub Ledger</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $creditAccList[0]['gl_code'] ?>||<?= $creditAccList[0]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($MainData[0]['sum_pf_employee'] + $MainData[0]['sum_pf_employeer']) ?></td>

                            </tr>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $creditAccList[1]['gl_code'] ?>||<?= $creditAccList[1]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($MainData[0]['sum_esi_employee'] + $MainData[0]['sum_esi_employeer']) ?></td>

                            </tr>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $creditAccList[2]['gl_code'] ?>||<?= $creditAccList[2]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($MainData[0]['sum_ptax']) ?></td>

                            </tr>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $creditAccList[3]['gl_code'] ?>||<?= $creditAccList[3]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($MainData[0]['sum_tds']) ?></td>

                            </tr>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $creditAccList[4]['gl_code'] ?>||<?= $creditAccList[4]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($MainData[0]['sum_gross']) ?></td>

                            </tr>
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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($credit_total); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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

                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $debitAccList[0]['gl_code'] ?>||<?= $debitAccList[0]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($MainData[0]['sum_gross'] + $MainData[0]['sum_pf_employee'] + $MainData[0]['sum_esi_employee'] + $MainData[0]['sum_ptax'] + $MainData[0]['sum_tds']) ?></td>

                            </tr>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $debitAccList[1]['gl_code'] ?>||<?= $debitAccList[1]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($MainData[0]['sum_pf_employeer']) ?></td>

                            </tr>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $debitAccList[2]['gl_code'] ?>||<?= $debitAccList[2]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($MainData[0]['sum_esi_employeer']) ?></td>

                            </tr>
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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($debit_total) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php $diffAmount = abs($credit_total - $debit_total); ?>
            <div class="account-amount deffrence-amount">
                <label for="">Amount Difference</label>
                <div class="card-border-area">
                    <p><?= $diffAmount; ?></p>
                </div>
            </div>

            <div class="paid-btn">
                <button type="submit" class="btn btn-primary float-right">Post</button>
            </div>


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