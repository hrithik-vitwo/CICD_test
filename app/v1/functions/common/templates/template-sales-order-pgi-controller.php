<?php
class TemplateSalesOrderPgiController
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
    public function printSalesOrderPgi($pgiId = 0, $templateId = 0, $redirectUrl = "")
    {
        $branchSoObj = new BranchSo();

        $oneSoList = queryGet("SELECT * FROM `erp_branch_sales_order_delivery_pgi` WHERE so_delivery_pgi_id =$pgiId")['data'];
        $countryCode = $_SESSION["logedBranchAdminInfo"]["companyCountry"];
        $components = getLebels($countryCode)['data'];
        $components = json_decode($components, true);
        $taxName = getTaxName($countryCode)['data'];
        $taxComponents = json_decode($oneSoList['taxComponents'], true);

        $country_fields = json_decode(getLebels($countryCode)['data']);

        $taxNumber = $country_fields->fields->taxNumber ?? null;
        $company_if_num = $country_fields->fields->company_if_num ?? null;
        $taxidNumber = $country_fields->fields->taxidNumber ?? null;
        $businessID = $country_fields->fields->businessID ?? null;
        $taxStatus = $country_fields->fields->taxStatus ?? null;
        $businessTaxID = $country_fields->fields->businessTaxID ?? null;
        $BankIdCode = $country_fields->fields->BankIdCode ?? null;
        $place_of_supply = $country_fields->place_of_supply ?? null;


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
        $customerDetails = $branchSoObj->fetchCustomerDetails($oneSoList['customer_id'])['data'][0];
        $customerId = $oneSoList['customer_id'];
        $customerStateQry = queryGet("SELECT * FROM `erp_customer_address` where `customer_id`='$customerId'");
        $customerStatedata = $customerStateQry['data'];

?>

        <div class="printable-view">
            <h3 class="h3-title text-center font-bold text-sm mb-4">Sales Order PGI</h3>
            <table class="classic-view table-bordered">
                <tbody>
                    <tr>
                        <td colspan="3" class="border-right">
                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                            <p class="font-bold"><?= $companyData['company_name'] ?></p>
                            <p><?= $companyData['location_building_no'] ?>, <?= $companyData['location_flat_no'] ?></p>
                            <p><?= $companyData['location'] ?>, <?= $companyData['location_street_name'] ?>, <?= $companyData['location_pin_code'] ?></p>
                            <p><?= $companyData['location_city'] ?>, <?= $companyData['location_district'] ?></p>
                            <p><?= $companyData['location_state'] ?></p>
                            <?php if($countryCode == '103') {?>
                            <p>GSTIN/UIN: <?= $companyData['branch_gstin'] ?></p>
                            <?php }
                             else { ?>

                            <p><?=$businessTaxID?>: <?= $companyData['branch_gstin'] ?? "-"?></p>

                            <?php } ?>

                            <p>Company’s <?=$taxNumber?>: <?= $companyData['company_pan'] ?? "-" ?></p>
                        </td>
                        <td colspan="2">
                            <p>PGI Number</p>
                            <p class="font-bold"><?= $oneSoList['pgi_no'] ?></p>
                        </td>
                        <td colspan="3">
                            <p>Dated</p>
                            <p class="font-bold"><?= formatDateORDateTime($oneSoList['pgiDate']) ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="border-right">
                            <p>Buyer (Bill to)</p>
                            <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                            <p><?= $oneSoList['customer_billing_address'] ?></p>
                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $customerDetails['customer_gstin'] ? $customerDetails['customer_gstin'] : "--" ?></p><?php } ?>
                                            <?php if($countryCode == '103') { ?>
                                            <p>State Name : <?= fetchStateNameByGstin($customerDetails['customer_gstin']) ?>, Code : <?= substr($customerDetails['customer_gstin'], 0, 2); ?></p>
                                            <?php } 
                                            else { ?>
                                            <p>State Name : <?= $customerStatedata['customer_address_state']?></p>
                                            <?php }?>
                            <!-- <p>State Name : Maharashtra, Code : 27</p> -->
                        </td>
                        <td colspan="5" class="border-right">
                            <p>Consignee (Ship to)</p>
                            <p class="font-bold"><?= $customerDetails['trade_name'] ?></p>
                            <p><?= $oneSoList['customer_shipping_address'] ?></p>
                            <?php if ($components['fields']['businessTaxID'] != null) { ?>
                                <p><?= $components['fields']['businessTaxID'] ?>: <?= $customerDetails['customer_gstin'] ? $customerDetails['customer_gstin'] : "--" ?></p><?php } ?>
                            <?php if($countryCode == '103') { ?>
                                            <p>State Name : <?= fetchStateNameByGstin($customerDetails['customer_gstin']) ?>, Code : <?= substr($customerDetails['customer_gstin'], 0, 2); ?></p>
                                            <?php } 
                                            else { ?>
                                            <p>State Name : <?= $customerStatedata['customer_address_state']?></p>
                                            <?php }?>
                                            <?php if($components['place_of_supply']==1){?>
                                                <p>Place of Supply : <?= fetchStateNameByGstin($customerDetails['customer_gstin']) ?></p>
                                           <?php }?>
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
                    $itemDetails = $branchSoObj->fetchBranchSoDeliveryItemsPgi($oneSoList['so_delivery_pgi_id'])['data'];
                    // console($itemDetails);

                    $batchSql = "SELECT LOG.refNumber AS refNo, LOG.logRef AS batch, LOG.storageLocationId,strloc.storage_location_name, LOG.storageType, warehouse.warehouse_name, items.itemCode, items.itemName, LOG.itemQty AS qty FROM erp_inventory_stocks_log AS LOG LEFT JOIN erp_branch_sales_order_delivery_pgi AS pgi ON LOG.refNumber = pgi.pgi_no LEFT JOIN erp_branch_sales_order_delivery_items_pgi AS items ON pgi.so_delivery_pgi_id = items.so_delivery_pgi_id LEFT JOIN erp_storage_location AS strloc ON strloc.storage_location_id = LOG.storageLocationId LEFT JOIN erp_storage_warehouse AS warehouse ON warehouse.warehouse_id = strloc.warehouse_id WHERE LOG.companyId = $this->company_id AND LOG.branchId = $this->branch_id AND LOG.locationId = $this->location_id  AND items.inventory_item_id =LOG.itemId AND LOG.refNumber = '" . $oneSoList['pgi_no'] . "' AND LOG.itemQty > 0 GROUP BY LOG.refNumber, LOG.logRef, LOG.storageLocationId, LOG.storageType, items.itemCode, items.itemName, LOG.itemQty;";
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



                    <!-- <tr>
                        <td colspan="7" class="text-right font-bold">
                            <?= $oneSoList['totalAmount'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <p>Amount Chargeable (in words)</p>
                            <p class="font-bold"><?= number_to_words_indian_rupees($oneSoList['totalAmount']); ?> ONLY</p>
                        </td>
                        <td colspan="5" class="text-right">E. & O.E</td>
                    </tr> -->

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
}
