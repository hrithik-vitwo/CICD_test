<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "listItem") {
    $itemId = $_GET['itemId'];
    $getItemObj = $ItemsObj->getItemById($itemId);
    $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];

    $company = $BranchSoObj->fetchCompanyDetails()['data'];
    $currencyIcon = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data']['currency_icon'];

    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

    // print_r($getItemObj);

    $itemPriceObj = $BranchSoObj->fetchBranchSoItemPriceDetails($getItemObj['data']['itemCode'])['data'][0];

    // $itemUnitPrice = $itemPriceObj['ItemPrice'] ?? 0;
    $itemUnitPrice = $getItemSummaryObj['itemPrice'] ?? 0;

    // $itemMaxDiscount = $itemPriceObj['ItemMaxDiscount'] ?? 0;
    $itemMaxDiscount = $getItemSummaryObj['itemMaxDiscount'] ?? 0;

    $hsnInfo = $BranchSoObj->fetchHsnDetails($getItemObj['data']['hsnCode'])['data'][0];
    $itemTaxPercentage = $hsnInfo['taxPercentage'];

    $itemTotalTax = ($itemUnitPrice * $itemTaxPercentage) / 100;
    $itemTotalPrice = $itemUnitPrice + $itemTotalTax;
    $itemBasePrice = $itemUnitPrice * 1;

?>
    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][parentGlId]" value="<?= $getItemObj['data']['parentGlId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemDesc]" value="<?= $getItemObj['data']['itemDesc'] ?>">
        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            <?= $getItemObj['data']['itemCode'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
            <?= $getItemObj['data']['itemName'] ?> <i style="cursor: pointer;" class="fa fa-pen itemRemarksIcon" id="itemRemarksIcon_<?= $getItemObj['data']['itemId'] ?>"></i>
            <textarea style="display:none" name="listItem[<?= $randCode ?>][itemRemarks]" class="form-control itemRemarks" id="itemRemarks_<?= $getItemObj['data']['itemId'] ?>" cols="30" rows="10" placeholder="Remarks"></textarea>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][hsnCode]" value="<?= $getItemObj['data']['hsnCode'] ?>">
            <?= $getItemObj['data']['hsnCode'] ?> <i class="fa fa-info-circle" style="cursor: pointer;" title="<?= $hsnInfo['hsnDescription'] ?>"></i>
        </td>
        <td class="inp-td">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1">#</span>
                <input step="0.01" type="number" name="listItem[<?= $randCode ?>][qty]" value="1" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>">
                <?= $getItemObj['data']['service_unit'] ?>
                <input type="hidden" name="listItem[<?= $randCode ?>][service_unit]" value="<?= $getItemObj['data']['service_unit'] ?>">
            </div>
            <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
        </td>
        <td class="inp-td" style="min-width: 40px !important;">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1"><?= $currencyIcon ?> </span>
                <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" value="<?= $getItemSummaryObj['itemPrice'] ?>" class="inp-design full-width-center itemUnitPrice" id="itemUnitPrice_<?= $randCode ?>">
            </div>
        </td>
        <td>
            <input type="hidden" name="listItem[<?= $randCode ?>][baseAmount]" value="<?=$itemBasePrice?>" class="form-control full-width-center itemBaseAmountInp" id="itemBaseAmountInp_<?= $randCode ?>">
            <span class="itemBaseAmountSpan" id="itemBaseAmountSpan_<?= $randCode ?>"><?=$itemBasePrice?></span>
        </td>
        <td class="inp-td" style="max-width: 20px !important;">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1"><?= '%' ?> </span>
                <input type="text" name="listItem[<?= $randCode ?>][totalDiscount]" value="0" class="inp-design full-width-center itemDiscount" id="itemDiscount_<?= $randCode ?>">
                <small class="maxLimitStyle" style="display: none;">Max <br><?php if ($itemMaxDiscount == 0) { ?> <span class="text-danger">0</span> <?php } else { ?> <strong class="itemMaxDiscount" id="itemMaxDiscount_<?= $randCode ?>"><?= $itemMaxDiscount ?></strong> <?php } ?>%</small>
            </div>
            <div style="font-size: .5em; display: none;" class="mt-2 text-dark itemSpecialDiscount" id="itemSpecialDiscount_<?= $randCode ?>"></div>
        </td>
        <td class="inp-td" style="min-width: 20px !important;max-width: 100px !important;">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1"><?= $currencyIcon ?> </span>
                <input type="text" value="0" name="listItem[<?= $randCode ?>][itemTotalDiscount1]" class="inp-design full-width-center itemTotalDiscount1" id="itemTotalDiscount1_<?= $randCode ?>">
                <span class="itemTotalDiscount" style="display: none;" id="itemTotalDiscount_<?= $randCode ?>">0</span>
            </div>
        </td>
        <td>
            <input class="form-control itemTax" id="itemTax_<?= $randCode ?>" type="hidden" name="listItem[<?= $randCode ?>][tax]" value="<?= $itemTaxPercentage ?>">
            <?= $hsnInfo['taxPercentage'] ?>%
        </td>
        <td>
            <input type="hidden" name="listItem[<?= $randCode ?>][itemTotalTax1]" value="<?= $itemTotalTax ?>" class="form-control full-width-center itemTotalTax1" id="itemTotalTax1_<?= $randCode ?>" readonly>
            <span class="rupee-symbol"><?= $currencyIcon ?></span>
            <span class="itemTotalTax" id="itemTotalTax_<?= $randCode ?>"><?= $itemTotalTax ?></span>
        </td>
        <td>
            <input type="hidden" name="listItem[<?= $randCode ?>][totalPrice]" value="<?= $itemTotalPrice ?>" class="form-control full-width-center itemTotalPrice" id="itemTotalPrice_<?= $randCode ?>" readonly>
            <div class="text-success font-weight-bold">
                <span class="rupee-symbol"><?= $currencyIcon ?></span>
                <span class="itemTotalPrice1" id="itemTotalPrice1_<?= $randCode ?>">
                    <?= $itemTotalPrice ?>
                </span>
            </div>
        </td>
        <td class="action-flex-btn">
            <button type="button" class="btn btn-danger delItemBtn">
                <i class="fa fa-minus" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="delItemBtn mx-1 fa fa-minus"></i>
            </button>
        </td>
    </tr>

<?php
} elseif ($_GET['itemId'] === "ss") {
    $price = 20;
    $qty = $_GET['id'];
    echo $qty * $price;
} elseif ($_GET['act'] === "approvalTab") {
    $soId = $_GET['soId'];
    $upd = "UPDATE `" . ERP_BRANCH_SALES_ORDER . "` SET approvalStatus=11 WHERE so_id='" . $soId . "'";
    if ($dbCon->query($upd)) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "Something wrong, try again!";
}
?>