<?php
include_once("../../vendor/func-vendor.php");
class TemplatePayment
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

    public function printManagePayment($pay_id = 0)
    {
        global $company_currency;
        $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
        $companyCurrencyData = $companyCurrencyObj["data"];
        $currency_name = $companyCurrencyData['currency_name'];

        $paymentCollectortypeget = queryGet("SELECT type FROM erp_grn_payments where payment_id = $pay_id")['data']['type'];
        // console($paymentCollectortypeget);
        if ($paymentCollectortypeget == 'vendor') {
            $sqlVendorid = queryGet("SELECT vendor_id FROM `erp_grn_payments` WHERE payment_id=" . $pay_id . "")['data'];
            $paymentCollectortypeName = 'Vendor';
            $vendorDetailsObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id`=" . $sqlVendorid['vendor_id'] . "")['data'];
            $email = $vendorDetailsObj['vendor_authorised_person_email'];
            $addressFetch = getVendorBuisnessAddress($sqlVendorid['vendor_id']);
        } else {
            $sqlCustomerid = queryGet("SELECT customer_id FROM `erp_grn_payments` WHERE payment_id=" . $pay_id . "")['data'];
            $flg = 1;
            $paymentCollectortypeName = 'Customer';
            $customerDetailsObj = queryGet("SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`=" . $sqlCustomerid['customer_id'] . "")['data'];
            $email = $customerDetailsObj['customer_authorised_person_email'];
            $addressFetch = getCustomerPrimaryAddressById($sqlCustomerid['customer_id']);
        }

        // console($sqlCustomerid);
        // console($sqlVendorid);
        // console($addressFetch);
        // console($flg);

        // company details section
        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$this->company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$this->company_id' AND `fldAdminBranchId`='$this->branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id' AND othersLocation_id='$this->location_id'")['data'];
        $companyData = array_merge($companyDetailsObj, $companyAdminDetailsObj, $locationDetailsObj);
        $companyAddress = $companyData['location_flat_no'] . "," . $companyData['location_street_name'] . "," . $companyData['location'] . "," . $companyData['location_city'] . "," . $companyData['location_district'] . "," . $companyData['location_state'] . "," . $companyData['location_pin_code'];

        // vendor details section

        // $vednorAddrssssql = "SELECT gpl.vendor_id, MAX(vbp.vendor_business_legal_name) AS vendor_business_legal_name, MAX(vbp.vendor_business_building_no) AS vendor_business_building_no, MAX(vbp.vendor_business_flat_no) AS vendor_business_flat_no, MAX(vbp.vendor_business_street_name) AS vendor_business_street_name, MAX(vbp.vendor_business_pin_code) AS vendor_business_pin_code, MAX(vbp.vendor_business_location) AS vendor_business_location, MAX(vbp.vendor_business_city) AS vendor_business_city, MAX(vbp.vendor_business_district) AS vendor_business_district, MAX(vbp.vendor_business_country) AS vendor_business_country, MAX(vbp.vendor_business_state) AS vendor_business_state FROM erp_grn_payments_log AS gpl JOIN erp_vendor_bussiness_places AS vbp ON gpl.vendor_id = vbp.vendor_id WHERE gpl.vendor_id=" . $sqlVendorid['vendor_id'] . " GROUP BY gpl.vendor_id";

        // $vendoraddbj = queryGet($vednorAddrssssql)['data'];
        // $vendoraddress = $vendoraddbj['vendor_business_building_no'] . "," . $vendoraddbj['vendor_business_flat_no'] . "," . $vendoraddbj['vendor_business_location'] . "," . $vendoraddbj['vendor_business_district'] . "," . $vendoraddbj['vendor_business_pin_code'];



        // payment advice section
        $sqlpadvice = "SELECT 
                    grnPay.*, 
                    CASE 
                        WHEN grnPay.type = 'vendor' THEN vDetail.vendor_code 
                        ELSE cDetail.customer_code 
                    END AS party_code,
                    CASE 
                        WHEN grnPay.type = 'vendor' THEN vDetail.trade_name 
                        ELSE cDetail.trade_name 
                    END AS party_name
                FROM `erp_grn_payments` AS grnPay
                LEFT JOIN `erp_vendor_details` AS vDetail 
                    ON grnPay.vendor_id = vDetail.vendor_id
                LEFT JOIN `erp_customer` AS cDetail 
                    ON grnPay.customer_id = cDetail.customer_id
                WHERE 
                    grnPay.company_id = '" . $this->company_id . "' 
                    AND grnPay.branch_id = '" . $this->branch_id . "' 
                    AND grnPay.location_id = '" . $this->location_id . "' 
                    AND grnPay.payment_id = '" . $pay_id . "' 
                    AND grnPay.status != 'deleted';";

                    $sqlpadviceobj = queryget($sqlpadvice)['data'];
                    // console($sqlpadviceobj);
                    // console($paymentCollectortypeName);

        $company_id = $sqlpadviceobj['company_id'];
        $branch_id = $sqlpadviceobj['branch_id'];
        $location_id = $sqlpadviceobj['location_id'];


        // table data section

        $sqlInv = "SELECT
                        grninv.grnIvCode,
                        grnlog.payment_amt,
                        grninv.*
                    FROM
                        `erp_grn_payments_log` AS grnlog
                    LEFT JOIN `erp_grninvoice` AS grninv
                    ON
                        grnlog.grn_id = grninv.grnIvId
                    WHERE grnlog.company_id='" . $this->company_id . "' AND grnlog.branch_id='" . $this->branch_id . "' AND grnlog.location_id='" . $this->location_id . "' AND grnlog.payment_id='" . $pay_id . "' AND  grnlog.status!='deleted' AND grninv.grnIvCode IS NOT NULL";

        $sqlinvObj = queryget($sqlInv, true);
        $sqlinvdata = $sqlinvObj['data'];


        ?>
        <div class="printable-view vendor-voucher-view">
            <h2 class="text-center">PAYMENT ADVISE</h2>
            <table class="table-responsive">
                <tbody>
                    <tr>
                        <td width="50%">
                            <div class="company-details">
                                <p>
                                    <img style="max-width: 200px; background-color: #ccc; border-radius: 5px"
                                        src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>"
                                        alt="company logo">
                                </p>
                                <p class="font-bold">Company Details</p>
                                <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                <p><?= $companyAddress ?></p>
                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                            </div>
                        </td>
                        <td>
                            <div class="payment-details">
                                <!-- <p class="font-bold">Payment Advise</p> -->
                                <p>Date : <?= formatDateORDateTime($sqlpadviceobj['documentDate']) ?></p>
                                <p>Document No : <?= $sqlpadviceobj['paymentCode'] ?></p>
                                <p>Transaction Id : <?= $sqlpadviceobj['transactionId'] ?></p>
                                <p>Payment Amount(<?= $currency_name ?>) :
                                    <?= decimalValuePreview($sqlpadviceobj['collect_payment']) ?></p>
                                <p>Payee Code / <?=$paymentCollectortypeName?> Code : <?= $sqlpadviceobj['party_code'] ?></p>
                                <p>Mode : <?= $sqlpadviceobj['mode'] ?></p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="payee-details">
                                <p class="font-bold">Payee Details</p>
                                <p class="font-bold"><?= $sqlpadviceobj['party_name'] ?></p>
                                <p><?= isset($addressFetch) ? $addressFetch : "" ?></p>
                                <p>E-Mail : <?= $email ?></p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="letter-details">
                                <p class="font-bold">Dear Sir / Madam,</p>
                                <p>We have debited your account vide payment document no. <?= $sqlpadviceobj['paymentCode'] ?>
                                    for the below mentioned transactions enunciated below vide bank transfer bearing reference.
                                    <?= $sqlpadviceobj['transactionId'] ?> for <?= $currency_name ?>
                                    <?= decimalValuePreview($sqlpadviceobj['collect_payment']) ?></p>
                            </div>
                            <div class="list-details">
                                <?php if ($sqlinvObj['numRows'] > 0) { ?>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Doc No</th>
                                                <th>Invoice No</th>
                                                <th>Invoice Date</th>
                                                <th>Due Date</th>
                                                <th class="text-right">Invoice Amt</th>
                                                <th class="text-right">Due Amt</th>
                                                <th class="text-right">Paid Amt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalInvamt = 0;
                                            $totalDueamt = 0;
                                            $totalpaidamt = 0;
                                            // $totalduepercentage=0;
                                            foreach ($sqlinvdata as $oneInv) {
                                                $statusLabel = fetchStatusMasterByCode($oneInv['paymentStatus'])['data']['label'];
                                                $due_amt = $oneInv['dueAmt'];
                                                $inv_amt = $oneInv['grnTotalAmount'];
                                                $duePercentage = round(($due_amt / $inv_amt) * 100, 2);

                                                // total calculations
                                                $totalInvamt += $oneInv['grnTotalAmount'];
                                                $totalDueamt += $oneInv['grnTotalAmount'] - $oneInv['payment_amt'];
                                                $totalpaidamt += $oneInv['payment_amt'];
                                                // $totalduepercentage+=round(($totalDueamt/$totalInvamt)*100);
                                

                                                ?>
                                                <tr>
                                                    <td><?= $oneInv['grnIvCode'] ?></td>
                                                    <td><?= $oneInv['vendorDocumentNo'] ?></td>
                                                    <td><?= formatDateORDateTime($oneInv['postingDate']) ?></td>
                                                    <td><?= formatDateORDateTime($oneInv['dueDate']) ?></td>
                                                    <td class="text-right"><?= decimalValuePreview($oneInv['grnTotalAmount']) ?></td>
                                                    <td class="text-right">
                                                        <?= decimalValuePreview(($oneInv['grnTotalAmount'] - $oneInv['payment_amt'])) ?>
                                                    </td>
                                                    <td class="text-right"><?= decimalValuePreview($oneInv['payment_amt']) ?></td>
                                                </tr>
                                                <?php
                                            }

                                            ?>

                                            <tr>
                                                <td colspan="4" class="font-bold">Total (<?= $currency_name ?>)</td>
                                                <td class="text-right font-bold"><?= decimalValuePreview((float) $totalInvamt) ?>
                                                </td>
                                                <td class="text-right font-bold"><?= decimalValuePreview((float) $totalDueamt) ?>
                                                </td>
                                                <td class="text-right font-bold"><?= decimalValuePreview((float) $totalpaidamt) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="thankful-details">
                                <p>We thank you for the business.</p>
                                <p class="font-bold">For <?= $companyData['company_name'] ?></p>
                                <p class="font-bold">Auth Signatory</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
