<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$CustomersObj = new CustomersController();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
    $customerId = $_GET['customerId'];
    $getAllCustomersObj = $CustomersObj->getAllDataCustomer();
    if ($getAllCustomersObj["status"] == "success") {
        echo '<option value="">Select Customer</option>';
        foreach ($getAllCustomersObj["data"] as $oneCustomers) {
?>
            <option <?php if ($oneCustomers["customer_id"] == $customerId) {
                        echo "selected";
                    } ?> value="<?= $oneCustomers["customer_id"] ?>"><?= $oneCustomers["trade_name"] ?></option>
    <?php } ?>  
<?php
    } else {
        echo '<option value="">Select Customer</option>';
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = $_POST['customerName'];
    $customerEmail = $_POST['customerEmail'];
    $customerPhone = $_POST['customerPhone'];

    $customerCode = time() . rand(0000, 9999);
    $ins = "INSERT INTO `" . ERP_CUSTOMER . "`
                SET
                    `company_id`='$company_id',
                    `company_branch_id`='$branch_id',
                    `location_id`='$location_id',
                    `customer_code`='$customerCode',
                    `trade_name`='$customerName',
                    `parentGlId`='502',
                    `customer_authorised_person_name`='$customerName',
                    `customer_authorised_person_email`='$customerEmail',
                    `customer_authorised_person_phone`='$customerPhone',
                    `customer_created_by`='" . $created_by . "',
                    `customer_updated_by`='" . $created_by . "',
                    `customer_status`='active'
    ";
    $insert = queryInsert($ins);
    echo json_encode($insert);
} else {
    echo "Something wrong, try again!";
}
?>