<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
require_once("../../app/v1/functions/common/templates/template-sales-order.controller.php");
require_once(BASE_DIR . "app/v1/functions/branch/func-brunch-so-controller.php");
require_once(BASE_DIR . "app/v1/functions/branch/func-items-controller.php");
include_once("../../app/v1/functions/branch/func-branch-failed-accounting-controller.php");

$dbObj = new Database();
$accountObj = new Accounting();
$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();



if (isset($_POST['act'])) {
    $invId = $_POST['so_invoice_id'];
    $invNo = $_POST['invoice_no'];
    $invoice_date = $_POST['postingDate'];
    $compInvoiceType = $_POST['compInvoiceType'];

    $customerCode = $_POST['debit']['customerCode'];
    $customerName = $_POST['debit']['customerName'];
    $customerParentGlId = $_POST['debit']['gl_id'];

    $roundOffValue = $_POST['roundOffValue'];

    $extra_remark = $_POST['extra_remark'] ?? '';
    $pgiitem = $_POST['credit']['pgiitems'];
    // echo  "--------------pgi";
    // console($pgiitem);
    $creditlist = $_POST['credit'];
    $debitlist = $_POST['debit'];

    $flug = false;
    $checkObject = queryGet('SELECT `so_invoice_id`,`invoice_date`,`pgi_id`, `pgi_journal_id`, `journal_id`,`invoice_no`,`so_number`,`status` FROM `' . ERP_BRANCH_SALES_ORDER_INVOICES . '` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `so_invoice_id`=' . $invId)['data'];
    $invoiceId = $checkObject["so_invoice_id"];
    $so_number = $checkObject["so_number"] ?? '';
    $invoiceCode = $checkObject["invoice_no"];
    $journal_id = $checkObject["journal_id"];
    $pgi_journal_id = $checkObject["pgi_journal_id"];
    $currentDateTime = date("Y-m-d H:i:s");

    //************************START ACCOUNTING ******************** */
    $newpgiJournalId = 0;
    if (!empty($pgi_journal_id)) {
        //Account reverse for Invoice insert with REVERSEINV000001 reference--------------------------------------------------------

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $pgi_journal_id . ' AND `branch_id`=' . $branch_id);
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
            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $pgi_journal_id, true);
            if ($debitObj['numRows'] == 0) {
                $accounting['credit'][] = [
                    'glId' => $creditlist["glId"],
                    'subGlCode' => '', // not available in credit
                    'subGlName' => '', // not available in credit
                    'credit_amount' => $creditlist["credit_amount"],
                    'credit_remark' => 'Reverse'
                ];
            } else {
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'credit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                    ];
                }
            }


            //debit details
            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $pgi_journal_id, true);
            if ($creditObj['numRows'] == 0) {
                foreach ($debitlist as $debitRow) {
                    $accounting['debit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'debit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse'
                    ];
                }
            } else {
                foreach ($creditObj["data"] as $creditRow) {
                    $accounting['debit'][] = [
                        'glId' => $creditRow["glId"],
                        'subGlCode' => $creditRow["subGlCode"],
                        'subGlName' => $creditRow["subGlName"],
                        'debit_amount' => $creditRow["credit_amount"],
                        'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                    ];
                }
            }
            $check_Journal = queryGet("SELECT * FROM `erp_acc_journal` WHERE (`parent_id`='" . $invoiceId . "' OR `refarenceCode`='".$invNo."') AND `parent_slug`='PGI' AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . "", true);
        
            if ($check_Journal['numRows'] == 1) {
                $accPostingObj = new AccountingPosting();
                $returnDataAcc = $accPostingObj->post($accounting);
                if ($returnDataAcc['status'] == 'success') {
                    $newJournalId = $returnDataAcc['journalId'];

                    //Journal Status change
                    $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $pgi_journal_id);
                    $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER_INVOICES . '` SET `pgi_journal_id`=0,`rev_pgi_journal_id`=' . $newJournalId . ' WHERE `so_invoice_id`=' . $invoiceId);
                } else {
                    $flug = true;
                }
            } else if ($check_Journal['numRows'] == 2) {
                $newid = $check_Journal['data'][1]['id'];
                $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER_INVOICES . '` SET `pgi_journal_id`=0,`rev_pgi_journal_id`=' . $newid . ' WHERE `so_invoice_id`=' . $invoiceId);
            } else {
                $flug = true;
            }
        }
    }
    if ((!$flug)) {
        if ($journal_id == 0) {
            $logAccFailedResponce = updatelogAccountingFailure($invNo);
        }
        swalAlert("success", 'Success', "PGI Accounting Posted Successfully", 'failed-accounting-pgi-invoice.php?reverse');
    } else {
        swalAlert("warning", 'Failed', "Accounting Posting Failed!",'failed-accounting-pgi-invoice.php?reverse');
    }
}

