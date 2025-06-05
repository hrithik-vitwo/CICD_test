<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");

if (isset($_GET["serial_number"]) && $_GET["serial_number"] != "" && isset($_GET["removeItemQuantity"]) && $_GET["removeItemQuantity"] != "" && isset($_GET["removeItemUnitPrice"]) && $_GET["removeItemUnitPrice"] != "" && isset($_GET["removeItemTax"]) && $_GET["removeItemTax"] != "" && isset($_GET["removeItemBasicPrice"]) && $_GET["removeItemBasicPrice"] != "") {
    global $company_id;
    global $branch_id;
    global $location_id;

    function getStorageLocationListForGrn()
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    global $isQaEnabled;

    // return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` AS storage LEFT JOIN `erp_storage_warehouse` ON erp_storage_warehouse.`warehouse_id`= storage.`warehouse_id` WHERE storage.`company_id`=' . $company_id . ' AND storage.`branch_id`=' . $branch_id . ' AND storage.`location_id`=' . $location_id . ' AND storage.`storage_location_type` IN ("RM-WH","FG-WH","QA","Asset") AND storage.`storage_location_material_type` IN ("RM","FG","QA","Asset") AND storage.`storage_location_storage_type`="Open" AND storage.`status`="active"', true);



    $slSql = 'SELECT * FROM
    `erp_storage_location` AS STORAGE
        LEFT JOIN `erp_storage_warehouse` ON erp_storage_warehouse.`warehouse_id` = STORAGE.`warehouse_id`
        WHERE STORAGE.`company_id` = ' . $company_id . '
        AND STORAGE.`branch_id` = ' . $branch_id . '
        AND STORAGE.`location_id` = ' . $location_id . ' 
        AND STORAGE.`storage_location_type` IN ("RM-WH", "FG-WH", "QA", "Asset") 
        AND STORAGE.`storage_location_material_type` IN ("RM", "FG", "QA", "Asset") 
        AND STORAGE.`storage_location_storage_type` = "Open" 
        AND STORAGE.`status` = "active"';

    return queryGet($slSql, true);

}


function getCostCenterListForGrn()
{
    global $company_id;
    global $branch_id;
    global $location_id;
    global $created_by;
    global $updated_by;
    return queryGet('SELECT * FROM `erp_cost_center` WHERE `company_id`=' . $company_id . ' AND `CostCenter_status`="active"', true);
}


    $sl = $_GET["serial_number"];
    $itemsName = $_GET["itemsName"];
    $removeItemQuantity = $_GET["removeItemQuantity"];
    $removeItemUnitPrice = $_GET["removeItemUnitPrice"];
    $removeItemTax = $_GET["removeItemTax"];
    $removeItemBasicPrice = $_GET["removeItemBasicPrice"];

