<?php


include_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

require __DIR__ . '/../../../../../vendor/autoload.php';


use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\PngWriter;

class TemplateCreditNoteTaxController
{
    // private $company_id, $branch_id, $location_id, $created_by, $updated_by;
    // function __construct()
    // {
    //     global $company_id;
    //     global $branch_id;
    //     global $location_id;
    //     global $created_by;
    //     global $updated_by;
    //     $this->company_id = $company_id;
    //     $this->branch_id = $branch_id;
    //     $this->location_id = $location_id;
    //     $this->created_by = $created_by;
    //     $this->updated_by = $updated_by;
    // }

    public function printCreditNoteTax($cr_note_id = 0)
    {
        // $countrycode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
        // $components = getLebels($countrycode)['data'];
        // $components = json_decode($components, true);
        $branchSoObj = new BranchSo();
        $oneList = queryGet("SELECT * FROM `erp_credit_note` WHERE cr_note_id =$cr_note_id")['data'];
        $company_id = $oneList['company_id'];
        $branch_id = $oneList['branch_id'];
        $location_id = $oneList['location_id'];
        $created_by = $oneList['created_by'];
        $updated_by = $oneList['updated_by'];
        $countryCode_sql = queryGet("SELECT * FROM `erp_companies` WHERE `company_id` = " . $company_id . "")['data'];
        $countrycode = $countryCode_sql['company_country'];
        $taxName = getTaxName($countrycode)['data'];
        $components = getLebels($countrycode)['data'];
        $components = json_decode($components, true);

        $country_fields = json_decode(getLebels($countrycode)['data']);


        $company_id = $oneList['company_id'];
        $branch_id = $oneList['branch_id'];
        $location_id = $oneList['location_id'];

        // console($oneList);

        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$company_id' AND `fldAdminBranchId`='$branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin,state as location_state FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state_code FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$branch_id' AND `company_id`='$company_id' AND othersLocation_id='$location_id'")['data'];
        $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

        $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
        $companyCurrencyName = $currencyDetails['currency_name'];

        $itemDetailsObj = queryGet("SELECT * FROM `credit_note_item` AS cr_item, `erp_inventory_items` AS item  WHERE item.itemId=cr_item.item_id AND `credit_note_id` = '" . $cr_note_id . "'", true);

        $contactDetails = queryGet("SELECT `fldAdminEmail`, `fldAdminPhone` FROM `tbl_company_admin_details` WHERE `fldAdminKey`='" . $company_id . "'")['data'];

        $e_inv_detail = queryGet("SELECT `irn`,`signed_qr_code`,`ack_no`,`ack_date` FROM `erp_e_invoices` WHERE `invoice_id` = $cr_note_id AND `document_type` = 'CRN' ");
        $eInvDetailData = $e_inv_detail['data'];

        if ($e_inv_detail['numRows'] > 0) {

            $irn = $e_inv_detail['data']['irn'];
            $qr = $e_inv_detail['data']['signed_qr_code'];

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
        }




        $taxComponents = json_decode($oneList['taxComponents'], true);
        $itemDetails = $itemDetailsObj['data'];
        $state_name = '';
        // console($itemDetails);
        $bill_id = $oneList['creditNoteReference'];
        $creditors_type = $oneList['creditors_type'];
        if ($creditors_type == 'customer') {
            $customerDetailsObj = queryGet("SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`=" . $oneList['party_id'] . "");
            $customerData = $customerDetailsObj['data'];
            // console($customerDetailsObj);


            $iv = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=$bill_id");
            // console($iv);
            $ref = $iv['data']['invoice_no'];
            $iv_date = explode(" ", $iv['data']['created_at'], 1);

            $source_address_sql = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $oneList['billing_address'] . "' ")['data'];

            $source_address = $source_address_sql['customer_address_building_no'] . ',' . $source_address_sql['customer_address_flat_no'] . ',' . $source_address_sql['customer_address_street_name'] . ',' . $source_address_sql['customer_address_pin_code'] . ',' . $source_address_sql['customer_address_location'] . ',' . $source_address_sql['customer_address_city'] . ',' . $source_address_sql['customer_address_district'] . ',' . $source_address_sql['customer_address_country'] . ',' . $source_address_sql['customer_address_state'];
            // console($iv_date);
            $state_name = $source_address_sql['customer_address_state'];
            $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $oneList['shipping_address'] . "' ")['data'];

