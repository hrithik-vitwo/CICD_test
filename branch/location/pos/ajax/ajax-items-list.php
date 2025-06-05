<?php
require_once("../../../../app/v1/connection-branch-admin.php");
// header("Content-Type: application/json");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
if ($_GET['act'] === "itemsList") {
    $itemId = $_GET['itemId'];
    $items = $_GET['items'];
    $type = $_GET['type'];

    $value = base64_decode($_GET['valueId']);

    // itemQty value was ?? 0 and now changed to ?? 1

    $itemQty = $items['qty'] ?? 1;
    $totalDiscount = $items['totalDiscount'] ?? 0;
    $itemTotalDiscount = $items['itemTotalDiscount'] ?? 0;
    $tolerance = $items['tolerance'] ? $items['tolerance'] : 0;

    $getItemObj = $ItemsObj->getItemById($itemId);
    $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];

    // console('$getItemObj');
    // console($getItemObj);
    $company = $BranchSoObj->fetchCompanyDetails()['data'];
    $fetchCurrency = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data'];
    $currencyIcon = $fetchCurrency['currency_icon'];
    $currencyName = $fetchCurrency['currency_name'];

    $randCode = $getItemObj['data']['itemId'] . rand(00000, 99999);

    // print_r($getItemObj);

    $itemPriceObj = $BranchSoObj->fetchBranchSoItemPriceDetails($getItemObj['data']['itemCode'])['data'][0];
    $goodsType = $getItemObj['data']['goodsType'];
    // $itemUnitPrice = $itemPriceObj['ItemPrice'] ?? 0;

    $itemUnitPrice = 0;
    if ($type === "?quotation_to_so" || $type === "?quotation") {
        $itemUnitPrice = $items['unitPrice'] ?? 0;
    } else {
        $itemUnitPrice = $getItemSummaryObj['itemPrice'] ?? 0;
    }

    // $itemMaxDiscount = $itemPriceObj['ItemMaxDiscount'] ?? 0;
    $itemMaxDiscount = $getItemSummaryObj['itemMaxDiscount'] ?? 0;

    $hsnInfo = $BranchSoObj->fetchHsnDetails($getItemObj['data']['hsnCode'])['data'][0];
    $itemTaxPercentage = $hsnInfo['taxPercentage'];

    // $itemTotalTax = ($itemUnitPrice * $itemTaxPercentage) / 100;
    $itemTotalTax = $items['totalTax'] ?? 0;
    // $itemTotalPrice = ($itemUnitPrice + $itemTotalTax) * ($itemQty ?? 0);
    $itemTotalPrice = $items['totalPrice'] ?? 0;
    $itemBasePrice = $itemUnitPrice * ($itemQty ?? 0);

    $itemStocks = $BranchSoObj->deliveryCreateItemQty($itemId)['sumOfBatches'];
    
