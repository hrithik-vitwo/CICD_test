<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$customerSelect = $_POST['customerSelect'];
$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];
$currency_name = $companyCurrencyData['currency_name'];
$BranchSoObj = new BranchSo();
$paymentLog = $BranchSoObj->fetchAllPaymentLogByCustomerId($customerSelect)['data'];

$fetchInvoiceByCustomer = $BranchSoObj->fetchBranchSoInvoiceBycustomerId($customerSelect)['data'];
$fetchAdvanceAmt = $BranchSoObj->fetchAdvanceAmt($customerSelect)['data']['totalAdvanceAmt'];

$fetchInvoiceAmtDetails = $BranchSoObj->totalInvoiceAmountDetailsByCustomer($customerSelect)['data'];

if ($fetchInvoiceByCustomer != NULL) {
?>

    <style>
        .settlement-modal .modal-header {
            height: 64px !important;
            justify-content: space-between;
            display: flex;
        }

        .settlement-modal .modal-body {
            top: 0 !important;
        }

        .modal.right .modal-header::after {
            display: none !important;
        }

        .settlement-modal .card {
            box-shadow: 2px 4px 8px -4px #000;
        }
    </style>
    <!-- <div>Advanced Pay: <span class="advancedPayAmt"><?= $fetchAdvanceAmt ?? 0; ?></span></div> -->
    <div class="row mb-3">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <p class="text-xs text-right">Advanced Pay</p>
            <p class="text-xs text-right font-bold rupee-symbol"><?=$currency_name?> <span class="advancedPayAmt"><?= decimalValuePreview($fetchAdvanceAmt) ?? 0; ?></span></p>
        </div>
    </div>


    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_outstanding_amount'] ?>" class="total_outstanding_amount">
    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_due_amount'] ?>" class="total_due_amount">
    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_overdue_amount'] ?>" class="total_overdue_amount">


    <input type="hidden" name="paymentDetails[advancedPayAmt]" value="<?= $fetchAdvanceAmt ?>" class="advancedPayAmt">
    <input type="hidden" name="paymentDetails[paymentCollectType]" value="adjust" class="paymentCollectType">
    <div class="scrollable-card">
        <table class="table defaultDataTable">
            <tr>
                <th>Invoice No</th>
                <th>Status</th>
                <th>Due Dates</th>
                <th>Invoice Amt.</th>
                <th>Due Amt.</th>
                <th>Settlement</th>
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
                $due_amt = $one['due_amount'];
                $totalCreditNoteAmount = 0;
                $creditNoteReferenceId = $one['so_invoice_id'];
                $cnSql = "SELECT *  FROM erp_credit_note WHERE creditNoteReference='" . $creditNoteReferenceId . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `creditors_type`='customer' AND status='active'";

                $cnRes = queryGet($cnSql, true);

                if ($cnRes['numRows'] > 0) {
                    $cnData = $cnRes['data'];

                    $totalCreditNoteAmount = queryGet("SELECT SUM(total) AS totalCreditNoteAmount FROM erp_credit_note WHERE creditNoteReference='" . $creditNoteReferenceId . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id GROUP BY creditNoteReference;")['data']['totalCreditNoteAmount'];
                }
              
                $due_amt = $due_amt - $totalCreditNoteAmount;
              
                $inv_amt = $one['all_total_amt'];
                $duePercentage = ($due_amt / $inv_amt) * 100;
            ?>
                <tr>
                    <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invoiceId]" value="<?= $one['so_invoice_id'] ?>">
                    <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invoiceNo]" value="<?= $one['invoice_no'] ?>">
                    <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invoiceStatus]" value="<?= $statusLabel ?>">
                    <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][creditPeriod]" value="<?= $one['credit_period'] ?>">
                    <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][invAmt]" value="<?= decimalValuePreview($one['all_total_amt']) ?>">
                    <input type="hidden" name="paymentInvoiceDetails[<?= $key ?>][dueAmt]" value="<?= decimalValuePreview($due_amt) ?>">
                    <input type="hidden" class="customerId" id="customerId_<?= $one['so_invoice_id'] ?>" value="<?= $customerSelect ?>">
                    <td><?= $one['invoice_no'] ?></td>
                    <td><span class="text-uppercase text-nowrap <?= $statusClass ?>"><?= $statusLabel ?></span></td>
                    <td><?= formatDateWeb($creditPeriod)?></td>
                    <td class="invAmt invoiceAmt text-right" id="invoiceAmt_<?= $one['so_invoice_id'] ?>"><?= decimalValuePreview($one['all_total_amt']) ?></td>
                    <td class="dueAmt text-right" id="dueAmt_<?= $one['so_invoice_id'] ?>"><?= inputValue($due_amt) ?></td>
                    <td>
                        <?php if ($one['invoiceStatus'] == 4) { ?>
                            <i class="fa fa-check mr-2" style="border-radius: 50%; background: #198754; padding: 5px; color: #fff;"></i>
                        <?php } else { ?>
                            <i class="fas fa-handshake po-list-icon paymentSettlement" id="paymentSettlement_<?= $one['so_invoice_id'] ?>" style="cursor:pointer" aria-hidden="true" data-toggle="modal" data-target="#paymentSettlement_<?= $one['so_invoice_id'] ?>"></i>
                        <?php } ?>
                    </td>
                    <td class="duePercentage" id="duePercentage_<?= $one['so_invoice_id'] ?>"><?= decimalQuantityPreview($duePercentage); ?>%</td>
                </tr>
                <!-- right modal start here  -->
                <div class="modal fade right customer-modal settlement-modal" id="paymentSettlement_<?= $one['so_invoice_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="true" style="display: none;" aria-hidden="true">
                    <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document">
                        <!--Content-->
                        <div class="modal-content">
                            <!--Header-->
                            <div class="modal-header">
                                <h5 class="text-white">Advanced List ( <span class="inv-<?= $one['so_invoice_id'] ?>-dueAmtOnModal"><?= decimalValuePreview($one['due_amount']) ?></span> )</h5>
                                <input type="hidden" class="inv-<?= $one['so_invoice_id'] ?>-dueAmtOnModalStatic" value="<?= inputValue($one['due_amount']) ?>">
                                <button type="button" class="btn btn-success invoiceAddBtn" value="<?= $one['so_invoice_id'] ?>" id="invoiceAddBtn_<?= $one['so_invoice_id'] ?>">POST</button>
                            </div>
                            <!--Body-->
                            <div class="modal-body pl-4 pr-4 pt-5">

                                <span class="text-xs text-danger" id="dueAmtAdvancedAmtMsg_<?= $one['so_invoice_id'] ?>"></span>
                                <span class="text-xs text-danger" id="emptyAdvAmtMsg_<?= $one['so_invoice_id'] ?>"></span>
                                <p class="text-success" id="postMsg_<?= $one['so_invoice_id'] ?>"></p>
                                <?php
                                if ($paymentLog != NULL) {
                                    foreach ($paymentLog as $log) {
                                        if ($log['advancedAmt'] != 0 && $log['advancedAmt'] >= 0) {
                                            $date = date_create($log['documentDate']);
                                            $documentDate = date_format($date, "d M, Y");
                                ?>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row border align-center my-2">
                                                        <div class="col-md-6">
                                                            <input type="hidden" class="inv-<?= $one['so_invoice_id'] ?>-paymentId" id="inv-<?= $one['so_invoice_id'] ?>-paymentId" value="<?= $log['payment_id'] ?>">
                                                            <input type="hidden" value="<?= $log['advancedAmt'] ?>" class="inv-<?= $one['so_invoice_id'] ?>-staticAdvancedAmtInp" id="inv-<?= $one['so_invoice_id'] ?>-staticAdvancedAmtInp_<?= $log['payment_id'] ?>">
                                                            <h6 class="text-success text-sm"><span class="rupee-symbol">₹</span><span class="inv-<?= $one['so_invoice_id'] ?>-advancedAmtSpan" id="inv-<?= $one['so_invoice_id'] ?>-advancedAmtSpan_<?= $log['payment_id'] ?>"><?= inputValue($log['advancedAmt']) ?></span></h6>
                                                            <input type="number" step="any" data-advancedid="<?= $log['payment_id'] ?>" placeholder="Enter amount" class="form-control inputAmountClass form-control-sm mb-2 inv-<?= $one['so_invoice_id'] ?>-advancedAmtInp" id="inv-<?= $one['so_invoice_id'] ?>-advancedAmtInp_<?= $log['payment_id'] ?>">
                                                            <span class="text-xs text-danger" id="inv-<?= $one['so_invoice_id'] ?>-advancedAmtMsg_<?= $log['payment_id'] ?>"></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-grid advance-list-cash">
                                                                <p class="text-right text-sm m-2 font-weight-bold"><?= $log['transactionId'] ?></p>
                                                                <p class="text-right text-xs m-2"><?= $documentDate ?></p>
                                                            </div>
                                                        </div>
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
    </div>


<?php } else { ?>
    <p class="text-xs text-danger">No Invoice Found!</p>
<?php } ?>