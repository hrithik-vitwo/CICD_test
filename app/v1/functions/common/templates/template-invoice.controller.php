<?php

require __DIR__ . '/../../../../../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\PngWriter;

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
        //  echo '0000000000000';
        // echo 1;
        // exit();

        $branchSoObj = new BranchSo();
        $invoiceDetailsObj = $branchSoObj->fetchBranchSoInvoiceById($invoiceId);
        $fetch_e_way = queryget("SELECT eInv.signed_qr_code as qr,eInv.irn as IRN,eWay.created_at AS eWayDate FROM `erp_e_invoices` AS eInv LEFT JOIN `erp_e_way_bills` AS `eWay` ON eInv.irn = eWay.irn WHERE eInv.`invoice_id` = $invoiceId");
        // console($fetch_e_way);

        if ($fetch_e_way['status'] == 'success') {
            $irn = $fetch_e_way['data']['IRN'];
            $e_way_date = formatDateORDateTime($fetch_e_way['data']['eWayDate']) ?? ' ';

            $qr = $fetch_e_way['data']['qr'];
            try {
                // Start output buffering


                // Create the QR code
                $result = Builder::create()
                    ->writer(new PngWriter())
                    ->data($qr)
                    ->encoding(new Encoding('UTF-8'))
                    ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
                    ->size(180)
                    ->margin(10)
                    ->build();

                // Clear any previous output and set the Content-Type header

                header('Content-Type: image/png');

                // Output the QR code image directly
                $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($result->getString());

                // End and flush the output buffer


            } catch (Exception $e) {

                echo 'Error: ' . $e->getMessage();
            }
        } else {

            $irn = ' ';
            $e_way_date = ' ';
        }



        // console($invoiceDetailsObj);

        if (count($invoiceDetailsObj['data']) <= 0) {
            // echo 1;
            // echo '<p class="text-warning text-center mt-5">Invoice Not found!</p>';
            // if ($redirectUrl != "") {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!", $redirectUrl);
            // } else {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!");
            // }
        } else {
            //echo 2;
            $invoiceDetails = $invoiceDetailsObj['data'][0];
            // $soObj=[];
            $soData = [];
            if ($invoiceDetails['so_id'] != "") {
                $soId = $invoiceDetails['so_id'];
                $soData = $branchSoObj->fetchSoDetailsBySoId($soId)['data'][0];
            }

            // console($invoiceDetails);

            $company_id = $invoiceDetails['company_id'];
            $branch_id = $invoiceDetails['branch_id'];
            $location_id = $invoiceDetails['location_id'];
            //console($invoiceDetails);

            $invoiceItemDetails = $branchSoObj->fetchBranchSoInvoiceItems($invoiceId)['data'];
            // console(($invoiceItemDetails));

            // fetch company config
            $companyConfigDetails = $branchSoObj->fetchCompanyConfig($invoiceDetails['companyConfigId'])['data'];
            // console(($companyConfigDetails));

            // fetch company data
            $companyData = unserialize($invoiceDetails['companyDetails']);
            $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
            $companyCurrencyIcon = $currencyDetails['currency_icon'];
            $companyCurrencyName = $currencyDetails['currency_name'];
            // console($companyData);
            // company bank details
            $company_bank_details = unserialize($invoiceDetails['company_bank_details']);

            // fetch customer data
            $customerData = unserialize($invoiceDetails['customerDetails']);
            $customerCurrencyName = $invoiceDetails['currency_name'] ?? "";
            $currencyConversionRate = $invoiceDetails['conversion_rate'] != "" ? $invoiceDetails['conversion_rate'] : 1;

            //QR

            // fetch item details by HSN
            $invoiceItemDetailsGroupByHSN = $branchSoObj->fetchBranchSoInvoiceItemsGroupByHSN($invoiceId)["data"];


            // fetch attachments
            $attachmentObj = $branchSoObj->getInvoiceAttachments($invoiceId);
            $tc_id = $invoiceDetails['tc_id'];
            //   echo "SELECT * FROM `erp_terms_and_condition_format` WHERE tc_slug='invoice' AND tc_id=$tc_id";
            $qry = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE tc_slug='invoice' AND tc_id=$tc_id")['data'];
            $termscond = stripcslashes(unserialize($qry['tc_text']));

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

                                        $ewbSql = "SELECT * FROM `erp_e_way_bills` as ewayBill  WHERE ewayBill.irn='" . $invoiceDetails['irn'] . "' AND ewayBill.company_id=$company_id AND ewayBill.branch_id=$branch_id AND ewayBill.location_id=$location_id";
                                        $ewbData = queryGet($ewbSql)['data'];

                                    ?>
                                        <tr>
                                            <td colspan="13" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" width="150" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                        <p>EWB. No: <?= $ewbData['ewb_no'] ?></p>
                                                        <p>EWB. Date: <?= formatDateORDateTime($ewbData['ewb_date']) ?></p>
                                                        <p>EWB. Valid Till: <?= formatDateORDateTime($ewbData['ewb_valid_till']) ?></p>
                                                    </div>
                                                    <div class="invoice-qr">
                                                        <!-- <img width="200" src="" alt="QRCode"> -->
                                                        <?php if ($fetch_e_way['numRows'] > 0) {
                                                        ?>
                                                            <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code">

                                                        <?php
                                                        }

                                                        ?>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td rowspan="3" colspan="7" class="px-2">
                                            <?php if ($invoiceDetails['irn'] == "") { ?>
                                                <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <?php } ?>
                                            <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                            <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                            <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                            <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                            <p>Company's PAN: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                            <?php if ($companyConfigDetails['email'] != "") { ?>
                                                <p>E-Mail : <?= $companyConfigDetails['email'] ?></p>
                                            <?php } else { ?>
                                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                            <?php
                                            } ?>
                                            <?php if ($companyConfigDetails['phone'] != "") { ?>
                                                <p>Phone : <?= $companyConfigDetails['phone'] ?></p>
                                            <?php } else {
                                            ?>
                                                <p>Phone : <?= $companyData['companyPhone'] ?></p>
                                            <?php
                                            } ?>
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
                                            <p>E-way Bill Date</p>
                                            <p class="font-bold"><?= $e_way_date ?></p>
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
                                        <th class="invoiceTableHeadStyle">Sl No.</th>
                                        <th class="invoiceTableHeadStyle">Particulars</th>
                                        <th class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th class="invoiceTableHeadStyle">Qty</th>
                                        <th class="invoiceTableHeadStyle">UOM</th>
                                        <th class="invoiceTableHeadStyle">MRP</th>
                                        <th class="invoiceTableHeadStyle">Rate</th>
                                        <th class="invoiceTableHeadStyle">Discount</th>
                                        <th class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">IGST</th>
                                        <?php } ?>
                                        <th class="invoiceTableHeadStyle" colspan="2">Total Amount</th>
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
                                        $allSubTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $totalCashDiscountAmt += $item['cashDiscountAmount'];
                                        $subTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];


                                        $singleItemTotalDiscount = $item['totalDiscountAmt'] + $item['cashDiscountAmount'];
                                        $singleItemBaseAmount = $item['itemTargetPrice'] * $item['qty'];
                                        $singleItemTaxableAmount = $singleItemBaseAmount - $singleItemTotalDiscount;

                                        $batchString = getUsedBatchSpecificDocumentDetails($company_id, $branch_id, $location_id, $item['inventory_item_id'], $invoiceDetails['invoice_no'])['batchString'];

                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                                <p><?php echo $batchString; ?></p>
                                            </td>
                                            <td class="px-2">

                                                <p><?= hsnInProperFormat($item['hsnCode']) ?></p>

                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= decimalQuantityPreview($item['invoiceQty']) ?></p>
                                                <?php } else { ?>
                                                    <p><?= decimalQuantityPreview($item['qty']) ?></p>
                                                <?php } ?>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['unitPrice']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['unitPrice'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <!-- Rate -->
                                            <td class="text-right px-2">
                                                <!-- <p><?php echo decimalValuePreview(((($item['unitPrice'] * $item['qty']) - $item['totalDiscountAmt']) / $item['qty'])); ?> </p> -->
                                                <p><?php echo decimalValuePreview($item['itemTargetPrice']); ?> </p>
                                            </td>

                                            <!-- Discount amount -->
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($singleItemTotalDiscount) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview(($singleItemTotalDiscount * $currencyConversionRate)) ?></small>
                                                <?php } ?>
                                            </td>

                                            <!-- Taxable amount -->
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($singleItemTaxableAmount) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview(($singleItemTaxableAmount * $currencyConversionRate)) ?></small>
                                                <?php } ?>
                                            </td>

                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class="">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>

                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>

                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2" colspan="2">
                                                    <p class="">(<?= decimalQuantityPreview($item['tax']) ?>%)</p>
                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($item['totalTax']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2" colspan="2">
                                                <p><?= decimalValuePreview($item['totalPrice']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalPrice'] * $currencyConversionRate) ?></small>
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
                                            if ($invoiceDetails['tcs_amount'] != 0) {
                                            ?>
                                                <p>TCS Amount (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">TCS Amount (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
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
                                            <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($subTotalAmt) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= decimalValuePreview($totalDiscountAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= decimalValuePreview($totalDiscountAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($totalCashDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= decimalValuePreview($totalCashDiscountAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= decimalValuePreview($totalCashDiscountAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($invoiceDetails['total_tax_amt'] > 0) { ?>
                                                <?php if ($conditionGST || $gstCode == "") { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['cgst']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['sgst']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['igst']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['tcs_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['tcs_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['tcs_amount'])) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['tcs_amount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php
                                            } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['adjusted_amount']), 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview($invoiceDetails['adjusted_amount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
                                            <p><?= decimalValuePreview($invoiceDetails['all_total_amt']) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></small>
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
                                        $totalTaxableValue += $item['hsnTaxableAmount'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="3" class="px-2">
                                                <p class="invoiceSmallFont"><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['hsnTaxableAmount']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($item['tax']) ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax'], 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($totalTaxableValue) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                        <td colspan="3" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
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
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate)) ?></small>
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
                                                <a href="<?= BUCKET_URL . 'uploads/' . $company_id . '/others/' ?><?= $attachmentObj['data']['file_name'] ?>" target="_blank" class="text-primary font-bold text-decoration-none text-decoration-underline" download>
                                                    View Attachment
                                                </a>

                                            <?php }
                                            if ($invoiceDetails['tc_id'] > 0) {
                                                $tc_id = $invoiceDetails['tc_id'];
                                                $iv_tc = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE `tc_id`=$tc_id");

                                                echo '<a href="#" class="tcContent" id="tcContent" data-toggle="modal" data-target="#tcContentModal" data-value=' . $invoiceDetails['tc_id'] . '><b>' . $iv_tc['data']['tc_variant'] . '</b></a>';
                                            }
                                            ?>
                                        </td>
                                        <td colspan="6" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <?php if ($companyData['signature'] != "") { ?>
                                                <p class="text-center sign-img">
                                                    <img width="160" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['signature'] ?>" alt="">
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                                </p>
                                            <?php } else { ?>
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php
                            if (isset($_GET['printChkbox'])) {

                                echo '<div class="page-break"></div>';
                                echo  $termscond;
                            }
                            ?>
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
                                        $ewbSql = "SELECT * FROM `erp_e_way_bills` as ewayBill  WHERE ewayBill.irn='" . $invoiceDetails['irn'] . "' AND ewayBill.company_id=$company_id AND ewayBill.branch_id=$branch_id AND ewayBill.location_id=$location_id";
                                        $ewbData = queryGet($ewbSql)['data'];
                                    ?>
                                        <tr>
                                            <td colspan="15" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" width="150" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                        <p>EWB. No: <?= $ewbData['ewb_no'] ?></p>
                                                        <p>EWB. Date: <?= formatDateORDateTime($ewbData['ewb_date']) ?></p>
                                                        <p>EWB. Valid Till: <?= formatDateORDateTime($ewbData['ewb_valid_till']) ?></p>
                                                    </div>
                                                    <div class="invoice-qr">
                                                        <!-- <img width="200" src="" alt="QRCode"> -->


                                                        <?php if ($fetch_e_way['numRows'] > 0) {
                                                        ?>
                                                            <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code">

                                                        <?php
                                                        }

                                                        ?>

                                                    </div>
                                                </div>
                                                <!-- <script>
                                                    $(document).ready(function() {
                                                        new QRCode("eInvoiceQrCode1<?= $invoiceDetails['invoice_no'] ?>", "<?= $invoiceDetails['signed_qr_code'] ?>");
                                                        $("#eInvoiceQrCode1<?= $invoiceDetails['invoice_no'] ?>").removeAttr("title");
                                                    });
                                                </script> -->
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td rowspan="3" colspan="8" class="px-2">
                                            <?php if ($invoiceDetails['irn'] == "") { ?>
                                                <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <?php } ?>
                                            <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                            <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                            <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                            <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                            <p>Company's PAN: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                            <?php if ($companyConfigDetails['email'] != "") { ?>
                                                <p>E-Mail : <?= $companyConfigDetails['email'] ?></p>
                                            <?php } else { ?>
                                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                            <?php
                                            } ?>
                                            <?php if ($companyConfigDetails['phone'] != "") { ?>
                                                <p>Phone : <?= $companyConfigDetails['phone'] ?></p>
                                            <?php } else {
                                            ?>
                                                <p>Phone : <?= $companyData['companyPhone'] ?></p>
                                            <?php
                                            } ?>
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
                                        <td colspan="8" class="px-2">
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
                                        <th class="invoiceTableHeadStyle">Sl No.</th>
                                        <th class="invoiceTableHeadStyle">Particulars</th>
                                        <th class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th class="invoiceTableHeadStyle">Qty</th>
                                        <th class="invoiceTableHeadStyle">MRP</th>
                                        <th class="invoiceTableHeadStyle">Unit Rate</th>
                                        <th class="invoiceTableHeadStyle">Trade Discount</th>
                                        <th class="invoiceTableHeadStyle">Gross Amt.</th>
                                        <th class="invoiceTableHeadStyle">Cash Discount</th>
                                        <th class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">IGST</th>
                                        <?php } ?>
                                        <th class="invoiceTableHeadStyle">Total Amount</th>
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
                                        $allSubTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $subTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];
                                        $taxableAmount = ($item['itemTargetPrice'] * $item['qty']) - $item['totalDiscountAmt'] - $item['cashDiscountAmount'];
                                        $batchString = getUsedBatchSpecificDocumentDetails($company_id, $branch_id, $location_id, $item['inventory_item_id'], $invoiceDetails['invoice_no'])['batchString'];

                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                                <p><?php echo $batchString; ?></p>
                                            </td>
                                            <td class="px-2">
                                                <p><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= decimalQuantityPreview($item['invoiceQty']) ?></p>
                                                <?php } else { ?>
                                                    <p><?= decimalQuantityPreview($item['qty']) ?></p>
                                                <?php } ?>
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['unitPrice']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['unitPrice'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>

                                            <!-- Rate -->
                                            <td class="text-right px-2">
                                                <p><?php echo decimalValuePreview($item['itemTargetPrice']); ?> </p>
                                                <!-- <p><?php echo decimalValuePreview(((($item['unitPrice'] * $item['qty']) - $item['totalDiscountAmt']) / $item['qty'])); ?> </p> -->
                                            </td>

                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?= decimalQuantityPreview($item['totalDiscount']) ?>%)</span><?= decimalValuePreview($item['totalDiscountAmt']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalDiscountAmt'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <!-- total discount -->
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview(($item['itemTargetPrice'] * $item['qty']) - $item['totalDiscountAmt']) ?></p>
                                            </td>
                                            <!-- cash disount amount -->
                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?= decimalQuantityPreview($item['cashDiscount']) ?>%)</span> <?= decimalValuePreview($item['cashDiscountAmount']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['cashDiscountAmount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>


                                            <!-- taxable amount -->
                                            <td class="text-right px-2">
                                                <p><?= $taxableAmount ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview(($item['itemTargetPrice'] * $item['qty']) * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class="">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>
                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>
                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2" colspan="2">
                                                    <p class="">(<?= decimalQuantityPreview($item['tax']) ?>%)</p>
                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($item['totalTax']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['totalPrice']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalPrice'] * $currencyConversionRate) ?></small>
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
                                            if ($invoiceDetails['tcs_amount'] != 0) {
                                            ?>
                                                <p>TCS Amount (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">TCS Amount (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
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
                                            <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($subTotalAmt) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['cgst']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                                <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['sgst']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['igst']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['tcs_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['tcs_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['tcs_amount'])) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['tcs_amount'] * $currencyConversionRate) ?></small>
                                            <?php
                                                }
                                            }
                                            ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['adjusted_amount'])) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['adjusted_amount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>

                                            <p><?= decimalValuePreview($invoiceDetails['all_total_amt']) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></small>
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
                                        $totalTaxableValue += $item['hsnTaxableAmount'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="4" class="px-2">
                                                <p class="invoiceSmallFont"><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['hsnTaxableAmount']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($item['tax']) ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td colspan="4" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="4">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($totalTaxableValue) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                        <td colspan="4" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="8" class="px-2">
                                            <p>Amount Chargeable (in words)</p>
                                            <p class="font-bold"><?= $companyCurrencyName . " " . number_to_words_indian_rupees($invoiceDetails['all_total_amt']); ?> ONLY</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate)) ?></small>
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
                                        <td colspan="8" class="px-2">
                                            <p>Remarks: <?= $invoiceDetails['remarks'] ?></p>
                                            <p>Declaration: <?= $invoiceDetails['declaration_note'] ?></p>
                                            <!-- <p><?= $companyData['company_footer'] ?></p> -->
                                            <p>Created By: <strong><?= getCreatedByUser($invoiceDetails['created_by']); ?></strong></p>

                                            <?php
                                            if ($invoiceDetails['tc_id'] > 0) {
                                                $tc_id = $invoiceDetails['tc_id'];
                                                $iv_tc = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE `tc_id`=$tc_id");

                                                echo '<a href="#" class="tcContent" data-toggle="modal" data-target="#tcContentModal" data-value=' . $invoiceDetails['tc_id'] . '><b>' . $iv_tc['data']['tc_variant'] . '</b></a>';
                                            }
                                            ?>

                                        </td>
                                        <td colspan="6" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <?php if ($companyData['signature'] != "") { ?>
                                                <p class="text-center sign-img">
                                                    <img width="160" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['signature'] ?>" alt="">
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                                </p>
                                            <?php } else { ?>
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <?php
                            if (isset($_GET['printChkbox'])) {

                                echo '<div class="page-break"></div>';
                                echo  $termscond;
                            }
                            ?>
                        </div>

                    </div>
                </div>
                </div>
            <?php
            } else if ($templateId == 2) { ?>
                <style>
                    .text-small {
                        font-size: 0.8em;
                    }

                    table.classic-view.table-bordered.tableBorder td p {
                        margin: 4px 0 !important;
                        font-size: 10px !important;
                        white-space: pre-wrap !important;
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
                                    // console($invoiceDetails);
                                    // console($soData);
                                    // if(count($soData)>0)
                                    // {
                                    //     echo "so is here";
                                    // }
                                    if ($invoiceDetails['irn'] != "") {
                                        $ewbSql = "SELECT * FROM `erp_e_way_bills` as ewayBill  WHERE ewayBill.irn='" . $invoiceDetails['irn'] . "' AND ewayBill.company_id=$company_id AND ewayBill.branch_id=$branch_id AND ewayBill.location_id=$location_id";
                                        $ewbData = queryGet($ewbSql)['data'];

                                    ?>
                                        <tr>
                                            <td colspan="13" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" width="150" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                        <p>EWB. No: <?= $ewbData['ewb_no'] ?></p>
                                                        <p>EWB. Date: <?= formatDateORDateTime($ewbData['ewb_date']) ?></p>
                                                        <p>EWB. Valid Till: <?= formatDateORDateTime($ewbData['ewb_valid_till']) ?></p>
                                                    </div>
                                                    <div class="invoice-qr">
                                                        <!-- <img width="200" src="" alt="QRCode"> -->
                                                        <?php if ($fetch_e_way['numRows'] > 0) {
                                                        ?>
                                                            <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code">

                                                        <?php
                                                        }

                                                        ?>
                                                    </div>
                                                </div>
                                                <!-- <script>
                                                    $(document).ready(function() {
                                                        new QRCode("eInvoiceQrCode0<?= $invoiceDetails['invoice_no'] ?>", "<?= $invoiceDetails['signed_qr_code'] ?>");
                                                        $("#eInvoiceQrCode0<?= $invoiceDetails['invoice_no'] ?>").removeAttr("title");
                                                    });
                                                </script> -->
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <td rowspan="3" colspan="7" class="px-2">
                                            <?php if ($invoiceDetails['irn'] == "") { ?>
                                                <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <?php } ?>
                                            <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                            <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                            <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                            <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                                            <p>Company's PAN: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                            <?php if ($companyConfigDetails['email'] != "") { ?>
                                                <p>E-Mail : <?= $companyConfigDetails['email'] ?></p>
                                            <?php } else { ?>
                                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                            <?php
                                            } ?>
                                            <?php if ($companyConfigDetails['phone'] != "") { ?>
                                                <p>Phone : <?= $companyConfigDetails['phone'] ?></p>
                                            <?php } else {
                                            ?>
                                                <p>Phone : <?= $companyData['companyPhone'] ?></p>
                                            <?php
                                            } ?>
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
                                                <p class="font-bold"><?= $invoiceDetails['credit_period'] ?></p>
                                            <?php } ?>
                                        </td>
                                        <td colspan="3" class="px-2">
                                            <?php if (count($soData) > 0) { ?>
                                                <p>SO Number</p>
                                                <p class="font-bold"><?= $soData['so_number'] ?></p>
                                                <br>
                                            <?php } ?>
                                            <p>Customer Order No</p>
                                            <p class="font-bold"><?= $soData['customer_po_no'] ?></p>
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
                                        <th class="invoiceTableHeadStyle">Sl No.</th>
                                        <th class="invoiceTableHeadStyle">Particulars</th>
                                        <th class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th class="invoiceTableHeadStyle">Qty</th>
                                        <th class="invoiceTableHeadStyle">UOM</th>
                                        <th class="invoiceTableHeadStyle">Rate</th>
                                        <th class="invoiceTableHeadStyle">Discount</th>
                                        <th class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">IGST</th>
                                        <?php } ?>
                                        <th class="invoiceTableHeadStyle" colspan="2">Total Amount</th>
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
                                        $allSubTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $totalCashDiscountAmt += $item['cashDiscountAmount'];
                                        $subTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];


                                        $singleItemTotalDiscount = $item['totalDiscountAmt'] + $item['cashDiscountAmount'];
                                        $singleItemBaseAmount = $item['itemTargetPrice'] * $item['qty'];
                                        $singleItemTaxableAmount = $singleItemBaseAmount - $singleItemTotalDiscount;

                                        // $batchString = getUsedBatchSpecificDocumentDetails($company_id, $branch_id, $location_id, $item['inventory_item_id'], $invoiceDetails['invoice_no'])['batchString'];

                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                            </td>
                                            <td class="px-2">

                                                <p><?= hsnInProperFormat($item['hsnCode']) ?></p>

                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= decimalQuantityPreview($item['invoiceQty']) ?></p>
                                                <?php } else { ?>
                                                    <p><?= decimalQuantityPreview($item['qty']) ?></p>
                                                <?php } ?>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <!-- Rate -->
                                            <td class="text-right px-2">
                                                <!-- <p><?php echo decimalValuePreview(((($item['unitPrice'] * $item['qty']) - $item['totalDiscountAmt']) / $item['qty'])); ?> </p> -->
                                                <p><?php echo decimalValuePreview($item['itemTargetPrice']); ?> </p>
                                            </td>

                                            <!-- Discount amount -->
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($singleItemTotalDiscount) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview(($singleItemTotalDiscount * $currencyConversionRate)) ?></small>
                                                <?php } ?>
                                            </td>

                                            <!-- Taxable amount -->
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($singleItemTaxableAmount) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview(($singleItemTaxableAmount * $currencyConversionRate)) ?></small>
                                                <?php } ?>
                                            </td>

                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class="">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>

                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>

                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2" colspan="2">
                                                    <p class="">(<?= decimalQuantityPreview($item['tax']) ?>%)</p>
                                                    <p class=""><span class="rupee-symbol"></span><?= decimalValuePreview($item['totalTax']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2" colspan="2">
                                                <p><?= decimalValuePreview($item['totalPrice']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalPrice'] * $currencyConversionRate) ?></small>
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
                                            if ($invoiceDetails['tcs_amount'] != 0) {
                                            ?>
                                                <p>TCS Amount (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">TCS Amount (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
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
                                            <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($subTotalAmt) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= decimalValuePreview($totalDiscountAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= decimalValuePreview($totalDiscountAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($totalCashDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= decimalValuePreview($totalCashDiscountAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= decimalValuePreview($totalCashDiscountAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($invoiceDetails['total_tax_amt'] > 0) { ?>
                                                <?php if ($conditionGST || $gstCode == "") { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['cgst']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['sgst']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['igst']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['tcs_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['tcs_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['tcs_amount'])) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['tcs_amount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php
                                            } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['adjusted_amount'])) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview($invoiceDetails['adjusted_amount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php
                                            }
                                            ?>
                                            <p><?= decimalValuePreview($invoiceDetails['all_total_amt']) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></small>
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
                                        $totalTaxableValue += $item['hsnTaxableAmount'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="3" class="px-2">
                                                <p class="invoiceSmallFont"><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['hsnTaxableAmount']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($item['tax']) ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax']) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } ?>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($totalTaxableValue) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                        <?php } ?>
                                        <td colspan="3" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
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
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate)) ?></small>
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
                                                <a href="<?= BUCKET_URL . 'uploads/' . $company_id . '/others/' ?><?= $attachmentObj['data']['file_name'] ?>" target="_blank" class="text-primary font-bold text-decoration-none text-decoration-underline" download>
                                                    View Attachment
                                                </a>
                                            <?php }

                                            if ($invoiceDetails['tc_id'] > 0) {
                                                $tc_id = $invoiceDetails['tc_id'];
                                                $iv_tc = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE `tc_id`=$tc_id");

                                                echo '<a href="#" class="tcContent" data-toggle="modal" data-target="#tcContentModal" data-value=' . $invoiceDetails['tc_id'] . '><b>' . $iv_tc['data']['tc_variant'] . '</b></a>';
                                            }
                                            ?>

                                        </td>
                                        <td colspan="6" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <?php if ($companyData['signature'] != "") { ?>
                                                <p class="text-center sign-img">
                                                    <img width="160" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['signature'] ?>" alt="">
                                                </p>
                                            <?php } else { ?>
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <?php

                            if (isset($_GET['printChkbox'])) {

                                echo '<div class="page-break"></div>';
                                echo  $termscond;
                            }
                            ?>
                        </div>
                    </div>
                </div>
                </div>
            <?php
            }
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
            // console($invoiceDetails);
            $soData = [];
            if ($invoiceDetails['so_id'] != "") {
                $soId = $invoiceDetails['so_id'];
                $soData = $branchSoObj->fetchSoDetailsBySoId($soId)['data'][0];
            }

            $invoiceItemDetails = $branchSoObj->fetchBranchSoInvoiceItems($invoiceId)['data'];
            // console($invoiceDetails);
            $companyConfigDetails = $branchSoObj->fetchCompanyConfig($invoiceDetails['companyConfigId'])['data'];


            $company_id = $invoiceDetails['company_id'];
            $branch_id = $invoiceDetails['branch_id'];
            $location_id = $invoiceDetails['location_id'];
            // fetch company data
            $companyData = unserialize($invoiceDetails['companyDetails']);
            // console($companyData);
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
                                        $ewbSql = "SELECT * FROM `erp_e_way_bills` as ewayBill  WHERE ewayBill.irn='" . $invoiceDetails['irn'] . "' AND ewayBill.company_id=$company_id AND ewayBill.branch_id=$branch_id AND ewayBill.location_id=$location_id";
                                        $ewbData = queryGet($ewbSql)['data'];
                                    ?>
                                        <tr>
                                            <td colspan="12" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" width="150" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                        <p>EWB. No: <?= $ewbData['ewb_no'] ?></p>
                                                        <p>EWB. Date: <?= formatDateORDateTime($ewbData['ewb_date']) ?></p>
                                                        <p>EWB. Valid Till: <?= formatDateORDateTime($ewbData['ewb_valid_till']) ?></p>
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
                                            <?php if ($invoiceDetails['irn'] == "") { ?>
                                                <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <?php } ?>
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
                                            <?php } else { ?>
                                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                            <?php
                                            } ?>
                                            <?php if ($companyConfigDetails['phone'] != "") { ?>
                                                <p>Phone : <?= $companyConfigDetails['phone'] ?></p>
                                            <?php } else {
                                            ?>
                                                <p>Phone : <?= $companyData['companyPhone'] ?></p>
                                            <?php
                                            } ?>
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
                                        <!-- <td colspan="3" class="px-2">
                                            <p>Conversion Rate</p>
                                            <p><?= "1 " . $companyCurrencyName . " = " . $currencyConversionRate . " " . $customerCurrencyName ?></p>
                                        </td> -->
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
                                        <th class="invoiceTableHeadStyle">Sl No.</th>
                                        <th class="invoiceTableHeadStyle">Particulars</th>
                                        <th class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th class="invoiceTableHeadStyle">Qty</th>
                                        <th class="invoiceTableHeadStyle">UOM</th>
                                        <th class="invoiceTableHeadStyle">MRP</th>
                                        <th class="invoiceTableHeadStyle">Rate</th>
                                        <th class="invoiceTableHeadStyle">Discount</th>
                                        <th class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">IGST</th>
                                        <?php } ?>
                                        <th class="invoiceTableHeadStyle" colspan="2">Total Amount</th>
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
                                        $allSubTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $totalCashDiscountAmt += $item['cashDiscountAmount'];
                                        $subTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];

                                        $singleItemTotalDiscount = $item['totalDiscountAmt'] + $item['cashDiscountAmount'];
                                        $singleItemBaseAmount = $item['itemTargetPrice'] * $item['qty'];
                                        $singleItemTaxableAmount = $singleItemBaseAmount - $singleItemTotalDiscount;
                                        $batchString = getUsedBatchSpecificDocumentDetails($company_id, $branch_id, $location_id, $item['inventory_item_id'], $invoiceDetails['invoice_no'])['batchString'];

                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                                <p><?php echo $batchString; ?></p>
                                            </td>
                                            <td class="px-2">
                                                <p><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= decimalQuantityPreview($item['invoiceQty']) ?></p>
                                                <?php } else { ?>
                                                    <p><?= decimalQuantityPreview($item['qty']) ?></p>
                                                <?php } ?>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $uomName ?></p>
                                            </td>

                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['unitPrice'] * $currencyConversionRate) ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['itemTargetPrice'] * $currencyConversionRate) ?></p>

                                            </td>

                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($singleItemTotalDiscount * $currencyConversionRate) ?></p>
                                            </td>

                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($singleItemTaxableAmount * $currencyConversionRate) ?></p>
                                            </td>

                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class="">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>
                                                    <p class=" "><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></p>

                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" ">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>
                                                    <p class=" "><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></p>

                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2 text-right" colspan="2">
                                                    <p class=" ">(<?= decimalQuantityPreview($item['tax']) ?>%)</p>
                                                    <p class=" "><span class="rupee-symbol"></span><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>

                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2" colspan="2">
                                                <p><?= decimalValuePreview($item['totalPrice'] * $currencyConversionRate) ?></p>

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
                                            <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></small>
                                            <?php } ?> -->
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= decimalValuePreview($totalDiscountAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= decimalValuePreview($totalDiscountAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php } ?>
                                            <?php if ($totalCashDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= decimalValuePreview($totalCashDiscountAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= decimalValuePreview($totalCashDiscountAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php } ?>
                                            <?php if ($invoiceDetails['total_tax_amt'] > 0) { ?>
                                                <?php if ($conditionGST || $gstCode == "") { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                <?php } else { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['adjusted_amount'] * $currencyConversionRate)) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview($invoiceDetails['adjusted_amount'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php
                                            }
                                            ?>
                                            <p><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></small>
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
                                        // $totalTaxableValue += $item['basePrice'];
                                        $totalTaxableValue += $item['hsnTaxableAmount'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="3" class="px-2">
                                                <p class="invoiceSmallFont"><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($item['tax']) ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } ?>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } ?>
                                        <td colspan="3" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
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
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate)) ?></small>
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
                                                <a href="<?= BUCKET_URL . 'uploads/' . $company_id . '/others/' ?><?= $attachmentObj['data']['file_name'] ?>" target="_blank" class="text-primary font-bold text-decoration-none text-decoration-underline" download>
                                                    View Attachment
                                                </a>
                                            <?php }
                                            if ($invoiceDetails['tc_id'] > 0) {
                                                $tc_id = $invoiceDetails['tc_id'];
                                                $iv_tc = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE `tc_id`=$tc_id");

                                                echo '<a href="#" class="tcContent" data-toggle="modal" data-target="#tcContentModal" data-value=' . $invoiceDetails['tc_id'] . '><b>' . $iv_tc['data']['tc_variant'] . '</b></a>';
                                            }

                                            ?>
                                        </td>
                                        <td colspan="6" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <?php if ($companyData['signature'] != "") { ?>
                                                <p class="text-center sign-img">
                                                    <img width="160" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['signature'] ?>" alt="">
                                                </p>
                                            <?php } else { ?>
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                            <?php } ?>
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
                                        $ewbSql = "SELECT * FROM `erp_e_way_bills` as ewayBill  WHERE ewayBill.irn='" . $invoiceDetails['irn'] . "' AND ewayBill.company_id=$company_id AND ewayBill.branch_id=$branch_id AND ewayBill.location_id=$location_id";
                                        $ewbData = queryGet($ewbSql)['data'];
                                    ?>
                                        <tr>
                                            <td colspan="12" class="px-2">
                                                <div class="qr-section d-flex justify-content-between">
                                                    <div class="icon-company my-3">
                                                        <img src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" width="150" alt="company logo">
                                                        <p>IRN: <?= $invoiceDetails['irn'] ?></p>
                                                        <p>Ack. No: <?= $invoiceDetails['ack_no'] ?></p>
                                                        <p>Ack. Date: <?= $invoiceDetails['ack_date'] ?></p>
                                                        <p>EWB. No: <?= $ewbData['ewb_no'] ?></p>
                                                        <p>EWB. Date: <?= formatDateORDateTime($ewbData['ewb_date']) ?></p>
                                                        <p>EWB. Valid Till: <?= formatDateORDateTime($ewbData['ewb_valid_till']) ?></p>
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
                                            <?php if ($invoiceDetails['irn'] == "") { ?>
                                                <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <?php } ?>
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
                                            <?php } else { ?>
                                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                            <?php
                                            } ?>
                                            <?php if ($companyConfigDetails['phone'] != "") { ?>
                                                <p>Phone : <?= $companyConfigDetails['phone'] ?></p>
                                            <?php } else {
                                            ?>
                                                <p>Phone : <?= $companyData['companyPhone'] ?></p>
                                            <?php
                                            } ?>
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
                                        <!-- <td colspan="4" class="px-2">
                                            <p>Conversion Rate</p>
                                            <p><?= "1 " . $companyCurrencyName . " = " . $currencyConversionRate . " " . $customerCurrencyName ?></p>
                                        </td> -->
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
                                        <th class="invoiceTableHeadStyle">Sl No.</th>
                                        <th class="invoiceTableHeadStyle">Particulars</th>
                                        <th class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th class="invoiceTableHeadStyle">Qty</th>
                                        <th class="invoiceTableHeadStyle">MRP</th>
                                        <th class="invoiceTableHeadStyle">Unit Rate</th>
                                        <th class="invoiceTableHeadStyle">Trade Discount</th>
                                        <th class="invoiceTableHeadStyle">Gross Amt.</th>
                                        <th class="invoiceTableHeadStyle">Cash Discount</th>
                                        <th class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">IGST</th>
                                        <?php } ?>
                                        <th class="invoiceTableHeadStyle">Total Amount</th>
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
                                        $allSubTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $subTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];
                                        $taxableAmount = ($item['itemTargetPrice'] * $item['qty']) - $item['totalDiscountAmt'] - $item['cashDiscountAmount'];
                                        $batchString = getUsedBatchSpecificDocumentDetails($company_id, $branch_id, $location_id, $item['inventory_item_id'], $invoiceDetails['invoice_no'])['batchString'];

                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                                <p><?php echo $batchString; ?></p>
                                            </td>
                                            <td class="px-2">
                                                <p><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= decimalQuantityPreview($item['invoiceQty']) ?></p>
                                                <?php } else { ?>
                                                    <p><?= decimalQuantityPreview($item['qty']) ?></p>
                                                <?php } ?>
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['unitPrice'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['unitPrice'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= $item['itemTargetPrice'] * $currencyConversionRate ?></p>
                                            </td>

                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?= decimalQuantityPreview($item['totalDiscount']) ?>%)</span> <?= decimalValuePreview($item['totalDiscountAmt'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalDiscountAmt'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= (($item['itemTargetPrice'] * $item['qty']) - $item['totalDiscountAmt']) * $currencyConversionRate ?></p>
                                            </td>

                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?= decimalQuantityPreview($item['cashDiscount']) ?>%)</span> <?= decimalValuePreview($item['cashDiscountAmount'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['cashDiscountAmount'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= $taxableAmount * $currencyConversionRate ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview(($item['itemTargetPrice'] * $item['qty']) * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></p>

                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>
                                                    <p><?php echo decimalValuePreview(((($item['unitPrice'] * $item['qty']) - $item['totalDiscountAmt']) / $item['qty'])); ?> </p>

                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></p>

                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2 text-right" colspan="2">
                                                    <p class=" font-bold">(<?= decimalQuantityPreview($item['tax']) ?>%)</p>
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>

                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['totalPrice'] * $currencyConversionRate) ?></p>

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
                                            <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></small>
                                            <?php } ?> -->
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                                <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php } else { ?>
                                                <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php } ?>

                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['adjusted_amount']) * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['adjusted_amount'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php
                                            }
                                            ?>
                                            <p><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></small>
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
                                        // $totalTaxableValue += $item['basePrice'];
                                        $totalTaxableValue += $item['hsnTaxableAmount'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="4" class="px-2">
                                                <p class="invoiceSmallFont"><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate, 2) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($item['tax']) ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } ?>
                                            <td colspan="4" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="4">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } ?>
                                        <td colspan="4" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
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
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate)) ?></small>
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
                                            <?php
                                            if ($invoiceDetails['tc_id'] > 0) {
                                                $tc_id = $invoiceDetails['tc_id'];
                                                $iv_tc = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE `tc_id`=$tc_id");

                                                echo '<a href="#" class="tcContent" data-toggle="modal" data-target="#tcContentModal" data-value=' . $invoiceDetails['tc_id'] . '><b>' . $iv_tc['data']['tc_variant'] . '</b></a>';
                                            }
                                            ?>
                                        </td>
                                        <td colspan="6" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <?php if ($companyData['signature'] != "") { ?>
                                                <p class="text-center sign-img">
                                                    <img width="160" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['signature'] ?>" alt="">
                                                </p>
                                            <?php } else { ?>
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php
            } else if ($templateId == 2) { ?>
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
                                                        <img src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" width="150" alt="company logo">
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
                                            <?php if ($invoiceDetails['irn'] == "") { ?>
                                                <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <?php } ?>
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
                                            <?php } else { ?>
                                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                            <?php
                                            } ?>
                                            <?php if ($companyConfigDetails['phone'] != "") { ?>
                                                <p>Phone : <?= $companyConfigDetails['phone'] ?></p>
                                            <?php } else {
                                            ?>
                                                <p>Phone : <?= $companyData['companyPhone'] ?></p>
                                            <?php
                                            } ?>
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
                                        <!-- <td colspan="3" class="px-2">
                                            <p>Conversion Rate</p>
                                            <p><?= "1 " . $companyCurrencyName . " = " . $currencyConversionRate . " " . $customerCurrencyName ?></p>
                                        </td> -->

                                        <td colspan="3" class="px-2">
                                            <?php if (count($soData) > 0) { ?>
                                                <p>SO Number</p>
                                                <p class="font-bold"><?= $soData['so_number'] ?></p>
                                                <br>
                                            <?php } ?>
                                            <p>Customer Order No</p>
                                            <p class="font-bold"><?= $soData['customer_po_no'] ?></p>
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
                                        <th class="invoiceTableHeadStyle">Sl No.</th>
                                        <th class="invoiceTableHeadStyle">Particulars</th>
                                        <th class="invoiceTableHeadStyle">HSN/SAC</th>
                                        <th class="invoiceTableHeadStyle">Qty</th>
                                        <th class="invoiceTableHeadStyle">UOM</th>
                                        <th class="invoiceTableHeadStyle">Rate</th>
                                        <th class="invoiceTableHeadStyle">Discount</th>
                                        <th class="invoiceTableHeadStyle">Taxable Amount</th>
                                        <?php
                                        if ($conditionGST || $gstCode == "") {
                                        ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle">CGST</th>
                                            <th class="text-center text-bold invoiceTableHeadStyle">SGST</th>
                                        <?php } else { ?>
                                            <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">IGST</th>
                                        <?php } ?>
                                        <th class="invoiceTableHeadStyle" colspan="2">Total Amount</th>
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
                                        $allSubTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['totalDiscountAmt'];
                                        $totalCashDiscountAmt += $item['cashDiscountAmount'];
                                        $subTotalAmt += $item['itemTargetPrice'] * $item['qty'];
                                        $totalAmt += $item['totalPrice'];

                                        $singleItemTotalDiscount = $item['totalDiscountAmt'] + $item['cashDiscountAmount'];
                                        $singleItemBaseAmount = $item['itemTargetPrice'] * $item['qty'];
                                        $singleItemTaxableAmount = $singleItemBaseAmount - $singleItemTotalDiscount;
                                        $batchString = getUsedBatchSpecificDocumentDetails($company_id, $branch_id, $location_id, $item['inventory_item_id'], $invoiceDetails['invoice_no'])['batchString'];

                                    ?>
                                        <tr>
                                            <td class="px-2"><?= $i++ ?></td>
                                            <td class="px-2">
                                                <p class="font-bold"><?= $item['itemName'] ?></p>
                                                <p class=""><?= $item['itemCode'] ?></p>
                                                <p class=""><?= $item['itemRemarks'] ?></p>
                                                <p><?php echo $batchString; ?></p>
                                            </td>
                                            <td class="px-2">
                                                <p><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td class="px-2">
                                                <?php if ($invoiceDetails['type'] == 'project') { ?>
                                                    <p><?= decimalQuantityPreview($item['invoiceQty']) ?></p>
                                                <?php } else { ?>
                                                    <p><?= decimalQuantityPreview($item['qty']) ?></p>
                                                <?php } ?>
                                            </td>
                                            <td class="px-2">
                                                <p><?= $uomName ?></p>
                                            </td>
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['itemTargetPrice'] * $currencyConversionRate) ?></p>

                                            </td>

                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview(($singleItemTotalDiscount * $currencyConversionRate)) ?></p>
                                            </td>

                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($singleItemTaxableAmount * $currencyConversionRate) ?></p>
                                            </td>

                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class="">(<?= decimalQuantityPreview($itemGstPer) ?>%)</p>
                                                    <p class=" "><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></p>

                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" ">(<?= decimalQuantityPreview($itemGstPer, 2) ?>%)</p>
                                                    <p class=" "><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></p>

                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2 text-right" colspan="2">
                                                    <p class=" ">(<?= decimalQuantityPreview($item['tax']) ?>%)</p>
                                                    <p class=" "><span class="rupee-symbol"></span><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>

                                                </td>
                                            <?php } ?>
                                            <td class="text-right px-2" colspan="2">
                                                <p><?= decimalValuePreview($item['totalPrice'] * $currencyConversionRate) ?></p>

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
                                            <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($subTotalAmt * $currencyConversionRate) ?></small>
                                            <?php } ?> -->
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= decimalValuePreview($totalDiscountAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= decimalValuePreview($totalDiscountAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php } ?>
                                            <?php if ($totalCashDiscountAmt > 0) { ?>
                                                <p><span class="rupee-symbol pr-1"></span>(-)<?= decimalValuePreview($totalCashDiscountAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(-)<?= decimalValuePreview($totalCashDiscountAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php } ?>
                                            <?php if ($invoiceDetails['total_tax_amt'] > 0) { ?>
                                                <?php if ($conditionGST || $gstCode == "") { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['cgst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['sgst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                <?php } else { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['igst'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            if ($invoiceDetails['adjusted_amount'] != 0) {
                                            ?>
                                                <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview(abs($invoiceDetails['adjusted_amount'] * $currencyConversionRate)) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= decimalValuePreview($invoiceDetails['adjusted_amount'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            <?php
                                            }
                                            ?>
                                            <p><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate) ?></small>
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
                                        // $totalTaxableValue += $item['basePrice'];
                                        $totalTaxableValue += $item['hsnTaxableAmount'];
                                        $totalCgstSgstAmt += $itemGstAmtHSN;
                                        $allTotalTaxAmt += $item['totalTax'];
                                    ?>
                                        <tr>
                                            <td colspan="3" class="px-2">
                                                <p class="invoiceSmallFont"><?= hsnInProperFormat($item['hsnCode']) ?></p>
                                            </td>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['hsnTaxableAmount'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($itemGstPerHSN) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmtHSN * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-right px-2">
                                                    <p class="invoiceSmallFont"><?= decimalQuantityPreview($item['tax']) ?>%</p>
                                                </td>
                                                <td class="text-right px-2" colspan="2">
                                                    <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>
                                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                    <?php } ?> -->
                                                </td>
                                            <?php } ?>
                                            <td colspan="3" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td class="text-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont">Total</p>
                                        </td>
                                        <td class="text-right font-bold px-2" colspan="3">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalTaxableValue * $currencyConversionRate) ?></small>
                                            <?php } ?> -->
                                        </td>
                                        <?php if ($conditionGST || $gstCode == "") { ?>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                            <td colspan="2" class="text-right px-2">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($totalCgstSgstAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } else { ?>
                                            <td class="text-right font-bold px-2" colspan="3">
                                                <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></p>
                                                <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
                                                <?php } ?> -->
                                            </td>
                                        <?php } ?>
                                        <td colspan="3" class="text-right font-bold px-2">
                                            <p class="invoiceSmallFont"><?= decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($allTotalTaxAmt * $currencyConversionRate) ?></small>
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
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(decimalValuePreview($invoiceDetails['all_total_amt'] * $currencyConversionRate)) ?></small>
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
                                                <a href="<?= BUCKET_URL . 'uploads/' . $company_id . '/others/' ?><?= $attachmentObj['data']['file_name'] ?>" target="_blank" class="text-primary font-bold text-decoration-none text-decoration-underline" download>
                                                    View Attachment
                                                </a>
                                            <?php }
                                            if ($invoiceDetails['tc_id'] > 0) {
                                                $tc_id = $invoiceDetails['tc_id'];
                                                $iv_tc = queryGet("SELECT * FROM `erp_terms_and_condition_format` WHERE `tc_id`=$tc_id");

                                                echo '<a href="#" class="tcContent" data-toggle="modal" data-target="#tcContentModal" data-value=' . $invoiceDetails['tc_id'] . '><b>' . $iv_tc['data']['tc_variant'] . '</b></a>';
                                            }
                                            ?>

                                        </td>
                                        <td colspan="6" class="text-right px-2">
                                            <p class="text-center font-bold">for <?= $companyData['company_name'] ?></p>
                                            <?php if ($companyData['signature'] != "") { ?>
                                                <p class="text-center sign-img">
                                                    <img width="160" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['signature'] ?>" alt="">
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                                </p>
                                            <?php } else { ?>
                                                <p class="text-center sign-img">Authorized Signatory</p>
                                            <?php } ?>
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