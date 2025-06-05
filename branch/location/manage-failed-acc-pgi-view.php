<?php

use Endroid\QrCode\Writer\ConsoleWriter;

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
require_once(BASE_DIR . "app/v1/functions/branch/func-brunch-so-controller.php");

global $created_by;
global $company_id;
global $branch_id;
global $location_id;

$dbObj = new Database();
$accountObj = new Accounting();
$BranchSoObj = new BranchSo();

if (isset($_GET['pay_id'])) {
    $pay_id = base64_decode($_GET['pay_id']);
}
$cond = "AND so_delivery_pgi_id =" . $pay_id . "";

$sql_list = "SELECT * FROM `erp_branch_sales_order_delivery_pgi` WHERE 1 ".$cond." AND `company_id` = $company_id AND `branch_id` = $branch_id AND `location_id` = $location_id AND `journal_id` IS NULL";

$sqlMainQryObj = $dbObj->queryGet($sql_list, true);
$MainData = $sqlMainQryObj['data'];
// console($sqlMainQryObj);
$pgiDebitCreditAccListObj =  $accountObj->getCreditDebitAccountsList("PGI");
// console($pgiDebitCreditAccListObj);
$pgiDebitAccList = $pgiDebitCreditAccListObj["debitAccountsList"];
$pgiCreditAccList = $pgiDebitCreditAccListObj["creditAccountsList"];

$itemsqlMainQryObj = $dbObj->queryGet("SELECT * FROM `erp_branch_sales_order_delivery_items_pgi` WHERE `so_delivery_pgi_id` = ".$pay_id."", true);
// console($itemsqlMainQryObj);
$itemnum_row = $itemsqlMainQryObj['numRows'];
$pgiItemData = $itemsqlMainQryObj['data'];
$totalcredit = 0;
$totaldebit = 0;
$customerId = $MainData[0]['customer_id'];
$customerDetailsObj =$dbObj-> queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'")['data'];