            $destination_address = $destination_address_sql['customer_address_building_no'] . ',' . $destination_address_sql['customer_address_flat_no'] . ',' . $destination_address_sql['customer_address_street_name'] . ',' . $destination_address_sql['customer_address_pin_code'] . ',' . $destination_address_sql['customer_address_location'] . ',' . $destination_address_sql['customer_address_city'] . ',' . $destination_address_sql['customer_address_district'] . ',' . $destination_address_sql['customer_address_country'] . ',' . $destination_address_sql['customer_address_state'];
        } else {
            $customerDetailsObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id`=" . $oneList['party_id'] . "");
            $customerData = $customerDetailsObj['data'];

            // echo '----------------------------------------------------------------';
            // console($customerDetailsObj);

            $iv = queryGet("SELECT * FROM `erp_grninvoice` WHERE `grnIvId`=$bill_id");
            // console($iv);
            $ref = $iv['data']['invoice_ number'];
            $iv_date = explode(" ", $iv['data']['created_at'], 1);

            // console($iv_date);
            $source_address_sql = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_business_id`= '" . $oneList['billing_address'] . "' ")['data'];
            // console($source_address_sql);

            $source_address = $source_address_sql['vendor_business_building_no'] . ',' . $source_address_sql['vendor_business_flat_no'] . ',' . $source_address_sql['vendor_business_street_name'] . ',' . $source_address_sql['vendor_business_pin_code'] . ',' . $source_address_sql['vendor_business_location'] . ',' . $source_address_sql['vendor_business_city'] . ',' . $source_address_sql['vendor_business_district'] . ',' . $source_address_sql['vendor_business_country'] . ',' . $source_address_sql['vendor_business_state'];
            $state_name = $source_address_sql['vendor_business_state'];
            $destination_address_sql =  queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_business_id`= '" . $oneList['shipping_address'] . "' ")['data'];

            $destination_address = $destination_address_sql['vendor_business_building_no'] . ',' . $destination_address_sql['vendor_business_flat_no'] . ',' . $destination_address_sql['vendor_business_street_name'] . ',' . $destination_address_sql['vendor_business_pin_code'] . ',' . $destination_address_sql['vendor_business_location'] . ',' . $destination_address_sql['vendor_business_city'] . ',' . $destination_address_sql['vendor_business_district'] . ',' . $destination_address_sql['vendor_business_country'] . ',' . $destination_address_sql['vendor_business_state'];
        }

        $branchGstin = substr($companyData['branch_gstin'], 0, 2);
        if ($creditors_type == 'customer') {
            $customerGstin = substr($customerData['customer_gstin'], 0, 2);
        } else {
            $customerGstin = substr($customerData['vendor_gstin'], 0, 2);
        }
        $conditionGST = $branchGstin == $customerGstin;

        // echo $branchGstin."-".$customerGstin."-".$conditionGST;

?>

        <div class="card classic-view bg-transparent">
            <div class="card-body classic-view-so-table" style="overflow: auto;">
                <div class="printable-view">
                    <h3 class="h3-title text-center font-bold text-sm mb-4">Credit Note</h3>


                    <table class="classic-view table-bordered tableBorder">
                        <tbody>
                            <tr>
                                <td colspan="3" class="px-2 border-right-0">
                                    <p class="text-left header-logo">
                                        <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                    </p>
                                </td>
                                <td colspan="6" class="border-right-0 border-left-0">
                                    <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                    <p><?= $companyData['location_building_no'] ?></p>
                                    <p>Flat No.<?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                    <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                    <?php if ($countrycode == 103) { ?>
                                        <p>State Name: <?= fetchStateNameByGstin($companyData['branch_gstin']) ?><?php if ($country_fields->state_code) { ?> , Code: <?= substr($companyData['branch_gstin'], 0, 2); ?></p> <?php } ?>
                                <?php } else { ?>
                                    <p>State Name: <?= $companyData['location_state'] ?><?php if ($country_fields->state_code) { ?> , Code: <?= $companyData['location_state_code']; ?></p> <?php }
                                                                                                                                                                                    } ?>
                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $companyData['branch_gstin'] ? $companyData['branch_gstin'] : ' --' ?></p><?php } ?>
                            <?php if ($components['fields']['taxNumber'] != null) { ?>
                                <p><?= $components['fields']['taxNumber'] ?>: <?= $companyData['company_pan'] ? $companyData['company_pan'] : '--' ?>
                                </p><?php } ?>
                            <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                            <p>Phone No : <?= $companyData['companyPhone'] ?></p>
                            <?php if ($e_inv_detail['numRows'] > 0) { ?>
                                <p>IRN No : <?= $irn ?></p>
                                <p>Ack. No: <?= $eInvDetailData['ack_no'] ?></p>
                                <p>Ack. Date: <?= formatDateORDateTime($eInvDetailData['ack_date']) ?></p>
                            <?php } ?>
                                </td>
                                <td colspan="3" class="border-left-0 vertical-align-bottom">
                                    <?php if ($e_inv_detail['numRows'] > 0) {
                                    ?>
                                        <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code">

                                    <?php
                                    }
                                    // else{
                                    //     echo 'no QR Code found!';
                                    // }
                                    ?>

                                    <p><b>Ref Inv no:</b></p>
                                    <p><?= $ref ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td rowspan="2" colspan="9">
                                    <div class="d-flex">
                                        <div class="cust-details">
                                            <p class="font-bold"> To, <?= $customerData['trade_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $source_address ?></p>
                                            <?php if ($creditors_type == 'customer') { ?>
                                                <p class="font-bold" style="white-space: pre-wrap;"> Contact Person :<?= $customerData['customer_authorised_person_name'] ?></p>
                                                <p class="font-bold" style="white-space: pre-wrap;"> Mobile No :<?= $customerData['customer_authorised_person_phone'] ?></p>

                                            <?php } else { ?>
                                                <p class="font-bold" style="white-space: pre-wrap;"> Contact Person :<?= $customerData['vendor_authorised_person_name'] ?></p>
                                                <p class="font-bold" style="white-space: pre-wrap;"> Mobile No :<?= $customerData['vendor_authorised_person_phone'] ?></p> <?php } ?>
                                        </div>
                                        <div class="add-details text-left">
                                            <?php if ($components['fields']['businessTaxID'] != null) {
                                                if ($creditors_type == 'customer') { ?>
                                                    <p><?= $components['fields']['businessTaxID'] ?>: <?= $customerData['customer_gstin'] ? $customerData['customer_gstin'] : ' --' ?></p><?php } else { ?>
                                                    <p><?= $components['fields']['businessTaxID'] ?>: <?= $customerData['vendor_gstin'] ? $customerData['vendor_gstin'] : ' --' ?></p>
                                            <?php }
                                                                                                                                                                                    } ?>
                                            <?php if ($components['fields']['taxNumber'] != null) {
                                                if ($creditors_type == 'customer') { ?>
                                                    <p><?= $components['fields']['taxNumber'] ?>: <?= $customerData['customer_pan'] ? $customerData['customer_pan'] : '--' ?>
                                                    </p><?php } else { ?>
                                                    <p><?= $components['fields']['taxNumber'] ?>: <?= $customerData['vendor_pan'] ? $customerData['vendor_pan'] : '--' ?>
                                                    </p>
                                                <?php }
                                                }
                                                if ($creditors_type == 'customer') { ?>
                                                <p>State Name : <?= $state_name; ?></p>
                                                <?php if ($country_fields->state_code) { ?>
                                                    <p>Code : <?= substr($customerData['customer_gstin'], 0, 2); ?></p>
                                                <?php } ?>
                                            <?php
                                                } else { ?>
                                                <p>State Name : <?= $state_name; ?></p>
                                                <?php if ($country_fields->state_code) { ?>
                                                    <p>Code : <?= substr($customerData['vendor_gstin'], 0, 2); ?></p>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                                <td colspan="3">
                                    <div class="code-details border-bottom">
                                        <p>Credit Note No: <b><?= $oneList['credit_note_no'] ?></b></p>
                                        <p>Dated : <b><?php $invDate = date_create($oneList['postingDate']);
                                                        echo date_format($invDate, "F d,Y"); ?></b></p>
                                    </div>
                                    <div class="pay-details">
                                        <p>Mode/Terms of Payment</p>
                                        <?php if ($oneList['credit_period'] != "") { ?>
                                            <p><?= $oneList['credit_period'] ?></p>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th rowspan="2" class="invoiceTableHeadStyle">Sl No.</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Item Name</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">HSN/SAC</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Quantity</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">UOM</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Rate</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Total Discount</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Taxable Amount</th>
                                <?php if ($countrycode == 103) {
                                    if ($oneList['igst'] == 0) {
                                ?>
                                        <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                        <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                        <th colspan="2" rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                    <?php } else { ?>
                                        <th class="text-center text-bold invoiceTableHeadStyle" colspan="3">IGST</th>
                                        <th colspan="3" rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                    <?php }
                                } else {
                                    foreach ($taxComponents as $tax) { ?>
                                        <th class="text-center text-bold invoiceTableHeadStyle" colspan="3"><?= $tax['gstType'] ?></th>
                                        <th colspan="3" rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                <?php }
                                } ?>
                            </tr>
                            <tr>
                                <?php if ($countrycode == 103) {
                                    if ($oneList['igst'] == 0) { ?>
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
                            $totaligst = 0;
                            $totalcgst = 0;
                            $totalsgst = 0;
                            $totaltax[] = 0;
                            foreach ($itemDetails as  $item) {
                                $uom = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE uomID='" . $item['baseUnitMeasure'] . "'");
                                $uomName = $uom['data']['uomName'];

                                $totalTaxAmt += $item['item_tax'];
                                $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                                $totalDiscountAmt += $item['discount_amount'];
                                $subTotalAmt += ($item['item_qty'] * $item['item_rate']);
                                $totalAmt += $item['item_amount'];
                                $taxbleAmount = $item['item_qty'] * $item['item_rate'] - ($item['discount_amount']);



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
                                        <?php if ($oneList['type'] == 'project') { ?>
                                            <p><?= decimalQuantityPreview($item['invoiceQty']) ?></p>
                                        <?php } else { ?>
                                            <p><?= decimalQuantityPreview($item['item_qty']) ?></p>
                                        <?php } ?>
                                    </td>
                                    <td class="px-2">
                                        <p><?= $uomName ?></p>
                                    </td>
                                    <td class="text-right px-2">
                                        <p><?= decimalValuePreview($item['item_rate']) ?></p>
                                    </td>
                                    <td class="text-right px-2">
                                        <p><?= decimalValuePreview($item['discount_amount']) ?></p>
                                    </td>
                                    <td>
                                        <p class="text-right"><?= decimalValuePreview($taxbleAmount) ?></p>
                                    </td>
                                    <?php if ($countrycode == 103) {
                                        if ($oneList['igst'] == 0) {
                                            $cgstigstpersentage = $item['item_tax'] / 2;
                                            $cgstAmt = isset($item['cgst']) && $item['cgst'] !== null && $item['cgst'] !== 0 ? $item['cgst'] : ($taxbleAmount * $cgstigstpersentage / 100);
                                            $sgstAmt = isset($item['sgst']) && $item['sgst'] !== null && $item['sgst'] !== 0 ?  $item['sgst'] : ($taxbleAmount * $cgstigstpersentage / 100);

                                            $totalcgst +=  $cgstAmt;
                                            $totalsgst += $sgstAmt;
                                    ?>
                                            <td>
                                                <p> <?= decimalQuantityPreview($cgstigstpersentage); ?>%</p>
                                            </td>
                                            <td>
                                                <p class="text-right"><?= decimalValuePreview($cgstAmt) ?></p>
                                            </td>
                                            <td>
                                                <p> <?=decimalQuantityPreview($cgstigstpersentage); ?>%</p>
                                            </td>
                                            <td>
                                                <p class="text-right"><?= decimalValuePreview($item['sgst']) ?></p>
                                            </td>

                                        <?php } else {
                                            $igstAmt = isset($item['igst']) && $item['igst'] !== null && $item['igst'] !== 0 ? $item['igst'] : ($taxbleAmount * $item['item_tax'] / 100);
                                            $totaligst += $igstAmt;
                                        ?>
                                            <td>
                                                <p> <?= decimalQuantityPreview($item['item_tax']) ?>%</p>
                                            </td>
                                            <td class="px-2" colspan="2">
                                                <p class="text-right"><?= decimalValuePreview($igstAmt) ?></p>
                                            </td>

                                        <?php }
                                    } else {
                                        foreach ($taxComponents as $key => $tax) {
                                            $taxpersentage = $item['item_tax'] / (100 / $tax['taxPercentage']);
                                            $taxAmt = isset($tax['taxAmount']) && $tax['taxAmount'] !== null && $tax['taxAmount'] !== 0 ? $tax['taxAmount'] : ($taxbleAmount * $taxpersentage / 100);

                                            $totaltax[$key] += $taxAmt; ?>
                                            <td>
                                                <p> <?= decimalQuantityPreview($item['item_tax']) ?>%</p>
                                            </td>
                                            <td class="px-2" colspan="2">
                                                <p class="text-right"><?= decimalValuePreview($taxAmt) ?></p>
                                            </td>
                                    <?php }
                                    } ?>
                                    <td class="text-right px-2" colspan="2">
                                        <p class="text-right"><?= decimalValuePreview($item['item_amount']) ?></p>

                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="11" class="font-bold text-right px-2">
                                    <p>Sub Total (<?= $companyCurrencyName ?>)</p>
                                    <p>Total Discount(<?= $companyCurrencyName ?>)</p>
                                    <?php if ($oneList['tcs'] > 0) {
                                    ?>
                                        <p>TCS(<?= $companyCurrencyName ?>)</p>

                                    <?php } ?>
                                    <?php if ($oneList['tds'] > 0) {
                                    ?>
                                        <p>TDS(<?= $companyCurrencyName ?>)</p>

                                    <?php } ?>

                                    <?php if ($countrycode == 103) {
                                        if ($oneList['igst'] == 0) { ?>
                                            <p>Total CGST (<?= $companyCurrencyName ?>)</p>

                                            <p>Total SGST (<?= $companyCurrencyName ?>)</p>

                                        <?php } else { ?>
                                            <p>Total IGST (<?= $companyCurrencyName ?>)</p>

                                        <?php }
                                    } else {
                                        foreach ($taxComponents as $tax) { ?>
                                            <p>Total <?= $tax['gstType'] ?> (<?= $companyCurrencyName ?>)</p>
                                        <?php }
                                    }
                                    if ($oneList['adjustment']) { ?>

                                        <p>Round-Off(<?= $companyCurrencyName ?>)</p>

                                    <?php } ?>

                                </td>
                                <td colspan="2" class="text-right font-bold px-2">
                                    <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($subTotalAmt) ?></p>
                                    <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($totalDiscountAmt) ?></p>
                                    <?php if ($oneList['tcs'] > 0) {
                                    ?>

                                        <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($oneList['tcs']) ?></p>


                                    <?php } ?>
                                    <?php if ($oneList['tds'] > 0) {
                                    ?>
                                        <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($oneList['tds']) ?></p>

                                    <?php } ?>

                                    <?php if ($countrycode == 103) {
                                        if ($oneList['igst'] == 0) { ?>
                                            <p><span class="pr-1"></span><?= decimalValuePreview($totalcgst) ?></p>

                                            <p><span class="pr-1"></span><?= decimalValuePreview($totalsgst) ?></p>

                                        <?php } else { ?>
                                            <p><span class="pr-1"></span><?= decimalValuePreview($totaligst) ?></p>

                                        <?php }
                                    } else {
                                        foreach ($taxComponents as $key => $tax) { ?>
                                            <p><span class="pr-1"></span><?= decimalValuePreview($totaltax[$key]) ?></p>
                                        <?php }
                                    }
                                    if ($oneList['adjustment']) {

                                        ?>
                                        <p><span class="pr-1"></span><?= decimalValuePreview($oneList['adjustment']) ?></p>


                                    <?php } ?>

                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="7" class="px-2">
                                    <p>Amount Chargeable (in words)</p>
                                    <p class="font-bold"><?= $companyCurrencyName . " " . number_to_words_indian_rupees($oneList['total']); ?> ONLY</p>

                                </td>
                                <td colspan="5" class="px-2">
                                    <div class="d-flex justify-content-between">
                                        <p class="font-bold">Grand Total (<?= $companyCurrencyName ?>)</p>

                                        <p class="font-bold"><?= decimalValuePreview($oneList['total']) ?></p>

                                    </div>
                                </td>
                            </tr>

                            <!--Static portion--->

                        </tbody>
                        <tfoot>

                            <tr>
                                <td colspan="12">
                                    <p><b>Remarks: </b> <?= $oneList['remark']; ?> </p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="12" class="text-right">
                                    <p>For <b><?= $companyData['company_name'] ?></b></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <p>Prepared By: <?= getCreatedByUser($oneList['created_by']); ?></p>
                                </td>
                                <td colspan="4">
                                    <p>Checked By</p>
                                </td>
                                <td colspan="5">
                                    <p>Authorised Signatory</p>
                                    <p>
                                    <p class="text-center sign-img">
                                        <img width="120" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="">
                                    </p>
                                    (Signature of the Licencee or his Authorised Agent)
                                    </p>
                                </td>
                            </tr>

                            <!--Static portion--->

                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
<?php
    }
}
