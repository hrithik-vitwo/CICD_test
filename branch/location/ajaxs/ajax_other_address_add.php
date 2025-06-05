<?php
require_once("../../../app/v1/connection-branch-admin.php");

// console($_REQUEST);

?>

<?php

$vendor_id = $_POST['vendor_id'];
$flatNo = $_POST['flatNo'];
$pinCode = $_POST['pinCode'];
$district = $_POST['district'];
$location = $_POST['location'];
$buildingNo = $_POST['buildingNo'];
$streetName = $_POST['streetName'];
$city = $_POST['city'];
$state = $_POST['state'];

$ins = "INSERT INTO `erp_vendor_bussiness_places` 
              SET
                 `vendor_id`='$vendor_id',
                 `vendor_business_primary_flag`='0',
                 `vendor_business_flat_no`='$flatNo',
                 `vendor_business_building_no`='$buildingNo',
                 `vendor_business_pin_code`='$pinCode',
                 `vendor_business_street_name`='$streetName',
                 `vendor_business_district`='$district',
                 `vendor_business_city`='$city',
                 `vendor_business_location`='$location',
                 `vendor_business_state`='$state'
  ";
if ($dbCon->query($ins)) {
  // echo "Inserted Successfull!";
  $lastId = $dbCon->insert_id;
?>
  <div class="row">
    <div class="col-md-12 mt-1" style="text-align: right;">
      <a href="javascript:void(0);" id="remove_<?= $lastId ?>" class="updateRemCF btn btn-danger my-2">
        <i class="fa fa-minus"></i>
      </a>
    </div>

    <div class="col-md-6">
      <div class="form-input">
        <label>Flat Number</label>
        <input type="text" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_flat_no]" class="form-control" id="vendor_business_flat_no" value="<?php echo $flatNo; ?>">
      </div>
      <div class="form-input">
        <label>Pin Code</label>
        <input type="text" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_pin_code]" class="form-control" id="vendor_business_pin_code" value="<?php echo $pinCode; ?>">
      </div>
      <div class="form-input">
        <label>District</label>
        <input type="text" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_district]" class="form-control" id="vendor_business_district" value="<?php echo $district; ?>">
      </div>
      <div class="form-input">
        <label>Location</label>
        <input type="text" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_location]" class="form-control" id="vendor_business_location" value="<?php echo $location; ?>">
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-input">
        <label>Building Number</label>
        <input type="text" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_building_no]" class="form-control" id="vendor_business_building_no" value="<?php echo $buildingNo; ?>">
      </div>

      <div class="form-input">
        <label>Street Name</label>
        <input type="text" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_street_name]" class="form-control" id="vendor_business_street_name" value="<?php echo $streetName; ?>">
      </div>

      <div class="form-input">
        <label>City</label>
        <input type="text" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_city]" class="form-control" id="vendor_business_city" value="<?php echo $city; ?>">
      </div>

      <div class="form-input">
        <label>State</label>
        <input type="text" name="vendorOtherAddress[<?= $oneAddress['vendor_business_id'] ?>][vendor_business_state]" class="form-control" id="vendor_business_state" value="<?php echo $state; ?>">
      </div>

    </div>
  </div>
<?php
} else {
  echo "Somthing went wrong!";
}
// echo "Inserted Successfull!";
?>