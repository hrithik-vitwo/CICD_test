<?php
require_once("../../../../app/v1/connection-branch-admin.php");
// header("Content-Type: application/json");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "itemGroupList") {
    $groupId = $_GET['groupId'] ?? 0;
    if ($groupId > 0) {
        $itemListObj = $BranchSoObj->fetchAllItemsByGroupWise($groupId);
    } else {
        $itemListObj = $BranchSoObj->fetchAllItemSummary();
    }
    $itemList = $itemListObj['data'];
    // console($itemListObj);
    if ($itemListObj['numRows'] > 0) {
        foreach ($itemList as $itemKey => $oneItem) {
            // console($oneItem);
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
        <div id="noDataFound" class="text-center py-2 bg-white">
            <img src="<?= BASE_URL ?>public/assets/gif/no-transaction.gif" width="150" alt="">
            <p>No Data Found</p>
        </div>
<?php }
} elseif ($_GET['act'] === "test") {
    echo "This is for the testing";
} else {
    echo "Something wrong, try again!";
}
?>