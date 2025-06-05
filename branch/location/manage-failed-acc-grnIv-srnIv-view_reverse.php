<?php

use Endroid\QrCode\Writer\Result\ConsoleResult;

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
include_once("../../app/v1/functions/branch/func-branch-failed-accounting-controller.php");


$dbObj = new Database();
$accountObj = new Accounting();
$grnObj = new GrnController();

if (isset($_POST['act'])) {

    $grnIvCode = $_POST['grnIvCode'];
    $grnIvId = $_POST['grnIvId'];
    $ivPostingJournalId = $_POST['ivPostingJournalId'];
    $flug = 0;
    $grnType = $_POST['grnType'];
    $grnIV = ($grnType === 'grn') ? 'grniv' : 'srniv';

    $grnStatus = 0;
    //************************START ACCOUNTING ******************** */

    //-----------------------------Grn ACC Start ----------------
    $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $ivPostingJournalId . ' AND `branch_id`=' . $branch_id);
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
        $check_Journal = queryGet("SELECT * FROM `erp_acc_journal` WHERE (`parent_id`='" . $grnIvId . "' OR `refarenceCode`='" . $grnIvCode . "') AND `parent_slug`='" . $grnIV . "' AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . "", true);
        if ($check_Journal['numRows'] == 1) {
            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $ivPostingJournalId);
                $dbObj->queryUpdate('UPDATE `erp_grninvoice` SET `ivPostingJournalId` = 0, `reverse_ivPostingJournalId` = ' . $newJournalId . ' WHERE `grnIvId`=' . $grnIvId);
            } else {
                $flug = 1;
            }
        } else if ($check_Journal['numRows'] == 2) {
            $newid = $check_Journal['data'][1]['id'];
            $dbObj->queryUpdate('UPDATE `erp_grninvoice` SET `ivPostingJournalId` = 0, `reverse_ivPostingJournalId` = ' . $newid . ' WHERE `grnIvId`=' . $grnIvId);
        } else {
            $flug = 1;
        }
    }



    if ($flug == 0) {
        swalAlert("success", 'Success', "GRNIv/SRNIv Accounting Posted Successfully", 'failed-accounting-grnIv-srnIv.php?reverse');
    } else {
        swalAlert("warning", 'Failed', "Accounting Posting Failed!");
    }
}


if (isset($_GET['grn_id'])) {
    $decoded_grn_id = base64_decode($_GET['grn_id']);
    $grn_id = explode("_", $decoded_grn_id)[1];
    $grnIv_id = explode("_", $decoded_grn_id)[0];
} else {
    $decoded_grn_id = base64_decode($_GET['srn_id']);
    $grn_id = explode("_", $decoded_grn_id)[1];
    $grnIv_id = explode("_", $decoded_grn_id)[0];
}



$cond = "AND grnIvId =" . $grnIv_id . "";


$sql_Mainqry = "SELECT * FROM `erp_grninvoice` as erp_grn LEFT JOIN `erp_vendor_details`as ven ON erp_grn.vendorId = ven.vendor_id  WHERE 1 " . $cond . " AND  (ivPostingJournalId != 0 AND ivPostingJournalId IS NOT NULL) AND (reverse_ivPostingJournalId = 0 OR reverse_ivPostingJournalId IS NULL) AND companyId =" . $company_id . " AND branchId=" . $branch_id . " AND locationId=" . $location_id . "" . $sts . " ORDER BY grnIvId DESC";

$sqlMainQryObj =  $dbObj->queryGet($sql_Mainqry);

// console($sqlMainQryObj);
// exit();
$num_row = $sqlMainQryObj['numRows'];
$grnMainData = $sqlMainQryObj['data'];

$partyCode = $grnMainData['vendorCode'];
$partyName = $grnMainData['vendorName'];
$partyparentGlId = $grnMainData['parentGlId'];
$journal_id = $grnMainData['ivPostingJournalId'];



$itemsqlMainQryObj = isset($_GET['grn_id']) ? $grnObj->getGrnItemDetails($grn_id) : $grnObj->getSrnItemDetails($grn_id);

// $itemsqlMainQryObj =  $dbObj->queryGet($item_sql, true);
$itemnum_row = $itemsqlMainQryObj['numRows'];
$grnItemData = $itemsqlMainQryObj['data'];
// console($grnItemData);

$grnType = isset($_GET['grn_id']) ? "grniv" : "srniv";
$grnDebitCreditAccListObj =  $accountObj->getCreditDebitAccountsList($grnType);
// console($grnDebitCreditAccListObj);
if ($grnDebitCreditAccListObj["status"] != "success") {
    return [
        "status" => "warning",
        "message" => "GRN Debit & Credit Account list is not available"
    ];
    die();
}

