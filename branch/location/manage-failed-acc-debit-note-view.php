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
include_once("../../app/v1/functions/branch/func-branch-failed-accounting-controller.php");


$dbObj = new Database();
$accountObj = new Accounting();
$grnObj = new GrnController();

if (isset($_GET['pay_id'])) {
    $pay_id =  base64_decode($_GET['pay_id']);
}
$cond = "AND dr_note_id =" . $pay_id . "";

$sql_list = "SELECT * FROM `erp_debit_note` WHERE 1 " . $cond . "  AND`branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . " AND `journal_id`=0  ORDER BY dr_note_id desc";

$sqlMainQryObj = $dbObj->queryGet($sql_list, true);
$MainData = $sqlMainQryObj['data'];
// console($MainData);
$finalwithouttax = 0;
$partyDetails = [];
$party_id = $MainData[0]['party_id'];
$party_name = $MainData[0]['debitor_type'];
if ($party_name == 'vendor') {
    // console("vendor");
    $vendorDetailsObj = $dbObj->queryGet('SELECT * FROM `erp_vendor_details` WHERE `vendor_id`=' . $party_id);
    $partyDetails['partyCode'] = $vendorDetailsObj["data"]["vendor_code"] ?? 0;
    $partyDetails['partyName'] = $vendorDetailsObj["data"]["trade_name"] ?? 0;
    $partyDetails['parentGlId'] = $vendorDetailsObj["data"]["parentGlId"] ?? 0;
} else {
    // console("customer");
    $CustomerDetailsObj = queryGet('SELECT * FROM `erp_customer` WHERE `customer_id`=' . $party_id);

    $partyDetails['partyCode'] = $CustomerDetailsObj["data"]["customer_code"] ?? 0;
    $partyDetails['partyName'] = $CustomerDetailsObj["data"]["trade_name"] ?? 0;
    $partyDetails['parentGlId'] = $CustomerDetailsObj["data"]["parentGlId"] ?? 0;
}
// console($partyDetails);

$taxDetails = [];
$taxDetails['cgst'] = $MainData[0]['cgst'] ?? 0;
$taxDetails['sgst'] = $MainData[0]['sgst'] ?? 0;
$taxDetails['igst'] = $MainData[0]['igst'] ?? 0;
// console($taxDetails);


$grnDebitCreditAccListObj =  $accountObj->getCreditDebitAccountsList("grniv");
// console($grnDebitCreditAccListObj);
$grnDebitAccList = $grnDebitCreditAccListObj["debitAccountsList"];
$grnCreditAccList = $grnDebitCreditAccListObj["creditAccountsList"];

$accMapp = getAllfetchAccountingMappingTbl($company_id);
// console($accMapp);


$roundOffValue = $MainData[0]['adjustment'];
$roundOffGL = $accMapp['data']['0']['roundoff_gl'];
$roundOff = getChartOfAccountsDataDetails($roundOffGL)['data'];
// console($roundOff);
$roundoffGlCode = $roundOff['gl_code'];
$roundoffGlName = $roundOff['gl_label'];

$itemsqlMainQryObj = $dbObj->queryGet("SELECT * FROM `debit_note_item` WHERE `debit_note_id`=" . $pay_id . "", true);
// console($itemsqlMainQryObj);
$itemnum_row = $itemsqlMainQryObj['numRows'];
$grnItemData = $itemsqlMainQryObj['data'];
// console($grnItemData);

