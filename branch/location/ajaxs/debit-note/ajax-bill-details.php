<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-items-controller.php");
$headerData = array('Content-Type: application/json');
$responseData = [];
$qrysrui = queryGet("SELECT loc.storage_location_id, loc.storage_location_code, loc.storage_location_name, loc.storage_location_type, loc.storageLocationTypeSlug, warh.warehouse_id, warh.warehouse_code, warh.warehouse_name FROM erp_storage_location AS loc LEFT JOIN erp_storage_warehouse AS warh ON loc.warehouse_id = warh.warehouse_id WHERE loc.storage_location_storage_type!='Reserve' AND loc.company_id=$company_id", true);
$sldattaqe = $qrysrui['data'];
?>
<thead>
  <tr>
    <th>Item Details</th>
    <th>Account</th>
    <th>Bill Qty</th>
    <th>Quantity</th>
    <th>Rate</th>
    <th style="width: 15%;">Tax</th>
    <th style="width: 10%;">Amount</th>
    <th>Action</th>
  </tr>
</thead>

<?php
if ($_GET['act'] === "bill") {

  $bill_id = $_GET['bill_id'];
  $attr = $_GET['attr'];
  if ($attr == 'inv') {
    $inv = queryGet("SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE `so_invoice_id` = $bill_id", true);
    // console($inv);
    $total = 0;

?>


    <tbody class="add-row inv_items">
      <?php
      foreach ($inv['data'] as $keyss => $data) {
        $rand = rand(100, 1000);
        $tax_amount = ($data['tax'] / 100 * $data['unitPrice']) * $data['qty'];
        $amount = ($data['qty'] * $data['unitPrice']) + $tax_amount;
        $total += $amount;

        $itemgl = queryGet("SELECT parentGlId,goodsType FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId='" . $data['inventory_item_id'] . "' AND company_id = '" . $company_id . "' ");

        $parentGlId = $itemgl['data']['parentGlId'];
        $goodsType = $itemgl['data']['goodsType'];

        $gl_sql = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `id` = $parentGlId");

      ?>

        <tr class="items_row" id="<?= $rand ?>">
          <td>
            <p class="pre-normal"><?= $data['itemCode'] . '[' . $data['itemName'] . ']' ?></p>
            <input type="hidden" value="<?= $data['itemCode'] ?>" name="item[<?= $rand ?>][item_code]">
            <input type="hidden" class="item_select  item_select_<?= $rand ?>" value="<?= $data['inventory_item_id'] . '_' . $goodsType  ?>" name="item[<?= $rand ?>][item_id]">
          </td>
          <td><?= $gl_sql['data']['gl_code'] ?> | <?= $gl_sql['data']['gl_label'] ?>
            <input type="hidden" value="<?= $parentGlId ?>" name="item[<?= $rand ?>][account]" class="form-control account_<?= $rand ?>">
          </td>
          
          <td class="text-right"><?= inputQuantity($data['qty']) ?></td>

          <td class="text-right">
            <div class="d-flex gap-2">

              <span class="custom_batch_<?= $rand ?> <?php if ($goodsType == 5 || $goodsType == 7) {
                                                        echo 'd-none';
                                                      } ?> ">
                <?php
                $itemId = $data['inventory_item_id'];
                $partyDebitDate = $_GET['partyDebitDate'] ?? date('Y-m-d');
                $randCode = $rand;

                // console($_GET);


                // $qtyObj = $BranchSoObj->deliveryCreateItemQty($getItemObj['data']['itemId']);
                $qtyObj = itemQtyStockChecking($itemId, "'rmWhOpen', 'fgWhOpen'", "DESC", '', $partyDebitDate, 1);

                // console($qtyObj);
                $sumOfBatches = $qtyObj['sumOfBatches'];
                $batchesDetails = convertToWHSLBatchArrayCommon($qtyObj['data']);
                // console($itemQtyStockCheck);

                // console($qtyObj);
                // console($batchesDetails);
                ?>

                <input type="hidden" name="item[<?= $rand ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $rand ?>" value="<?= $sumOfBatches; ?>">

                <!-- Button to Open the Modal -->
                <div class="qty-modal py-2">
                  <p class="font-bold text-center checkQtySpan" id="checkQtySpan_<?= $rand ?>"><?= inputQuantity($sumOfBatches); ?></p>
                  <hr class="my-2 w-50 mx-auto">
                  <div class="text-xs d-flex align-items-center gap-2 justify-content-center">
                    <p class="itemSellType" id="itemSellType_<?= $rand ?>">CUSTOM</p>
                    <ion-icon name="create-outline" class="stockBtn" id="stockBtn_<?= $rand ?>" data-bs-toggle="modal" data-bs-target="#stockSetup<?= $rand ?>" style="cursor: pointer;"></ion-icon>
                  </div>
                </div>
                <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $rand ?>" name="item[<?= $rand ?>][itemSellType]" value="CUSTOM">

                <!-- The Modal -->
                <div class="modal fade stock-setup-modal" id="stockSetup<?= $rand ?>">
                  <div class="modal-dialog">
                    <div class="modal-content">

                      <!-- Modal Header -->
                      <div class="modal-header" style="background: #003060; color: #fff;">
                        <h4 class="modal-title text-sm text-white">Stock Setup (CUSTOM)</h4>
                        <p class="text-xs my-2 ml-5">Total Picked Qty :
                          <span class="font-bold itemSelectTotalQty" id="itemSelectTotalQty_<?= $rand ?>"><?=inputQuantity(0)?></span>
                        </p>

                      </div>

                      <!-- Modal body -->
                      <div class="modal-body">

                        <!-- start warehouse accordion -->
                        <div class="modal-select-type my-3">
                          <div class="type type-three">
                            <input type="radio" name="item[<?= $rand ?>][itemreleasetype]" class="itemreleasetypeclass custom" data-rdcode="<?= $rand ?>" value="CUSTOM" id="custom_<?= $rand ?>" checked>
                            <label for="custom" class="text-xs mb-0 text-muted">Custom</label>
                          </div>
                        </div>

                        <div class="customitemreleaseDiv<?= $rand ?>">
                          <?php
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
                                                          <input type="checkbox" name="item[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                        <?php } else { ?>
                                                          <input type="checkbox" name="item[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
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
                                                          <input step="any" type="number" name="item[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="form-control ml-auto enterQty inputQuantityClass batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>">
                                                        <?php } else { ?>
                                                          <input step="any" type="number" name="item[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class=" form-control ml-auto enterQty inputQuantityClass batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>">
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
                <input class="form-control" type="hidden" id="checkQtyVal_<?= $rand ?>" name="item[<?= $rand ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">
              </span>

              <?php if ($goodsType == 5 || $goodsType == 7) { ?>

                <input type="number" step="any" name="item[<?= $rand ?>][qty]" class="form-control inputQuantityClass itemQty" id="itemQty_<?= $rand ?>" value="<?= inputQuantity($data['goodQty']) ?>">
              <?php } else { ?>
                <input type="number" step="any" name="item[<?= $rand ?>][qty]" class="form-control inputQuantityClass itemQty" id="itemQty_<?= $rand ?>" value="" <?php if (count($batchesDetails) > 0) {
                                                                                                                                      echo 'readonly';
                                                                                                                                    } ?>>
              <?php } ?>
            </div>

          </td>

          <td class="text-right"><input type="number" step="any" name="item[<?= $rand ?>][rate]" class="form-control price inputAmountClass" id="price_<?= $rand ?>" value="<?= inputValue($data['itemTargetPrice'] )?>"></td>
          <td>
            <div class="d-flex gap-2">
              <input type="number" step="any" class="form-control tax inputQuantityClass" name="item[<?= $rand ?>][tax]" id="tax_<?= $rand ?>" value="<?= inputQuantity($data['tax']) ?>">
              <span class="d-inline-block">%</span>
              <input type="hidden" class="form-control tax_amount" name="item[<?= $rand ?>][tax_amount]" id="tax_amount_<?= $rand ?>" value="<?= $tax_amount ?>">
            </div>


            <!-- <select name="" id="" class="form-control">
                                                            <option value="0">Select Account</option>
                                                            <option value="0">Select Account</option>
                                                            <option value="0">Select Account</option>
                                                        </select> -->
          </td>
          <td class="text-right amount" id="amount_<?= $rand ?>"><?= inputValue($amount) ?>
            <input type="hidden" value="<?= $amount ?>" id="amountHidden_<?= $rand ?>" name="item[<?= $rand ?>][amount]">
          </td>
          <td>
            <div class="btns-grp d-flex gap-2">
              <a style="cursor: pointer" class="btn btn-danger add-btn-minus-bill">
                <i class="fa fa-minus"></i>
              </a>
            </div>
          </td>
        </tr>





      <?php
      }

      ?>

    </tbody>
    <tr>
      <td colspan="5" class="text-right">SGST</td>
      <td colspan="2" class="text-right" id="sgst_span"></td>
      <input type="hidden" name="sgst" id="sgst" value='' />
    </tr>
    <tr>
      <td colspan="5" class="text-right">CGST</td>
      <td colspan="2" class="text-right" id="cgst_span"></td>
      <input type="hidden" name="cgst" id="cgst" value='' />
    </tr>
    <tr>
      <td colspan="5" class="text-right">IGST</td>
      <td colspan="2" class="text-right" id="igst_span"></td>
      <input type="hidden" name="igst" id="igst" value='' />
    </tr>
    <tr>
      <td colspan="5" class="text-right">Sub Total</td>
      <td colspan="2" class="text-right" id="subTotal"><?= inputValue($total) ?>
        <input type="hidden" id="subTotal" name="subTotal" value="<?= inputValue($total) ?>">

      </td>
    </tr>

    <tr>
      <td colspan="5"></td>
      <td>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 px-0">
            <div class="round-off-section p-0">
              <div class="round-off-head d-flex gap-2">
                <input type="checkbox" class="checkbox round_off_checkbox_inv" name="round_off_checkbox" id="round_off_checkbox_inv">
                <p class="text-xs" for="round_off_checkbox">Adjust Amount</p>
              </div>
              <div id="round_off_hide_inv" style="display:none;">
                <div class="row round-off calculte-input px-0">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="adjust-currency d-flex gap-2">
                      <select id="round_sign" name="round_sign" class="form-control text-center">
                        <option value="+">+</option>
                        <option value="-">-</option>
                      </select>
                      <input type="number" step="any" name="round_value" step="any" id="round_value" value="0" class="form-control text-center inputAmountClass">
                    </div>
                  </div>
                </div>
                <!-- <div class="row" style="width: 100%;">
                  <div class="col-lg-12 col-md-12 col-sm-12 px-0">
                    <div class="totaldueamount d-flex justify-content-between border-top border-white pt-2">
                      <p class="font-bold">Adjusted Amount</p>
                      <input type="hidden" name="paymentDetails[adjustedCollectAmount]" class="adjustedCollectAmountInp">
                      <p class="text-success font-bold rupee-symbol">₹ <span class="adjustedDueAmt">0</span></p>
                      <input type="hidden" name="paymentDetails[roundOffValue]" class="roundOffValueHidden">
                    </div>
                  </div>
                </div> -->
              </div>
              <script>
                $(document).ready(function() {
                  $('#round_off_checkbox_inv').on('click', function() {

                    var isChecked = $('#round_off_checkbox_inv').prop('checked');
                    if (isChecked) {
                      $('#round_off_hide_inv').show();
                    } else {
                      $('#round_value').val('0');
                      calculateAllItemsGrandAmount();
                      $('#round_off_hide_inv').hide();
                    }
                  })
                });
              </script>
            </div>
          </div>
        </div>
      </td>
      <td></td>
    </tr>

    <!-- <tr>
        <td colspan="4" class="text-right">Discount</td>
        <td class="td-flex">
            <input type="text" name="discount" class="form-control" id="discount" value="0"><span class="d-inline-block">%</span>
        </td>
        <td class="text-right" id="discountAmount">0.00
          
        </td>
    </tr> -->
    <!-- <tr>
        <td colspan="4" class="text-right">Adjustment</td> 
        <td class="td-flex">
            <input type="text" class="form-control"><span class="d-inline-block">?</span>
        </td>
        <td class="text-right">0.00</td>
    </tr> -->
    <tr>
      <td colspan="5" class="text-right font-bold"> Total</td>
      <td colspan="2" class="text-right font-bold" id="grandTotal"> <?= inputValue($total) ?>
      </td>
    </tr>
    <tr>
      <td><input type="hidden" id="grandTotalHidden" name="grandTotal" value="<?= inputValue($total) ?>"></td>
      <td> <input type="hidden" name="discountAmount" id="discountAmountHidden" class="form-control" value="0"></td>
      <td><input type="hidden" class="form-control" name="subTotal" id="subTotalHidden" value="<?= inputValue($total) ?>"></td>
    </tr>

  <?php
  } else {
    $grninv = queryGet("SELECT * FROM `erp_grninvoice` WHERE `grnIvId` = $bill_id");

    // console($grninv);
    $inv = queryGet("SELECT * FROM `erp_grninvoice_goods` as goods, `erp_hsn_code` as hsn WHERE hsn.`hsnCode` = goods.`goodHsn` AND goods.`grnIvId` = $bill_id", true);
    //console($inv);
    $total = 0;
  ?>

    <tbody class="add-row inv_items">
      <?php
      foreach ($inv['data'] as $keyss => $data) {
        $rand = rand(100, 1000);
        $tax_amount = ($data['taxPercentage'] / 100 * $data['unitPrice']) * $data['goodQty'];
        $amount = ($data['goodQty'] * $data['unitPrice']) + $tax_amount;
        $total += $amount;


        $itemgl = queryGet("SELECT parentGlId,goodsType,baseUnitMeasure FROM `" . ERP_INVENTORY_ITEMS . "` WHERE itemId='" . $data['goodId'] . "' AND company_id = '" . $company_id . "' ");

        $parentGlId = $itemgl['data']['parentGlId'];
        $goodsType = $itemgl['data']['goodsType'];
        $goodsUom = $itemgl['data']['baseUnitMeasure'];

        $gl_sql = queryGet("SELECT * FROM `" . ERP_ACC_CHART_OF_ACCOUNTS . "` WHERE `id` = $parentGlId");

      ?>

        <tr class="items_row" id="<?= $rand ?>">
          <td><?= $data['goodCode'] . '[' . $data['goodName'] . ']' ?>
            <input type="hidden" value="<?= $data['goodCode'] ?>" name="item[<?= $rand ?>][item_code]">
            <input type="hidden" class="item_select  item_select_<?= $rand ?>" value="<?= $data['goodId'] . '_' . $goodsType ?>" name="item[<?= $rand ?>][item_id]">

          </td>

          <td><?= $gl_sql['data']['gl_code'] ?> | <?= $gl_sql['data']['gl_label'] ?>
            <input type="hidden" value="<?= $parentGlId ?>" name="item[<?= $rand ?>][account]" class="form-control account_<?= $rand ?>">
          </td>

          <td class="text-right"><?= inputQuantity($data['receivedQty']) ?></td>

          <td class="text-right">
            <div class="d-flex">
              <span class="custom_batch_<?= $rand ?> <?php if ($goodsType == 5 || $goodsType == 7) {
                                                        echo 'd-none';
                                                      } ?> ">
                <?php
                $itemId = $data['goodId'];
                $partyDebitDate = $_GET['partyDebitDate'] ?? date('Y-m-d');
                $randCode = $rand;

                // console($_GET);


                // $qtyObj = $BranchSoObj->deliveryCreateItemQty($getItemObj['data']['itemId']);
                $qtyObj = itemQtyStockChecking($itemId, "'rmWhOpen', 'fgWhOpen'", "DESC", "'" . $grninv['data']['grnCode'] . $data['itemStorageLocation'] . "'", $partyDebitDate);

                // console($qtyObj);
                $sumOfBatches = $qtyObj['sumOfBatches'];
                $batchesDetails = convertToWHSLBatchArrayCommon($qtyObj['data']);
                // console($itemQtyStockCheck);

                // console($qtyObj);
                // console($batchesDetails);
                ?>

                <input type="hidden" name="item[<?= $rand ?>][stockQty]" class="form-control checkQty" id="checkQty_<?= $rand ?>" value="<?= $sumOfBatches; ?>">

                <!-- Button to Open the Modal -->
                <div class="qty-modal py-2">
                  <p class="font-bold text-center checkQtySpan" id="checkQtySpan_<?= $rand ?>"><?= inputQuantity($sumOfBatches); ?></p>
                  <hr class="my-2 w-50 mx-auto">
                  <div class="text-xs d-flex align-items-center gap-2 justify-content-center">
                    <p class="itemSellType" id="itemSellType_<?= $rand ?>">CUSTOM</p>
                    <ion-icon name="create-outline" class="stockBtn" id="stockBtn_<?= $rand ?>" data-bs-toggle="modal" data-bs-target="#stockSetup<?= $rand ?>" style="cursor: pointer;"></ion-icon>
                  </div>
                </div>
                <input type="hidden" class="itemSellTypeHidden" id="itemSellTypeHidden_<?= $rand ?>" name="item[<?= $rand ?>][itemSellType]" value="CUSTOM">

                <!-- The Modal -->
                <div class="modal fade stock-setup-modal" id="stockSetup<?= $rand ?>">
                  <div class="modal-dialog">
                    <div class="modal-content">

                      <!-- Modal Header -->
                      <div class="modal-header" style="background: #003060; color: #fff;">
                        <h4 class="modal-title text-sm text-white">Stock Setup (CUSTOM)</h4>
                        <p class="text-xs my-2 ml-5">Total Picked Qty :
                          <span class="font-bold itemSelectTotalQty" id="itemSelectTotalQty_<?= $rand ?>"><?=inputQuantity(0)?></span>
                        </p>

                      </div>

                      <!-- Modal body -->
                      <div class="modal-body">

                        <!-- start warehouse accordion -->
                        <div class="modal-select-type my-3">
                          <div class="type type-three">
                            <input type="radio" name="item[<?= $rand ?>][itemreleasetype]" class="itemreleasetypeclass custom" data-rdcode="<?= $rand ?>" value="CUSTOM" id="custom_<?= $rand ?>" checked>
                            <label for="custom" class="text-xs mb-0 text-muted">Custom</label>
                          </div>
                        </div>

                        <div class="customitemreleaseDiv<?= $rand ?>">
                          <?php
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
                                                          <input type="checkbox" name="item[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>">
                                                        <?php } else { ?>
                                                          <input type="checkbox" name="item[<?= $randCode ?>][batchselectionchekbox][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" class="batchCbox batchCheckbox<?= $batch['logRef'] ?>" id="batchCheckbox_<?= $whKey ?><?= $locationKey ?><?= $batchKey ?>" disabled>
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
                                                          <input step="any" type="number" name="item[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class="form-control ml-auto enterQty inputQuantityClass batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>">
                                                        <?php } else { ?>
                                                          <input step="any" type="number" name="item[<?= $randCode ?>][batchselection][<?= $batch['logRef'] . '_' . $locationKey . '_' . $whKey . $batchKey; ?>]" data-maxval="<?= $batch['itemQty'] ?>" data-rdcode="<?= $randCode . '|' . $batch['logRef']; ?>" class=" form-control ml-auto enterQty inputQuantityClass batchqty<?= $batch['logRef']; ?> qty<?= $randCode; ?>" id="enterQty_<?= $batch['logRef']; ?>" disabled>
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
                <input class="form-control" type="hidden" id="checkQtyVal_<?= $rand ?>" name="item[<?= $rand ?>][sumOfBatches]" value="<?= $sumOfBatches ?>">
              </span>
              <?php if ($goodsType == 5 || $goodsType == 7) { ?>

                <input type="number" step="any" name="item[<?= $rand ?>][qty]" class="form-control inputQuantityClass itemQty" id="itemQty_<?= $rand ?>" value="<?= inputQuantity($data['goodQty']) ?>">
              <?php } else { ?>
                <input type="number" step="any" name="item[<?= $rand ?>][qty]" class="form-control inputQuantityClass itemQty" id="itemQty_<?= $rand ?>" value="" <?php if (count($batchesDetails) > 0) {
                                                                                                                                      echo 'readonly';
                                                                                                                                    } ?>>
              <?php } ?>
            </div>

          </td>
          <td class="text-right"><input type="number" step="any" name="item[<?= $rand ?>][rate]" class="form-control inputAmountClass price" id="price_<?= $rand ?>" value="<?= inputValue($data['itemTargetPrice']) ?>"></td>
          <td>
            <div class="d-flex gap-2">
              <input type="number" step="any" class="form-control tax inputQuantityClass" name="item[<?= $rand ?>][tax]" id="tax_<?= $rand ?>" value="<?= inputQuantity($data['taxPercentage']) ?>">
              <span class="percent-position">%</span>
              <input type="hidden" class="form-control tax_amount" name="item[<?= $rand ?>][tax_amount]" id="tax_amount_<?= $rand ?>" value="<?= $tax_amount ?>">
            </div>

            <!-- <select name="" id="" class="form-control">
                                                            <option value="0">Select Account</option>
                                                            <option value="0">Select Account</option>
                                                            <option value="0">Select Account</option>
                                                        </select> -->
          </td>
          <td class="text-right amount" id="amount_<?= $rand ?>"><?= inputValue($amount) ?>
            <input type="hidden" value="<?= $amount ?>" id="amountHidden_<?= $rand ?>" name="item[<?= $rand ?>][amount]">
          </td>
          <td>
            <div class="btns-grp d-flex gap-2">
              <a style="cursor: pointer" class="btn btn-danger add-btn-minus-bill">
                <i class="fa fa-minus"></i>
              </a>
            </div>
          </td>
        </tr>
      <?php
      }

      ?>

    </tbody>
    <tr>
      <td colspan="5" class="text-right">SGST</td>
      <td colspan="2" class="text-right" id="sgst_span"></td>
      <input type="hidden" name="sgst" id="sgst" value='' />
    </tr>
    <tr>
      <td colspan="5" class="text-right">CGST</td>
      <td colspan="2" class="text-right" id="cgst_span"></td>
      <input type="hidden" name="cgst" id="cgst" value='' />
    </tr>
    <tr>
      <td colspan="5" class="text-right">IGST</td>
      <td colspan="2" class="text-right" id="igst_span"></td>
      <input type="hidden" name="igst" id="igst" value='' />
    </tr>
    <tr>
      <td colspan="5" class="text-right">Sub Total</td>
      <td colspan="2" class="text-right" id="subTotal"><?= inputValue($total) ?>
        <input type="hidden" id="subTotal" name="subTotal" value="<?= inputValue($total) ?>">

      </td>
    </tr>
    <tr>
      <td colspan="5"></td>
      <td>
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 px-0">
            <div class="round-off-section p-0">
              <div class="round-off-head d-flex gap-2">
                <input type="checkbox" class="checkbox round_off_checkbox_grn" name="round_off_checkbox" id="round_off_checkbox_grn">
                <p class="text-xs" for="round_off_checkbox">Adjust Amount</p>
              </div>

              <div id="round_off_hide_grn" style="display:none;">
                <div class="row round-off calculte-input px-0">
                  <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="adjust-currency d-flex gap-2">
                      <select id="round_sign" name="round_sign" class="form-control text-center">
                        <option value="+">+</option>
                        <option value="-">-</option>
                      </select>
                      <input type="number" step="any" name="round_value" step="any" id="round_value" value="0" class="form-control text-center inputAmountClass">
                    </div>
                  </div>
                </div>
                <!-- <div class="row" style="width: 100%;">
                  <div class="col-lg-12 col-md-12 col-sm-12 px-0">
                    <div class="totaldueamount d-flex justify-content-between border-top border-white pt-2">
                      <p class="font-bold">Adjusted Amount</p>
                      <input type="hidden" name="paymentDetails[adjustedCollectAmount]" class="adjustedCollectAmountInp">
                      <p class="text-success font-bold rupee-symbol">₹ <span class="adjustedDueAmt">0</span></p>
                      <input type="hidden" name="paymentDetails[roundOffValue]" class="roundOffValueHidden">
                    </div>
                  </div>
                </div> -->
              </div>
              <script>
                $(document).ready(function() {
                  $('#round_off_checkbox_grn').on('click', function() {

                    var isChecked = $('#round_off_checkbox_grn').prop('checked');
                    if (isChecked) {
                      $('#round_off_hide_grn').show();
                    } else {
                      $('#round_value').val('0');
                      calculateAllItemsGrandAmount();
                      $('#round_off_hide_grn').hide();
                    }
                  })
                });
              </script>
            </div>
          </div>
        </div>
      </td>
      <td></td>
    </tr>

    <tr>
      <td colspan="5" class="text-right font-bold"> Total</td>
      <td colspan="2" class="text-right font-bold" id="grandTotal"> <?= inputValue($total) ?>
      </td>
    </tr>


    <tr>
      <td><input type="hidden" id="grandTotalHidden" name="grandTotal" value="<?= inputValue($total) ?>"></td>
      <td> <input type="hidden" name="discountAmount" id="discountAmountHidden" class="form-control" value="0"></td>
      <td><input type="hidden" class="form-control" name="subTotal" id="subTotalHidden" value="<?= inputValue($total) ?>"></td>
    </tr>




<?php
  }
}


?>