$grnDebitAccList = $grnDebitCreditAccListObj["debitAccountsList"];
$grnCreditAccList = $grnDebitCreditAccListObj["creditAccountsList"];
// console($grnCreditAccList);
function getDebitById($grnType, $type, $idToFind)
{
    $accountObj = new Accounting();
    $grnDebitCreditAccListObj =  $accountObj->getCreditDebitAccountsList($grnType);
    $grnDebitAccList = $grnDebitCreditAccListObj["debitAccountsList"];
    $grnCreditAccList = $grnDebitCreditAccListObj["creditAccountsList"];
    $array=[];
    if($type=='debit'){
      $array=$grnDebitAccList;
    }else{
        $array= $grnCreditAccList;
    
    }

    foreach ($array as $debit) {
        if ($debit['id'] == $idToFind) {
            return $debit;
        }
    }
    return null; // return null if not found
}

$accMapp = getAllfetchAccountingMappingTbl($company_id);
// console($accMapp);


$roundOffGL = $accMapp['data']['0']['roundoff_gl'];

$roundOff = getChartOfAccountsDataDetails($roundOffGL)['data'];
$roundoffGlCode = $roundOff['gl_code'];
$roundoffGlName = $roundOff['gl_label'];


$compInvoiceTypeval = 'domestic';
if ($compInvoiceType == 'R') {
    //Domestic Transaction
    $compInvoiceTypeval = 'domestic';
} else {
    //Export Transaction        
    $compInvoiceTypeval = 'export';
}


