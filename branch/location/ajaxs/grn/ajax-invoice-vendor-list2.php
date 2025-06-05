<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-grn-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$vendorId = $_POST['customerSelect'];

$grnObj = new GrnController();
$paymentLog = $grnObj->fetchAllPaymentLogByVendorId($vendorId)['data'];

// $fetchInvoiceByVendor = $grnObj->fetchGRNByVendorId($vendorId)['data'];
$fetchInvoiceByVendor = $grnObj->fetchGRNInvoiceByVendorId($vendorId)['data'];
$fetchAdvanceAmt = $grnObj->fetchGrnAdvanceAmt($vendorId)['data']['totalAdvanceAmt'];
// console("imranali59059");
// console($fetchInvoiceByVendor);
if ($fetchInvoiceByVendor != NULL) {
?>
<div>Advanced Pay: <span class="advancedPayAmt"><?= decimalValuePreview($fetchAdvanceAmt) ?? decimalValuePreview(0); ?></span></div>
<input type="hidden" name="paymentDetails[paymentCollectType]" value="collect" class="paymentCollectType">
<input type="hidden" name="paymentDetails[advancedPayAmt]" value="<?= $fetchAdvanceAmt ?>" class="advancedPayAmt">
<table class="table">
    <tr>
        <th>GRNIV Code</th>
        <th>Invoice No.</th>
        <th>Status</th>
        <th>Due Dates</th>
        <th>Invoice Amt.</th>
        <th>Due Amt.</th>
        <th>Settlement</th>
        <th>Due %</th>
    </tr>
    <?php foreach ($fetchInvoiceByVendor as $key => $one) {
        $statusLabel = fetchStatusMasterByCode($one['paymentStatus'])['data']['label'];
        $statusClass = "";
        if ($statusLabel == "paid") {
            $statusClass = "status";
        } elseif ($statusLabel == "partial paid") {
            $statusClass = "status-warning";
        } else {
            $statusClass = "status-danger";
        }

        $days = $one['credit_period'];
        $date = date_create($one['dueDate']);
        date_add($date, date_interval_create_from_date_string($days . " days"));
        $creditPeriod = date_format($date, "Y-m-d");

        $due_amt = $one['dueAmt'];
        $inv_amt = $one['grnTotalAmount'];
        $duePercentage = ($due_amt / $inv_amt) * 100;
    ?>
        <tr>
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][grnIvId]" value="<?= $one['grnIvId'] ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][grnCode]" value="<?= $one['grnCode'] ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][paymentStatus]" value="<?= $statusLabel ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][creditPeriod]" value="<?= formatDateWeb($one['credit_period']) ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invAmt]" value="<?= $one['grnTotalAmount'] ?>">
            <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][dueAmt]" value="<?= $one['dueAmt'] ?>">
            <input type="hidden" class="vendorId" id="vendorId_<?= $one['grnIvId'] ?>" value="<?= $vendorId ?>">
            <td><?= $one['grnIvCode'] ?></td>
            <td><?= $one['vendorDocumentNo'] ?></td>
            <td><span class="text-uppercase <?= $statusClass ?>"><?= $statusLabel ?></span></td>
            <td><?= formatDateWeb($one['dueDate']) ?></td>
            <td class="invAmt invoiceAmt" id="invoiceAmt_<?= $one['grnIvId'] ?>"><?= inputValue($one['grnTotalAmount']) ?></td>
            <td class="dueAmt" id="dueAmt_<?= $one['grnIvId'] ?>"><?= inputValue($one['dueAmt']) ?></td>
            
            <td>
                <?php if ($one['paymentStatus'] == 4) { ?>
                    <i class="fas fa-check py-2 px-2 shadow-sm border text-success" style="border-radius:50%;align-items: center;"></i>
                <?php } else { ?>
                    <i class="fas fa-handshake py-2 px-2 text-dark shadow-sm border text-light paymentSettlement" id="paymentSettlement_<?= $one['grnIvId'] ?>" style="border-radius:50%;align-items: center; cursor:pointer" aria-hidden="true" data-toggle="modal" data-target="#paymentSettlement_<?= $one['grnIvId'] ?>"></i>
                <?php } ?>
            </td>
            
            <td class="duePercentage" id="duePercentage_<?= $one['grnIvId'] ?>"><?= round($duePercentage); ?>%</td>
        </tr>
        <!-- right modal start here  -->
        <div class="modal fade right customer-modal vendor-modal" id="paymentSettlement_<?= $one['grnIvId'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                    <!--Content-->
                    <div class="modal-content">
                        <!--Header-->
                        <!-- <div class="modal-header">
