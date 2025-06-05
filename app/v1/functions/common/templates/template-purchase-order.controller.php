<?php
require_once("../../app/v1/connection-branch-admin.php");
require_once("../../app/v1/functions/branch/func-brunch-po-controller.php");
require_once("../../../../../app/v1/functions/branch/func-items-controller.php");

$dbObj = new Database();
$BranchPoObj = new BranchPo();
$ItemsObj = new ItemsController();
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
        $cond='AND po_id ="'.$poId.'"';

        $sql_list = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER . "` WHERE 1 " . $cond . "  AND`branch_id`=$branch_id AND `location_id`=$location_id AND `company_id`=$company_id " . $sts . "  ORDER BY po_id desc limit " . $GLOBALS['start'] . "," . $GLOBALS['show'] . " ";
        $qry_list = queryGet($sql_list);

        $onePoList=$qry_list['data'];
        ?>
        <div class="tab-pane fade" id="profile<?= $onePoList['po_number'] ?>" role="tabpanel" aria-labelledby="profile-tab">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12">
                                                                                            <div class="accordion accordion-flush customer-details-sells-order" id="accordionFlushCustDetails">
                                                                                                <div class="accordion-item customer-details">
                                                                                                    <h2 class="accordion-header" id="flush-headingOne">
                                                                                                        <button class="accordion-button active" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOnePo" aria-expanded="false" aria-controls="flush-collapseOne">
                                                                                                            <span>Vendor Details</span>
                                                                                                        </button>
                                                                                                    </h2>
                                                                                                    <div id="flush-collapseOnePo" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                                                                                        <div class="accordion-body cust-detsils-body">

                                                                                                            <div class="card">
                                                                                                                <div class="card-body">
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                            <?php
                                                                                                                            $vendorDetails = $BranchPoObj->fetchVendorDetails($onePoList['vendor_id'])['data'][0];
                                                                                                                            ?>
                                                                                                                            <div class="icon">
                                                                                                                                <i class="fa fa-hashtag"></i>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                            <span>Vendor Code</span>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                            <p>
                                                                                                                                <?= $vendorDetails['vendor_code'] ?>
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <hr>
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                            <div class="icon">
                                                                                                                                <i class="fa fa-user"></i>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                            <span>Vendor Name</span>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                            <p>
                                                                                                                                <?= $vendorDetails['trade_name'] ?>
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <hr>
                                                                                                                    <div class="row">
                                                                                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                                                                                            <div class="icon">
                                                                                                                                <i class="fa fa-file"></i>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-4 col-md-4 col-sm-4">
                                                                                                                            <span>GST</span>
                                                                                                                        </div>
                                                                                                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                                                                                                            <p>
                                                                                                                                <?= $vendorDetails['vendor_gstin'] ?>
                                                                                                                            </p>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

   <?php }
}