$postingDate = $grnMainData['postingDate'];
$date_msg = '';
if (new DateTime(date("Y-m-d", strtotime($compOpeningDate))) < new DateTime(date("Y-m-d", strtotime($grnMainData['invoice_date'] ?? "")))) {
    $postingDate = $compOpeningDate;
    $date_msg = "Invoice Posting Date changed by Company Openings date.";
}
$totalcr = 0;
$totaldr = 0;



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
<div class="content-wrapper is-failed-account-view vitwo-alpha-global" style="overflow: auto">

    <div class="container-fluid mt-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= BRANCH_URL; ?>" class="text-dark"><i class="fas fa-home po-list-icon"></i> Home</a></li>
            <li class="breadcrumb-item active"><a href="failed-accounting-grnIv-srnIv.php?reverse" class="text-dark"><i class="fa fa-list po-list-icon"></i>Reverse Failed Accounting List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Reverse Accounting Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-grnIv-srnIv.php?reverse">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
    </div>

    <form method="post" action="">
        <input type="hidden" name="act">
        <input type="hidden" name="grnCode" value="<?= $grnMainData['grnCode']; ?>">
        <input type="hidden" name="grnIvCode" value="<?= $grnMainData["grnIvCode"] ?>">
        <input type="hidden" name="grnId" value="<?= $grn_id; ?>">
        <input type="hidden" name="grnIvId" value="<?= $grnIv_id; ?>">
        <input type="hidden" name="grnType" value="<?= $grnMainData['grnType']; ?>">
        <input type="hidden" name="documentNo" value="<?= $grnMainData['vendorDocumentNo']; ?>">
        <input type="hidden" name="documentDate" value="<?= $grnMainData['vendorDocumentDate']; ?>">
        <input type="hidden" name="invoicePostingDate" value="<?= $grnMainData['postingDate']; ?>">
        <input type="hidden" name="invoiceDueDate" value="<?= $grnMainData['dueDate']; ?>">
        <input type="hidden" name="invoiceDueDays" value="<?= $grnMainData['dueDays']; ?>">
        <input type="hidden" name="vendorId" value="<?= $grnMainData['vendorId']; ?>">
        <input type="hidden" name="vendorCode" value="<?= $grnMainData['vendorCode']; ?>">
        <input type="hidden" name="vendorName" value="<?= $grnMainData['vendorName']; ?>">
        <input type="hidden" name="vendorGstin" value="<?= $grnMainData['vendorGstin']; ?>">
        <input type="hidden" name="totalInvoiceCGST" value="<?= $grnMainData['grnTotalCgst']; ?>">
        <input type="hidden" name="totalInvoiceSGST" value="<?= $grnMainData['grnTotalSgst']; ?>">
        <input type="hidden" name="totalInvoiceIGST" value="<?= $grnMainData['grnTotalIgst']; ?>">
        <input type="hidden" name="totalInvoiceSubTotal" value="<?= $grnMainData['grnSubTotal']; ?>">
        <input type="hidden" name="totalInvoiceTotal" value="<?= $grnMainData['grnTotalAmount']; ?>">
        <input type="hidden" name="locationGstinStateName" value="<?= $grnMainData['locationGstinStateName']; ?>">
        <input type="hidden" name="vendorGstinStateName" value="<?= $grnMainData['vendorGstinStateName']; ?>">
        <input type="hidden" name="vendorDocumentFile" value="<?= $grnMainData['vendorDocumentFile']; ?>">
        <input type="hidden" name="totalInvoiceTDS" value="<?= $grnMainData['grnTotalTds']; ?>">
        <input type="hidden" name="currency" value="<?= $grnMainData['currency']; ?>">
        <input type="hidden" name="currency_conversion_rate" value="<?= $grnMainData['conversion_rate']; ?>">
        <input type="hidden" name="ivPostingJournalId" value="<?= $grnMainData['ivPostingJournalId']; ?>">
        <input type="hidden" name="id" value="<?= $grnDetails["grnId"] ?>">
        <input type="hidden" name="parentGlId" value="<?= $grnDetails["parentGlId"] ?>">

        <div class="wrapper-account">
            <div class="header-block">
                <h2>Failed GRNIv/SRNIv Acconting For : <b><?= $grnMainData['grnIvCode'] ?></b>
                    <!-- <?php if ($itemnum_row == 0) {
                                swalAlert("warning", 'Reverse', "Item Issue Please reverse this grn/srn.", "failed-accounting-grnIv-srnIv.php"); ?>
                        <span class="status-bg status-closed">Item Issue Please reverse this grniv/srniv.</span>
                    <?php } ?> -->
                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Posting Date : <p><?= formatDateWeb($postingDate); ?></p>
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
                            <?php
                            $totalAmount = 0;
                            $roundOfff = $grnMainData['adjusted_amount'];
                            $itemTotalIgst = 0;
                            $itemTotalCgst = 0;
                            $itemTotalSgst = 0;
                            $itemTotalAmt = 0;


                            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
                            // console($debitObj);
                            if ($debitObj['numRows'] == 0) {

                                $pgiitem = [];
                                // console($grnItemData);
                                foreach ($grnItemData as $invoiceItem => $item) {
                                    $itemTotalCgst += $item['cgst'];
                                    $itemTotalSgst += $item['sgst'];
                                    $itemTotalIgst += $item['igst'];
                                    $itemTotalAmt += $item['totalAmount'];
                                    foreach ($grnDebitAccList as $index => $oneDebit) {

                                        $itemdetails = $dbObj->queryGet("SELECT *FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId=" . $item['goodId'] . "")['data'];
                                        $summSql = "SELECT movingWeightedPrice FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` WHERE itemId=" . $item['goodId'] . " AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id";
                                        $itemSummeryDetails = $dbObj->queryGet($summSql)['data'];

                                        $movingWeightedPrice = $item['goodsMainPrice'] > 0 ? $item['goodsMainPrice'] : $itemSummeryDetails['movingWeightedPrice'];
                                        // console($itemdetails);
                                        // console($item['itemCode']);
                                        if ($item['igst'] > 0) {
                                            $itemTax = $item['igst'];
                                        } else {
                                            $itemTax = $item['cgst'] + $item['sgst'];
                                        }

                                        if ($itemdetails['goodsType'] != 5) { ?>

                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemId]" value="<?= $item['goodId'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemCode]" value="<?= $item['goodCode'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemHsn]" value="<?= $item['goodHsn'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemName]" value="<?= $item['goodName'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemQty]" value="<?= $item['goodQty'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemTax]" value="<?= $itemTax ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemUnitPrice]" value="<?= $item['unitPrice'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemGrandTotalPrice]" value="<?= $item['totalAmount'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemTotalPrice]" value="<?= $item['totalAmount'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemStorageLocationId]" value="<?= $item['itemStorageLocation'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemStockQty]" value="<?= $item['itemStocksQty'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemReceivedQty]" value="<?= $item['receivedQty'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemUOM]" value="<?= $item['itemUOM'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemCGST]" value="<?= $item['cgst'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemSGST]" value="<?= $item['sgst'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemIGST]" value="<?= $item['igst'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemTds]" value="<?= $item['tds'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][itemInvoiceGoodsType]" value="<?= $item['goodstype'] ?>">
                                            <input type="hidden" name="grnItemList[<?= $invoiceItem ?>][parentGlId]" value="<?= $itemdetails['parentGlId'] ?>">
                                            <input type="hidden" name="totalInvoiceCGST" value="<?= $grnMainData['cgst'] ?>">
                                            <input type="hidden" name="totalInvoiceSGST" value="<?= $grnMainData['sgst'] ?>">
                                            <input type="hidden" name="totalInvoiceIGST" value="<?= $grnMainData['igst'] ?>">
                                            <input type="hidden" name="totalInvoiceTDS" value="<?= $grnMainData['grnTotalTds'] ?>">
                                            <input type="hidden" name="totalInvoiceSubTotal" value="<?= $grnMainData['grnSubTotal'] ?>">
                                            <input type="hidden" name="totalInvoiceTotal" value="<?= $grnMainData['grnTotalAmount'] ?>">
                                        <?php   }
                                        $totalAmount += $item['totalAmount'];
                                        $inventory = getChartOfAccountsDataDetails($itemdetails['parentGlId'])['data'];
                                        // console($inventory);
                                        ?>

                                    <?php } ?>
                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                GR/IR
                                                <!-- <?= $inventory['gl_code'] ?> ||<?= $inventory['gl_label']; ?> -->
                                                <input type="hidden" name="[parentGlId]" value="<?= $itemdetails['parentGlId'] ?>">
                                                <input type="hidden" name="[gl_code]" value="<?= $inventory['gl_code'] ?>">
                                                <input type="hidden" name="[gl_label]" value="<?= $inventory['gl_label'] ?>">
                                            </p>


                                        </td>

                                        <td>
                                            <p class="pre-normal">
                                                --
                                            </p>
                                        </td>
                                        <td class="text-right"><?php echo decimalValuePreview($item['totalAmount']) ?></td>
                                        <input type="hidden" name="[totalPrice]" value="<?= $item['totalPrice'] ?>">
                                        <input type="hidden" name="[totalTax]" value="<?= $item['totalTax'] ?>">
                                    </tr>
                                <?php
                                } ?>
                                <tr>
                                    <td>
                                        <p>Total SGST</p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            --
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($itemTotalSgst) ?></td>

                                </tr>
                                <tr>
                                    <td>
                                        <p>Total CGST</p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            --
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($itemTotalCgst) ?></td>

                                </tr>
                                <tr>
                                    <td>
                                        <p>Total IGST</p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            --
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($itemTotalIgst) ?></td>

                                    <?php
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
                            <?php }
                                } else {
                                //    console($debitObj['data']);
                                    foreach ($debitObj["data"] as $debitRow) {
                                        $totalcr += $debitRow['debit_amount'];
                                        $grnDebit=getDebitById($grnType,'debit', $debitRow['glId']);
                                        if(!$grnDebit){
                                            $grnDebit['gl_code']=$roundoffGlCode;
                                            $grnDebit['gl_label']=$roundoffGlName;
                                        }
                                        // console($grnDebit);
                            ?>

                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $grnDebit['gl_code'] ?> ||<?= $grnDebit['gl_label']; ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            <!-- <?= $debitRow['subGlCode'] ?> || <?= $debitRow['subGlName'] ?> -->
                                             --
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($debitRow['debit_amount']) ?></td>
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
                            <td class="text-right text-bold"><?php echo decimalValuePreview($totalcr); ?></td>
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
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalAmount = 0;
                            $roundOfff = $grnMainData['adjusted_amount'];
                            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
                            if ($creditObj['numRows'] == 0) {
                                $totalcr = 0;
                                $totaldr = 0;
                                // console($grnItemData);
                                // foreach ($grnCreditAccList as $index => $onegrnCreditAccList) {
                            ?>
                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            20031 ||Trade payables

                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $grnMainData['vendorCode'] ?> || <?= $grnMainData['vendorName'] ?>
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($itemTotalAmt) ?></td>

                                </tr>
                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            20039 ||TDS payables

                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $grnMainData['vendorCode'] ?> || <?= $grnMainData['vendorName'] ?>
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo decimalValuePreview($grnMainData['grnTotalTds']) ?></td>

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
                                <?php }
                            } else {
                                // console($creditObj['data']);
                                foreach ($creditObj["data"] as $creditRow) {
                                    $totaldr += $creditRow['credit_amount']; 
                                     $grnCredit=getDebitById($grnType,'credit', $creditRow['glId']);
                                    ?>

                                    <tr>
                                        <td>
                                            <p class="pre-normal">
                                                <?php echo $grnCredit['gl_code'] ?> || <?php echo $grnCredit['gl_label'] ?>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="pre-normal">
                                               <?= $creditRow['subGlCode'] ?> || <?= $creditRow['subGlName'] ?>
                                            </p>
                                        </td>
                                        <td class="text-right"><?= decimalValuePreview($creditRow['credit_amount']); ?></td>
                                    </tr>

                            <?php  }
                            }
                            ?>

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
            <?php $diffAmount = $totaldr - $totalcr;  ?>
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