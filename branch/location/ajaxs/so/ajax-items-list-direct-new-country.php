<?php
require_once("../../../../app/v1/connection-branch-admin.php");
require_once("../../../../app/v1/functions/branch/func-items-controller.php");
// require_once("../../../../app/v1/functions/branch/func-brunch-so-controller.php");
require_once(BASE_DIR . "app/v1/functions/branch/func-brunch-so-controller.php");
require_once("../../../../app/v1/functions/branch/func-discount-controller.php");

$headerData = array('Content-Type: application/json');
$responseData = [];

$ItemsObj = new ItemsController();
$BranchSoObj = new BranchSo();
$discountObj = new CustomerDiscountGroupController();
if ($_GET['act'] === "listItem") {
   //  console($_GET);
    $itemId = $_GET['itemId'];
    $items = $_GET['items'];
    $type = $_GET['type'];
    $invoicedate = $_GET['invoicedate'];
    $compInvoiceType = $_GET['compInvoiceType'] ?? 'R';
    $customer_id = $_GET['customer_id'];

    $value = base64_decode($_GET['valueId']);

    $itemMRP = $ItemsObj->mrp($customer_id, $itemId);
    // console($items);
    if ($type === "?so_to_invoice" || $type === "?edit_so") {
        $itemMRP = $items["unitPrice"];
    }
    if ($type === "?quotation" || $type === "?proforma_to_invoice") {
        $itemMRP = $items["unitPrice"];
    }

    $itemQty = $items['qty'] ?? 0;
    $totalDiscount = $items['totalDiscount'] ?? 0;
    $discountVariantId = $items['discountVariantId'] ?? 0;
    $tolerance = $items['tolerance'] ? $items['tolerance'] : 0;

    $unitPrice = $items['unitPrice'] ?? 0;

    $getItemObj = $ItemsObj->getItemById($itemId);
    $getItemSummaryObj = $BranchSoObj->fetchItemSummaryDetails($itemId)['data'][0];

    $itemTargetPrice = $getItemSummaryObj["itemPrice"];

    // console(["itemTargetPrice"=> $itemTargetPrice]);
    // console($getItemObj);
    $company = $BranchSoObj->fetchCompanyDetails()['data'];
    $fetchCurrency = $BranchSoObj->fetchCurrencyIcon($company['company_currency'])['data'];
    $currencyIcon = $fetchCurrency['currency_icon'];
    $currencyName = $fetchCurrency['currency_name'];

    $randCode = $getItemObj['data']['itemId'] . rand(00, 99);

    // print_r($getItemObj);

    $itemPriceObj = $BranchSoObj->fetchBranchSoItemPriceDetails($getItemObj['data']['itemCode'])['data'][0];
    $goodsType = $getItemObj['data']['goodsType'];
    // $itemUnitPrice = $itemPriceObj['ItemPrice'] ?? 0;
    $itemRemarks = $items['itemRemarks'] ?? '';

    $tradeDiscountPercentage = 0;
    $cashDiscountPercentage = 0;

    $discountVariantId = $items['discountVariantId'] ?? 0;
    $cashDiscountType = $items['cashDiscountType'] ?? "";
    if ($type === '?so_to_invoice' || $type === "?edit_so" || $type === "?quotation") {
        $tradeDiscountPercentage = $items['totalDiscount'] ?? 0;
        $cashDiscountPercentage = $items['cashDiscountPercentage'] ?? 0;
        if ($type === "?quotation") {
            $cashDiscountPercentage = $items['cashDiscount'] ?? 0;
        }
    }
    $itemUnitPrice = 0;
    if ($type === "?quotation_to_so" || $type === "?quotation" || $type === "?joborder_to_invoice" || $type === "?so_to_invoice" || $type === "?party_order_to_so" || $type === "?party_order_to_quotation" || $type === "?pgi_to_invoice" || $type === "?edit_so" || $type === "?proforma_to_so" || $type === "?proforma_to_invoice") {
        $itemUnitPrice = $items['unitPrice'] ?? 0;
        $invoiceQty = $items['completion_value'] ?? 0;
        $remainingQty = $items['remainingQty'];
    } else if ($type === "?repost_invoice") {
        $itemUnitPrice = $items['unitPrice'] ?? 0;
        $invoiceQty = 0;
        $remainingQty = 0;
    } else {
        $itemUnitPrice = $getItemSummaryObj['itemPrice'] ?? 0;
    }

    // $itemMaxDiscount = $itemPriceObj['ItemMaxDiscount'] ?? 0;
    $itemMaxDiscount = $getItemSummaryObj['itemMaxDiscount'] ?? 0;

    $get_state = queryGet("SELECT * FROM `erp_companies` WHERE company_id = $company_id");
   // console($get_state);
    // exit();

    $hsnInfo = $BranchSoObj->fetchHsnDetails($getItemObj['data']['hsnCode'])['data'][0];
    $itemTaxPercentage = $hsnInfo['taxPercentage'];
    $itemTaxPercentageBkup = $hsnInfo['taxPercentage'];

    if($get_state['data']['company_state'] == NULL || $get_state['data']['company_state'] == ' ') {
       // echo 'not india';
        $itemTaxPercentageBkup = 0;
        $itemTaxPercentage = 0;

    }
    else{
       // echo 'india';

        
    if ($compInvoiceType === "CBW" || $compInvoiceType === "LUT" || $compInvoiceType === "SEWOP" || $compInvoiceType === "E") {
        $itemTaxPercentageBkup = $hsnInfo['taxPercentage'];
        $itemTaxPercentage = 0;
    } else {
        $itemTaxPercentageBkup = $hsnInfo['taxPercentage'];
        $itemTaxPercentage = $hsnInfo['taxPercentage'];
    }

    }
  
        



    $itemBasePrice = $itemUnitPrice * ($itemQty ?? 0);
    $itemTotalPrice = $items['totalPrice'] ?? 0;
    $itemTotalTax = $items['totalTax'] ?? 0;
    $itemTotalDiscount = $items['itemTotalDiscount'] ?? 0;
    $itemTotalCashDiscount = 0;

    if ($type === "?joborder_to_invoice") {
        $itemBasePrice = $items['unitPrice'] * ($items['completion_value'] ?? 0);
        $itemTotalTax = $itemBasePrice * $items['tax'] / 100;
        $itemTotalDiscount = $itemBasePrice * $items['totalDiscount'] / 100;
        $itemTotalPrice = $itemBasePrice + $itemTotalTax - $itemTotalDiscount;
    } else {
        $itemBasePrice = $itemUnitPrice * ($itemQty ?? 0);
        $itemTotalPrice = $items['totalPrice'] ?? 0;
        $itemTotalTax = $items['totalTax'] ?? 0;
        $itemTotalDiscount = $items['itemTotalDiscount'] ?? 0;
    }
    $masterItemDetails = $getItemSummaryObj;
?>
    <tr class="rowDel itemRow" id="delItemRowBtn_<?= $getItemObj['data']['itemId'] ?>_<?= $randCode ?>" data-id="<?= $getItemObj['data']['goodsType'] ?>">

        <input class="form-control full-width" id="itemId_<?= $randCode ?>" type="hidden" name="listItem[<?= $randCode ?>][itemId]" value="<?= $getItemObj['data']['itemId'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][invStatus]" value="<?= $getItemObj['data']['invStatus'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemDesc]" value="<?= $getItemObj['data']['itemDesc'] ?>">
        <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][parentGlId]" value="<?= $getItemObj['data']['parentGlId'] ?>">
        <input class="form-control full-width goodsType" type="hidden" name="listItem[<?= $randCode ?>][goodsType]" value="<?= $getItemObj['data']['goodsType'] ?>">
        <?php if ($getItemObj['data']['goodsType'] == 4) {
        ?>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][goodsMainPrice]" value="<?= $getItemSummaryObj['movingWeightedPrice']; ?>">
        <?php } else {
            $goodsMainPrice = 0;
            $getItemBomDetailObj = $ItemsObj->getItemBomDetail($itemId);
            $pricetype = strtolower($getItemBomDetailObj['data']['bomProgressStatus']);
            $goodsMainPrice = $getItemBomDetailObj['data'][$pricetype] ?? 0;
        ?>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][goodsMainPrice]" value="<?= $goodsMainPrice; ?>">
        <?php } ?>

        <!-- ************************* start pos invoice -->
        <?php if ($type === "?pos-invoice") { ?>
            <td>
                <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
                <?= $getItemObj['data']['itemName'] ?>
                <i style="cursor: pointer;" class="fa fa-pen toggleServiceRemarksPen" id="toggleServiceRemarksPen_<?= $randCode ?>"></i>
                <textarea style="display:none" name="listItem[<?= $randCode ?>][itemRemarks]" class="form-control itemRemarks" id="itemRemarks_<?= $randCode ?>" cols="20" rows="3" placeholder="Remarks"><?= $itemRemarks ?></textarea>
            </td>
            <td class="inp-td" style="min-width: 40px !important;">
                <div class="d-flex">
                    <span class="rupee-symbol currency-symbol pr-1"><?= $currencyName ?> </span>
                    <input type="hidden" name="listItem[<?= $randCode ?>][unitPrice]" class="inp-design full-width-center originalItemUnitPriceInp" id="originalItemUnitPriceInp_<?= $randCode ?>" value="<?php echo  $itemMRP; ?>">
                    <!-- this value should be inserted in DB 👇🏾 -->
                    <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" class="inp-design text-right full-width-center originalChangeItemUnitPriceInp" id="originalChangeItemUnitPriceInp_<?= $randCode ?>" value="<?php echo  $itemMRP; ?>">
                </div>
                <div style="font-size: 0.8em;" class="float-right font-weight-bold text-primary convertedDiv" id="convertedDiv_<?= $randCode ?>">
                    <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemUnitPriceSpan" id="convertedItemUnitPriceSpan_<?= $randCode ?>"><?php echo  $itemMRP; ?></span>)</strong>
                </div>
            </td>
        <?php } ?>
        <!-- ************************* end pos invoice -->
        <td>
            <input class="form-control full-width" type="hidden" name="listItem[<?= $randCode ?>][itemCode]" value="<?= $getItemObj['data']['itemCode'] ?>">
            <?= $getItemObj['data']['itemCode'] ?>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][itemName]" value="<?= $getItemObj['data']['itemName'] ?>">
            <p style="white-space: pre-wrap;"><?= $getItemObj['data']['itemName'] ?></p>
            <i style="cursor: pointer;" class="fa fa-pen toggleServiceRemarksPen" id="toggleServiceRemarksPen_<?= $randCode ?>"></i>
            <textarea style="display:none" name="listItem[<?= $randCode ?>][itemRemarks]" class="form-control itemRemarks" id="itemRemarks_<?= $randCode ?>" cols="20" rows="3" placeholder="Remarks"><?= $itemRemarks ?></textarea>
        </td>
        <td>
            <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][hsnCode]" value="<?= $getItemObj['data']['hsnCode'] ?>">
            <?= $getItemObj['data']['hsnCode'] ?>
            <i class="fa fa-info-circle" style="cursor: pointer;" title="<?= $hsnInfo['hsnDescription'] ?>"></i>
        </td>
        <?php if ($type === "?sales_order_creation" || $type === "?quotation_creation" || $type === "?quotation_to_so" || $type === "?quotation" || $type === "?party_order_to_so" || $type === "?party_order_to_quotation" || $type === "?proforma_invoice" || $type === "?edit_so" || $type === "?proforma_to_so" || $type === "?proforma_to_invoice") { ?>
            <td class="text-center">
                <?php
                if ($goodsType == 5) { ?>
                    <small class="text-xs">Not Required</small>
                <?php } else {
                    $qtyObj = $BranchSoObj->itemQtyStockCheck($getItemObj['data']['itemId'], "'rmWhOpen', 'fgWhOpen'", "DESC");
                    echo $sumOfBatches = $qtyObj['sumOfBatches'];
                }
                ?>
            </td>
        <?php } else if ($type == '?pgi_to_invoice') { ?>
            <td class="text-center">
                <?php
                $pgiCode = $_GET['othersdata'];
                $qtyObj = $BranchSoObj->itemQtyStockCheck($getItemObj['data']['itemId'], "'fgMktOpen'", "DESC", "", $invoicedate);
                echo $sumOfBatches = $qtyObj['sumOfBatches'] . ' ';
                echo $uomName = getUomDetail($getItemObj['data']['baseUnitMeasure'])['data']['uomName'];
                ?>
                <input type="hidden" name="listItem[<?= $randCode ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $randCode ?>" value="<?= $sumOfBatches; ?>">
                <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $randCode ?>" name="listItem[<?= $randCode ?>][itemSellType]" value="FIFO">
                <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">
            </td>
        <?php } else if ($goodsType == 5) { ?>
            <td>
                <p>Not Required</p>
            </td>
        <?php } else { ?>
            <td>
                <div class="d-flex">
                    <?php if ($type === '?pgi_to_invoice') {
                        $pgiCode = $_GET['othersdata'];
                        $qtyObj = $BranchSoObj->itemQtyStockCheck($getItemObj['data']['itemId'], "'fgMktOpen'", "DESC", $pgiCode, $invoicedate);
                        echo $sumOfBatches = $qtyObj['sumOfBatches'] . ' ';
                        echo $uomName = getUomDetail($getItemObj['data']['baseUnitMeasure'])['data']['uomName'];
                    ?>
                        <input type="hidden" name="listItem[<?= $randCode ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $randCode ?>" value="<?= $sumOfBatches; ?>">
                        <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $randCode ?>" name="listItem[<?= $randCode ?>][itemSellType]" value="FIFO">
                        <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">
                    <?php } else { ?>
                        <?php

                        $qtyObj = $BranchSoObj->itemQtyStockCheck($getItemObj['data']['itemId'], "'rmWhOpen', 'fgWhOpen'", "DESC", '', $invoicedate);

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
                                <p class="itemSellType" id="itemSellType_<?= $randCode ?>"><?= $masterItemDetails['item_sell_type'] ?></p>
                                <ion-icon name="create-outline" class="stockBtn" id="stockBtn_<?= $randCode ?>" data-bs-toggle="modal" data-bs-target="#stockSetup<?= $randCode ?>" style="cursor: pointer;"></ion-icon>
                            </div>
                        </div>
                        <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $randCode ?>" name="listItem[<?= $randCode ?>][itemSellType]" value="<?= $masterItemDetails['item_sell_type'] ?>">

                        <!-- The Modal -->
                        <div class="modal fade stock-setup-modal" id="stockSetup<?= $randCode ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header" style="background: #003060; color: #fff;">
                                        <h4 class="modal-title text-sm text-white">Stock Setup (<?= $masterItemDetails['item_sell_type'] ?>)</h4>
                                        <p class="text-xs my-2 ml-5">Total Picked Qty :
                                            <span class="font-bold itemSelectTotalQty" id="itemSelectTotalQty_<?= $randCode ?>">0</span>
                                        </p>

                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">

                                        <!-- start warehouse accordion -->
                                        <div class="modal-select-type my-3">
                                            <div class="type type-one">
                                                <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass fifo" data-rdcode="<?= $randCode ?>" value="FIFO" id="fifo_<?= $randCode ?>" <?= ($masterItemDetails['item_sell_type'] == "FIFO") ? "checked" : ""; ?>>
                                                <label for="fifo" class="text-xs mb-0">FIFO</label>
                                            </div>
                                            <div class="type type-two">
                                                <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass lifo" data-rdcode="<?= $randCode ?>" value="LIFO" id="lifo_<?= $randCode ?>" <?= ($masterItemDetails['item_sell_type'] == "LIFO") ? "checked" : "" ?>>
                                                <label for="lifo" class="text-xs mb-0">LIFO</label>
                                            </div>
                                            <div class="type type-three">
                                                <input type="radio" name="listItem[<?= $randCode ?>][itemreleasetype]" class="itemreleasetypeclass custom" data-rdcode="<?= $randCode ?>" value="CUSTOM" id="custom_<?= $randCode ?>">
                                                <label for="custom" class="text-xs mb-0 text-muted">Custom</label>
                                            </div>
                                        </div>

                                        <div class="customitemreleaseDiv<?= $randCode ?>" style="display: none;">
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
                                                                                                            <input type="checkbox" name="listItem[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                        <?php } else { ?>
                                                                                                            <input type="checkbox" name="listItem[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" disabled>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                    <div class="d-grid">
                                                                                                        <p class="text-sm mb-2">
                                                                                                            <?= $batch['logRef'] ?>
                                                                                                        </p>
                                                                                                        <p class="text-xs mb-2 font-bold batchItemQty" id="batchItemQty_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                                                                            <span class="text-xs font-italic d-block"><?= formatDateTime($batch['bornDate']) ?> || <?= $batch['itemQty'] ?> <?= $uomName ?> </span>
                                                                                                        </p>
                                                                                                    </div>
                                                                                                    <div class="input">
                                                                                                        <?php if ($batch['itemQty'] > 0) { ?>
                                                                                                            <input step="any" type="number" name="listItem[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>">
                                                                                                        <?php } else { ?>
                                                                                                            <input step="any" type="number" name="listItem[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class=" form-control ml-auto enterQty batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" disabled>
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
                        <input class="form-control" type="hidden" name="listItem[<?= $randCode ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">
                    <?php } ?>
                </div>
            </td>
        <?php } ?>
        <?php if ($type === "?so_to_invoice" || $type === "?quotation" || $type === "?quotation_to_so" || $type === "?party_order_to_so" || $type === "?party_order_to_quotation" || $type === "?pgi_to_invoice" || $type === "?edit_so" || $type === "?proforma_to_so" || $type === "?proforma_to_invoice") { ?>
            <td class="inp-td">
                <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $itemQty ?>" class="inp-design full-width itemInvQty" id="itemInvQty_<?= $randCode ?>" readonly>

                <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $getItemObj['data']['baseUnitMeasure'] ?>">
            </td>
        <?php } ?>
        <td class="inp-td">

            <div class="d-flex">
                <?php if ($goodsType == 5) { ?>
                    <?php if ($type == "?so_to_invoice" || $type == "?quotation_to_so" || $type == "?quotation" || $type == "?party_order_to_so" || $type == "?party_order_to_quotation" || $type == "?pgi_to_invoice"  || $type === "?edit_so" || $type === "?proforma_to_so" || $type === "?proforma_to_invoice") { ?>
                        <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="0" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>">
                        <?php
                        echo $uomName = getUomDetail($getItemObj['data']['service_unit'])['data']['uomName'];
                        // $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['service_unit'])['data']['uomName'] 
                        ?>
                        <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $getItemObj['data']['service_unit'] ?>">
                    <?php } else { ?>
                        <?php if ($type == "?joborder_to_invoice") { ?>
                            <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $invoiceQty ?>" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>" readonly>
                        <?php } else { ?>
                            <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="<?= $itemQty ?>" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>">
                        <?php } ?>
                        <?php
                        echo $uomName = getUomDetail($getItemObj['data']['service_unit'])['data']['uomName'];
                        //  $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['service_unit'])['data']['uomName'];
                        ?>
                        <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $getItemObj['data']['service_unit'] ?>">
                    <?php } ?>
                <?php } else { ?>
                    <input step="any" type="number" name="listItem[<?= $randCode ?>][qty]" value="0" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>">
                    <?php
                    echo $uomName = getUomDetail($getItemObj['data']['baseUnitMeasure'])['data']['uomName'];
                    //  $ItemsObj->getBaseUnitMeasureById($getItemObj['data']['baseUnitMeasure'])['data']['uomName'] ;
                    ?>
                    <input type="hidden" name="listItem[<?= $randCode ?>][uom]" value="<?= $getItemObj['data']['baseUnitMeasure'] ?>">
                <?php } ?>
            </div>
            <span style="display:none; font-size: .8em!important" class="text-danger qtyMsg" id="qtyMsg_<?= $randCode ?>">Please enter valid qty</span>
        </td>
        <?php if ($type === "?joborder_to_invoice") { ?>
            <!-- <td class="inp-td">
                <div class="d-flex">
                    <input type="hidden" value="<?= abs($remainingQty) ?>" class="inp-design full-width remainingQtyHidden" id="remainingQtyHidden_<?= $randCode ?>" readonly>
                    <input step="any" type="number" name="listItem[<?= $randCode ?>][remainingQty]" value="<?= abs($remainingQty) ?>" class="inp-design full-width remainingQty" id="remainingQty_<?= $randCode ?>" readonly>
                </div>
            </td>
            <td class="inp-td">
                <div class="d-flex">
                    <input step="any" type="number" name="listItem[<?= $randCode ?>][invoiceQty]" value="<?= $invoiceQty ?>" class="inp-design full-width itemQty" id="itemQty_<?= $randCode ?>" readonly>
                </div>
                <span style="display:none; font-size: .8em!important" class="text-danger invoiceQtyMsg" id="invoiceQtyMsg_<?= $randCode ?>">Please enter valid qty</span>
            </td> -->
        <?php } ?>
        <!-- Mrp -->
        <td class="inp-td">
            <div class="d-flex">
                <input step="any" type="number" name="listItem[<?= $randCode ?>][itemTargetPrice]" value="<?= $itemTargetPrice ?>" class="inp-design full-width itemTargetPrice" id="itemTargetPrice_<?= $randCode ?>" readonly>
            </div>
        </td>
        <!-- end mrp -->
        <!-- rate -->
        <td class="inp-td" style="min-width: 40px !important;">
            <div class="d-flex">
                <span class="rupee-symbol currency-symbol pr-1"><?= $currencyName ?> </span>
                <input type="hidden" name="listItem[<?= $randCode ?>][unitPrice]" class="inp-design full-width-center originalItemUnitPriceInp" id="originalItemUnitPriceInp_<?= $randCode ?>" value="<?php echo  $itemMRP; ?>">
                <!-- this value should be inserted in DB 👇🏾 -->
                <input type="text" name="listItem[<?= $randCode ?>][unitPrice]" class="inp-design text-right full-width-center originalChangeItemUnitPriceInp" id="originalChangeItemUnitPriceInp_<?= $randCode ?>" value="<?php echo  $itemMRP; ?>">
            </div>
            <div style="font-size: 0.8em;" class="float-right font-weight-bold text-primary convertedDiv" id="convertedDiv_<?= $randCode ?>">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemUnitPriceSpan" id="convertedItemUnitPriceSpan_<?= $randCode ?>"><?php echo  $itemMRP; ?></span>)</strong>
            </div>
        </td>
        <!-- end rate -->
        <td class="text-right" style="display: none;">
            <input type="hidden" name="listItem[<?= $randCode ?>][baseAmount]" value="" class="form-control full-width-center itemBaseAmountInp" id="itemBaseAmountInp_<?= $randCode ?>">
            <span class="itemBaseAmountSpan" id="itemBaseAmountSpan_<?= $randCode ?>">0</span>
            <div style="font-size: 0.8em;" class="font-weight-bold text-primary convertedDiv">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemBaseAmountSpan" id="convertedItemBaseAmountSpan_<?= $randCode ?>">0</span>)</strong>
            </div>
        </td>
        <td class="text-right">
            <input type="hidden" name="listItem[<?= $randCode ?>][itemTradeDiscountPercentage]" value="0" class="form-control full-width-center itemTradeDiscountPercentageInp" id="itemTradeDiscountPercentageInp_<?= $randCode ?>">

            <input type="hidden" name="listItem[<?= $randCode ?>][itemTradeDiscountName]" value="" class="form-control full-width-center itemTradeDiscountName" id="itemTradeDiscountName_<?= $randCode ?>">


            <input type="hidden" name="listItem[<?= $randCode ?>][itemTradeDiscountAmount]" value="0" class="form-control full-width-center itemTradeDiscountAmountInp" id="itemTradeDiscountAmountInp_<?= $randCode ?>">
            <div style="border-bottom: 1px solid #ccc; font-size: 0.8em;">
                <span class="itemTradeDiscountPercentageSpan" id="itemTradeDiscountPercentageSpan_<?= $randCode ?>"><?= $tradeDiscountPercentage ?></span> %
            </div>
            <div>
                <span class="itemTradeDiscountAmountSpan" id="itemTradeDiscountAmountSpan_<?= $randCode ?>">0.00</span>
            </div>
            <div style="border-top: 1px solid #ccc; font-size: 0.8em;" class="float-right font-weight-bold text-primary convertedDiv" id="convertedDiv_<?= $randCode ?>">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemTradeDiscountAmountSpan" id="convertedItemTradeDiscountAmountSpan_<?= $randCode ?>">0.00</span>)</strong>
            </div>
        </td>
        <td class="text-right">
            <div>
                <span class="itemGrossAmountSpan" id="itemGrossAmountSpan_<?= $randCode ?>">0</span>
            </div>
            <div style="border-top: 1px solid #ccc; font-size: 0.8em;" class="float-right font-weight-bold text-primary convertedDiv" id="convertedDiv_<?= $randCode ?>">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedGrossAmountSpan" id="convertedGrossAmountSpan_<?= $randCode ?>">0.00</span>)</strong>
            </div>
        </td>
        <td class="text-right">
            <input type="hidden" name="listItem[<?= $randCode ?>][cashDiscountPercentage]" value="0" class="form-control full-width-center itemCashDiscountPercentageInp" id="itemCashDiscountPercentageInp_<?= $randCode ?>">
            <input type="hidden" name="listItem[<?= $randCode ?>][cashDiscountAmount]" value="0" class="itemCashDiscountAmountHiddenInp" id="itemCashDiscountAmountHiddenInp_<?= $randCode ?>">
            <div style="border-bottom: 1px solid #ccc; font-size: 0.8em;">
                <span class="itemCashDiscountPercentageSpan" id="itemCashDiscountPercentageSpan_<?= $randCode ?>"><?= $cashDiscountPercentage ?></span> %
            </div>
            <span class="itemCashDiscountAmountSpan" id="itemCashDiscountAmountSpan_<?= $randCode ?>">0.00</span>
            <div style="border-top: 1px solid #ccc; font-size: 0.8em;" class="float-right font-weight-bold text-primary convertedDiv" id="convertedDiv_<?= $randCode ?>">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedCashDiscountAmountSpan" id="convertedCashDiscountAmountSpan_<?= $randCode ?>">0.00</span>)</strong>
            </div>
        </td>
    <?php

if($get_state['data']['company_state'] == NULL || $get_state['data']['company_state'] == ' ') {
?>

<td class="text-right" style="display:none;">
            <div style="border-bottom: 1px solid #ccc; font-size: 0.8em;">
                <span class="itemTaxableAmountSpan" id="itemTaxableAmountSpan_<?= $randCode ?>"></span>
            </div>
            <div style="border-top: 1px solid #ccc; font-size: 0.8em;" class="float-right font-weight-bold text-primary convertedDiv" id="convertedDiv_<?= $randCode ?>">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedTaxableAmountSpan" id="convertedTaxableAmountSpan_<?= $randCode ?>">0.00</span>)</strong>
            </div>
        </td>
        <td style="display:none;">
            <input class="form-control itemTaxBkup" id="itemTaxBkup_<?= $randCode ?>" type="hidden" name="listItem[<?= $randCode ?>][taxBkup]" value="<?= $itemTaxPercentageBkup ?>">

            <input class="form-control itemTax" id="itemTax_<?= $randCode ?>" type="hidden" name="listItem[<?= $randCode ?>][tax]" value="<?= $itemTaxPercentage ?>">
            <span class="itemTaxPercentage" id="itemTaxPercentage_<?= $randCode ?>"><?= $itemTaxPercentage ?></span>%
        </td>
        <td class="text-right" style="display:none;">
            <input type="hidden" name="listItem[<?= $randCode ?>][itemTotalTax1]" value="0" class="form-control full-width-center itemTotalTax1" id="itemTotalTax1_<?= $randCode ?>" readonly>
            <span class="rupee-symbol currency-symbol"><?= $currencyName ?></span>
            <span class="itemTotalTax" id="itemTotalTax_<?= $randCode ?>"> 0</span>
            <div style="font-size: 0.8em;" class="font-weight-bold text-primary convertedDiv">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemTaxAmountSpan" id="convertedItemTaxAmountSpan_<?= $randCode ?>">0</span>)</strong>
            </div>
        </td>


<?php
    

}
else{


    
    ?>
        <td class="text-right">
            <div style="border-bottom: 1px solid #ccc; font-size: 0.8em;">
                <span class="itemTaxableAmountSpan" id="itemTaxableAmountSpan_<?= $randCode ?>"></span>
            </div>
            <div style="border-top: 1px solid #ccc; font-size: 0.8em;" class="float-right font-weight-bold text-primary convertedDiv" id="convertedDiv_<?= $randCode ?>">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedTaxableAmountSpan" id="convertedTaxableAmountSpan_<?= $randCode ?>">0.00</span>)</strong>
            </div>
        </td>
        <td>
            <input class="form-control itemTaxBkup" id="itemTaxBkup_<?= $randCode ?>" type="hidden" name="listItem[<?= $randCode ?>][taxBkup]" value="<?= $itemTaxPercentageBkup ?>">

            <input class="form-control itemTax" id="itemTax_<?= $randCode ?>" type="hidden" name="listItem[<?= $randCode ?>][tax]" value="<?= $itemTaxPercentage ?>">
            <span class="itemTaxPercentage" id="itemTaxPercentage_<?= $randCode ?>"><?= $itemTaxPercentage ?></span>%
        </td>
        <td class="text-right">
            <input type="hidden" name="listItem[<?= $randCode ?>][itemTotalTax1]" value="0" class="form-control full-width-center itemTotalTax1" id="itemTotalTax1_<?= $randCode ?>" readonly>
            <span class="rupee-symbol currency-symbol"><?= $currencyName ?></span>
            <span class="itemTotalTax" id="itemTotalTax_<?= $randCode ?>"> 0</span>
            <div style="font-size: 0.8em;" class="font-weight-bold text-primary convertedDiv">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemTaxAmountSpan" id="convertedItemTaxAmountSpan_<?= $randCode ?>">0</span>)</strong>
            </div>
        </td>
        <?php
}

?>
        <td class="text-right">
            <input type="hidden" name="listItem[<?= $randCode ?>][totalPrice]" value="0" class="form-control full-width-center itemTotalPrice" id="itemTotalPrice_<?= $randCode ?>" readonly>
            <div class="text-success">
                <span class="rupee-symbol currency-symbol"><?= $currencyName ?></span>
                <span class="font-weight-bold itemTotalPrice1" id="itemTotalPrice1_<?= $randCode ?>">
                    0
                </span>
            </div>
            <div style="font-size: 0.8em;" class="font-weight-bold text-primary convertedDiv">
                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemTotalPriceSpan" id="convertedItemTotalPriceSpan_<?= $randCode ?>">0</span>)</strong>
            </div>
        </td>
        <td class="action-flex-btn">
            <button type="button" class="btn-view btn btn-primary delShedulingBtn" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $randCode ?>">
                <i id="statusItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="statusItemBtn fa fa-cog"></i>
            </button>

            <button type="button" class="btn btn-danger delItemBtn">
                <i class="fa fa-minus" id="delItemBtn_<?= $getItemObj['data']['itemId'] ?>" class="delItemBtn mx-1 fa fa-minus"></i>
            </button>

            <div class="modal modal-left left-item-modal fade deliveryScheduleModal discountViewModal discountViewModal_<?= $randCode ?>" id="deliveryScheduleModal_<?= $randCode ?>" tabindex="-1" role="dialog" aria-labelledby="left_modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= $getItemObj['data']['itemName'] ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="text-white">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body discountViewBody_<?= $randCode ?>">
                            <h6 class="modal-title">Total Amount: <span class="totalItemAmountModal" id="totalItemAmountModal_<?= $randCode ?>">1</span></h6>
                            <nav class="delivery-discount">
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link delivery-navLink active" id="nav-delivery-tab" data-bs-toggle="tab" data-bs-target="#nav-delivery-<?= $randCode ?>" type="button" role="tab" aria-controls="nav-delivery" aria-selected="true">Discount</button>
                                    <button class="nav-link cash-discount-navLink" id="nav-cash-discount-tab" data-bs-toggle="tab" data-bs-target="#nav-cash-discount-<?= $randCode ?>" type="button" role="tab" aria-controls="nav-cash-discount" aria-selected="true">Cash Discount</button>
                                    <button class="nav-link discount-navLink" id="nav-discount-tab" data-bs-toggle="tab" data-bs-target="#nav-discount-<?= $randCode ?>" type="button" role="tab" aria-controls="nav-discount" aria-selected="false">Delivery</button>
                                </div>
                            </nav>
                            <div class="tab-content delivery-discount-tabContent" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-delivery-<?= $randCode ?>" role="tabpanel" aria-labelledby="nav-delivery-tab">
                                    <div class="row">
                                        <input class="form-control full-width randClass" id="randClass_<?= $randCode ?>" type="hidden" value="<?= $randCode ?>">
                                        <?php
                                        $data = array(
                                            array(
                                                'discount_type' => 'percentage',
                                                'discount_percentage' => 10,
                                                'discount_value' => 4000,
                                                'discount_max_value' => 100
                                            ),
                                            array(
                                                'discount_type' => 'value',
                                                'discount_percentage' => 10,
                                                'discount_value' => 9000,
                                                'discount_max_value' => 100
                                            )
                                        );
                                        ?>
                                        <div class="col-lg-12 col-md-12 col-sm-12 discountViewD_<?= $randCode ?>">
                                            <div class="discount-view discountView_<?= $randCode ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <label>Discount (%)</label>
                                            <input type="hidden" id="itemDiscountMax_<?= $randCode ?>" value="0">
                                            <input type="hidden" name="listItem[<?= $randCode ?>][itemDiscountVarientId]" id="itemDiscountVarientId_<?= $randCode ?>" value="0">
                                            <input type="hidden" id="previousDiscountVarientId_<?= $randCode ?>" value="<?= $discountVariantId > 0 ? $discountVariantId : 0 ?>">
                                            <div class="d-flex">
                                                <span class="rupee-symbol symbol pr-1"><?= '%' ?> </span>
                                                <input type="number" step="any" name="listItem[<?= $randCode ?>][totalDiscount]" value="<?= $totalDiscount ?>" class="form-control itemDiscount" id="itemDiscount_<?= $randCode ?>" readonly>
                                                <small class="maxLimitStyle" style="display: none;">Max <br><?php if ($itemMaxDiscount == 0) { ?> <span class="text-danger">0</span> <?php } else { ?> <strong class="itemMaxDiscount" id="itemMaxDiscount_<?= $randCode ?>"><?= $itemMaxDiscount ?></strong> <?php } ?>%</small>
                                            </div>
                                            <div style="font-size: .5em; display: none;" class="mt-2 text-dark itemSpecialDiscount" id="itemSpecialDiscount_<?= $randCode ?>"></div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <label>Discount Amt</label>
                                            <div class="d-flex">
                                                <span class="rupee-symbol currency-symbol pr-1"><?= $currencyName ?> </span>
                                                <input type="hidden" value="<?= $itemTotalDiscount ?>" name="listItem[<?= $randCode ?>][itemTotalDiscount]" class="form-control itemTotalDiscountHidden" id="itemTotalDiscountHidden_<?= $randCode ?>">
                                                <input type="number" step="any" value="<?= $itemTotalDiscount ?>" name="listItem[<?= $randCode ?>][itemTotalDiscount1]" class="form-control itemTotalDiscount1" id="itemTotalDiscount1_<?= $randCode ?>" readonly>
                                                <span class="itemTotalDiscount" style="display: none;" id="itemTotalDiscount_<?= $randCode ?>"><?= $itemTotalDiscount ?></span>
                                            </div>
                                            <div style="font-size: 0.8em;" class="font-weight-bold text-primary convertedDiv">
                                                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemDiscountAmountSpan" id="convertedItemDiscountAmountSpan_<?= $randCode ?>"><?= $itemTotalDiscount ?></span>)</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-cash-discount-<?= $randCode ?>" role="tabpanel" aria-labelledby="nav-cash-discount-tab">
                                    <div class="row mb-3 border-bottom">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div>
                                                <label for="grossAmountRadio_<?= $randCode ?>">Gross Amount</label>
                                                <input type="radio" name="listItem[<?= $randCode ?>][cashDiscountType]" data-cash-discount-type="grossAmount" class="grossAmountRadio cashDiscountTypeRadioBtn" <?= ($cashDiscountType != "baseAmount") ? "checked" : "" ?> id="grossAmountRadio_<?= $randCode ?>">
                                            </div>
                                            <div class="itemGrossAmountInCashDiscount" id="itemGrossAmountInCashDiscount_<?= $randCode ?>">0.00</div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div>
                                                <label for="baseAmountRadio_<?= $randCode ?>">Base Amount</label>
                                                <input type="radio" name="listItem[<?= $randCode ?>][cashDiscountType]" data-cash-discount-type="baseAmount" class="baseAmountRadio cashDiscountTypeRadioBtn" <?= ($cashDiscountType == "baseAmount") ? "checked" : "" ?> id="baseAmountRadio_<?= $randCode ?>">
                                            </div>
                                            <div class="itemBaseAmountInCashDiscount" id="itemBaseAmountInCashDiscount_<?= $randCode ?>">0.00</div>
                                        </div>
                                        <input type="hidden" name="listItem[<?= $randCode ?>][selectedCashDiscountType]" value="<?= $cashDiscountType ?>" id="selectedCashDiscountType_<?= $randCode ?>">
                                        <input type="hidden" value="" id="previousCashDiscountType_<?= $randCode ?>">
                                        <?php
                                        if ($cashDiscountType != "") { ?>
                                            <p class="pre-normal text-muted mt-1">In previous document the Cash discount was selected based on <b><i><?= ucfirst($cashDiscountType) ?></i></b> and the percentage was <b><i><?= $cashDiscountPercentage ?>%</i></b></p>
                                        <?php }
                                        ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <label>Cash Discount (%)</label>
                                            <div class="d-flex">
                                                <span class="rupee-symbol symbol pr-1"><?= '%' ?> </span>
                                                <input type="number" step="any" name="listItem[<?= $randCode ?>][totalCashDiscount]" value="<?= $totalCashDiscount ?>" class="form-control itemCashDiscount" id="itemCashDiscount_<?= $randCode ?>">
                                                <small class="maxLimitStyle" style="display: none;">Max <br><?php if ($itemMaxCashDiscount == 0) { ?> <span class="text-danger">0</span> <?php } else { ?> <strong class="itemMaxCashDiscount" id="itemMaxCashDiscount_<?= $randCode ?>"><?= $itemMaxCashDiscount ?></strong> <?php } ?>%</small>
                                            </div>
                                            <div style="font-size: .5em; display: none;" class="mt-2 text-dark itemSpecialCashDiscount" id="itemSpecialCashDiscount_<?= $randCode ?>"></div>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <label>Cash Discount Amt</label>
                                            <div class="d-flex">
                                                <span class="rupee-symbol currency-symbol pr-1"><?= $currencyName ?> </span>
                                                <input type="hidden" value="<?= $itemTotalCashDiscount ?>" name="listItem[<?= $randCode ?>][itemTotalCashDiscount]" class="form-control itemTotalCashDiscountHidden" id="itemTotalCashDiscountHidden_<?= $randCode ?>">
                                                <input type="number" step="any" value="<?= $itemTotalCashDiscount ?>" name="listItem[<?= $randCode ?>][itemTotalCashDiscount1]" class="form-control itemTotalCashDiscount1" id="itemTotalCashDiscount1_<?= $randCode ?>">
                                                <span class="itemTotalCashDiscount" style="display: none;" id="itemTotalCashDiscount_<?= $randCode ?>"><?= $itemTotalCashDiscount ?></span>
                                            </div>
                                            <div style="font-size: 0.8em;" class="font-weight-bold text-primary convertedDiv">
                                                <strong>(<span class="rupee-symbol currency-symbol-dynamic"><?= $currencyName ?> </span> <span class="convertedItemCashDiscountAmountSpan" id="convertedItemCashDiscountAmountSpan_<?= $randCode ?>"><?= $itemTotalCashDiscount ?></span>)</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-discount-<?= $randCode ?>" role="tabpanel" aria-labelledby="nav-discount-tab">
                                    <div class="row">
                                        <?php if ($type === "?sales_order_creation" || $type === "?quotation_to_so" || $type === "?party_order_to_so" || $type === "?party_order_to_quotation" || $type === "?proforma_to_so") { ?>
                                            <input class="form-control full-width randClass" id="randClass_<?= $randCode ?>" type="hidden" value="<?= $randCode ?>">
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="form-input">
                                                    <label>Tolerance (%)</label>
                                                    <input type="text" name="listItem[<?= $randCode ?>][tolerance]" class="form-control" id="location" placeholder="Tolerance (%)" value="<?= $tolerance ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                <div class="modal-add-row modal-add-row_<?= $randCode ?>">
                                                    <div class="row modal-cog-right">
                                                        <div class="col-lg-5 col-md-5 col-sm-5">
                                                            <div class="form-input">
                                                                <label>Delivery Date</label>
                                                                <input type="date" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][multiDeliveryDate]" class="form-control multiDeliveryDate" id="multiDeliveryDate_<?= $randCode ?>" placeholder="delivery date">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5 col-md-5 col-sm-5">
                                                            <div class="form-input">
                                                                <label>Quantity</label>
                                                                <input type="text" name="listItem[<?= $randCode ?>][deliverySchedule][<?= $randCode ?>][quantity]" class="form-control multiQuantity" data-itemid="<?= $randCode ?>" id="multiQuantity_<?= $randCode ?>" placeholder="quantity" value="<?= $itemQty ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                                            <a style="cursor: pointer" class="btn btn-primary addQtyBtn" id="addQtyBtn_<?= $randCode ?>" onclick='addMultiQty(<?= $randCode ?>)'>
                                                                <i class="fa fa-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
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