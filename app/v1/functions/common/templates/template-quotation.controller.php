<?php
class TemplateQuotationController
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

    public function printQuotation($quotationId = 0, $company_id = 0, $branch_id = 0, $location_id = 0,$printFlag=false, $templateId = 0, $redirectUrl = "")
    {
        $branchSoObj = new BranchSo();
        $quotationDetailsObj = $branchSoObj->getQuotations($quotationId);
        // console($quotationDetailsObj);

        if (count($quotationDetailsObj['data']) <= 0) {
            echo '<p class="text-warning text-center mt-5">Invoice Not found!</p>';
            // if ($redirectUrl != "") {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!", $redirectUrl);
            // } else {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!");
            // }
        } else {
            $quotationDetails = $quotationDetailsObj['data'];

            $company_id = $quotationDetails['company_id'];
            $branch_id = $quotationDetails['branch_id'];
            $location_id = $quotationDetails['location_id'];

            $quotationItemDetails = $branchSoObj->getQuotationItems($quotationId)['data'];         

            $customerId = $quotationDetails['customer_id'];
            $customerCurrencyName = $quotationDetails['currency_name'] ?? "";

            // fetch customer details
            $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'");
            $customerData = $customerDetailsObj['data'];

            // fetch company details
            $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
            $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
            $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
            $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
            $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
            $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);


            $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
            $companyCurrencyName = $currencyDetails['currency_name'];
            $currencyConversionRate = $quotationDetails['conversion_rate'] != "" ? $quotationDetails['conversion_rate'] : 1;

            $attachmentObj = $branchSoObj->getQuotationAttachments($quotationId);

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
                            <h3 class="h3-title text-center font-bold text-sm mb-4">Quotation</h3>
                            <table class="classic-view table-bordered tableBorder">
                                <tbody>
                                    <tr>
                                        <td rowspan="3" colspan="5" class="px-2">
                                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
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
                                        <td colspan="4" class="px-2">
                                            <p>Quotation No.</p>
                                            <p class="font-bold"><?= $quotationDetails['quotation_no'] ?></p>
                                        </td>
                                        <td colspan="5" class="px-2">
                                            <p>Dated</p>
                                            <p class="font-bold"><?php $invDate = date_create($quotationDetails['posting_date']);
                                                                    echo date_format($invDate, "F d,Y"); ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="px-2">
                                            <p>Mode/Terms of Payment</p>
                                            <?php if ($quotationDetails['credit_period'] != "") { ?>
                                                <p><?= $quotationDetails['credit_period'] ?></p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="px-2">
                                            <p>Buyer (Bill to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $quotationDetails['customer_billing_address'] ?></p>
                                            <p>GSTIN/UIN : <?= $customerData['customer_gstin'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                        </td>
                                        <td colspan="5" class="px-2">
                                            <p>Consignee (Ship to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $quotationDetails['customer_shipping_address'] ?></p>
                                            <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                            <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <?php
                                    $branchGstCode = substr($companyData['branch_gstin'], 0, 2);
                                    $customerGstCode = substr($customerData['customer_gstin'], 0, 2);

                                    $gstCode = 0;
                                    if ($customerGstCode == "") {
                                        $gstCode = $quotationDetails['placeOfSupply'] ?? 0;
                                    } else {
                                        $gstCode = substr($customerData['customer_gstin'], 0, 2);
                                    }

                                    $conditionGST = $branchGstCode == $gstCode ?? 0;
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
                                    foreach ($quotationItemDetails as $key => $item) {
                                        $uomName = getUomDetail($item['uom'])['data']['uomName'];

                                        $totalTaxAmt += $item['totalTax'];
                                        $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                                        $totalDiscountAmt += $item['itemTotalDiscount'];
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
                                                <?php if ($quotationDetails['type'] == 'project') { ?>
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
                                            <td class="text-right px-2">
                                                <p><?= decimalValuePreview($item['unitPrice'] * $item['qty']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview(($item['unitPrice'] * $item['qty']) * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <!-- <td class="border-bottom-0"><?= $subTotalAmt ?></td> -->
                                            <td class="text-right px-2">
                                                <p><span class="text-small">(<?=decimalQuantityPreview( $item['cashDiscount']) ?>%)</span> <?= decimalValuePreview($item['cashDiscountAmount']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['itemTotalDiscount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php
                                            if ($conditionGST || $gstCode == "") {
                                                $itemGstAmt = $item['totalTax'] / 2;
                                                $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= decimalValuePreview($itemGstPer) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><?= decimalValuePreview($itemGstPer) ?>%</p>
                                                </td>
                                                <td class="text-right px-2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="px-2">
                                                    <p class=" font-bold"><?= decimalQuantityPreview($item['tax']) ?>%</p>
                                                </td>
                                                <td class="px-2" colspan="2">
                                                    <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($item['totalTax']) ?></p>
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
                                        <td colspan="11" class="font-bold text-right px-2">
                                            <p>Sub Total (<?= $companyCurrencyName ?>)</p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted">Sub Total (<?= $customerCurrencyName ?>)</small>
                                            <?php } ?>
                                            <?php if ($totalDiscountAmt > 0) { ?>
                                                <p>Discount (<?= $companyCurrencyName ?>)</p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted">Discount (<?= $customerCurrencyName ?>)</small>
                                                <?php } ?>
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
                                            <?php if ($conditionGST || $gstCode == "") { ?>
                                                <p><span class="pr-1"></span><?= decimalValuePreview($totalTaxAmt / 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview(($totalTaxAmt / 2) * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                                <p><span class="pr-1"></span><?= decimalValuePreview($totalTaxAmt / 2) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview(($totalTaxAmt / 2) * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <p><span class="pr-1"></span><?= decimalValuePreview($totalTaxAmt) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= decimalValuePreview($totalTaxAmt * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            <?php } ?>
                                            <p><?= decimalValuePreview($quotationDetails['totalAmount']) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($quotationDetails['totalAmount'] * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Amount Chargeable (in words)</p>
                                            <p class="font-bold"><?= $companyCurrencyName . " " . number_to_words_indian_rupees($quotationDetails['totalAmount']); ?> ONLY</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(number_format($quotationDetails['totalAmount'] * $currencyConversionRate, 2)) ?></small>
                                            <?php } ?> -->
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Remarks: <?= $quotationDetails['remarks'] ?></p>
                                            <p>Declaration: <?= $quotationDetails['declaration_note'] ?></p>
                                            <!-- <p><?= $companyData['company_footer'] ?></p> -->
                                            <p>Created By: <strong><?= getCreatedByUser($quotationDetails['created_by']); ?></strong></p>
                                            <?php if ($attachmentObj['status'] == 'success' && $printFlag==TRUE) { ?>
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
                                </tfoot>
                            </table>

                        </div>
                    </div>
                </div>
            <?php
            } else if ($templateId == 1) {
            ?>
                <div class="card classic-view bg-transparent">
                    <p>Template 2</p>
                </div>
<?php
            }
        }
    }
}
