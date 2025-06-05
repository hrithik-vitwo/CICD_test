<?php
include_once("../../../../app/v1/connection-branch-admin.php");
include("../../../../app/v1/functions/branch/func-ocr-invoice-controller.php");
include("../../../../app/v1/functions/branch/func-goods-controller.php");
require_once("../../../../app/v1/functions/branch/func-brunch-po-controller.php");
$BranchPoObj = new BranchPo();
if (isset($_GET["po"]) && $_GET["po"] != "" && isset($_GET["serial_number"]) && $_GET["serial_number"] != "" && isset($_GET["listtype"]) && $_GET["listtype"] != "") {
    global $company_id;
    global $branch_id;
    global $location_id;
    $po = $_GET["po"];
    $list_type = $_GET["listtype"];

    function getStorageLocationListForGrn()
    {
        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;
        global $isQaEnabled;
        global $companyCountry;

        return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` AS storage LEFT JOIN `erp_storage_warehouse` ON erp_storage_warehouse.`warehouse_id`= storage.`warehouse_id` WHERE storage.`company_id`=' . $company_id . ' AND storage.`branch_id`=' . $branch_id . ' AND storage.`location_id`=' . $location_id . ' AND storage.`storage_location_type` IN ("RM-WH","FG-WH","QA","Asset") AND storage.`storage_location_material_type` IN ("RM","FG","QA","Asset") AND storage.`storage_location_storage_type`="Open" AND storage.`status`="active"', true);

        // if($isQaEnabled == 1)
        // {
        //     return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` AS storage LEFT JOIN `erp_storage_warehouse` ON erp_storage_warehouse.`warehouse_id`= storage.`warehouse_id` WHERE storage.`company_id`=' . $company_id . ' AND storage.`branch_id`=' . $branch_id . ' AND storage.`location_id`=' . $location_id . ' AND storage.`storage_location_type` = "QA" AND storage.`storage_location_material_type` = "QA" AND storage.`status`="active"', true);
        // }
        // else
        // {
        //     return queryGet('SELECT * FROM `' . ERP_STORAGE_LOCATION . '` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `storage_location_type` IN ("RM-WH","FG-WH") AND `storage_location_material_type` IN ("RM","FG") AND `storage_location_storage_type`="Open" AND `status`="active"', true);
        // }
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
        $vendorCreditPeriod = $poDetails["vendor_credit_period"] ?? 0;

        $functional_area = $poDetails["functional_area"];

        // console($vendorCreditPeriod);

        $totalCGST = 0;
        $totalSGST = 0;
        $totalIGST = $total_tax == "" ? 0 : $total_tax;
        $totalTdsValue = 0;
        $getStorageLocationListForGrnObj = getStorageLocationListForGrn();
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

            $itemTax = $itemTax == "" ? 0 : $itemTax;
            $tds = 0;
            $tds_id = $oneItemData["tds"];
            $tds_query = queryGet("SELECT `TDSRate` FROM `erp_tds_details` WHERE `id`='" . $tds_id . "'");
            $tds = $tds_query["data"]["TDSRate"] ?? 0;

            $basic_amt = ($itemUnitPrice * $itemQty);

            $tds_value = $basic_amt * ($tds / 100);
            if ($companyCountry == 103) {
                if ($vendorGstinStateCode == $customerGstinStateCode) {
                    $cgst = $itemTax / 2;
                    $sgst = $itemTax / 2;
                    $igst = 0;
                } else {
                    $cgst = 0;
                    $sgst = 0;
                    $igst = $itemTax == "" ? 0 : $itemTax;
                }

                $itemTotalPrice = ($itemUnitPrice * $itemQty) + $cgst + $sgst + $igst - $tds_value;;
            } else {
                $getItemTaxRule = getItemTaxRule($companyCountry, $vendorGstinStateCode, $customerGstinStateCode);
                $data = json_decode($getItemTaxRule['data'], true);

                $cgst = 0;
                $sgst = 0;
                $igst = 0;
                $itemTotalPrice = ($itemUnitPrice * $itemQty) + $itemTax - $tds_value;
            }
            $internalItemId = "";
            $internalItemCode = "";
            $internalItemHsn = "";

            $internalItemId = $oneItemData["inventory_item_id"];
            $internalItemCode = $oneItemData["itemCode"];
            $internalItemUom = $oneItemData["uom"];

            // $itemType = $oneItemData["type"];
            $itemHSN = $oneItemData["hsnCode"];
            $itemName = $oneItemData["itemName"];

            // $itemHSN = $oneItemData["ProductCode"] ?? $itemHSN;

            //Check for mapped Item
            // if ($internalItemCode == "") {
            //     $itemHSN = $oneItemData["ProductCode"];
            //     $itemName = $oneItemData["Description"] ?? "";
            // }

            array_push($po_ids, $oneItemData["po_item_id"]);

            if ($itemName == "" || strtolower($itemName) == "cgst" || strtolower($itemName) == "sgst") {
                continue;
            }
            $sl += 1;

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


                <input type="hidden" id="internalItemId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemId]" value="<?= $internalItemId ?>" />
                <input type="hidden" id="internalPoItemId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][PoItemId]" value="<?= $oneItemData["po_item_id"] ?>" />
                <input type="hidden" class="linePoNumber" id="internalItemPo_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemPurchaseOrder]" value="<?= $po ?>" />
                <input type="hidden" id="internalItemCode_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCode]" value="<?= $internalItemCode ?>" />
                <input type="hidden" id="internalItemHsn_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemHsn]" value="<?= $itemHSN ?>" />
                <input type="hidden" id="internalItemName_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemName]" value="<?= $itemName ?>" />
                <input type="hidden" id="grnItemQty_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemQty]" value="<?= $itemQty ?>" />
                <input type="hidden" class="ItemInvoiceTotalPriceStatic" id="ItemInvoiceTotalPriceStatic_<?= $sl ?>" value="<?= $itemUnitPrice * $itemQty ?>" />
                <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTax]" value="<?= $itemTax ?>" />
                <!-- <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" /> -->
                <input type="hidden" id="ItemGRNName_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemGRNName]" value="<?= $grnItemName ?>" />
                <input type="hidden" class="ItemInvoiceGrandTotalPrice" id="ItemInvoiceGrandTotalPrice_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemGrandTotalPrice]" value="<?= $itemTotalPrice ?>" />
                <input type="hidden" class="ItemInvoiceTotalPrice" id="ItemInvoiceTotalPrice_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTotalPrice]" value="<?= $itemUnitPrice * $itemQty ?>" />
                <input type="hidden" class="ItemInvoiceTDSValue" id="ItemInvoiceTDSValue_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemInvoiceTDSValue]" value="<?= $tds_value ?>" />
                <?php if ($companyCountry == 103) { ?>
                    <input type="hidden" class="ItemInvoiceCGSTClass" id="ItemInvoiceCGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCGST]" value="<?= $cgst ?>" />
                    <input type="hidden" class="ItemInvoiceSGSTClass" id="ItemInvoiceSGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemSGST]" value="<?= $sgst ?>" />
                    <input type="hidden" class="ItemInvoiceIGSTClass" id="ItemInvoiceIGST_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemIGST]" value="<?= $igst ?>" />
                    <input type="hidden" id="ItemInvoiceCGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemCGSTNew]" value="<?= $cgst ?>" />
                    <input type="hidden" id="ItemInvoiceSGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemSGSTNew]" value="<?= $sgst ?>" />
                    <input type="hidden" id="ItemInvoiceIGSTNew_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemIGSTNew]" value="<?= $igst ?>" />
                    <?php } else {
                    if (isset($data['tax']) && is_array($data['tax'])) {
                        foreach ($data['tax'] as $t) {

                            $tax_per = $t['taxPercentage'];
                    ?>
                            <input type="hidden" class="ItemInvoice<?= $t['taxComponentName'] ?>Class" id="ItemInvoice<?= $t['taxComponentName'] ?>_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][item<?= $t['taxComponentName'] ?>]" value="<?= round(($itemTax * $tax_per) / 100, 2); ?>" />
                            <input type="hidden" id="ItemInvoice<?= $t['taxComponentName'] ?>New_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][item<?= $t['taxComponentName'] ?>New]" value="<?= round(($itemTax * $tax_per) / 100, 2); ?>" />


                        <?php
                        }
                        ?>
                        <!-- <input type="hidden" id="hiddenTaxValues_<?= $sl ?>" name="hiddenTaxValues[<?= $sl ?>]" value=""> -->



                <?php
                    }
                } ?>
                <input type="hidden" id="hiddenTaxValues_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][hiddenTaxValues]" value="">

                <input type="hidden" id="ItemInvoiceUnits_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnits]" value="<?= $invoice_units ?>" />
                <input type="hidden" id="ItemInvoiceUOM_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUOM]" value="<?= $internalItemUom ?>" />
                <input type="hidden" id="itemStockQty_<?= $sl ?>" value="<?= $itemQty ?>" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemStockQty]">
                <input type="hidden" id="itemVendorName_<?= $sl ?>" value="<?= $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0]['trade_name'] ?>" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][vendorName]">
                <input type="hidden" id="itemVendorCode_<?= $sl ?>" value="<?= $BranchPoObj->fetchVendorDetails($vendor_id)['data'][0]['vendor_code'] ?>" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][vendorCode]">
                <input type="hidden" id="itemVendorId_<?= $sl ?>" value="<?= $vendor_id ?>" class="form-control lineVendorId" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][vendor_id]">
                <input type="hidden" id="" value="pending" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][postedType]">
                <input type="hidden" id="allocated_array_<?= $sl ?>" value="" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][allocated_array]">
                <input type="hidden" id="temporary_allocated_array_<?= $sl ?>" value="" class="form-control" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][temporary_allocated_array]">
                <?php
                if ($list_type == "service") {
                ?>
                    <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemInvoiceGoodsType]" value="service" />
                <?php
                } else {
                ?>
                    <input type="hidden" class="ItemInvoiceGoodsType" id="ItemInvoiceGoodsType_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemInvoiceGoodsType]" value="goods" />
                <?php
                }
                ?>
                <input type="hidden" id="ItemInvoiceUOMID_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUOMID]" value="<?= $internalItemuom_id ?>" />

                <td><?= $sl ?></td>
                <td id="grnItemPOTdSpan_<?= $sl ?>">
                    <p class="pre-normal <?= $po ?>"><?= $po ?></p>
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
                <?php
                if ($list_type == "service") {
                ?>
                    <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                        <select class="form-control text-xs costCenterSelect itemCostCenterId_<?= $sl ?>" id="itemCostCenterId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemStorageLocationId]" required>
                            <option value="">Select Cost Center</option>
                            <?php
                            foreach ($getCostCenterListForGrnObj["data"] as $oneCostCenter) {
                                echo '<option value="' . $oneCostCenter["CostCenter_id"] . '">' . $oneCostCenter["CostCenter_code"] . ' | ' . $oneCostCenter["CostCenter_desc"] . '</option>';
                            }
                            ?>
                            <option value="inventorise_<?= $sl ?>"><strong>Inventorise</strong></option>
                        </select>

                        <div class="modal cost-center-modal fade" id="costCenterModal_<?= $sl ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="inventorizeButton">Inventorise Cost</h1>
                                        <p><span class="text-sm font-bold" id="distribution_cost_<?= $sl ?>">0</span></p>

                                    </div>
                                    <div class="modal-body">
                                        <div class="inner-section">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Item Code</th>
                                                        <th>Item Name</th>
                                                        <th>Vendor Code</th>
                                                        <th>Vendor Name</th>
                                                        <th>Unit Price</th>
                                                        <th>Quantity</th>
                                                        <th>Basic Amount</th>
                                                        <th>Inventorise Amount</th>
                                                        <th>Allocated Cost</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="costCenterDetailsBody_<?= $sl ?>" id="costCenterId_<?= $sl ?>">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary distributeButtonClass" id="distributeButton_<?= $sl ?>" data-value="0">Distribute Cost</button>
                                        <button type="button" class="btn btn-primary inventButtonClass" id="inventButton_<?= $sl ?>" data-itemId="<?= $internalItemId ?>" data-allocatedArray="" data-sl="<?= $sl ?>">Corfirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                <?php
                } else {
                ?>

                    <td id="grnItemStrgLocTdSpan_<?= $sl ?>">
                        <select class="form-control text-xs storageLocationSelect" id="itemStorageLocationId_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemStorageLocationId]" required>
                            <option value="">Select storage location</option>
                            <?php
                            $itemId = $internalItemId;
                            $summary = queryGet("SELECT * FROM `erp_inventory_stocks_summary` WHERE `itemId`='$itemId' AND `company_id`='$company_id' AND `branch_id`='$branch_id' AND `location_id`='$location_id'", false);
                            // getqaListForGrnObj
                            if ($summary["data"]["quality_enabled"] == '1') {
                                foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                    if ($oneRmStorageLocation["storage_location_id"] == $summary["data"]["qa_storage_location"]) {
                                        echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                    } else {
                                        echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                    }
                                }
                            } else {
                                foreach ($getStorageLocationListForGrnObj["data"] as $oneRmStorageLocation) {
                                    if ($oneRmStorageLocation["storage_location_id"] == $summary["data"]["default_storage_location"]) {
                                        echo '<option selected value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                    } else {
                                        echo '<option value="' . $oneRmStorageLocation["storage_location_id"] . '">' . $oneRmStorageLocation["warehouse_code"] . ' | ' . $oneRmStorageLocation["storage_location_code"] . ' | ' . $oneRmStorageLocation["storage_location_name"] . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </td>
                <?php
                }
                ?>
                <td>
                    <div class="form-input d-flex gap-2">
                        <input step="any" type="number" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemReceivedQty]" value="<?= $itemQty ?>" id="grnItemReceivedQtyTdInput_<?= $sl ?>" class="form-control text-xs received_quantity" required>
                        <input type="hidden" name="poItemId[<?= $po_item_id ?>]" id="grnPoInputQty_<?= $sl ?>" value="0">
                        <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemRemainQty]" id="grnPoInputRemainQty_<?= $sl ?>" value="0">
                        <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][poQty]" id="grnPoQty_<?= $sl ?>" value="<?= $itemQty ?>">
                        <p class="text-xs"><?= $internalItemUom ?></p>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm m-0" style="flex-wrap: nowrap;">
                        <div class="input-group-prepend">
                            <span class="input-group-text spanInvoiceCurrencyName" id="spanInvoiceCurrencyName_<?= $sl ?>"><?= $curr_name ?></span>
                        </div>
                        <input type="number" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPriceOtherCurrency]" value="<?= number_format($itemUnitPrice, 2, '.', '') ?>" id="grnItemUnitPriceTdInput_<?= $sl ?>" class="form-control border py-3 text-right itemUnitPrice" required readonly>
                        <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPricehidden]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceTdInputhidden_<?= $sl ?>" class="form-control text-xs itemUnitPricehidden">
                        <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemUnitPrice]" value="<?= $itemUnitPrice ?>" id="grnItemUnitPriceInrhidden_<?= $sl ?>" class="form-control text-xs grnItemUnitPriceInrhidden">
                    </div>
                    <span class="text-small spanUnitPriceINR" id="spanUnitPriceINR_<?= $sl ?>"></span>
                </td>
                <td class="text-right" id="grnItemInvoiceBaseAmtTdSpan_<?= $sl ?>"><?= $curr_name . " : " . number_format($itemUnitPrice * $itemQty, 2) ?>
                    <p class="text-small spanBasePriceINR" id="spanBasePriceINR_<?= $sl ?>"></p>
                </td>
                <input type="hidden" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][allocatedCost]" id="grnItemAllocatedCosthidden_<?= $sl ?>" value="0">
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
                            <td class="text-right" id="grnItemInvoice<?= $t['taxComponentName'] ?>TdSpan_<?= $sl ?>">
                                <?= $curr_name . " : " . round(($itemTax * $tax_per) / 100, 2) ?>
                                <span class="text-small span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR" id="span<?= ucfirst(strtolower($t['taxComponentName'])) ?>PriceINR_<?= $sl ?>"></span>
                            </td>
                <?php
                        }
                    }
                } ?>
                <td>
                    <div class="form-input d-flex" style="align-items: center; gap: 7px;">
                        <input type="number" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][itemTds]" value="<?= $tds ?>" id="grnItemTdsTdInput_<?= $sl ?>" class="form-control text-xs itemTds" required>
                        <p class="text-xs">%</p>
                    </div>
                </td>
                <span style="display: none" class="text-right" id="grnItemInvoiceTotalPriceTdSpan_<?= $sl ?>"><?= number_format($itemTotalPrice, 2) ?> </span>
                <input type="hidden" value="<?= $tax_percentage ?>" id="grnItemInternalTax_<?= $sl ?>" class="form-control text-xs itemInternalTax" step="any">

                <?php
                $st_loc_id = $summary["data"]["default_storage_location"];
                // $binDetailsObj = queryGet("SELECT * FROM `erp_storage_bin` WHERE company_id='" . $company_id . "' AND branch_id='" . $branch_id . "' AND location_id='" . $location_id . "' AND storage_location_id='" . $st_loc_id . "'", true);
                // $binDetails = $binDetailsObj["data"] ?? [];

                // $options = "";
                // foreach($binDetails as $binDetail)
                // {
                //     $options .= "<option value='".$binDetail["bin_id"]."'>".$binDetail["bin_name"]."</option>";
                // }

                $rackDetailsObj = queryGet("SELECT rack_id FROM `erp_rack` WHERE storage_location_id = '" . $st_loc_id . "'", true);
                $options = "";
                // console($rackDetailsObj);
                foreach ($rackDetailsObj["data"] as $rackDetail) {
                    $rack_id = $rackDetail["rack_id"];
                    if (is_null($rack_id) || $rack_id == "")
                        continue;
                    $layerDetailsObj = queryGet("SELECT * FROM `erp_layer` WHERE rack_id = '" . $rack_id . "'", true);
                    // console($rack_id);
                    foreach ($layerDetailsObj["data"] as $layerDetail) {
                        $layer_id = $layerDetail["layer_id"];
                        if (is_null($layer_id) || $layer_id == "")
                            continue;
                        $binDetailsObj = queryGet("SELECT * FROM `erp_storage_bin` WHERE layer_id = '" . $layer_id . "'", true);
                        // console($binDetailsObj);
                        foreach ($binDetailsObj["data"] as $binDetail) {
                            $bin_id = $binDetail["bin_id"];
                            if (is_null($bin_id) || $bin_id == "")
                                continue;
                            $bin_name = $binDetail["bin_name"];
                            $options .=  "<option value='" . $bin_id . "'>" . $bin_name . "</option>";
                        }
                    }
                }


                ?>

                <script>
                    $(document).ready(function() {
                        var vendorId = <?= json_encode($vendor_id) ?>;
                        var sl = <?= json_encode($sl) ?>;
                        var itemQty = <?= json_encode($itemQty) ?>;
                        var options = <?= json_encode($options) ?>;
                        addGrnItemMultipleBatch(vendorId, sl, itemQty, true, options);
                    });
                </script>

                <input type="hidden" value="<?= $options ?>" id="grnItemAllBins_<?= $sl ?>" class="form-control">

                <td class="text-right" id="grnItemDeleteTdSpan_<?= $sl ?>">
                    <?php
                    if ($list_type != "service") {
                    ?>
                        <button type="button" class="btn-view btn btn-primary delShedulingBtn" data-toggle="modal" data-storage="<?= $summary["data"]["default_storage_location"] ?>" data-target="#deliveryScheduleModal_<?= $sl ?>">
                            <i id="statusItemBtn_<?= $internalItemId ?>" class="statusItemBtn fa fa-cog"></i>
                        </button>
                    <?php
                    }
                    ?>
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
                                            <input type="checkbox" class="grnEnableCheckBxClass" value="1" id="grnEnableCheckBx_<?= $sl ?>" name="grnItemList[<?= $vendor_id ?>][<?= $sl ?>][activateBatch]"> Enable check box to insert the manual Batch
                                            <input type="hidden" name="" id="grnStoreId_<?= $sl ?>" value="<?= $summary["data"]["default_storage_location"] ?? "" ?>">
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