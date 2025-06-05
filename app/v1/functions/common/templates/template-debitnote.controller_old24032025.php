<?php
include_once("../../app/v1/functions/branch/func-brunch-so-controller.php");

require __DIR__ . '/../../../../../vendor/autoload.php'; 
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\PngWriter;
class TemplateDebitNoteController
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

    public function printDebitNotes($dr_note_id = 0)
    {
        $branchSoObj = new BranchSo();
        $oneList = queryGet("SELECT * FROM `erp_debit_note` WHERE dr_note_id =$dr_note_id")['data'];


        
        $company_id = $oneList['company_id'];
        $branch_id = $oneList['branch_id'];
        $location_id = $oneList['location_id'];
        $get_country = queryGet("SELECT * FROM `erp_companies` WHERE company_id = $company_id");
        $company_country  = $get_country['data']['company_country'];

        $components = getLebels($company_country)['data'];
        $components = json_decode($components, true);
        
         

        // console($oneList);

        $companyDetailsObj = queryGet("SELECT company_website,company_name,company_pan,company_cin,company_tan,company_currency,company_logo,`signature`,company_footer FROM `" . ERP_COMPANIES . "` WHERE `company_id`='$this->company_id'")['data'];
        $companyAdminDetailsObj = queryGet("SELECT fldAdminEmail as companyEmail,fldAdminPhone as companyPhone FROM `" . TBL_BRANCH_ADMIN_DETAILS . "` WHERE `fldAdminCompanyId`='$this->company_id' AND `fldAdminBranchId`='$this->branch_id' AND `fldAdminRole`=1 ORDER BY `fldAdminKey`")['data'];
        $branchDetailsObj = queryGet("SELECT branch_name,branch_gstin FROM `" . ERP_BRANCHES . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id'")['data'];
        $companyBankDetailsObj = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE company_id='$this->company_id' AND flag='1'")['data'];
        $locationDetailsObj = queryGet("SELECT othersLocation_building_no as location_building_no,othersLocation_flat_no as location_flat_no, othersLocation_street_name as location_street_name, othersLocation_pin_code as location_pin_code, othersLocation_location as `location`, othersLocation_city as location_city, othersLocation_district as location_district, othersLocation_state as location_state FROM `" . ERP_BRANCH_OTHERSLOCATION . "` WHERE `branch_id`='$this->branch_id' AND `company_id`='$this->company_id' AND othersLocation_id='$this->location_id'")['data'];
        $companyData = array_merge($companyDetailsObj, $companyBankDetailsObj, $companyAdminDetailsObj, $branchDetailsObj, $locationDetailsObj);

        $currencyDetails = $branchSoObj->fetchCurrencyIcon($companyData['company_currency'])['data'];
        $companyCurrencyName = $currencyDetails['currency_name'];

        $itemDetailsObj = queryGet("SELECT * FROM `debit_note_item` AS dr_item, `erp_inventory_items` AS item  WHERE item.itemId=dr_item.item_id AND `debit_note_id` = '" . $dr_note_id . "'", true);

        $itemDetails = $itemDetailsObj['data'];
        // console($itemDetailsObj);
        
        $contactDetails=queryGet("SELECT `contact_details` FROM `erp_debit_note` WHERE `dr_note_id`='".$dr_note_id."'")['data']['contact_details'];



        $bill_id = $oneList['debitNoteReference'];
        $debitor_type = $oneList['debitor_type'];
        if ($debitor_type == 'customer') {
            $customerDetailsObj = queryGet("SELECT * FROM `" . ERP_CUSTOMER . "` WHERE `customer_id`=".$oneList['party_id']."");
            $customerData = $customerDetailsObj['data'];
            // console($customerDetailsObj);
            $branchGstin = substr($companyData['branch_gstin'], 0, 2);
            $customerGstin = substr($customerData['customer_gstin'], 0, 2);
            $conditionGST = $branchGstin == $customerGstin;

            $iv = queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE `so_invoice_id`=$bill_id");
            // console($iv);
            $ref = $iv['data']['invoice_no'];
            $iv_date = explode(" ", $iv['data']['created_at'], 1);

            $source_address_sql = queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $oneList['billing_address'] . "' ")['data'];

            $source_address = $source_address_sql['customer_address_building_no'] . ',' . $source_address_sql['customer_address_flat_no'] . ',' . $source_address_sql['customer_address_street_name'] . ',' . $source_address_sql['customer_address_pin_code'] . ',' . $source_address_sql['customer_address_location'] . ',' . $source_address_sql['customer_address_city'] . ',' . $source_address_sql['customer_address_district'] . ',' . $source_address_sql['customer_address_country'] . ',' . $source_address_sql['customer_address_state'];
            // console($iv_date);

            $destination_address_sql =  queryGet("SELECT * FROM `erp_customer_address` WHERE `customer_address_id`= '" . $oneList['shipping_address'] . "' ")['data'];

            $destination_address = $destination_address_sql['customer_address_building_no'] . ',' . $destination_address_sql['customer_address_flat_no'] . ',' . $destination_address_sql['customer_address_street_name'] . ',' . $destination_address_sql['customer_address_pin_code'] . ',' . $destination_address_sql['customer_address_location'] . ',' . $destination_address_sql['customer_address_city'] . ',' . $destination_address_sql['customer_address_district'] . ',' . $destination_address_sql['customer_address_country'] . ',' . $destination_address_sql['customer_address_state'];
        } else {
            $customerDetailsObj = queryGet("SELECT * FROM `" . ERP_VENDOR_DETAILS . "` WHERE `vendor_id`=".$oneList['party_id']."");
            $customerData = $customerDetailsObj['data'];
            // console($customerDetailsObj);
            $branchGstin = substr($companyData['branch_gstin'], 0, 2);
            $customerGstin = substr($customerData['vendor_gstin'], 0, 2);
            $conditionGST = $branchGstin == $customerGstin;

            $iv = queryGet("SELECT * FROM `erp_grninvoice` WHERE `grnIvId`=$bill_id");
            // console($iv);
            $ref = $iv['data']['grnIvCode'];
            $iv_date = explode(" ", $iv['data']['created_at'], 1);

            // console($iv_date);
            $source_address_sql = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_business_id`= '" . $oneList['billing_address'] . "' ")['data'];
            // console($source_address_sql);

            $source_address = $source_address_sql['vendor_business_building_no'] . ',' . $source_address_sql['vendor_business_flat_no'] . ',' . $source_address_sql['vendor_business_street_name'] . ',' . $source_address_sql['vendor_business_pin_code'] . ',' . $source_address_sql['vendor_business_location'] . ',' . $source_address_sql['vendor_business_city'] . ',' . $source_address_sql['vendor_business_district'] . ',' . $source_address_sql['vendor_business_country'] . ',' . $source_address_sql['vendor_business_state'];

            }


            $e_inv_detail = queryGet("SELECT `irn`,`signed_qr_code`,`ack_no`,`ack_date` FROM `erp_e_invoices` WHERE `invoice_id` = $dr_note_id AND `document_type` = 'DBN' ");
            $eInvDetailData=$e_inv_detail['data'];
            if($e_inv_detail['numRows'] > 0){
    
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




?>

        <div class="card classic-view bg-transparent">
            <div class="card-body classic-view-so-table" style="overflow: auto;">
                <div class="printable-view">
                    <h3 class="h3-title text-center font-bold text-sm mb-4">Debit Note</h3>


                    <table class="classic-view table-bordered tableBorder">
                        <tbody>
                            <tr>
                                <td colspan="3" class="px-2 border-right-0">
                                    <p class="text-left header-logo">
                                        <!-- <img width="130" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt=""> -->
                                        <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                    </p>
                                </td>
                                <td colspan="6" class="border-right-0 border-left-0">
                                    <p class="font-bold"> <?= $companyData['company_name'] ?></p>
                                    <p><?= $companyData['location_building_no'] ?></p>
                                    <p>Flat No.<?= $companyData['location_flat_no'] ?>, <?= $companyData['location_street_name'] ?>,</p>
                                    <p><?= $companyData['location'] ?>, <?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?> <?= $companyData['location_pin_code'] ?></p>
                                    <p>State Name : <?= fetchStateNameByGstin($companyData['branch_gstin']) ?>, Code : <?= substr($companyData['branch_gstin'], 0, 2); ?></p>
                                    <p>E-Mail : <?= explode('||',$contactDetails)[1] ?></p>
                                    <p>Phone No : <?= explode('||',$contactDetails)[2]?></p>
                                    <?php if($e_inv_detail['numRows'] > 0){  
                                        ?>   
                                    <p>IRN No : <?= $irn ?></p>
                                    <p>Ack. No: <?= $eInvDetailData['ack_no'] ?></p>
                                    <p>Ack. Date: <?= formatDateORDateTime($eInvDetailData['ack_date']) ?></p>
                                    <?php } ?>
                                </td>
                                <td colspan="3" class="border-left-0 vertical-align-bottom">
                                <?php if($e_inv_detail['numRows'] > 0){
                                    ?>
                                <img src="<?php echo $qrCodeDataUri; ?>" alt="QR Code">

                                <?php
                                } 
                                // else{
                                //     echo 'no QR Code found!';
                                // }
                                ?>
                                
                                <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $companyData['branch_gstin'] ? $companyData['branch_gstin'] : ' --' ?></p><?php } ?>
                            <?php if ($components['fields']['taxNumber'] != null) { ?>
                                <p><?= $components['fields']['taxNumber'] ?>: <?= $companyData['company_pan'] ? $companyData['company_pan'] : '--' ?>
                                </p><?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td rowspan="2" colspan="9">
                                    <div class="d-flex">
                                        <div class="cust-details">
                                            <p class="font-bold"> To, <?= $customerData['trade_name'] ?></p>
                                            <p style="white-space: pre-wrap;"><?= $source_address ?></p>
                                            <p class="font-bold" style="white-space: pre-wrap;"> Contact Person :<?= $customerData['vendor_authorised_person_name'] ?></p>
                                            <p class="font-bold" style="white-space: pre-wrap;"> Mobile No :<?= $customerData['vendor_authorised_person_phone'] ?></p>
                                        </div>
                                        <div class="add-details text-left">
                                           
                                        <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $companyData['branch_gstin'] ? $companyData['branch_gstin'] : ' --' ?></p><?php } ?>
                            <?php if ($components['fields']['taxNumber'] != null) { ?>
                                <p><?= $components['fields']['taxNumber'] ?>: <?= $companyData['company_pan'] ? $companyData['company_pan'] : '--' ?>
                                </p><?php } ?>
                                            <!-- <p>State Name : <?= fetchStateNameByGstin($customerData['vendor_gstin']) ?></p>
                                            <p>Code : <?= substr($customerData['vendor_gstin'], 0, 2); ?></p> -->
                                          
                                        </div>
                                    </div>
                                </td>
                                <td colspan="3">
                                    <div class="code-details border-bottom">
                                        <p>Debit Note No</p>
                                        <p class="font-bold"><?= $oneList['debit_note_no'] ?></p>
                                        <p class="font-bold"><?php $invDate = date_create($oneList['postingDate']);
                                                                echo date_format($invDate, "F d,Y"); ?></p>
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
                            <?php
                            $branchGstin = substr($companyData['branch_gstin'], 0, 2);
                            $customerGstin = substr($customerData['vendor_gstin'], 0, 2);
                            $conditionGST = $branchGstin == $customerGstin;
                            ?>
                            <tr>
                                <th rowspan="2" class="invoiceTableHeadStyle">Sl No.</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Item Name</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">HSN/SAC</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Quantity</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">UOM</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Rate</th>
                                <th rowspan="2" class="invoiceTableHeadStyle">Taxable Amount</th>
                                <?php
                                if ($conditionGST || $customerGstin == "") {
                                ?>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">CGST</th>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="2">SGST</th>
                                    <th colspan="2" rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                <?php } else { ?>
                                    <th class="text-center text-bold invoiceTableHeadStyle" colspan="3">IGST</th>
                                    <th colspan="3" rowspan="2" class="invoiceTableHeadStyle">Total Amount</th>
                                <?php } ?>
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
                            $totaligst=0;
                            $totalcgst=0;
                            $totalsgst=0;
                            foreach ($itemDetails as  $item) {
                                $uom = queryGet("SELECT `uomName` FROM `erp_inventory_mstr_uom` WHERE uomID='" . $item['uomRel'] . "'");
                                $uomName = $uom['data']['uomName'];

                                $totalTaxAmt += $item['item_tax'];
                                $allSubTotalAmt += $item['unitPrice'] * $item['qty'];
                                $totalDiscountAmt += $item['itemTotalDiscount'];
                                $subTotalAmt += ($item['item_qty'] * $item['item_rate']);
                                $totalAmt += $item['item_amount'];
                                $taxbleAmount = $item['item_qty'] * $item['item_rate'];

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
                                        <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                            <small class="text-small text-muted"><?= $customerCurrencyName . ' ' . number_format($item['unitPrice'] * $currencyConversionRate, 2) ?></small>
                                        <?php } ?> -->
                                    </td>
                                    <td>
                                        <p><?= decimalValuePreview($taxbleAmount) ?></p>
                                    </td>
                                    <?php
                                    if ($conditionGST || $customerGstin == "") {
                                        $totalcgst+=$item['cgst'];
                                        $totalsgst+=$item['sgst'];

                                    ?>
                                        <td>
                                            <p > <?= decimalQuantityPreview($item['item_tax'] / 2) ?>%</p>
                                        </td>
                                        <td>
                                            <p class="text-right"><?= decimalValuePreview($item['cgst']) ?></p>
                                        </td>
                                        <td>
                                            <p> <?= decimalQuantityPreview($item['item_tax']/ 2) ?>%</p>
                                        </td>
                                        <td>
                                            <p class="text-right"><?= decimalValuePreview($item['sgst']) ?></p>
                                        </td>

                                    <?php } else { 
                                        $totaligst+=$item['igst'];
                                        ?>
                                        <td>
                                            <p> <?= decimalQuantityPreview($item['item_tax']) ?>%</p>
                                        </td>
                                        <td class="px-2" colspan="2">
                                            <p class="text-right"><?= decimalValuePreview($item['igst']) ?></p>
                                        </td>
                                    <?php } ?>
                                    <td class="text-right px-2" colspan="2">
                                        <p class="text-right"><?= decimalValuePreview($item['item_amount']) ?></p>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td colspan="11" class="font-bold text-right px-2">
                                    <p>Sub Total (<?= $companyCurrencyName ?>)</p>

                                    <?php if ($conditionGST || $customerGstin == "") { ?>
                                        <p>Total CGST (<?= $companyCurrencyName ?>)</p>
                                        <p>Total SGST (<?= $companyCurrencyName ?>)</p>
                                    <?php } else { ?>
                                        <p>Total IGST (<?= $companyCurrencyName ?>)</p>
                                    <?php } ?>
                                </td>
                                <td colspan="2" class="text-right font-bold px-2">
                                    <p><span class="rupee-symbol pr-1"></span><?= decimalValuePreview($subTotalAmt) ?></p>                                  

                                    <?php if ($conditionGST || $customerGstin == "") { ?>
                                        <p><span class="pr-1"></span><?= decimalValuePreview($totalcgst) ?></p>
                                        <p><span class="pr-1"></span><?= decimalValuePreview($totalsgst) ?></p>
                                    <?php } else { ?>
                                        <p><span class="pr-1"></span><?= decimalValuePreview($totaligst) ?></p>
                                    <?php } ?>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td colspan="7" class="px-2">
                                    <p>Amount Chargeable (in words)</p>
                                    <p class="font-bold"><?= $companyCurrencyName . " " . number_to_words_indian_rupees($oneList['total']); ?> ONLY</p>
                                    <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                                <small class="text-small text-muted"><?= $customerCurrencyName . " " . number_to_words_indian_rupees(number_format($oneList['total'] * $currencyConversionRate, 2)) ?></small>
                                            <?php } ?> -->
                                </td>
                                <td colspan="5" class="px-2">
                                    <div class="d-flex justify-content-between">
                                        <p class="font-bold">Grand Total (<?= $companyCurrencyName ?>)</p>
                                        <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                            <small class="text-small text-muted">Grand Total (<?= $customerCurrencyName ?>)</small>
                                        <?php } ?> -->
                                        <p class="font-bold"><?= decimalValuePreview($oneList['total']) ?></p>
                                        <!-- <?php if ($companyCurrencyName != $customerCurrencyName) { ?>
                                            <small class="text-small text-muted"><?= number_format($oneList['total'] * $currencyConversionRate, 2) ?></small>
                                        <?php } ?> -->
                                    </div>
                                </td>
                            </tr>

                            <!--Static portion--->

                        </tbody>
                        <tfoot>

                            <tr>
                                <td colspan="12">
                                    <p><b>Remarks: </b> <?= $oneList['remark']; ?> </p>
                                    <p><b>Inv no:</b> <?= $ref ?></p>
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
                                    <p>Checked By: </p>
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
                        <!-- <tfoot>
                            <tr>
                                <td colspan="7" class="px-2">
                                    <p>Remarks: <?= $oneList['remarks'] ?></p>
                                    <p>Declaration: <?= $oneList['declaration_note'] ?></p>
                                    <p><?= $companyData['company_footer'] ?></p>
                                    <p>Created By: <strong><?= getCreatedByUser($oneList['created_by']); ?></strong></p>
                                    <?php if ($attachmentObj['status'] == 'success') { ?>
                                        <a href="<?= COMP_STORAGE_URL . '/others/' ?><?= $attachmentObj['data']['file_name'] ?>" target="_blank" class="text-primary font-bold text-decoration-none text-decoration-underline" download>
                                            View Attachment
                                        </a>
                                    <?php } ?>
                                </td>
                                <td colspan="6" class="text-right px-2">
                                    <p class="text-center font-bold">For <?= $companyData['company_name'] ?></p>
                                    <p class="text-center sign-img">
                                        <img width="160" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="">
                                    </p>
                                </td>
                            </tr>
                        </tfoot> -->
                    </table>

                </div>
            </div>
        </div>
<?php
    }
}
