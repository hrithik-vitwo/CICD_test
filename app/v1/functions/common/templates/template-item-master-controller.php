<?php
include_once("../../../../../app/v1/functions/branch/func-goods-controller.php");
include_once("../../../../../app/v1/functions/branch/func-bom-controller.php");
include_once("../../../../../app/v1/functions/company/func-ChartOfAccounts.php");
include_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");

class TemplateItemController
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

    public function printItemPreview($itemId = 0)
    {
        global $companyCountry;
        $componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
        $BranchPoObj = new BranchPo();
        $dbObj = new Database();

        $cond = 'AND itemId ="' . $itemId . '"';

        $sql_list = "SELECT * FROM `" . ERP_INVENTORY_ITEMS . "` WHERE 1 " . $cond . "  AND`branch`=$this->branch_id AND `location_id`=$this->location_id AND `company_id`=$this->company_id " . $sts . "  ORDER BY itemId  ";
        $qry_list = $dbObj->queryGet($sql_list);

        $data = $qry_list['data'];


        $company_id = $data['company_id'];
        $branch_id = $data['branch_id'];
        $location_id = $data['location_id'];

        // console($qry_list);
        // console($qry_list);
        // exit();
        // exit();
        // added from main good page
        $itemId = $data['itemId'];
        $itemCode = $data['itemCode'];

        $itemName = $data['itemName'];

        $netWeight = $data['netWeight'];

        $volume = $data['volume'];

        $goodsType = $data['goodsType'];

        $grossWeight = $data['grossWeight'];

        $buom_id = $data['baseUnitMeasure'];
        $auom_id = $data['issueUnitMeasure'];

        $buom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$buom_id ");
        $buom = $buom_sql['data']['uomName'];

        $service_unit_sql =  queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`='" . $data['service_unit'] . "' ");
        //  console($buom);
        $auom_sql = queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomId`=$auom_id ");
        $auom = $buom_sql['data']['uomName'];


        $goodTypeId = $data['goodsType'];
        $type_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_TYPES . "` WHERE `goodTypeId`=$goodTypeId ");
        $type_name = $type_sql['data']['goodTypeName'] ? $type_sql['data']['goodTypeName'] : '-';



        $goodGroupId = $data['goodsGroup'];
        $group_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_MASTR_GOOD_GROUPS . "` WHERE `goodGroupId`=$goodGroupId ");
        $group_name = $group_sql['data']['goodGroupName'] ? $group_sql['data']['goodGroupName'] : '-';

        $purchaseGroupId = $data['purchaseGroup'];
        $purchase_group_sql = queryGet("SELECT * FROM `erp_inventory_mstr_purchase_groups` WHERE `purchaseGroupId` = $purchaseGroupId ");
        $purchase_group = isset($purchase_group_sql['data']['purchaseGroupName']) ? $purchase_group_sql['data']['purchaseGroupName'] : '-';


        $summary_sql = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`=$itemId");
        $mwp = $summary_sql['data']['movingWeightedPrice'];
        $val_class = $summary_sql['data']['priceType'] ? $summary_sql['data']['priceType'] : '-';
        $min_stock = $summary_sql['data']['min_stock'] ? $summary_sql['data']['min_stock'] : '-';
        $max_stock = $summary_sql['data']['max_stock'] ? $summary_sql['data']['max_stock'] : '-';


        $gldetails = getChartOfAccountsDataDetails($data['parentGlId'])['data'];
        $glName = $gldetails['gl_label'];
        $glCode = $gldetails['gl_code'];
        $item_id = $data['itemId'];
        $storage_sql = queryGet("SELECT * FROM `" . ERP_INVENTORY_STORAGE . "` WHERE `item_id`=$item_id AND `location_id`=$location_id");
        $storage_data = $storage_sql['data'];
?>

        <div class="tab-pane" id="classic-view<?= $data['itemId'] ?>" role="tabpanel" aria-labelledby="profile-tab">
            <div class="card classic-view bg-transparent">
                <div class="card-body classic-view-so-table" style="overflow: auto;">
                    <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->
                    <div class="printable-view">
                        <h3 class="h3-title text-center font-bold text-sm mb-4">Items</h3>
                        <?php
                        $companyData = $BranchPoObj->fetchCompanyDetailsById($this->company_id)['data'];

                        // console($data);

                        //console($companyData);

                        ?>
                        <table class="classic-view table-bordered">

                            <?php
                            if ($data['goodsType'] == 5 || $data['goodsType'] == 7 || $data['goodsType'] == 10) {

                                //console($data);

                            ?>

                                <tbody>
                                    <tr>
                                        <td class="border-right">
                                            <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                            <p><?= $companyData['company_flat_no'] ?>, <?= $companyData['company_building'] ?></p>
                                            <p><?= $companyData['company_district'] ?>,<?= $companyData['company_location'] ?>,<?= $companyData['company_pin'] ?></p>
                                            <p><?= $companyData['company_city'] ?></p>
                                            <!-- <p>GSTIN/UIN: <?= $companyData['company_name'] ?></p> -->
                                            <p>Company’s <?=$componentsjsn['fields']['businessTaxID']?>: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name :<?= $companyData['company_state'] ?></p>
                                            <!-- <p>E-Mail : <?= $companyData['company_name'] ?></p>  -->
                                        </td>
                                        <td>
                                            <p class="font-bold"><?= $data['itemName'] ?></p>
                                            <p><?= $data['itemDesc'] ?></p>
                                        </td>
                                    </tr>
                                </tbody>




                                <!-- service-tab-pdf -->
                                <tbody>
                                    <tr>
                                        <th colspan="2" class="text-left">Service Details</th>
                                    </tr>
                                    <tr class="service-list">
                                        <th class="bg-transparent text-left">HSN</th>
                                        <td class="text-left border-left">
                                            <p><?= $data['hsnCode'] ?></p>
                                        </td>
                                    </tr>
                                    <tr class="service-list">
                                        <th class="bg-transparent text-left">GL Details</th>
                                        <td class="text-left border-left">
                                            <p><?= $glName ?> [<?= $glCode ?>]</p>
                                        </td>
                                    </tr>
                                    <tr class="service-list">
                                        <th class="bg-transparent text-left">TDS</th>
                                        <td class="text-left border-left">
                                            <p><?= $data['tds'] ?></p>
                                        </td>
                                    </tr>
                                    <tr class="service-list">
                                        <th class="bg-transparent text-left">TDS Percentage</th>
                                        <td class="text-left border-left">
                                            <p><?= $data['tds'] ?></p>
                                        </td>
                                    </tr>
                                    <tr class="service-list">
                                        <th class="bg-transparent text-left">Service Unit</th>
                                        <td class="text-left border-left">
                                            <p><?= $data['service_unit'] ?></p>
                                        </td>
                                    </tr>
                                    <tr class="service-list">
                                        <th class="bg-transparent text-left">Service Target Price</th>
                                        <td class="text-left border-left">
                                            <p>-</p>
                                        </td>
                                    </tr>
                                </tbody>

                            <?php

                            } else {

                            ?>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="border-right">
                                        <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                            <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                            <p><?= $companyData['company_flat_no'] ?>, <?= $companyData['company_building'] ?></p>
                                            <p><?= $companyData['company_district'] ?>,<?= $companyData['company_location'] ?>,<?= $companyData['company_pin'] ?></p>
                                            <p><?= $companyData['company_city'] ?></p>
                                            <!-- <p>GSTIN/UIN: <?= $companyData['company_name'] ?></p> -->
                                            <p>Company’s PAN: <?= $companyData['company_pan'] ?></p>
                                            <p>State Name :<?= $companyData['company_state'] ?></p>
                                            <!-- <p>E-Mail : <?= $companyData['company_name'] ?></p>  -->
                                        </td>

                                    </tr>
                                </tbody>
                                <tbody>
                                    <tr>
                                        <th class="bg-transparent text-left" colspan="7">Basic Details</th>
                                    </tr>
                                    <tr>
                                        <th>Item Name</th>
                                        <th colspan="2">Description</th>
                                        <th>Base UOM</th>
                                        <th>Alternate UOM</th>
                                        <th>HSN</th>
                                        <th>Moving Weighted Price</th>
                                    </tr>
                                    <tr>
                                        <td class="text-left">
                                            <p><?= $data['itemName'] ?></p>
                                        </td>
                                        <td class="text-left" colspan="2">
                                            <p><?= $data['itemDesc'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?php
                                                if ($data['goodsType'] == 5 || $data['goodsType'] == 7 || $data['goodsType'] == 10) {
                                                    echo $service_unit_sql['data']['uomName'];
                                                } else {
                                                    echo $buom;
                                                } ?> </p>
                                        </td>
                                        <td class="text-center">
                                            <p><?php
                                                if ($data['goodsType'] == 5 || $data['goodsType'] == 7 || $data['goodsType'] == 10) {
                                                    echo '-';
                                                } else {
                                                    echo $auom;
                                                } ?> </p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $data['hsnCode'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $mwp  ? decimalValuePreview($mwp) : '-'; ?></p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th colspan="4">Specification</th>
                                        <th colspan="3">Specification Details</th>
                                    </tr>
                                    <?php
                                    foreach ($select_spec['data'] as $specs_each) {
                                    ?>
                                        <tr>
                                            <td class="text-left" colspan="4">
                                                <p><?= $specs_each['specification'] ?></p>
                                            </td>
                                            <td class="text-left" colspan="3">
                                                <p><?= $specs_each['specification_detail'] ?></p>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    <tr>
                                        <th class="bg-transparent text-left" colspan="7">Storage Details</th>
                                    </tr>
                                    <tr>
                                        <th colspan="2">Storage Control</th>
                                        <th>Max Storage Period</th>
                                        <th colspan="2">Minimum Remain Self Life</th>
                                        <th>Minimum Stock</th>
                                        <th>Maximum Stock</th>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <p><?= $storage_data['storageControl'] ? $storage_data['storageControl']  : '-'; ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $storage_data['maxStoragePeriod'] ?  $storage_data['maxStoragePeriod']  : '-'; ?></p>
                                        </td>
                                        <td class="text-center" colspan="2">
                                            <p><?= $storage_data['minRemainSelfLife'] ? $storage_data['minRemainSelfLife']  : '-'; ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= decimalQuantityPreview($min_stock) ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= decimalQuantityPreview($max_stock) ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-transparent text-left" colspan="7">Specification Details</th>
                                    </tr>
                                    <tr>
                                        <th>Net Weight</th>
                                        <th>Gross Weight</th>
                                        <th>Width</th>
                                        <th>Height</th>
                                        <th>Length</th>
                                        <th>Volume In CM</th>
                                        <th>Volume In M</th>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <p><?= $data['netWeight'] . "  " . $data['weight_unit'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $data['grossWeight'] . "  " . $data['weight_unit'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $data['width'] . "  " . $data['measuring_unit'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $data['height'] . " " . $data['measuring_unit'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $data['length'] . "  " . $data['measuring_unit'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $data['volume'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $data['volumeCubeCm'] ?></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="bg-transparent text-left" colspan="7">Classification </th>
                                    </tr>
                                    <tr>
                                        <th colspan="3">Goods Type</th>
                                        <th>Group Type</th>
                                        <th colspan="2">Purchase Group Type</th>
                                        <th>Availability Check</th>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-left">
                                            <p><?= $type_name ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $group_name ?></p>
                                        </td>
                                        <td colspan="2" class="text-center">
                                            <p><?= $purchase_group ?> </p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $data['availabilityCheck']  ? $data['availabilityCheck'] : '-';  ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                            <?php
                            }

                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>


<?php
    }
}
?>