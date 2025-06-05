<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];
$BranchSoObj = new BranchSo();
$ItemsObj = new ItemsController();
if ($_GET['act'] === "listItem") {
    $itemId = $_GET['itemId'];
    $getItemObj = $ItemsObj->getItemById($itemId);
    //   console($getItemObj);
    $itemCode = $getItemObj['data']['itemCode'];
    $lastPricesql = "SELECT * FROM `erp_branch_purchase_order_items`as po_item JOIN `erp_branch_purchase_order` as po ON po_item.`po_id`=po.po_id WHERE `location_id`=$location_id AND `itemCode`=$itemCode ORDER BY po_item.`po_item_id` DESC LIMIT 1";


    $last = queryGet($lastPricesql);
    $lastRow = $last['data'] ?? "";
    $lastPrice = $lastRow['unitPrice'] ?? "0";

    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);
    $hsn = $getItemObj['data']['hsnCode'];
    $gstPercentage = queryGet("SELECT * FROM `erp_hsn_code` WHERE `hsnCode` = '" . $hsn . "'");
    // console($randCode);
    $gstAmount = ($gstPercentage['data']['taxPercentage'] / 100) * $lastPrice;
    $totalAmount = $lastPrice + $gstAmount;


?>
    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            <?= $getItemObj['data']['itemCode'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
            <p class="pre-normal"><?= $getItemObj['data']['itemName'] ?></p>
        </td>
        <td>
            <div class="d-flex">
                <!-- name="listItem[<?= $randCode ?>][stockQty]"  -->
                <!-- <span class="rupee-symbol currency-symbol currency-symbol-dynamic pr-1">#</span>
                    <select name="listItem[<?= $randCode ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $randCode ?>">
                        <option value="FgWhOpen_<?= $getItemSummaryObj['fgWhOpen'] ?>">FG Warehouse (<?= $getItemSummaryObj['fgWhOpen'] ?>)</option>
                        <option value="FgMktOpen_<?= $getItemSummaryObj['fgMktOpen'] ?>">FG Mkt Location (<?= $getItemSummaryObj['fgMktOpen'] ?>)</option>
                    </select> -->
                    <?php
                    // $qtyObj = $BranchSoObj->deliveryCreateItemQty($getItemObj['data']['itemId']);
                    
                    $qtyObj = $BranchSoObj->itemQtyStockCheck($getItemObj['data']['itemId'],  "'rmWhOpen','rmProdOpen','sfgStockOpen', 'fgWhOpen','fgMktOpen'", "DESC", '', $creationDate);

                    // console($qtyObj);
                    $sumOfBatches = $qtyObj['sumOfBatches'];
                    $batchesDetails = $BranchSoObj->convertToWHSLBatchArray($qtyObj['data']);
                    // console($itemQtyStockCheck);
                    ?>
                    <input type="hidden" name="listItem[<?= $randCode ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $randCode ?>" value="<?= $sumOfBatches; ?>">

                    <!-- Button to Open the Modal -->
                    <div class="qty-modal py-2">
                        <p class="font-bold text-center checkQtySpan" id="checkQtySpan_<?= $randCode ?>"><?= $sumOfBatches; ?></p>
                        <hr class="my-2 w-50 mx-auto">
                        <div class="text-xs d-flex align-items-center gap-2 justify-content-center">
                            <p class="itemSellType" id="itemSellType_<?= $randCode ?>">CUSTOM</p>
                            <ion-icon name="create-outline" class="stockBtn" id="stockBtn_<?= $randCode ?>" data-bs-toggle="modal" data-bs-target="#stockSetup<?= $randCode ?>" style="cursor: pointer;"></ion-icon>
                        </div>
                    </div>
                    <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $randCode ?>" name="listItem[<?= $randCode ?>][itemSellType]" value="CUSTOM">

                    <!-- The Modal -->
                    <div class="modal fade stock-setup-modal" id="stockSetup<?= $randCode ?>">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <!-- Modal Header -->
                                <div class="modal-header" style="background: #003060; color: #fff;">
                                    <h4 class="modal-title text-sm text-white">Stock Setup (CUSTOM)
                                        <p class="note font-normal">
                                        This will help you to check the storage location wise current  stock and purchase or production date  along with cost.
                                        You can pick the cost from the line by selecting the radio.
                                        </p>
                                    </h4>
                                </div>

                                <!-- Modal body -->
                                <div class="modal-body">

                                    <!-- start warehouse accordion -->
                                    <div class="modal-select-type my-3">
                                        <!-- <div class="type type-one">
                                            <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass fifo" data-rdcode="<?= $randCode ?>" value="FIFO" id="fifo_<?= $randCode ?>" <?php if ($masterItemDetails['item_sell_type'] == "FIFO") {
                                                                                                                                                                                                                                echo "checked";
                                                                                                                                                                                                                            } ?>>
                                            <label for="fifo" class="text-xs mb-0">FIFO</label>
                                        </div>
                                        <div class="type type-two">
                                            <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass lifo" data-rdcode="<?= $randCode ?>" value="LIFO" id="lifo_<?= $randCode ?>" <?php if ($masterItemDetails['item_sell_type'] == "LIFO") {
                                                                                                                                                                                                                                echo "checked";
                                                                                                                                                                                                                            } ?>>
                                            <label for="lifo" class="text-xs mb-0">LIFO</label>
                                        </div> -->
                                        <div class="type type-three">
                                            <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass custom" data-rdcode="<?= $randCode ?>" value="CUSTOM" id="custom_<?= $randCode ?>" checked>
                                            <label for="custom" class="text-xs mb-0 text-muted">Custom</label>
                                        </div>
                                    </div>
                                    <!-- <div class="textarea-note my-2">
                                        <textarea class="form-control" cols="6" rows="20" placeholder="notes...."></textarea>
                                      </div> -->
                                    <div class="customitemreleaseDiv<?= $randCode ?>">
                                        <?php
                                        // console($qtyObj);
                                        // console($batchesDetails);
                                        foreach ($batchesDetails as $whKey => $wareHouse) {
                                        ?>
                                            <div class="accordion accordion-flush warehouse-accordion p-0" id="accordionFlushExample">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header w-100" id="flush-headingOne">
                                                        <button class="accordion-button btn btn-primary warehouse-header waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $whKey ?>" aria-expanded="true" aria-controls="flush-collapseOne">
                                                            <?= $wareHouse['warehouse_code'] ?> | <?= $wareHouse['warehouse_name'] ?>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse<?= $whKey ?>" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" style="">
                                                        <div class="accordion-body p-0">
                                                            <h1></h1>
                                                            <div class="card bg-transparent">
                                                                <div class="card-body px-2 mx-3" style="background-color: #f9f9f9;">
                                                                    <!-- start location accordion -->
                                                                    <?php foreach ($wareHouse['storage_locations'] as $locationKey => $location) {
                                                                        if($_GET["prod"] == "1")
                                                                        {
                                                                        if($location["storage_location_type"] == "RM-PROD"){
                                                                    ?>

                                                                        <div id="locAccordion">
                                                                            <div class="card bg-transparent">
                                                                                <div class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                    <a class="btn text-dark w-100 storage-after" data-bs-toggle="collapse" href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                        <?= $location['storage_location_code'] ?> | <?= $location['storage_location_name'] ?>
                                                                                    </a>
                                                                                </div>
                                                                                <div id="collapse<?= $whKey ?><?= $locationKey ?>" class="collapse" data-bs-parent="#locAccordion">
                                                                                    <div class="card-body bg-light mx-3">
                                                                                        <?php
                                                                                        // console($location['batches']);
                                                                                        foreach ($location['batches'] as $batchKey => $batch) {
                                                                                            // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                            $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                        ?>
                                                                                            <div class="storage-location mb-2">
                                                                                                <div class="input-radio">
                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?=  $batch['logRef'] ?>" data-mrp = "<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>">
                                                                                                    <?php } else { ?>
                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?=  $batch['logRef'] ?>" data-mrp = "<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" disabled>
                                                                                                    <?php } ?>
                                                                                                </div>
                                                                                                <div class="d-grid">
                                                                                                
                                                                                                <p class="text-sm mb-2">
                                                                                                        <?= $batch['itemPrice'] ?>
                                                                                                    </p>
                                                                                                    <p class="text-sm mb-2">
                                                                                                        <?= $batch['logRef'] ?>
                                                                                                    </p>
                                                                                                    <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                        <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= $batch['itemQty'] ?> <?= $uomName ?> </span>
                                                                                                    </p>
                                                                                                </div>
                                                                                                <div class="input">
                                                                                                  
                                                                                                </div>
                                                                                            </div>
                                                                                            <hr>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    <?php
                                                                        } 
                                                                        else
                                                                        {
                                                                            ?>

                                                                        <div id="locAccordion">
                                                                            <div class="card bg-transparent">
                                                                                <div class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                    <a class="btn text-dark w-100 storage-after" data-bs-toggle="collapse" href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                        <?= $location['storage_location_code'] ?> | <?= $location['storage_location_name'] ?>
                                                                                    </a>
                                                                                </div>
                                                                                <div id="collapse<?= $whKey ?><?= $locationKey ?>" class="collapse" data-bs-parent="#locAccordion">
                                                                                    <div class="card-body bg-light mx-3">
                                                                                        <?php
                                                                                        // console($location['batches']);
                                                                                        foreach ($location['batches'] as $batchKey => $batch) {
                                                                                            // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                            $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                        ?>
                                                                                            <div class="storage-location mb-2">
                                                                                                <div class="input-radio">
                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?=  $batch['logRef'] ?>" data-mrp = "<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>">
                                                                                                    <?php } else { ?>
                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?=  $batch['logRef'] ?>" data-mrp = "<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" disabled>
                                                                                                    <?php } ?>
                                                                                                </div>
                                                                                                <div class="d-grid">
                                                                                                <p class="text-sm mb-2">
                                                                                                        <?= $batch['itemPrice'] ?>
                                                                                                    </p>
                                                                                                    <p class="text-sm mb-2">
                                                                                                        <?= $batch['logRef'] ?>
                                                                                                    </p>
                                                                                                    <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                        <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= $batch['itemQty'] ?> <?= $uomName ?> </span>
                                                                                                    </p>
                                                                                                </div>
                                                                                                <div class="input">
                                                                                                  
                                                                                                </div>
                                                                                            </div>
                                                                                            <hr>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                    <?php
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        ?>
                                                                        <div id="locAccordion">
                                                                            <div class="card bg-transparent">
                                                                                <div class="card-header p-2 border rounded-0 bg-transparent border-0 border-bottom">
                                                                                    <a class="btn text-dark w-100 storage-after" data-bs-toggle="collapse" href="#collapse<?= $whKey ?><?= $locationKey ?>">
                                                                                        <?= $location['storage_location_code'] ?> | <?= $location['storage_location_name'] ?>
                                                                                    </a>
                                                                                </div>
                                                                                <div id="collapse<?= $whKey ?><?= $locationKey ?>" class="collapse" data-bs-parent="#locAccordion">
                                                                                    <div class="card-body bg-light mx-3">
                                                                                        <?php
                                                                                       // console($location['batches']);
                                                                                        foreach ($location['batches'] as $batchKey => $batch) {

                                                                                            // $batchItemUom = $ItemsObj->getBaseUnitMeasureById($batch['itemUom'])['data']['uomName'];
                                                                                            $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                        ?>
                                                                                            <div class="storage-location mb-2">
                                                                                                <div class="input-radio">
                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?=  $batch['logRef'] ?>" data-mrp = "<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>">
                                                                                                    <?php } else { ?>
                                                                                                        <input type="radio" name="listItem[<?= $randCode ?>][batchselectionchekbox]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?> myRadio" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" value="<?=  $batch['logRef'] ?>" data-mrp = "<?= $batch['itemPrice'] ?>" data-attr="<?= $randCode ?>" disabled>
                                                                                                    <?php } ?>
                                                                                                </div>
                                                                                                <div class="d-grid">
                                                                                                <p class="text-sm mb-2">
                                                                                                      Purchase Price :  <?= $batch['itemPrice'] ?>
                                                                                                    </p>
                                                                                                    
                                                                                                    <p class="text-sm mb-2">
                                                                                                        <?= $batch['logRef'] ?>
                                                                                                    </p>
                                                                                                    <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                        <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= $batch['itemQty'] ?> <?= $uomName ?> </span>
                                                                                                    </p>
                                                                                                </div>
                                                                                                <div class="input">
                                                                                                   
                                                                                                </div>
                                                                                            </div>
                                                                                            <hr>
                                                                                        <?php } ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <?php

                                                                    }

                                                                } ?>



                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <!-- end warehouse accordion -->
                                </div>

                                <!-- Modal footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success proceed" id="proceed_<?= $randCode ?>" data-bs-dismiss="modal">Proceed >></button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">

            </div>
        </td>
        <td>
            <input type="number" step="any" name="listItem[<?= $randCode ?>][cost]" value="" class="form-control full-width-center cost" id="cost_<?= $randCode ?>" data-attr="<?= $randCode ?>">


        </td>

        <td>
            <input type="number" step="any" name="listItem[<?= $randCode ?>][margin]" value="" class="form-control full-width-center margin" id="margin_<?= $randCode ?>"  data-attr="<?= $randCode ?>">
        </td>

        <td>
            <input type="number" step="any" name="listItem[<?= $randCode ?>][mrp]" value="" class="form-control full-width-center mrp" id="mrp_<?= $randCode ?>">
        </td>

        <td class="action-flex-btn">



            <button type="button" class="btn btn-danger delItemBtn" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>">
                <i class="fa fa-minus"></i>
            </button>


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