<?php
include_once("../../app/v1/functions/branch/func-brunch-so-controller.php");
class TemplateDeliveryController
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

    public function printDelivery($delv_id = 0)
    {
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
                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                            <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
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
                            <p>GSTIN/UIN : <?= $customerDetails['customer_gstin'] ?></p>
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
                            LOG.companyId = " . $this->company_id . " AND LOG.branchId = " . $this->branch_id . " AND LOG.locationId = " . $this->location_id . " AND LOG.refNumber='" . $oneSoList['delivery_no'] . "' AND LOG.itemQty>0
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
                                <p><?= $onePgiItem['storageType'] ?></p>
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
