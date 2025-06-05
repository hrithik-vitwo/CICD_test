<?php
// require_once("../../../connection-branch-admin.php");


class FailedAccController
{

    function logAccountingFailure($last_source_id, $source_type)
    {

        global $company_id;
        global $branch_id;
        global $location_id;
        global $created_by;
        global $updated_by;

        $dbCon = new Database();

        //failed invoice table variable
        $sourceId = "";
        $documentNo = "";
        $docType = $source_type;
        $totalAmount = "";
        $totalTax = "";
        $status = "";
        $getLastInvoiceData = "";
        $encoded_inv_data = "";


        if ($source_type == "inv") {

            $getLastInvoiceSql = $dbCon->queryGet("SELECT * FROM `erp_branch_sales_order_invoices` WHERE company_id = $company_id AND branch_id = $branch_id AND so_invoice_id = $last_source_id");

            $getLastInvoiceItemsSql = $dbCon->queryGet("SELECT * FROM `erp_branch_sales_order_invoice_items` WHERE  so_invoice_id = $last_source_id", true);
            if ($getLastInvoiceSql['numRows'] > 0) {

                $getLastInvoiceData = $getLastInvoiceSql['data'];
                $sourceId = $getLastInvoiceData['so_invoice_id'];
                $documentNo = $getLastInvoiceData['invoice_no'];
                $totalAmount = $getLastInvoiceData['all_total_amt'];
                $totalTax = $getLastInvoiceData['total_tax_amt'];
                $status = "open";
                $encoded_inv_data = json_encode($getLastInvoiceData);
            }

            if ($getLastInvoiceItemsSql['numRows'] > 0) {
                $getLastInvoiceItemsData = $getLastInvoiceItemsSql['data'];
                foreach ($getLastInvoiceItemsData as $item) {
                    $encoded_data = json_encode($item);

                    $insertFailedItemLog = $dbCon->queryInsert("INSERT INTO `erp_failed_acc_items`
                    SET 
                        failure_item_id = " . $item['so_invoice_item_id'] . " ,
                        inventory_item_id	 = " . $item['inventory_item_id'] . ",
                        source_id       =  " . $item['so_invoice_id'] . ",
                        itemCode         = '" . $item['itemCode'] . "',
                        itemName         = '" . $item['itemName'] . "',
                        itemHsn          = '" . $item['hsnCode'] . "',
                        invoice_item_qty = '" . $item['qty'] . "',
                        company_id  = '$company_id',
                        branch_id  ='$branch_id',
                        location_id = '$location_id',
                        source_type  = '$source_type',
                        itemUom          = '" . $item['uom'] . "',
                        itemTotalTax     = '" . $item['totalTax'] . "',
                        itemTotalAmt     = '" . $item['totalPrice'] . "',
                        encoded_data     = '" . $encoded_data . "',
                        createdBy        = '" . $created_by . "',
                        updatedBy        = '" . $updated_by . "'");
                }
                if ($insertFailedItemLog['status'] != "success") {
                    $returnData['status'] = 'failed';
                    $returnData['message'] = 'Item logged failed';
                    $returnData['sql'] = $insertFailedItemLog;
                }
            }
        } elseif ($source_type == "grn" || $source_type == "srn") {
            $getLastInvoiceSql = $dbCon->queryGet("SELECT * FROM `erp_grn` WHERE companyId = $company_id AND branchId = $branch_id AND grnId = $last_source_id");

            $getLastInvoiceItemsSql = $dbCon->queryGet("SELECT * FROM `erp_grn_goods` WHERE  grnId = $last_source_id", true);

            if ($getLastInvoiceSql['numRows'] > 0) {
                $getLastInvoiceData = $getLastInvoiceSql['data'];
                $sourceId = $getLastInvoiceData['grnId'];
                $documentNo = $getLastInvoiceData['grnCode'];
                $totalAmount = $getLastInvoiceData['grnTotalAmount'];
                $totalTax = $getLastInvoiceData['grnTotalCgst'] + $getLastInvoiceData['grnTotalSgst'] + $getLastInvoiceData['grnTotalIgst'];
                $status = "open";
                $encoded_inv_data = json_encode($getLastInvoiceData);
            }
            if ($getLastInvoiceItemsSql['numRows'] > 0) {
                $getLastInvoiceItemsData = $getLastInvoiceItemsSql['data'];
                foreach ($getLastInvoiceItemsData as $item) {
                    $encoded_data = json_encode($item);
                    $itemTotalTax = $item['cgst'] + $item['sgst'] + $item['igst'];
                    $uomsql = $dbCon->queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomName` ='" . $item['itemUOM'] . "'")['data'];
                    $insertFailedItemLog = $dbCon->queryInsert("INSERT INTO `erp_failed_acc_items`
                    SET 
                        failure_item_id = " . $item['grnGoodId'] . " ,
                        inventory_item_id	 = " . $item['goodId'] . ",
                        source_id       =  " . $item['grnId'] . ",
                        itemCode         = '" . $item['goodCode'] . "',
                        itemName         = '" . $item['goodName'] . "',
                        itemHsn          = '" . $item['goodHsn'] . "',
                        invoice_item_qty = '" . $item['goodQty'] . "',
                        company_id  = '$company_id',
                        branch_id  ='$branch_id',
                        location_id = '$location_id',
                        source_type  = '$source_type',
                        itemUom          = '" . $uomsql['uomId'] . "',
                        itemTotalTax     = '$itemTotalTax',
                        itemTotalAmt     = '" . $item['totalAmount'] . "',
                        encoded_data     = '" . $encoded_data . "',
                        createdBy        = '" . $created_by . "',
                        updatedBy        = '" . $updated_by . "'");
                }
                if ($insertFailedItemLog['status'] != "success") {
                    $returnData['status'] = 'failed';
                    $returnData['message'] = 'Item logged failed';
                    $returnData['sql'] = $insertFailedItemLog;
                }
                $returnData['item'] = $insertFailedItemLog;
            }
        } elseif ($source_type == "grniv" || $source_type == "srniv") {
            $getLastInvoiceSql = $dbCon->queryGet("SELECT * FROM `erp_grninvoice` WHERE companyId = $company_id AND branchId = $branch_id AND grnIvId = $last_source_id");

            $getLastInvoiceItemsSql = $dbCon->queryGet("SELECT * FROM `erp_grninvoice_goods` WHERE  grnId = $last_source_id", true);

            if ($getLastInvoiceSql['numRows'] > 0) {
                $getLastInvoiceData = $getLastInvoiceSql['data'];
                $sourceId = $getLastInvoiceData['grnIvId'];
                $documentNo = $getLastInvoiceData['grnIvCode'];
                $totalAmount = $getLastInvoiceData['grnTotalAmount'];
                $totalTax = $getLastInvoiceData['grnTotalCgst'] + $getLastInvoiceData['grnTotalSgst'] + $getLastInvoiceData['grnTotalIgst'];
                $status = "open";
                $encoded_inv_data = json_encode($getLastInvoiceData);
            }
            if ($getLastInvoiceItemsSql['numRows'] > 0) {
                $getLastInvoiceItemsData = $getLastInvoiceItemsSql['data'];
                foreach ($getLastInvoiceItemsData as $item) {
                    $encoded_data = json_encode($item);
                    $itemTotalTax = $item['cgst'] + $item['sgst'] + $item['igst'];
                    $uomsql = $dbCon->queryGet("SELECT * FROM `erp_inventory_mstr_uom` WHERE `uomName` ='" . $item['itemUOM'] . "'")['data'];
                    $insertFailedItemLog = $dbCon->queryInsert("INSERT INTO `erp_failed_acc_items`
                    SET 
                        failure_item_id = " . $item['grnIvGoodId'] . " ,
                        inventory_item_id	 = " . $item['goodId'] . ",
                        source_id       =  " . $item['grnId'] . ",
                        itemCode         = '" . $item['goodCode'] . "',
                        itemName         = '" . $item['goodName'] . "',
                        itemHsn          = '" . $item['goodHsn'] . "',
                        invoice_item_qty = '" . $item['goodQty'] . "',
                        company_id  = '$company_id',
                        branch_id  ='$branch_id',
                        location_id = '$location_id',
                        source_type  = '$source_type',
                        itemUom          = '" . $uomsql['uomId'] . "',
                        itemTotalTax     = '$itemTotalTax',
                        itemTotalAmt     = '" . $item['totalAmount'] . "',
                        encoded_data     = '" . $encoded_data . "',
                        createdBy        = '" . $created_by . "',
                        updatedBy        = '" . $updated_by . "'");
                }
                if ($insertFailedItemLog['status'] != "success") {
                    $returnData['status'] = 'failed';
                    $returnData['message'] = 'Item logged failed';
                    $returnData['sql'] = $insertFailedItemLog;
                }
                $returnData['item'] = $insertFailedItemLog;
            }
        } elseif ($source_type == "CreditNote") {
            $getLastInvoiceSql = $dbCon->queryGet("SELECT * FROM `erp_credit_note` WHERE company_id = $company_id AND branch_id = $branch_id AND cr_note_id = $last_source_id");
            $getLastInvoiceItemsSql = $dbCon->queryGet("SELECT * FROM `credit_note_item` WHERE  credit_note_id = $last_source_id", true);
            if ($getLastInvoiceSql['numRows'] > 0) {
                $getLastInvoiceData = $getLastInvoiceSql['data'];
                $sourceId = $getLastInvoiceData['cr_note_id'];
                $documentNo = $getLastInvoiceData['credit_note_no'];
                $totalAmount = $getLastInvoiceData['total'];
                $taxComponents = $getLastInvoiceData['taxComponents'];
                $data = json_decode($taxComponents, true);
                foreach ($data as $item) {
                    $totalTax += $item['taxAmount'];
                }
                $status = "open";
                $encoded_inv_data = json_encode($getLastInvoiceData);
            }
            if ($getLastInvoiceItemsSql['numRows'] > 0) {
                $getLastInvoiceItemsData = $getLastInvoiceItemsSql['data'];
                foreach ($getLastInvoiceItemsData as $item) {
                    $credit_note_item_id = $item['credit_note_item_id'];
                    $item_id = $item['item_id'];
                    $credit_note_id = $item['credit_note_id'];
                    $item_qty = $item['item_qty'];
                    $item_amount = $item['item_amount'];
                    $encoded_data = json_encode($item);
                    $taxComponentsitem = $item['taxComponents'];
                    $dataitem = json_decode($taxComponentsitem, true);
                    $totalTaxAmountitem = 0;
                    foreach ($dataitem as $item) {
                        $totalTaxAmountitem += $item['taxAmount'];
                    }
                    $itemsql = $dbCon->queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId` = $item_id AND `company_id` = " . $company_id . "")["data"];
                    $itemCode = $itemsql['itemCode'];
                    $itemName = $itemsql['itemName'];
                    $hsnCode = $itemsql['hsnCode'];
                    $uomRel = $itemsql['uomRel'];

                    $insertFailedItemLog = $dbCon->queryInsert("INSERT INTO `erp_failed_acc_items`
                    SET 
                        failure_item_id = '$credit_note_item_id',
                        inventory_item_id = '$item_id',
                        source_id       =  '$credit_note_id',
                        itemCode         = '$itemCode',
                        itemName         = '$itemName',
                        itemHsn          = '$hsnCode',
                        invoice_item_qty = '$item_qty',
                        company_id  = '$company_id',
                        branch_id  ='$branch_id',
                        location_id = '$location_id',
                        source_type  = '$source_type',
                        itemUom          = '$uomRel',
                        itemTotalTax     = '$totalTaxAmountitem',
                        itemTotalAmt     = '$item_amount',
                        encoded_data     = '" . $encoded_data . "',
                        createdBy        = '" . $created_by . "',
                        updatedBy        = '" . $updated_by . "'");
                }
                if ($insertFailedItemLog['status'] != "success") {
                    $returnData['status'] = 'failed';
                    $returnData['message'] = 'Item logged failed';
                    $returnData['sql'] = $insertFailedItemLog;
                }
                $returnData['item'] = $insertFailedItemLog;
            }
        } elseif ($source_type == "debitNote") {
            $getLastInvoiceSql = $dbCon->queryGet("SELECT * FROM `erp_debit_note` WHERE company_id = $company_id AND branch_id = $branch_id AND cr_note_id = $last_source_id");
            $getLastInvoiceItemsSql = $dbCon->queryGet("SELECT * FROM `debit_note_item` WHERE  credit_note_id = $last_source_id", true);
            if ($getLastInvoiceSql['numRows'] > 0) {
                $getLastInvoiceData = $getLastInvoiceSql['data'];
                $sourceId = $getLastInvoiceData['debit_note_id'];
                $documentNo = $getLastInvoiceData['debit_note_no'];
                $totalAmount = $getLastInvoiceData['total'];
                $taxComponents = $getLastInvoiceData['taxComponents'];
                $data = json_decode($taxComponents, true);
                foreach ($data as $item) {
                    $totalTax += $item['taxAmount'];
                }
                $status = "open";
                $encoded_inv_data = json_encode($getLastInvoiceData);
            }
            if ($getLastInvoiceItemsSql['numRows'] > 0) {
                $getLastInvoiceItemsData = $getLastInvoiceItemsSql['data'];
                foreach ($getLastInvoiceItemsData as $item) {
                    $credit_note_item_id = $item['credit_note_item_id'];
                    $item_id = $item['item_id'];
                    $credit_note_id = $item['credit_note_id'];
                    $item_qty = $item['item_qty'];
                    $item_amount = $item['item_amount'];
                    $encoded_data = json_encode($item);
                    $taxComponentsitem = $item['taxComponents'];
                    $dataitem = json_decode($taxComponentsitem, true);
                    $totalTaxAmountitem = 0;
                    foreach ($dataitem as $item) {
                        $totalTaxAmountitem += $item['taxAmount'];
                    }
                    $itemsql = $dbCon->queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId` = $item_id AND `company_id` = " . $company_id . "")["data"];
                    $itemCode = $itemsql['itemCode'];
                    $itemName = $itemsql['itemName'];
                    $hsnCode = $itemsql['hsnCode'];
                    $uomRel = $itemsql['uomRel'];

                    $insertFailedItemLog = $dbCon->queryInsert("INSERT INTO `erp_failed_acc_items`
                    SET 
                        failure_item_id = '$credit_note_item_id',
                        inventory_item_id = '$item_id',
                        source_id       =  '$credit_note_id',
                        itemCode         = '$itemCode',
                        itemName         = '$itemName',
                        itemHsn          = '$hsnCode',
                        invoice_item_qty = '$item_qty',
                        company_id  = '$company_id',
                        branch_id  ='$branch_id',
                        location_id = '$location_id',
                        source_type  = '$source_type',
                        itemUom          = '$uomRel',
                        itemTotalTax     = '$totalTaxAmountitem',
                        itemTotalAmt     = '$item_amount',
                        encoded_data     = '" . $encoded_data . "',
                        createdBy        = '" . $created_by . "',
                        updatedBy        = '" . $updated_by . "'");
                }
                if ($insertFailedItemLog['status'] != "success") {
                    $returnData['status'] = 'failed';
                    $returnData['message'] = 'Item logged failed';
                    $returnData['sql'] = $insertFailedItemLog;
                }
                $returnData['item'] = $insertFailedItemLog;
            }
        } elseif ($source_type == "production") {
            $getLastInvoiceSql = $dbCon->queryGet("SELECT * FROM `erp_production_declarations` WHERE company_id = $company_id AND branch_id = $branch_id AND id = $last_source_id");
            $sub_prod_id = $getLastInvoiceData['getLastInvoiceData'];
            $totalAmount=0;
            $totalTax=0;
            if ($getLastInvoiceSql['numRows'] > 0) {
                $getLastInvoiceData = $getLastInvoiceSql['data'];
                $sourceId = $getLastInvoiceData['id'];
                $documentNo = $getLastInvoiceData['code'];
                $sub_prod_id=$getLastInvoiceData['sub_prod_id'];
                $status = "open";
                $encoded_inv_data = json_encode($getLastInvoiceData);
                $getLastInvoiceItemsSql = $dbCon->queryGet("SELECT * FROM `erp_production_order_sub` WHERE  sub_prod_id = $sub_prod_id");
                $getLastInvoiceItemsData = $getLastInvoiceItemsSql['data'];
                $item_id=$getLastInvoiceItemsData['itemId'];
                $itemCode=$getLastInvoiceItemsData['itemCode'];
                $item_qty=$getLastInvoiceItemsData['prodQty'];
                $itemsql = $dbCon->queryGet("SELECT * FROM `erp_inventory_items` WHERE `itemId` = $item_id AND `company_id` = " . $company_id . "")["data"];
                $uomRel = $itemsql['uomRel'];
                $hsnCode = $itemsql['hsnCode'];
                $itemName= $itemsql['itemName'];
                $encoded_data = json_encode($getLastInvoiceItemsSql);
                $insertFailedItemLog = $dbCon->queryInsert("INSERT INTO `erp_failed_acc_items`
                    SET 
                        failure_item_id = '$item_id',
                        inventory_item_id = '$item_id',
                        source_id       =  '$sub_prod_id',
                        itemCode         = '$itemCode',
                        itemName         = '$itemName',
                        itemHsn          = '$hsnCode',
                        invoice_item_qty = '$item_qty',
                        company_id  = '$company_id',
                        branch_id  ='$branch_id',
                        location_id = '$location_id',
                        source_type  = '$source_type',
                        itemUom          = '$uomRel',
                        encoded_data     = '" . $encoded_data . "',
                        createdBy        = '" . $created_by . "',
                        updatedBy        = '" . $updated_by . "'");
                }
                if ($insertFailedItemLog['status'] != "success") {
                    $returnData['status'] = 'failed';
                    $returnData['message'] = 'Item logged failed';
                    $returnData['sql'] = $insertFailedItemLog;
                }
                 $returnData['item'] = $insertFailedItemLog;
        }
        
        $insertFailedDocs = $dbCon->queryInsert("INSERT INTO erp_failed_acc_documents 
                                 SET 
                                    source_id = '$sourceId',
                                    company_id= $company_id,
                                    branch_id  ='$branch_id',
                                    location_id = '$location_id',
                                    document_no = '$documentNo',
                                    doc_type = '$docType',
                                    total_amount = '$totalAmount',
                                    total_tax = '$totalTax',
                                    `status` = '$status',
                                    created_by = '$created_by',
                                    updated_by= '$updated_by',
                                    encoded_data = '$encoded_inv_data'");
        if ($insertFailedDocs['status'] == "success") {
            $returnData['status'] = 'success';
            $returnData['message'] = 'Failure logged successfully';
            $returnData['sql'] = $insertFailedDocs;
        } else {
            $returnData['status'] = 'failed';
            $returnData['message'] = 'Failed to log failure';
            $returnData['sql'] = $insertFailedDocs;
            $returnData['last_source_id'] = $last_source_id;
            $returnData['source_type'] = $source_type;
        }

        return $returnData;
    }
}
function updatelogAccountingFailure($documentNo)
{
    global $company_id;
    $dbCon = new Database();

    $failedQuery = "UPDATE `erp_failed_acc_documents` SET `status` = 'closed' WHERE `document_no` = '" . $documentNo . "'AND `company_id`=" . $company_id . " ";
    $failedUpdate = $dbCon->queryUpdate($failedQuery);
    if ($failedUpdate['status'] == "success") {
        $returnData['status'] = 'success';
        $returnData['message'] = 'Failure closed successfully';
    } else {
        $returnData['status'] = 'failed';
        $returnData['message'] = 'Failed to close';
    }
    return $returnData;
}
