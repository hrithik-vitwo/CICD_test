<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
if ($_GET['act'] === "listItem") {
    $itemId = $_GET['itemId'];
    $getItemObj = $ItemsObj->getItemById($itemId);
    $itemCode = $getItemObj['data']['itemCode'];
    $lastPricesql = "SELECT * FROM `" . ERP_BRANCH_PURCHASE_ORDER_ITEMS . "` WHERE `itemCode`=$itemCode ORDER BY po_item_id DESC LIMIT 1";
    $last = queryGet($lastPricesql);
    $lastRow = $last['data'] ?? "";
    $lastPrice = $lastRow['unitPrice'] ?? "";

    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);
   // console($randCode);
   $hsn = $getItemObj['data']['hsnCode'];
   $gstPercentage = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode` = '".$hsn."'");
  // console($randCode);
  $gstAmount = ($gstPercentage['data']['taxPercentage']/100)*$lastPrice;
  $totalAmount = $lastPrice + $gstAmount;

  $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
  // console($companyCurrencyObj);
   $companyCurrencyData = $companyCurrencyObj["data"];
   
$comp_currency = $companyCurrencyData["currency_name"];


?>
    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][update_itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][update_itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            <?= $getItemObj['data']['itemCode'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][update_itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
            <?= $getItemObj['data']['itemName'] ?>
        </td>
        <td>
            <div class="flex-display">
                <input type="number" name="listItem[<?= $randCode ?>][update_qty]" value="1" min="1" class="form-control full-width updateitemQty" id="updateitemQty_<?= $randCode ?>">
                <?php
                       if($getItemObj['data']['goodsType'] == 7){
                        echo $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['service_unit'])['data']['uomName'];
                        // echo $getItemObj['data']['service_unit'];
                        ?>
                     <input type="hidden" name="listItem[<?= $randCode ?>][update_uom]" value="<?= $getItemObj['data']['service_unit'] ?>">


                        <?php
                       }
                       else{
                        ?>

                <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                <input type="hidden" name="listItem[<?= $randCode ?>][update_uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
            <?php 
                       }
                       ?>
            </div>
        </td>
        <td>
                                                    <div class="flex-display">
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][update_remQty]" value="0" class="form-control full-width updateitemRemQty" id="updateitemRemQty_<?= $randCode ?>" readonly>
                                                      
                                                       <input type="hidden" value="<?= $data['remainingQty'] ?>" id= "updateitemRemQtyHidden_<?= $randCode ?>" readonly>
                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][update_uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                       
                                                    </div>
                                                    <p id="issueItemRemQty_<?= $randCode ?>" class="error"></p>
                                                </td>
                                                <td>
                                                    <div class="flex-display">
                                                        <input type="number" step="any" name="listItem[<?= $randCode ?>][update_srnQty]" value="0" class="form-control full-width updateitemSrnQty" id="updateitemSrnQty_<?= $randCode ?>" readonly>
                                                        <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                                                        <input type="hidden" name="listItem[<?= $randCode ?>][update_uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
                                                    </div>
                                                </td>
        <td>
            <input type="number" step="any" name="listItem[<?= $randCode ?>][update_unitPrice]" value="<?= $lastPrice ?>" class="form-control full-width-center updateitemUnitPrice" id="updateitemUnitPrice_<?= $randCode ?>" data-attr="<?= $randCode ?>" >
            <div class="d-flex gap-2 my-1">
            <?= $comp_currency ?> <p id="local_unit_price_<?= $randCode ?>">0.00</p>
            </div>
        </td>
        <!-- <td class="flex-display">
            <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
        </td> -->
        <td>
            <input type="number" name="listItem[<?= $randCode ?>][update_basePrice]" value="<?=$lastPrice?>" class="form-control full-width-center updateitemBasePrice" id="updateitemBasePrice_<?= $randCode ?>" data-attr="<?= $randCode ?>"  readonly>
            <div class="d-flex gap-2 my-1">
            <?= $comp_currency ?> <p id="local_base_price_<?= $randCode ?>">0.00</p>
            </div>
        </td>

        <td>
            <input type="number" name="listItem[<?= $randCode ?>][update_gst]" value="<?=$gstPercentage['data']['taxPercentage'] ?>" class="form-control full-width-center updategst" id="updategst_<?= $randCode ?>" data-attr="<?= $randCode ?>"  readonly>
            
        </td>
        <td>
            <input type="number" name="listItem[<?= $randCode ?>][update_gstAmount]" value="<?=$gstAmount?>" class="form-control full-width-center updategstAmount" id="updategstAmount_<?= $randCode ?>" data-attr="<?= $randCode ?>"  readonly>
            <div class="d-flex gap-2 my-1">
            <?= $comp_currency ?> <p id="local_gst_amount_<?= $randCode ?>">0.00</p>
            </div>
        </td>
        <td>
            <input type="number" name="listItem[<?= $randCode ?>][update_totalPrice]" value="<?= $lastPrice ?>" class="form-control full-width-center updateitemTotalPrice" id="updateitemTotalPrice_<?= $randCode ?>" data-attr="<?= $randCode ?>"  readonly>
            <div class="d-flex gap-2 my-1">
            <?= $comp_currency ?> <p id="local_total_price_<?= $randCode ?>">0.00</p>
            </div>
        </td>
        <td class="action-flex-btn">

<button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
    <i class="statusItemBtn fa fa-cog" id="statusItemBtn_<?= $itemId ?>"></i>
</button>

<button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $itemId ?>">
    <i class="fa fa-minus"></i>
</button>
</td>



<div class="modal modal-left left-item-modal fade" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Delivery Shedule <?= $randCode ?></h5>
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
                                <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control" id="delivery-date" placeholder="delivery date" value="">
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                            <div class="form-input">
                                <label>Quantity</label>
                                <input type="number" step="any" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2 col-12">
                            <div class="add-btn-plus">
                                <a style="cursor: pointer" class="btn btn-primary waves-effect waves-light" onclick='addDeliveryQty(<?= $randCode ?>)'>
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <div class="modal-footer modal-footer-fixed">
            <button type="submit" class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light" data-dismiss="modal">Save & Close</button>
        </div>
    </div>
</div>
</div>

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