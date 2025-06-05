<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-grn-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$vendorId = $_POST['customerSelect'];

$grnObj = new GrnController();
// $fetchInvoiceByCustomer = $grnObj->fetchGRNByVendorId($vendorId)['data'];
$fetchInvoiceByCustomer = $grnObj->fetchGRNInvoiceByVendorId($vendorId)['data'];
$fetchAdvanceAmt = $grnObj->fetchGrnAdvanceAmt($vendorId)['data']['totalAdvanceAmt'];
$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];
// console($vendorId);
// console("imranali59059");
// console($fetchInvoiceByCustomer);
if ($fetchInvoiceByCustomer != NULL) {
?>
    <!-- <div>Advanced Pay: <span class="advancedPayAmt"><?= $fetchAdvanceAmt ?? 0; ?></span></div> -->
    <input type="hidden" name="paymentDetails[paymentCollectType]" value="collect" class="paymentCollectType">
    <input type="hidden" name="paymentDetails[advancedPayAmt]" value="<?= $fetchAdvanceAmt ?>" class="advancedPayAmt">
    <table class="table">
        <tr>
            <th>IV No.</th>
            <th>Doc. No.</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Invoice Amt.</th>
            <th>Due Amt.</th>
            <th>Pay. Amt.</th>
            <th>Due %</th>
        </tr>
        <?php foreach ($fetchInvoiceByCustomer as $key => $one) {
            $statusLabel = fetchStatusMasterByCode($one['paymentStatus'])['data']['label'];
            $statusClass = "";
            if ($statusLabel == "paid") {
                $statusClass = "status";
            } elseif ($statusLabel == "partial paid") {
                $statusClass = "status-warning";
            } else {
                $statusClass = "status-danger";
            }
            // console('imranali59059');
            // console($one);

            $days = $one['credit_period'];
            $date = date_create($one['invoice_date']);
            date_add($date, date_interval_create_from_date_string($days . " days"));
            $creditPeriod = date_format($date, "d-m-Y");

            // checking Previous accounting impact
            $checkSql = "SELECT ivPostingJournalId FROM " . ERP_GRNINVOICE . " WHERE grnIvId='" . $one['grnIvId'] . "' AND companyId='$company_id' AND branchId='$branch_id' AND locationId='$location_id' AND grnStatus='active' ";
            $checkObj = queryGet($checkSql);
            $isAccountingFailed = true;
            if ($checkObj['status'] == "success" && $checkObj['numRows'] > 0) {
                $checkData = $checkObj["data"];
                $grnPostingJournalId = $checkData['ivPostingJournalId'];

                if ($grnPostingJournalId == 0 || $grnPostingJournalId == '' || $grnPostingJournalId == null) {
                    $isAccountingFailed = false;
                }
            }
        ?>
            <tr>
                <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][grnIvId]" value="<?= $one['grnIvId'] ?>">
                <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][grnCode]" value="<?= $one['grnIvCode'] ?>">
                <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][paymentStatus]" value="<?= $statusLabel ?>">
                <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][creditPeriod]" value="<?= $one['credit_period'] ?>">
                <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invAmt]" value="<?= $one['grnTotalAmount'] ?>">
                <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][dueAmt]" value="<?= $one['dueAmt'] ?>">
                <td><?= $one['grnIvCode'] ?? "<span class='text-danger'>Not Found!</span>"; ?></td>
                <td><?= $one['vendorDocumentNo'] ?? "<span class='text-danger'>Not Found!</span>"; ?></td>
                <td><span class="text-uppercase <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                <td><?= formatDateORDateTime($one['dueDate']) ?></td>
                <td class="invAmt invoiceAmt" id="invoiceAmt_<?= $one['grnIvId'] ?>"><?= decimalValuePreview($one['grnTotalAmount']) ?></td>
                <td class="dueAmt" id="dueAmt_<?= $one['grnIvId'] ?>"><?= decimalValuePreview($one['dueAmt']) ?></td>
                <td>
                    <div class="input-group m-0">
                        <?php if ($one['dueAmt'] <= 0) { ?>
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">✅</span>
                            </div>
                            <input readonly type="text" class="form-control receiveAmt px-3" style="background-color: #c6e5d4 !important;" placeholder="No Due Payment" aria-label="Username" aria-describedby="basic-addon1">
                        <?php } else { ?>

                            <div class="input-group-prepend">
                                <?php if (!$isAccountingFailed) { ?>
                                    <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1">❌</span>
                                <?php } else { ?>
                                    <span class="input-group-text" style="font-family:'Font Awesome 5 Free'" id="basic-addon1"><?= $companyCurrencyData["currency_name"] ?></span>
                                <?php  }  ?>
                            </div>

                            <?php if (!$isAccountingFailed) { ?>
                                <input type="text" name="paymentInvoiceDetails[<?= $key ?>][recAmt]" class="form-control receiveAmt px-3 inputAmountClass" id="receiveAmt_<?= $one['grnIvId'] ?>" placeholder="Accounting document not found" aria-label="Username" aria-describedby="basic-addon1" readonly>

                            <?php } else { ?>
                                <input type="text" name="paymentInvoiceDetails[<?= $key ?>][recAmt]" class="form-control receiveAmt px-3 inputAmountClass" id="receiveAmt_<?= $one['grnIvId'] ?>" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
                            <?php  }  ?>
                        <?php } ?>
                    </div>
                    <small style="display: none;" class="text-danger mt-n4 warningMsg" id="warningMsg_<?= $one['grnIvId'] ?>">Amount Exceeded </small>
                    <!-- <input type="text" class="form-control receiveAmt" id="receiveAmt_<?= $one['grnIvId'] ?>" placeholder="Enter Amount"> -->
                </td>
                <?php
                $due_amt = $one['dueAmt'];
                $inv_amt = $one['grnTotalAmount'];
                $duePercentage = ($due_amt / $inv_amt) * 100;
                ?>
                <td class="duePercentage" id="duePercentage_<?= $one['grnIvId'] ?>"><?= round($duePercentage); ?>%</td>
            </tr>
        <?php } ?>
    </table>

<?php } else { ?>
    <p class="text-xs text-danger">No Invoice Found!</p>
<?php } ?>