<?php
require_once("../../../../app/v1/connection-branch-admin.php");
// require_once("../../../../app/v1/functions/branch/func-customers-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

if (isset($_GET["customer-list"])) {
    $branchSoObj = new BranchSo();
    $customerList = $branchSoObj->fetchCustomerList()['data'];
    http_response_code(200);
    $requestUniqueId = time() . rand(9999, 1000);

?>
    <div class="card collect-payment-card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <select name="" class="form-control" id="customerSelect_<?= $requestUniqueId ?>">
                        <option value="">Select Customer</option>
                        <?php foreach ($customerList as $customer) { ?>
                            <option value="<?= $customer['customer_id'] ?>"><?= $customer['trade_name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-4">
                    <div class="totalamount">
                        <p class="text-xs"> Total Invoice Amount</p>
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="totalInvAmt">0</span></p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="totaldueamount">
                        <p class="text-xs">Current Due Amount</p>
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="totalDueAmt">0</span></p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="totaloverdue">
                        <p class="text-xs">Overdue Amount</p>
                        <p class="text-xs font-bold rupee-symbol">₹ <span class="totalOverDueAmt">0</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <p class="">Invoice List</p>
    <div class="card">
        <div class="card-body p-0" id="customerInvList_<?= $requestUniqueId ?>" style="overflow-x: auto;"></div>
    </div>


    <script>
        $(document).on("change", "#customerSelect_<?= $requestUniqueId ?>", function() {
            let customerSelect = $(this).val();
            console.log("customerSelected", customerSelect)
            $.ajax({
                type: "POST",
                url: `<?= BASE_URL ?>branch/location/ajaxs/so/ajax-invoice-customer-list.php`,
                data: {
                    customerSelect
                },
                beforeSend: function() {
                    console.log("Loading Invoices...");
                },
                success: function(response) {
                    $("#customerInvList_<?= $requestUniqueId ?>").html(response);
                    calculateDueAmt();
                }
            });
        });
    </script>
    
<?php
} elseif ($_GET["customer-inv-list"]) {
}

?>