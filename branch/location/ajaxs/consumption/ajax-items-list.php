<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
if ($_GET['act'] === "listItem") {
    $batchId = $_GET['batchId'];
    $getItemObj = queryGet("SELECT * FROM `erp_inventory_stocks_log` as logs LEFT JOIN `erp_inventory_items` as item ON logs.itemId = item.itemId  WHERE logs.stockLogId = $batchId ");
        //    $getItemObj =  $item_sql['data'];
        //    console($getItemObj);
        $itemPrice =  $getItemObj['data']['itemPrice']*1;

    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);
   // console($randCode);
?>

    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <input type="hidden" name="listItem[<?= $randCode ?>][log_id]" value="<?= $getItemObj['data']['stockLogId']?>" >
        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][refNumber]" value="<?= $getItemObj['data']['refNumber'] ?>">
            <?= $getItemObj['data']['refNumber'] ?>
        </td>
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
                <input type="number" name="listItem[<?= $randCode ?>][qty]" value="1" min="1" class="form-control full-width itemQty" id="itemQty_<?= $randCode ?>">
                <?php
                       if($getItemObj['data']['goodsType'] == 7){
                        echo $getItemObj['data']['service_unit'];
                        ?>
                     <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $getItemObj['data']['service_unit'] ?>">


                        <?php
                       }
                       else{
                        ?>

                <?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>
                <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ?>">
            <?php 
                       }
                       ?>
            </div>
        </td>
        <td>
            <input type="number" name="listItem[<?= $randCode ?>][unitPrice]" value="<?=$getItemObj['data']['itemPrice']?>" class="form-control full-width-center itemUnitPrice" id="itemUnitPrice_<?= $randCode ?>" readonly>
            <input type="hidden" name="listItem[<?= $randCode ?>][unitPriceHidden]" value="<?=$getItemObj['data']['itemPrice']?>" id="ItemUnitPriceTdInputhidden_<?= $randCode ?>" class="form-control text-xs itemUnitPricehidden" >


        </td>
        <!-- <td class="flex-display">
            <input type="number" name="listItem[<?= $randCode ?>][totalDiscount]" value="0.00" class="form-control full-width-center itemDiscount">%
        </td> -->
        <td>
            <input type="number" name="listItem[<?= $randCode ?>][totalPrice]" value="<?=$itemPrice?>" class="form-control full-width-center itemTotalPrice" id="itemTotalPrice_<?= $randCode ?>" readonly>
        </td>
        <td class="action-flex-btn">

            <button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>">
                <i class="fa fa-minus"></i>
            </button>
            


            
        </td>
    </tr>






<?php
}else {
    echo "Something wrong, try again!";
}
?>