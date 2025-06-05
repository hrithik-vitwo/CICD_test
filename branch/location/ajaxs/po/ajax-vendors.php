<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-vendors-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$VendorsObj = new VendorController();
if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
    //GET REQUEST
    
    $getAllVendorsObj = $VendorsObj->getAllDataVendor();

    if ($getAllVendorsObj["status"] == "success") {
        echo '<option value="">Select Vendor </option>';
        foreach ($getAllVendorsObj["data"] as $oneVendors) {
?>
            <option value="<?= $oneVendors["vendor_id"] ?>"><?= $oneVendors["trade_name"] ?>(<?= $oneVendors['vendor_code'] ?>)</option>
<?php
        }
    } else {
        echo '<option value="">Select Vendor</option>';
    } 
} else {
    echo "Something wrong, try again!";
}
?>