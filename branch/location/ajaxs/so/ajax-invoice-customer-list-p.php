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

$companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
$companyCurrencyData = $companyCurrencyObj["data"];

$companyId = $company_id;
$branchId = $branch_id;
$locationId = $location_id;
// console('$fetchInvoiceDetails');
// console($fetchInvoiceAmtDetails);
// console($fetchInvoiceByCustomer);

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

    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_outstanding_amount'] ?>" class="total_outstanding_amount">
    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_due_amount'] ?>" class="total_due_amount">
    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['total_overdue_amount'] ?>" class="total_overdue_amount">
    <input type="hidden" value="<?= $fetchInvoiceAmtDetails['advancedPayAmt'] ?>" class="total_advancedPay_amount">

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
            <th>Adjusted Amt. (<?= $companyCurrencyData["currency_name"] ?>)</th>
            <th>Due %</th>
            <th>Action</th>
        </tr>
        <?php
        $invRowNo = 0;
        // console($fetchInvoiceByCustomer);
        foreach ($fetchInvoiceByCustomer as $key => $one) {

            $invRowNo += 1;
            $invoicesObj = $BranchSoObj->fetchInvoiceDetails($one['so_invoice_id']);
            $invoiceData = $invoicesObj['data'];
            $customer_id = $invoiceData['customer_id'];
            $oneInvoiceId = $one['so_invoice_id'];

            $crNoreRefSqlObj = queryGet("SELECT * FROM `erp_credit_note` WHERE `company_id`=$company_id AND `branch_id`= $branch_id AND `location_id`=$location_id AND `creditors_type`='customer' AND `creditNoteReference`=$oneInvoiceId")['data'];
            $crNoteAmt = $crNoreRefSqlObj['total'];
        

            $statusLabel = fetchStatusMasterByCode($one['invoiceStatus'])['data']['label'];
            $statusClass = "";
            if ($statusLabel == "paid") {
                $statusClass = "status";
            } elseif ($statusLabel == "partial paid") {
                $statusClass = "status-warning";
            } else {
                $statusClass = "status-danger";
            }

            if ($companyCurrencyData["currency_name"] != $invoiceData["currency_name"]) {
                $currencyConverstionObj = currency_conversion($companyCurrencyData["currency_name"], $invoiceData["currency_name"]);
                $currentConverstionRate = $currencyConverstionObj["quotes"][$companyCurrencyData["currency_name"] . $invoiceData["currency_name"]] ?? $invoiceData["conversion_rate"];
            } else {
                $currentConverstionRate = $invoiceData["conversion_rate"];
            }

            $days = $one['credit_period'];

            $date = date_create($one['invoice_date']);
            date_add($date, date_interval_create_from_date_string($days . " days"));
            $creditPeriod = date_format($date, "Y-m-d");
        ?>
            <tr>
                <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][invoiceId]" value="<?= htmlspecialchars($one['so_invoice_id']) ?>">
                <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][invoiceNo]" value="<?= htmlspecialchars($one['invoice_no']) ?>">
                <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][invoiceStatus]" value="<?= htmlspecialchars($statusLabel) ?>">
                <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][creditPeriod]" value="<?= htmlspecialchars($one['credit_period']) ?>">
                <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][invAmt]" value="<?= htmlspecialchars($one['all_total_amt']) ?>">
                <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][dueAmt]" id="dueAmount_<?= htmlspecialchars($invRowNo) ?>" value="<?= htmlspecialchars($one['due_amount']) ?>">
                <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][customer_id]" value="<?= htmlspecialchars($customer_id) ?>">

                <input type="hidden" id="inputPreviousCurrencyRate_<?= htmlspecialchars($invRowNo) ?>" value="<?= htmlspecialchars($one['conversion_rate']) ?>">
                <input type="hidden" id="inputCurrentCurrencyRate_<?= htmlspecialchars($invRowNo) ?>" value="<?= htmlspecialchars($currentConverstionRate) ?>">
                <input type="hidden" id="inputInvoiceCurrencyName_<?= htmlspecialchars($invRowNo) ?>" value="<?= htmlspecialchars($one['currency_name']) ?>">
                <input type="hidden" id="inputCompanyCurrencyName_<?= htmlspecialchars($invRowNo) ?>" value="<?= htmlspecialchars($companyCurrencyData["currency_name"]) ?>">

                <td><?= htmlspecialchars($one['invoice_no']) ?></td>
                <td><span class="text-uppercase text-nowrap <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($statusLabel) ?></span></td>
                <td><?= htmlspecialchars($one['credit_period']) ?></td>
                <td class="invAmt invoiceAmt text-right" id="invoiceAmt_<?= htmlspecialchars($one['so_invoice_id']) ?>"><?= decimalValuePreview($one['all_total_amt']) ?></td>
                <td class="dueAmt text-right" id="dueAmt_<?= htmlspecialchars($one['so_invoice_id']) ?>"><?= decimalValuePreview($one['due_amount']) ?></td>

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
                            <input type="text" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][recAmt]" class="form-control receiveAmt px-3 text-right" id="receiveAmt_<?= htmlspecialchars($one['so_invoice_id']) ?>" placeholder="Enter amount" aria-label="Username" aria-describedby="basic-addon1">
                        <?php } ?>
                    </div>
                    <small style="display: none;" class="text-danger mt-n4 warningMsg" id="warningMsg_<?= htmlspecialchars($one['so_invoice_id']) ?>">Amount Exceeded</small>
                </td>
                <td>
                    <div class="input-group input-group-sm m-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?= htmlspecialchars($invoiceData["currency_name"]) ?></span>
                        </div>
                        <input type="number" step="any" id="inputInvoiceAdjustAmt_<?= htmlspecialchars($invRowNo) ?>" name="paymentInvoiceDetails[<?= htmlspecialchars($one['invoice_no']) ?>][<?= htmlspecialchars($invRowNo) ?>][paymentAdjustAMT]" class="form-control border py-3 text-right inputInvoiceAdjustAmt" placeholder="0.00" readonly>
                    </div>
                    <span id="spanInvoiceAdjustAmt_<?= htmlspecialchars($invRowNo) ?>" class="text-small spanInvoiceAdjustAmt"></span>
                    <input type="hidden" name="paymentInvoiceDetails[<?= htmlspecialchars($one['invoice_no']) ?>][<?= htmlspecialchars($invRowNo) ?>][paymentAdjustINR]" id="hiddenInvoicePayAmtAdjust_<?= htmlspecialchars($invRowNo) ?>" value="0">
                </td>
                <?php
                $due_amt = $one['due_amount'];
                $inv_amt = $one['all_total_amt'];
                $duePercentage = ($due_amt / $inv_amt) * 100;
                ?>
                <td class="duePercentage" id="duePercentage_<?= htmlspecialchars($one['so_invoice_id']) ?>"><?= round($duePercentage); ?>%</td>
                <td>
                    <a style="cursor:pointer" data-toggle="modal" class="collectActionModalBtn_<?= htmlspecialchars($invRowNo) ?>" data-id="<?= htmlspecialchars($one['so_invoice_id']) ?>" data-target="#collectActionModal_<?= htmlspecialchars($invRowNo) ?>">
                        <i class="fa fa-cog po-list-icon"></i>
                    </a>
                </td>
            </tr>

            <!-- action modal start -->
            <div class="modal fade right customer-modal classic-view-modal" id="collectActionModal_<?= htmlspecialchars($invRowNo) ?>" role="dialog" data-backdrop="true" aria-hidden="true">
                <div class="modal-dialog modal-full-height modal-right modal-notify modal-success" role="document" style="max-width: 30%;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="text-light text-nowrap">
                                <p class="text-sm my-2"><?= htmlspecialchars($invoiceData['vendorName']) ?></p>
                                <p class="text-xs my-2"><span class="text-muted">Invoice Number:</span> <?= htmlspecialchars($one['invoice_no']) ?></p>
                                <p class="text-xs my-2"><span class="text-muted">Invoice Amount:</span> <?= htmlspecialchars(decimalValuePreview($one['all_total_amt'])) ?></p>
                                <p class="text-xs my-2"><span class="text-muted">Due Amt:</span><span id='modalRemainAmt_<?= htmlspecialchars($invRowNo) ?>'> <?= htmlspecialchars(decimalValuePreview($one['due_amount'])) ?> </span> <span class="text-muted"></p>
                                <input type="hidden" name="modalDueamt" id="modalDueAmt_<?= htmlspecialchars($invRowNo) ?>" value="<?= htmlspecialchars(decimalValuePreview($one['due_amount'])) ?>">
                            </div>
                        </div>
                        <div class="modal-body p-3">
                            <div class="card mb-3">
                                <div class="card-header py-1 text-light">Round Off</div>
                                <div class="card-body py-1">
                                    <div class="d-flex gap-2 m-0 p-0">
                                        <div class="input-group input-group-sm w-50">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><?= htmlspecialchars($companyCurrencyData["currency_name"]) ?></span>
                                            </div>
                                            <select class="form-control inputRoundOffSign adjustmentInputSign" id="inputRoundOffSign_<?= htmlspecialchars($invRowNo) ?>">
                                                <option value="+"> + </option>
                                                <option value="-"> - </option>
                                            </select>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" id="inputRoundOffInr_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputRoundOffInr adjustmentInputValue" placeholder="0.00">
                                            <br><span class="text-small spanErrorAmtroundoff" id="spanErrorAmtroundoff_<?= htmlspecialchars($invRowNo) ?>"></span>
                                            <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputRoundOffInrWithSign]" id="inputRoundOffInrWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputRoundOffInrWithSign" value="0.00">
                                            <input type="hidden" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputRoundOffWithSign]" id="inputRoundOffWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputRoundOffWithSign" value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header py-1 text-light">Write Off</div>
                                <div class="card-body py-1">
                                    <div class="d-flex gap-2 m-0 p-0">
                                        <div class="input-group input-group-sm w-50">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><?= htmlspecialchars($companyCurrencyData["currency_name"]) ?></span>
                                            </div>
                                            <select id="inputWriteBackSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control inputWriteBackSign adjustmentInputSign">
                                                <option value="+"> + </option>
                                                <option value="-"> - </option>
                                            </select>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" id="inputWriteBackInr_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputWriteBackInr adjustmentInputValue" placeholder="0.00">
                                            <br><span class="text-small spanErrorAmtWriteBack" id="spanErrorAmtWriteBack_<?= htmlspecialchars($invRowNo) ?>"></span>
                                            <input type="hidden" step="any" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputWriteBackInrWithSign]" id="inputWriteBackInrWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputWriteBackInrWithSign" value="0.00">
                                            <input type="hidden" step="any" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputWriteBackWithSign]" id="inputWriteBackWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputWriteBackWithSign" value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header py-1 text-light">Financial Charges</div>
                                <div class="card-body py-1">
                                    <div class="d-flex gap-2 m-0 p-0">
                                        <div class="input-group input-group-sm w-50">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><?= htmlspecialchars($companyCurrencyData["currency_name"]) ?></span>
                                            </div>
                                            <select id="inputFinancialChargesSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control inputFinancialChargesSign adjustmentInputSign">
                                                <option value="+"> + </option>
                                                <option value="-"> - </option>
                                            </select>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" id="inputFinancialChargesInr_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputFinancialChargesInr adjustmentInputValue" placeholder="0.00">
                                            <br><span class="text-small spanErrorAmtFinancialCharges" id="spanErrorAmtFinancialCharges_<?= htmlspecialchars($invRowNo) ?>"></span>
                                            <input type="hidden" step="any" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputFinancialChargesInrWithSign]" id="inputFinancialChargesInrWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputFinancialChargesInrWithSign" value="0.00">
                                            <input type="hidden" step="any" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputFinancialChargesWithSign]" id="inputFinancialChargesWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputFinancialChargesWithSign" value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header py-1 text-light">Forex Loss/Gain</div>
                                <div class="card-body py-1">
                                    <div class="d-flex gap-2 m-0 p-0">
                                        <div class="input-group input-group-sm w-50">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><?= htmlspecialchars($companyCurrencyData["currency_name"]) ?></span>
                                            </div>
                                            <select id="inputForexLossGainSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control inputForexLossGainSign">
                                                <option value="+"> + </option>
                                                <option value="-"> - </option>
                                            </select>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" id="inputForexLossGainInr_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputForexLossGainInr" placeholder="0.00">
                                            <br><span class="text-small spanErrorAmtForexLossGain" id="spanErrorAmtForexLossGain_<?= htmlspecialchars($invRowNo) ?>"></span>
                                            <input type="hidden" step="any" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputForexLossGainInrWithSign]" id="inputForexLossGainInrWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputForexLossGainInrWithSign" value="0.00">
                                            <input type="hidden" step="any" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputForexLossGainWithSign]" id="inputForexLossGainWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputForexLossGainWithSign" value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header py-1 text-light">Total TDS</div>
                                <div class="card-body py-1">
                                    <div class="d-flex gap-2 m-0 p-0">
                                        <div class="input-group input-group-sm w-50">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><?= htmlspecialchars($companyCurrencyData["currency_name"]) ?></span>
                                            </div>
                                            <select id="inputTotalTdsSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control inputTotalTdsSign">
                                                <option value="+"> + </option>
                                                <option value="-" selected="selected"> - </option>
                                            </select>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="any" id="inputinputTotalTdsInr_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputinputTotalTdsInr adjustmentInputValue" placeholder="0.00">
                                            <br><span class="text-small spanErrorAmtTOtalTds" id="spanErrorAmtTOtalTds_<?= htmlspecialchars($invRowNo) ?>"></span>
                                            <input type="hidden" step="any" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputTotalTdsWithSign]" id="inputTotalTdsInrWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputForexLossGainInrWithSign" value="0.00">
                                            <input type="hidden" step="any" name="paymentInvDetails[<?= htmlspecialchars($customer_id) ?>][<?= htmlspecialchars($key) ?>][inputTotalTdsWithSign]" id="inputTotalTdsWithSign_<?= htmlspecialchars($invRowNo) ?>" class="form-control border py-3 text-right inputForexLossGainWithSign" value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        } ?>
    </table>

