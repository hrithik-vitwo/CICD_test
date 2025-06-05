<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$customerSelect = $_POST['customerSelect'];

$BranchSoObj = new BranchSo();
$paymentLog = $BranchSoObj->fetchAllPaymentLogByCustomerId($customerSelect)['data'];
// console("payment logs");

if ($paymentLog != NULL) {
    foreach ($paymentLog as $log) {
        if ($log['advancedAmt'] != 0 && $log['advancedAmt'] >= 0) {


            $date = date_create($log['documentDate']);
            $documentDate = date_format($date, "d M, Y");
?>

            <style>
                .align-center {
                    align-items: center;
                }

                span.rupee-symbol {
                    font-family: system-ui;
                }

                .advancedAmtList {
                    overflow-x: hidden;
                }
            </style>
            <div class="row border align-center">
                <div class="col-md-6">
                    <h4 class="text-success font-bold text-lg"><span class="rupee-symbol">₹</span><?= decimalValuePreview($log['advancedAmt']) ?></h4>
                </div>
                <div class="col-md-6">
                    <div class="d-grid advance-list-cash">
                        <p class="text-right text-sm m-2 font-weight-bold"><?= $log['transactionId'] ?></p>
                        <p class="text-right text-xs m-2"><?= $documentDate ?></p>
                    </div>
                </div>
            </div>
    <?php
        }
    }
} else {
    ?>
    <span class="text-danger">Advanced Amount Not Found!</span>
<?php
}
?>