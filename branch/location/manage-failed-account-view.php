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
include_once("../../app/v1/functions/branch/func-branch-failed-accounting-controller.php");

$dbObj = new Database();
$accountObj = new Accounting();

if (isset($_POST['act'])) {

    // console($_POST);
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
    $listItem = $_POST['credit']['items'];

    $flug1 = 0;
    $flug2 = 0;
    //************************START ACCOUNTING ******************** */
    $pgistatus = 0;
    if (count($pgiitem) > 0) {
        $pgistatus = 1;
        //-----------------------------PGI ACC Start ----------------
        $PGIInputData = [
            "BasicDetails" => [
                "documentNo" => $invNo, // Invoice Doc Number
                "documentDate" => $invoice_date, // Invoice number
                "postingDate" => $invoice_date, // current date
                "reference" => $invNo, // grn code
                "remarks" => "PGI Creation - " . $invNo . " " . $extra_remark,
                "journalEntryReference" => "Sales"
            ],
            "customerDetails" => [
                "customerName" => $customerName,
                "customerCode" => $customerCode,
                "customerGlId" => $customerParentGlId
            ],
            "FGItems" => $pgiitem
        ];
        // console($PGIInputData);
        $check_pgiJournal = queryGet("SELECT * FROM `erp_acc_journal` WHERE `parent_id`='" . $invId . "' AND `parent_slug`='PGI' AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . "");

        if ($check_pgiJournal['status'] == 'success') {
            $pgiJournalId = $check_pgiJournal['data']['id'];
            $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
            SET
            `pgi_journal_id`=$pgiJournalId
                WHERE `so_invoice_id`='$invId'";
            $invoicpgi = queryUpdate($sqliv);
            // console($invoicpgi);
        } else {
            $ivPostingObj = $accountObj->sopgiAccountingPosting($PGIInputData, "PGI", 0);
            // console($ivPostingObj); 

            if ($ivPostingObj['status'] == 'success') {
                $pgiJournalId = $ivPostingObj['journalId'];

                $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                    SET
                    `pgi_journal_id`=$pgiJournalId
                        WHERE `so_invoice_id`='$invId'";
                $invoicpgi = queryUpdate($sqliv);
                stockQtyImpact($allItems, "repost");
                // console($invoicpgi);

            } else {
                $flug1++;
            }
        }
        //-----------------------------PGI ACC END ----------------
    }


    //-----------------------------Invoicing ACC Start ----------------
    $InvoicingInputData = [
        "BasicDetails" => [
            "documentNo" => $invNo, // Invoice Doc Number
            "documentDate" => $invoice_date, // Invoice number
            "postingDate" => $invoice_date, // current date
            "grnJournalId" => '',
            "reference" => $invNo, // grn code
            "remarks" => "SO Invoicing - " . $invNo . " " . $extra_remark,
            "journalEntryReference" => "Sales"
        ],
        "customerDetails" => [
            "customerName" => $customerName,
            "customerCode" => $customerCode,
            "customerGlId" => $customerParentGlId
        ],
        "companyDetails" => $arrMarge,
        "compInvoiceType" => $compInvoiceType,
        "FGItems" => $listItem,
        "taxDetails" => $_POST['taxDetails'],
        "roundOffValue" => $roundOffValue
    ];
    // console($InvoicingInputData);
    $check_ivJournal = queryGet("SELECT * FROM `erp_acc_journal` WHERE `parent_id`='" . $invId . "' AND `parent_slug`='SOInvoicing' AND `branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=" . $company_id . "");

    if ($check_ivJournal['status'] == 'success') {
        $ivJournalId = $check_ivJournal['data']['id'];
        $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                    SET
                        `journal_id`=$ivJournalId 
                        WHERE `so_invoice_id`='$invId'";
        $invoic = queryUpdate($sqliv);

        // console($invoic);
    } else {
        $SOivPostingObj = $accountObj->soIvAccountingPosting($InvoicingInputData, "SOInvoicing", $invId);
        // console($SOivPostingObj);

        if ($SOivPostingObj['status'] == 'success') {
            $allItems = queryGet("SELECT `inventory_item_id` as itemId,`qty` FROM erp_branch_sales_order_invoice_items WHERE so_invoice_id = '$invId'", true)['data'];

            $allItems = array_map(function ($item) use ($invId) {
                $item['type'] = 'inv';
                $item['id'] = $invId;
                return $item;
            }, $allItems);

            $ivJournalId = $SOivPostingObj['journalId'];
            $sqliv = "UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "`
                    SET
                        `journal_id`=$ivJournalId 
                        WHERE `so_invoice_id`='$invId'";
            $invoic = queryUpdate($sqliv);
            // console($invoic);
        } else {
            $flug2++;
        }
    }


    if ($pgistatus == 0) {
        swalAlert($SOivPostingObj["status"], $SOivPostingObj["status"], $SOivPostingObj["message"], 'failed-accounting-invoices.php');
    } else {
        if ($flug1 == 0 and $flug2 == 0) {
            // stockQtyImpact($allItems, "repost");
            $update=updatelogAccountingFailure($invNo);
            swalAlert("success", 'Success', "PGI and Invoicing Accounting Posted Successfully", 'failed-accounting-invoices.php');
        } elseif ($flug1 > 0 and $flug2 == 0) {
            swalAlert("success", 'Success', "PGI Failed and Invoicing Accounting Posted Successfully", 'failed-accounting-invoices.php');
        } elseif ($flug1 = 0 and $flug2 > 0) {
            swalAlert("success", 'Success', "PGI Accounting Posted Successfully and Invoicing Failed!", 'failed-accounting-invoices.php');
        } else {
            swalAlert("warning", 'Failed', "Accounting Posting Failed!");
        }
    }
}


if (isset($_GET['inv_id'])) {
    $inv_id = base64_decode($_GET['inv_id']);
}

$cond = "AND so_inv.so_invoice_id=" . $inv_id . "";

$jlChksql = "SELECT so_inv.so_invoice_id  FROM `erp_branch_sales_order_invoices` AS so_inv
            LEFT JOIN `erp_e_invoices` ON so_inv.so_invoice_id = `erp_e_invoices`.invoice_id   WHERE 1 " . $cond . " AND so_inv.journal_id=0 OR so_inv.journal_id IS NULL AND  so_inv.company_id='" . $company_id . "'  AND so_inv.branch_id='" . $branch_id . "'  AND so_inv.location_id='" . $location_id . "'  AND so_inv.`status` ='active' ORDER BY so_inv.invoice_date DESC,so_inv.invoice_no ASC";

$jlsqlMainQryObj =  $dbObj->queryGet($jlChksql);
$jlnum_row = $sqlMainQryObj['numRows'];

// if ($jlsqlMainQryObj['status'] != 'success') {
//     echo "ERROR: " . $jlsqlMainQryObj['status'];
//     $url = "failed-accounting-invoices.php";
//     header('Location: ' . $url);
//     exit;
// }
$sql_Mainqry = "SELECT
                    so_inv.*,
                    cust.trade_name,
                    cust.customer_code,
                    cust.parentGlId
                FROM
                    `erp_branch_sales_order_invoices` AS so_inv
                LEFT JOIN `erp_e_invoices` ON so_inv.so_invoice_id = `erp_e_invoices`.invoice_id
                LEFT JOIN `erp_customer` as cust 
                ON so_inv.customer_id=cust.customer_id  WHERE 1 " . $cond . " AND (so_inv.journal_id = 0 OR so_inv.journal_id IS NULL) AND  so_inv.company_id='" . $company_id . "'  AND so_inv.branch_id='" . $branch_id . "'  AND so_inv.location_id='" . $location_id . "'  AND so_inv.`status` !='deleted'ORDER BY so_inv.invoice_date DESC,so_inv.invoice_no ASC";

$sqlMainQryObj =  $dbObj->queryGet($sql_Mainqry);
$num_row = $sqlMainQryObj['numRows'];
$invoiceMaindata = $sqlMainQryObj['data'];

$partyCode = $invoiceMaindata['customer_code'];
$partyName = $invoiceMaindata['trade_name'];
$partyparentGlId = $invoiceMaindata['parentGlId'];



$item_sql = "SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE so_invoice_id= " . $inv_id . "";

$itemsqlMainQryObj =  $dbObj->queryGet($item_sql, true);
$itemnum_row = $itemsqlMainQryObj['numRows'];
$invoiceItemData = $itemsqlMainQryObj['data'];
// console($itemsqlMainQryObj);



$pgiDebitCreditAccListObj = $accountObj->getCreditDebitAccountsList("PGI");
// console($pgiDebitCreditAccListObj);
if ($pgiDebitCreditAccListObj["status"] != "success") {
    return [
        "status" => "warning",
        "message" => "PGI Debit & Credit Account list is not available"
    ];
    die();
}

$pgiDebitAccList = $pgiDebitCreditAccListObj["debitAccountsList"];
$pgiCreditAccList = $pgiDebitCreditAccListObj["creditAccountsList"];


$invoiceingDebitCreditAccListObj = $accountObj->getCreditDebitAccountsList("SOInvoicing");
// console($invoiceingDebitCreditAccListObj);
if ($invoiceingDebitCreditAccListObj["status"] != "success") {
    return [
        "status" => "warning",
        "message" => "Invoice Debit & Credit Account list is not available"
    ];
    die();
}

$invoiceingDebitAccList = $invoiceingDebitCreditAccListObj["debitAccountsList"];
$invoiceingCreditAccList = $invoiceingDebitCreditAccListObj["creditAccountsList"];




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


$postingDate = $invoiceMaindata['invoice_date'];
$date_msg = '';
if (new DateTime(date("Y-m-d", strtotime($compOpeningDate))) > new DateTime(date("Y-m-d", strtotime($invoiceMaindata['invoice_date'] ?? "")))) {
    $postingDate = $compOpeningDate;
    $date_msg = "Invoice Posting Date changed by Company Openings date.";
}



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
            <li class="breadcrumb-item active"><a href="failed-accounting-invoices.php" class="text-dark"><i class="fa fa-list po-list-icon"></i>Failed Accounting List</a></li>
            <li class="breadcrumb-item active"><a class="text-dark"><i class="fa fa-plus po-list-icon"></i>Accounting Posting View</a></li>
            <li class="back-button">
                <a href="failed-accounting-invoices.php">
                    <i class="fa fa-reply po-list-icon"></i>
                </a>
            </li>
        </ol>
    </div>

    <form method="post" action="">
        <input type="hidden" name="act">
        <input type="hidden" name="so_invoice_id" value="<?= $inv_id; ?>">
        <input type="hidden" name="postingDate" value="<?= $postingDate ?>">
        <input type="hidden" name="invoice_no" value="<?= $invoiceMaindata['invoice_no'] ?>">
        <input type="hidden" name="extra_remarks" value="<?= $invoiceMaindata['remarks'] ?>">
        <input type="hidden" name="compInvoiceType" value="<?= $invoiceMaindata['compInvoiceType'] ?>">
        <div class="wrapper-account">
            <div class="header-block">
                <h2>Failed Invoice Acconting For : <b><?= $invoiceMaindata['invoice_no'] ?></b>
                    <?php if ($itemnum_row == 0) {
                        swalAlert("warning", 'Reverse & Repost', "Item Issue Please reverse and repost this invoice."); ?>
                        <span class="status-bg status-closed">Item Issue Please reverse and repost this invoice.</span>
                    <?php } ?>
                </h2>
                <h2><ion-icon name="analytics-outline"></ion-icon>Invoice Posting Date : <p><?= formatDateWeb($postingDate); ?></p>
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
                            $roundOfff = $invoiceMaindata['adjusted_amount'];
                            $totalcr = 0;
                            $totaldr = 0;


                            $pgiitem = [];
                            foreach ($invoiceItemData as $invoiceItem => $item) {

                                $itemdetails = $dbObj->queryGet("SELECT parentGlId,goodsType FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId=" . $item['inventory_item_id'] . "")['data'];
                                $summSql = "SELECT movingWeightedPrice FROM `" . ERP_INVENTORY_STOCKS_SUMMARY . "` WHERE itemId=" . $item['inventory_item_id'] . " AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id";
                                $itemSummeryDetails = $dbObj->queryGet($summSql)['data'];

                                $movingWeightedPrice = $item['goodsMainPrice'] > 0 ? $item['goodsMainPrice'] : $itemSummeryDetails['movingWeightedPrice'];

                                if ($itemdetails['goodsType'] != 5) { ?>

                                    <input type="hidden" name="credit[pgiitems][<?= $invoiceItem ?>][parentGlId]" value="<?= $itemdetails['parentGlId'] ?>">
                                    <input type="hidden" name="credit[pgiitems][<?= $invoiceItem ?>][gl_code]" value="<?= $inventory['gl_code'] ?>">
                                    <input type="hidden" name="credit[pgiitems][<?= $invoiceItem ?>][gl_label]" value="<?= $inventory['gl_label'] ?>">

                                    <input type="hidden" name="credit[pgiitems][<?= $invoiceItem ?>][goodsType]" value="<?= $itemdetails['goodsType'] ?>">
                                    <input type="hidden" name="credit[pgiitems][<?= $invoiceItem ?>][itemCode]" value="<?= $item['itemCode'] ?>">
                                    <input type="hidden" name="credit[pgiitems][<?= $invoiceItem ?>][itemName]" value="<?= $item['itemName'] ?>">
                                    <input type="hidden" name="credit[pgiitems][<?= $invoiceItem ?>][goodsMainPrice]" value="<?php echo $movingWeightedPrice ?>">
                                    <input type="hidden" name="credit[pgiitems][<?= $invoiceItem ?>][qty]" value="<?= $item['qty'] ?>">

                                <?php   }
                                $totalAmount += $item['totalPrice'];
                                $inventory = getChartOfAccountsDataDetails($itemdetails['parentGlId'])['data'];
                                ?>

                                <tr>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $inventory['gl_code'] ?> ||<?= $inventory['gl_label']; ?>
                                            <input type="hidden" name="credit[items][<?= $invoiceItem ?>][parentGlId]" value="<?= $itemdetails['parentGlId'] ?>">
                                            <input type="hidden" name="credit[items][<?= $invoiceItem ?>][gl_code]" value="<?= $inventory['gl_code'] ?>">
                                            <input type="hidden" name="credit[items][<?= $invoiceItem ?>][gl_label]" value="<?= $inventory['gl_label'] ?>">
                                        </p>
                                    </td>
                                    <td>
                                        <p class="pre-normal">
                                            <?= $item['itemCode'] ?> || <?= $item['itemName'] ?>
                                            <input type="hidden" name="credit[items][<?= $invoiceItem ?>][goodsType]" value="<?= $itemdetails['goodsType'] ?>">
                                            <input type="hidden" name="credit[items][<?= $invoiceItem ?>][itemCode]" value="<?= $item['itemCode'] ?>">
                                            <input type="hidden" name="credit[items][<?= $invoiceItem ?>][itemName]" value="<?= $item['itemName'] ?>">
                                            <input type="hidden" name="credit[items][<?= $invoiceItem ?>][goodsMainPrice]" value="<?php echo $movingWeightedPrice; ?>">
                                            <input type="hidden" name="credit[items][<?= $invoiceItem ?>][qty]" value="<?= $item['qty'] ?>">
                                        </p>
                                    </td>
                                    <td class="text-right"><?php echo $item['totalPrice'] ?></td>
                                    <input type="hidden" name="credit[items][<?= $invoiceItem ?>][totalPrice]" value="<?= $item['totalPrice'] ?>">
                                    <input type="hidden" name="credit[items][<?= $invoiceItem ?>][totalTax]" value="<?= $item['totalTax'] ?>">
                                </tr>
                            <?php }
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
                                <td class="text-right text-bold"><?php echo $totalcr; ?></td>
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
                            $totaldr = $totalAmount + $roundOfff;
                            $partydr = $totalAmount + $roundOfff;
                            if ($roundOfff < 0) {
                                $partydr = $totalAmount + $roundOfff;
                                $totaldr = $partydr + abs($roundOfff);
                            } ?>
                            <tr>
                                <td>
                                    <p class="pre-normal">
                                        <?php echo $invoiceingDebitAccList[0]['gl_code'] ?> || <?php echo $invoiceingDebitAccList[0]['gl_label'] ?>
                                        <input type="hidden" name="debit[gl_id]" value="<?= $invoiceingDebitAccList[0]['id'] ?>">
                                        <input type="hidden" name="debit[gl_code]" value="<?= $invoiceingDebitAccList[0]['gl_code'] ?>">
                                        <input type="hidden" name="debit[gl_label]" value="<?= $invoiceingDebitAccList[0]['gl_label'] ?>">
                                    </p>
                                </td>
                                <td>
                                    <p class="pre-normal">
                                        <?= $partyCode ?> || <?= $partyName ?>
                                        <input type="hidden" name="debit[customerCode]" value="<?= $partyCode ?>">
                                        <input type="hidden" name="debit[customerName]" value="<?= $partyName ?>">
                                    </p>
                                </td>
                                <td class="text-right"><?= $partydr; ?></td>
                            </tr>
                            <?php
                            $diffAmount = $totalcr - $totaldr;
                            if ($roundOfff < 0) { ?>
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
                                <td class="text-right text-bold"><?php echo $totaldr; ?></td>
                                <input type="hidden" name="debit[totalamount]" value="<?= $totaldr ?>">
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
            <input type="hidden" name="taxDetails[igst]" value="<?= $invoiceMaindata['igst'] ?>">
            <input type="hidden" name="taxDetails[cgst]" value="<?= $invoiceMaindata['cgst'] ?>">
            <input type="hidden" name="taxDetails[sgst]" value="<?= $invoiceMaindata['sgst'] ?>">

        </div>
        <?php
        if ($itemnum_row > 0) {
            if ($invoiceMaindata['journal_id'] > 0) { ?>
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