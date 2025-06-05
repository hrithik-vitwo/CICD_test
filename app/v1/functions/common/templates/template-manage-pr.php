<?php
include_once("../../../../../app/v1/connection-branch-admin.php");
include_once("../../../../../app/v1/functions/branch/func-branch-pr-controller.php");
include_once("../../../../../app/v1/functions/branch/func-items-controller.php");
include_once("../../../../../app/v1/functions/branch/func-brunch-po-controller.php");
class TemplatePr
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

    public function printManagePr($pr_id = 0)
    {
        global $companyCountry;
        $componentsjsn = json_decode(getLebels($companyCountry)['data'], true);
        $BranchPoObj = new BranchPo();
        $BranchPrObj = new BranchPr();

        $ItemsObj = new ItemsController();

        $dbObj = new Database();
        $sts = "";

        $cond = "purchaseRequestId=$pr_id";
        $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_REQUEST . "` WHERE  " . $cond . "  AND company_id='" . $this->company_id . "' AND branch_id='" . $this->branch_id . "' AND location_id='" . $this->location_id . "'   ORDER BY purchaseRequestId  ";
        $qry_list = $dbObj->queryGet($sql_list);
        $onePrList = $qry_list['data'];

        $company_id = $onePrList['company_id'];
        $branch_id = $onePrList['branch_id'];
        $location_id = $onePrList['location_id'];

?>
        <div class="tab-pane" id="classic-view<?= $onePrList['prCode'] ?>" role="tabpanel" aria-labelledby="profile-tab">
            <div class="card classic-view bg-transparent">
                <div class="card-body classic-view-so-table" style="overflow: auto;">
                    <!-- <button type="button" class="btn btn-primary classic-view-btn float-right" id="printButton">Print Table</button> -->
                    <div class="printable-view">
                        <h3 class="h3-title text-center font-bold text-sm mb-4">Purchase Request</h3>

                        <?php

                        $companyData = $BranchPoObj->fetchCompanyDetailsById($this->company_id)['data'];
                        $itemDetails = $BranchPrObj->fetchBranchPrItems($onePrList['purchaseRequestId'])['data'];

                        //console($companyData);

                        ?>
                        <table class="classic-view table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <img style="max-width: 200px; background-color: #ccc; border-radius: 5px" src="<?= BUCKET_URL . "uploads/" . $company_id . "/profile/" . $companyData['company_logo'] ?>" alt="company logo">
                                        <p class="font-bold"><?= $companyData['company_name'] ?></p>
                                        <p><?= $companyData['company_flat_no'] ?>, <?= $companyData['company_building'] ?></p>
                                        <p><?= $companyData['company_district'] ?>,<?= $companyData['company_location'] ?>,<?= $companyData['company_pin'] ?></p>
                                        <p><?= $companyData['company_city'] ?></p>
                                        <p>Company’s <?=$componentsjsn['fields']['taxNumber']?>: <?= $companyData['company_pan'] ?></p>
                                        <p>State Name :<?= $companyData['company_state'] ?></p>
                                    </td>
                                    <td class="border-right-none">
                                        <p>Purchase Request Number</p>
                                        <p class="font-bold"><?= $onePrList['prCode'] ?></p>
                                    </td>
                                    <td class="border-left-none">
                                        <p>Dated</p>
                                        <p class="font-bold"><?= formatDateORDateTime($onePrList['pr_date']) ?></p>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th>Sl No.</th>
                                    <th>Particulars</th>
                                    <th>Quantity</th>
                                    <th>UOM</th>
                                    <th>Note</th>
                                </tr>
                                <?php
                                foreach ($itemDetails as $oneItemList) {

                                    //console($oneItemList)

                                ?>

                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">
                                            <p class="font-bold"><?= $oneItemList['itemName'] ?></p>
                                            <p class="text-italic"><?= $oneItemList['itemCode'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= decimalQuantityPreview($oneItemList['itemQuantity']) ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $oneItemList['uomName'] ?></p>
                                        </td>
                                        <td class="text-center">
                                            <p><?= $oneItemList['itemNote'] ?></p>
                                        </td>
                                    </tr>

                                <?php

                                }
                                ?>


                                <!-- <tr>
                                                      <td colspan="5">
                                                        <p>Amount Chargeable (in words)</p>
                                                        <p class="font-bold">ONE THOUSAND TWO HUNDRED AND SIXTY ONLY</p>
                                                      </td>
                                                      <td colspan="5" class="text-right">E. & O.E</td>
                                                    </tr> -->
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
                                    <td colspan="3">
                                        <p>Remarks:</p>
                                        <p>Created By: <b><?= getCreatedByUser($onePrList['created_by']) ?></b></p>
                                    </td>

                                    <td colspan="2" class="text-right border">
                                        <p>Authorised Signatory</p>
                                        <p>
                                        <p class="text-center sign-img">
                                            <img width="120" src="<?= COMP_STORAGE_URL ?>/profile/<?= $companyData['signature'] ?>" alt="">
                                        </p>
                                        (Signature of the Licencee or his Authorised Agent)
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
<?php

    }
}
