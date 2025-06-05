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
    $creationDate = $_GET['creationDate'];
    $movemenrtypesDropdown = $_GET['movemenrtypesDropdown'];


    $itemQty = $items['qty'] ?? 0;
    $getItemObj = $ItemsObj->getItemById($itemId);
    $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];

    // console($getItemSummaryObj);
    // console($getItemObj);
    $company = $BranchSoObj->fetchCompanyDetails()['data'];
    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

    // print_r($getItemObj);
    $goodsType = $getItemObj['data']['goodsType'];
    $masterItemDetails = $getItemSummaryObj;

    $qrysrui = queryGet("SELECT loc.storage_location_id, loc.storage_location_code, loc.storage_location_name, loc.storage_location_type, loc.storageLocationTypeSlug, warh.warehouse_id, warh.warehouse_code, warh.warehouse_name FROM erp_storage_location AS loc LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id WHERE loc.storage_location_storage_type!='Reserve' AND loc.company_id=$company_id", true);
    $sldattaqe = $qrysrui['data'];

    // console($qrysrui);
?>

    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>_<?= $randCode ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][parentGlId]" value="<?= $getItemObj['data']['parentGlId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][goodsType]" value="<?= $getItemObj['data']['goodsType'] ?>">

        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            <?= $getItemObj['data']['itemCode'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
            <p style="white-space: pre-wrap;"><?= $getItemObj['data']['itemName'] ?></p>

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
                <input type="hidden" name="listItem[<?= $randCode ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $randCode ?>" value="<?= inputQuantity($sumOfBatches); ?>">

                <!-- Button to Open the Modal -->
                <div class="qty-modal py-2">
                    <p class="font-bold text-center checkQtySpan" id="checkQtySpan_<?= $randCode ?>"><?= inputQuantity($sumOfBatches); ?></p>
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
                                <h4 class="modal-title text-sm text-white">Stock Setup (CUSTOM)</h4>
                                <p class="text-xs my-2 ml-5">Total Picked Qty :
                                    <span class="font-bold itemSelectTotalQty" id="itemSelectTotalQty_<?= $randCode ?>">0</span>
                                </p>

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

                                    <style>
                                        input.red-placeholder {
                                            color: red;
                                            /* Text color */
                                            border: 1px solid red;
                                            /* Border color */
                                        }
                                    </style>
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
                                                                    if ($_GET["prod"] == "1") {
                                                                        if ($location["storage_location_type"] == "RM-PROD") {
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
                                                                                                if (in_array($batch['refActivityName'], ['STRGE-LOC', 'PGI', 'REV-INVOICE', 'CN', 'DN', 'MAT-MAT-IN'])) {
                                                                                                    $batchno = $batch['logRef'];
                                                                                                } else {
                                                                                                    $batchno = $batch['refNumber'];
                                                                                                }


                                                                                                $batchStatus = $BranchSoObj->checkBatchStatus($batchno, $company_id, $branch_id, $location_id,$batch['refActivityName']);

                                                                                                $disbaledstatus = $batchStatus['disabled'];
                                                                                                $status = $batchStatus['status'];
                                                                                                $placeholderText = $batchStatus['placeholderText'];
                                                                                                $placeholderClass = $batchStatus['placeholderClass'];
                                                                                                $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                            ?>
                                                                                                <div class="storage-location mb-2">
                                                                                                    <div class="input-radio">
                                                                                                        <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                            <input type="checkbox" <?= $disbaledstatus ?> name="listItem[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                        <?php } else { ?>
                                                                                                            <input type="checkbox" <?= $disbaledstatus ?> name="listItem[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" disabled>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                    <div class="d-grid">
                                                                                                        <p class="text-sm mb-2">
                                                                                                            <?= $batch['logRef'] ?>
                                                                                                        </p>
                                                                                                        <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                            <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= inputQuantity($batch['itemQty']) ?> <?= $uomName ?> </span>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                    <div class="input">
                                                                                                        <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                            <input step="any" <?= $disbaledstatus ?> type="number" name="listItem[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> inputQuantityClass form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>">
                                                                                                        <?php } else { ?>
                                                                                                            <input step="any" <?= $disbaledstatus ?> type="number" name="listItem[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> inputQuantityClass form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>" disabled>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <hr>
                                                                                            <?php } ?>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        <?php
                                                                        } else {
                                                                        ?>

                                                                            <div id=" locAccordion">
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
                                                                                                if (in_array($batch['refActivityName'], ['STRGE-LOC', 'PGI', 'REV-INVOICE', 'CN', 'DN', 'MAT-MAT-IN'])) {
                                                                                                    $batchno = $batch['logRef'];
                                                                                                } else {
                                                                                                    $batchno = $batch['refNumber'];
                                                                                                }


                                                                                                $batchStatus = $BranchSoObj->checkBatchStatus($batchno, $company_id, $branch_id, $location_id,$batch['refActivityName']);

                                                                                                $disbaledstatus = $batchStatus['disabled'];
                                                                                                $status = $batchStatus['status'];
                                                                                                $placeholderText = $batchStatus['placeholderText'];
                                                                                                $placeholderClass = $batchStatus['placeholderClass'];
                                                                                                $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                            ?>
                                                                                                <div class="storage-location mb-2">
                                                                                                    <div class="input-radio">
                                                                                                        <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                            <input type="checkbox" <?= $disbaledstatus ?> name="listItem[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                        <?php } else { ?>
                                                                                                            <input type="checkbox" <?= $disbaledstatus ?> name="listItem[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" disabled>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                    <div class="d-grid">
                                                                                                        <p class="text-sm mb-2">
                                                                                                            <?= $batch['logRef'] ?>
                                                                                                        </p>
                                                                                                        <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                            <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= inputQuantity($batch['itemQty']) ?> <?= $uomName ?> </span>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                    <div class="input">
                                                                                                        <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                            <input step="any" <?= $disbaledstatus ?> readonly type="number" name="listItem[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> inputQuantityClass form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>">
                                                                                                        <?php } else { ?>
                                                                                                            <input step="any" <?= $disbaledstatus ?> readonly type="number" name="listItem[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> inputQuantityClass form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>" disabled>
                                                                                                        <?php } ?>
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
                                                                    } else {
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
                                                                                            if (in_array($batch['refActivityName'], ['STRGE-LOC', 'PGI', 'REV-INVOICE', 'CN', 'DN', 'MAT-MAT-IN'])) {
                                                                                                $batchno = $batch['logRef'];
                                                                                            } else {
                                                                                                $batchno = $batch['refNumber'];
                                                                                            }


                                                                                            $batchStatus = $BranchSoObj->checkBatchStatus($batchno, $company_id, $branch_id, $location_id,$batch['refActivityName']);

                                                                                            $disbaledstatus = $batchStatus['disabled'];
                                                                                            $status = $batchStatus['status'];
                                                                                            $placeholderText = $batchStatus['placeholderText'];
                                                                                            $placeholderClass = $batchStatus['placeholderClass'];
                                                                                            $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                                                        ?>
                                                                                            <div class="storage-location mb-2">
                                                                                                <div class="input-radio">
                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                        <input type="checkbox" <?= $disbaledstatus ?> name="listItem[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                    <?php } else { ?>
                                                                                                        <input type="checkbox" <?= $disbaledstatus ?> name="listItem[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" disabled>
                                                                                                    <?php } ?>
                                                                                                </div>
                                                                                                <div class="d-grid">
                                                                                                    <p class="text-sm mb-2">
                                                                                                        <?= $batch['logRef'] ?>
                                                                                                    </p>
                                                                                                    <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                        <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= inputQuantity($batch['itemQty']) ?> <?= $uomName ?> </span>
                                                                                                    </p>
                                                                                                </div>
                                                                                                <div class="input">
                                                                                                    <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                        <input step="any" <?= $disbaledstatus ?> type="number" name="listItem[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> inputQuantityClass form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>">
                                                                                                    <?php } else { ?>
                                                                                                        <input step="any" <?= $disbaledstatus ?> type="number" name="listItem[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> inputQuantityClass form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>" disabled>
                                                                                                    <?php } ?>
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
                                    <?php }

                                    if ($movemenrtypesDropdown == "book_to_physical") {
                                    ?>

                                        <!-- Manual Batch Entry -->
                                        <div class="accordion manual-accordion accordion-flush warehouse-accordion p-0" id="accordionFlushExample2">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header w-100" id="flush-headingOne">
                                                    <button style=" background-color: #798c8c !important; color:#fff !important;" class="accordion-button btn btn-primary warehouse-header waves-effect waves-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $itemId ?>" aria-expanded="true" aria-controls="flush-collapseOne">
                                                        <b>Manual Batch</b>
                                                    </button>
                                                </h2>
                                                <div id="collapse<?= $itemId ?>" class="accordion-collapse collapse <?php if ($qtyObj['numRows'] <= 0) { ?>show<?php } ?>" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample" style="">
                                                    <div class="accordion-body p-3">
                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-input mb-2">
                                                                    <label for="">Storage Location<span class="text-danger">*</span></label>
                                                                    <select class="form-control" name="listItem[<?= $randCode ?>][manualbatchselection][storageLocation]">
                                                                        <option value="">Select Storage location</option>
                                                                        <?php
                                                                        // console($sldattaqe);
                                                                        foreach ($sldattaqe as $datasllll) {
                                                                        ?>
                                                                            <option value="<?= $datasllll['storage_location_id'] . '|' . $datasllll['storageLocationTypeSlug']; ?>"><?php echo $datasllll['warehouse_code'] . ' >> ' . $datasllll['storage_location_code'] . ' >> ' . $datasllll['storage_location_name']; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-input">
                                                                    <label for=""> Batch Number<span class="text-danger">*</span></label>
                                                                    <input type="text" data-rnds="<?= $randCode ?>" class="form-control manualBatchNumber" name="listItem[<?= $randCode ?>][manualbatchselection][batchNumber]">
                                                                    <p class="note manualBatchNumberDate<?= $randCode ?>"></p>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-input">
                                                                    <label for="">Quantity<span class="text-danger">*</span></label>
                                                                    <input step="0.00" type="number" class="form-control enterQtyManual qty<?= $randCode; ?>" data-rdcode="<?= $randCode . '|Manual'; ?>" name="listItem[<?= $randCode ?>][manualbatchselection][qty]">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-input">
                                                                    <label for="">Rcv/Mfg Date<span class="text-danger">*</span></label>
                                                                    <input type="date" class="form-control manualBatchNumberBornDate<?= $randCode ?>" name="listItem[<?= $randCode ?>][manualbatchselection][bornDate]">
                                                                </div>
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
                                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Proceed >></button>
                            </div>

                        </div>
                    </div>
                </div>
                <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][sumOfBatches]" value="<?= inputQuantity($sumOfBatches) ?>">

            </div>
        </td>
        <td class="inp-td">
            <div class="d-flex">
                <?php if ($movemenrtypesDropdown == "book_to_physical") { ?>
                    <select name="listItem[<?= $randCode ?>][sign]" class="text-center booktohicl_<?= $randCode ?>">
                        <option value="+">+</option>
                        <option value="-" selected>-</option>
                    </select>
                <?php } else { ?>
                    <input type="hidden" name="listItem[<?= $randCode ?>][sign]" value="-">
                <?php } ?>
                <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= inputQuantity(0) ?>" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>" readonly>
                <?php
                echo $uomName = getUomDetail($getItemObj['data']['baseUnitMeasure'])['data']['uomName'];
                //  $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ;
                ?>
                <input type="hidden" name="listItem[<?= $randCode ?>][uom]" id="source_uom_<?= $randCode ?>" value="<?= $getItemObj['data']['baseUnitMeasure'] ?>">

            </div>
            <span style="display:none; font-size: .8em!important" class="text-danger qtyMsg" id="qtyMsg_<?= $randCode ?>">Please enter valid qty</span>
        </td>

        <?php
        if ($_GET["mat"] == "1") {
        ?>
            <td>
                <select name="listItem[<?= $randCode ?>][destinationItems]" class="select2 form-control destination_item_class" id="destination_item_<?= $randCode ?>" data-mainitem="<?= base64_encode($itemId) ?>" required>
                    <option value="">Select Item</option>
                    <?php
                    $qrysrui = queryGet("SELECT
                summary.*,
                items.*,
                hsn.taxPercentage AS taxPercentage
                FROM  `" . ERP_INVENTORY_ITEMS . "` AS items
                LEFT JOIN `" . ERP_INVENTORY_STOCKS_SUMMARY . "` AS summary ON items.itemId = summary.itemId
                LEFT JOIN `" . ERP_HSN_CODE . "` AS hsn ON items.hsnCode = hsn.hsnCode
                WHERE items.goodsType IN (1,2,3,4)
                    AND items.status = 'active'
                    AND (summary.company_id = $company_id OR summary.company_id IS NULL)
                    AND (summary.status = 'active' OR summary.status IS NULL)
                    AND items.hsnCode IN (SELECT hsnCode FROM `erp_hsn_code`)
                    AND summary.bomStatus IN (0,2);
                ", true);
                    $sldattaqeItem = $qrysrui['data'];

                    foreach ($sldattaqeItem as $datasllllItem) {

                        $destination_uom = getUomDetail($datasllllItem['baseUnitMeasure'])['data']['uomName'];
                        $destination_uomId = getUomDetail($datasllllItem['baseUnitMeasure'])['data']['uomId'];
                        $mwp = $datasllllItem['movingWeightedPrice'];

                    ?>
                        <option value="<?= $datasllllItem['itemId'] ?>" data-mwp="<?= $mwp; ?>" data-uomid=<?= $destination_uomId ?> data-uom=<?= $destination_uom ?>><?php echo $datasllllItem['itemName'] . ' ( ' . $datasllllItem['itemCode'] . ' ) ' ?></option>
                    <?php } ?>

                </select>
            </td>
            <td>
                <input step="any" type="number" name="listItem[<?= $randCode ?>][destination_qty]" value="0" class="form-control destination_itemQty" id="destination_itemQty_<?= $randCode ?>" required><span id="destination_uom_<?= $randCode ?>"></span>
                <input type="hidden" name="listItem[<?= $randCode ?>][destination_uom]" id="destination_uom_hidden_<?= $randCode ?>" value="">
            </td>
            <td>
                <select name="listItem[<?= $randCode ?>][destinationStorageLocation]" class="select2 form-control " required>
                    <option value="">Select Storage Location</option>
                    <?php
                    // console($sldattaqe);
                    foreach ($sldattaqe as $datasllll) {
                    ?>
                        <option value="<?= $datasllll['storage_location_id'] . '|' . $datasllll['storageLocationTypeSlug']; ?>"><?php echo $datasllll['warehouse_code'] . ' >> ' . $datasllll['storage_location_code'] . ' >> ' . $datasllll['storage_location_name']; ?></option>
                    <?php } ?>

                </select>
            </td>
        <?php
        }
        ?>
        <td class="action-flex-btn">

            <button type="button" class="btn btn-danger delItemBtn">
                <i class="fa fa-minus" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="delItemBtn mx-1 fa fa-minus"></i>
            </button>

        </td>
    </tr>

<?php
} else {
    echo "Something wrong, try again!";
}
?>


<script>
    $(document).ready(function() {
        $(".select2").select2();
    })
</script>