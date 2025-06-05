
<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// error_log("An error occurred", 3, "/var/log/php_errors.log");
require_once("../../../app/v1/connection-branch-admin.php");
//administratorLocationAuth();
require_once("../../common/header.php");
require_once("../../common/navbar.php");
require_once("../../common/sidebar.php");
require_once("../../common/pagination.php");
require_once("../../../app/v1/functions/company/func-branches.php");
require_once("../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../../app/v1/functions/branch/func-items-controller.php");

// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩
// ₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩₩

$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
$customerDetailsObj = new CustomersController();

$invoice_id = base64_decode($_GET['invoice_id']);

$company = $BranchSoObj->fetchCompanyDetails()['data'];
$currencyDetails = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data'];
$currencyIcon = $currencyDetails['currency_icon'];
$currencyName = $currencyDetails['currency_name'];

$invoiceDetails = $BranchSoObj->fetchBranchSoInvoiceById($invoice_id)['data'][0];
$invoiceItemDetails = $BranchSoObj->fetchBranchSoInvoiceItems($invoice_id)['data'];
$customerDetails = $BranchSoObj->fetchCustomerDetails($invoiceDetails['customer_id'])['data'][0];
$customerAddressDetails = $BranchSoObj->fetchCustomerAddressDetails($customerDetails['customer_id'])['data'];
$companyData = unserialize($invoiceDetails['companyDetails']);
$customerData = unserialize($invoiceDetails['customerDetails']);
$encodeInvId = base64_encode($invoice_id);
$conversion_rate = 1;
$conversion_currency_name = $invoiceDetails['currency_name'] ?? "";
if (isset($_GET['conversion'])) {
    if ($invoiceDetails['conversion_rate'] != "") {
        $conversion_rate = $invoiceDetails['conversion_rate'];
    } else {
        $conversion_rate = 1;
    }
}

$company_bank_details = unserialize($invoiceDetails['company_bank_details']);

$invoiceItemDetailsGroupByHSN = $BranchSoObj->fetchBranchSoInvoiceItemsGroupByHSN($invoice_id)['data'];
?>
<style>
    .wrapper {
        min-height: auto !important;
    }

    @media print {
        .sidebar-mini.sidebar-collapse .content-wrapper {
            margin-left: 0 !important;
        }
    }
</style>

