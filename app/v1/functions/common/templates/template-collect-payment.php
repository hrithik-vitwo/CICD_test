<?php
// require_once("../../../../../app/v1/connection-branch-admin.php");
include_once("../../../../../app/v1/functions/branch/func-brunch-so-controller.php");
include_once("../../../../../app/v1/functions/vendor/func-vendor.php");
class TemplateCollectPaymentController
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

    public function printcollectpayment($paymentId = 0)
    {
        global $companyCountry;
        $dbobj = new Database();
        $BranchSoObj = new BranchSo();

        $companyDetailsObj = $dbobj->queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$this->company_id'")['data'];
        $companyAdminDetailsObj = $dbobj->queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$this->company_id' AND `fldAdminBranchId`='$this->branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $locationDetailsObj = $dbobj->queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id' AND othersLocation_id='$this->location_id'")['data'];
        $companyData = array_merge($companyDetailsObj, $companyAdminDetailsObj, $locationDetailsObj);
        $companyAddressfetch = getCompanyAddress($this->company_id, $this->branch_id, $this->location_id, $companyCountry);
        $companyAddress = $companyData['location_flat_no'] . "," . $companyData['location_street_name'] . "," . $companyData['location'] . "," . $companyData['location_city'] . "," . $companyData['location_district'] . "," . $companyData['location_state'] . "," . $companyData['location_pin_code'];
        $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=" . $companyDetailsObj['company_currency'] . "");
        $companyCurrencyData = $companyCurrencyObj["data"];
        $company_currency = $companyCurrencyData['currency_name'];
        $sts = " AND sopayment.status !='deleted'";
        $cond = "AND sopayment.payment_id=$paymentId";

        $paymentSql = queryGet("SELECT type FROM erp_branch_sales_order_payments where payment_id = $paymentId");
        $paymentCollectortype = $paymentSql['data']['type'];
        // console($paymentCollectortype);
        // $paymentSqldata = $paymentSql['data'];
        // console($paymentSql);
        // console($cond);

        if ($paymentCollectortype != 'vendor') {

            $sql_payment = "SELECT sopayment.*,cust.customer_code as customer_code, cust.trade_name as customer_name ,cust.customer_authorised_person_email as customer_email,custAddress.customer_address_building_no, custAddress.customer_address_flat_no, custAddress.customer_address_street_name, custAddress.customer_address_pin_code, custAddress.customer_address_location, custAddress.customer_address_district, custAddress.customer_address_state FROM `erp_branch_sales_order_payments` as sopayment LEFT JOIN erp_customer as cust on cust.customer_id=sopayment.customer_id LEFT JOIN `erp_customer_address` as custAddress ON sopayment.customer_id = custAddress.customer_address_id WHERE 1 " . $cond . "  AND sopayment.company_id='" . $this->company_id . "'  AND sopayment.branch_id='" . $this->branch_id . "'   AND sopayment.location_id='" . $this->location_id . "' " . $sts . " ORDER BY sopayment.payment_id DESC ";
            $sql_data = $dbobj->queryGet($sql_payment)['data'];
            $paymentCollectorName = $sql_data['customer_name'];
            $paymentCollectorEmail = $sql_data['customer_email'];
            $paymentCollectorCode = $sql_data['customer_code'];
            $paymentCollectoraddressfetch = getCustomerPrimaryAddressById($sql_data['customer_id']);
            $paymentCollectortypeName = 'Customer';
        } else {
            $sql_payment = "SELECT sopayment.*, 
                vend.vendor_id, 
                vend.trade_name AS vendor_name, 
                vend.vendor_code, 
                vend.vendor_authorised_person_email 
                FROM `erp_branch_sales_order_payments` AS sopayment 
                LEFT JOIN `erp_vendor_details` AS vend ON vend.vendor_id = sopayment.vendor_id 
                WHERE 1 " . $cond . "  AND sopayment.company_id='" . $this->company_id . "' AND sopayment.branch_id='" . $this->branch_id . "'   
                AND sopayment.location_id='" . $this->location_id . "' " . $sts . " 
                ORDER BY sopayment.payment_id DESC ";
            $sql_data = $dbobj->queryGet($sql_payment)['data'];
            // console($sql_payment);
            $paymentCollectorName = $sql_data['vendor_name'];
            $paymentCollectorEmail = $sql_data['vendor_authorised_person_email'];
            $paymentCollectorCode = $sql_data['vendor_code'];
            $paymentCollectoraddressfetch = getVendorBuisnessAddress($sql_data['vendor_id']);
            $paymentCollectortypeName = 'Vendor';
            
        }

        $company_id = $sql_data['company_id'];
        $branch_id = $sql_data['branch_id'];
        $location_id = $sql_data['location_id'];
        $customerAddress = $sql_data['customer_address_building_no'] . ', ' . $sql_data['customer_address_flat_no'] . ', ' . $sql_data['customer_address_street_name'] . ', ' . $sql_data['customer_address_pin_code'] . ', ' . $sql_data['customer_address_location'] . ', ' . $sql_data['customer_address_district'] . ', ' . $sql_data['customer_address_state'];


        $sql_inv_log = "SELECT paylog.*, invoice.invoice_date, invoice.credit_period, invoice.invoiceStatus, invoice.due_amount, invoice.all_total_amt FROM `erp_branch_sales_order_payments_log` AS paylog LEFT JOIN erp_branch_sales_order_invoices AS invoice ON invoice.so_invoice_id = paylog.invoice_id WHERE paylog.payment_type = 'pay' AND paylog.company_id = $company_id AND paylog.branch_id = $branch_id AND paylog.location_id = $location_id AND invoice.company_id = $company_id AND invoice.branch_id = $branch_id AND invoice.location_id = $location_id AND paylog.payment_id = $paymentId AND paylog.status != 'deleted';";
        $inv_data = $dbobj->queryGet($sql_inv_log, true);

?>
        <div class="printable-view vendor-voucher-view">
            <h2 class="text-center">COLLECTION ADVISE</h2>
            <table>
                <tbody>
                    <tr>
                        <td width="50%">
                            <div class="company-details">
                                <p>
                                    <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                </p>
                                <p class="font-bold">Company Details</p>
                                <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                <p><?= $companyAddressfetch ?></p>
                                <p>E-Mail : <?= $companyData['companyEmail'] ?></p>
                            </div>
                        </td>
                        <td>
                            <div class="payment-details">
                                <!-- <p class="font-bold">Collection Advise</p> -->
                                <p>Date : <?= formatDateORDateTime($sql_data['documentDate']) ?></p>
                                <p>Document No :<?= $sql_data['collectionCode'] ?></p>
                                <p>Transaction Id : <?= $sql_data['transactionId'] ?></p>
                                <p>Amount Collected(<?= $company_currency ?>) : <?= decimalValuePreview($sql_data['collect_payment']) ?></p>
                                <p><?= $paymentCollectortypeName ?> Code :<?= $paymentCollectorCode  ?></p>
                                <p>Mode :<?= $sql_data['mode'] ?></p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="payee-details">
                                <p><?= $paymentCollectortypeName ?> Details</p>
                                <p class="font-bold"><?= $paymentCollectorName ?></p>
                                <p><?= $paymentCollectoraddressfetch ?></p>
                                <p>E-Mail : <?= $paymentCollectorEmail ?></p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="letter-details">
                                <p class="font-bold">Dear Sir / Madam,</p>
                                <p>We have credited your account vide payment document no. <?= $sql_data['collectionCode'] ?> for the below mentioned transactions enunciated below vide bank transfer bearing reference. <?= $sql_data['transactionId'] ?> for <?= $company_currency ?> <?= decimalValuePreview($sql_data['collect_payment']) ?></p>
                            </div>
                            <div class="list-details">

                                <?php if ($inv_data['numRows'] > 0) { ?>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Invoice No</th>
                                                <th>Invoice Date</th>
                                                <th>Due Date</th>
                                                <th class="text-right">Invoice Amt</th>
                                                <th class="text-right">Due Amount</th>
                                                <th class="text-right">Received Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalInvamt = 0;
                                            $totalDueamt = 0;
                                            $totalpaidamt = 0;
                                            $totalCreditamt = 0;
                                            $totalduepercentage = 0;
                                            foreach ($inv_data['data'] as $oneInv) {
                                                $inv = $BranchSoObj->fetchBranchSoInvoiceById($oneInv['invoice_id'])['data'][0];
                                                $invoice_no = $inv['invoice_no'];
                                                // console($oneInv);

                                                $days = $oneInv['credit_period'];
                                                $date = date_create($oneInv['invoice_date']);
                                                date_add($date, date_interval_create_from_date_string($days . " days"));
                                                $creditPeriod = date_format($date, "Y-m-d");
                                                // console($inv); 
                                                // console($invoice_no);

                                                // $statusLabel = fetchStatusMasterByCode($oneInv['paymentStatus'])['data']['label'];
                                                $statusLabel = fetchStatusMasterByCode($oneInv['invoiceStatus'])['data']['label'];


                                                $inv_amt = $oneInv['all_total_amt'];
                                                // $due_amt = $oneInv['due_amount'];
                                                $due_amt = $oneInv['all_total_amt'] - $oneInv['payment_amt'];
                                                $duePercentage = round(($due_amt / $inv_amt) * 100, 2);

                                                // total calculations
                                                $totalInvamt += $oneInv['all_total_amt'];
                                                $totalDueamt += $oneInv['due_amount'];
                                                $totalpaidamt += $oneInv['payment_amt'];
                                                $totalduepercentage += round(($totalDueamt / $totalInvamt) * 100);

                                                //calculate due amount 
                                                $totalCreditNoteAmount = 0;


                                                $cnSql = "SELECT *  FROM erp_credit_note WHERE creditNoteReference='" . $oneInv['invoice_id'] . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id AND `creditors_type`='$paymentCollectortype' AND status='active'";

                                                $cnRes = queryGet($cnSql, true);
                                                if ($cnRes['numRows'] > 0) {
                                                    $cnData = $cnRes['data'];

                                                    $totalCreditNoteAmount = queryGet("SELECT SUM(total) AS totalCreditNoteAmount FROM erp_credit_note WHERE creditNoteReference='" . $oneInv['invoice_id'] . "' AND company_id=$company_id AND branch_id=$branch_id AND location_id=$location_id GROUP BY creditNoteReference;")['data']['totalCreditNoteAmount'];
                                                }

                                                $totalCreditamt = $totalCreditamt + $totalCreditNoteAmount;

                                                // echo $totalDueamt;


                                            ?>
                                                <tr>
                                                    <td><?= $invoice_no ?></td>
                                                    <td><?= formatDateWeb($oneInv['invoice_date']) ?></td>
                                                    <td><?= formatDateWeb($creditPeriod) ?></td>
                                                    <td class="text-right"><?= decimalValuePreview($oneInv['all_total_amt']) ?></td>
                                                    <td class="text-right"><?= decimalValuePreview($oneInv['all_total_amt'] - $oneInv['payment_amt'] - $totalCreditNoteAmount) ?></td>
                                                    <td class="text-right"><?= decimalValuePreview($oneInv['payment_amt']) ?></td>
                                                </tr>
                                            <?php

                                            }

                                            ?>

                                            <tr>
                                                <td colspan="3" class="font-bold">Total (<?= $company_currency ?>)</td>
                                                <td class="text-right font-bold"><?= decimalValuePreview($totalInvamt) ?></td>
                                                <td class="text-right font-bold"><?= decimalValuePreview($totalInvamt - $totalpaidamt - $totalCreditamt) ?></td>
                                                <td class="text-right font-bold"><?= decimalValuePreview($totalpaidamt) ?></td>
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
                                <p>We thank you for your business patronage with us & looking forward to serving.</p>
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
