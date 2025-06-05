<?php
class TemplateSalesOrderControllerTaxComponents
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

    public function printSalesOrderProformaTaxComponents($proformaId = 0, $templateId = 0, $redirectUrl = "")
    {
        $countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
        $components = getLebels($countrycode)['data'];
        $components = json_decode($components, true);
        // console($components['fields']['taxNumber']);
        $branchSoObj = new BranchSo();
        $taxtype_sql = queryGet("SELECT * FROM `erp_tax_rulebook` WHERE `country_id` =" . $countrycode . "");
        $taxName = $taxtype_sql['data'];
        $oneSoList = queryGet("SELECT * FROM `erp_proforma_invoices` WHERE proforma_invoice_id =$proformaId")['data'];

        $company_id = $oneSoList['company_id'];
        $branch_id = $oneSoList['branch_id'];
        $location_id = $oneSoList['location_id'];

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$this->company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$this->company_id' AND `fldAdminBranchId`='$this->branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin,state as location_state FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$this->company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id' AND othersLocation_id='$this->location_id'")['data'];
        $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);
        $customerDetails = $branchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
        // console($companyData);
        $company_bank_details = unserialize($oneSoList['company_bank_details']);



?>

        <div class="printable-view">
            <h3 class="h3-title text-center font-bold text-sm">Proforma Invoice</h3>

            <table class="classic-view table-bordered">
                <tbody>
                    <tr>
                        <td colspan="5" class="border-right">
                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                            <p class="font-bold"><?= $companyData['company_name'] ?></p>
                            <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?></p>
                            <p><?= $companyData['location'] ?>, <?= $companyData['location_street_name'] ?>, <?= $companyData['location_pin_code'] ?></p>
                            <p><?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?></p>
                            <p><?= $companyData['location_state'] ?></p>
                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $companyData['branch_gstin'] ? $companyData['branch_gstin'] : ' --' ?></p><?php } ?>
                            <?php if ($components['fields']['taxNumber'] != null) { ?>
                                <p><?= $components['fields']['taxNumber'] ?>: <?= $companyData['company_pan'] ? $companyData['company_pan'] : '--' ?>
                                </p><?php } ?>
                        </td>
                        <td colspan="3">
                            <p>Document No</p>
                            <p class="font-bold"><?= $oneSoList['invoice_no'] ?></p>
                        </td>
                        <td colspan="3">
                            <p>Dated</p>
                            <p class="font-bold"><?= formatDateWeb($oneSoList['invoice_date']) ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" class="border-right">
                            <p>Buyer (Bill to)</p>
                            <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                            <p><?= $oneSoList['customer_billing_address'] ?></p>
                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $customerDetails['customer_gstin'] ? $customerDetails['customer_gstin'] : "--" ?></p><?php } ?>
                            <!-- <p>State Name : Maharashtra, Code : 27</p> -->
                        </td>
                        <td colspan="5" class="border-right">
                            <p>Consignee (Ship to)</p>
                            <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                            <p><?= $oneSoList['customer_shipping_address'] ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>Sl No.</th>
                        <th>Particulars</th>
                        <th>HSN/SAC </th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>UOM</th>
                        <th>Discount</th>
                        <th><?= $taxName['tax_name'] ?></th>
                        <th>Total Amount</th>
                    </tr>
                    <?php
                    $itemDetails = $branchSoObj->fetchProformaInvoiceItems($oneSoList['proforma_invoice_id'])['data'];
                    // console($itemDetails);
                    $i = 0;
                    // $conversion_rate
                    foreach ($itemDetails as $onePgiItem) {
                        // $unitPrice = $onePgiItem['unitPrice'] * $conversion_rate;
                        // $totalDiscount = $onePgiItem['totalDiscount'] * $conversion_rate;
                        $uomName = getUomDetail($onePgiItem['uom'])['data']['uomName'];

                    ?>


                        <tr>
                            <td class="text-center"><?= ++$i ?></td>
                            <td class="text-center">
                                <p class="font-bold"><?= $onePgiItem['itemName'] ?></p>
                                <p class="text-italic"><?= $onePgiItem['itemCode'] ?></p>
                                <p class="text-italic"><?= $onePgiItem['itemRemarks'] ?></p>
                            </td>
                            <td class="text-center">
                                <p><?= $onePgiItem['hsnCode'] ?></p>
                            </td>
                            <td class="text-center">
                                <p><?= decimalQuantityPreview($onePgiItem['qty']) ?></p>
                            </td>
                            <td class="text-right">
                                <p><?= decimalValuePreview($onePgiItem['unitPrice']) ?></p>
                            </td>
                            <td class="text-center">
                                <p><?= $uomName ?></p>
                            </td>
                            <td class="text-right">
                                <p><?= decimalValuePreview($onePgiItem['totalDiscountAmt'] + $onePgiItem['cashDiscountAmount']) ?></p>
                                <!-- <p class="font-bold text-italic">(<?= $onePgiItem['totalDiscount'] ?>%)</p> -->
                            </td>
                            <td class="text-right">
                                <p><?= decimalValuePreview($onePgiItem['totalTax']) ?></p>
                                <p class="font-bold text-italic">(<?= decimalQuantityPreview($onePgiItem['tax']) ?>%)</p>
                            </td>
                            <td class="text-right"><?= decimalValuePreview($onePgiItem['totalPrice']) ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="10" class="text-right font-bold">
                            <?= decimalValuePreview($oneSoList['all_total_amt']) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <p>Amount Chargeable (in words)</p>
                            <p class="font-bold"><?= number_to_words_indian_rupees($oneSoList['all_total_amt']); ?> ONLY</p>
                        </td>

                        <td colspan="5" class="px-2">
                            <p class="text-left">E. & O.E</p>
                            <p>Company's Bank Details</p>
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
                        <td colspan="5">
                            <p>Remarks:</p>
                            <p>Created By: <b><?= getCreatedByUser($oneSoList['created_by']) ?></b></p>
                        </td>
                        <td colspan="5" class="text-right border">
                            <p class="text-center font-bold"> for <?= $companyData['company_name'] ?></p>
                            <p class="text-center sign-img">
                                <img width="160" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="">

                            </p>
                        </td>

                    </tr>
                </tbody>
            </table>
        </div>

        <?php
    }

    public function printSalesOrder($soId = 0, $templateId = 0, $redirectUrl = "")
    {


        $countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
        $components = getLebels($countrycode)['data'];
        $stateCodeReq = getLebels($countrycode)['data']['state_code'];
        $components = json_decode($components, true);
        $branchSoObj = new BranchSo();
        $salesOrderDetailsObj = $branchSoObj->fetchSoDetailsById($soId);
        if (count($salesOrderDetailsObj['data']) <= 0) {
            echo '<p class="text-warning text-center mt-5">Invoice Not found!</p>';
            // if ($redirectUrl != "") {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!", $redirectUrl);
            // } else {
            //     swalAlert("warning", 'Opps!', "Invoice Not found!");
            // }
        } else {
            $soDetails = $salesOrderDetailsObj['data'][0];
            $company_id = $soDetails['company_id'];
            $branch_id = $soDetails['branch_id'];
            $location_id = $soDetails['location_id'];
            $salesOrderItemDetails = $branchSoObj->fetchBranchSoItems($soId)['data'];
            // console($soDetails);

            $customerId = $soDetails['customer_id'];
            $customerCurrencyName = $soDetails['currency_name'] ?? "";
            $taxComponents = json_decode($soDetails['taxComponents'], true);

            // fetch customer details
            $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'");
            $customerStateQry = queryGet("SELECT * FROM `erp_customer_address` where `customer_id`='$customerId'");
            $customerStatedata = $customerStateQry['data'];
            $customerData = $customerDetailsObj['data'];
            // console($customerData);

            // fetch company details
            $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$this->company_id'")['data'];
            $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$this->company_id' AND `fldAdminBranchId`='$this->branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
            $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin,state as location_state  FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id'")['data'];

            $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$this->company_id' AND flag='1'")['data'];
            $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id' AND othersLocation_id='$this->location_id'")['data'];
            $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

            // console($locationDetailsObj);
            $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
            $companyCurrencyName = $currencyDetails['currency_name'];
            $currencyConversionRate = $soDetails['conversion_rate'] != "" ? $soDetails['conversion_rate'] : 1;

            // fetch sales order attachments
            $attachmentObj = $branchSoObj->getSalesOrderAttachments($soId);

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
                            <h3 class="h3-title text-center font-bold text-sm mb-4">Sales Order</h3>
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
                                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $companyData['branch_gstin'] ? $companyData['branch_gstin'] : "--" ?></p><?php } ?>
                                            <?php if ($components['fields']['taxNumber'] != null) { ?>
                                                <p><?= $components['fields']['taxNumber'] ?>: <?= $companyData['company_pan'] ? $companyData['company_pan'] : "--" ?></p><?php } ?>

                                            <?php if ($countrycode == '103') {    ?>
                                                <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                            <?php } else {    ?>
                                                <p>State Name : <?= $companyData['location_state'] ?></p>
                                            <?php } ?>
                                            <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                                        </td>
                                        <td colspan="4" class="px-2">
                                            <p>Sales Order No.</p>
                                            <p class="font-bold"><?= $soDetails['so_number'] ?></p>
                                            <br>
                                            <p>Customer Order No.</p>
                                            <p class="font-bold"><?= $soDetails['customer_po_no'] ?></p>
                                        </td>
                                        <td colspan="5" class="px-2">
                                            <p>Dated</p>
                                            <p class="font-bold"><?php $invDate = date_create($soDetails['so_date']);
                                                                    echo date_format($invDate, "F d,Y"); ?></p>
                                            <br>
                                            <p>Validity Period</p>
                                            <p class="font-bold"><?= formatDateORDateTime($soDetails['validityperiod']) ?></p>


                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="px-2">
                                            <p>Mode/Terms of Payment</p>
                                            <?php if ($soDetails['credit_period'] != "") { ?>
                                                <p><?= $soDetails['credit_period'] ?></p>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="px-2">
                                            <p>Buyer (Bill to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $soDetails['billingAddress'] ?></p>
                                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $customerData['customer_gstin'] ? $customerData['customer_gstin'] : "--" ?></p><?php } ?>
                                            <?php if ($countrycode == '103') { ?>
                                                <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                            <?php } else { ?>
                                                <p>State Name : <?= $customerStatedata['customer_address_state'] ?></p>
                                            <?php } ?>
                                        </td>
                                        <td colspan="5" class="px-2">
                                            <p>Consignee (Ship to)</p>
                                            <p class="font-bold"> <?= $customerData['customer_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $soDetails['shippingAddress'] ?></p>
                                            <?php if ($countrycode == '103') { ?>
                                                <p>State Name : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?>, Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                            <?php } else { ?>
                                                <p>State Name : <?= $customerStatedata['customer_address_state'] ?></p>
                                            <?php } ?>
                                            <?php if ($components['place_of_supply'] == 1) { ?>
                                                <p>Place of Supply : <?= fetchStateNameByGstin($customerData['customer_gstin']) ?></p>
                                            <?php } ?>
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
                                        if ($countrycode == 103) {
                                            if ($conditionGST || $customerGstin == "") {
                                        ?>
                                                <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                                <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                            <?php } else { ?>
                                                <th class="text-center text-bold invoiceTableHeadStyle" colspan="3">IGST</th>
                                            <?php }
                                        } else {
                                            foreach ($taxComponents as $tax) {
                                            ?>
                                                <th class="text-center text-bold invoiceTableHeadStyle" colspan="3"><?= $tax['gstType'] ?></th>
                                        <?php }
                                        } ?>
                                        <th rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                    </tr>
                                    <tr>

                                        <?php if ($countrycode == 103) {
                                            if ($conditionGST || $customerGstin == "") { ?>
                                                <th class="invoiceTableHeadStyle">Rate</th>
                                                <th class="invoiceTableHeadStyle">Amount</th>
                                                <th class="invoiceTableHeadStyle">Rate</th>
                                                <th class="invoiceTableHeadStyle">Amount</th>
                                            <?php } else { ?>
                                                <th class="invoiceTableHeadStyle">Rate</th>
                                                <th class="invoiceTableHeadStyle" colspan="2">Amount</th>
                                            <?php }
                                        } else {
                                            foreach ($taxComponents as $tax) { ?>
                                                <th class="invoiceTableHeadStyle">Rate</th>
                                                <th class="invoiceTableHeadStyle" colspan="2">Amount</th>
                                        <?php }
                                        } ?>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    $totalTaxAmt = 0;
                                    $subTotalAmt = 0;
                                    $allSubTotalAmt = 0;
                                    $totalDiscountAmt = 0;
                                    $totalAmt = 0;
                                    foreach ($salesOrderItemDetails as $key => $item) {
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
                                                <?php if ($soDetails['type'] == 'project') { ?>
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
                                                <p><!--<span class="text-small">(<?= $item['totalDiscount'] ?>%)</span> --> <?= decimalValuePreview($item['itemTotalDiscount'] + $item['cashDiscountAmount']) ?></p>
                                                <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                    <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['itemTotalDiscount'] * $currencyConversionRate) ?></small>
                                                <?php } ?>
                                            </td>
                                            <?php
                                            if ($countrycode == 103) {
                                                if ($conditionGST || $customerGstin == "") {
                                                    $itemGstAmt = $item['totalTax'] / 2;
                                                    $itemGstPer = $item['tax'] / 2;
                                            ?>
                                                    <td class="text-right px-2">
                                                        <p class=" font-bold"><?= decimalQuantityPreview($itemGstPer) ?>%</p>
                                                    </td>
                                                    <td class="text-right px-2">
                                                        <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                        <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                            <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-right px-2">
                                                        <p class=" font-bold"><?= decimalQuantityPreview($itemGstPer) ?>%</p>
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
                                                    <td class="px-2 text-right" colspan="2">
                                                        <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($item['totalTax']) ?></p>
                                                        <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                            <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($item['totalTax'] * $currencyConversionRate) ?></small>
                                                        <?php } ?>
                                                    </td>
                                                <?php }
                                            } else {
                                                foreach ($taxComponents as $tax) {
                                                    $itemGstAmt = $item['totalTax'] / (100 / $tax['taxPercentage']);
                                                    $itemGstPer = $item['tax'] / (100 / $tax['taxPercentage']); ?>

                                                    <td class="px-2">
                                                        <p class=" font-bold"><?= decimalQuantityPreview($itemGstPer) ?>%</p>
                                                    </td>
                                                    <td class="px-2 text-right" colspan="2">
                                                        <p class=" font-bold"><span class="rupee-symbol"></span><?= decimalValuePreview($itemGstAmt) ?></p>
                                                        <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                            <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . decimalValuePreview($itemGstAmt * $currencyConversionRate) ?></small>
                                                        <?php } ?>
                                                    </td>
                                            <?php }
                                            } ?>
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
                                            <?php if ($countrycode == 103) {
                                                if ($conditionGST || $customerGstin == "") { ?>
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
                                                <?php }
                                            } else {
                                                foreach ($taxComponents as $tax) { ?>
                                                    <p>Total <?= $tax['gstType'] ?> (<?= $companyCurrencyName ?>)</p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted">Total <?= $tax['gstType'] ?> (<?= $customerCurrencyName ?>)</small>
                                                    <?php } ?>
                                            <?php  }
                                            } ?>

                                            <p>Total Discount (<?= $companyCurrencyName ?>)</p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted">Grand Total (<?= $customerCurrencyName ?>)</small>
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
                                            <?php if ($countrycode == 103) {
                                                if ($conditionGST || $customerGstin == "") { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($totalTaxAmt / 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview(($totalTaxAmt / 2) * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($totalTaxAmt / 2, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview(($totalTaxAmt / 2) * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($totalTaxAmt, 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($totalTaxAmt * $currencyConversionRate) ?></small>
                                                    <?php } ?>
                                                <?php }
                                            } else {
                                                foreach ($taxComponents as $tax) { ?>
                                                    <p><span class="pr-1"></span><?= decimalValuePreview($totalTaxAmt / (100 / $tax['taxPercentage']), 2) ?></p>
                                                    <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                        <small class="text-small text-muted"><?= decimalValuePreview($totalTaxAmt / (100 / $tax['taxPercentage']) * $currencyConversionRate) ?></small>
                                            <?php }
                                                }
                                            } ?>
                                            <p><?= decimalValuePreview($soDetails['totalDiscount'] + $soDetails['totalCashDiscount']) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview(($soDetails['totalDiscount'] + $soDetails['totalCashDiscount']) * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                            <p><?= decimalValuePreview($soDetails['totalAmount']) ?></p>
                                            <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= decimalValuePreview($soDetails['totalAmount'] * $currencyConversionRate) ?></small>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Amount Chargeable (in words)</p>
                                            <p class="font-bold"><?= $companyCurrencyName . " " . number_to_words_indian_rupees($soDetails['totalAmount']); ?> ONLY</p>
                                            <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(decimalValuePreview($soDetails['totalAmount'] * $currencyConversionRate)) ?></small>
                                            <?php } ?> -->
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="px-2">
                                            <p>Remarks: <?= $soDetails['remarks'] ?></p>
                                            <!-- <p>Declaration: <?= $soDetails['declaration_note'] ?></p> -->
                                            <!-- <p><?= $companyData['company_footer'] ?></p> -->
                                            <p>Created By: <strong><?= getCreatedByUser($soDetails['created_by']); ?></strong></p>
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

    public function printDelivery($delv_id = 0)
    {
        $countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
        $components = getLebels($countrycode)['data'];
        $components = json_decode($components, true);
        $branchSoObj = new BranchSo();

        $oneSoList = queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_DELIVERY . "` WHERE 1 AND so_delivery_id=" . $delv_id . "  AND company_id='" . $this->company_id . "' AND branch_id='" . $this->branch_id . "' AND location_id='" . $this->location_id . "' ORDER BY so_delivery_id")['data'];
        $customerId = $oneSoList['customer_id'];
        $customerCurrencyName = $oneSoList['currency_name'] ?? "";
        // console($oneSoList);
        // fetch customer details
        $customerDetailsObj = queryGet("SELECT parentGlId,customer_pan,customer_gstin,trade_name as customer_name,customer_code,constitution_of_business,customer_opening_balance,customer_currency,customer_website,customer_credit_period,customer_picture,customer_authorised_person_name,customer_authorised_person_email,customer_authorised_alt_email,customer_authorised_person_phone,customer_authorised_alt_phone,customer_authorised_person_designation,customer_profile,customer_status FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`='$customerId'");
        $customerDetails = $customerDetailsObj['data'];

        // console($customerDetails);
        $company_id = $oneSoList['company_id'];
        $branch_id = $oneSoList['branch_id'];
        $location_id = $oneSoList['location_id'];

        // fetch company details
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$this->company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$this->company_id' AND `fldAdminBranchId`='$this->branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$this->company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id' AND othersLocation_id='$this->location_id'")['data'];
        $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);


        $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
        $companyCurrencyName = $currencyDetails['currency_name'];

        // fetch sales order attachments
        // $attachmentObj = $branchSoObj->getSalesOrderAttachments($soId);


?>

        <div class="printable-view">
            <h3 class="h3-title text-center font-bold text-sm mb-4">Sales Order Delivery</h3>
            <table class="classic-view table-bordered">
                <tbody>
                    <tr>
                        <td colspan="3">
                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                            <p class="font-bold"><?= $companyData['company_name'] ?></p>
                            <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?></p>
                            <p><?= $companyData['location'] ?>, <?= $companyData['location_street_name'] ?>, <?= $companyData['location_pin_code'] ?></p>
                            <p><?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?></p>
                            <p><?= $companyData['location_state'] ?></p>
                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $companyData['branch_gstin'] ? $companyData['branch_gstin'] : ' --' ?></p><?php } ?>
                            <?php if ($components['fields']['taxNumber'] != null) { ?>
                                <p><?= $components['fields']['taxNumber'] ?>: <?= $companyData['company_pan'] ? $companyData['company_pan'] : '--' ?>
                                </p><?php } ?>
                            <!-- <p>State Name : West Bengal, Code : 19</p> -->
                            <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                        </td>
                        <td colspan="2" class="border-right-none">
                            <p>Sales Order Delivery Number</p>
                            <p class="font-bold"><?= $oneSoList['delivery_no'] ?></p>
                        </td>
                        <td colspan="3" class="border-left-none">
                            <p>Dated</p>
                            <p class="font-bold"><?= formatDateORDateTime($oneSoList['delivery_date']) ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <p>Buyer (Bill to)</p>
                            <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                            <p><?= $oneSoList['customer_billing_address'] ?></p>
                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $customerDetails['customer_gstin'] ? $customerDetails['customer_gstin'] : "--" ?></p><?php } ?>
                            <!-- <p>State Name : Maharashtra, Code : 27</p> -->
                        </td>
                        <td colspan="5">
                            <p>Consignee (Ship to)</p>
                            <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                            <p><?= $oneSoList['customer_shipping_address'] ?></p>
                            <!-- <p>State Name : Maharashtra, Code : 27</p>
                                                        <p>Place of Supply : Maharashtra</p> -->
                        </td>
                    </tr>

                    <tr>
                        <th>Sl No.</th>
                        <th>Particulars</th>
                        <th>Quantity</th>
                        <th>Batch</th>
                        <th>Storage Type</th>
                        <th>Warehouse</th>
                    </tr>
                    <?php
                    // $itemDetails = $branchSoObj->fetchBranchSoDeliveryItemsPgi($oneSoList['so_delivery_pgi_id'])['data'];
                    // console($itemDetails);

                    $batchSql = "SELECT
                    LOG.refNumber AS del_code,
                    LOG.logRef AS batch,
                    LOG.storageLocationId,
                    strLoc.storage_location_name,
                    warehouse.warehouse_name,
                    LOG.storageType,
                    items.itemCode,
                    items.itemName,
                    LOG.itemQty AS qty
                FROM
                    erp_inventory_stocks_log AS LOG
                LEFT JOIN erp_branch_sales_order_delivery AS delivery
                ON
                    LOG.refNumber = delivery.delivery_no
                LEFT JOIN erp_branch_sales_order_delivery_items AS items
                ON
                    delivery.so_delivery_id = items.so_delivery_id
                    LEFT JOIN `erp_storage_location` as strLoc
                    ON LOG.storageLocationId=strLoc.storage_location_id
                    LEFT JOIN erp_storage_warehouse AS warehouse
            ON 
                warehouse.warehouse_id = strLoc.warehouse_id
                    WHERE
                            LOG.companyId = " . $this->company_id . " AND LOG.branchId = " . $this->branch_id . " AND LOG.locationId = " . $this->location_id . "  AND items.inventory_item_id =LOG.itemId AND LOG.refNumber='" . $oneSoList['delivery_no'] . "' AND LOG.itemQty>0
                        GROUP BY
                            LOG.refNumber,
                            LOG.logRef,
                            LOG.storageLocationId,
                            LOG.storageType,
                            items.itemCode,
                            items.itemName,
                            strLoc.storage_location_name,
                            warehouse.warehouse_name,
                            LOG.itemQty";
                    $batchQuery = queryGet($batchSql, true);
                    // console($batchQuery);
                    $i=0;
                    foreach ($batchQuery['data'] as $onePgiItem) {
                        // $unitPrice = $onePgiItem['unitPrice'] * $conversion_rate;
                        // $totalDiscount = $onePgiItem['totalDiscount'] * $conversion_rate;
                    ?>


                        <tr>
                            <td class="text-center"><?= ++$i ?></td>
                            <td class="text-center">
                                <p class="font-bold"><?= $onePgiItem['itemName'] ?></p>
                                <p class="text-italic"><?= $onePgiItem['itemCode'] ?></p>
                            </td>
                            <td class="text-center">
                                <p><?= decimalQuantityPreview($onePgiItem['qty']) ?></p>
                            </td>
                            <td class="text-center">
                                <p><?= $onePgiItem['batch'] ?></p>
                            </td>
                            <td class="text-right">
                                <p><?= $onePgiItem['storage_location_name'] ?></p>
                            </td>
                            <td class="text-center">
                                <p><?= $onePgiItem['warehouse_name'] ?></p>
                            </td>

                        </tr>
                    <?php
                    } ?>



                    <!-- <td colspan="5">
                            <p>Amount Chargeable (in words)</p>
                            <p class="font-bold"><?= number_to_words_indian_rupees($oneSoList['totalAmount']); ?> ONLY</p>
                        </td>

                        <td colspan="5" class="text-right">E. & O.E</td> -->

                    </tr>

                    <tr>
                        <td colspan="5">
                            <p>Remarks:</p>
                            <p>Created By: <b><?= getCreatedByUser($oneSoList['created_by']) ?></b></p>
                        </td>
                        <td colspan="5" class="text-right">
                            <p class="text-center font-bold"> for <?= $companyData['company_name'] ?></p>
                            <p class="text-center sign-img">
                                <img width="60" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="signature">
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
<?php
    }
}