$subgl_code = '';
$subgl_name = '';
$goodsType = '';
$account = '';
$uom = '';
$items = [];
$itemTotalAmt = 0;
foreach ($grnItemData as $key => $item) {
    $itemTotalAmt += ceil($item['item_amount']);
    $qty = $item['item_qty'];
    $rate = $item['item_rate'];
    $tax = $item['item_tax'];
    $withouttax = ceil($qty * $rate);
    $tax_amount = ($tax / 100) * ($qty * $rate);
    $amount = ($qty * $rate) + ($tax_amount);

    $itemArrys = array();
    $itemArrys = explode('_', $item['item_id']);
    $item_id = $itemArrys[0] ?? 0;

    $account = $item['account'];
    if (count($itemArrys) > 0) {
        $itemglQry = $dbObj->queryGet("SELECT baseUnitMeasure,parentGlId,itemCode,itemName,goodsType FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId='" . $item_id . "' AND company_id = '" . $company_id . "' ");
        $itemgl = $itemglQry['data'];
        $subgl_code = $itemgl['itemCode'];
        $subgl_name = $itemgl['itemName'];
        $goodsType = $itemgl['goodsType'];
        $uom = $itemgl['baseUnitMeasure'];
        $account = $itemgl['parentGlId'];
    }

    $items[$key]['accountGl'] = $account;
    $items[$key]['goodsType'] = $goodsType;
    $items[$key]['subgl_code'] = $subgl_code;
    $items[$key]['subgl_name'] = $subgl_name;
    $items[$key]['withouttax'] = $withouttax;
    $items[$key]['tax'] = $tax_amount;
    $finalwithouttax += $withouttax;
}
// console($items);
$itemTotalAmt += $roundOffValue;
if (isset($_POST['act'])) {
    $debit_note_no = $MainData[0]['debit_note_no'];
    $debit_note_id = $MainData[0]['dr_note_id'];
    $posting_date = $MainData[0]['postingDate'];
    $invoice = $dbObj->queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id` = " . $MainData[0]['debitNoteReference'] . "");
    $parent_id_code = $invoice['data']['invoice_no'];
    $remarks = "Debit Note for " . $parent_id_code . "";
    $accslug =  $MainData[0]['debitor_type'] . "DN";
    $customer_vendor = $MainData[0]['debitor_type'];
    $postingAccountingData = [
        "documentNo" => $debit_note_no,
        "documentDate" => $posting_date,
        "invoicePostingDate" => $posting_date,
        "referenceNo" => $parent_id_code,
        "type" => 'DN',
        "for" => $customer_vendor,
        "journalEntryReference" => 'DN',
        "remarks" => $remarks,
        "compInvoiceType" =>  "",
        "items" =>  $items,
        "roundOffValue" => $roundOffValue,
        "partyDetails" => $partyDetails,
        "taxDetails" => $taxDetails
    ];
    //   console($postingAccountingData);
    $journal_check_sql = queryGet("SELECT * FROM `erp_acc_journal` WHERE `parent_id`='" . $debit_note_id . "' AND `documentNo`='" . $debit_note_no . "'");
    if ($journal_check_sql['numRows'] > 0) {
        $journal = $journal_check_sql['data']['id'];
        $queryObj = $dbObj->queryUpdate("UPDATE `erp_debit_note` SET `journal_id`=" . $journal . " WHERE `dr_note_id`=" . $debit_note_id . "");
        if ($queryObj['status'] == 'success') {
            if ($customer_vendor == 'vendor') {
                $goods_journal_sql = queryGet("SELECT * FROM `erp_acc_journal` WHERE `parent_slug` LIKE 'VendorDNGoods' AND `parent_id`='" . $debit_note_id . "' AND `documentNo`='" . $debit_note_no . "'");
                if ($goods_journal_sql['numRows'] > 0) {
                    $goods_journal = $goods_journal_sql['data']['id'];
                    $queryObj_goods = $dbObj->queryUpdate("UPDATE `erp_debit_note` SET `goods_journal_id`=" . $goods_journal . " WHERE `dr_note_id`=" . $debit_note_id . "");
                    // console($queryObj_goods);
                    if ($queryObj_goods['status' == 'success']) {
                        $update=updatelogAccountingFailure($debit_note_no);
                        swalAlert("success", 'Success', "Debit Note Posted Successfully", 'failed-accounting-debit-note.php');
                    } else {
                        swalAlert("warning", 'Failed', "Debit Note Posting Failed!", 'failed-accounting-debit-note.php');
                    }
                }
                swalAlert("success", 'Success', "Debit Note Posted Successfully", 'failed-accounting-debit-note.php');

            }else{
                $goods_journal_sql = queryGet("SELECT * FROM `erp_acc_journal` WHERE `parent_slug` LIKE 'CustomerDNGoods' AND `parent_id`='" . $debit_note_id . "' AND `documentNo`='" . $debit_note_no . "'");
                if ($goods_journal_sql['numRows'] > 0) {
                    $goods_journal = $goods_journal_sql['data']['id'];
                    $queryObj_goods = $dbObj->queryUpdate("UPDATE `erp_debit_note` SET `goods_journal_id`=" . $goods_journal . " WHERE `dr_note_id`=" . $debit_note_id . "");
                    // console($queryObj_goods);
                    if ($queryObj_goods['status' == 'success']) {
                        $update=updatelogAccountingFailure($invNo);
                        swalAlert("success", 'Success', "Debit Note Posted Successfully", 'failed-accounting-debit-note.php');
                    } else {
                        swalAlert("warning", 'Failed', "Debit Note Posting Failed!", 'failed-accounting-debit-note.php');
                    }
                }
                swalAlert("success", 'Success', "Debit Note Posted Successfully", 'failed-accounting-debit-note.php');
            }
        }else{
            swalAlert("warning", 'Failed', "Debit Note Posting Failed!", 'failed-accounting-debit-note.php');
        }
    } else {
        $accPostingObj = [];
        if ($customer_vendor == 'vendor') {
            $accPostingObj = $accountObj->dNoteForVendorAccountingPosting($postingAccountingData, $accslug, $debit_note_id);
        } else {

            $accPostingObj = $accountObj->dNoteForCustomerAccountingPosting($postingAccountingData, $accslug, $debit_note_id);
        }
        // console($accPostingObj);
        if ($accPostingObj["status"] == "success" && $accPostingObj["journalId"] != "") {

            $allItems = queryGet("SELECT `item_id` as itemId,`item_qty` as qty FROM debit_note_item WHERE debit_note_id = '$debit_note_id'", true)['data'];

            $allItems = array_map(function ($item) use ($debit_note_id) {
                $item['type'] = 'dn';
                $item['id'] = $debit_note_id;
                return $item;
            }, $allItems);
            stockQtyImpact($allItems, "repost");
            $queryObj = $dbObj->queryUpdate("UPDATE `erp_debit_note` SET `journal_id`=" . $accPostingObj["journalId"] . ", `goods_journal_id` = " . $accPostingObj['goodsJournalId'] . " WHERE `dr_note_id`=" . $debit_note_id . "");
            // console($queryObj);
            swalAlert("success", 'Success', "Debit Note Posted Successfully", 'failed-accounting-debit-note.php');
        } else {
            swalAlert("warning", 'Failed', "Debit Note Posting Failed!", 'failed-accounting-debit-note.php');
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
            <li class="breadcrumb-item active"><a href="failed-accounting-debit-note.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed Debit Note List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Debit Note Posting View</a></li>
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

                <h2>Failed Debit Note For : <b><?= $MainData[0]['debit_note_no'] ?></b>
                    <?php
                    // console($MainData[0]['total']);
                    // console($itemTotalAmt);
                    if (decimalValuePreview($MainData[0]['total']) != decimalValuePreview($itemTotalAmt)) { ?>
                        <span class="status-bg status-closed">Amount Issue in this debit note.<?php
                                                                                                if ($itemnum_row == 0) {
                                                                                                ?>
                            and this needs to be reversed</span>

                    <?php
                                                                                                    swalAlert("warning", 'Reverse', "Amount Issue in this debit note and this needs to be reversed .");
                                                                                                } else { ?>
                        </span>;
                <?php
                                                                                                    swalAlert("warning", 'Reverse', "Amount Issue in this debit note.");
                                                                                                }
                                                                                            }
                ?>
                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Posting Date : <p><?= formatDateWeb($MainData[0]['postingDate']); ?></p>
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
                            $totalcr = 0;
                            $totaldr = 0;
                            ?>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?= $grnCreditAccList[0]['gl_code'] ?>||<?= $grnCreditAccList[0]['gl_label'] ?>
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        <?= $MainData[0]['party_code'] ?> || <?= $MainData[0]['party_name'] ?>
                                    </p>
                                </td>
                                <?php $tradeValue = $MainData[0]['total'];
                                // console($MainData[0]['cgst']);
                                ?>
                                <td class="text-right"><?php echo decimalValuePreview($tradeValue) ?></td>

                            </tr>
                            <?php
                            $totalcr = ceil($tradeValue);
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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totalcr) ?></td>
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
                            $itemTotalIgst = 0;
                            $itemTotalCgst = 0;
                            $itemTotalSgst = 0;
                            $itemTotalAmt = 0;
                            foreach ($grnItemData as $key => $item) {
                                $itemTotalCgst += ceil($item['cgst']);
                                $itemTotalSgst += ceil($item['sgst']);
                                $itemTotalIgst += ceil($item['igst']);
                                $itemTotalAmt += ceil($item['item_amount']);
                            }
                            ?>
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
                                $totaldr = $itemTotalSgst + $itemTotalCgst + $itemTotalIgst + $finalwithouttax; ?>

                            <tr>
                                <td>
                                    <p>Total Without Tax</p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        --
                                    </p>
                                </td>
                                <td class="text-right"><?php echo decimalValuePreview($finalwithouttax) ?></td>
                            <tr>

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
                                <td class="text-right text-bold"><?php echo decimalValuePreview($totaldr)  ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="account-amount deffrence-amount">
                <label for="">RoundOff</label>
                <div class="card-border-area">
                    <p><?= $roundOffValue; ?></p>
                </div>
            </div>
            <?php
            $diffAmount = abs($totalcr - $totaldr); ?>
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