?>
    <tr class="rowDel itemRow" data-row="<?= $randCode ?>" data-id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemDesc]" value="<?= $getItemObj['data']['itemDesc'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][parentGlId]" value="<?= $getItemObj['data']['parentGlId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][goodsType]" value="<?= $getItemObj['data']['goodsType'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][goodsMainPrice]" value="<?= $getItemSummaryObj['movingWeightedPrice']?>">
        <input type="hidden" name="listItem[<?= $randCode ?>][itemStocks]" value="<?= $itemStocks ?>">
        <!-- itemCode & itemName 👇🏾 -->
        <td style="width: 35%;">
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            
            <p class="pre-wrap font-bold my-2 text-xs"><?= $getItemObj['data']['itemName'] ?></p>
            <span style="font-size: 10px; font-style: italic;"><?= $getItemObj['data']['itemCode'] ?></span>
        </td>
        <!-- item unit price 👇🏾 -->
        <td class="inp-td pos-input-td">
            <div class="d-flex justify-content-center">
                <span class="rupee-symbol currency-symbol pr-1"><?= $currencyName ?> </span>
                <input type="hidden" name="listItem[<?= $randCode ?>][unitPrice]" value="<?= $itemUnitPrice ?>" class="inp-design full-width-center originalItemUnitPriceInp" id="originalItemUnitPriceInp_<?= $randCode ?>">
                <!-- this value should be inserted in DB 👇🏾 -->
                <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" value="<?=inputValue($itemUnitPrice) ?>" class="inp-design form-control text-right full-width-center originalChangeItemUnitPriceInp" id="originalChangeItemUnitPriceInp_<?= $randCode ?>_<?= $itemId ?>">
                <input type="hidden" name="listItem[<?= $randCode ?>][itemTargetPrice]" value="<?= inputValue($itemUnitPrice) ?>" class="inp-design form-control text-right full-width-center originalChangeItemUnitPriceInp" id="originalChangeItemUnitPriceInp_<?= $randCode ?>_<?= $itemId ?>">

            </div>
        </td>
        <!-- item qty 👇🏾 -->
        <td class="inp-td pos-input-td text-center">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-primary btn-sm text-sm decrement-btn itemDecrementBtn" id="itemDecrementBtn_<?= $randCode ?>_<?= $itemId ?>">-</button>
                <input type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= inputQuantity($itemQty) ?>" class="inp-design form-control text-center full-width itemQty itemQtyWidth inputQuantityClass" id="itemQty_<?= $randCode ?>_<?= $itemId ?>">
                <!-- <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?> -->
                <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $getItemObj['data']['baseUnitMeasure'] ?>">
                <button type="button" class="btn btn-primary btn-sm text-sm increment-btn itemIncrementBtn" id="itemIncrementBtn_<?= $randCode ?>_<?= $itemId ?>">+</button>
            </div>
            <span class="text-danger qtyMsg error-qty-msg" id="qtyMsg_<?= $randCode ?>"></span>
        </td>
        <!-- item total price 👇🏾 -->
        <td class="text-right">
            <input type="hidden" name="listItem[<?= $randCode ?>][totalPrice]" value="<?= inputValue($itemTotalPrice) ?>" class="form-control full-width-center itemTotalPrice" id="itemTotalPrice_<?= $randCode ?>" readonly>
            <div class="text-success">
                <span class="rupee-symbol currency-symbol"><?= $currencyName ?></span>
                <span class="font-bold itemTotalPrice1" id="itemTotalPrice1_<?= $randCode ?>">
                    <?= inputValue($itemTotalPrice) ?>
                </span>
            </div>
        </td>

        <!-- item details modal -->
        <td class="action-flex-btn">
            <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                <i id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="statusItemBtn fa fa-cog"></i>
            </button>

            <button type="button" class="btn btn-danger delItemBtn">
                <i class="fa fa-minus" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="delItemBtn mx-1 fa fa-minus"></i>
            </button>

            <div class="modal modal-left left-item-modal fade" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= $getItemObj['data']['itemName'] ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- <h6 class="modal-title">Remaining Qty: <span class="mainQty" id="mainQty_<?= $randCode ?>">1</span> <span class="error" id="mainQtymsg_<?= $randCode ?>"></span></h6> -->
                            <h6 class="modal-title">Total Amount: <span class="totalItemAmountModal" id="totalItemAmountModal_<?= $randCode ?>">1</span></h6>
                            <div class="row">
                                <input class="form-control full-width randClass" id="randClass_<?= $randCode ?>" type="hidden" value="<?= $randCode ?>">

                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>Discount (%)</label>
                                    <div class="d-flex">
                                        <span class="rupee-symbol symbol pr-1"><?= '%' ?> </span>
                                        <input type="text" name="listItem[<?= $randCode ?>][totalDiscount]" value="<?= $totalDiscount ?>" class="form-control itemDiscount" id="itemDiscount_<?= $randCode ?>_<?= $itemId ?>">
                                        <small class="maxLimitStyle" style="display: none;">Max <br><?php if ($itemMaxDiscount == 0) { ?> <span class="text-danger">0</span> <?php } else { ?> <strong class="itemMaxDiscount" id="itemMaxDiscount_<?= $randCode ?>"><?= $itemMaxDiscount ?></strong> <?php } ?>%</small>
                                    </div>
                                    <div style="font-size: .5em; display: none;" class="mt-2 text-dark itemSpecialDiscount" id="itemSpecialDiscount_<?= $randCode ?>"></div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>Discount Amt</label>
                                    <div class="d-flex">
                                        <span class="rupee-symbol currency-symbol pr-1"><?= $currencyName ?> </span>
                                        <input type="hidden" value="<?= number_format($itemTotalDiscount, 2) ?>" name="listItem[<?= $randCode ?>][itemTotalDiscount]" class="form-control itemTotalDiscountHidden" id="itemTotalDiscountHidden_<?= $randCode ?>">
                                        <input type="text" value="<?= number_format($itemTotalDiscount, 2) ?>" name="listItem[<?= $randCode ?>][itemTotalDiscount1]" class="form-control itemTotalDiscount1" id="itemTotalDiscount1_<?= $randCode ?>_<?= $itemId ?>">
                                        <span class="itemTotalDiscount" style="display: none;" id="itemTotalDiscount_<?= $randCode ?>"><?= number_format($itemTotalDiscount, 2) ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>Tax Percentage</label>
                                    <div class="d-flex">
                                        <input class="form-control itemTax" id="itemTax_<?= $randCode ?>" type="hidden" name="listItem[<?= $randCode ?>][tax]" value="<?= $itemTaxPercentage ?>">
                                        <?= $hsnInfo['taxPercentage'] ?>%
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>Tax Amount</label>
                                    <div class="d-flex">
                                        <input type="hidden" name="listItem[<?= $randCode ?>][itemTotalTax1]" value="<?= number_format($itemTotalTax, 2) ?>" class="form-control full-width-center itemTotalTax1" id="itemTotalTax1_<?= $randCode ?>" readonly>
                                        <span class="rupee-symbol currency-symbol"><?= $currencyName ?></span>
                                        <span class="itemTotalTax" id="itemTotalTax_<?= $randCode ?>"> <?= number_format($itemTotalTax, 2) ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>Base Amount</label>
                                    <div class="d-flex">
                                        <input type="hidden" name="listItem[<?= $randCode ?>][baseAmount]" value="<?= number_format($itemBasePrice, 2) ?>" class="form-control full-width-center itemBaseAmountInp" id="itemBaseAmountInp_<?= $randCode ?>">
                                        <span class="itemBaseAmountSpan" id="itemBaseAmountSpan_<?= $randCode ?>"><?= number_format($itemBasePrice, 2) ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>HSN Code</label>
                                    <div class="d-flex">
                                        <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][hsnCode]" value="<?= $getItemObj['data']['hsnCode'] ?>">
                                        <?= $getItemObj['data']['hsnCode'] ?>
                                        <i class="fa fa-info-circle" style="cursor: pointer;" title="<?= $hsnInfo['hsnDescription'] ?>"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-fixed">
                            <button type="button" class="btn btn-primary w-100" data-dismiss="modal">Save & Close</button>
                        </div>
                    </div>
                </div>
            </div>
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