    <?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
require_once("../../../../branch/location/bom/controller/bom.controller.php");
require_once("../../../../branch/location/bom/controller/mrp.controller.php");
// console($_GET);
// exit();
$productionItemId = $_REQUEST["productionItemId"];
$productionOrderId = $_REQUEST["productionOrderId"];
$productionOrderRemainQty = $_REQUEST["productionOrderRemainQty"];
$productionOrderDeclareQty = $_REQUEST["productionOrderDeclareQty"];
$productionOrderMrpStatus = $_REQUEST["productionOrderMrpStatus"];
$productionOrderDeclareDate = $_REQUEST["productionOrderDeclareDate"] ?? "";
$prodType= $_REQUEST["act"];

$bomControllerObj = new BomController();

$bomDetailObj = $bomControllerObj->getBomDetailsByItemId($productionItemId);
// console($bomDetailObj);
?>
<p class="text-left m-0">Bill Of Material</p>
<div class="card">
    <div class="card-body p-2" style="overflow-x : auto">
        <div class="">
            <div class="card">
                <div class="card-body">
                    <p class="text-left m-0 pl-3 pb-2 font-bold">Items</p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="borderNone">Item Code</th>
                                <th class="borderNone">Item Title</th>
                                <th class="borderNone">Consumption/Unit</th>
                                <th class="borderNone">Total Consumption</th>
                                <th class="borderNone">Available Stock</th>
                                <th class="borderNone">Storage Location Name</th>
                                <th class="borderNone">UOM</th>
                                <th class="borderNone">Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($bomDetailObj["data"]["bom_material_data"] ?? [] as $bomOneItem) {
                                // rmProdOpen
                                $stockLogObj = itemQtyStockCheckWithAcc($bomOneItem["item_id"], "'rmProdOpen'", ($bomOneItem["item_sell_type"] == "FIFO" ? "ASC" : "DESC"),null, $productionOrderDeclareDate);
                                $itemAvailableStocks = $stockLogObj['sumOfBatches'];
                            ?>
                                <tr class="productionOrderBomItemTrList_<?= $productionOrderId ?>">
                                    <td><?= $bomOneItem["itemCode"] ?? "" ?></td>
                                    <td>
                                        <p class="pre-wrap"><?= $bomOneItem["itemName"] ?? "" ?></p>
                                    </td>
                                    <td><span class="totalConsumptionPerUnit"><?= decimalQuantityPreview($bomOneItem["totalConsumption"]) ?></span> (<?= decimalQuantityPreview($bomOneItem["consumption"]) . " + " . decimalQuantityPreview($bomOneItem["extra"]) ?>%)</td>
                                    <td><span class="totalConsumption"><?= decimalQuantityPreview($bomOneItem["totalConsumption"] * $productionOrderDeclareQty) ?></span></td>
                                    <td><span class="totalAvailableStock"><?= decimalQuantityPreview($itemAvailableStocks) ?></span></td>
                                    <td><span class="totalAvailableStock"><?= $stockLogObj['data'][0]['storage_location_name'] ?></span></td>
                                    <td><?= $bomOneItem["uom"] ?></td>
                                    <td><?= $bomOneItem["item_sell_type"] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <p class="text-left m-0 pl-3 pb-2 font-bold">Activities</p>
                    <p class="text-left m-0 pl-3 pb-2 font-bold">Hourly Deployment</p>
                    <table class="table mb-3">
                        <thead>
                            <tr>
                                <th class="borderNone">#</th>
                                <th class="borderNone">Cost Center</th>
                                <th class="borderNone">Code</th>
                                <th class="borderNone">Head Name</th>
                                <th class="borderNone">Consumption/Unit</th>
                                <th class="borderNone">Extra(%)</th>
                                <th class="borderNone">UOM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 0;
                            foreach ($bomDetailObj["data"]["bom_hd_data"] ?? [] as $bomOneItem) {
                            ?>
                                <tr>
                                    <td><?= $sl += 1 ?></td>
                                    <td>
                                        <p class="pre-wrap"><?= $bomOneItem["CostCenter_desc"] ?></p>
                                    </td>
                                    <td><?= $bomOneItem["CostCenter_code"] ?></td>
                                    <td><?= strtoupper($bomOneItem["head_type"]) ?></td>
                                    <td><?= decimalQuantityPreview($bomOneItem["consumption"]) ?></td>
                                    <td><?= decimalQuantityPreview($bomOneItem["extra"]) ?></td>
                                    <td><?= $bomOneItem["uom"] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>

                    <p class="text-left m-0 pl-3 pb-2 font-bold">Other Heads</p>
                    <table class="table mb-3">
                        <thead>
                            <tr>
                                <th class="borderNone">#</th>
                                <th class="borderNone">Cost center</th>
                                <th class="borderNone">Code</th>
                                <th class="borderNone">Other Head</th>
                                <th class="borderNone">Total Consumptions</th>
                                <th class="borderNone">Extra(%)</th>
                                <th class="borderNone">UOM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 0;
                            foreach ($bomDetailObj["data"]["bom_other_head_data"] ?? [] as $bomOneItem) {
                            ?>
                                <tr>
                                    <td><?= $sl += 1 ?></td>
                                    <td>
                                        <p class="pre-wrap"><?= $bomOneItem["CostCenter_desc"] ?></p>
                                    </td>
                                    <td><?= $bomOneItem["CostCenter_code"] ?></td>
                                    <td><?= ucfirst($bomOneItem["head_name"] ?? "") ?></td>
                                    <td><?=decimalQuantityPreview($bomOneItem["consumption"]) ?></td>
                                    <td><?= $bomOneItem["extra"] ?></td>
                                    <td><?= $bomOneItem["uom"] ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            if ($bomDetails["bomProgressStatus"] == "COGM") {
            ?>
                <div class="card">
                    <div class="card-body">
                        <p class="text-left m-0 pl-3 pb-2 font-bold">Other Addons</p>
                        <form action="" method="post">
                            <input type="hidden" name="bomId" value="<?= $bomDetails["bomId"] ?>">
                            <table class="table mb-3">
                                <thead>
                                    <tr>
                                        <th class="borderNone">Others</th>
                                        <th class="borderNone">Select Gl</th>
                                        <th class="borderNone">Amount</th>
                                        <th class="borderNone">Remarks</th>
                                        <th class="borderNone">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="otherAddonsForm">
                                </tbody>
                            </table>
                            <button type="submit" name="addCOGSFormSubmitBtn" class="btn btn-sm btn-primary text-xs mb-4 mr-3 float-right" value="Create COGS">Create COGS</button>
                        </form>

                    </div>
                </div>
            <?php
            } elseif ($bomDetails["bomProgressStatus"] == "COGS") {
            ?>
                <div class="card">
                    <div class="card-body">
                        <p class="text-left m-0 pl-3 pb-2 font-bold">COGS Items </p>
                        <table class="table mb-3">
                            <thead>
                                <tr>
                                    <th class="borderNone">Name</th>
                                    <th class="borderNone">Gl Code</th>
                                    <th class="borderNone">Amount</th>
                                    <th class="borderNone">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($bomItemsList as $bomOneItem) {
                                    if ($bomOneItem["bomItemType"] == "othersCogs") {
                                        // goods other item list
                                ?>
                                        <tr>
                                            <td><?= $bomOneItem["othersItem"] ?></td>
                                            <td><?= $bomOneItem["itemGl"] ?></td>
                                            <td><?= decimalValuePreview($bomOneItem["amount"]) ?></td>
                                            <td><?= $bomOneItem["remarks"] ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <p class="text-left pl-3 pb-2 font-bold">Discount & Margins</p>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="borderNone">Discount</th>
                                    <th class="bomMargin">Margin Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" class="form-control mt-2 mb-2" name="bomDiscount" placeholder="Bom Discount" /></td>
                                    <td><input step="0.01" type="number" class="form-control mt-2 mb-2" name="bomMargin" placeholder="Bom margings" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php
            } ?>

        </div>
    </div>
    <div class="card-footer p-2">
        <button id="consumptionPostingCancel_<?= $productionOrderId ?>" type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button id="consumptionPosting_<?= $productionOrderId ?>" type="submit" name="consumptionPosting" class="btn btn-primary">Print & declare</button>
     

        <?php if($prodType=="productionOrder"){?>
        
        <ul class="production-order-note ml-0 pl-0 mt-2">
            <p>
                NOTE : By Confirming below are the things that will be triggered automatically
            </p>
            <hr class="my-2">
            <li>
                <p>
                    Automatic accounting
                </p>
            </li>
            <li>
                <p>
                    Consumption of raw-material (from production stock)
                </p>
            </li>
            <li>
                <p>
                    All the (SFG inside the BOM) will be produced
                </p>
            </li>
            <li>
                <p>
                    All produced SFG inside the trigger will be consumed
                </p>
            </li>
            <li>
                <p>
                    The final Product (FG/SFG) of that order will be produced (Add in stock)
                </p>
            </li>
        </ul>
        <?php }?>

    </div>
</div>