if (isset($_GET['pay_id'])) {
    $inv_id = base64_decode($_GET['pay_id']);
}

$cond = "AND so_inv.so_invoice_id=" . $inv_id . "";



$sql_Mainqry = "SELECT
                    so_inv.*,
                    cust.trade_name,
                    cust.customer_code,
                    cust.parentGlId
                FROM
                    `erp_branch_sales_order_invoices` AS so_inv
                LEFT JOIN `erp_e_invoices` ON so_inv.so_invoice_id = `erp_e_invoices`.invoice_id
                LEFT JOIN `erp_customer` as cust 
                ON so_inv.customer_id=cust.customer_id  WHERE 1 " . $cond . " AND (so_inv.pgi_journal_id != 0 OR so_inv.pgi_journal_id IS NOT NULL) AND (so_inv.rev_pgi_journal_id = 0 OR so_inv.rev_pgi_journal_id IS NULL) AND  so_inv.company_id='" . $company_id . "'  AND so_inv.branch_id='" . $branch_id . "'  AND so_inv.location_id='" . $location_id . "'  AND so_inv.`status` !='deleted'ORDER BY so_inv.invoice_date DESC,so_inv.invoice_no ASC";

$sqlMainQryObj =  $dbObj->queryGet($sql_Mainqry);
// console($sqlMainQryObj);
$num_row = $sqlMainQryObj['numRows'];
$invoiceMaindata = $sqlMainQryObj['data'];

$partyCode = $invoiceMaindata['customer_code'];
$partyName = $invoiceMaindata['trade_name'];
$partyparentGlId = $invoiceMaindata['parentGlId'];
$postingDate = $invoiceMaindata['invoice_date'];
$pgi_journal_id = $invoiceMaindata['pgi_journal_id'];

$pgiDebitCreditAccListObj = $accountObj->getCreditDebitAccountsList("PGI");

$pgiDebitAccList = $pgiDebitCreditAccListObj["debitAccountsList"];
$pgiCreditAccList = $pgiDebitCreditAccListObj["creditAccountsList"];


$item_sql = "SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE so_invoice_id= " . $inv_id . "";

