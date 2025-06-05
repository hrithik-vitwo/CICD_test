<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$customerSelect = $_POST['customerSelect'];

$BranchSoObj = new BranchSo();
$fetchInvoiceByCustomer = $BranchSoObj->fetchBranchSoInvoiceBycustomerId($customerSelect)['data'];
$fetchAdvanceAmt = $BranchSoObj->fetchAdvanceAmt($customerSelect)['data']['totalAdvanceAmt'];

$fetchInvoiceAmtDetails = $BranchSoObj->totalInvoiceAmountDetailsByCustomer($customerSelect)['data'];

// console('$fetchInvoiceDetails');
// console($fetchInvoiceAmtDetails);

if ($fetchInvoiceByCustomer != NULL) {
?>

<style>
    td.recieved-amt input {
        width: 50%;
        height: auto;
        font-size: 12px;
    }

    @media (max-width: 575px) {
        td.recieved-amt input {
            width: 100%;
        }
    }
</style>

<input type="hidden" value="<?=$fetchInvoiceAmtDetails['total_outstanding_amount']?>" class="total_outstanding_amount">
<input type="hidden" value="<?=$fetchInvoiceAmtDetails['total_due_amount']?>" class="total_due_amount">
<input type="hidden" value="<?=$fetchInvoiceAmtDetails['total_overdue_amount']?>" class="total_overdue_amount">
 
<!-- <div>Advanced Pay: <span class="advancedPayAmt"><?= $fetchAdvanceAmt ?? 0; ?></span></div> -->
<input type="hidden" name="paymentDetails[paymentCollectType]" value="collect" class="paymentCollectType">
<input type="hidden" name="paymentDetails[advancedPayAmt]" value="<?= $fetchAdvanceAmt ?>" class="advancedPayAmt">
<table class="table defaultDataTable table-nowrap">
    <tr>
        <th>Invoice No</th>
        <th>Status</th>
        <th>Due Dates</th>
        <th>Invoice Amt.</th>
        <th>Due Amt.</th>
        <th style="width: 15%">Rec. Amt.</th>
        <th>Due %</th>
    </tr>
    <?php foreach ($fetchInvoiceByCustomer as $key => $one) {
        $statusLabel = fetchStatusMasterByCode($one['invoiceStatus'])['data']['label'];
        $statusClass = "";
        if ($statusLabel == "paid") {
            $statusClass = "status";
        } elseif ($statusLabel == "partial paid") {
            $statusClass = "status-warning";
        } else {
            $statusClass = "status-danger";
        }

        $days = $one['credit_period'];

        $date = date_create($one['invoice_date']);
        date_add($date, date_interval_create_from_date_string($days . " days"));
        $creditPeriod = date_format($date, "Y-m-d");
    ?>
        <tr>
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invoiceId]" value="<?= $one['so_invoice_id'] ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invoiceNo]" value="<?= $one['invoice_no'] ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invoiceStatus]" value="<?= $statusLabel ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][creditPeriod]" value="<?= $one['credit_period'] ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invAmt]" value="<?= $one['all_total_amt'] ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][dueAmt]" value="<?= $one['due_amount'] ?>">
            <td><?= $one['invoice_no'] ?></td>
            <td><span class="text-uppercase text-nowrap <?= $statusClass ?>"><?= $statusLabel ?></span></td>
            <td><?= $creditPeriod ?></td>
            <td class="invAmt invoiceAmt text-right" id="invoiceAmt_<?= $one['so_invoice_id'] ?>"><?= $one['all_total_amt'] ?></td>
            <td class="dueAmt text-right" id="dueAmt_<?= $one['so_invoice_id'] ?>"><?= $one['due_amount'] ?></td>
            <!-- <td class=" recieved-amt">
                <?php if ($one['invoiceStatus'] == 4) { ?>
                    <div class="form-input">

                        <p class="receiveAmt text-xs"> <i class="fa fa-check mr-2" style="border-radius: 50%; background: #198754; padding: 5px; color: #fff;"></i>
                            Payment Done</p>
                    </div>
                <?php } else { ?>

                    <input type="text" name="paymentInvoiceDetails[<?= $key ?>][recAmt]" class="form-control receiveAmt" id="receiveAmt_<?= $one['so_invoice_id'] ?>" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
                <?php } ?>
                <small style="display: none;" class="text-danger mt-n4 warningMsg" id="warningMsg_<?= $one['so_invoice_id'] ?>">Amount Exceeded </small>
            </td> -->
            <td>
                <div class="input-group enter-amount-input m-0">
                    <?php if ($one['due_amount'] <= 0) { ?>
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">✅</span>
                        </div>
                        <input readonly type="text" class="form-control receiveAmt px-3 text-right" style="background-color: #c6e5d4 !important;" placeholder="No Due Payment" aria-label="Username" aria-describedby="basic-addon1">
                    <?php } else { ?>
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">₹</span>
                        </div>
                        <input type="text" name="paymentInvoiceDetails[<?= $key ?>][recAmt]" class="form-control receiveAmt px-3 text-right" id="receiveAmt_<?= $one['so_invoice_id'] ?>" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
                    <?php } ?>
                </div>
                <small style="display: none;" class="text-danger mt-n4 warningMsg" id="warningMsg_<?= $one['so_invoice_id'] ?>">Amount Exceeded </small>
                <!-- <input type="text" class="form-control receiveAmt" id="receiveAmt_<?= $one['so_invoice_id'] ?>" placeholder="Enter Amount"> -->
            </td>
            <?php
            $due_amt = $one['due_amount'];
            $inv_amt = $one['all_total_amt'];
            $duePercentage = ($due_amt / $inv_amt) * 100;
            ?>
            <td class="duePercentage" id="duePercentage_<?= $one['so_invoice_id'] ?>"><?= round($duePercentage); ?>%</td>
        </tr>
    <?php } ?>
</table>

<?php } else { ?>
    <p class="text-xs text-danger">No Invoice Found!</p>
<?php } ?>


