<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-vendors-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$CustomersObj = new VendorController();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
    $getAllCustomersObj = $CustomersObj->getAllDataVendor();

    if ($getAllCustomersObj["status"] == "success") {
        echo '<option value="">Select Vendor </option>';
        foreach ($getAllCustomersObj["data"] as $oneCustomers) {
?>
            <option value="<?= $oneCustomers["vendor_id"] ?>"><?= $oneCustomers["trade_name"] ?></option>
<?php
        }
    } else {
        echo '<option value="">Items Type</option>';
    }
} else {
    echo "Something wrong, try again!";
}
?>