$itemsqlMainQryObj =  $dbObj->queryGet($item_sql, true);
$itemnum_row = $itemsqlMainQryObj['numRows'];
$invoiceItemData = $itemsqlMainQryObj['data'];

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
            <li class="breadcrumb-item active"><a href="failed-accounting-pgi-invoice.php?reverse" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed Revese PGI List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Reverse PGI Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-pgi-invoice.php?reverse">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
    </div>
    <form id="post" method="post" action="">
        <input type="hidden" name="act">
        <input type="hidden" name="so_invoice_id" value="<?= $inv_id; ?>">
        <input type="hidden" name="invoice_no" value="<?= $invoiceMaindata['invoice_no'] ?>">
        <input type="hidden" name="extra_remarks" value="<?= $invoiceMaindata['remarks'] ?>">
        <div class="wrapper-account">
            <div class="header-block">
                <h2>Failed Invoice Acconting For : <b><?= $invoiceMaindata['invoice_no'] ?></b>

                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Invoice Posting Date : <p><?= formatDateWeb($postingDate); ?></p>
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
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalAmount = 0;
                            $totalcrAmount = 0;
                            $impact=false;
                            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $pgi_journal_id, true);
                           
                            if ($creditObj['numRows'] == 0) {
                                foreach ($invoiceItemData as $invoiceItem => $item) {
                                    // console($item);

                                    $itemdetails = $dbObj->queryGet("SELECT parentGlId,goodsType FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId=" . $item['inventory_item_id'] . "")['data'];
                                    // console($itemdetails);
                                    $itemId = $item['inventory_item_id'];
                                    $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];



                                    if ($itemdetails['goodsType'] == 4) {
                                        $movingWeightedPrice = $getItemSummaryObj['movingWeightedPrice'];
                                        $withouttaxamount = $movingWeightedPrice * $item['qty'];
                                        $totalAmount += $withouttaxamount;
                                    } else {
                                        $getItemBomDetailObj = $ItemsObj->getItemBomDetail($itemId);
                                        $pricetype = strtolower($getItemBomDetailObj['data']['bomProgressStatus']);
                                        $goodsMainPrice = $getItemBomDetailObj['data'][$pricetype] ?? 0;
                                        $movingWeightedPrice = $goodsMainPrice;
                                        $withouttaxamount = $movingWeightedPrice * $item['qty'];
                                        $totalAmount += $withouttaxamount;
                                    }
                                    if($movingWeightedPrice==0){
                                        swalAlert("error","Failed","Please check this item MWP","failed-accounting-pgi-invoice.php?reverse");
                                    }else{
                                        $impact=true;
                                    }
                            ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $pgiCreditAccList[0]['gl_code'] ?>||<?= $pgiCreditAccList[0]['gl_label'] ?>
                                                <input type="hidden" name="debit[<?= $item['inventory_item_id'] ?>][glId]" value="<?= $pgiCreditAccList[0]['id'] ?>">
                                                <input type="hidden" name="debit[<?= $item['inventory_item_id'] ?>][debit_remark]" value="<?= $invNo ?>">
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $item['itemCode'] ?> || <?= $item['itemName'] ?>
                                                <input type="hidden" name="debit[<?= $item['inventory_item_id'] ?>][subGlName]" value="<?= $item['itemName'] ?>">
                                                <input type="hidden" name="debit[<?= $item['inventory_item_id'] ?>][subGlCode]" value="<?= $item['itemCode'] ?>">
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo decimalValuePreview($withouttaxamount); ?></td>
                                        <input type="hidden" name="debit[<?= $item['inventory_item_id'] ?>][debit_amount]" value="<?= $withouttaxamount ?>">


                                    </tr>
                                <?php }
                                $totalcrAmount = $totalAmount;
                            } else {
                                foreach ($creditObj["data"] as $creditRow) {
                                    $totalAmount += $creditRow['credit_amount'];
                                ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $pgiCreditAccList[0]['gl_code'] ?>||<?= $pgiCreditAccList[0]['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                --
                                        </td>
                                        <td class="text-right"><?php echo decimalValuePreview($creditRow['credit_amount']) ?></td>
                                    </tr>
                            <?php  }
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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totalAmount); ?></td>
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
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $pgi_journal_id, true);
                            if ($debitObj['numRows'] == 0) { ?>
                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $pgiDebitAccList[0]['gl_code'] ?>||<?= $pgiDebitAccList[0]['gl_label'] ?>
                                            <input type="hidden" name="credit[glId]" value="<?= $pgiDebitAccList[0]['id'] ?>">
                                            <input type="hidden" name="credit[credit_remark]" value="<?= $invNo ?>">

                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            --
                                        </p>
                                    </td>
                                    <td class="text-right"><?= decimalValuePreview($totalAmount); ?></td>
                                    <input type="hidden" name="credit[credit_amount]" value="<?= $totalAmount ?>">
                                </tr>
                                <?php } else {
                                foreach ($debitObj["data"] as $debitRow) {
                                    $totalcrAmount += $debitRow['debit_amount'];
                                ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $pgiDebitAccList[0]['gl_code'] ?>||<?= $pgiDebitAccList[0]['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                                <?= $debitRow['subGlName'] ?> || <?= $debitRow['subGlCode'] ?>
                                            </p>
                                        </td>
                                        <td class="text-right"><?= decimalValuePreview($debitRow['debit_amount']); ?></td>
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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totalcrAmount); ?></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="account-amount deffrence-amount">
                <label for="">Amount Difference</label>
                <div class="card-border-area">
                    <p><?= ($totalAmount - $totalcrAmount) ?></p>
                </div>
            </div>

            <div class="paid-btn">
                <button type="submit" class="btn btn-primary float-right">Post</button>
            </div>
    </form>


</div>


 <script>
     var impact = <?= json_encode($impact) ?>;
     

    $(document).ready(function() {
        $('#post').on('submit', function(e) {
            if (impact === true) {
                e.preventDefault();

                 Swal.fire({
                    title: 'Are you sure?',
                    text: "This item is accounting on current item MWP. Do you want to continue?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, continue',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    } else {
                        window.location.href = "failed-accounting-pgi-invoice.php?reverse";
                    }
                });
            }
        });
    });
</script> 