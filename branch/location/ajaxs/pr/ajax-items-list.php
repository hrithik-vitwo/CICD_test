<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$today = date("Y-m-d");

$ItemsObj = new ItemsController();
if ($_GET['act'] === "listItem") {
    $itemId = $_GET['itemId'];
    $date=$_GET['date'];
    $getItemObj = $ItemsObj->getItemForPR($itemId);
    // console($getItemObj);
    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);
?>
    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width pr_item_list" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            <?= $getItemObj['data']['itemCode'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
            <p style="white-space: pre-wrap;"><?= $getItemObj['data']['itemName'] ?></p>
        </td>
        <td>

            <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="<?=inputQuantity(1)?>" class="form-control itemQty inputQuantityClass" id="itemQty_<?= $randCode ?>">

        </td>
        <td>
            <div class="uom-input">
                <select id="" name="listItem[<?= $randCode ?>][uom]" class="form-control">
                    <?php
                    if ($getItemObj['data']['goodsType'] == 7) {
                    ?>
                        <option value="<?php echo $getItemObj['data']['service_unit']; ?>"><?php echo $getItemObj['data']['serviceUnit']; ?></option>

                    <?php

                    } else {
                    ?>

                        <option value="<?php echo $getItemObj['data']['baseUnitMeasure']; ?>"><?php echo $getItemObj['data']['base_unit']; ?></option>

                    <?php

                    }

                    ?>

                </select>
            </div>


        </td>
        <td> <input type="text" name="listItem[<?= $randCode ?>][note]" value="" class="form-control itemNote" id="itemNote_<?= $randCode ?>"> </td>
        <td class="action-flex-btn">

            <button type="button" class="btn-view btn btn-primary" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                <i class="statusItemBtn fa fa-cog" id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>"></i>
            </button>

            <button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>">
                <i class="fa fa-minus"></i>
            </button>

            <div class="modal modal-left left-item-modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-white">Delivery Shedule</h5>
                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span class="text-white" aria-hidden="true">&times;</span>
                            </button> -->
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
                                                <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control delDate delDate_<?= $randCode ?>" data-attr="<?= $randCode ?>"  data-itemid="<?= $itemId ?>"  id="delivery-date" placeholder="delivery date"  value="<?= $date ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-5 col-md-5 col-sm-5 col-12">
                                            <div class="form-input">
                                                <label>Quantity</label>
                                                <input type="text" step="any" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity multiQty_<?= $randCode ?>" data-attr="<?= $randCode ?>" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="<?=inputQuantity(1)?>">
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
                            <button type="submit" id="finalBtn" class="btn btn-primary save-close-btn btn-xs float-right waves-effect waves-light" aria-label="Close"   data-dismiss="modal" data-itemid="<?= $itemId ?>" data-attr="<?=$randCode?>" >Save & Close</button>
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