...
                        </div> -->
                        <!--Body-->
                        <div class="modal-body pl-4 pr-4 pt-5 mt-5">
                            <div style="display: flex;justify-content: space-between;">
                                <h6>Due Amount: <span class="inv-<?= $one['grnIvId'] ?>-dueAmtOnModal"><?= inputValue($one['dueAmt']) ?></span></h6>
                                <h5>Advanced List</h5>
                                <input type="hidden" class="inv-<?= $one['grnIvId'] ?>-dueAmtOnModalStatic" value="<?= inputValue($one['dueAmt']) ?>">
                                <button type="button" class="btn btn-success btn-sm invoiceAddBtn" value="<?= $one['grnIvId'] ?>" id="invoiceAddBtn_<?= $one['grnIvId'] ?>">POST</button>
                            </div>
                            <span class="text-xs text-danger" id="dueAmtAdvancedAmtMsg_<?= $one['grnIvId'] ?>"></span>
                            <span class="text-xs text-danger" id="emptyAdvAmtMsg_<?= $one['grnIvId'] ?>"></span>
                            <p class="text-success" id="postMsg_<?= $one['grnIvId'] ?>"></p>
                            <?php
                            if ($paymentLog != NULL) {
                                foreach ($paymentLog as $log) {
                                    if ($log['advancedAmt'] != 0 && $log['advancedAmt'] >= 0) {
                                        $date = date_create($log['documentDate']);
                                        $documentDate = date_format($date, "d M, Y");
                            ?>
                                        <div class="row border align-center my-2">
                                            <div class="col-md-6">
                                                <input type="hidden" class="inv-<?= $one['grnIvId'] ?>-paymentId" id="inv-<?= $one['grnIvId'] ?>-paymentId" value="<?= $log['payment_id'] ?>">
                                                <input type="hidden" value="<?= $log['advancedAmt'] ?>" class="inv-<?= $one['grnIvId'] ?>-staticAdvancedAmtInp" id="inv-<?= $one['grnIvId'] ?>-staticAdvancedAmtInp_<?= $log['payment_id'] ?>">
                                                <h6 class="text-success"><span class="rupee-symbol">₹</span><span class="inv-<?= $one['grnIvId'] ?>-advancedAmtSpan" id="inv-<?= $one['grnIvId'] ?>-advancedAmtSpan_<?= $log['payment_id'] ?>"><?= inputValue($log['advancedAmt']) ?></span></h6>
                                                <input type="text" data-advancedid="<?= $log['payment_id'] ?>" placeholder="Enter amount" class="form-control form-control-sm mb-2 inv-<?= $one['grnIvId'] ?>-advancedAmtInp" id="inv-<?= $one['grnIvId'] ?>-advancedAmtInp_<?= $log['payment_id'] ?>">
                                                <span class="text-xs text-danger" id="inv-<?= $one['grnIvId'] ?>-advancedAmtMsg_<?= $log['payment_id'] ?>"></span>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-grid advance-list-cash">
                                                    <p class="text-right text-sm m-2 font-weight-bold"><?= $log['transactionId'] ?></p>
                                                    <p class="text-right text-xs m-2"><?= $documentDate ?></p>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                            } else {
                                ?>
                                <p class="text-xs text-danger">Advanced Amount Not Found!</p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- right modal end here  -->
    <?php } ?>
</table>

<?php 
} else { 
    ?>
    <p class="text-xs text-danger">No Invoice Found!</p>
<?php
 } 
?>