<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
if ($_GET['act'] === "listItem") {
    $itemId = $_GET['itemId'];
    $getItemObj = $ItemsObj->getItemById($itemId);
    //   console($getItemObj);
    $itemCode = $getItemObj['data']['itemCode'];
    $lastPricesql = "SELECT * FROM `erp_branch_purchase_order_items`as po_item JOIN `erp_branch_purchase_order` as po ON po_item.`po_id`=po.po_id WHERE `location_id`=$location_id AND `itemCode`=$itemCode ORDER BY po_item.`po_item_id` DESC LIMIT 1";
    $ptype = $_GET['ptype'];

    $last = queryGet($lastPricesql);
    $lastRow = $last['data'] ?? "";
    $lastPrice = $lastRow['unitPrice'] ?? "0";

    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);
    $hsn = $getItemObj['data']['hsnCode'];
    $gstPercentage_sql = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode` = '" . $hsn . "'");
    // console($gstPercentage_sql);
    // console($randCode);
    if ($companyCountry == 103) {
        if ($ptype != 'international') {
            $gstAmount = ($gstPercentage_sql['data']['taxPercentage'] / 100) * $lastPrice;
            $gstPercentage = $gstPercentage_sql['data']['taxPercentage'];
        } else {
            $gstAmount = 0;
            $gstPercentage = 0;
        }
    } else {
        $gstAmount = ($gstPercentage_sql['data']['taxPercentage'] / 100) * $lastPrice;
        $gstPercentage = $gstPercentage_sql['data']['taxPercentage'];
    }
    $totalAmount = $lastPrice + $gstAmount;

    $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
    // console($companyCurrencyObj);
    $companyCurrencyData = $companyCurrencyObj["data"];

    $comp_currency = $companyCurrencyData["currency_name"];



?>
    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            <?= $getItemObj['data']['itemCode'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
            <?= $getItemObj['data']['itemName'] ?>
        </td>
        <td>
            <div class="flex-display">
                <input type="number" step="any" name="listItem[<?= $randCode ?>][qty]" value="<?=inputQuantity(1)?>" min="1" class="form-control full-width itemQty inputQuantityClass" id="itemQty_<?= $randCode ?>">
                <?php
                if ($getItemObj['data']['goodsType'] == 7) {
                ?>
                    <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['service_unit'])['data']['uomName'] ?>
                    <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['service_unit'])['data']['uomName'] ?>">


                <?php
                } else {
                ?>

                    <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                    <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                <?php
                }
                ?>
            </div>
        </td>
        <td>
            <input type="number" name="listItem[<?= $randCode ?>][unitPrice]" value="<?= inputValue($lastPrice) ?>" class="form-control full-width-center itemUnitPrice inputAmountClass" id="itemUnitPrice_<?= $randCode ?>" data-attr="<?= $randCode ?>">
            <div class="d-flex gap-2 my-1">
                <?= $comp_currency ?> <p id="local_unit_price_<?= $randCode ?>">0.00</p>
            </div>

            <input type="hidden" name="listItem[<?= $randCode ?>][unitPriceHidden]" value="<?= inputValue($lastPrice) ?>" id="ItemUnitPriceTdInputhidden_<?= $randCode ?>" class="form-control text-xs itemUnitPricehidden">


        </td>
        <!-- <td class="flex-display">
            <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
        </td> -->
        <td class="gstTD">
            <input type="number" name="listItem[<?= $randCode ?>][basePrice]" value="<?= inputValue($lastPrice) ?>" class="form-control full-width-center itemBasePrice" id="itemBasePrice_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>
            <div class="d-flex gap-2 my-1">
                <?= $comp_currency ?> <p id="local_base_price_<?= $randCode ?>">0.00</p>
            </div>


        </td>

        <td class="gstTD">
            <input type="number" name="listItem[<?= $randCode ?>][gst]" value="<?= inputValue($gstPercentage) ?>" class="form-control full-width-center gst" id="gst_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>
            <div class="d-flex gap-2 my-1">
                <?= $comp_currency ?> <p id="local_gst_<?= $randCode ?>"><?= inputValue($gstPercentage) ?></p>
            </div>

            <input type="number" style="display:none" name="listItem[<?= $randCode ?>][gstbackup]" value="<?= inputValue($gstPercentage_sql['data']['taxPercentage']) ?>" class="form-control full-width-center gst" id="gstbackup_<?= $randCode ?>" readonly>

        </td>
        <td class="gstTD">
            <input type="number" name="listItem[<?= $randCode ?>][gstAmount]" value="<?= inputValue($gstAmount) ?>" class="form-control full-width-center gstAmount" id="gstAmount_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>
            <div class="d-flex gap-2 my-1">
                <?= $comp_currency ?> <p id="local_gst_amount_<?= $randCode ?>">0.00</p>
            </div>

        </td>
        <td>
            <input type="number" name="listItem[<?= $randCode ?>][totalPrice]" value="<?= inputValue($totalAmount) ?>" class="form-control full-width-center itemTotalPrice" id="itemTotalPrice_<?= $randCode ?>" data-attr="<?= $randCode ?>" readonly>
            <div class="d-flex gap-2 my-1">
                <?= $comp_currency ?> <p id="local_total_price_<?= $randCode ?>">0.00</p>
            </div>

        </td>
        <td class="action-flex-btn">

            <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                <i class="statusItemBtn fa fa-cog" id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>"></i>
            </button>

            <button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>">
                <i class="fa fa-minus"></i>
            </button>
            <!-- <button type="button" class="btn-view" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                <i style="cursor: pointer; color: silver; text-shadow: 0 0 2px black; margin-left: 10px !important" id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="statusItemBtn mx-1 fa fa-cog"></i>
            </button>



            <i style="cursor: pointer; color: red; margin-right: 10px !important; border-color: red;
                        width: 17px;
                        height: 17px;
                        border-radius: 50%;
                        border: 1.5px solid red;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="delItemBtn mx-1 fa fa-minus"></i> -->


            <div class="modal modal-left left-item-modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white">Delivery Shedule</h5>
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button> -->
                        </div>
                        <div class="modal-body">
                            <!-- <h6 class="modal-title">Total Qty: <span class="mainQty" id="mainQty_<?= $randCode ?>">1</span></h6> -->
                            <div class="row">


                                <div class="col-lg-12 col-md-12 col-sm-12  modal-add-row modal-add-row-delivery_<?= $randCode ?>">

                                    <div class="row">
                                        <div class=" col-lg-5 col-md-5 col-sm-5 col-12">
                                            <div class="form-input">
                                                <label>Delivery date</label>
                                                <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control delDate delDate_<?= $randCode ?>" data-attr="<?= $randCode ?>" data-itemid="<?= $itemId ?>" id="delivery-date" placeholder="delivery date" value="<?= $_GET['deliveryDate'] ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                            <div class="form-input">
                                                <label>Quantity</label>
                                                <input type="text" step="any" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity multiQty_<?= $randCode ?>" data-attr="<?= $randCode ?>" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="<?=inputValue(1)?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                                            <div class="add-btn-plus">
                                                <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light" onclick='addDeliveryQty(<?= $randCode ?>,<?= $itemId ?>)'>
                                                    <i class="fa fa-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p id = "Date_error<?=$itemId?>" class="text-danger Date_error"  data-attr="<?= $itemId ?>"></p>
                        </div>
                        <div class="modal-footer modal-footer-fixed">
                            <button type="submit" id="finalBtn" class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light finalBtn" data-dismiss="modal" aria-label="Close" data-itemid="<?= $itemId ?>" data-attr="<?=$randCode?>">Save & Close</button>
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
} else {
    echo "Something wrong, try again!";
}
?>