<?php
require_once("../../../../app/v1/connection-branch-admin.php");
// header("Content-Type: application/json");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "itemSearch") {
    $searchText = $_GET['searchText'] ?? 0;
    $itemListObj = $BranchSoObj->fetchAllItemSummarySearch($searchText);
    $itemList = $itemListObj['data'];
    if ($itemListObj['numRows'] > 0) {
        foreach ($itemList as $itemKey => $oneItem) {
            $itemStocks = $BranchSoObj->deliveryCreateItemQty($oneItem['itemId'])['sumOfBatches'];
?>
            <input type="hidden" name="itemStocks" value="<?= $itemStocks ?>">
            <div class="col-md-3 col-sm-4 col-xs-4 px-0">
                <div class="text-xs oneItemCard p-3" id="oneItemCard_<?= $itemStocks ?>_<?= $oneItem['itemId'] ?>">
                    <p><?= $oneItem['itemName'] ?></p>
                    <span class="text-xs"><?= $itemStocks ?></span>
                    <?php if ($itemStocks == 0 || $itemStocks <= 0) { ?>
                        <span class="text-danger">Out of stock</span>
                    <?php } ?>
                </div>
            </div>
        <?php
        }
    } else { ?>
        <span class="text-danger">Data Not Found.</span>
<?php }
} elseif ($_GET['act'] === "checkStock") {
    $enterQty = $_GET['enterQty'] ?? 0;
    $itemId = $_GET['itemId'] ?? 0;
    $itemStocks = $BranchSoObj->deliveryCreateItemQty($itemId)['sumOfBatches'];
    if (intval($enterQty) <= intval($itemStocks)) {
        echo "success";
    } else {
        echo "warning";
    }
} else {
    echo "Something wrong, try again!";
}
?>