<?php
// include_once("../../../../../app/v1/connection-branch-admin.php");
// include_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");

class TemplatePoController
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


    public function printPoItems($poId = 0)
    {
        global $company_currency;

        $BranchPoObj = new BranchPo();
        $dbObj = new Database();
        $cond = 'AND po_id ="' . $poId . '"';
        $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . "  AND`branch_id`=$this->branch_id AND `location_id`=$this->location_id AND `company_id`=$this->company_id   ORDER BY po_id  ";

        $qry_list = $dbObj->queryGet($sql_list);
        // console($qry_list);



        $onePoList = $qry_list['data'];

        $company_id = $onePoList['company_id'];
        $branch_id = $onePoList['branch_id'];
        $location_id = $onePoList['location_id'];

        $qry = queryGet("SELECT tc_text FROM `erp_applied_terms_and_conditions` WHERE slug='po' AND slug_id=" . $poId . "")['data'];
        $termscond = stripcslashes(unserialize($qry['tc_text']));
        // console($termscond);
        // exit();
?>



        <div class="tab-pane " id="preview<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="preview-tab">
            <div class="card classic-view bg-transparent">
                <div class="card-body classic-view-so-table" style="overflow: auto;">
                    <div class="printable-view">
                        <h3 class="h3-title text-center font-bold text-sm mb-4">Purchase Order</h3>

                        <?php
                        $vendor_id = $onePoList['vendor_id'];
                        $companyData = $BranchPoObj->fetchCompanyDetailsById($this->company_id)['data'];
                        $sqlGstFetch="SELECT br.branch_gstin as gstno FROM `erp_branches` as br WHERE br.company_id=$company_id AND br.branch_id = $branch_id";

                        $gstNo=queryGet($sqlGstFetch)['data']['gstno']??'-';

                        $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                        $vendor_address = $dbObj->queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id` = $vendor_id AND `vendor_business_primary_flag` = 1");
                        //  console($vendor_address);
                        $check_cur = $dbObj->queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`='" . $onePoList['currency'] . "'");

                        $itemDetails = $BranchPoObj->fetchBranchPoItems($onePoList['po_id'])['data'];
                        $bill_location_sql = $dbObj->queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = '" . $onePoList['bill_address'] . "'");
                        $ship_location_sql = $dbObj->queryGet("SELECT * FROM `erp_branch_otherslocation` WHERE `othersLocation_id` = '" . $onePoList['ship_address'] . "'");
                        //  console($ship_location_sql);
                        $ship_location = $ship_location_sql['data']['othersLocation_name'] . "," . $ship_location_sql['data']['othersLocation_building_no'] . "," . $ship_location_sql['data']['othersLocation_flat_no'] . "," . $ship_location_sql['data']['othersLocation_street_name'] . "," . $ship_location_sql['data']['othersLocation_pin_code'] . "," . $ship_location_sql['data']['othersLocation_location'] . "," . $ship_location_sql['data']['othersLocation_city'] . "," . $ship_location_sql['data']['othersLocation_district'] . "," . $ship_location_sql['data']['othersLocation_state'];

                        $companyCurrencyObj = $dbObj->queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                        $companyCurrencyData = $companyCurrencyObj["data"];

                        $comp_currency = $companyCurrencyData["currency_name"];

                        if ($vendor_address['numRows'] > 0) {
                            $v_address = $vendor_address['data'];
                        } else {
                            $vendor_address_first = $dbObj->queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id` = $vendor_id AND `vendor_business_active_flag`=0  ORDER BY `vendor_business_id` ASC");
                            // console($vendor_address_first);
                            $v_address = $vendor_address_first['data'];
                        }

                        ?>

                        <table class="classic-view table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="6" class="border-right">
                                        <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                        <p class="font-bold"><?= $vendorDetails['trade_name'] ?></p>
                                        <?php if ($v_address != null) { ?>
                                            <p><?= $v_address['vendor_business_flat_no'] ?>, <?= $v_address['vendor_business_building'] ?></p>
                                            <p><?= $v_address['vendor_business_district'] ?>,<?= $v_address['vendor_business_location'] ?>,<?= $v_address['vendor_business_pin'] ?></p>
                                            <p><?= $v_address['vendor_city'] ?></p>
                                            <p>State Name :<?= $vendorDetails['vendor_business_state'] ?></p>
                                        <?php
                                        }
                                        ?>

                                        <p>GSTIN/UIN: <?= $vendorDetails['vendor_gstin'] ?></p>

                                        <p>Company’s PAN: <?= $vendorDetails['vendor_pan'] ?></p>


                                    </td>
                                    <td colspan="3">
                                        <p>Purchase Order Number : <span class="font-bold"><?= $onePoList['po_number'] ?></span></p>
                                        <!-- <p class="font-bold"><?= $onePoList['po_number'] ?></p> -->

                                        <p>INCO Terms : <span class="font-bold"><?= ucfirst($onePoList['po_type']) . "/" . $onePoList['inco_type'] ?></span></p>
                                        <!-- <p class="font-bold"><?= ucfirst($onePoList['po_type']) . "/" . $onePoList['inco_type'] ?></p> -->
                                        <p>Credit Period : <span class="font-bold"><?= $vendorDetails['vendor_credit_period'] ?> days</span></p>

                                    </td>
                                    <td colspan="2">
                                        <p>Dated :<?= formatDateORDateTime($onePoList['po_date']) ?></p>

                                        <p>Validity Period :<?= formatDateORDateTime($onePoList['validityperiod']) ?></p>

                                        <p>Delivery Date :<?= formatDateORDateTime($onePoList['delivery_date']) ?></p>

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="border-right">
                                        <p>Bill To Address</p>
                                        <p class="font-bold"><?= $companyData['company_name']  ?></p>
                                        <p class="font-bold"><?= $bill_location_sql['data']['othersLocation_name'] ?></p>
                                        <p><?= $bill_location_sql['data']['othersLocation_building_no'] ?> ,<?= $bill_location_sql['data']['othersLocation_flat_no']  ?>,<?= $bill_location_sql['data']['othersLocation_street_name'] ?>, <?= $bill_location_sql['data']['othersLocation_pin_code'] ?>, <?= $bill_location_sql['data']['othersLocation_location'] ?>, <?= $bill_location_sql['data']['othersLocation_city'] ?>, <?= $bill_location_sql['data']['othersLocation_district'] ?></p>
                                        <p>GST No : <?= $gstNo ?></p>
                                        <p>State Name : <?= $bill_location_sql['data']['othersLocation_state'] ?></p>
                                        <p>Place of Supply : <?= $bill_location_sql['data']['othersLocation_state'] ?></p>
                                    </td>
                                    <td colspan="5">
                                        <p>Ship To Address</p>
                                        <p class="font-bold"><?= $companyData['company_name']  ?></p>
                                        <p class="font-bold"><?= $ship_location_sql['data']['othersLocation_name'] ?></p>
                                        <p><?= $ship_location_sql['data']['othersLocation_building_no'] ?> ,<?= $ship_location_sql['data']['othersLocation_flat_no']  ?>,<?= $ship_location_sql['data']['othersLocation_street_name'] ?>, <?= $ship_location_sql['data']['othersLocation_pin_code'] ?>, <?= $ship_location_sql['data']['othersLocation_location'] ?>, <?= $ship_location_sql['data']['othersLocation_city'] ?>, <?= $ship_location_sql['data']['othersLocation_district'] ?></p>
                                        <p>State Name : <?= ($ship_location_sql['data']['othersLocation_state']) ?></p>
                                        <p>Place of Supply : <?= $ship_location_sql['data']['othersLocation_state'] ?></p>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Particulars</th>
                                    <th>Quantity</th>
                                    <th>UOM</th>
                                    <th>HSN</th>
                                    <th>Rate</th>
                                    <th>Base Amount</th>
                                    <th>GST</th>
                                    <th>GST Amount</th>
                                    <th>Currency</th>
                                    <th>Total Amount</th>
                                </tr>
                                <?php


                                $cnt = 1;
                                foreach ($itemDetails as $oneItems) {
                                    // console($oneItems);
                                    $hsnCodeObj = getHSNCodeByItemId($oneItems['inventory_item_id'])['data'];

                                ?>
                                    <tr>
                                        <td class="text-center"><?= $cnt++ ?></td>
                                        <td class="text-center">
                                            <p class="font-bold"><?= $oneItems['itemName'] ?></p>
                                            <p class="text-italic"><?= $oneItems['itemCode'] ?></p>
                                        </td>

                                        <td class="text-center"><p><?= decimalQuantityPreview($oneItems['qty']) ?></p></td>
                                        <td class="text-center"><p><?= $oneItems['uom'] ?></p></td>
                                        <td class="text-center"><p><?= hsnInProperFormat($hsnCodeObj['hsnCode']) ?></p></td>
                                        <td class="text-right"><p><?= decimalValuePreview($oneItems['unitPrice'] * $onePoList['conversion_rate']) ?></p></td>
                                        <td class="text-right"><p><?= decimalValuePreview(($oneItems['total_price'] - $oneItems['gstAmount']) * $onePoList['conversion_rate']) ?></p></td>
                                        <td class="text-right"><p><?=decimalValuePreview( $oneItems['gst']) ?></p></td>
                                        <td class="text-right"><p><?= decimalValuePreview($oneItems['gstAmount'] * $onePoList['conversion_rate']) ?></p></td>
                                        <td class="text-right"><p><?= $check_cur['data']['currency_name'] ?></p></td>
                                        <td class="text-right"><p><?= decimalValuePreview($oneItems['total_price'] * $onePoList['conversion_rate']) ?></p></td>
                                    </tr>
                                <?php

                                }

                                ?>
                                <tr>
                                    <td colspan="11" class="text-right font-bold">

                                        <?php
                                        if ($check_cur['data']['currency_name'] != $comp_currency) {
                                            echo $check_cur['data']['currency_name']." ";
                                            echo decimalValuePreview($onePoList['totalAmount'] * $onePoList['conversion_rate']);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p> Total Amount (in words)</p>
                                        <p class="font-bold"><?= number_to_words_indian_rupees($onePoList['totalAmount']); ?></p>
                                    </td>
                                    <td colspan="4">
                                        <?php echo "* All values are in " . $check_cur['data']['currency_name'];
                                        ?>
                                    </td>
                                    <td colspan="5" class="text-right">E. & O.E</td>
                                </tr>
                                <!-- <tr>
                                                                                                            <td colspan="5"></td>
                                                                                                            <td colspan="5">
                                                                                                                <p class="font-bold">Company’s Bank Details</p>
                                                                                                                <p>Bank Name :</p>
                                                                                                                <p>A/c No. :</p>
                                                                                                                <p>Branch & IFS Code :</p>
                                                                                                            </td>
                                                                                                        </tr> -->
                                <tr>
                                    <td colspan="6">
                                        <p>Remarks: <?= $onePoList['remarks'] ?></p>
                                        <p>Created By: <b><?php
                                                            echo getCreatedByUser($onePoList['created_by']);
                                                            ?></b></p>
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
?>