?>

                <?php
                $getStorageLocationListForGrnObj = getStorageLocationListForGrn();
                $getCostCenterListForGrnObj = getCostCenterListForGrn();
                // $sl = 0;
                $totalSubtotal = 0;
                $GrandtoalTotal = 0;
                $grandcgst = 0;
                $grandsgst = 0;
                $grandigst = 0;
                $totalTdsValue = 0;
                $totalTaxPercent = 0;
                // foreach ($invoiceData["Items"] as $oneItemObj) {

                    // $oneItemData = $oneItemObj;

                    $companyCurrencyObj = queryGet("SELECT * FROM `erp_currency_type` WHERE `currency_id`=$company_currency");
                    $companyCurrencyData = $companyCurrencyObj["data"];
                    $comp_currency = $companyCurrencyData["currency_name"];

                    $itemHSN = "";
                    $tax = 0;
                    $goodsType = "";
                    $itemName = $itemsName ?? "";
                    $grnItemName = $itemsName ?? "";
                    $itemQty = $removeItemQuantity ?? "0";
                    $itemTax = $removeItemTax ?? "0";
                    $itemUnitPrice = $removeItemUnitPrice ?? "0";
                    // $Total = $oneItemData["Amount"] ?? "0";
                    $invoice_units = "";
                    $cgst = 0;
                    $sgst = 0;
                    $igst = $removeItemTax;

                    // if ($vendorGstinStateCode == $customerGstinStateCode) {
                    //     $itemTotalPrice = ($itemUnitPrice * $itemQty) + $cgst + $sgst;
                    // } else {
                    //     $itemTotalPrice = ($itemUnitPrice * $itemQty) + $igst;
                    // }

                    $baseAmt = ($itemUnitPrice * $itemQty);


                    $internalItemId = "";
                    $internalItemCode = "";
                    $internalItemHsn = "";
                    $tds = 0;
                    // if ($vendorCode != "") {
                    //     $itemCodeAndHsnObj = getItemCodeAndHsn($vendorCode, $itemName, $baseAmt);
                    //     //  console($itemCodeAndHsnObj);
                    //     $internalItemId = $itemCodeAndHsnObj["itemId"];
                    //     $internalItemCode = $itemCodeAndHsnObj["itemCode"];
                    //     $internalItemUom = $itemCodeAndHsnObj["uom"];
                    //     $internalItemuom_id = $itemCodeAndHsnObj["uom_id"];
                    //     $itemType = $itemCodeAndHsnObj["type"];
                    //     $itemHSN = $itemCodeAndHsnObj["itemHsn"];
                    //     $itemName = $itemCodeAndHsnObj["itemName"];
                    //     $tax = $itemCodeAndHsnObj["tax"];
                    //     $tds = $itemCodeAndHsnObj["tds"] ?? 0;
                    //     $goodsType = $itemCodeAndHsnObj["goodsType"];
                    //     $slab = $itemCodeAndHsnObj["slab"];
                    // }
                    // $itemHSN = $oneItemData["ProductCode"] ?? $itemHSN;

                    //Check for mapped Item
                    // if ($internalItemCode == "") {
                    //     $itemHSN = $oneItemData["ProductCode"];
                    //     $itemName = $oneItemData["Description"] ?? "";
                    // }

                    $basic_amt = ($itemUnitPrice * $itemQty);

                    $tds_value = 0;


                    // if (strtolower($itemName) == "cgst" || strtolower($itemName) == "sgst") {
                    //     continue;
                    // }
                    if ($itemName == "") {
                        $itemName = "Item Name or Description not identified -" . uniqid();
                    }
                    $sl += 1;
                ?>

                    <tr id="grnItemRowTr_<?= $sl ?>">
                        <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                        <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                        <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                        <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                        <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemQty]" value="<?= $itemQty ?>" />
                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                        <!-- <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                        <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                        <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSValue]" value="0" />
                        <input type="hidden" class="ItemInvoiceTDSSlab" id="ItemInvoiceTDSSlab_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSSlab]" value='<?= json_encode($slab) ?>' />

                        <?php
                        if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                        ?>
                            <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceGoodsType]" value="goods" />
                        <?php
                        } else {
                        ?>
                            <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
                        <?php
                        }
                        ?>


                        <?php
                        $subtotal = ($itemUnitPrice * $itemQty);

                        // $after_tax_apply = $subtotal * $tax / 100;

                        // $tax_added_value = $subtotal + ($subtotal * $tax / 100);

                        ?>
                        <input type="hidden" value="0" id="grnItemInternalTaxValue_<?= $sl ?>" class="form-control text-xs itemInternalTaxValue" step="any">
                        <?php

                        $totalTaxPercent += $tax_added_value;


                        $itemTotalPrice = ($basic_amt) + $cgst + $sgst + $igst - $tds_value;

                        ?>


                        <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPrice" id="ItemInvoiceTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemTotalPrice]" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPriceStatic" id="ItemInvoiceTotalPriceStatic_<?= $sl ?>" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceCGSTClass" id="ItemInvoiceCGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCGST]" value="<?= $cgst ?>" />
                        <input type="hidden" id="ItemInvoiceCGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCGSTNew]" value="<?= $cgst ?>" />
                        <input type="hidden" class="ItemInvoiceSGSTClass" id="ItemInvoiceSGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemSGST]" value="<?= $sgst ?>" />
                        <input type="hidden" id="ItemInvoiceSGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemSGSTNew]" value="<?= $sgst ?>" />
                        <input type="hidden" class="ItemInvoiceIGSTClass" id="ItemInvoiceIGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemIGST]" value="<?= $igst ?>" />
                        <input type="hidden" id="ItemInvoiceIGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemIGSTNew]" value="<?= $igst ?>" />
                        <input type="hidden" id="ItemInvoiceUnits_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUnits]" value="<?= $invoice_units ?>" />
                        <input type="hidden" id="ItemInvoiceUOM_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUOM]" value="" />
                        <input type="hidden" id="ItemInvoiceUOMID_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUOMID]" value="" />


                        <td><input type="checkbox" id="check_box_<?= $sl ?>" name="check_box" class="checkbx" value="<?= $sl ?>"></td>
                        <td><?= $sl ?></td>
                        <td id="grnItemNameTdSpan_<?= $sl ?>"><?= $itemName ?></td>
                        <td class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                            <?php
                            // if ($postStatus != 0) {
                            //     echo $internalItemCode;
                            // } else {
                                
                            // }

                            if ($internalItemCode == "") {
                                echo '<a class="btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCode" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCode">Map Code</i></a>';
                            } else {
                                echo $internalItemCode;
                                echo '<a class="btn btn-sm btn-xs btn-secondary ml-2 openModalMapInvoiceItemCodeChange" data-itemrownum="' . $sl . '" data-toggle="modal" data-target="#mapInvoiceItemCodeChange"><i class="fas fa-pencil-alt"></i></a>';
                            }
                            ?>
                        </td>
                        <td class="grnItemHSNTdSpan" id="grnItemHSNTdSpan_<?= $sl ?>"><?= $itemHSN ?></td>
                        <td id="grnItemStrgLocTdSpan_<?= $sl ?>" class="storageSelect">

                            <?php
                            if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                                //Get Summary
                                // $itemId = $internalItemId;
                                // $summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$itemId' AND `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'", false);

                            ?>
                                <select class="form-control text-xs storageLocationSelect" id="itemStorageLocationId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" required>
                                    <option value="">Select storage location</option>
                                    <?php

                                    // getqaListForGrnObj
                                    // if ($summary["data"]["quality_enabled"] == '1') {

                                        // $st_loc_id = $summary["data"]["qa_storage_location"];

                                        // $rackDetailsObj = queryGet("SELECT rack_id FROM `erp_rack` WHERE storage_location_id = '" . $st_loc_id . "'", true);
                                        // $options = "";
                                        // // console($rackDetailsObj);
                                        // foreach ($rackDetailsObj["data"] as $rackDetail) {
                                        //     $rack_id = $rackDetail["rack_id"];
                                        //     if (is_null($rack_id) || $rack_id == "")
                                        //         continue;
                                        //     $layerDetailsObj = queryGet("SELECT * FROM `erp_layer` WHERE rack_id = '" . $rack_id . "'", true);
                                        //     // console($rack_id);
                                        //     foreach ($layerDetailsObj["data"] as $layerDetail) {
                                        //         $layer_id = $layerDetail["layer_id"];
                                        //         if (is_null($layer_id) || $layer_id == "")
                                        //             continue;
                                        //         $binDetailsObj = queryGet("SELECT * FROM `erp_storage_bin` WHERE layer_id = '" . $layer_id . "'", true);
                                        //         // console($binDetailsObj);
                                        //         foreach ($binDetailsObj["data"] as $binDetail) {
                                        //             $bin_id = $binDetail["bin_id"];
                                        //             if (is_null($bin_id) || $bin_id == "")
                                        //                 continue;
                                        //             $bin_name = $binDetail["bin_name"];
                                        //             $options .=  "<option value='" . $bin_id . "'>" . $bin_name . "</option>";
                                        //         }
                                        //     }
                                        // }


                                        // foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                        //     if ($oneRmStorageLocation["storage_location_id"] == $summary["data"]["qa_storage_location"]) {
                                        //         echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                        //     } else {
                                        //         echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                        //     }
                                        // }
                                    // } else {

                                        // $st_loc_id = $summary["data"]["default_storage_location"];

                                        // $rackDetailsObj = queryGet("SELECT rack_id FROM `erp_rack` WHERE storage_location_id = '" . $st_loc_id . "'", true);
                                        // $options = "";
                                        // console($rackDetailsObj);
                                        // foreach ($rackDetailsObj["data"] as $rackDetail) {
                                        //     $rack_id = $rackDetail["rack_id"];
                                        //     if (is_null($rack_id) || $rack_id == "")
                                        //         continue;
                                        //     $layerDetailsObj = queryGet("SELECT * FROM `erp_layer` WHERE rack_id = '" . $rack_id . "'", true);
                                        //     // console($rack_id);
                                        //     foreach ($layerDetailsObj["data"] as $layerDetail) {
                                        //         $layer_id = $layerDetail["layer_id"];
                                        //         if (is_null($layer_id) || $layer_id == "")
                                        //             continue;
                                        //         $binDetailsObj = queryGet("SELECT * FROM `erp_storage_bin` WHERE layer_id = '" . $layer_id . "'", true);
                                        //         // console($binDetailsObj);
                                        //         foreach ($binDetailsObj["data"] as $binDetail) {
                                        //             $bin_id = $binDetail["bin_id"];
                                        //             if (is_null($bin_id) || $bin_id == "")
                                        //                 continue;
                                        //             $bin_name = $binDetail["bin_name"];
                                        //             $options .=  "<option value='" . $bin_id . "'>" . $bin_name . "</option>";
                                        //         }
                                        //     }
                                        // }

                                        
                                    // }

                                    ?>
                                </select>
                                <input type="hidden" value="" id="grnItemAllBins_<?= $sl ?>" class="form-control">

                            <?php
                            } else {
                            ?>
                                <select class="form-control text-xs" id="itemStorageLocationId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" required>
                                    <option value="">Select Cost Center</option>
                                    <?php
                                    foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                        echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                                    }
                                    ?>
                                </select>
                            <?php
                            }
                            ?>


                        </td>
                        <?php
                        $options =  "";
                        ?>

                        <script>
                    $(document).ready(function(){
                        var sl = <?= json_encode($sl) ?>;
                        var itemQty = <?= json_encode($itemQty) ?>;
                        var options = <?= json_encode($options) ?>;
                        addGrnItemMultipleBatch(sl, itemQty, true, options);
                    });
                </script>

                        <input type="hidden" value="<?= $options ?>" id="grnItemAllBins_<?= $sl ?>" class="form-control">

                        <td id="grnItemStkQtyTdSpan_<?= $sl ?>">
                            <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                <input type="number" id="itemStockQty_<?= $sl ?>" value="<?= $itemQty ?>" class="form-control text-xs w-50" name="grnItemList[<?= $sl ?>][itemStockQty]">
                                <p class="text-xs" id="grnItemUOM_<?= $sl ?>"></p>
                            </div>
                        </td>
                        <td id="grnItemInvoiceQtyTdSpan_<?= $sl ?>"><?= $itemQty . " " . $invoice_units ?> </td>
                        <td>
                            <div class="form-input">
                                <input type="number" step="any" name="grnItemList[<?= $sl ?>][itemReceivedQty]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs received_quantity" required>
                            </div>
                        </td>
                        <!-- <td class="text-right" id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= number_format($itemUnitPrice, 2) ?></td> -->
                        <td>
                            <div class="input-group input-group-sm m-0" style="flex-wrap: nowrap;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text spanInvoiceCurrencyName" id="spanInvoiceCurrencyName_<?= $sl ?>"><?= $comp_currency ?></span>
                                </div>
                                <input type="number" name="grnItemList[<?= $sl ?>][itemUnitPriceOtherCurrency]" value="<?= number_format($itemUnitPrice, 2, '.', '') ?>" id="grnItemUnitPriceTdInput_<?= $sl ?>" class="form-control text-xs itemUnitPrice w-auto" step="any" required>
                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPricehidden]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceTdInputhidden_<?= $sl ?>" class="form-control text-xs itemUnitPricehidden">
                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceInrhidden_<?= $sl ?>" class="form-control text-xs grnItemUnitPriceInrhidden">
                            </div>
                            <span class="text-small spanUnitPriceINR" id="spanUnitPriceINR_<?= $sl ?>"></span>
                        </td>
                        <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= $comp_currency . ": " . number_format($itemUnitPrice * $itemQty, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceCGSTTdSpan_<?= $sl ?>"><?= $comp_currency . ": " . number_format($cgst, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceSGSTTdSpan_<?= $sl ?>"><?= $comp_currency . ": " . number_format($sgst, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceIGSTTdSpan_<?= $sl ?>"><?= $comp_currency . ": " . number_format($igst, 2) ?></td>

                        <?php
                        if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                        ?>
                            <td>
                                <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                    <input type="number" name="grnItemList[<?= $sl ?>][itemTds]" value="<?= $tds ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-center p-0 px-2 text-xs itemTds border-0" style="width: 30px !important;" required>
                                    <p class="text-xs">%</p>
                                </div>
                            </td>
                        <?php
                        } else {
                        ?>
                            <td>
                                <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                    <input type="number" name="grnItemList[<?= $sl ?>][itemTds]" value="<?= $tds ?? 0 ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-center p-0 px-2 text-xs itemTds border-0" style="width: 30px !important;" required>
                                    <p class="text-xs">%</p>
                                </div>
                            </td>
                        <?php
                        }
                        ?>
                        <input type="hidden" value="<?= $tax ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">
                        <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </span>
                        <td class="text-right" id="grnItemDeleteTdSpan_<?= $sl ?>">
                            <div id="grnItemSettingsTdSpan_<?= $sl ?>">
                                <?php
                                if ($goodsType == 1 || $goodsType == 4 || $goodsType == 5 || $goodsType == 9) {
                                ?>
                                    <button type="button" class="btn-view btn btn-primary delShedulingBtn" data-toggle="modal" data-target="#deliveryScheduleModal_<?= $sl ?>">
                                        <i id="statusItemBtn_<?= $internalItemId ?>" class="statusItemBtn fa fa-cog"></i>
                                    </button>
                                <?php
                                }
                                ?>
                            </div>
                            <button title="Delete Item" type="button" id="grnItemDeleteButton_<?= $sl ?>" class="btn btn-sm remove_row" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button>

                            <div class="modal modal-left left-item-modal fade deliveryScheduleModal discountViewModal discountViewModal_<?= $sl ?>" id="deliveryScheduleModal_<?= $sl ?>" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="left_modal">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"><?= $itemName ?></h5>
                                        </div>
                                        <div class="modal-body multiBatchModelViewBody_<?= $sl ?>">
                                            <div class="qty-title d-flex justify-content-between mb-1 mb-3 pb-2 border-bottom">
                                                <h6 class="modal-title text-xs font-bold">Total Quantity: <span class="totalItemAmountModal" id="totalItemAmountModal_<?= $sl ?>"><?= $itemQty ?></span></h6>
                                                <div class="check-box text-left font-bold text-xs">
                                                    <input type="checkbox" class="grnEnableCheckBxClass" value="1" id="grnEnableCheckBx_<?= $sl ?>" name="grnItemList[<?= $sl ?>][activateBatch]"> Enable check box to insert the manual Batch
                                                    <input type="hidden" name="" id="grnStoreId_<?= $sl ?>" value="">
                                                </div>
                                            </div>
                                            <p class="note mb-3">
                                                By default the generated doc (GRN000927) number will be the batch number
                                            </p>
                                            <div class="modal-add-row" id="modal-add-row_<?= $sl ?>">
                                                <!-- <div class="row manual-grn-plus-modal modal-cog-right">
                                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                                        <div class="form-input">
                                                            <label>Batch Number</label>
                                                            <input type="text" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][multipleBatch][1][batchNumber]" class="form-control multiDeliveryDate" id="multiDeliveryDate_<?= $sl ?>" placeholder="Batch Number">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 col-md-5 col-sm-5">
                                                        <div class="form-input">
                                                            <label>Quantity</label>
                                                            <input type="text" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][multipleBatch][1][qty]" class="form-control multiQuantity" data-itemid="<?= $sl ?>" id="multiQuantity_<?= $sl ?>" placeholder="quantity" value="<?= $itemQty ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-2">
                                                        <a style="cursor: pointer" class="btn btn-primary addQtyBtn" id="addQtyBtn_<?= $sl ?>_<?= $vendor_id ?>">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                    </div>
                                                </div> -->
                                            </div>
                                            <?php
                                            $defaultMultiBatchRows[] = [
                                                "vendorId" => $vendor_id,
                                                "sl" => $sl,
                                                "qty" => $itemQty
                                            ];
                                            ?>
                                            <!-- <script>
                                                $(document).ready(function() {
                                                    console.log("Calling addGrnItemMultipleBatch(batchVendorId, id) to add the default row");
                                                    addGrnItemMultipleBatch(`<?= $vendor_id ?>`, `<?= $sl ?>`);

                                                });
                                            </script> -->
                                        </div>
                                        <div class="modal-footer modal-footer-fixed">
                                            <button type="button" class="btn btn-primary w-100" data-dismiss="modal" id="saveAndClose_<?= $sl ?>">Save & Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="span-error-tr">
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent" colspan="3">
                            <span class="error text-warning" id='grnItemMessage_<?= $sl ?>'>
                                
                            </span>
                        </td>
                        <td class="bg-transparent"></td>
                        <td colspan="3" class="bg-transparent">
                            <?php // if ((float)$itemTotalPrice != (float)$Total) {echo "<span class='error calculate-error'>".$itemTotalPrice." is the difference</span>"; } 
                            ?>
                        </td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>
                        <td class="bg-transparent"></td>

                    </tr>
                <?php
                    $totalSubtotal += ($itemUnitPrice * $itemQty);
                    $GrandtoalTotal += $itemTotalPrice;
                    $grandcgst += $cgst;
                    $grandsgst += $sgst;
                    $grandigst += $igst;
                    $totalTdsValue += $tds_value;
                // }
                ?>



<?php

}

?>