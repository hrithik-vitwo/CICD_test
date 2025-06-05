<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-vendors-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

$VendorObj = new VendorController();
if ($_GET['act'] === "vendorlist") {
    $vendorId = $_GET['vendorId'];
    $getVendorObj = $VendorObj->getDataVendorDetails($vendorId);
    $data = $getVendorObj['data'][0];
    //console($data);
    $vendor_bussiness =queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id` = '".$data['vendor_id']."' AND `vendor_business_primary_flag` = 1");

?>


    <div class="card po-vendor-details-view">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-code"><i class="fa fa-check"></i>&nbsp;<p>Code :&nbsp;</p>
                    <p> <?= $data['vendor_code'] ?></p>
                    <div class="divider"></div>
                </div>
                <?php if($companyCountry==103){?>
                <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-gstin"><i class="fa fa-check"></i>&nbsp;<p>GSTIN :&nbsp;</p>
                    <p> <?= $data['vendor_gstin'] ?></p>
                    <div class="divider"></div>
                </div>
                <?php } ?>
                <?php ?>
                <div class="col-lg-4 col-md-4 col-sm-12 display-flex customer-status"><i class="fa fa-check"></i>&nbsp;<p> Vendor Status :&nbsp;</p>
                    <p class="status"> <?= $data['vendor_status'] ?></p>
                </div>
                <input type="hidden" name="vendor_state" id="vendor_state_code" value="<?=$vendor_bussiness['data']['state_code'] ?>">
            </div> 
        </div>
    </div>

<?php
} else {
    echo "Something wrong, try again!";
}
?>