<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$bankId = $_GET['bankId'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    //GET REQUEST
    $bankDetails = queryGet("SELECT bank_name,ifsc_code,account_no,account_holder_name,bank_address FROM `" . ERP_ACC_BANK_CASH_ACCOUNTS . "` WHERE id='$bankId' ")['data'];

?>
    <div class="d-flex">
        <p>Bank Name :</p>
        <p class="font-bold" id="bankName"><?= $bankDetails['bank_name'] ?></p>
    </div>
    <div class="d-flex">
        <p>A/c No. :</p>
        <p class="font-bold" id="accountNo"><?= $bankDetails['account_no'] ?></p>
    </div>
    <div class="d-flex">
        <p>Branch & IFS Code :</p>
        <p class="font-bold" id="ifscCode"><?= $bankDetails['ifsc_code'] ?></p>
    </div>
<?php
} else {
    echo "Something wrong, try again!";
}

?>