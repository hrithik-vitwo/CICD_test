<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if (isset($_GET["po"]) && $_GET["po"] != "" && isset($_GET["serial_number"]) && $_GET["serial_number"] != "") {
    global $company_id;
    global $branch_id;
    global $location_id;
    $po = $_GET["po"];

    function getCostCenterListForGrn()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        return queryGet('SELECT * FROM `erp_cost_center` WHERE `company_id`=' . $company_id . ' AND `CostCenter_status`="active"', true);
    }


    $poDetailsObj = queryGet("SELECT * FROM `erp_branch_purchase_order` LEFT JOIN `erp_vendor_details` ON erp_vendor_details.vendor_id = erp_branch_purchase_order.vendor_id WHERE erp_branch_purchase_order.po_number = '" . $po . "' AND erp_branch_purchase_order.company_id='" . $company_id . "' AND erp_branch_purchase_order.branch_id='" . $branch_id . "' AND erp_branch_purchase_order.location_id='" . $location_id . "'", false);
    $poDetails = $poDetailsObj["data"] ?? [];

    $currency = $poDetails["currency"];
    $curr_name_query = queryGet("SELECT * FROM `erp_currency_type` WHERE currency_id = $currency", false);
    $curr_name = $curr_name_query["data"]["currency_name"];
    $conversion_rate = $poDetails["conversion_rate"];

    if ($poDetailsObj["numRows"] == 0) {
    } else {

        $subtotal = 0;
        $total = 0;
        $total_tax = 0;

        $invoiceTotal = $total ?? 0;
        $invoiceSubTotal = $subtotal ?? 0;
        $invoiceTaxTotal = $total_tax ?? 0;


        $loginBranchGstin = "";
        $branchDeails = [];
        $branchDeailsObj = queryGet("SELECT `erp_branches`.*,`erp_companies`.`company_name`, `erp_companies`.`company_pan`,`erp_companies`.`company_const_of_business` FROM `erp_branches`, `erp_companies` WHERE `erp_branches`.`company_id`=`erp_companies`.`company_id` AND `branch_id`=" . $branch_id);
        if ($branchDeailsObj["status"] == "success") {
        $branchDeails = $branchDeailsObj["data"];
        $loginBranchGstin = $branchDeails["branch_gstin"];
        $loginBranchName = $branchDeails["branch_name"];
        $loginCompanyName = $branchDeails["company_name"];
        $loginCompanyPan = $branchDeails["company_pan"];
        $loginCompanyConstOfBusiness = $branchDeails["company_const_of_business"];
        } else {
        return [
        "status" => "warning",
        "message" => "Branch not found!",
        "file" => $filename
        ];
        }


        $customerName = $loginBranchName ?? "";
        $customerPurchaseOrder = $poDetails["po_number"] ?? "";

        $customerGstin = $loginBranchGstin;
        $vendorGstin = $poDetails["vendor_gstin"] ?? "";

        $customerGstinStateCode = substr($customerGstin, 0, 2);

        $vendor_id = $poDetails["vendor_id"];


        if ($vendorGstin == "" || $vendorGstin == NULL || !isset($vendorGstin)) {
        $vendorGstinStateCode = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=" . $vendor_id . " AND `vendor_business_primary_flag`='1' ORDER BY `vendor_business_id` DESC", false)["data"]["state_code"] ?? "";
        } else {
        $vendorGstinStateCode = substr($vendorGstin, 0, 2);
        }


        $vendorAddress = queryGet("SELECT * FROM `erp_vendor_bussiness_places` WHERE `vendor_id`=" . $vendor_id . " AND `vendor_business_primary_flag`='1' ORDER BY `vendor_business_id` DESC", false)["data"]["gstStateName"] ?? "";
        $vendorAddressRecipient = "";

        $vendorGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $vendorGstinStateCode)["data"]["gstStateName"] ?? "";
        $customerGstinStateName = queryGet("SELECT * FROM `erp_gst_state_code` WHERE `gstStateCode`=" . $customerGstinStateCode)["data"]["gstStateName"] ?? "";

        $vendorPan = substr($vendorGstin, 2, 10);

        $vendorCode = $poDetails["vendor_code"];
        $vendorId = $poDetails["vendor_id"];
        $vendorName = $poDetails["trade_name"] ?? "";
        $vendorCreditPeriod = $poDetails["vendor_credit_period"];

        $functional_area = $poDetails["functional_area"];

        $totalCGST = 0;
        $totalSGST = 0;
        $totalIGST = $total_tax == "" ? 0 : $total_tax;

        $getCostCenterListForGrnObj = getCostCenterListForGrn();

        $po_id = $poDetails["po_id"];
        $po_item = queryGet("SELECT * FROM `erp_branch_purchase_order_items` LEFT JOIN `erp_inventory_items` ON erp_inventory_items.itemId = erp_branch_purchase_order_items.inventory_item_id LEFT JOIN `erp_hsn_code` ON erp_hsn_code.hsnCode = erp_inventory_items.hsnCode WHERE erp_branch_purchase_order_items.po_id = '" . $po_id . "' AND erp_branch_purchase_order_items.remainingQty > 0", true);

        $po_item_data = $po_item["data"];
        $poItemSl = 1;
        $sl = $_GET["serial_number"];
        $totalSubtotal = 0;
        $GrandtoalTotal = 0;
        $grandcgst = 0;
        $grandsgst = 0;
        $grandigst = 0;
        $totalTdsValue = 0;
        $po_ids = array();
        foreach ($po_item_data as $oneItemObj) {

            $oneItemData = $oneItemObj;

            $itemHSN = "";
            $itemName = $oneItemData["itemName"] ?? "";
            $grnItemName = $oneItemData["itemName"] ?? "";
            $itemQty = $oneItemData["remainingQty"] ?? "0";
            $itemUnitPrice = $oneItemData["unitPrice"] ?? "0";
            $invoice_units = $oneItemData["uom"] ?? "";
            $internalItemuom_id = $oneItemData["baseUnitMeasure"];
            $goodsType = $oneItemData["goodsType"];
            $po_item_id = $oneItemData["po_item_id"];

            $subtotal = $itemUnitPrice * $itemQty;

            $tax_percentage = $oneItemData["taxPercentage"];

            $itemTax = ($itemUnitPrice * $itemQty) * $tax_percentage / 100;

            $Total = ($itemUnitPrice * $itemQty) + $tax_amt;

            $cgst = 0;
            $sgst = 0;
            $igst = $itemTax == "" ? 0 : $itemTax;


            $internalItemId = "";
            $internalItemCode = "";
            $internalItemHsn = "";
            $tds = 0;
            $internalItemId = $oneItemData["inventory_item_id"];
            $internalItemCode = $oneItemData["itemCode"];
            $internalItemUom = $oneItemData["uom"];
            // $itemType = $oneItemData["type"];
            $itemHSN = $oneItemData["hsnCode"];
            $itemName = $oneItemData["itemName"];
            $tds_id = $oneItemData["tds"];
            $tds_query = queryGet("SELECT `TDSRate` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");
            $tds = $tds_query["data"]["TDSRate"] ?? 0;

            $basic_amt = ($itemUnitPrice * $itemQty);

            $tds_value = $basic_amt * ($tds / 100);

            if ($vendorGstinStateCode == $customerGstinStateCode) {
                $cgst = $itemTax / 2;
                $sgst = $itemTax / 2;
                $igst = 0;
            } else {
                $cgst = 0;
                $sgst = 0;
                $igst = $itemTax == "" ? 0 : $itemTax;
            }

            $itemTotalPrice = $basic_amt + $cgst + $sgst + $igst - $tds_value;

            // if ($vendorGstinStateCode == $customerGstinStateCode) {
            //     $itemTotalPrice = ($basic_amt) + $cgst + $sgst - $tds_value ;
            // } else {
            //     $itemTotalPrice = ($basic_amt) + $igst - $tds_value;
            // }

            array_push($po_ids, $oneItemData["po_item_id"]);

            if ($itemName == "" || strtolower($itemName) == "cgst" || strtolower($itemName) == "sgst") {
                continue;
            }
            $sl += 1;

?>
                    <tr id="grnItemRowTr_<?= $sl ?>">
                        <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                        <input type="hidden" id="internalItemPo_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemPurchaseOrder]" value="<?= $po ?>" />
                        <input type="hidden" id="internalPoItemId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][PoItemId]" value="<?= $oneItemData["po_item_id"] ?>" />
                        <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                        <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                        <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                        <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemQty]" value="<?= $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPriceStatic" id="ItemInvoiceTotalPriceStatic_<?= $sl ?>" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" name="grnItemList[<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                        <!-- <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                        <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                        <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                        <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceTDSValue]" value="<?= $tds_value ?>" />
                        <input type="hidden" class="ItemInvoiceTotalPrice" id="ItemInvoiceTotalPrice_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemTotalPrice]" value="<?= $itemUnitPrice * $itemQty ?>" />
                        <input type="hidden" class="ItemInvoiceCGSTClass" id="ItemInvoiceCGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCGST]" value="<?= $cgst ?>" />
                        <input type="hidden" class="ItemInvoiceSGSTClass" id="ItemInvoiceSGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemSGST]" value="<?= $sgst ?>" />
                        <input type="hidden" class="ItemInvoiceIGSTClass" id="ItemInvoiceIGST_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemIGST]" value="<?= $igst ?>" />
                        <input type="hidden" id="ItemInvoiceCGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemCGSTNew]" value="<?= $cgst ?>" />
                        <input type="hidden" id="ItemInvoiceSGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemSGSTNew]" value="<?= $sgst ?>" />
                        <input type="hidden" id="ItemInvoiceIGSTNew_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemIGSTNew]" value="<?= $igst ?>" />
                        <input type="hidden" id="ItemInvoiceUnits_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUnits]" value="<?= $invoice_units ?>" />
                        <input type="hidden" id="ItemInvoiceUOM_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUOM]" value="<?= $internalItemUom ?>" />
                        <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
                        <input type="hidden" id="ItemInvoiceUOMID_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemUOMID]" value="<?= $internalItemuom_id ?>" />


                        <td><?= $sl ?></td>
                        <td id="grnItemPOTdSpan_<?= $sl ?>">
                            <p class="pre-normal <?= $po ?>"><?= $po ?></p>
                        </td>
                        <td id="grnItemNameTdSpan_<?= $sl ?>"><p class="pre-normal"><?= $itemName ?></p></td>
                        <td class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                            <?php
                            echo $internalItemCode;
                            ?>
                        </td>
                        <td class="grnItemHSNTdSpan" id="grnItemHSNTdSpan_<?= $sl ?>"><?= $itemHSN ?></td>
                        <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                            <select class="form-control text-xs itemCostCenterId_<?= $sl ?>" name="grnItemList[<?= $sl ?>][itemStorageLocationId]" required>
                                <option value="">Select Cost Center</option>
                                <?php
                                foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                    echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                            <div class="form-input d-flex gap-2">
                                <input type="number" name="grnItemList[<?= $sl ?>][itemReceivedQty]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs received_quantity" required>
                                <input type="hidden" name="poItemId[<?= $po_item_id ?>]" id="grnPoInputQty_<?= $sl ?>" value="0">
                                <input type="hidden" name="grnItemList[<?= $sl ?>][itemRemainQty]" id="grnPoInputRemainQty_<?= $sl ?>" value="0">
                                <input type="hidden" name="grnItemList[<?= $sl ?>][poQty]" id="grnPoQty_<?= $sl ?>" value="<?= $itemQty ?>">
                                <p class="text-xs"><?= $internalItemUom ?></p>
                            </div>
                        </td>
                        <!-- <td class="text-right" id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= number_format($itemUnitPrice, 2) ?></td> -->
                        <td>
                        <div class="input-group input-group-sm m-0" style="flex-wrap: nowrap;">
                        <div class="input-group-prepend">
                            <span class="input-group-text spanInvoiceCurrencyName" id="spanInvoiceCurrencyName_<?= $sl ?>"><?= $curr_name ?></span>
                        </div>
                            <input type="number" name="grnItemList[<?= $sl ?>][itemUnitPriceOtherCurrency]" value="<?= number_format($itemUnitPrice, 2, '.', '') ?>" id="grnItemUnitPriceTdInput_<?= $sl ?>" class="form-control border py-3 text-right itemUnitPrice" required readonly>
                            <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPricehidden]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceTdInputhidden_<?= $sl ?>" class="form-control text-xs itemUnitPricehidden">
                            <input type="hidden" name="grnItemList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceInrhidden_<?= $sl ?>" class="form-control text-xs grnItemUnitPriceInrhidden">
                        </div>
                        <span class="text-small spanUnitPriceINR" id="spanUnitPriceINR_<?= $sl ?>"></span>
                        </td>
                        <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= $curr_name." : ". number_format($itemUnitPrice * $itemQty, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceCGSTTdSpan_<?= $sl ?>"><?= $curr_name." : ". number_format($cgst, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceSGSTTdSpan_<?= $sl ?>"><?= $curr_name." : ". number_format($sgst, 2) ?></td>
                        <td class="text-right" id="grnItemInvoiceIGSTTdSpan_<?= $sl ?>"><?= $curr_name." : ". number_format($igst, 2) ?></td>
                        <td>
                            <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                                <input type="number" name="grnItemList[<?= $sl ?>][itemTds]" value="<?= $tds ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-xs itemTds" required>
                                <p class="text-xs">%</p>
                            </div>
                        </td>
                        <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </span>
                        <input type="hidden" value="<?= $tax_percentage ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">
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

<?php

            $totalSubtotal += ($itemUnitPrice * $itemQty);
            $GrandtoalTotal += $itemTotalPrice;
            $grandcgst += $cgst;
            $grandsgst += $sgst;
            $grandigst += $igst;
            $totalTdsValue += $tds_value;
        }
    }
}

?>