<?php } else { ?>
    <p class="text-xs text-danger">No Invoice Found!</p>
<?php } ?>
<script>
    $(document).ready(function() {

        function parseNumberWithDefault(value, defaultValue = 0) {
            let parsedValue = Number(value);
            if (isNaN(parsedValue) || !isFinite(parsedValue)) {
                parsedValue = defaultValue;
            }
            return parsedValue;
        }

        function updateAdjustmentAmount(rowNo) {

            let companyCurrencyName = $(`#inputCompanyCurrencyName_${rowNo}`).val();
            let invoiceCurrencyName = $(`#inputInvoiceCurrencyName_${rowNo}`).val();
            let previousCurrencyRate = $(`#inputPreviousCurrencyRate_${rowNo}`).val();
            let currentCurrencyRate = $(`#inputCurrentCurrencyRate_${rowNo}`).val();

            let inputInvoicePayableAmt = parseNumberWithDefault($(`#inputInvoicePayableAmt_${rowNo}`).val());
            let inputInvoicePayAmt = parseNumberWithDefault($(`#inputInvoicePayAmt_${rowNo}`).val());
            // let inputInvoiceRemainAmt = parseNumberWithDefault($(`#inputInvoiceRemainAmt_${rowNo}`).val());
            let inputInvoiceRemainAmt = inputInvoicePayableAmt - inputInvoicePayAmt;
            let inputInvoiceRemainAmtInr = inputInvoiceRemainAmt / currentCurrencyRate;

            let round_off_sign = $(`#inputRoundOffSign_${rowNo}`).val();
            let round_off_value_inr = parseNumberWithDefault($(`#inputRoundOffInr_${rowNo}`).val());
            let write_back_sign = $(`#inputWriteBackSign_${rowNo}`).val();
            let write_back_value_inr = parseNumberWithDefault($(`#inputWriteBackInr_${rowNo}`).val());
            let fin_charge_sign = $(`#inputFinancialChargesSign_${rowNo}`).val();
            let fin_charge_value_inr = parseNumberWithDefault($(`#inputFinancialChargesInr_${rowNo}`).val());
            let tds_sign = $(`#inputTotalTdsSign_${rowNo}`).val();
            let tds_sign_inr = parseNumberWithDefault($(`#inputinputTotalTdsInr_${rowNo}`).val());
            // let forex_sign = $(`#inputForexLossGainSign_${rowNo}`).val();
            // let forex_value_inr = parseNumberWithDefault($(`#inputForexLossGainInr_${rowNo}`).val());
            console.log("tds_sign_inr" + tds_sign)


            let round_off_value_inr_with_sign = round_off_sign == "+" ? round_off_value_inr : round_off_value_inr * -1;
            let write_back_value_inr_with_sign = write_back_sign == "+" ? write_back_value_inr : write_back_value_inr * -1;
            let fin_charge_value_inr_with_sign = fin_charge_sign == "+" ? fin_charge_value_inr : fin_charge_value_inr * -1;
            let tds_value_inr_with_sign = tds_sign == "+" ? tds_sign_inr : tds_sign_inr * -1;
            // let forex_value_inr_with_sign = fin_charge_sign == "+" ? forex_value_inr : forex_value_inr * -1;

            let round_off_value_with_sign = round_off_value_inr_with_sign * currentCurrencyRate;
            let write_back_value_with_sign = write_back_value_inr_with_sign * currentCurrencyRate;
            let fin_charge_value_with_sign = fin_charge_value_inr_with_sign * currentCurrencyRate;
            let tds_value_with_sign = tds_value_inr_with_sign * currentCurrencyRate;
            // let forex_value_with_sign = forex_value_inr_with_sign * currentCurrencyRate;


            $(`#inputRoundOffInrWithSign_${rowNo}`).val(round_off_value_inr_with_sign);
            $(`#inputRoundOffWithSign_${rowNo}`).val(round_off_value_with_sign);

            $(`#inputWriteBackInrWithSign_${rowNo}`).val(write_back_value_inr_with_sign);
            $(`#inputWriteBackWithSign_${rowNo}`).val(write_back_value_with_sign);

            $(`#inputFinancialChargesInrWithSign_${rowNo}`).val(fin_charge_value_inr_with_sign);
            $(`#inputFinancialChargesWithSign_${rowNo}`).val(fin_charge_value_with_sign);

            $(`#inputTotalTdsInrWithSign_${rowNo}`).val(tds_value_inr_with_sign);
            $(`#inputTotalTdsWithSign_${rowNo}`).val(tds_value_with_sign);

            // $(`#inputForexLossGainInrWithSign_${rowNo}`).val(forex_value_inr_with_sign);
            // $(`#inputForexLossGainWithSign_${rowNo}`).val(forex_value_with_sign);

            // let totalAdjustedAmount = forex_value_with_sign+fin_charge_value_with_sign+write_back_value_with_sign+round_off_value_with_sign;
            let totalAdjustedAmount = fin_charge_value_with_sign + write_back_value_with_sign + round_off_value_with_sign + tds_value_with_sign;
            // let totalAdjustedAmountInr = forex_value_inr+fin_charge_value_inr+write_back_value_inr+round_off_value_inr;
            let totalAdjustedAmountInr = fin_charge_value_inr_with_sign + write_back_value_inr_with_sign + round_off_value_inr_with_sign + tds_value_inr_with_sign;
            let dueAmountStr = ($(`#modalDueAmt_${rowNo}`).val());
            let dueAmount = parseFloat(dueAmountStr.replace(/,/g, ''));
            let newDueAmount = dueAmount + totalAdjustedAmount;
            // console.log("totalAdjustedAmount=>" + totalAdjustedAmount)

            $(`#hiddenInvoicePayAmtAdjust_${rowNo}`).val(totalAdjustedAmountInr.toFixed(2));
            if (companyCurrencyName != invoiceCurrencyName) {
                $(`#spanInvoiceAdjustAmt_${rowNo}`).html(`${companyCurrencyName}: ${totalAdjustedAmountInr.toFixed(2)}`);
            }
            $(`#inputInvoiceAdjustAmt_${rowNo}`).val((totalAdjustedAmount.toFixed(2)));
            $(`.total_advancedPay_amount`).val((totalAdjustedAmount.toFixed(2)));

            $(`#inputInvoiceRemainAmt_${rowNo}`).val((inputInvoiceRemainAmt + totalAdjustedAmount).toFixed(2));
            $(`#modalRemainAmt_${rowNo}`).html((newDueAmount).toFixed(2));
            // $(`#dueAmount_${rowNo}`).val((newDueAmount).toFixed(2));
            $(`#hiddenInvoicePayAmtRemain_${rowNo}`).val((inputInvoiceRemainAmtInr + totalAdjustedAmountInr).toFixed(2));

        }


        $(document).on("keyup", ".adjustmentInputValue", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            updateAdjustmentAmount(rowNo);
        });
        $(document).on("change", ".adjustmentInputSign", function() {
            let rowNo = ($(this).attr("id")).split("_")[1];
            updateAdjustmentAmount(rowNo);
        });

        function calculateVendorPaymentForm() {
            console.log("Updating all fields");
            let grandTotalRemainAmt = 0;
            let grandTotalPayAmt = 0;
            //reading each row
            $(".inputInvoicePayAmt").each(function() {

                let invRowNo = ($(this).attr("id")).split("_")[1];

                let companyCurrencyName = $(`#inputCompanyCurrencyName_${invRowNo}`).val();
                let invoiceCurrencyName = $(`#inputInvoiceCurrencyName_${invRowNo}`).val();
                let previousCurrencyRate = $(`#inputPreviousCurrencyRate_${invRowNo}`).val();
                let currentCurrencyRate = $(`#inputCurrentCurrencyRate_${invRowNo}`).val();

                let invAmt = parseNumberWithDefault($(`#inputInvoiceAmt_${invRowNo}`).val(), 0);
                let invPayableAmt = parseNumberWithDefault($(`#inputInvoicePayableAmt_${invRowNo}`).val(), 0);
                let invPayAmt = parseNumberWithDefault($(`#inputInvoicePayAmt_${invRowNo}`).val(), 0);

                let invAdjustAmt = parseNumberWithDefault($(`#inputInvoiceAdjustAmt_${invRowNo}`).val(), 0);
                // let invINRPayAmt = parseNumberWithDefault($(`#hiddenInvoicePayAmt_${invRowNo}`).val(), 0);
                let invRemainAmt = (invPayableAmt - invPayAmt) + invAdjustAmt;
                let invINRPayAmt = parseNumberWithDefault(invPayAmt / currentCurrencyRate, 0);
                let invINRPayaybleAmt = parseNumberWithDefault(invPayableAmt / currentCurrencyRate, 0);
                let invINRPayAmtRemain = parseNumberWithDefault((invRemainAmt / currentCurrencyRate), 0);

                if (invPayAmt > invPayableAmt) {
                    $(`#spanInvoicePayAmt_${invRowNo}`).html(`<span class="text-danger">Amount exist!</span>`);
                    $(`#inputInvoicePayAmt_${invRowNo}`).val(0);
                    $(`#inputInvoiceRemainAmt_${invRowNo}`).val(invPayableAmt);
                    $(`#modalRemainAmt_${invRowNo}`).html(invPayableAmt);
                    invPayAmt = 0;
                    invINRPayAmt = 0;
                    invRemainAmt = invPayableAmt;
                    invINRPayAmtRemain = invINRPayaybleAmt;
                } else {
                    $(`#spanInvoicePayAmt_${invRowNo}`).html('');
                    $(`#inputInvoiceRemainAmt_${invRowNo}`).val(invRemainAmt.toFixed(2));
                    $(`#modalRemainAmt_${invRowNo}`).html(invRemainAmt.toFixed(2));
                }
                grandTotalPayAmt += invINRPayAmt;
                grandTotalRemainAmt += invINRPayAmtRemain;

                //Calculating the round of, write back and forex loss gain.
                if (companyCurrencyName == invoiceCurrencyName) {
                    // inputRoundOff_
                    // inputRoundOffSign_
                    // inputWriteBack_
                    // inputWriteBackSign_
                    // inputFinancialCharges_
                    // inputFinancialChargesSign_
                    // inputForexLossGain_
                    // inputForexLossGainSign_

                    // spanInvoiceAmt_
                    // spanInvoicePayableAmt_
                    // spanInvoicePayAmt_
                    // spanInvoiceRemainAmt_
                    $(`#inputForexLossGain_${invRowNo}`).prop("disabled", true);
                    $(`#inputForexLossGainSign_${invRowNo}`).prop("disabled", true);
                    $(`#hiddenInvoicePayAmt_${invRowNo}`).val(`${invINRPayAmt}`);
                    $(`#hiddenInvoicePayAmtRemain_${invRowNo}`).val(`${invINRPayAmtRemain}`);

                    if (invRemainAmt > 0 && invRemainAmt < 1) {
                        $(`#inputRoundOff_${invRowNo}`).val(invRemainAmt.toFixed(2));
                        $(`#inputRoundOffSign_${invRowNo}`).val("+");
                    }

                } else {
                    $(`#inputForexLossGain_${invRowNo}`).prop("disabled", false);
                    $(`#inputForexLossGainSign_${invRowNo}`).prop("disabled", false);

                    $(`#spanInvoiceAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(invAmt/previousCurrencyRate).toFixed(2)}`);
                    $(`#spanInvoicePayableAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(invPayableAmt/previousCurrencyRate).toFixed(2)}`);
                    $(`#spanInvoicePayAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(invINRPayAmt).toFixed(2)}`);
                    $(`#spanInvoiceRemainAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(invINRPayAmtRemain).toFixed(2)}`);

                    $(`#hiddenInvoicePayAmt_${invRowNo}`).val(`${invINRPayAmt}`);
                    $(`#hiddenInvoicePayAmtRemain_${invRowNo}`).val(`${invINRPayAmtRemain}`);

                    if (previousCurrencyRate != currentCurrencyRate) {
                        let forexLossGainAmt = (invPayAmt / currentCurrencyRate) - (invPayAmt / previousCurrencyRate);
                        $(`#inputForexLossGain_${invRowNo}`).val(Math.abs(forexLossGainAmt).toFixed(2));
                        $(`#inputForexLossGainInr_${invRowNo}`).val(Math.abs(forexLossGainAmt).toFixed(2));
                        $(`#inputForexLossGainInrWithSign_${invRowNo}`).val(Math.abs(forexLossGainAmt).toFixed(2));
                        let forex_other_currency = forexLossGainAmt * currentCurrencyRate;
                        $(`#inputForexLossGainWithSign_${invRowNo}`).val(Math.abs(forex_other_currency).toFixed(2));
                        if (forexLossGainAmt > 0) {
                            //loss
                            $(`#inputForexLossGainSign_${invRowNo}`).val("-");
                            // $(`#inputInvoiceAdjustAmt_${invRowNo}`).val(in_current_currency_adj * (-1));
                            // $(`#hiddenInvoicePayAmtAdjust_${invRowNo}`).val(forexLossGainAmt * (-1));
                            // $(`#spanInvoiceAdjustAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(forexLossGainAmt * (-1)).toFixed(2)}`);

                        } else if (forexLossGainAmt < 0) {
                            //gain
                            $(`#inputForexLossGainSign_${invRowNo}`).val("+");
                            // $(`#inputInvoiceAdjustAmt_${invRowNo}`).val(in_current_currency_adj);
                            // $(`#hiddenInvoicePayAmtAdjust_${invRowNo}`).val(forexLossGainAmt);
                            // $(`#spanInvoiceAdjustAmt_${invRowNo}`).html(`${companyCurrencyName}: ${(forexLossGainAmt).toFixed(2)}`);
                        } else {
                            $(`#inputForexLossGain_${invRowNo}`).prop("disabled", true);
                            $(`#inputForexLossGainSign_${invRowNo}`).prop("disabled", true);
                        }
                    }
                }

            });

            console.log(grandTotalPayAmt.toFixed(2), grandTotalRemainAmt);
            $(`#inputTotalPaymentAmount`).val(grandTotalPayAmt.toFixed(2));
            $(`#inputTotalRemainAmount`).val(grandTotalRemainAmt.toFixed(2));
        }
        calculateVendorPaymentForm();


    })
</script>