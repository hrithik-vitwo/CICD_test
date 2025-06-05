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
    $serachvalue = $_GET['searchValue'];
    $groupId = $_GET['groupId'] ?? 0;
    if ($groupId > 0) {
        $itemListObj = $BranchSoObj->fetchAllItemsByGroupWiseSearch($groupId, $serachvalue);
    } else {
        $itemListObj = $BranchSoObj->fetchAllItemSummarySearch($serachvalue);
    }
    $itemList = $itemListObj['data'];
   
    if ($itemListObj['numRows'] > 0) {
        foreach ($itemList as $itemKey => $oneItem) {
            $itemStocks = $BranchSoObj->deliveryCreateItemQty($oneItem['itemId'])['sumOfBatches'];
            
        ?>
            <input type="hidden" name="itemStocks" value="<?= $itemStocks ?>">
            <div class="col-md-3 col-sm-4 col-xs-4 px-0">
                <div class="text-xs oneItemCard p-3" id="oneItemCard_<?= $itemStocks ?>_<?= $oneItem['itemId'] ?>">
                    <?php 
                   $sqlqry = "SELECT `image_name`
FROM `erp_inventory_item_images` 
WHERE `location_id` = " . intval($oneItem['location_id']) . " 
  AND `branch_id` = " . intval($oneItem['branch']) . " 
  AND `company_id` = " . intval($oneItem['company_id']) . " 
  AND `item_id` = " . intval($oneItem['itemId']) . " 
LIMIT 1;";

$img = queryGet($sqlqry);
                    ?>
               <img 
    data-src="<?= BASE_URL ?>uploads/1/others/<?=$img['data']['image_name'] ?>" 
    alt="<?= $oneItem['itemName'] ?>"
    class="item-image lazy"
    width="100"
    height="100"
    loading="lazy"
    onerror="this.onerror=null; this.src='<?= BASE_URL ?>uploads/1/others/vitwo_default.jpg';"
/>
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
} else {
    echo "Something wrong, try again!";
}
?>