<?php
require_once("../../../../app/v1/connection-branch-admin.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
function checkBatchStatus($batchno, $company_id, $branch_id, $location_id,$refActivityName="not required")
{
   if ($refActivityName != "not required") {
            $refActivityName = strtolower($refActivityName);
            if (in_array($refActivityName,['migration','consumption(book-physical)'])) {
                $isValid = 1;
                return [
                    'disabled' => $isValid ? '' : 'disabled',
                    'status' => !$isValid,
                    'placeholderText' => $isValid ? '' : 'Acc. doc. not found',
                    'placeholderClass' => $isValid ? '' : 'red-placeholder'
                ];
            }
        }
    $prodResult = queryGet("SELECT `prod_declaration_journal_id`, `fgsfg_declaration_journal_id`
                            FROM erp_production_declarations
                            WHERE `company_id` = '$company_id' AND `branch_id` = '$branch_id'
                            AND `location_id` = '$location_id' AND `code` = '$batchno'");

    $grnResult = queryGet("SELECT `grnCode`, `grnPostingJournalId`
                           FROM erp_grn
                           WHERE `companyId` = '$company_id' AND `branchId` = '$branch_id'
                           AND `locationId` = '$location_id' AND `grnCode` = '$batchno'");

    $isProdValid = $prodResult['status'] == 'success' &&
        !empty($prodResult['data']['prod_declaration_journal_id']) &&
        !empty($prodResult['data']['fgsfg_declaration_journal_id']);

    $isGrnValid = $grnResult['status'] == 'success' &&
        !empty($grnResult['data']['grnCode']) &&
        !empty($grnResult['data']['grnPostingJournalId']);

    $isValid = $isProdValid || $isGrnValid;

    return [
        'disabled' => $isValid ? '' : 'disabled',
        'status' => !$isValid,
        'placeholderText' => $isValid ? '' : 'Acc. doc. not found',
        'placeholderClass' => $isValid ? '' : 'red-placeholder'
    ];
}
if ($_GET['act'] === "itemStock") {
    $itemId = $_GET['itemId'];
    $invoiceDate = $_GET['invoiceDate'];
    $randCode = $_GET['randCode'];

    // console($_GET);
    $qrysrui = queryGet("SELECT loc.storage_location_id, loc.storage_location_code, loc.storage_location_name, loc.storage_location_type, loc.storageLocationTypeSlug, warh.warehouse_id, warh.warehouse_code, warh.warehouse_name FROM erp_storage_location AS loc LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id WHERE loc.storage_location_storage_type!='Reserve' AND loc.company_id=$company_id", true);
    $sldattaqe = $qrysrui['data'];

    // $qtyObj = $BranchSoObj->deliveryCreateItemQty($getItemObj['data']['itemId']);
    $qtyObj = itemQtyStockChecking($itemId, "'rmWhOpen', 'fgWhOpen'", "DESC", '', $invoiceDate);

    // console($qtyObj);
    $sumOfBatches = $qtyObj['sumOfBatches'];
    $batchesDetails = convertToWHSLBatchArrayCommon($qtyObj['data']);
    // console($itemQtyStockCheck);

    // console($qtyObj);
    // console($batchesDetails);
    foreach ($batchesDetails as $whKey => $wareHouse) {
?>
        <style>
            input.red-placeholder {
                color: red;
                /* Text color */
                border: 1px solid red;
                /* Border color */
            }
        </style>
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
                                                         $batchStatus = checkBatchStatus($batchno, $company_id, $branch_id, $location_id,$batch['refActivityName']);

                                                        $disbaledstatus = $batchStatus['disabled'];
                                                        $status = $batchStatus['status'];
                                                        $placeholderText = $batchStatus['placeholderText'];
                                                        $placeholderClass = $batchStatus['placeholderClass'];

                                                        $uomName = getUomDetail($batch['itemUom'])['data']['uomName'];
                                                    ?>
                                                        <div class="storage-location mb-2">
                                                            <div class="input-radio">
                                                                <?php if ($batch['itemQty'] > 0) { ?>
                                                                    <input type="checkbox" <?= $disbaledstatus ?> name="item[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                <?php } else { ?>
                                                                    <input type="checkbox" <?= $disbaledstatus ?> name="item[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" disabled>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="d-grid">
                                                                <p class="text-sm mb-2">
                                                                    <?= $batch['logRef'] ?>
                                                                </p>
                                                                <p class="text-xs mb-2 font-bold batchItemQty inputQuantityClass" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                    <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= inputQuantity($batch['itemQty']) ?> <?= $uomName ?> </span>
                                                                </p>
                                                            </div>
                                                            <div class="input">
                                                                <?php if ($batch['itemQty'] > 0) { ?>
                                                                    <input step="0.01" <?= $disbaledstatus ?> type="number" name="item[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>">
                                                                <?php } else { ?>
                                                                    <input step="0.01" <?= $disbaledstatus ?> type="number" name="item[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="<?= $placeholderClass ?> form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" placeholder="<?= $placeholderText ?>" disabled>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

<?php
} ?>