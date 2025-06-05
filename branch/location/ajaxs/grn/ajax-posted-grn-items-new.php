<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if (isset($_GET["grn"]) && $_GET["grn"] != "" && isset($_GET["serial_number"]) && $_GET["serial_number"] != "" && isset($_GET["listtype"]) && $_GET["listtype"] != "") {
    global $company_id;
    global $branch_id;
    global $location_id;
    $grn = $_GET["grn"];
    $list_type = $_GET["listtype"];

    function getStorageLocationListForGrn()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $isQaEnabled;

        return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` AS storage LEFT JOIN `erp_storage_warehouse` ON erp_storage_warehouse.`warehouse_id`= storage.`warehouse_id` WHERE storage.`company_id`=' . $company_id . ' AND storage.`branch_id`=' . $branch_id . ' AND storage.`location_id`=' . $location_id . ' AND storage.`storage_location_type` IN ("RM-WH","FG-WH","QA","Asset") AND storage.`storage_location_material_type` IN ("RM","FG","QA","Asset") AND storage.`storage_location_storage_type`="Open" AND storage.`status`="active"', true);
    }


    $grnDetailsObj = queryGet('SELECT * FROM `erp_grn` WHERE `grnCode`="' . $grn . '" AND `companyId`=' . $company_id . ' AND `branchId`=' . $branch_id . ' AND `locationId`=' . $location_id);
    $grnDetails = $grnDetailsObj["data"] ?? [];

    $currency = $grnDetails["currency"];
    $curr_name_query = queryGet("SELECT * FROM `erp_currency_type` WHERE currency_id = $currency", false);
    $curr_name = $curr_name_query["data"]["currency_name"];
    $conversion_rate = $grnDetails["conversion_rate"];

    if ($grnDetailsObj["numRows"] == 0) {
        echo "Hello";
    } else {

        $subtotal = 0;
        $total = 0;
        $total_tax = 0;


        $invoiceSubTotal = $subtotal ?? 0;
        $invoiceTotal = $total ?? 0;
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
        }

        $customerName = $loginBranchName ?? "";
        // $customerPurchaseOrder = $grnDetails["po_number"] ?? "";

        $customerGstin = $loginBranchGstin;
        $vendorGstin = $grnDetails["vendorGstin"] ?? "";

        $customerGstinStateCode = substr($customerGstin, 0, 2);

        $vendor_id = $grnDetails["vendorId"];

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

        $vendorCode = $grnDetails["vendorCode"];
        $vendorId = $grnDetails["vendorId"];
        $vendorName = $grnDetails["vendorName"] ?? "";
        $vendorCreditPeriod = $grnDetails["vendor_credit_period"] ?? 0;

        $functional_area = $grnDetails["functional_area"] ?? "";

        // console($vendorCreditPeriod);

        $totalCGST = 0;
        $totalSGST = 0;
        $totalIGST = $total_tax == "" ? 0 : $total_tax;
        $totalTdsValue = 0;
        $getStorageLocationListForGrnObj = getStorageLocationListForGrn();




        $grnId = $grnDetails["grnId"];

        $grn_item = queryGet('SELECT grnGoods.*, storageLocation.`storage_location_code`,storageLocation.`storage_location_name` FROM `erp_grn_goods` as grnGoods,`erp_storage_location` as storageLocation WHERE grnGoods.`itemStorageLocation`=storageLocation.`storage_location_id` AND grnGoods.`grnId`=' . $grnId, true);

        $grn_item_data = $grn_item["data"];
        $poItemSl = 1;
        $sl = $_GET["serial_number"];
        $po = $grnDetails["grnPoNumber"];
        $totalSubtotal = 0;
        $GrandtoalTotal = 0;
        $grandcgst = 0;
        $grandsgst = 0;
        $grandigst = 0;
        $po_ids = array();
        foreach ($grn_item_data as $oneItemObj) {

            $oneItemData = $oneItemObj;
            $itemHSN = "";
            $itemName = $oneItemData["goodName"] ?? "";
            $grnItemName = $oneItemData["goodName"] ?? "";
            $itemQty = $oneItemData["receivedQty"] ?? "0";
            $itemUnitPrice = $oneItemData["unitPrice"] ?? "0";
            $invoice_units = $oneItemData["itemUOM"] ?? "";
            $internalItemuom_id = $oneItemData["baseUnitMeasure"];
            $goodsType = $oneItemData["goodstype"];

            $subtotal = $itemUnitPrice * $itemQty;

            // $tax_percentage = $oneItemData["taxPercentage"];

            // $itemTax = ($itemUnitPrice * $itemQty) * $tax_percentage / 100;

            // $Total = ($itemUnitPrice * $itemQty) + $tax_amt;

            $itemTax = 1;
            $tds = $oneItemData["tds"] ?? 0;

            $basic_amt = ($itemUnitPrice * $itemQty);

            $tds_value = ($basic_amt * ($tds / 100)) ?? 0;

            if ($companyCountry == 103) {
                $cgst = $oneItemData["cgst"];
                $sgst = $oneItemData["sgst"];
                $igst = $oneItemData["igst"];

                $itemTotalPrice = ($itemUnitPrice * $itemQty) + $cgst + $sgst + $igst - $tds_value;;
            } else {
                $total_tax_item = 0;
                $getItemTaxRule = getItemTaxRule($companyCountry, $vendorGstinStateCode, $customerGstinStateCode);
                $data = json_decode($getItemTaxRule['data'], true);
                if (!empty($oneItemData["igst"])) {
                    $total_tax_item = $oneItemData["igst"];
                } else if (!empty($oneItemData["sgst"]) && !empty($oneItemData["cgst"])) {
                    $total_tax_item = $oneItemData["cgst"] + $oneItemData["sgst"]; // Set a default value if "igst" is empty
                } else if (!empty($oneItemData["taxComponents"])) {

                    $data1 = json_decode($oneItemData["taxComponents"]);
                    if (is_array($data1)) {
                        foreach ($data1 as $item) {
                            if (isset($item->taxAmount)) {
                                $total_tax_item += (float)$item->taxAmount;
                            }
                        }
                    }
                }
                $itemTotalPrice = ($itemUnitPrice * $itemQty) + $total_tax_item - $tds_value;;
            }

            $internalItemId = "";
            $internalItemCode = "";
            $internalItemHsn = "";

            $internalItemId = $oneItemData["goodId"];
            $internalItemCode = $oneItemData["goodCode"];
            $internalItemUom = $oneItemData["itemUOM"];

            // $itemType = $oneItemData["type"];
            $itemHSN = $oneItemData["goodHsn"];
            $itemName = $oneItemData["goodName"];
            $itemStorageLocation = $oneItemData["itemStorageLocation"];

            $sl += 1;

?>

            <?php
            if ($list_type == "service") {
            ?>
                <tr class="serviceclass" id="grnItemRowTr_<?= $sl ?>">
                <?php
            } else {
                ?>
                <tr class="goodsclass" id="grnItemRowTr_<?= $sl ?>">
                <?php
            }
                ?>


                <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                <input type="hidden" id="internalItemPo_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemPurchaseOrder]" value="<?= $po ?>" />
                <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemQty]" value="0" />
                <input type="hidden" class="ItemInvoiceTotalPriceStatic" id="ItemInvoiceTotalPriceStatic_<?= $sl ?>" value="0" />
                <input type="hidden" name="grnItemPostedList[<?= $sl ?>][itemTax]" value="0" />
                <!-- <input type="hidden" name="grnItemPostedList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemGrandTotalPrice]" value="0" />
                <input type="hidden" class="ItemInvoiceTotalPrice" id="ItemInvoiceTotalPrice_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemTotalPrice]" value="0" />
                <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemInvoiceTDSValue]" value="0" />
                <input type="hidden" class="ItemInvoiceCGSTClass" id="ItemInvoiceCGST_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemCGST]" value="0" />
                <input type="hidden" class="ItemInvoiceSGSTClass" id="ItemInvoiceSGST_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemSGST]" value="0" />
                <input type="hidden" class="ItemInvoiceIGSTClass" id="ItemInvoiceIGST_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemIGST]" value="0" />
                <input type="hidden" id="ItemInvoiceCGSTNew_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemCGSTNew]" value="0" />
                <input type="hidden" id="ItemInvoiceSGSTNew_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemSGSTNew]" value="0" />
                <input type="hidden" id="ItemInvoiceIGSTNew_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemIGSTNew]" value="0" />
                <input type="hidden" id="ItemInvoiceUnits_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemUnits]" value="<?= $invoice_units ?>" />
                <input type="hidden" id="ItemInvoiceUOM_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemUOM]" value="<?= $internalItemUom ?>" />
                <input type="hidden" id="itemStockQty_<?= $sl ?>" value="<?= $itemQty ?>" class="form-control" name="grnItemPostedList[<?= $sl ?>][itemStockQty]">
                <input type="hidden" id="itemVendorName_<?= $sl ?>" value="<?= $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0]['trade_name'] ?>" class="form-control" name="grnItemPostedList[<?= $sl ?>][vendorName]">
                <input type="hidden" id="itemVendorCode_<?= $sl ?>" value="<?= $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0]['vendor_code'] ?>" class="form-control" name="grnItemPostedList[<?= $sl ?>][vendorCode]">
                <input type="hidden" id="itemVendorId_<?= $sl ?>" value="<?= $vendor_id ?>" class="form-control" name="grnItemPostedList[<?= $sl ?>][vendor_id]">
                <input type="hidden" id="" value="posted" class="form-control" name="grnItemPostedList[<?= $sl ?>][postedType]">
                <input type="hidden" id="allocated_array_<?= $sl ?>" value="" class="form-control" name="grnItemPostedList[<?= $sl ?>][allocated_array]">
                <input type="hidden" id="temporary_allocated_array_<?= $sl ?>" value="" class="form-control" name="grnItemPostedList[<?= $sl ?>][temporary_allocated_array]">

                <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][grnno]" value="<?= $grn ?>" />
                <?php
                if ($list_type == "service") {
                ?>
                    <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
                <?php
                } else {
                ?>
                    <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemInvoiceGoodsType]" value="goods" />
                <?php
                }
                ?>
                <input type="hidden" id="ItemInvoiceUOMID_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemUOMID]" value="<?= $internalItemuom_id ?>" />

                <td><?= $sl ?></td>
                <td id="grnItemPOTdSpan_<?= $sl ?>">
                    <p class="pre-normal <?= $grn ?>"><?= $grn ?></p>
                </td>
                <td>
                    <?= $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0]['trade_name'] ?>
                </td>
                <td>
                    <?= $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0]['vendor_code'] ?>
                </td>
                <td id="grnItemNameTdSpan_<?= $sl ?>">
                    <p class="pre-normal"><?= $itemName ?></p>
                </td>
                <td class="grnItemCodeTdSpan" id="grnItemCodeTdSpan_<?= $sl ?>">
                    <?php
                    echo $internalItemCode;
                    ?>
                </td>
                <td class="grnItemHSNTdSpan" id="grnItemHSNTdSpan_<?= $sl ?>"><?= $itemHSN ?></td>

                <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                    <select class="form-control text-xs" id="itemStorageLocationId_<?= $sl ?>" name="grnItemPostedList[<?= $sl ?>][itemStorageLocationId]" required>
                        <option value="">Select storage location</option>
                        <?php


                        $itemId = $internalItemId;
                        // getqaListForGrnObj

                        foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                            if ($oneRmStorageLocation["storage_location_id"] == $itemStorageLocation) {
                                echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                            } else {
                                echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                            }
                        }



                        // foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                        //     echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                        // }
                        ?>
                    </select>
                </td>


                <td>
                    <div class="form-input d-flex gap-2">
                        <input step="any" type="number" name="grnItemPostedList[<?= $sl ?>][itemReceivedQty]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs received_quantity" required readonly>
                        <input type="hidden" name="grnItemPostedList[<?= $sl ?>][itemRemainQty]" id="grnPoInputRemainQty_<?= $sl ?>" value="0">
                        <input type="hidden" name="grnItemPostedList[<?= $sl ?>][poQty]" id="grnPoQty_<?= $sl ?>" value="<?= $itemQty ?>">
                        <p class="text-xs"><?= $internalItemUom ?></p>
                    </div>
                </td>
                <!-- <td class="text-right" id="grnItemInvoiceUnitPriceTdSpan_<?= $sl ?>"><?= number_format($itemUnitPrice, 2) ?></td> -->
                <td>
                    <div class="input-group input-group-sm m-0" style="flex-wrap: nowrap;">
                        <div class="input-group-prepend">
                            <span class="input-group-text spanInvoiceCurrencyName" id="spanInvoiceCurrencyName_<?= $sl ?>"><?= $curr_name ?></span>
                        </div>
                        <input type="number" name="grnItemPostedList[<?= $sl ?>][itemUnitPriceOtherCurrency]" value="<?= number_format($itemUnitPrice, 2, '.', '') ?>" id="grnItemUnitPriceTdInput_<?= $sl ?>" class="form-control border py-3 text-right itemUnitPrice" required readonly>
                        <input type="hidden" name="grnItemPostedList[<?= $sl ?>][itemUnitPricehidden]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceTdInputhidden_<?= $sl ?>" class="form-control text-xs itemUnitPricehidden">
                        <input type="hidden" name="grnItemPostedList[<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceInrhidden_<?= $sl ?>" class="form-control text-xs grnItemUnitPriceInrhidden">
                    </div>
                    <span class="text-small spanUnitPriceINR" id="spanUnitPriceINR_<?= $sl ?>"></span>
                    <!-- <div class="form-input">
                
            </div> -->
                </td>
                <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($itemUnitPrice * $itemQty, 2) ?>
                    <p class="text-small spanBasePriceINR" id="spanBasePriceINR_<?= $sl ?>"></p>
                </td>
                <input type="hidden" name="grnItemPostedList[<?= $sl ?>][allocatedCost]" id="grnItemAllocatedCosthidden_<?= $sl ?>" value="0">
                <td id="grnItemAllocatedCost_<?= $sl ?>">0</td>
                <?php if ($companyCountry == 103) { ?>
                    <td class="text-right" id="grnItemInvoiceCGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($cgst, 2) ?>
                        <span class="text-small spanCgstPriceINR" id="spanCgstPriceINR_<?= $sl ?>"></span>
                    </td>
                    <td class="text-right" id="grnItemInvoiceSGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($sgst, 2) ?>
                        <span class="text-small spanSgstPriceINR" id="spanSgstPriceINR_<?= $sl ?>"></span>
                    </td>
                    <td class="text-right" id="grnItemInvoiceIGSTTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($igst, 2) ?>
                        <span class="text-small spanIgstPriceINR" id="spanIgstPriceINR_<?= $sl ?>"></span>
                    </td>


                    <?php } else {
                    if (isset($data['tax']) && is_array($data['tax'])) {
                        foreach ($data['tax'] as $t) {
                            $tax_per = $t['taxPercentage'];
                    ?>

                            <td class="text-right" id="grnItemInvoice<?= $t['taxComponentName'] ?>TdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format(($total_tax_item * $tax_per) / 100, 2) ?>
                                <span class="text-small span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR" id="span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR_<?= $sl ?>"></span>
                            </td>

                        <?php
                        }
                        ?>

                        <input type="hidden" id="hiddenTaxValues_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][hiddenTaxValues]" value="">

                        <!-- <input type="hidden" id="hiddenTaxValues_<?= $sl ?>" name="hiddenTaxValues[<?= $sl ?>]" value=""> -->

                <?php
                    }
                } ?>
                <td>


                    <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                        <input type="number" name="grnItemPostedList[<?= $sl ?>][itemTds]" value="0" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-xs itemTds" required readonly>
                        <p class="text-xs">%</p>
                    </div>
                </td>
                <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </span>
                <input type="hidden" value="<?= $tax_percentage ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">

                <td class="text-right" id="grnItemDeleteTdSpan_<?= $sl ?>"><button title="Delete Item" type="button" id="grnItemDeleteButton_<?= $sl ?>" class="btn btn-sm remove_row" style="cursor: pointer; border:none"><i class="fa fa-trash po-list-icon"></i></button></td>
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