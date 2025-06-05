<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");

if (isset($_GET["serial_number"]) && $_GET["serial_number"] != "" && isset($_GET["removeItemQuantity"]) && $_GET["removeItemQuantity"] != "" && isset($_GET["removeItemUnitPrice"]) && $_GET["removeItemUnitPrice"] != "" && isset($_GET["removeItemTax"]) && $_GET["removeItemTax"] != "" && isset($_GET["removeItemBasicPrice"]) && $_GET["removeItemBasicPrice"] != "") {
    global $company_id;
    global $branch_id;
    global $location_id;


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
                $getCostCenterListForGrnObj = getCostCenterListForGrn();
                // $sl = 0;
                $totalSubtotal = 0;
                $GrandtoalTotal = 0;
                $grandcgst = 0;
                $grandsgst = 0;
                $grandigst = 0;
                $totalTdsValue = 0;
                $totalTaxPercent = 0;

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
                    $invoice_units = "";
                    $cgst = 0;
                    $sgst = 0;
                    $igst = $removeItemTax;

                    $baseAmt = ($itemUnitPrice * $itemQty);


                    $internalItemId = "";
                    $internalItemCode = "";
                    $internalItemHsn = "";
                    $tds = 0;

                    $basic_amt = ($itemUnitPrice * $itemQty);

                    $tds_value = 0;


                    if ($itemName == "") {
                        $itemName = "Item Name or Description not identified -" . uniqid();
                    }
                    $sl += 1;
                    $subtotal = ($itemUnitPrice * $itemQty);

                    // $after_tax_apply = $subtotal * $tax / 100;

                    // $tax_added_value = $subtotal + ($subtotal * $tax / 100);

                    ?>
                    
                    <?php

                    // $totalTaxPercent += $tax_added_value;

                    // if ($vendorGstinStateCode == $customerGstinStateCode) {
                    //     $cgst = $after_tax_apply / 2;
                    //     $sgst = $after_tax_apply / 2;
                    //     $igst = 0;
                    // } else {
                    //     $cgst = 0;
                    //     $sgst = 0;
                    //     $igst = $after_tax_apply;
                    // }

                    $itemTotalPrice = ($basic_amt) + $cgst + $sgst + $igst - $tds_value;

                    ?>

                    <tr id="grnItemRowTr_<?= $sl ?>">
                    <input type="hidden" value="<?= $tax_added_value ?>" id="grnItemInternalTaxValue_<?= $sl ?>" class="form-control text-xs itemInternalTaxValue" step="any">
                        <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                        <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                        <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                        <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                        <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemQty]" value="<?= $itemQty ?>" />
                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                        <!-- <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                        <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                        <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                        <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSValue]" value="<?= $tds_value ?>" />
                        <input type="hidden" class="ItemInvoiceTDSSlab" id="ItemInvoiceTDSSlab_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSSlab]" value='<?= json_encode($slab) ?>' />
                        <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
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
                            <select class="form-control text-xs itemCostCenterId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" required>
                                <option value="">Select Cost Center</option>
                                <?php
                                foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                    echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                                }
                                ?>
                            </select>

                            <!-- <button id="cstcntr_btn_<?= $sl ?>" type="button" class="btn btn-info btn-lg cstcntr_btn" data-toggle="modal" data-target="#myModal_<?= $sl ?>">Select Cost Center</button> -->
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
                        <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= $comp_currency.": ". number_format($itemUnitPrice * $itemQty, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceCGSTTdSpan_<?= $sl ?>"><?= $comp_currency.": ". number_format($cgst, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceSGSTTdSpan_<?= $sl ?>"><?= $comp_currency.": ". number_format($sgst, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceIGSTTdSpan_<?= $sl ?>"><?= $comp_currency.": ". number_format($igst, 2) ?></td>
                        <td>
                            <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                <input type="number" name="grnItemList[<?= $sl ?>][itemTds]" value="<?= $tds ?? 0 ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-center p-0 px-2 text-xs itemTds border-0" style="width: 30px !important;" required>
                                <p class="text-xs">%</p>
                            </div>
                        </td>
                        <input type="hidden" value="<?= $tax ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">
                        <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </span>
                        <td class="text-right" id="grnItemDeleteTdSpan_<?= $sl ?>"><button title="Delete Item" type="button" id="grnItemDeleteButton_<?= $sl ?>" class="btn btn-sm remove_row" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button></td>
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
                        </td>
                        <td class="bg-transparent"></td>
                        <td colspan="3" class="bg-transparent">
                            <?php // if ((float)$itemTotalPrice != (float)$Total) {echo "<span class='error calculate-error'>".$itemTotalPrice." is the difference</span>"; } 
                            ?>
                        </td>
                    </tr>

                    <!-- <div class="modal fade" id="myModal_<?= $sl ?>" role="dialog" data-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">

                                    <h4 class="modal-title">Select Cost Centers</h4>
                                    <?= $sl ?>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $funcList = $BranchPoObj->fetchFunctionality()['data'];
                                    foreach ($funcList as $data) {
                                        $rand = rand(10, 100);
                                    ?>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6">

                                                <div class="form-input">
                                                    <input type="text" name="grnItemList[<?= $sl ?>][cost_center][<?= $rand ?>][code]" class="form-control costCenterName" value="<?= $data['CostCenter_code'] ?>" readonly>
                                                    <input type="hidden" name="grnItemList[<?= $sl ?>][cost_center][<?= $rand ?>][id]" class="form-control costCenterName" value="<?= $data['CostCenter_id'] ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6">

                                                <div class="form-input">
                                                    <input type="text" name="grnItemList[<?= $sl ?>][cost_center][<?= $rand ?>][rate]" class="form-control cstcntr_rate">
                                                     costCenterRate
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="modal-footer">
                                    <p id="modalAmount_<?= $sl ?>">Total Amount: <?= $itemUnitPrice * $itemQty ?></p>
                                    <button id="modalButton_<?= $sl ?>" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div> -->
                <?php
                    $totalSubtotal += ($itemUnitPrice * $itemQty);
                    $GrandtoalTotal += $itemTotalPrice;
                    $grandcgst += $cgst;
                    $grandsgst += $sgst;
                    $grandigst += $igst;
                    $totalTdsValue += $tds_value;
                // }

}

?>