// console($customerDetailsObj);
$customerCode = $customerDetailsObj["customer_code"] ?? 0;
$customerParentGlId = $customerDetailsObj["parentGlId"] ?? 0;
$customerName = addslashes($customerDetailsObj['customer_name']);
// console($pgiItemData);
$pgiItems = [];
foreach ($pgiItemData  as $crkey => $pgivalue) {
    $itemId = $pgivalue['inventory_item_id'];
    $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];
    $movingWeightedPrice = $getItemSummaryObj['movingWeightedPrice'];
    $goodsType = $getItemSummaryObj['goodsType'];
    if ($goodsType == 4) {
        // console($movingWeightedPrice);
        $pGlIdObj = $dbObj->queryGet('SELECT `parentGlId` FROM `erp_inventory_items` WHERE `company_id` =' . $company_id . ' AND `itemId` =' . $itemId);
        $pgiitem = [
            'parentGlId' => $pGlIdObj["data"]["parentGlId"],
            'itemCode' => $pgivalue['itemCode'],
            'itemName' => $pgivalue['itemName'],
            'goodsMainPrice' => $movingWeightedPrice,
            'qty' => $pgivalue['qty']
        ];
        $pgiItems[] = $pgiitem;
    }
}
// console($pgiItems);
if (isset($_POST['act'])) {
    $PGIInputData = [
        "BasicDetails" => [
            "documentNo" => $MainData[0]['pgi_no'],
            "documentDate" => $MainData[0]['pgiDate'],
            "postingDate" => $MainData[0]['pgiDate'],
            "reference" => $MainData[0]['delivery_no'],
            "remarks" => "PGI Creation - " . $MainData[0]['pgi_no']. "",
            "journalEntryReference" => "Pgi"
        ],
        "customerDetails" => [
            "customerId" => $customerId,
            "customerName" => $customerName,
            "customerCode" => $customerCode,
            "customerGlId" => $customerParentGlId
        ],
        "FGItems" => $pgiItems
    ];
    // console($PGIInputData);
    $journal_check_sql = $dbObj->queryGet("SELECT * FROM `erp_acc_journal` WHERE `documentNo`='" . $MainData[0]['pgi_no'] . "' AND `parent_slug`='PGI'");
    // console($journal_check_sql);
    if ($journal_check_sql['numRows'] > 0) {
        $journal = $journal_check_sql['data']['id'];
        $queryObj = $dbObj->queryUpdate("UPDATE `erp_branch_sales_order_delivery_pgi` SET `journal_id`=$journal WHERE `so_delivery_pgi_id`='$pay_id'");
        if ($queryObj['status'] == 'success') {
            if($MainData[0]['pgiStatus']=='invoice'){
                $sqlInv = "UPDATE `erp_branch_sales_order_invoices` SET `journal_id`=$pgiJournalId WHERE `pgi_id`='$pay_id' AND `journal_id`=0";
                $upadatesqlIiv=$dbObj->queryUpdate($sqlInv);
            }
            swalAlert("success", 'Success', "PGI Accounting Posted Successfully", 'failed-accounting-pgi.php');
        } else {
            swalAlert("warning", 'Failed', "PGI Accounting Posting Failed",'failed-accounting-pgi.php');
        }
    } else {
        $ivPostingObj = $BranchSoObj->sopgiAccountingPosting($PGIInputData, "PGI",$pay_id);
        if ($ivPostingObj['status'] == 'success') {
                $pgiJournalId = $ivPostingObj['journalId'];
                $sqliv = "UPDATE `erp_branch_sales_order_delivery_pgi` SET `journal_id`=$pgiJournalId WHERE `so_delivery_pgi_id`='$pay_id'";
                $upadatesqliv=$dbObj->queryUpdate($sqliv);
                if($MainData[0]['pgiStatus']=='invoice'){
                    $sqlInv = "UPDATE `erp_branch_sales_order_invoices` SET `journal_id`=$pgiJournalId WHERE `pgi_id`='$pay_id' AND `journal_id`=0";
                    $upadatesqlIiv=$dbObj->queryUpdate($sqlInv);
                }
                swalAlert("success", 'Success', "PGI Accounting Posted Successfully", 'failed-accounting-pgi.php');
        } else {
            swalAlert("warning", 'Failed', "PGI Accounting Posting Failed",'failed-accounting-pgi.php');
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
            <li class="breadcrumb-item active"><a href="failed-accounting-pgi.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed PGI List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>PGI Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-pgi.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
    </div>

    <form method="post" action="">
        <input type="hidden" name="act">
        <div class="wrapper-account">
            <div class="header-block">

                <h2>Failed PGI For : <b><?= $MainData[0]['invoice_no'] ?></b>
                    <!-- <?php
                            if (decimalValuePreview($MainData[0]['total']) != decimalValuePreview($itemTotalAmt)) {
                                swalAlert("warning", 'Reverse', "Amount Issue in this debit note."); ?>
                        <span class="status-bg status-closed">Amount Issue in this debit note.</span>
                    <?php } ?> -->
                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Posting Date : <p><?= $MainData[0]['invoice_date'] ?></p>
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
                            <?php foreach ($pgiItemData  as $crkey => $pgivalue) {
                                $itemId = $pgivalue['inventory_item_id'];
                                $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];
                                $movingWeightedPrice = $getItemSummaryObj['movingWeightedPrice'];
                                $goodsType = $getItemSummaryObj['goodsType'];
                                $withouttaxamount = $movingWeightedPrice * $pgivalue['qty'];
                                if ($goodsType == 4) {
                                    $totalcredit += $withouttaxamount;
                            ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $pgiCreditAccList[0]['gl_code'] ?>||<?= $pgiCreditAccList[0]['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $pgivalue['itemCode'] ?>||<?= $pgivalue['itemName'] ?>
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo decimalValuePreview($withouttaxamount); ?></td>

                                    </tr>
                            <?php }
                            } ?>
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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totalcredit); ?></td>
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
                            <?php foreach ($pgiItemData  as $crkey => $pgivalue) {
                                $itemId = $pgivalue['inventory_item_id'];
                                $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];
                                $movingWeightedPrice = $getItemSummaryObj['movingWeightedPrice'];
                                $goodsType = $getItemSummaryObj['goodsType'];
                                $withouttaxamount = $movingWeightedPrice * $pgivalue['qty'];
                                if ($goodsType == 4) {
                                    $totaldebit += $withouttaxamount;
                            ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $pgiDebitAccList[0]['gl_code'] ?>||<?= $pgiDebitAccList[0]['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                --
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo decimalValuePreview($withouttaxamount) ?></td>

                                    </tr>
                            <?php }
                            } ?>
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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totaldebit) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php $diffAmount = abs($totalcredit - $totaldebit); ?>
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