<div class="content-wrapper">
    <?php if (isset($_GET['conversion'])) { ?>
        <!-- currance conversion -->
        <div class="card classic-view bg-transparent">
            <div class="card-body classic-view-so-table" style="overflow: auto;">
                <!-- <a href="classic-view/invoice-preview-print.php?invoice_id=<?= base64_encode($invoiceDetails['so_invoice_id']) ?>&conversion=<?= $invoiceDetails['conversion_rate'] ?>" class="btn btn-primary classic-view-btn float-right">Print</a> -->
                <div class="printable-view">
                    <h3 class="h3-title text-center font-bold text-sm mb-4">Tax Invoice</h3>
                    <table class="classic-view table-bordered">
                        <tbody>
                            <tr>
                                <td rowspan="3" colspan="7" class="px-2">
                                    <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                    <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                    <p><?= $companyData['location_building_no'] ?></p>
                                    <p>Flat No.<?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
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
                                    <p>Dispatch Doc No.</p>
                                    <?php if ($invoiceDetails['pgi_no'] != "") { ?>
                                        <p><?= $invoiceDetails['pgi_no'] ?></p>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-2">
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
                                </td>
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
                            $customerGstin = substr($customerData['customer_gstin'], 0, 2);
                            $conditionGST = $branchGstin == $customerGstin;
                            ?>
                            <tr>
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">Sl No.</th>
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">Particulars</th>
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">HSN/SAC</th>
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">Quantity</th>
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">UOM</th>
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">Rate</th>
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">Taxable Amount</th>
                                <!-- <th rowspan="2">Sub Total</th> -->
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">Discount</th>
                                <?php
                                if ($conditionGST || $customerGstin == "") {
                                ?>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                <?php } else { ?>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">IGST</th>
                                <?php } ?>
                                <th class="text-bold invoiceTableHeadStyle" rowspan="2">Total Amount</th>
                            </tr>
                            <tr>
                                <?php if ($conditionGST || $customerGstin == "") { ?>
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
                            ?>
                                <tr>
                                    <td class="px-2"><?= $i++ ?></td>
                                    <td class="px-2">
                                        <p class="font-bold"><?= $item['itemName'] ?></p>
                                        <p class=""><?= $item['itemCode'] ?></p>
                                        <p class=""><?= $item['itemRemarks'] ?></p>
                                    </td>
                                    <td>
                                        <p><?= $item['hsnCode'] ?></p>
                                    </td>
                                    <td class="px-2">
                                        <?php if ($invoiceDetails['type'] == 'project') { ?>
                                            <p><?= $item['invoiceQty'] ?></p>
                                        <?php } else { ?>
                                            <p><?= $item['qty'] ?></p>
                                        <?php } ?>
                                    </td>
                                    <td class="px-2"><?= $uomName ?></td>
                                    <td class="text-right px-2"><?= number_format($item['unitPrice'] * $conversion_rate, 2) ?></td>
                                    <td class="text-right px-2"><?= number_format($subTotalAmt * $conversion_rate, 2) ?></td>
                                    <td class="text-right px-2">
                                        <p><?= number_format($item['totalDiscountAmt'] * $conversion_rate, 2) ?></p>
                                        <p class=" font-bold">(<?= $item['totalDiscount'] ?>%)</p>
                                    </td>
                                    <?php
                                    if ($conditionGST || $customerGstin == "") {
                                        $itemGstAmt = $item['totalTax'] / 2;
                                        $itemGstPer = $item['tax'] / 2;
                                    ?>
                                        <td class="text-right px-2">
                                            <p class=" font-bold"><?= $itemGstPer ?>%</p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class=" font-bold"><span class="rupee-symbol"><?= $conversion_currency_name ?></span><?= number_format($itemGstAmt * $conversion_rate, 2) ?></p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class=" font-bold"><?= $itemGstPer ?>%</p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class=" font-bold"><span class="rupee-symbol"><?= $conversion_currency_name ?></span><?= number_format($itemGstAmt * $conversion_rate, 2) ?></p>
                                        </td>
                                    <?php } else { ?>
                                        <td class="px-2">
                                            <p class=" font-bold"><?= $item['tax'] ?>%</p>
                                        </td>
                                        <td class="px-2">
                                            <p class=" font-bold"><span class="rupee-symbol"><?= $conversion_currency_name ?></span><?= number_format($item['totalTax'] * $conversion_rate, 2) ?></p>
                                        </td>
                                    <?php } ?>
                                    <td class="text-right px-2">
                                        <p><?= number_format($item['totalPrice'] * $conversion_rate, 2) ?></p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="11" class="font-bold text-right px-2">
                                    <p>Sub Total</p>
                                    <?php if ($conditionGST || $customerGstin == "") { ?>
                                        <p>CGST</p>
                                        <p>SGST</p>
                                    <?php } else { ?>
                                        <p>IGST</p>
                                    <?php } ?>
                                    <?php
                                    if ($invoiceDetails['adjusted_amount'] != 0) {
                                        echo "<p>Round Off</p>";
                                    }
                                    ?>
                                    <p>Grand Total</p>
                                </td>
                                <td colspan="2" class="text-right font-bold px-2">
                                    <p><span class="rupee-symbol pr-1"></span><?= number_format($subTotalAmt * $conversion_rate, 2) ?></p>
                                    <?php if ($conditionGST || $customerGstin == "") { ?>
                                        <p><span class="pr-1"></span><?= number_format($invoiceDetails['cgst'] * $conversion_rate, 2) ?></p>
                                        <p><span class="pr-1"></span><?= number_format($invoiceDetails['sgst'] * $conversion_rate, 2) ?></p>
                                    <?php } else { ?>
                                        <p><span class="pr-1"></span><?= number_format($invoiceDetails['igst'] * $conversion_rate, 2) ?></p>
                                    <?php } ?>
                                    <?php
                                    if ($invoiceDetails['adjusted_amount'] != 0) {
                                    ?>
                                        <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= number_format(abs($invoiceDetails['adjusted_amount'] * $conversion_rate), 2) ?></p>
                                    <?php
                                    }
                                    ?>
                                    <p><span class="rupee-symbol pr-1"><?= $currencyName ?></span><?= number_format($invoiceDetails['all_total_amt'] * $conversion_rate, 2) ?></p>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th class="text-bold invoiceHSNTableHeadStyle" colspan="3" rowspan="2">HSN/SAC</th>
                                <th class="text-bold invoiceHSNTableHeadStyle" colspan="3" rowspan="2">Taxable Value</th>
                                <?php if ($conditionGST || $customerGstin == "") { ?>
                                    <th colspan="2" class="text-bold text-center border invoiceHSNTableHeadStyle">Central Tax</th>
                                    <th colspan="2" class="text-bold text-center border invoiceHSNTableHeadStyle">State Tax</th>
                                <?php } else { ?>
                                    <th colspan="3" class="text-bold text-center invoiceHSNTableHeadStyle">IGST</th>
                                <?php } ?>
                                <th class="text-bold invoiceHSNTableHeadStyle" colspan="3" rowspan="2">Total Tax Amount</th>
                            </tr>
                            <tr>
                                <?php if ($conditionGST || $customerGstin == "") { ?>
                                    <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                    <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                    <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                    <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
                                <?php } else { ?>
                                    <th class="text-bold invoiceHSNTableHeadStyle">Rate</th>
                                    <th class="text-bold invoiceHSNTableHeadStyle">Amount</th>
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
                                    <td colspan="3">
                                        <p class="invoiceSmallFont">
                                            <?= $item['hsnCode'] ?>
                                        </p>
                                    </td>
                                    <td colspan="3" class="text-right">
                                        <p class="invoiceSmallFont">
                                            <?= number_format($item['basePrice'] * $conversion_rate, 2) ?>
                                        </p>
                                    </td>
                                    <?php if ($conditionGST || $customerGstin == "") { ?>
                                        <td class="text-right">
                                            <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                        </td>
                                        <td class="text-right">
                                            <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN, 2) ?></p>
                                        </td>
                                        <td class="text-right">
                                            <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                        </td>
                                        <td class="text-right">
                                            <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN, 2) ?></p>
                                        </td>
                                    <?php } else { ?>
                                        <td class="text-right">
                                            <p class="invoiceSmallFont"><?= $item['tax'] ?>%</p>
                                        </td>
                                        <td class="text-right">
                                            <p class="invoiceSmallFont"><?= number_format($item['totalTax'] * $conversion_rate, 2) ?></p>
                                        </td>
                                    <?php } ?>
                                    <td class="text-right" colspan="3">
                                        <p class="invoiceSmallFont"><?= number_format($item['totalTax'] * $conversion_rate, 2) ?></p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="3" class="text-bold">
                                    <p class="invoiceSmallFont">Total</p>
                                </td>
                                <td colspan="3" class="text-bold">
                                    <p class="invoiceSmallFont"><?= number_format($totalTaxableValue * $conversion_rate, 2) ?></p>
                                </td>
                                <?php if ($conditionGST || $customerGstin == "") { ?>
                                    <td colspan="2" class="text-right">
                                        <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt * $conversion_rate, 2) ?></p>
                                    </td>
                                    <td colspan="2" class="text-right">
                                        <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt * $conversion_rate, 2) ?></p>
                                    </td>
                                <?php } else { ?>
                                    <td colspan="3" class="text-right">
                                        <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt * $conversion_rate, 2) ?></p>
                                    </td>
                                <?php } ?>
                                <td colspan="3" class="text-right font-bold">
                                    <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt * $conversion_rate, 2) ?></p>
                                </td>
                            </tr>
                        </tbody>

                        <tbody class="footer-text">
                            <tr>
                                <td colspan="7">
                                    <p>Amount Chargeable (in words)</p>
                                    <p class="font-bold"><?= $invoiceDetails['currency_name'] . ' ' . number_to_words_indian_rupees($invoiceDetails['all_total_amt'] * $conversion_rate); ?> ONLY</p>
                                </td>
                                <td colspan="6">
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
                                <td colspan="7">
                                    <p>Remarks: <?= $invoiceDetails['remarks'] ?></p>
                                    <p>Declaration: <?= $invoiceDetails['declaration_note'] ?></p>
                                    <p><?= $companyData['company_footer'] ?></p>
                                    <p>Created By: <strong><?= getCreatedByUser($invoiceDetails['created_by']); ?></strong></p>
                                </td>
                                <td colspan="6">
                                    <p class="text-center font-bold">for <?= $companyData['branch_name'] ?></p>
                                    <p class="text-center sign-img"><img width="160" src="../../public/storage/<?= $companyData['signature'] ?>" alt=""></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="card classic-view bg-transparent">
            <div class="card-body classic-view-so-table" style="overflow: auto;">
                <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" onclick="window.print();">Print</button> -->

                <div class="printable-view">
                    <h3 class="h3-title text-center font-bold text-sm mb-4">Tax Invoice</h3>
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
                                    <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                    <!-- <p class="font-bold"> <?= $companyData['branch_name'] ?></p> -->
                                    <p><?= $companyData['location_building_no'] ?></p>
                                    <p>Flat No.<?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
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
                            $customerGstin = substr($customerData['customer_gstin'], 0, 2);
                            $conditionGST = $branchGstin == $customerGstin;
                            ?>
                            <tr>
                                <th rowspan="2" class="invoiceTableHeadStyle">Sl No.</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Particulars</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">HSN/SAC</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Quantity</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">UOM</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Rate</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Taxable Amount</th>
                                <!-- <th rowspan="2">Sub Total</th> -->
                                <th rowspan="2" class="invoiceTableHeadStyle">Discount</th>
                                <?php
                                if ($conditionGST || $customerGstin == "") {
                                ?>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                <?php } else { ?>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="3">IGST</th>
                                <?php } ?>
                                <th rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                            </tr>
                            <tr>
                                <?php if ($conditionGST || $customerGstin == "") { ?>
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
                                    </td>
                                    <td class="text-right px-2">
                                        <p><?= number_format($item['unitPrice'] * $item['qty'], 2) ?></p>
                                    </td>
                                    <!-- <td class="border-bottom-0"><?= $subTotalAmt ?></td> -->
                                    <td class="text-right px-2">
                                        <p><?= number_format($item['totalDiscountAmt'], 2) ?></p>
                                        <p class=" font-bold">(<?= $item['totalDiscount'] ?>)%</p>
                                    </td>
                                    <?php
                                    if ($conditionGST || $customerGstin == "") {
                                        $itemGstAmt = $item['totalTax'] / 2;
                                        $itemGstPer = $item['tax'] / 2;
                                    ?>
                                        <td class="text-right px-2">
                                            <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class=" font-bold"><span class="rupee-symbol"><?= $currencyName ?></span><?= number_format($itemGstAmt, 2) ?></p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class=" font-bold"><?= number_format($itemGstPer, 2) ?>%</p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class=" font-bold"><span class="rupee-symbol"><?= $currencyName ?></span><?= number_format($itemGstAmt, 2) ?></p>
                                        </td>
                                    <?php } else { ?>
                                        <td class="px-2">
                                            <p class=" font-bold"><?= $item['tax'] ?>%</p>
                                        </td>
                                        <td class="px-2" colspan="2">
                                            <p class=" font-bold"><span class="rupee-symbol"><?= $currencyName ?></span><?= number_format($item['totalTax'], 2) ?></p>
                                        </td>
                                    <?php } ?>
                                    <td class="text-right px-2">
                                        <p><?= number_format($item['totalPrice'], 2) ?></p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="11" class="font-bold text-right px-2">
                                    <p>Sub Total</p>
                                    <?php if ($conditionGST || $customerGstin == "") { ?>
                                        <p>CGST</p>
                                        <p>SGST</p>
                                    <?php } else { ?>
                                        <p>IGST</p>
                                    <?php } ?>
                                    <?php
                                    if ($invoiceDetails['adjusted_amount'] != 0) {
                                        echo "<p>Round Off</p>";
                                    }
                                    ?>
                                    <p>Grand Total</p>
                                </td>
                                <td colspan="2" class="text-right font-bold px-2">
                                    <p><span class="rupee-symbol pr-1"></span><?= number_format($subTotalAmt, 2) ?></p>
                                    <?php if ($conditionGST || $customerGstin == "") { ?>
                                        <p><span class="pr-1"></span><?= number_format($invoiceDetails['cgst'], 2) ?></p>
                                        <p><span class="pr-1"></span><?= number_format($invoiceDetails['sgst'], 2) ?></p>
                                    <?php } else { ?>
                                        <p><span class="pr-1"></span><?= number_format($invoiceDetails['igst'], 2) ?></p>
                                    <?php } ?>

                                    <?php
                                    if ($invoiceDetails['adjusted_amount'] != 0) {
                                    ?>
                                        <p>(<?= $invoiceDetails['adjusted_amount'] >= 0 ? "+" : "-" ?>)<?= number_format(abs($invoiceDetails['adjusted_amount']), 2) ?></p>
                                    <?php
                                    }
                                    ?>
                                    <p><span class="rupee-symbol pr-1"><?= $currencyName ?></span><?= number_format($invoiceDetails['all_total_amt'], 2) ?></p>
                                </td>
                            </tr>
                        </tbody>

                        <tbody>
                            <tr>
                                <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">HSN/SAC</th>
                                <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Taxable Value</th>
                                <?php if ($conditionGST || $customerGstin == "") { ?>
                                    <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">Central Tax</th>
                                    <th colspan="2" class="text-bold text-center invoiceHSNTableHeadStyle">State Tax</th>
                                <?php } else { ?>
                                    <th colspan="3" class="text-bold text-center invoiceHSNTableHeadStyle">IGST</th>
                                <?php } ?>
                                <th colspan="3" class="text-bold invoiceHSNTableHeadStyle" rowspan="2">Total Tax Amount</th>
                            </tr>
                            <tr>
                                <?php if ($conditionGST || $customerGstin == "") { ?>
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
                                    </td>
                                    <?php if ($conditionGST || $customerGstin == "") { ?>
                                        <td class="text-right px-2">
                                            <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN, 2) ?></p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class="invoiceSmallFont"><?= $itemGstPerHSN ?>%</p>
                                        </td>
                                        <td class="text-right px-2">
                                            <p class="invoiceSmallFont"><?= number_format($itemGstAmtHSN, 2) ?></p>
                                        </td>
                                    <?php } else { ?>
                                        <td class="text-right px-2">
                                            <p class="invoiceSmallFont"><?= $item['tax'] ?>%</p>
                                        </td>
                                        <td class="text-right px-2" colspan="2">
                                            <p class="invoiceSmallFont"><?= number_format($item['totalTax'], 2) ?></p>
                                        </td>
                                    <?php } ?>
                                    <td colspan="3" class="text-right px-2">
                                        <p class="invoiceSmallFont"><?= number_format($item['totalTax'], 2) ?></p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td class="text-bold px-2" colspan="3">
                                    <p class="invoiceSmallFont">Total</p>
                                </td>
                                <td class="text-right font-bold px-2" colspan="3">
                                    <p class="invoiceSmallFont"><?= number_format($totalTaxableValue, 2) ?></p>
                                </td>
                                <?php if ($conditionGST || $customerGstin == "") { ?>
                                    <td colspan="2" class="text-right px-2">
                                        <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt, 2) ?></p>
                                    </td>
                                    <td colspan="2" class="text-right px-2">
                                        <p class="invoiceSmallFont"><?= number_format($totalCgstSgstAmt, 2) ?></p>
                                    </td>
                                <?php } else { ?>
                                    <td class="text-right font-bold px-2" colspan="3">
                                        <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt, 2) ?></p>
                                    </td>
                                <?php } ?>
                                <td colspan="3" class="text-right font-bold px-2">
                                    <p class="invoiceSmallFont"><?= number_format($allTotalTaxAmt, 2) ?></p>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="7" class="px-2">
                                    <p>Amount Chargeable (in words)</p>
                                    <p class="font-bold"><?= number_to_words_indian_rupees($invoiceDetails['all_total_amt']); ?> ONLY</p>
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
    <?php } ?>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    $(document).ready(function() {
        // // Clone the invoice content and show it in a new window
        // var printWindow = window.open();
        // var printableDiv = $(".classic-view-so-table").clone();

        // // Add the cloned content to the new window
        // printWindow.document.open();
        // printWindow.document.write('<html><head><title>Print Invoice</title>');
        // printWindow.document.write('<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.4/css/bootstrap.css">');
        // printWindow.document.write('<link rel="stylesheet" type="text/css" href="../../../../public/assets/listing.css">');
        // printWindow.document.write('</head><body>');
        // printWindow.document.write(printableDiv.html());
        // printWindow.document.write('</body></html>');
        // printWindow.document.close();

        // // Print the new window
        // printWindow.print();
        // printWindow.close();

        window.print();
    });
</script>