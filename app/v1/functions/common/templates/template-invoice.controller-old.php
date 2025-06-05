<?php
class TemplateInvoiceController
{
    private $company_id, $branch_id, $location_id, $created_by, $updated_by;
    function __construct()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
    }


    // print invoice
    public function printInvoice($invoiceId = 0, $templateId = 0, $redirectUrl = "")
    {
        $branchSoObj = new BranchSo();
        $invoiceDetailsObj = $branchSoObj->fetchBranchSoInvoiceById($invoiceId);
        // console($invoiceDetailsObj);

        if (count($invoiceDetailsObj['data']) <= 0) {
            echo '<p class="text-warning text-center mt-5">Invoice Not found!</p>';
            // if ($redirectUrl != "") {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!", $redirectUrl);
            // } else {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!");
            // }
        } else {
            $invoiceDetails = $invoiceDetailsObj['data'][0];
            $invoiceItemDetails = $branchSoObj->fetchBranchSoInvoiceItems($invoiceId)['data'];
            // console($invoiceDetails);
            // console($invoiceItemDetails);


            // fetch company config
            $companyConfigDetails = $branchSoObj->fetchCompanyConfig($invoiceDetails['companyConfigId'])['data'];

            // fetch company data
            $companyData = unserialize($invoiceDetails['companyDetails']);
            $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
            $companyCurrencyIcon = $currencyDetails['currency_icon'];
            $companyCurrencyName = $currencyDetails['currency_name'];

            // company bank details
            $company_bank_details = unserialize($invoiceDetails['company_bank_details']);

            // fetch customer data
            $customerData = unserialize($invoiceDetails['customerDetails']);
            $customerCurrencyName = $invoiceDetails['currency_name'] ?? "";
            $currencyConversionRate = $invoiceDetails['conversion_rate'] != "" ? $invoiceDetails['conversion_rate'] : 1;

            // fetch item details by HSN
            $invoiceItemDetailsGroupByHSN = $branchSoObj->fetchBranchSoInvoiceItemsGroupByHSN($invoiceId)['data'];

            // fetch attachments
            $attachmentObj = $branchSoObj->getInvoiceAttachments($invoiceId);

            if ($templateId == 0) { ?>
                <style>
                    .text-small {
                        font-size: 0.8em;
                    }
                </style>
                <div class="card classic-view bg-transparent">
                    <div class="card-body classic-view-so-table" style="overflow: auto;">
                        <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print();">Print</button> -->
                        <div class="printable-view">
                            <h3 class="h3-title text-center font-bold text-sm">Tax Invoice</h3>
                            <?php if ($invoiceDetails['compInvoiceType'] == 'CBW' || $invoiceDetails['compInvoiceType'] == 'LUT' || $invoiceDetails['compInvoiceType'] == 'SEWOP') { ?>
                                <p class="text-center ">(SUPPLY MEANT FOR EXPORT/SUPPLY TO SEZ UNIT OR SEZ DEVELOPER FOR AUTHORISED OPERATIONS UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF IGST)</p>
                            <?php } ?>
                            <table class="classic-view table-bordered tableBorder">
                                <tbody>
                                    <?php
                                    if ($invoiceDetails['irn'] != "") {
                                    ?>
                                        <tr>
                                            <td colspan="12" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= COMP_STORAGE_URL . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                    </div>
                                                    <div class="invoice-qr">
                                                        <!-- <img width="200" src="" alt="QRCode"> -->
                                                        <div id="eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>"></div>
                                                    </div>
                                                </div>
                                                <script>
                                                    $(document).ready(function() {
                                                        new QRCode("eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>", "<?= $invoiceDetails['signed_qr_code'] ?>");
                                                        $("#eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>").removeAttr("title");
                                                    });
                                                </script>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td rowspan="3" colspan="7" class="px-2">
                                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= COMP_STORAGE_URL . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                            <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                            <p><?= $companyData['location_building_no'] ?></p>
                                            <p><?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                            <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                            <p>Company's PAN: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                            <?php if ($companyConfigDetails['email'] != "") { ?>
                                                <p>E-Mail : <?= $companyConfigDetails['email'] ?></p>
                                            <?php } ?>
                                            <?php if ($companyConfigDetails['phone'] != "") { ?>
                                                <p>Phone : <?= $companyConfigDetails['phone'] ?></p>
                                            <?php } ?>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <p>Invoice No.</p>
                                            <p class="font-bold"><?= $invoiceDetails['invoice_no'] ?></p>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <p>Dated</p>
                                            <p class="font-bold"><?php $invDate = date_create($invoiceDetails['invoice_date']);
                                                                    echo date_format($invDate, "F d,Y"); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-2">
                                            <p>Mode/Terms of Payment</p>
                                            <?php if ($invoiceDetails['credit_period'] != "") { ?>
                                                <p><?= $invoiceDetails['credit_period'] ?></p>
                                            <?php } ?>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <p>Dispatch Doc No.</p>
                                            <?php if ($invoiceDetails['pgi_no'] != "") { ?>
                                                <p><?= $invoiceDetails['pgi_no'] ?></p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td colspan="3" class="px-2">
                                    <p>Buyer’s Order No.</p>
                                    <?php if ($invoiceDetails['po_number'] != "") { ?>
                                        <p><?= $invoiceDetails['po_number'] ?></p>
                                    <?php } ?>
                                </td>
                                <td colspan="3" class="px-2">
                                    <p>Dated</p>
                                    <?php if ($invoiceDetails['po_date'] != "") { ?>
                                        <p><?= $invoiceDetails['po_date'] ?></p>
                                    <?php } ?>
                                </td> -->
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Buyer (Bill to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_billing_address'] ?></p>
                                            <p>GSTIN/UIN : <?= $customerData['customer_gstin'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                        </td>
                                        <td colspan="6" class="px-2">
                                            <p>Consignee (Ship to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_shipping_address'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                            <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $branchGstin = substr($companyData['branch_gstin'], 0, 2);
                                    $customerGstCode = substr($customerData['customer_gstin'], 0, 2);

                                    $gstCode = 0;
                                    if ($customerGstCode == "") {
                                        $gstCode = $invoiceDetails['placeOfSupply'] ?? 0;
                                    } else {
                                        $gstCode = substr($customerData['customer_gstin'], 0, 2);
                                    }

                                    $conditionGST = $branchGstin == $gstCode;
                                    ?>
                                    <tr>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Sl No.</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Particulars</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Quantity</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">UOM</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Rate</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Discount</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="3">IGST</th>
                                        <?php } ?>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                    </tr>
                                    <tr>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle">Amount</th>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle">Amount</th>
                                        <?php } else { ?>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle" colspan="2">Amount</th>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    $totalTaxAmt = 0;
                                    $subTotalAmt = 0;
                                    $allSubTotalAmt = 0;
                                    $totalDiscountAmt = 0;
                                    $totalCashDiscountAmt = 0;
                                    $totalAmt = 0;
                                    foreach ($invoiceItemDetails as $key => $item) {
                                        $uomName = getUomDetail($item['uom'])['data']['uomName'];
                                        // $uomObj = $ItemsObj->getBaseUnitMeasureById($item['uom']);
                                        // $uomName = $uomObj['data']['uomName'];

                                        $totalTaxAmt += $item['totalTax'];
                                        $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $totalCashDiscountAmt += $item['cashDiscountAmount'];
                                        $subTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];



                                        $singleItemTotalDiscount = $item['totalDiscountAmt'] + $item['cashDiscountAmount'];
                                        $singleItemBaseAmount = $item['unitPrice'] * $item['qty'];
                                        $singleItemTaxableAmount = $singleItemBaseAmount - $singleItemTotalDiscount;


                                        // print Batch Number


                                        $batchSql = "SELECT stockLog.logRef, SUM(stockLog.itemQty) AS itemQty FROM `erp_inventory_stocks_log` AS stockLog WHERE stockLog.companyId = $this->company_id AND stockLog.branchId = $this->branch_id AND stockLog.locationId=$this->location_id AND stockLog.itemId = '" . $item['inventory_item_id'] . "' AND stockLog.refNumber = '" . $invoiceDetails['invoice_no'] . "' GROUP BY stockLog.logRef, stockLog.itemQty;";

                                        $batchQuery = queryGet($batchSql, true);
                                        $batch = "";

                                        if ($batchQuery['status'] == 'success') {
                                            // $batch = $batchQuery['data'];

                                            $logRefs = array_column($batchQuery['data'], 'logRef');
                                            $itemQtys = array_column($batchQuery['data'], 'itemQty');

                                            $batch = implode(' || ', array_map(function ($logRef, $itemQty) {
                                                // return "$logRef: $itemQty";
                                                return "$logRef";

                                            }, $logRefs, $itemQtys));

                                            // echo $batch;
                                        }



                                        // console($batch);


                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                                <p class="pre-normal">Batch No <span class=""><?= $batch?></span></p>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $item['hsnCode'] ?></p>
                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= $item['invoiceQty'] ?></p>
                                                <?php } else { ?>
                                                    <p><?= $item['qty'] ?></p>
                                                <?php } ?>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= number_format($item['unitPrice'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['unitPrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>

                                            <!-- Discount amount -->
                                            <td class="text-right px-2">
                                                <p><?= number_format($singleItemTotalDiscount, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format(($singleItemTotalDiscount * $currencyConversionRate), 2) ?></small>
                                                <?php } ?>
                                            </td>

                                            <!-- Taxable amount -->
                                            <td class="text-right px-2">
                                                <p><?= number_format($singleItemTaxableAmount, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format(($singleItemTaxableAmount * $currencyConversionRate), 2) ?></small>
                                                <?php } ?>
                                            </td>

                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($itemGstAmt, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmt * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($itemGstAmt, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmt * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2">
                                                    <p class=" font-bold"><?= $item['tax'] ?>%</p>
                                                </td>
                                                <td class="px-2" colspan="2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($item['totalTax'], 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2">
                                                <p><?= number_format($item['totalPrice'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalPrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php
                                    } ?>
                                    <tr>
                                        <td colspan="11" class="font-bold text-right px-2">
                                            <p>Sub Total (<?= $companyCurrencyName ?>)</p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted">Sub Total (<?= $customerCurrencyName ?>)</small>
                                            <?php } ?>
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p>Trade Discount (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Discount (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($totalCashDiscountAmt > 0) { ?>
                                                <p>Cash Discount (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Discount (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($invoiceDetails['total_tax_amt'] > 0) { ?>
                                                <?php if ($conditionGST || $gstCode == "") { ?>
                                                    <p>Total CGST (<?= $companyCurrencyName ?>)</p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted">Total CGST (<?= $customerCurrencyName ?>)</small>
                                                    <?php } ?>
                                                    <p>Total SGST (<?= $companyCurrencyName ?>)</p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted">Total SGST (<?= $customerCurrencyName ?>)</small>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <p>Total IGST (<?= $companyCurrencyName ?>)</p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted">Total IGST (<?= $customerCurrencyName ?>)</small>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>Round Off (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Round Off (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
                                            <p>Grand Total (<?= $companyCurrencyName ?>)</p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted">Grand Total (<?= $customerCurrencyName ?>)</small>
                                            <?php } ?>
                                        </td>
                                        <td colspan="2" class="text-right font-bold px-2">
                                            <p><span class="rupee-symbol pr-1"></span><?= number_format($subTotalAmt, 2) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= number_format($subTotalAmt * $currencyConversionRate, 2) ?></small>
                                            <?php } ?>
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= number_format($totalDiscountAmt, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= number_format($totalDiscountAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($totalCashDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= number_format($totalCashDiscountAmt, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= number_format($totalCashDiscountAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($invoiceDetails['total_tax_amt'] > 0) { ?>
                                                <?php if ($conditionGST || $gstCode == "") { ?>
                                                    <p><span class="pr-1"></span><?= number_format($invoiceDetails['cgst'], 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= number_format($invoiceDetails['cgst'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                    <p><span class="pr-1"></span><?= number_format($invoiceDetails['sgst'], 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= number_format($invoiceDetails['sgst'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <p><span class="pr-1"></span><?= number_format($invoiceDetails['igst'], 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= number_format($invoiceDetails['igst'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= number_format(abs($invoiceDetails['adjusted_amount']), 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= number_format($invoiceDetails['adjusted_amount'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
                                            <p><?= number_format($invoiceDetails['all_total_amt'], 2) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2) ?></small>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">HSN/SAC</th>
                                        <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Taxable Value</th>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">Central Tax</th>
                                            <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">State Tax</th>
                                        <?php } else { ?>
                                            <th colspan="3" class="text-bold text-center invoiceHSNTableHeadStyle">IGST</th>
                                        <?php } ?>
                                        <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Total Tax Amount</th>
                                    </tr>
                                    <tr>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                        <?php } else { ?>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle" colspan="2">Amount</th>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $totalTaxableValue = 0;
                                    $totalCgstSgstAmt = 0;
                                    $allTotalTaxAmt = 0;
                                    foreach ($invoiceItemDetailsGroupByHSN as $key => $item) {
                                        $itemGstPerHSN = $item['tax'] / 2;
                                        $itemGstAmtHSN = $item['totalTax'] / 2;
                                        $totalTaxableValue += $item['basePrice'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="3" class="px-2">
                                                <p class="invoiceSmallFont"><?= $item['hsnCode'] ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($item['basePrice'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['basePrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $item['tax'] ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= number_format($item['totalTax'], 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($item['totalTax'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= number_format($totalTaxableValue, 2) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalTaxableValue * $currencyConversionRate, 2) ?></small>
                                            <?php } ?>
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                        <td colspan="3" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt, 2) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></small>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Amount Chargeable (in words)</p>
                                            <p class="font-bold"><?= $companyCurrencyName . " " . number_to_words_indian_rupees($invoiceDetails['all_total_amt']); ?> ONLY</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2)) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <td colspan="6" class="px-2">
                                            <p class=" text-right">E. & O.E</p>
                                            <p>Company’s Bank Details</p>
                                            <div class="d-flex">
                                                <p>Bank Name :</p>
                                                <p class="font-bold"><?= $company_bank_details['bank_name'] ?></p>
                                            </div>
                                            <div class="d-flex">
                                                <p>A/c No. :</p>
                                                <p class="font-bold"><?= $company_bank_details['account_no'] ?></p>
                                            </div>
                                            <div class="d-flex">
                                                <p>Branch & IFS Code :</p>
                                                <p class="font-bold"><?= $company_bank_details['ifsc_code'] ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Remarks: <?= $invoiceDetails['remarks'] ?></p>
                                            <p>Declaration: <?= $invoiceDetails['declaration_note'] ?></p>
                                            <!-- <p><?= $companyData['company_footer'] ?></p> -->
                                            <p>Created By: <strong><?= getCreatedByUser($invoiceDetails['created_by']); ?></strong></p>
                                            <?php if ($attachmentObj['status'] == 'success') { ?>
                                                <a href="<?= COMP_STORAGE_URL . '/others/' ?><?= $attachmentObj['data']['file_name'] ?>" target="_blank" class="text-primary font-bold text-decoration-none text-decoration-underline" download>
                                                    View Attachment
                                                </a>
                                            <?php } ?>
                                        </td>
                                        <td colspan="6" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <p class="text-center sign-img">
                                                <img width="160" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="signature">
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            } else if ($templateId == 1) { ?>
                <div class="card classic-view bg-transparent">
                    <div class="card-body classic-view-so-table " style="overflow: auto;">
                        <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print();">Print</button> -->
                        <div class="printable-view">
                            <h3 class="h3-title text-center font-bold text-sm mb-4">Tax Invoice</h3>
                            <?php if ($invoiceDetails['compInvoiceType'] == 'CBW' || $invoiceDetails['compInvoiceType'] == 'LUT' || $invoiceDetails['compInvoiceType'] == 'SEWOP') { ?>
                                <p class="text-center ">(SUPPLY MEANT FOR EXPORT/SUPPLY TO SEZ UNIT OR SEZ DEVELOPER FOR AUTHORISED OPERATIONS UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF IGST)</p>
                            <?php } ?>
                            <table class="classic-view table-bordered tableBorder">
                                <tbody>
                                    <?php
                                    if ($invoiceDetails['irn'] != "") {
                                    ?>
                                        <tr>
                                            <td colspan="12" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= COMP_STORAGE_URL . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                    </div>
                                                    <div class="invoice-qr">
                                                        <!-- <img width="200" src="" alt="QRCode"> -->
                                                        <div id="eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>"></div>
                                                    </div>
                                                </div>
                                                <script>
                                                    $(document).ready(function() {
                                                        new QRCode("eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>", "<?= $invoiceDetails['signed_qr_code'] ?>");
                                                        $("#eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>").removeAttr("title");
                                                    });
                                                </script>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td rowspan="3" colspan="7" class="px-2">
                                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= COMP_STORAGE_URL . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                            <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                            <p><?= $companyData['location_building_no'] ?></p>
                                            <p><?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                            <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                            <p>Company's PAN: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                            <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <p>Invoice No.</p>
                                            <p class="font-bold"><?= $invoiceDetails['invoice_no'] ?></p>
                                        </td>
                                        <td colspan="4" class="px-2">
                                            <p>Dated</p>
                                            <p class="font-bold"><?php $invDate = date_create($invoiceDetails['invoice_date']);
                                                                    echo date_format($invDate, "F d,Y"); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-2">
                                            <p>Mode/Terms of Payment</p>
                                            <?php if ($invoiceDetails['credit_period'] != "") { ?>
                                                <p><?= $invoiceDetails['credit_period'] ?></p>
                                            <?php } ?>
                                        </td>
                                        <td colspan="4" class="px-2">
                                            <p>Dispatch Doc No.</p>
                                            <?php if ($invoiceDetails['pgi_no'] != "") { ?>
                                                <p><?= $invoiceDetails['pgi_no'] ?></p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td colspan="3" class="px-2">
                                    <p>Buyer’s Order No.</p>
                                    <?php if ($invoiceDetails['po_number'] != "") { ?>
                                        <p><?= $invoiceDetails['po_number'] ?></p>
                                    <?php } ?>
                                </td>
                                <td colspan="3" class="px-2">
                                    <p>Dated</p>
                                    <?php if ($invoiceDetails['po_date'] != "") { ?>
                                        <p><?= $invoiceDetails['po_date'] ?></p>
                                    <?php } ?>
                                </td> -->
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Buyer (Bill to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_billing_address'] ?></p>
                                            <p>GSTIN/UIN : <?= $customerData['customer_gstin'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                        </td>
                                        <td colspan="7" class="px-2">
                                            <p>Consignee (Ship to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_shipping_address'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                            <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $branchGstin = substr($companyData['branch_gstin'], 0, 2);
                                    $customerGstCode = substr($customerData['customer_gstin'], 0, 2);

                                    $gstCode = 0;
                                    if ($customerGstCode == "") {
                                        $gstCode = $invoiceDetails['placeOfSupply'] ?? 0;
                                    } else {
                                        $gstCode = substr($customerData['customer_gstin'], 0, 2);
                                    }

                                    $conditionGST = $branchGstin == $gstCode;
                                    ?>
                                    <tr>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Sl No.</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Particulars</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Quantity</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">UOM</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">MRP</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Trade Discount</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Gross Amt.</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Cash Discount</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="3">IGST</th>
                                        <?php } ?>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                    </tr>
                                    <tr>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle">Amount</th>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle">Amount</th>
                                        <?php } else { ?>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle" colspan="2">Amount</th>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    $totalTaxAmt = 0;
                                    $subTotalAmt = 0;
                                    $allSubTotalAmt = 0;
                                    $totalDiscountAmt = 0;
                                    $totalAmt = 0;
                                    foreach ($invoiceItemDetails as $key => $item) {
                                        $uomName = getUomDetail($item['uom'])['data']['uomName'];
                                        // $uomObj = $ItemsObj->getBaseUnitMeasureById($item['uom']);
                                        // $uomName = $uomObj['data']['uomName'];

                                        $totalTaxAmt += $item['totalTax'];
                                        $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $subTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];
                                        $taxableAmount = ($item['unitPrice'] * $item['qty']) - $item['totalDiscountAmt'] - $item['cashDiscountAmount'];
                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $item['hsnCode'] ?></p>
                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= $item['invoiceQty'] ?></p>
                                                <?php } else { ?>
                                                    <p><?= $item['qty'] ?></p>
                                                <?php } ?>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= number_format($item['unitPrice'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['unitPrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?= $item['totalDiscount'] ?>%)</span><?= number_format($item['totalDiscountAmt'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalDiscountAmt'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= ($item['unitPrice'] * $item['qty']) - $item['totalDiscountAmt'] ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?= $item['cashDiscount'] ?>%)</span> <?= number_format($item['cashDiscountAmount'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['cashDiscountAmount'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= $taxableAmount ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format(($item['unitPrice'] * $item['qty']) * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($itemGstAmt, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmt * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($itemGstAmt, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmt * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2">
                                                    <p class=" font-bold"><?= $item['tax'] ?>%</p>
                                                </td>
                                                <td class="px-2" colspan="2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($item['totalTax'], 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2">
                                                <p><?= number_format($item['totalPrice'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalPrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="12" class="font-bold text-right px-2">
                                            <p>Sub Total (<?= $companyCurrencyName ?>)</p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted">Sub Total (<?= $customerCurrencyName ?>)</small>
                                            <?php } ?>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <p>Total CGST (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Total CGST (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                                <p>Total SGST (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Total SGST (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <p>Total IGST (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Total IGST (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>Round Off (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Round Off (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
                                            <p>Grand Total (<?= $companyCurrencyName ?>)</p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted">Grand Total (<?= $customerCurrencyName ?>)</small>
                                            <?php } ?>
                                        </td>
                                        <td colspan="2" class="text-right font-bold px-2">
                                            <p><span class="rupee-symbol pr-1"></span><?= number_format($subTotalAmt, 2) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= number_format($subTotalAmt * $currencyConversionRate, 2) ?></small>
                                            <?php } ?>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <p><span class="pr-1"></span><?= number_format($invoiceDetails['cgst'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= number_format($invoiceDetails['cgst'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                                <p><span class="pr-1"></span><?= number_format($invoiceDetails['sgst'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= number_format($invoiceDetails['sgst'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <p><span class="pr-1"></span><?= number_format($invoiceDetails['igst'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= number_format($invoiceDetails['igst'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            <?php } ?>

                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= number_format(abs($invoiceDetails['adjusted_amount']), 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= number_format($invoiceDetails['adjusted_amount'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
                                            <p><?= number_format($invoiceDetails['all_total_amt'], 2) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2) ?></small>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>

                                <tbody>
                                    <tr>
                                        <th colspan="4" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">HSN/SAC</th>
                                        <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Taxable Value</th>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">Central Tax</th>
                                            <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">State Tax</th>
                                        <?php } else { ?>
                                            <th colspan="3" class="text-bold text-center invoiceHSNTableHeadStyle">IGST</th>
                                        <?php } ?>
                                        <th colspan="4" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Total Tax Amount</th>
                                    </tr>
                                    <tr>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                        <?php } else { ?>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle" colspan="2">Amount</th>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $totalTaxableValue = 0;
                                    $totalCgstSgstAmt = 0;
                                    $allTotalTaxAmt = 0;
                                    foreach ($invoiceItemDetailsGroupByHSN as $key => $item) {
                                        $itemGstPerHSN = $item['tax'] / 2;
                                        $itemGstAmtHSN = $item['totalTax'] / 2;
                                        $totalTaxableValue += $item['basePrice'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="4" class="px-2">
                                                <p class="invoiceSmallFont"><?= $item['hsnCode'] ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($item['basePrice'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['basePrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $item['tax'] ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= number_format($item['totalTax'], 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td colspan="4" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($item['totalTax'], 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="4">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= number_format($totalTaxableValue, 2) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalTaxableValue * $currencyConversionRate, 2) ?></small>
                                            <?php } ?>
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt, 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                        <td colspan="4" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt, 2) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></small>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Amount Chargeable (in words)</p>
                                            <p class="font-bold"><?= $companyCurrencyName . " " . number_to_words_indian_rupees($invoiceDetails['all_total_amt']); ?> ONLY</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2)) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <td colspan="7" class="px-2">
                                            <p class=" text-right">E. & O.E</p>
                                            <p>Company’s Bank Details</p>
                                            <div class="d-flex">
                                                <p>Bank Name :</p>
                                                <p class="font-bold"><?= $company_bank_details['bank_name'] ?></p>
                                            </div>
                                            <div class="d-flex">
                                                <p>A/c No. :</p>
                                                <p class="font-bold"><?= $company_bank_details['account_no'] ?></p>
                                            </div>
                                            <div class="d-flex">
                                                <p>Branch & IFS Code :</p>
                                                <p class="font-bold"><?= $company_bank_details['ifsc_code'] ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Remarks: <?= $invoiceDetails['remarks'] ?></p>
                                            <p>Declaration: <?= $invoiceDetails['declaration_note'] ?></p>
                                            <!-- <p><?= $companyData['company_footer'] ?></p> -->
                                            <p>Created By: <strong><?= getCreatedByUser($invoiceDetails['created_by']); ?></strong></p>
                                        </td>
                                        <td colspan="7" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <p class="text-center sign-img">
                                                <img width="160" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="">
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            } else if ($templateId == 2) { ?>
                <h2>This is template 2</h2>
            <?php }
        }
    }

    // print customer invoice
    public function printCustomerInvoice($invoiceId = 0, $templateId = 0, $redirectUrl = "")
    {
        $branchSoObj = new BranchSo();
        $invoiceDetailsObj = $branchSoObj->fetchBranchSoInvoiceById($invoiceId);

        if (count($invoiceDetailsObj['data']) <= 0) {
            echo '<p class="text-warning text-center mt-5">Invoice Not found!</p>';
            // if ($redirectUrl != "") {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!", $redirectUrl);
            // } else {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!");
            // }
        } else {
            $invoiceDetails = $invoiceDetailsObj['data'][0];
            $invoiceItemDetails = $branchSoObj->fetchBranchSoInvoiceItems($invoiceId)['data'];

            // fetch company data
            $companyData = unserialize($invoiceDetails['companyDetails']);
            $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
            $companyCurrencyIcon = $currencyDetails['currency_icon'];
            $companyCurrencyName = $currencyDetails['currency_name'];

            // company bank details
            $company_bank_details = unserialize($invoiceDetails['company_bank_details']);

            // fetch customer data
            $customerData = unserialize($invoiceDetails['customerDetails']);
            $customerCurrencyName = $invoiceDetails['currency_name'] ?? "";
            $currencyConversionRate = $invoiceDetails['conversion_rate'] != "" ? $invoiceDetails['conversion_rate'] : 1;

            // fetch item details by HSN
            $invoiceItemDetailsGroupByHSN = $branchSoObj->fetchBranchSoInvoiceItemsGroupByHSN($invoiceId)['data'];

            // fetch attachments
            $attachmentObj = $branchSoObj->getInvoiceAttachments($invoiceId);

            if ($templateId == 0) {
            ?>
                <style>
                    .text-small {
                        font-size: 0.8em;
                    }
                </style>
                <div class="card classic-view bg-transparent">
                    <div class="card-body classic-view-so-table" style="overflow: auto;">
                        <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print();">Print</button> -->
                        <div class="printable-view">
                            <h3 class="h3-title text-center font-bold text-sm mb-4">Tax Invoice</h3>
                            <?php if ($invoiceDetails['compInvoiceType'] == 'CBW' || $invoiceDetails['compInvoiceType'] == 'LUT' || $invoiceDetails['compInvoiceType'] == 'SEWOP') { ?>
                                <p class="text-center ">(SUPPLY MEANT FOR EXPORT/SUPPLY TO SEZ UNIT OR SEZ DEVELOPER FOR AUTHORISED OPERATIONS UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF IGST)</p>
                            <?php } ?>
                            <table class="classic-view table-bordered tableBorder">
                                <tbody>
                                    <?php
                                    if ($invoiceDetails['irn'] != "") {
                                    ?>
                                        <tr>
                                            <td colspan="12" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= COMP_STORAGE_URL . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                    </div>
                                                    <div class="invoice-qr">
                                                        <!-- <img width="200" src="" alt="QRCode"> -->
                                                        <div id="eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>"></div>
                                                    </div>
                                                </div>
                                                <script>
                                                    $(document).ready(function() {
                                                        new QRCode("eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>", "<?= $invoiceDetails['signed_qr_code'] ?>");
                                                        $("#eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>").removeAttr("title");
                                                    });
                                                </script>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td rowspan="3" colspan="7" class="px-2">
                                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= COMP_STORAGE_URL . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                            <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                            <p><?= $companyData['location_building_no'] ?></p>
                                            <p><?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                            <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                            <p>Company's PAN: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                            <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <p>Invoice No.</p>
                                            <p class="font-bold"><?= $invoiceDetails['invoice_no'] ?></p>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <p>Dated</p>
                                            <p class="font-bold"><?php $invDate = date_create($invoiceDetails['invoice_date']);
                                                                    echo date_format($invDate, "F d,Y"); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-2">
                                            <p>Mode/Terms of Payment</p>
                                            <?php if ($invoiceDetails['credit_period'] != "") { ?>
                                                <p><?= $invoiceDetails['credit_period'] ?></p>
                                            <?php } ?>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <p>Conversion Rate</p>
                                            <p><?= "1 " . $companyCurrencyName . " = " . $currencyConversionRate . " " . $customerCurrencyName ?></p>
                                        </td>
                                    </tr>
                                    <tr>

                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Buyer (Bill to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_billing_address'] ?></p>
                                            <p>GSTIN/UIN : <?= $customerData['customer_gstin'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                        </td>
                                        <td colspan="6" class="px-2">
                                            <p>Consignee (Ship to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_shipping_address'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                            <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $branchGstin = substr($companyData['branch_gstin'], 0, 2);
                                    $customerGstCode = substr($customerData['customer_gstin'], 0, 2);

                                    $gstCode = 0;
                                    if ($customerGstCode == "") {
                                        $gstCode = $invoiceDetails['placeOfSupply'] ?? 0;
                                    } else {
                                        $gstCode = substr($customerData['customer_gstin'], 0, 2);
                                    }

                                    $conditionGST = $branchGstin == $gstCode;
                                    ?>
                                    <tr>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Sl No.</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Particulars</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Quantity</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">UOM</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Rate</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Discount</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="3">IGST</th>
                                        <?php } ?>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                    </tr>
                                    <tr>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle">Amount</th>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle">Amount</th>
                                        <?php } else { ?>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle" colspan="2">Amount</th>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    $totalTaxAmt = 0;
                                    $subTotalAmt = 0;
                                    $allSubTotalAmt = 0;
                                    $totalDiscountAmt = 0;
                                    $totalCashDiscountAmt = 0;
                                    $totalAmt = 0;
                                    foreach ($invoiceItemDetails as $key => $item) {
                                        $uomName = getUomDetail($item['uom'])['data']['uomName'];
                                        // $uomObj = $ItemsObj->getBaseUnitMeasureById($item['uom']);
                                        // $uomName = $uomObj['data']['uomName'];

                                        $totalTaxAmt += $item['totalTax'];
                                        $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $totalCashDiscountAmt += $item['cashDiscountAmount'];
                                        $subTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];

                                        $singleItemTotalDiscount = $item['totalDiscountAmt'] + $item['cashDiscountAmount'];
                                        $singleItemBaseAmount = $item['unitPrice'] * $item['qty'];
                                        $singleItemTaxableAmount = $singleItemBaseAmount - $singleItemTotalDiscount;

                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $item['hsnCode'] ?></p>
                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= $item['invoiceQty'] ?></p>
                                                <?php } else { ?>
                                                    <p><?= $item['qty'] ?></p>
                                                <?php } ?>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= number_format($item['unitPrice'] * $currencyConversionRate, 2) ?></p>

                                            </td>

                                            <td class="text-right px-2">
                                                <p><?= number_format(($singleItemTotalDiscount * $currencyConversionRate), 2) ?></p>
                                            </td>

                                            <td class="text-right px-2">
                                                <p><?= number_format($singleItemTaxableAmount * $currencyConversionRate, 2) ?></p>
                                            </td>

                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class="font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($itemGstAmt * $currencyConversionRate, 2) ?></p>

                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($itemGstAmt * $currencyConversionRate, 2) ?></p>

                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2">
                                                    <p class=" font-bold"><?= $item['tax'] ?>%</p>
                                                </td>
                                                <td class="px-2" colspan="2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($item['totalTax'] * $currencyConversionRate, 2) ?></p>

                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2">
                                                <p><?= number_format($item['totalPrice'] * $currencyConversionRate, 2) ?></p>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="11" class="font-bold text-right px-2">
                                            <p>Sub Total (<?= $customerCurrencyName ?>)</p>
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p>Trade Discount (<?= $customerCurrencyName ?>)</p>

                                            <?php } ?>
                                            <?php if ($totalCashDiscountAmt > 0) { ?>
                                                <p>Cash Discount (<?= $customerCurrencyName ?>)</p>
                                            <?php } ?>
                                            <?php if ($invoiceDetails['total_tax_amt'] > 0) { ?>
                                                <?php if ($conditionGST || $gstCode == "") { ?>
                                                    <p>Total CGST (<?= $customerCurrencyName ?>)</p>
                                                    <p>Total SGST (<?= $customerCurrencyName ?>)</p>
                                                <?php } else { ?>
                                                    <p>Total IGST (<?= $customerCurrencyName ?>)</p>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>Round Off (<?= $customerCurrencyName ?>)</p>
                                            <?php
                                            }
                                            ?>
                                            <p>Grand Total (<?= $customerCurrencyName ?>)</p>
                                        </td>
                                        <td colspan="2" class="text-right font-bold px-2">
                                            <p><span class="rupee-symbol pr-1"></span><?= number_format($subTotalAmt * $currencyConversionRate, 2) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= number_format($subTotalAmt * $currencyConversionRate, 2) ?></small>
                                            <?php } ?> -->
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= number_format($totalDiscountAmt * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= number_format($totalDiscountAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            <?php } ?>
                                            <?php if ($totalCashDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= number_format($totalCashDiscountAmt * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= number_format($totalCashDiscountAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            <?php } ?>
                                            <?php if ($invoiceDetails['total_tax_amt'] > 0) { ?>
                                                <?php if ($conditionGST || $gstCode == "") { ?>
                                                    <p><span class="pr-1"></span><?= number_format($invoiceDetails['cgst'] * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= number_format($invoiceDetails['cgst'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                    <p><span class="pr-1"></span><?= number_format($invoiceDetails['sgst'] * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= number_format($invoiceDetails['sgst'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                <?php } else { ?>
                                                    <p><span class="pr-1"></span><?= number_format($invoiceDetails['igst'] * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= number_format($invoiceDetails['igst'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= number_format(abs($invoiceDetails['adjusted_amount'] * $currencyConversionRate), 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= number_format($invoiceDetails['adjusted_amount'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            <?php
                                            }
                                            ?>
                                            <p><?= number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2) ?></small>
                                            <?php } ?> -->
                                        </td>
                                    </tr>
                                </tbody>

                                <tbody>
                                    <tr>
                                        <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">HSN/SAC</th>
                                        <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Taxable Value</th>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">Central Tax</th>
                                            <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">State Tax</th>
                                        <?php } else { ?>
                                            <th colspan="3" class="text-bold text-center invoiceHSNTableHeadStyle">IGST</th>
                                        <?php } ?>
                                        <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Total Tax Amount</th>
                                    </tr>
                                    <tr>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                        <?php } else { ?>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle" colspan="2">Amount</th>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $totalTaxableValue = 0;
                                    $totalCgstSgstAmt = 0;
                                    $allTotalTaxAmt = 0;
                                    foreach ($invoiceItemDetailsGroupByHSN as $key => $item) {
                                        $itemGstPerHSN = $item['tax'] / 2;
                                        $itemGstAmtHSN = $item['totalTax'] / 2;
                                        $totalTaxableValue += $item['basePrice'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="3" class="px-2">
                                                <p class="invoiceSmallFont"><?= $item['hsnCode'] ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($item['basePrice'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['basePrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $item['tax'] ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= number_format($item['totalTax'] * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } ?>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($item['totalTax'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= number_format($totalTaxableValue * $currencyConversionRate, 2) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalTaxableValue * $currencyConversionRate, 2) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } ?>
                                        <td colspan="3" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></small>
                                            <?php } ?> -->
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Amount Chargeable (in words)</p>
                                            <p class="font-bold"><?= $customerCurrencyName . " " . number_to_words_indian_rupees($invoiceDetails['all_total_amt'] * $currencyConversionRate); ?> ONLY</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2)) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <td colspan="6" class="px-2">
                                            <p class=" text-right">E. & O.E</p>
                                            <p>Company’s Bank Details</p>
                                            <div class="d-flex">
                                                <p>Bank Name :</p>
                                                <p class="font-bold"><?= $company_bank_details['bank_name'] ?></p>
                                            </div>
                                            <div class="d-flex">
                                                <p>A/c No. :</p>
                                                <p class="font-bold"><?= $company_bank_details['account_no'] ?></p>
                                            </div>
                                            <div class="d-flex">
                                                <p>Branch & IFS Code :</p>
                                                <p class="font-bold"><?= $company_bank_details['ifsc_code'] ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Remarks: <?= $invoiceDetails['remarks'] ?></p>
                                            <p>Declaration: <?= $invoiceDetails['declaration_note'] ?></p>
                                            <!-- <p><?= $companyData['company_footer'] ?></p> -->
                                            <p>Created By: <strong><?= getCreatedByUser($invoiceDetails['created_by']); ?></strong></p>
                                            <?php if ($attachmentObj['status'] == 'success') { ?>
                                                <a href="<?= COMP_STORAGE_URL . '/others/' ?><?= $attachmentObj['data']['file_name'] ?>" target="_blank" class="text-primary font-bold text-decoration-none text-decoration-underline" download>
                                                    View Attachment
                                                </a>
                                            <?php } ?>
                                        </td>
                                        <td colspan="6" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <p class="text-center sign-img">
                                                <img width="160" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="">
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            } elseif ($templateId == 1) { ?>
                <div class="card classic-view bg-transparent">
                    <div class="card-body classic-view-so-table " style="overflow: auto;">
                        <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print();">Print</button> -->
                        <div class="printable-view">
                            <h3 class="h3-title text-center font-bold text-sm mb-4">Tax Invoice</h3>
                            <?php if ($invoiceDetails['compInvoiceType'] == 'CBW' || $invoiceDetails['compInvoiceType'] == 'LUT' || $invoiceDetails['compInvoiceType'] == 'SEWOP') { ?>
                                <p class="text-center ">(SUPPLY MEANT FOR EXPORT/SUPPLY TO SEZ UNIT OR SEZ DEVELOPER FOR AUTHORISED OPERATIONS UNDER BOND OR LETTER OF UNDERTAKING WITHOUT PAYMENT OF IGST)</p>
                            <?php } ?>
                            <table class="classic-view table-bordered tableBorder">
                                <tbody>
                                    <?php
                                    if ($invoiceDetails['irn'] != "") {
                                    ?>
                                        <tr>
                                            <td colspan="12" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= COMP_STORAGE_URL . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                    </div>
                                                    <div class="invoice-qr">
                                                        <!-- <img width="200" src="" alt="QRCode"> -->
                                                        <div id="eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>"></div>
                                                    </div>
                                                </div>
                                                <script>
                                                    $(document).ready(function() {
                                                        new QRCode("eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>", "<?= $invoiceDetails['signed_qr_code'] ?>");
                                                        $("#eInvoiceQrCode<?= $invoiceDetails['invoice_no'] ?>").removeAttr("title");
                                                    });
                                                </script>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td rowspan="3" colspan="7" class="px-2">
                                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= COMP_STORAGE_URL . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                            <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                            <p><?= $companyData['location_building_no'] ?></p>
                                            <p><?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                            <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                            <p>Company's PAN: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                            <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <p>Invoice No.</p>
                                            <p class="font-bold"><?= $invoiceDetails['invoice_no'] ?></p>
                                        </td>
                                        <td colspan="4" class="px-2">
                                            <p>Dated</p>
                                            <p class="font-bold"><?php $invDate = date_create($invoiceDetails['invoice_date']);
                                                                    echo date_format($invDate, "F d,Y"); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-2">
                                            <p>Mode/Terms of Payment</p>
                                            <?php if ($invoiceDetails['credit_period'] != "") { ?>
                                                <p><?= $invoiceDetails['credit_period'] ?></p>
                                            <?php } ?>
                                        </td>
                                        <td colspan="4" class="px-2">
                                            <p>Conversion Rate</p>
                                            <p><?= "1 " . $companyCurrencyName . " = " . $currencyConversionRate . " " . $customerCurrencyName ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <!-- <td colspan="3" class="px-2">
                                    <p>Buyer’s Order No.</p>
                                    <?php if ($invoiceDetails['po_number'] != "") { ?>
                                        <p><?= $invoiceDetails['po_number'] ?></p>
                                    <?php } ?>
                                </td>
                                <td colspan="3" class="px-2">
                                    <p>Dated</p>
                                    <?php if ($invoiceDetails['po_date'] != "") { ?>
                                        <p><?= $invoiceDetails['po_date'] ?></p>
                                    <?php } ?>
                                </td> -->
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Buyer (Bill to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_billing_address'] ?></p>
                                            <p>GSTIN/UIN : <?= $customerData['customer_gstin'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                        </td>
                                        <td colspan="7" class="px-2">
                                            <p>Consignee (Ship to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $invoiceDetails['customer_shipping_address'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                            <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $branchGstin = substr($companyData['branch_gstin'], 0, 2);
                                    $customerGstCode = substr($customerData['customer_gstin'], 0, 2);

                                    $gstCode = 0;
                                    if ($customerGstCode == "") {
                                        $gstCode = $invoiceDetails['placeOfSupply'] ?? 0;
                                    } else {
                                        $gstCode = substr($customerData['customer_gstin'], 0, 2);
                                    }

                                    $conditionGST = $branchGstin == $gstCode;
                                    ?>
                                    <tr>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Sl No.</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Particulars</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Quantity</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">UOM</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">MRP</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Trade Discount</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Gross Amt.</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Cash Discount</th>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="3">IGST</th>
                                        <?php } ?>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                    </tr>
                                    <tr>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle">Amount</th>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle">Amount</th>
                                        <?php } else { ?>
                                            <th class="invoiceTableHeadStyle">Rate</th>
                                            <th class="invoiceTableHeadStyle" colspan="2">Amount</th>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    $totalTaxAmt = 0;
                                    $subTotalAmt = 0;
                                    $allSubTotalAmt = 0;
                                    $totalDiscountAmt = 0;
                                    $totalAmt = 0;
                                    foreach ($invoiceItemDetails as $key => $item) {
                                        $uomName = getUomDetail($item['uom'])['data']['uomName'];
                                        // $uomObj = $ItemsObj->getBaseUnitMeasureById($item['uom']);
                                        // $uomName = $uomObj['data']['uomName'];

                                        $totalTaxAmt += $item['totalTax'];
                                        $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $subTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];
                                        $taxableAmount = ($item['unitPrice'] * $item['qty']) - $item['totalDiscountAmt'] - $item['cashDiscountAmount'];
                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $item['hsnCode'] ?></p>
                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= $item['invoiceQty'] ?></p>
                                                <?php } else { ?>
                                                    <p><?= $item['qty'] ?></p>
                                                <?php } ?>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= number_format($item['unitPrice'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['unitPrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?= $item['totalDiscount'] ?>%)</span> <?= number_format($item['totalDiscountAmt'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalDiscountAmt'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= (($item['unitPrice'] * $item['qty']) - $item['totalDiscountAmt']) * $currencyConversionRate ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?= $item['cashDiscount'] ?>%)</span> <?= number_format($item['cashDiscountAmount'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['cashDiscountAmount'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= $taxableAmount * $currencyConversionRate ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format(($item['unitPrice'] * $item['qty']) * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($itemGstAmt * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmt * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($itemGstAmt * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmt * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2">
                                                    <p class=" font-bold"><?= $item['tax'] ?>%</p>
                                                </td>
                                                <td class="px-2" colspan="2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= number_format($item['totalTax'] * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2">
                                                <p><?= number_format($item['totalPrice'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalPrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="12" class="font-bold text-right px-2">
                                            <p>Sub Total (<?= $customerCurrencyName ?>)</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted">Sub Total (<?= $customerCurrencyName ?>)</small>
                                            <?php } ?> -->
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <p>Total CGST (<?= $customerCurrencyName ?>)</p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Total CGST (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?> -->
                                                <p>Total SGST (<?= $customerCurrencyName ?>)</p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Total SGST (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?> -->
                                            <?php } else { ?>
                                                <p>Total IGST (<?= $customerCurrencyName ?>)</p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Total IGST (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?> -->
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>Round Off (<?= $customerCurrencyName ?>)</p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Round Off (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?> -->
                                            <?php
                                            }
                                            ?>
                                            <p>Grand Total (<?= $customerCurrencyName ?>)</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted">Grand Total (<?= $customerCurrencyName ?>)</small>
                                            <?php } ?> -->
                                        </td>
                                        <td colspan="2" class="text-right font-bold px-2">
                                            <p><span class="rupee-symbol pr-1"></span><?= number_format($subTotalAmt * $currencyConversionRate, 2) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= number_format($subTotalAmt * $currencyConversionRate, 2) ?></small>
                                            <?php } ?> -->
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <p><span class="pr-1"></span><?= number_format($invoiceDetails['cgst'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= number_format($invoiceDetails['cgst'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                                <p><span class="pr-1"></span><?= number_format($invoiceDetails['sgst'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= number_format($invoiceDetails['sgst'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            <?php } else { ?>
                                                <p><span class="pr-1"></span><?= number_format($invoiceDetails['igst'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= number_format($invoiceDetails['igst'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            <?php } ?>

                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= number_format(abs($invoiceDetails['adjusted_amount']) * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= number_format($invoiceDetails['adjusted_amount'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            <?php
                                            }
                                            ?>
                                            <p><?= number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2) ?></small>
                                            <?php } ?> -->
                                        </td>
                                    </tr>
                                </tbody>

                                <tbody>
                                    <tr>
                                        <th colspan="4" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">HSN/SAC</th>
                                        <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Taxable Value</th>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">Central Tax</th>
                                            <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">State Tax</th>
                                        <?php } else { ?>
                                            <th colspan="3" class="text-bold text-center invoiceHSNTableHeadStyle">IGST</th>
                                        <?php } ?>
                                        <th colspan="4" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Total Tax Amount</th>
                                    </tr>
                                    <tr>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                        <?php } else { ?>
                                            <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                            <th class="text-bold invoiceHSNTableHeadStyle" colspan="2">Amount</th>
                                        <?php } ?>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $totalTaxableValue = 0;
                                    $totalCgstSgstAmt = 0;
                                    $allTotalTaxAmt = 0;
                                    foreach ($invoiceItemDetailsGroupByHSN as $key => $item) {
                                        $itemGstPerHSN = $item['tax'] / 2;
                                        $itemGstAmtHSN = $item['totalTax'] / 2;
                                        $totalTaxableValue += $item['basePrice'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="4" class="px-2">
                                                <p class="invoiceSmallFont"><?= $item['hsnCode'] ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($item['basePrice'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['basePrice'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($itemGstAmtHSN * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= $item['tax'] ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= number_format($item['totalTax'] * $currencyConversionRate, 2) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } ?>
                                            <td colspan="4" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($item['totalTax'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['totalTax'] * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="4">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= number_format($totalTaxableValue * $currencyConversionRate, 2) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalTaxableValue * $currencyConversionRate, 2) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($totalCgstSgstAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } ?>
                                        <td colspan="4" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($allTotalTaxAmt * $currencyConversionRate, 2) ?></small>
                                            <?php } ?> -->
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Amount Chargeable (in words)</p>
                                            <p class="font-bold"><?= $companyCurrencyName . " " . number_to_words_indian_rupees($invoiceDetails['all_total_amt'] * $currencyConversionRate); ?> ONLY</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(number_format($invoiceDetails['all_total_amt'] * $currencyConversionRate, 2)) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <td colspan="7" class="px-2">
                                            <p class=" text-right">E. & O.E</p>
                                            <p>Company’s Bank Details</p>
                                            <div class="d-flex">
                                                <p>Bank Name :</p>
                                                <p class="font-bold"><?= $company_bank_details['bank_name'] ?></p>
                                            </div>
                                            <div class="d-flex">
                                                <p>A/c No. :</p>
                                                <p class="font-bold"><?= $company_bank_details['account_no'] ?></p>
                                            </div>
                                            <div class="d-flex">
                                                <p>Branch & IFS Code :</p>
                                                <p class="font-bold"><?= $company_bank_details['ifsc_code'] ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Remarks: <?= $invoiceDetails['remarks'] ?></p>
                                            <p>Declaration: <?= $invoiceDetails['declaration_note'] ?></p>
                                            <!-- <p><?= $companyData['company_footer'] ?></p> -->
                                            <p>Created By: <strong><?= getCreatedByUser($invoiceDetails['created_by']); ?></strong></p>
                                        </td>
                                        <td colspan="7" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <p class="text-center sign-img">
                                                <img width="160" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="">
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
<?php
            }
        }
    }
}
?>