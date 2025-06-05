<?php
require_once "func-branch-failed-accounting-controller.php";
class ReversePosting extends AccountingPosting
{
    private $company_id;
    private $branch_id;
    private $location_id;
    private $updated_by;
    private $created_by;
    private $failedAccController;
    private $dbObj;

    function __construct()
    {
        global $company_id, $branch_id, $location_id, $created_by, $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->updated_by = $updated_by;
        $this->created_by = $created_by;
        $this->failedAccController = new FailedAccController();
    }


    private function getNextJvNo()
    {
        $dbObj = new Database();
        $jvObj = $dbObj->queryGet('SELECT MAX(`jv_no`)+1 AS newJvNo FROM `erp_acc_journal` WHERE `branch_id`=' . $this->branch_id);
        return $jvObj["data"]["newJvNo"] ?? 0;
    }
    ///////////////////////////////////////////Payment---------------------------
    private function checkPayment($value)
    {
        $dbObj = new Database(true);
        $obj = $dbObj->queryGet('SELECT `journal_id`,paymentCode FROM `erp_grn_payments` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `payment_id`=' . $value);
        if ($obj["status"] == "success" && $obj["data"]["journal_id"] != "") {
            return [
                "status" => "success",
                "message" => "Success",
                "journal_id" => $obj["data"]["journal_id"],
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Warning",
            ];
        }
    }

    function reversePayment($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Payment");
        $dbObj->setSuccessMsg("Payment Reversed successfully!");
        $dbObj->setErrorMsg("Payment Reversed failed!");

        $payment_id = $value;
        $checkObj = $this->checkPayment($payment_id);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $journal_id = $checkObj["journal_id"];
        $newJvNo = $this->getNextJvNo();

        $reverseRefCode = "REV" . $checkObj['paymentCode'];

        //Reverse the journal


        //Account reverse for Invoice insert with REVERSEINV000001 reference--------------------------------------------------------

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        if ($journalObj["status"] == 'success') {
            $journalData = $journalObj["data"];
            $reversePostingDate = $journalData["postingDate"];

            $accounting = array();
            $accounting['journal']['parent_id'] = $journalData["parent_id"];
            $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
            $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['party_code'] = $journalData["party_code"];
            $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
            $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
            $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
            $accounting['journal']['documentDate'] = $journalData["documentDate"];
            $accounting['journal']['postingDate'] = $reversePostingDate;


            //credit details
            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($debitObj["data"] as $debitRow) {
                $accounting['credit'][] = [
                    'glId' => $debitRow["glId"],
                    'subGlCode' => $debitRow["subGlCode"],
                    'subGlName' => $debitRow["subGlName"],
                    'credit_amount' => $debitRow["debit_amount"],
                    'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                ];
            }

            //debit details
            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($creditObj["data"] as $creditRow) {
                $accounting['debit'][] = [
                    'glId' => $creditRow["glId"],
                    'subGlCode' => $creditRow["subGlCode"],
                    'subGlName' => $creditRow["subGlName"],
                    'debit_amount' => $creditRow["credit_amount"],
                    'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                ];
            }

            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
            }
        }

        $paymentlogObj = $dbObj->queryGet("SELECT * FROM `" . ERP_GRN_PAYMENTS_LOG . "` WHERE `payment_id`=$payment_id AND status='active' ", true);

        foreach ($paymentlogObj['data'] as $paymentlog) {
            $invoiceId = $paymentlog['grn_id'];

            if ($invoiceId != 0 || !empty($invoiceId)) {
                $sattledAmt = $paymentlog['payment_amt'];
                //4 fullypaid,2 parciallypaid, 15- Due
                $paymentlogObj = $dbObj->queryGet("SELECT grnTotalAmount,dueAmt+$sattledAmt AS dueAmt,paymentStatus FROM `" . ERP_GRNINVOICE . "` WHERE `grnIvId`=$invoiceId ");
                if ($paymentlogObj['data']['grnTotalAmount'] == $paymentlogObj['data']['dueAmt']) {
                    $paymentStatus = '15';
                } else {
                    $paymentStatus = '2';
                }
                $upd = $dbObj->queryUpdate("UPDATE `" . ERP_GRNINVOICE . "` SET `paymentStatus`=$paymentStatus, `dueAmt`=dueAmt+$sattledAmt WHERE `grnIvId`=$invoiceId");
            }
        }
        //Payment Status change
        $dbObj->queryUpdate("UPDATE `" . ERP_GRN_PAYMENTS . "` SET `status`='reverse' WHERE `payment_id`=$payment_id");

        $dbObj->queryUpdate('UPDATE `' . ERP_GRN_PAYMENTS_LOG . '` SET `status`="inactive" WHERE `payment_id`=' . $payment_id);

        // return $returnDataAcc;
        return $dbObj->queryFinish();
    }

    ////////////////////////////////////Colection---------------------------------

    private function checkCollection($value)
    {
        $dbObj = new Database(true);
        $obj = $dbObj->queryGet('SELECT `journal_id` FROM `erp_branch_sales_order_payments` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `payment_id`=' . $value);

        if ($obj["status"] == "success" && $obj["data"]["journal_id"] != "") {
            return [
                "status" => "success",
                "message" => "Success",
                "journal_id" => $obj["data"]["journal_id"],
                "obj" => $obj,
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Warning",
                "obj" => $obj,
            ];
        }
    }


    function reverseCollection($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Collection");
        $dbObj->setSuccessMsg("Collection Reversed successfully!");
        $dbObj->setErrorMsg("Collection Reversed failed!");

        $payment_id = $value;
        $checkObj = $this->checkCollection($payment_id);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }
        $journal_id = $checkObj["journal_id"];
        $newJvNo = $this->getNextJvNo();

        $reverseRefCode = "REV" . $checkObj['collectionCode'];

        //Reverse the journal


        //Account reverse for Invoice insert with REVERSEINV000001 reference--------------------------------------------------------

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        if ($journalObj["status"] == 'success') {
            $journalData = $journalObj["data"];
            $reversePostingDate = $journalData["postingDate"];

            $accounting = array();
            $accounting['journal']['parent_id'] = $journalData["parent_id"];
            $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
            $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['party_code'] = $journalData["party_code"];
            $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
            $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
            $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
            $accounting['journal']['documentDate'] = $journalData["documentDate"];
            $accounting['journal']['postingDate'] = $reversePostingDate;


            //credit details
            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($debitObj["data"] as $debitRow) {
                $accounting['credit'][] = [
                    'glId' => $debitRow["glId"],
                    'subGlCode' => $debitRow["subGlCode"],
                    'subGlName' => $debitRow["subGlName"],
                    'credit_amount' => $debitRow["debit_amount"],
                    'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                ];
            }

            //debit details
            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($creditObj["data"] as $creditRow) {
                $accounting['debit'][] = [
                    'glId' => $creditRow["glId"],
                    'subGlCode' => $creditRow["subGlCode"],
                    'subGlName' => $creditRow["subGlName"],
                    'debit_amount' => $creditRow["credit_amount"],
                    'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                ];
            }

            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
            }
        }


        $paymentlogObj = $dbObj->queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` WHERE `payment_id`=$payment_id AND status='active' ", true);

        foreach ($paymentlogObj['data'] as $paymentlog) {
            $invoiceId = $paymentlog['invoice_id'];

            if ($invoiceId != 0 || !empty($invoiceId)) {
                $sattledAmt = $paymentlog['payment_amt'];
                //4 fullypaid,2 parciallypaid, 15- Due
                $paymentlogObj = $dbObj->queryGet("SELECT all_total_amt,due_amount+$sattledAmt AS due_amount,invoiceStatus FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE `so_invoice_id`=$invoiceId ");
                if ($paymentlogObj['data']['all_total_amt'] == $paymentlogObj['data']['due_amount']) {
                    $invoiceStatus = '15';
                } else {
                    $invoiceStatus = '2';
                }
                $upd = $dbObj->queryUpdate("UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` SET `invoiceStatus`=$invoiceStatus, `due_amount`=due_amount+$sattledAmt WHERE `so_invoice_id`=$invoiceId");
            }
        }
        //Payment Status change
        $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER_PAYMENTS . '` SET `status`="reverse" WHERE `payment_id`=' . $payment_id);

        $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . '` SET `status`="inactive" WHERE `payment_id`=' . $payment_id);



        return $dbObj->queryFinish();
    }

    function reverseCollectionFailedAccounting($value)
    {
        $dbObj = new Database(true);

        // $dbObj->setActionName("Reverse Collection");
        // $dbObj->setSuccessMsg("Collection Reversed successfully!");
        // $dbObj->setErrorMsg("Collection Reversed failed!");

        $payment_id = $value;

        $paymentSql = "SELECT * FROM `' . ERP_BRANCH_SALES_ORDER_PAYMENTS . '` WHERE payment_id='" . $payment_id . "' ";
        $paymentObj = $dbObj->queryGet($paymentSql);
        if ($paymentObj['status'] = "success") {

            $paymentlogObj = $dbObj->queryGet("SELECT * FROM `" . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . "` WHERE `payment_id`=$payment_id AND status='active' ", true);

            if ($paymentlogObj['status'] == "success") {
                foreach ($paymentlogObj['data'] as $paymentlog) {
                    $invoiceId = $paymentlog['invoice_id'];

                    if ($invoiceId != 0 || !empty($invoiceId)) {
                        $sattledAmt = $paymentlog['payment_amt'];
                        //4 fullypaid,2 parciallypaid, 15- Due
                        $paymentlogObj = $dbObj->queryGet("SELECT all_total_amt,due_amount+$sattledAmt AS due_amount,invoiceStatus FROM `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` WHERE `so_invoice_id`=$invoiceId ");
                        if ($paymentlogObj['data']['all_total_amt'] == $paymentlogObj['data']['due_amount']) {
                            $invoiceStatus = '15';
                        } else {
                            $invoiceStatus = '2';
                        }
                        $upd = $dbObj->queryUpdate("UPDATE `" . ERP_BRANCH_SALES_ORDER_INVOICES . "` SET `invoiceStatus`=$invoiceStatus, `due_amount`=due_amount-$sattledAmt WHERE `so_invoice_id`=$invoiceId");
                    }
                }
                $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER_PAYMENTS_LOG . '` SET `status`="inactive" WHERE `payment_id`=' . $payment_id);
            }

            //Payment Status change
            $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER_PAYMENTS . '` SET `status`="reverse" WHERE `payment_id`=' . $payment_id);

            return $dbObj->queryFinish();
        } else {
            return ["status" => "error", "message" => "payment id does not find"];
        }
    }

    // ///////////////////////////////////////////////////////GRN Start --------------------------------


    // add branch SO delivery 
    private function availableItemQty($item_id, $batch_number)
    {
        $dbObj = new Database();
        $today = date("Y-m-d");
        $selStockLog = "SELECT loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,SUM(log.itemQty) as itemQty,log.itemUom,log.logRef,grn.postingDate FROM erp_inventory_stocks_log AS log LEFT JOIN erp_storage_location AS loc ON log.storageLocationId=loc.storage_location_id LEFT JOIN erp_grn AS grn ON log.logRef=grn.grnCode WHERE grn.grnCode='" . $batch_number . "' AND log.companyId=" . $this->company_id . " AND log.branchId=" . $this->branch_id . " AND log.locationId=" . $this->location_id . " AND log.itemId=$item_id AND loc.storageLocationTypeSlug IN('rmWhOpen','rmWhReserve','fgWhOpen') GROUP BY loc.storage_location_id,loc.storage_location_code,loc.storage_location_name,loc.storage_location_type,loc.storageLocationTypeSlug,log.itemUom,log.logRef,grn.postingDate ORDER BY grn.postingDate ASC";

        $getStockLog = $dbObj->queryGet($selStockLog, true);
        // return $getStockLog;

        $totquantities = array_column($getStockLog['data'], "itemQty");
        $itemOpenStocks = array_sum($totquantities);
        if ($itemOpenStocks == '') {
            $itemOpenStocks = '0';
        }
        $returnStock = [];
        $returnStock['sumOfBatches'] = $itemOpenStocks;
        $returnStock['storage_location_id'] = $getStockLog['data']['storage_location_id'];
        $returnStock['storageLocationTypeSlug'] = $getStockLog['data']['storageLocationTypeSlug'];

        return $returnStock;
    }


    private function checkGRN($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `grnId`,`pending_grn_id`,`postingDate`,`vendorId`,`grnPoNumber`, `grnPostingJournalId`, `ivPostingJournalId`,`grnType`,`grnCode`,`grnStatus`,`iv_status`,`grnCreatedAt` FROM `erp_grn` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `grnId`=' . $value);


        if ($obj["data"]["status"] == "success" && $obj["data"]["grnStatus"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "GRN already Reversed.",
            ];
        }
        if ($obj["status"] == "success" && $obj["data"]["iv_status"] == 0) {
            $grnItemObj = $dbObj->queryGet('SELECT `grnGoodId`, `grnId`, `grnCode`, `goodName`, `goodDesc`, `goodId`, `goodCode`, `goodHsn`, `goodQty`, `receivedQty`, `unitPrice`, `cgst`, `sgst`, `igst`, `tds`, `goodstype`, `totalAmount`, `itemStocksQty`, `itemUOM`, `itemStorageLocation`, `grnType`, `grnGoodStatus` FROM `erp_grn_goods` WHERE `grnId`=' . $value, true);
            $stockShortageLog = [];
            if ($obj['data']["grnType"] == "grn") {
                // foreach ($grnItemObj["data"] as $key => $grnItem) {

                //     $checkStockObj = $this->availableItemQty($grnItem['goodId'], $grnItem['grnCode']);


                //     if ($checkStockObj["sumOfBatches"] < $grnItem["receivedQty"]) {
                //         $stockShortageLog[] = [
                //             "goodId" => $grnItem["goodId"],
                //             "goodCode" => $grnItem["goodCode"],
                //             "goodName" => $grnItem["goodName"],
                //             "receivedQty" => $grnItem["receivedQty"],
                //             "availableQty" => $checkStockObj["sumOfBatches"],
                //             "storageLocation" => $checkStockObj["storage_location_id"],
                //             "checkStockObj" => $checkStockObj,
                //         ];
                //     }
                // }
                $sqlll = 'SELECT * FROM erp_inventory_stocks_log WHERE companyId=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND refActivityName!="GRN" AND logRef="' . $obj['data']["grnCode"] . '"';
                $grnItemStockObj = $dbObj->queryGet($sqlll);
                if ($grnItemStockObj['status'] == 'success') {
                    $grnItemStockSumObj = $dbObj->queryGet('SELECT SUM(itemQty) as qty FROM erp_inventory_stocks_log WHERE companyId=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND refActivityName!="GRN" AND logRef="' . $obj['data']["grnCode"] . '"');
                    if ($grnItemStockSumObj['status'] == 'success' && $grnItemStockSumObj['data']['qty'] == 0) {
                        $grnItemStockObj = $dbObj->queryGet('SELECT * FROM erp_inventory_stocks_log WHERE companyId=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND refActivityName="GRN" AND refNumber="' . $obj['data']["grnCode"] . '"', true)['data'];
                        return [
                            "status" => "success",
                            "message" => "Ready for reverse posting1",
                            "grnType" => $obj["data"]["grnType"],
                            "pending_grn_id" => $obj["data"]["pending_grn_id"],
                            "grnId" => $obj["data"]["grnId"],
                            "postingDate" => $obj["data"]["postingDate"],
                            "grnPoNumber" => $obj["data"]["grnPoNumber"],
                            "vendorId" => $obj["data"]["vendorId"],
                            "grnCode" => $obj["data"]["grnCode"],
                            "grnCreatedAt" => $obj["data"]["grnCreatedAt"],
                            "journal_id" => $obj["data"]["grnPostingJournalId"],
                            "stock_items" => $grnItemObj["data"],
                            "stock_batch_items" => $grnItemStockObj,
                        ];
                    } else {
                        return [
                            "status" => "warning",
                            "message" => "Stock Shortage",
                            "journal_id" => $obj["data"]["grnPostingJournalId"],
                            "stock_items" => $stockShortageLog,
                            "stock_batch_items" => $stockShortageLog,
                        ];
                    }
                } else {
                    $grnItemStockObj = $dbObj->queryGet('SELECT * FROM erp_inventory_stocks_log WHERE companyId=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND refActivityName="GRN" AND refNumber="' . $obj['data']["grnCode"] . '"', true)['data'];
                    return [
                        "status" => "success",
                        "message" => "Ready for reverse posting2",
                        "grnType" => $obj["data"]["grnType"],
                        "pending_grn_id" => $obj["data"]["pending_grn_id"],
                        "grnId" => $obj["data"]["grnId"],
                        "postingDate" => $obj["data"]["postingDate"],
                        "grnPoNumber" => $obj["data"]["grnPoNumber"],
                        "vendorId" => $obj["data"]["vendorId"],
                        "grnCode" => $obj["data"]["grnCode"],
                        "journal_id" => $obj["data"]["grnPostingJournalId"],
                        "grnCreatedAt" => $obj["data"]["grnCreatedAt"],
                        "stock_items" => $grnItemObj["data"],
                        "stock_batch_items" => $grnItemStockObj,
                        "sqlll" => $sqlll,
                    ];
                }
            } else {
                return [
                    "status" => "success",
                    "message" => "Ready for reverse posting3",
                    "grnType" => $obj["data"]["grnType"],
                    "pending_grn_id" => $obj["data"]["pending_grn_id"],
                    "grnId" => $obj["data"]["grnId"],
                    "postingDate" => $obj["data"]["postingDate"],
                    "grnPoNumber" => $obj["data"]["grnPoNumber"],
                    "vendorId" => $obj["data"]["vendorId"],
                    "grnCode" => $obj["data"]["grnCode"],
                    "journal_id" => $obj["data"]["grnPostingJournalId"],
                    "stock_items" => $grnItemObj["data"],
                    "obj" => $obj,
                    "stock_batch_items" => $stockShortageLog,
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for GRN Reverse posting, please reverse IV first",
                "obj" => $obj,
            ];
        }
    }



    function reverseGRN($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse GRN");
        $dbObj->setSuccessMsg("GRN Reversed successfully!");
        $dbObj->setErrorMsg("GRN Reversed failed!");

        $grnId = $value;
        $checkObj = $this->checkGRN($grnId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }
        // return $checkObj;
        // exit;
        $grnType = $checkObj["grnType"];
        $dbObj->setActionName("Reverse " . $grnType);
        $dbObj->setSuccessMsg($grnType . " Reversed successfully!");
        $dbObj->setErrorMsg($grnType . " Reversed failed!");

        $grnId = $checkObj["grnId"];
        $pending_grn_id = $checkObj["pending_grn_id"];
        $grnCode = $checkObj["grnCode"];
        $journal_id = $checkObj["journal_id"];
        $stock_batch_items = $checkObj["stock_batch_items"];
        $postingDate = $checkObj["postingDate"];
        $grnCreatedAt = $checkObj["grnCreatedAt"];

        $grnPoNumber = $checkObj["grnPoNumber"];
        $vendorId = $checkObj["vendorId"];


        $reverseRefCode = "REV" . $grnCode;

        //Reverse the journal

        //GRN Status change
        $dbObj->queryUpdate('UPDATE `erp_grn` SET `grnStatus`="reverse" WHERE `grnId`=' . $grnId);
        if ($pending_grn_id != 0) {
            $dbObj->queryUpdate('UPDATE `erp_grn_multiple` SET `status`=0 WHERE `grn_mul_id`=' . $pending_grn_id);
        }
        if (!empty($grnPoNumber)) {
            $grnPoNumberArry = explode("|", $grnPoNumber);
        }

        if ($grnType == 'grn') {
            //Item stock minus entry & Moving weighing update
            foreach ($stock_batch_items as $oneItem) {
                $itemId = $oneItem["itemId"];
                $storageType = $oneItem["storageType"];
                $storageLocationId = $oneItem["storageLocationId"];
                $itemqty = $oneItem["itemQty"];
                $itemQty = $oneItem["itemQty"] * -1;
                $itemPrice = $oneItem["itemPrice"];
                $logRef = $oneItem["logRef"];
                $bornDate = $oneItem["bornDate"];
                $itemUom = $oneItem["itemUom"];
                $goodCode = $oneItem["goodCode"];
                $parentId = is_null($oneItem["parentId"]) ? 'NULL' : $oneItem["parentId"];



                // item level reversal

                // log checking moving average
                $logSql = "SELECT COALESCE(( SELECT map.movingAveragePrice AS mwp FROM erp_inventory_stocks_moving_average AS map WHERE map.companyId = $this->company_id AND map.itemId = $itemId AND map.createdAt < '" . $grnCreatedAt . "' ORDER BY map.createdAt DESC LIMIT 1 ), 0) AS mwp";
                $oldMwp = $dbObj->queryGet($logSql)['data']['mwp'] ?? 0;

                // rev log
                $dbObj->queryInsert('INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $this->company_id . ',`parentId`='.$parentId.',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`storageLocationId`=' . $storageLocationId . ',`storageType`="' . $storageType . '",`itemId`=' . $itemId . ',`itemQty`=' . $itemQty . ',`itemUom`=' . $itemUom . ',`itemPrice`=' . $itemPrice . ',`refActivityName`="REV-GRN",`logRef`="' . $logRef . '",`refNumber`="' . $reverseRefCode . '",`bornDate`="' . $bornDate . '",`postingDate`="' . $postingDate . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');


                // fetch the old item Qty
                // $prevItemSql='SELECT COALESCE(( SELECT sum.itemTotalQty FROM erp_inventory_stocks_summary AS sum WHERE sum.itemId = '.$itemId.' AND sum.company_id = '.$this->company_id.'), 0) AS prevQty;';

                $prevItemSql = 'SELECT COALESCE(summ.itemTotalQty, 0) AS prevQty FROM erp_inventory_stocks_summary AS summ WHERE summ.itemId = ' . $itemId . ' AND summ.company_id = ' . $this->company_id;

                $prevItemRes = $dbObj->queryGet($prevItemSql);
                console($prevItemRes);

                $prevItemQty = $prevItemRes['data']['prevQty'];

                $newTotalQty = $prevItemQty - $oneItem["itemQty"];


                // main formula of map
                echo "Item " . $itemId;
                echo " New Total Qty " . $newTotalQty;
                echo " Old Mwp   Qty " . $oldMwp;

                $movingWeightedPrice = $this->calculateNewMwpForRev($itemId, $newTotalQty, $itemPrice);
                echo " new Mwp   Qty " . $movingWeightedPrice;

                $resSql = queryGet('SELECT `itemId`, `rmWhOpen`, `rmWhReserve`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `itemId`=' . $itemId);
                console($resSql);
                // moving weighted price insertion
                $dbObj->queryInsert('INSERT INTO `erp_inventory_stocks_moving_average` SET `companyId`=' . $this->company_id . ',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`itemId`=' . $itemId . ',`itemCode`="' . $goodCode . '",`movingAveragePrice`=' . $movingWeightedPrice . ',`createdBy`="' . $this->created_by . '"');


                // update the main summery
                $upSumSql = 'UPDATE `erp_inventory_stocks_summary` SET `itemTotalQty`=' . $newTotalQty . ', `movingWeightedPrice`=' . $movingWeightedPrice . ' ,`updatedBy`="' . $this->updated_by . '" WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `itemId`=' . $itemId;
                $dbObj->queryUpdate($upSumSql);

                if (!empty($grnPoNumber)) {
                    foreach ($grnPoNumberArry as $grnPo => $po) {
                        $posql = queryGet("SELECT po_id,po_number,po_status FROM `erp_branch_purchase_order` WHERE `po_number` = '" . $po . "' AND `company_id`='" . $this->company_id . "' AND `branch_id`='" . $this->branch_id . "' AND `location_id`='" . $this->location_id . "' AND `vendor_id`=$vendorId");
                        // console($posql);
                        if ($posql['status'] == 'success') {
                            $poItemSql = queryGet("SELECT po_item_id,po_id,inventory_item_id,remainingQty FROM `erp_branch_purchase_order_items` WHERE `po_id` = '" . $posql['data']['po_id'] . "' AND `inventory_item_id` = $itemId");

                            $remain_qty = $poItemSql["data"]["remainingQty"];
                            $remaining_qty = $remain_qty + $itemqty;

                            // console($poItemSql);
                            if ($poItemSql['status'] == 'success') {
                                $poUpdate = $dbObj->queryUpdate("UPDATE `erp_branch_purchase_order` SET `po_status`=9 WHERE `po_id`='" . $posql['data']['po_id'] . "'");
                                // console($poUpdate);
                                $poItemUp = $dbObj->queryUpdate("UPDATE `erp_branch_purchase_order_items` SET remainingQty= $remaining_qty WHERE `po_item_id`=" . $poItemSql['data']['po_item_id']);
                                // console($poItemUp);
                            }
                        }
                    }
                }
            }
        }

        //Account reverse insert with REVERSEGRN000001 reference

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        if ($journalObj["status"] == 'success') {
            $journalData = $journalObj["data"];
            $reversePostingDate = $journalData["postingDate"];

            $accounting = array();
            $accounting['journal']['parent_id'] = $journalData["parent_id"];
            $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
            $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['party_code'] = $journalData["party_code"];
            $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
            $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
            $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
            $accounting['journal']['documentDate'] = $journalData["documentDate"];
            $accounting['journal']['postingDate'] = $reversePostingDate;


            //credit details
            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($debitObj["data"] as $debitRow) {
                $accounting['credit'][] = [
                    'glId' => $debitRow["glId"],
                    'subGlCode' => $debitRow["subGlCode"],
                    'subGlName' => $debitRow["subGlName"],
                    'credit_amount' => $debitRow["debit_amount"],
                    'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                ];
            }

            //debit details
            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($creditObj["data"] as $creditRow) {
                $accounting['debit'][] = [
                    'glId' => $creditRow["glId"],
                    'subGlCode' => $creditRow["subGlCode"],
                    'subGlName' => $creditRow["subGlName"],
                    'debit_amount' => $creditRow["credit_amount"],
                    'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                ];
            }

            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
            }
        }





        return $dbObj->queryFinish();
    }


    private function checkIV($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `grnIvId`,`grnId`,`postingDate`,`ivPostingJournalId`,`grnIvCode`,paymentStatus,`grnStatus` FROM `erp_grninvoice` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `grnIvId`=' . $value);

        if ($obj["status"] == "success" && $obj["data"]["grnStatus"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "GRNIV already Reversed.",
            ];
        }
        if ($obj["status"] == "success" && $obj["data"]["paymentStatus"] == 15) {

            return [
                "status" => "success",
                "message" => "Ready for reverse posting",
                "grnId" => $obj["data"]["grnId"],
                "grnIvId" => $obj["data"]["grnIvId"],
                "postingDate" => $obj["data"]["postingDate"],
                "grnIvCode" => $obj["data"]["grnIvCode"],
                "journal_id" => $obj["data"]["ivPostingJournalId"]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for GRNIV Reverse posting, please reverse Payment first",
                "obj" => $obj
            ];
        }
    }

    function reverseGRNIV($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse GRNIV");
        $dbObj->setSuccessMsg("GRNIV Reversed successfully!");
        $dbObj->setErrorMsg("GRNIV Reversed failed!");

        $grnIvId = $value;
        $checkObj = $this->checkIV($grnIvId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $grnId = $checkObj["grnId"];
        $grnIvId = $checkObj["grnIvId"];
        $grnIvCode = $checkObj["grnIvCode"];
        $journal_id = $checkObj["journal_id"];

        $reverseRefCode = "REV" . $grnIvCode;

        //Reverse the journal


        //Account reverse insert with REVERSEGRNIV000001 reference

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        if ($journalObj["status"] == 'success') {
            $journalData = $journalObj["data"];
            $reversePostingDate = $journalData["postingDate"];

            $accounting = array();
            $accounting['journal']['parent_id'] = $journalData["parent_id"];
            $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
            $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['party_code'] = $journalData["party_code"];
            $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
            $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
            $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
            $accounting['journal']['documentDate'] = $journalData["documentDate"];
            $accounting['journal']['postingDate'] = $reversePostingDate;


            //credit details
            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($debitObj["data"] as $debitRow) {
                $accounting['credit'][] = [
                    'glId' => $debitRow["glId"],
                    'subGlCode' => $debitRow["subGlCode"],
                    'subGlName' => $debitRow["subGlName"],
                    'credit_amount' => $debitRow["debit_amount"],
                    'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                ];
            }

            //debit details
            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($creditObj["data"] as $creditRow) {
                $accounting['debit'][] = [
                    'glId' => $creditRow["glId"],
                    'subGlCode' => $creditRow["subGlCode"],
                    'subGlName' => $creditRow["subGlName"],
                    'debit_amount' => $creditRow["credit_amount"],
                    'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                ];
            }

            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
            }
        }
        //GRN Status change
        $dbObj->queryUpdate('UPDATE `erp_grn` SET `iv_status`=0 WHERE `grnId`=' . $grnId);
        //GRNIV Status change
        $dbObj->queryUpdate('UPDATE `erp_grninvoice` SET `grnStatus`="reverse", `ivPostingJournalId`=' . $newJournalId . ' WHERE `grnIvId`=' . $grnIvId);


        return $dbObj->queryFinish();
    }

    // ////////////////////////////////////////////////////////////SO Start --------------------------------


    private function checkDelivery($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `so_delivery_id`,`delivery_date`, `delivery_no`,`deliveryStatus`,`status` FROM `erp_branch_sales_order_delivery` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `so_delivery_id`=' . $value);

        if ($obj["status"] == "success" && $obj["data"]["status"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "Delivery already Reversed.",
            ];
        }
        if ($obj["status"] == "success" && $obj["data"]["deliveryStatus"] == 'open') {
            return [
                "status" => "success",
                "message" => "Ready for reverse posting",
                "so_delivery_id" => $obj["data"]["so_delivery_id"],
                "delivery_date" => $obj["data"]["delivery_date"],
                "delivery_no" => $obj["data"]["delivery_no"]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for Delivery Reverse posting, please reverse PGI first",
                "obj" => $obj,
            ];
        }
    }

    function reverseDelivery($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Delivery");
        $dbObj->setSuccessMsg("Delivery Reversed successfully!");
        $dbObj->setErrorMsg("Delivery Reversed failed!");

        $delId = $value;
        $checkObj = $this->checkDelivery($delId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $delId = $checkObj["so_delivery_id"];
        $delCode = $checkObj["delivery_no"];

        $reverseRefCode = "REV" . $delCode;

        $stocks_log = $dbObj->queryGet("SELECT `storageLocationId`, `storageType`, `itemId`, -1 * `itemQty` AS `itemQty`, `itemUom`, `itemPrice`, `refActivityName`, `logRef`, `refNumber`, `bornDate`, `postingDate` FROM `erp_inventory_stocks_log` WHERE `refActivityName`='DELIVERY' AND `refNumber`='$delCode' AND status='active'", true);

        //Item stock minus entry & Moving weighing update
        foreach ($stocks_log['data'] as $oneItem) {
            $goodId = $oneItem["itemId"];
            $bornDate = $oneItem["bornDate"];
            $postingDate = $oneItem["postingDate"];
            $storageLocation = $oneItem["storageLocationId"];
            $storageType = $oneItem["storageType"];
            $receivedQty = $oneItem["itemQty"];
            $itemUom = $oneItem["itemUom"];
            $unitPrice = $oneItem["itemPrice"];
            $logRef = $oneItem["logRef"];

            $dbObj->queryInsert('INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $this->company_id . ',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`storageLocationId`=' . $storageLocation . ',`storageType`="' . $storageType . '",`itemId`=' . $goodId . ',`itemQty`=' . $receivedQty . ',`itemUom`=' . $itemUom . ',`itemPrice`=' . $unitPrice . ',`refActivityName`="REV-DELIVERY",`logRef`="' . $logRef . '",`refNumber`="' . $reverseRefCode . '",`bornDate`="' . $bornDate . '",`postingDate`="' . $postingDate . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');
        }

        //Invoice Status change
        $dbObj->queryUpdate("UPDATE `erp_branch_sales_order_delivery` SET `status`='reverse' WHERE `so_delivery_id`=$delId");


        $delItems = $dbObj->queryGet("SELECT * FROM erp_branch_sales_order_delivery_items WHERE so_delivery_id = $delId AND status='active'", true);

        //----------------------------------------------------------------
        // foreach($delItems as $Item){
        //     //Stock Log reversed  Processing Item wise


        // }

        //----------------------------------------------------------------


        return $dbObj->queryFinish();
    }



    private function checkPGI($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `so_delivery_pgi_id`,`pgiDate`, `journal_id`, `pgi_no`,`invoiceStatus`,`status` FROM `erp_branch_sales_order_delivery_pgi` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `so_delivery_pgi_id`=' . $value);

        if ($obj["status"] == "success" && $obj["data"]["status"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "PGI already Reversed.",
            ];
        }
        if ($obj["status"] == "success" && $obj["data"]["invoiceStatus"] == 1) {
            return [
                "status" => "success",
                "message" => "Ready for reverse posting",
                "so_delivery_pgi_id" => $obj["data"]["so_delivery_pgi_id"],
                "pgiDate" => $obj["data"]["pgiDate"],
                "pgi_no" => $obj["data"]["pgi_no"],
                "journal_id" => $obj["data"]["journal_id"]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for Reverse posting, please reverse Invoice first",
            ];
        }
    }

    function reversePGI($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse PGI");
        $dbObj->setSuccessMsg("PGI Reversed successfully!");
        $dbObj->setErrorMsg("PGI Reversed failed!");

        $pgiId = $value;
        $checkObj = $this->checkPGI($pgiId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $pgiId = $checkObj["so_delivery_pgi_id"];
        $pgiCode = $checkObj["pgi_no"];
        $journal_id = $checkObj["journal_id"];

        $reverseRefCode = "REV" . $pgiCode;

        $stocks_log = $dbObj->queryGet("SELECT `storageLocationId`, `storageType`, `itemId`, -1 * `itemQty` AS `itemQty`, `itemUom`, `itemPrice`, `refActivityName`, `logRef`, `refNumber`, `bornDate`, `postingDate` FROM `erp_inventory_stocks_log` WHERE `refActivityName`='PGI' AND `refNumber`='$pgiCode' AND status='active'", true);

        //Item stock minus entry & Moving weighing update
        foreach ($stocks_log['data'] as $oneItem) {
            $goodId = $oneItem["itemId"];
            $bornDate = $oneItem["bornDate"];
            $postingDate = $oneItem["postingDate"];
            $storageLocation = $oneItem["storageLocationId"];
            $storageType = $oneItem["storageType"];
            $receivedQty = $oneItem["itemQty"];
            $itemUom = $oneItem["itemUom"];
            $unitPrice = $oneItem["itemPrice"];
            $logRef = $oneItem["logRef"];

            $dbObj->queryInsert('INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $this->company_id . ',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`storageLocationId`=' . $storageLocation . ',`storageType`="' . $storageType . '",`itemId`=' . $goodId . ',`itemQty`=' . $receivedQty . ',`itemUom`=' . $itemUom . ',`itemPrice`=' . $unitPrice . ',`refActivityName`="REV-PGI",`logRef`="' . $logRef . '",`refNumber`="' . $reverseRefCode . '",`bornDate`="' . $bornDate . '",`postingDate`="' . $postingDate . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');
        }

        //Invoice Status change
        $dbObj->queryUpdate('UPDATE `erp_branch_sales_order_delivery_pgi` SET `status`="reverse" WHERE `so_delivery_pgi_id`=' . $pgiId);


        $pgiItems = $dbObj->queryGet("SELECT * FROM erp_branch_sales_order_delivery_items_pgi WHERE so_delivery_pgi_id = $pgiId AND status='active'", true);

        //----------------------------------------------------------------
        // foreach($pgiItems as $Item){
        //     //Stock Log reversed  Processing Item wise


        // }

        //----------------------------------------------------------------


        return $dbObj->queryFinish();
    }

    private function checkInvoice($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `so_invoice_id`,`invoice_date`,`pgi_id`, `pgi_journal_id`, `journal_id`,`invoice_no`,`so_number`,`status` FROM `' . ERP_BRANCH_SALES_ORDER_INVOICES . '` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `so_invoice_id`=' . $value);

        if ($obj["status"] == "success" && $obj["data"]["status"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "Invoice already Reversed.",
            ];
        }
        if ($obj["status"] == "success" && $obj["data"]["iv_status"] == 0) {

            return [
                "status" => "success",
                "message" => "Ready for reverse posting",
                "so_invoice_id" => $obj["data"]["so_invoice_id"],
                "invoice_date" => $obj["data"]["invoice_date"],
                "pgi_id" => $obj["data"]["pgi_id"],
                "so_number" => $obj["data"]["so_number"],
                "invoice_no" => $obj["data"]["invoice_no"],
                "pgi_journal_id" => $obj["data"]["pgi_journal_id"],
                "journal_id" => $obj["data"]["journal_id"]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for Invoice Reverse posting, please reverse Collection first",
            ];
        }
    }



    function checkCollectionAgainstInvoice($invoiceId, $invoiceType)
    {
        $dbObj = new Database(true);
        $sts = '';
        if ($invoiceType == 'dn') {
            $sts = "lg.doc_type='dn' ";
        } else if ($invoiceType == 'inv' || $invoiceType == 'invoice') {
            $sts = "lg.doc_type='inv' ";
        }

        $sql = "SELECT * FROM `erp_branch_sales_order_payments_log` as lg WHERE lg.company_id = $this->company_id AND lg.invoice_id = '$invoiceId' AND lg.status='active'";

        if (!empty($sts)) {
            $sql .= " AND $sts";
        }
        $obj = $dbObj->queryGet($sql, true);
        if ($obj['status'] == "success") {
            if ($obj['numRows'] > 0) {
                return ['status' => 'warning', 'message' => 'Collection found against this invoice', 'sql' => $sql];
            }
        }
    }


    function reverseInvoice($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Invoice");
        $dbObj->setSuccessMsg("Invoice Reversed successfully!");
        $dbObj->setErrorMsg("Invoice Reversed failed!");

        $invoiceId = $value;
        $checkObj = $this->checkInvoice($invoiceId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
            exit();
        }

        $checkObject = $this->checkCollectionAgainstInvoice($invoiceId, 'inv');
        if ($checkObject['status'] == "warning") {
            return $checkObject;
            exit();
        }

        $invoiceId = $checkObj["so_invoice_id"];
        $pgi_id = $checkObj["pgi_id"] ?? '';
        $so_number = $checkObj["so_number"] ?? '';
        $invoiceCode = $checkObj["invoice_no"];
        $journal_id = $checkObj["journal_id"];
        $pgi_journal_id = $checkObj["pgi_journal_id"];


        $invoiceItems = $dbObj->queryGet("SELECT * FROM erp_branch_sales_order_invoice_items WHERE so_invoice_id = $invoiceId AND status='active'", true);

        $reverseRefCode = "REV" . $invoiceCode;
        $stocks_log = $dbObj->queryGet("SELECT `storageLocationId`, `storageType`, `itemId`, -1 * `itemQty` AS `itemQty`, `itemUom`, `itemPrice`, `refActivityName`, `logRef`, `refNumber`, `bornDate`, `postingDate` FROM `erp_inventory_stocks_log` WHERE `refActivityName`='INVOICE' AND `refNumber`='$invoiceCode' AND status='active'", true);

        //Item stock minus entry & Moving weighing update
        foreach ($stocks_log['data'] as $oneItem) {
            $goodId = $oneItem["itemId"];
            $bornDate = $oneItem["bornDate"];
            $postingDate = $oneItem["postingDate"];
            $storageLocation = $oneItem["storageLocationId"];
            $storageType = $oneItem["storageType"];
            $receivedQty = $oneItem["itemQty"];
            $itemUom = $oneItem["itemUom"];
            $unitPrice = $oneItem["itemPrice"];
            $logRef = $oneItem["logRef"];

            $dbObj->queryInsert('INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $this->company_id . ',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`storageLocationId`=' . $storageLocation . ',`storageType`="' . $storageType . '",`itemId`=' . $goodId . ',`itemQty`=' . $receivedQty . ',`itemUom`=' . $itemUom . ',`itemPrice`=' . $unitPrice . ',`refActivityName`="REV-INVOICE",`logRef`="' . $logRef . '",`refNumber`="' . $reverseRefCode . '",`bornDate`="' . $bornDate . '",`postingDate`="' . $postingDate . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');
        }

        //Reverse the journal

        //Account reverse for PGI insert with REVERSEINV000001 reference---------------------------------------------------------
        $newInvoiceJournalId = 0;
        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);        
        if ($journalObj["status"] == 'success') {
            $journalData = $journalObj["data"];
            $reversePostingDate = $journalData["postingDate"];

            $accounting = array();
            $accounting['journal']['parent_id'] = $journalData["parent_id"];
            $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
            $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['party_code'] = $journalData["party_code"];
            $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
            $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
            $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
            $accounting['journal']['documentDate'] = $journalData["documentDate"];
            $accounting['journal']['postingDate'] = $reversePostingDate;


            //credit details
            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($debitObj["data"] as $debitRow) {
                $accounting['credit'][] = [
                    'glId' => $debitRow["glId"],
                    'subGlCode' => $debitRow["subGlCode"],
                    'subGlName' => $debitRow["subGlName"],
                    'credit_amount' => $debitRow["debit_amount"],
                    'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                ];
            }

            //debit details
            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($creditObj["data"] as $creditRow) {
                $accounting['debit'][] = [
                    'glId' => $creditRow["glId"],
                    'subGlCode' => $creditRow["subGlCode"],
                    'subGlName' => $creditRow["subGlName"],
                    'debit_amount' => $creditRow["credit_amount"],
                    'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                ];
            }

            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
            }
        }

        $newpgiJournalId = 0;
        if (!empty($pgi_journal_id)) {
            //Account reverse for Invoice insert with REVERSEINV000001 reference--------------------------------------------------------

            $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $pgi_journal_id . ' AND `branch_id`=' . $this->branch_id);
            // console($journalObj);
            if ($journalObj["status"] == 'success') {
                $journalData = $journalObj["data"];
                $reversePostingDate = $journalData["postingDate"];

                $accounting = array();
                $accounting['journal']['parent_id'] = $journalData["parent_id"];
                $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
                $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['party_code'] = $journalData["party_code"];
                $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
                $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
                $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['documentDate'] = $journalData["documentDate"];
                $accounting['journal']['postingDate'] = $reversePostingDate;


                //credit details
                $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $pgi_journal_id, true);
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'credit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                    ];
                }

                //debit details
                $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $pgi_journal_id, true);
                foreach ($creditObj["data"] as $creditRow) {
                    $accounting['debit'][] = [
                        'glId' => $creditRow["glId"],
                        'subGlCode' => $creditRow["subGlCode"],
                        'subGlName' => $creditRow["subGlName"],
                        'debit_amount' => $creditRow["credit_amount"],
                        'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                    ];
                }

                $accPostingObj = new AccountingPosting();
                $returnDataAcc = $accPostingObj->post($accounting);
                if ($returnDataAcc['status'] == 'success') {
                    $newJournalId = $returnDataAcc['journalId'];

                    //Journal Status change
                    $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
                }
            }
        }

        //Invoice Status change
        $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER_INVOICES . '` SET `status`="reverse", `pgi_journal_id`=' . $newpgiJournalId . ', `journal_id`=' . $newInvoiceJournalId . ' WHERE `so_invoice_id`=' . $invoiceId);

        //----------------------------------------------------------------
        // foreach($invoiceItems as $Item){
        //     //Stock Log reversed  Processing Item wise


        // }

        //----------------------------------------------------------------

        //SO Status change
        if (!empty($so_number) || $so_number != 0) {
            $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER . '` SET `approvalStatus`= 9 WHERE company_id=' . $this->company_id . ' AND branch_id=' . $this->branch_id . '  AND location_id=' . $this->location_id . ' AND so_number="' . $so_number);
        }

        //PGI Status change
        if (!empty($pgi_id) || $pgi_id != 0) {
            $dbObj->queryUpdate('UPDATE `erp_branch_sales_order_delivery_pgi` SET `invoiceStatus`=9, `pgiStatus`="open", `journal_id`=' . $newpgiJournalId . ' `updated_by`=' . $this->updated_by . ' WHERE `so_delivery_pgi_id`=' . $pgi_id);
        }


        return $dbObj->queryFinish();
    }

    // ////////////////////////////////////////////////////////Depreciation-----------------------------------------

    private function checkdepreciation($value)
    {
        $dbObj = new Database();

        $objLast = $dbObj->queryGet('SELECT * FROM `erp_asset_depreciation` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND status!="reverse" ORDER BY asset_depreciation_id DESC LIMIT 1');

        $obj = $dbObj->queryGet('SELECT * FROM `erp_asset_depreciation` WHERE asset_depreciation_id=' . $value);

        if ($obj["status"] == "success" && $obj["data"]["status"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "Action already Reversed.",
            ];
        }
        if ($objLast["status"] == "success") {
            if ($objLast["data"]['asset_depreciation_id'] == $value) {
                return [
                    "status" => "success",
                    "message" => "Ready for reverse posting",
                    "asset_use_id" => $obj["data"]["asset_use_id"],
                    "asset_depreciation_id" => $obj["data"]["asset_depreciation_id"],
                    "depreciation_value" => $obj["data"]["depreciation_value"],
                    "depreciation_code" => $obj["data"]["depreciation_code"],
                    "journal_id" => $obj["data"]["journal_id"]
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "Not eligible for Depreciation Reverse posting, please Latest One!",
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for Depreciation Reverse posting, please Latest One!",
            ];
        }
    }

    function reverseDepreciation($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Depreciation");
        $dbObj->setSuccessMsg("Depreciation Reversed successfully!");
        $dbObj->setErrorMsg("Depreciation Reversed failed!");

        $depreciationId = $value;
        $checkObj = $this->checkdepreciation($depreciationId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $asset_use_id = $checkObj["asset_use_id"];
        $depreciation_value = $checkObj["depreciation_value"];
        $depreciationId = $checkObj["asset_depreciation_id"];
        $depreciation_code = $checkObj["depreciation_code"];
        $journal_id = $checkObj["journal_id"];

        $reverseRefCode = "REV" . $depreciation_code;

        //Reverse the journal


        //Account reverse for Invoice insert with REVERSEINV000001 reference--------------------------------------------------------

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        if ($journalObj["status"] == 'success') {
            $journalData = $journalObj["data"];
            $reversePostingDate = $journalData["postingDate"];

            $accounting = array();
            $accounting['journal']['parent_id'] = $journalData["parent_id"];
            $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
            $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['party_code'] = $journalData["party_code"];
            $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
            $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
            $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
            $accounting['journal']['documentDate'] = $journalData["documentDate"];
            $accounting['journal']['postingDate'] = $reversePostingDate;


            //credit details
            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($debitObj["data"] as $debitRow) {
                $accounting['credit'][] = [
                    'glId' => $debitRow["glId"],
                    'subGlCode' => $debitRow["subGlCode"],
                    'subGlName' => $debitRow["subGlName"],
                    'credit_amount' => $debitRow["debit_amount"],
                    'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                ];
            }

            //debit details
            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($creditObj["data"] as $creditRow) {
                $accounting['debit'][] = [
                    'glId' => $creditRow["glId"],
                    'subGlCode' => $creditRow["subGlCode"],
                    'subGlName' => $creditRow["subGlName"],
                    'debit_amount' => $creditRow["credit_amount"],
                    'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                ];
            }

            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
            }
        }

        //Depreciation Status change
        $dbObj->queryUpdate('UPDATE `erp_asset_depreciation` SET `status`="reverse", `journal_id`=' . $newJournalId . ' WHERE `asset_depreciation_id`=' . $depreciationId);

        //asset_use Price change
        $dbObj->queryUpdate("UPDATE erp_asset_use SET depreciation_amount = depreciation_amount - $depreciation_value, depreciated_asset_value = depreciated_asset_value + $depreciation_value WHERE use_asset_id = $asset_use_id");



        return $dbObj->queryFinish();
    }

    private function checkProdDeclaration($item_id, $production_code)
    {
        $dbObj = new Database(true);
        $sqlll = 'SELECT * FROM erp_inventory_stocks_log WHERE companyId=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND itemId="'.$item_id.'" AND refActivityName="PROD-IN" AND logRef="' . $production_code . '"';
        $ItemStockObj = $dbObj->queryGet($sqlll);
        
        if ($ItemStockObj['status'] == 'success') {
            $ItemStockSumObj = $dbObj->queryGet('SELECT SUM(itemQty) as qty FROM erp_inventory_stocks_log WHERE companyId=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND itemId="'.$item_id.'" AND refActivityName!="PROD-IN" AND logRef="' . $production_code . '"');
            if ($ItemStockSumObj['status'] == 'success' && $ItemStockSumObj['data']['qty'] == 0) {
                return 1;
            }else{
                return 0;
            }
        }
    }

    function reverseProdDeclaration($declarationId = null)
    {
        $created_by = $this->created_by;
        $updated_by = $this->updated_by;
        $company_id = $this->company_id;
        $branch_id = $this->branch_id;
        $location_id = $this->location_id;
        $revAcc = [];
        $revfgsfgAcc = [];

        $dbObj = new Database(true);
        $dbObj->setActionName("Reverse Production Declaration");
        $dbObj->setSuccessMsg("Production Declaration Reversed successfully!");
        $dbObj->setErrorMsg("Production Declaration Reversed failed!");
        $declarationObj = $dbObj->queryGet("SELECT * FROM `erp_production_declarations` WHERE `location_id`=$location_id AND `status`!='reverse' AND `id`=$declarationId");
        if ($declarationObj["status"] == "success") {
            $declarationCode = $declarationObj["data"]["code"];
            $prod_id = $declarationObj["data"]["prod_id"];
            $prod_code = $declarationObj["data"]["prod_code"];
            $sub_prod_id = $declarationObj["data"]["sub_prod_id"];
            $sub_prod_code = $declarationObj["data"]["sub_prod_code"];
            $declarationQuantity = $declarationObj["data"]["quantity"];
            $prod_declaration_journal_id = $declarationObj["data"]["prod_declaration_journal_id"];
            $fgsfg_declaration_journal_id = $declarationObj["data"]["fgsfg_declaration_journal_id"];
            $createdby = $declarationObj["data"]['created_by'];

            // start check item Movement 
            $getItemId=$dbObj->queryGet("SELECT `itemId` FROM `erp_production_order` where `so_por_id`=$prod_id AND `porCode`='$prod_code' AND `location_id`=$location_id AND `company_id`=$company_id AND `branch_id`=$branch_id");

            if($getItemId['status']=="success" && $getItemId['numRows']>0)
            {
                $production_itemId=$getItemId['data']['itemId'];
                $result_status=$this->checkProdDeclaration($production_itemId,$declarationCode);
                if($result_status==0)
                {
                    return [
                        "status" => "warning",
                        "message" => "Stock shortage: Unable to reverse production!"
                    ];
                }
            }else{
                return [
                    "status" => "warning",
                    "message" => "Something Went wrong!"
                ];
            }
            // end  
            
            // Check the reverse is qty is available or not
            $prevStockLogObj = $dbObj->queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE `locationId`='$location_id' AND `refNumber` = '$declarationCode'", true);

            $check = 0;
            $checkMsg = '';
            $messages = [];
            foreach ($prevStockLogObj["data"] as $row) {
                $itemId = $row["itemId"];
                $resItem = checkItemImpactById($itemId);
                if ($resItem['status'] != "success") {
                    $check = 1;
                    $messages[] = "Item ID {$itemId}: " . $resItem['message'];
                }
            }

            if ($check != 0) {
                $checkMsg = implode("\n", $messages);
                return ["status" => "error", "message" => $checkMsg];
                exit();
            }

            // $updateObj = $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `status`='reverse' WHERE `location_id` = '$location_id' AND `id`='$declarationId'");
            foreach ($prevStockLogObj["data"] as $row) {
                $storageLocationId = $row["storageLocationId"];
                $storageType = $row["storageType"];
                $itemId = $row["itemId"];
                $itemQty = $row["itemQty"];
                $remainingQty = $row["remainingQty"];
                $itemUom = $row["itemUom"];
                $itemPrice = $row["itemPrice"];
                $refActivityName = $row["refActivityName"];
                $logRef = $row["logRef"];
                $refNumber = $row["refNumber"];
                $min_stock = $row["min_stock"];
                $max_stock = $row["max_stock"];
                $bornDate = $row["bornDate"];
                $postingDate = $row["postingDate"];
                $parentId = is_null($row["parentId"]) ? 'NULL' : '' . $row["parentId"] . '';

                if ($row["refActivityName"] == "PROD-IN") {
                    $refActivityName = "REV-" . $refActivityName;
                    $refNumber = "REV-" . $refNumber;
                    $itemQty = $itemQty * (-1);
                } else {
                    $refActivityName = "REV-" . $refActivityName;
                    $refNumber = "REV-" . $refNumber;
                    $itemQty = $itemQty * (-1);
                }
                /* Reverse MAP calculation */

                $getItemValuationClass = fetchValuationByItemId($itemId);


                if ($getItemValuationClass == "v") {

                    $itemReverseMapValue = calculateNewMwp($itemId, abs($itemQty), $itemPrice, "prodinrev");

                    $dbObj->queryInsert("INSERT INTO `erp_inventory_stocks_log` SET `companyId`='$company_id',`branchId`='$branch_id',`locationId`='$location_id',`parentId`='$parentId',`storageLocationId`='$storageLocationId',`storageType`='$storageType',`itemId`='$itemId',`itemQty`='$itemQty',`remainingQty`='$remainingQty',`itemUom`='$itemUom',`itemPrice`='$itemPrice',`refActivityName`='$refActivityName',`logRef`='$logRef',`refNumber`='$refNumber',`min_stock`='$min_stock',`max_stock`='$max_stock',`bornDate`='$bornDate',`postingDate`='$postingDate',`createdBy`='$created_by',`updatedBy`='$updated_by'");
                } else if ($getItemValuationClass == "s") {

                    $dbObj->queryInsert("INSERT INTO `erp_inventory_stocks_log` SET `companyId`='$company_id',`branchId`='$branch_id',`locationId`='$location_id',`parentId`='$parentId',`storageLocationId`='$storageLocationId',`storageType`='$storageType',`itemId`='$itemId',`itemQty`='$itemQty',`remainingQty`='$remainingQty',`itemUom`='$itemUom',`itemPrice`='$itemPrice',`refActivityName`='$refActivityName',`logRef`='$logRef',`refNumber`='$refNumber',`min_stock`='$min_stock',`max_stock`='$max_stock',`bornDate`='$bornDate',`postingDate`='$postingDate',`createdBy`='$created_by',`updatedBy`='$updated_by'");

                    if ($itemQty > 0) {
                        summeryDirectStockUpdateByItemId($itemId, abs($itemQty), "+");
                    } else {
                        summeryDirectStockUpdateByItemId($itemId, abs($itemQty), "-");
                    }
                }
            }
            //Minus the PROD-IN Item from stock log
            //Plus the PROD-OUT item in stock log
            //Update journal for this declaration
            //Credit all the debited row from this journal
            //Debit all the credited row from this journal
            $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $prod_declaration_journal_id . ' AND `branch_id`=' . $this->branch_id);
            if ($journalObj["status"] == 'success') {
                $journalData = $journalObj["data"];
                $reversePostingDate = $journalData["postingDate"];

                $accounting = array();
                $accounting['journal']['parent_id'] = $journalData["parent_id"];
                $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
                $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['remark'] = $declarationCode;
                $accounting['journal']['party_code'] = $journalData["party_code"];
                $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
                $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
                $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['documentDate'] = $journalData["documentDate"];
                $accounting['journal']['postingDate'] = $reversePostingDate;


                //credit details
                $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $prod_declaration_journal_id, true);
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'credit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                    ];
                }

                //debit details
                $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $prod_declaration_journal_id, true);
                foreach ($creditObj["data"] as $creditRow) {
                    $accounting['debit'][] = [
                        'glId' => $creditRow["glId"],
                        'subGlCode' => $creditRow["subGlCode"],
                        'subGlName' => $creditRow["subGlName"],
                        'debit_amount' => $creditRow["credit_amount"],
                        'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                    ];
                }

                $accPostingObj = new AccountingPosting();
                $returnDataAcc = $accPostingObj->post($accounting);
                $revAcc = $returnDataAcc;
                if ($returnDataAcc['status'] == 'success') {
                    $newJournalId = $returnDataAcc['journalId'];

                    //Journal Status change
                    $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $prod_declaration_journal_id);
                    $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `prod_declaration_journal_id` = 0, `reverse_prod_declaration_journal_id` = " . $newJournalId . " WHERE `location_id` = '$location_id' AND `id`='$declarationId'");
                }
            }
            $journalObjfgsfg = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $fgsfg_declaration_journal_id . ' AND `branch_id`=' . $this->branch_id);

            if ($journalObjfgsfg["status"] == 'success') {
                $journalData = $journalObjfgsfg["data"];
                $reversePostingDate = $journalData["postingDate"];

                $accounting = array();
                $accounting['journal']['parent_id'] = $journalData["parent_id"];
                $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
                $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['remark'] = $declarationCode;
                $accounting['journal']['party_code'] = $journalData["party_code"];
                $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
                $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
                $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['documentDate'] = $journalData["documentDate"];
                $accounting['journal']['postingDate'] = $reversePostingDate;


                //credit details
                $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $fgsfg_declaration_journal_id, true);
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'credit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                    ];
                }

                //debit details
                $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $fgsfg_declaration_journal_id, true);
                foreach ($creditObj["data"] as $creditRow) {
                    $accounting['debit'][] = [
                        'glId' => $creditRow["glId"],
                        'subGlCode' => $creditRow["subGlCode"],
                        'subGlName' => $creditRow["subGlName"],
                        'debit_amount' => $creditRow["credit_amount"],
                        'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                    ];
                }

                $accPostingObj = new AccountingPosting();
                $returnDataAcc = $accPostingObj->post($accounting);
                $revfgsfgAcc = $returnDataAcc;
                if ($returnDataAcc['status'] == 'success') {
                    $newJournalId = $returnDataAcc['journalId'];

                    //Journal Status change
                    $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $fgsfg_declaration_journal_id);
                    $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `fgsfg_declaration_journal_id` = 0, `reverse_fgsfg_declaration_journal_id` = " . $newJournalId . " WHERE `location_id` = '$location_id' AND `id`='$declarationId'");
                }
            }
            if ($revAcc['status'] == 'success' && $revfgsfgAcc['status'] == 'success') {
                $updateObj = $dbObj->queryUpdate("UPDATE `erp_production_declarations` SET `status`='reverse' WHERE `location_id` = '$location_id' AND `id`='$declarationId'");
            } else {
                $logAccFailedResponce = $this->failedAccController->logAccountingFailure($declarationId, 'production');
            }

            $dbObj->queryUpdate("UPDATE `erp_production_order_sub`
            SET
              `remainQty`=`remainQty`+$declarationQuantity,
              `updated_by`='$this->updated_by',
              `status` = CASE WHEN `remainQty`=0 THEN 10 ELSE `status` END
            WHERE 1
                AND `sub_prod_id`=$sub_prod_id
                AND `company_id`=$company_id
                AND `branch_id`=$branch_id
                AND `location_id`=$location_id
            ");

            $dbObj->queryUpdate("UPDATE `erp_production_order`
                SET
                `remainQty`=`remainQty`+$declarationQuantity,
                `updated_by`='$updated_by',
                `status` = CASE WHEN `remainQty`=0 THEN 10 ELSE `status` END
                WHERE 1
                    AND `so_por_id`=$prod_id
                    AND `company_id`=$company_id
                    AND `branch_id`=$branch_id
                    AND `location_id`=$location_id
            ");
            $datesql = $dbObj->queryGet("SELECT * FROM `erp_inventory_stocks_log` WHERE logRef='$declarationCode' and `refActivityName`='PROD-IN'")['data'];
            $productionDeclareDate = $datesql['postingDate'];
            $declearItemId = $datesql['itemId'];
            $itemsql = $dbObj->queryGet("SELECT * FROM `erp_inventory_items` WHERE itemId=$declearItemId")['data'];
            $ItemCode = $itemsql['itemCode'];
            $itemName = $itemsql['itemName'];
            $currentTime = date("Y-m-d H:i:s");

            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrailprod = array();
            $auditTrailprod['basicDetail']['trail_type'] = 'REVERSE';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrailprod['basicDetail']['table_name'] = 'erp_production_declarations';
            $auditTrailprod['basicDetail']['column_name'] = 'id'; // Primary key column
            $auditTrailprod['basicDetail']['document_id'] = $declarationId;  // primary key
            $auditTrailprod['basicDetail']['document_number'] = $sub_prod_code;
            $auditTrailprod['basicDetail']['party_id'] = 0;
            $auditTrailprod['basicDetail']['action_code'] = $action_code;
            $auditTrailprod['basicDetail']['action_referance'] = '';
            $auditTrailprod['basicDetail']['action_title'] = 'Production Reversed';  //Action comment
            $auditTrailprod['basicDetail']['action_name'] = 'Update';     //	Add/Update/Deleted
            $auditTrailprod['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrailprod['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrailprod['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrailprod['basicDetail']['action_sqlQuery'] = '';
            $auditTrailprod['basicDetail']['others'] = '';
            $auditTrailprod['basicDetail']['remark'] = '';

            $auditTrailprod['action_data']['Production Declaration Details']['Production Declaration Date'] = formatDateWeb($productionDeclareDate);
            $auditTrailprod['action_data']['Production Declaration Details']['ItemCode'] = $ItemCode;
            $auditTrailprod['action_data']['Production Declaration Details']['ItemName'] = $itemName;
            $auditTrailprod['action_data']['Production Declaration Details']['Production Quantity'] = decimalQuantityPreview($declarationQuantity);
            $auditTrailprod['action_data']['Production Declaration Details']['Created by'] = getCreatedByUser($createdby);

            $auditTrailprod['action_data']['Production Reverse Details']['Reverse By'] = getCreatedByUser($this->created_by);
            $auditTrailprod['action_data']['Production Reverse Details']['Reverse At'] = formatDateORDateTime($currentTime);
        }else{
            return [
                "status" => "warning",
                "message" => "Already Reversed!"
            ];
        }
        $resultObj = $dbObj->queryFinish();
        if ($resultObj['status'] == 'success') {
            $auditTrailreturn = generateAuditTrail($auditTrailprod);
        }
        return $resultObj;
    }

    // ////////////////////////////////////////////////////////Journal-----------------------------------------

    private function checkJournal($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT * FROM `' . ERP_ACC_JOURNAL . '`  WHERE id=' . $value);

        if ($obj["status"] == "success" && $obj["data"]["reverse_jid"] != "0") {
            return [
                "status" => "warning",
                "message" => "Action already Reversed.",
            ];
        }
        if ($obj["status"] == "success") {
            return [
                "status" => "success",
                "message" => "Ready for reverse posting",
                "id" => $obj["data"]["id"],
                "jv_no" => $obj["data"]["jv_no"]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for Reverse posting!",
            ];
        }
    }

    function reverseJOURNAL($value)
    {

        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Journal");
        $dbObj->setSuccessMsg("Journal Reversed successfully!");
        $dbObj->setErrorMsg("Journal Reversed failed!");

        $depreciationId = $value;
        $checkObj = $this->checkJournal($depreciationId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $journal_id = $checkObj["id"];
        $jv_no = $checkObj["jv_no"];

        $reverseRefCode = "REV" . $jv_no;

        //Reverse the journal


        //Account reverse for Invoice insert with REVERSEINV000001 reference--------------------------------------------------------

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        if ($journalObj["status"] == 'success') {
            $journalData = $journalObj["data"];
            $reversePostingDate = $journalData["postingDate"];

            $accounting = array();
            $accounting['journal']['parent_id'] = $journalData["parent_id"];
            $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
            $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
            $accounting['journal']['party_code'] = $journalData["party_code"];
            $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
            $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
            $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
            $accounting['journal']['documentDate'] = $journalData["documentDate"];
            $accounting['journal']['postingDate'] = $reversePostingDate;


            //credit details
            $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($debitObj["data"] as $debitRow) {
                $accounting['credit'][] = [
                    'glId' => $debitRow["glId"],
                    'subGlCode' => $debitRow["subGlCode"],
                    'subGlName' => $debitRow["subGlName"],
                    'credit_amount' => $debitRow["debit_amount"],
                    'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                ];
            }

            //debit details
            $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
            foreach ($creditObj["data"] as $creditRow) {
                $accounting['debit'][] = [
                    'glId' => $creditRow["glId"],
                    'subGlCode' => $creditRow["subGlCode"],
                    'subGlName' => $creditRow["subGlName"],
                    'debit_amount' => $creditRow["credit_amount"],
                    'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                ];
            }

            $accPostingObj = new AccountingPosting();
            $returnDataAcc = $accPostingObj->post($accounting);
            if ($returnDataAcc['status'] == 'success') {
                $newJournalId = $returnDataAcc['journalId'];

                //Journal Status change
                $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
            }
        }

        return $dbObj->queryFinish();
    }

    private function checkDebitNote($value)
    {

        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `dr_note_id`,`debitor_type`,`party_id`, `party_code`, `journal_id`,`goods_journal_id`,`party_name`,`debit_note_no`,`debit_note_no_serialized`,`debitNoteReference`,`postingDate`,`remark`,`source_address`,`destination_address`,`billing_address`,`shipping_address`,`contact_details`,`status`,`cgst`,`igst`,`sgst`,`total`,`adjustment` FROM `erp_debit_note` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `dr_note_id`=' . $value . '');

        // return $dbObj;

        if ($obj["status"] == "success" && $obj["data"]["status"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "Debit Note already Reversed.",
            ];
        }


        if ($obj["status"] == "success") {

            return [
                "status" => "success",
                "message" => "Ready for reverse posting",
                "dr_note_id" => $obj["data"]["dr_note_id"],
                "debitor_type" => $obj["data"]["debitor_type"],
                "party_id" => $obj["data"]["party_id"],
                "party_code" => $obj["data"]["party_code"],
                "party_name" => $obj["data"]["party_name"],
                "debit_note_no" => $obj["data"]["debit_note_no"],
                "journal_id" => $obj["data"]["journal_id"],
                "goods_journal_id" => $obj["data"]["goods_journal_id"],
                "debitNoteReference" => $obj["data"]["debitNoteReference"],
                "debit_note_no_serialized" => $obj["data"]["debit_note_no_serialized"]


            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for Debit Note Reverse posting",
                "obj" => $obj
            ];
        }
    }
    function reverseDebitNote($value)
    {
        // return 0;
        //return $value;
        global $created_by;
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Debit Note");
        $dbObj->setSuccessMsg("Debit Note Reversed successfully!");
        $dbObj->setErrorMsg("Debit Note Reversed failed!");
        $debitNoteId = $value;
        $checkObj = $this->checkDebitNote($debitNoteId);

        // return $checkObj;

        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $checkObject = $this->checkCollectionAgainstInvoice($debitNoteId, 'dn');
        if ($checkObject['status'] == "warning") {
            return $checkObject;
            exit();
        }

        $dr_note_id = $checkObj["dr_note_id"];
        $debit_note_no = $checkObj["debit_note_no"];
        $journal_id = $checkObj["journal_id"];
        $goods_journal_id = $checkObj['goods_journal_id'];


        $reverseRefCode = "REV" . $debit_note_no;

        $stocks_log = $dbObj->queryGet("SELECT `storageLocationId`,`parentId`, `storageType`, `itemId`, -1 * `itemQty` AS `itemQty`, `itemUom`, `itemPrice`, `refActivityName`, `logRef`, `refNumber`, `bornDate`, `postingDate` FROM `erp_inventory_stocks_log` WHERE `refActivityName`='DN' AND `refNumber`='$debit_note_no' AND status='active'", true);

        foreach ($stocks_log['data'] as $oneItem) {
            $goodId = $oneItem["itemId"];
            $bornDate = $oneItem["bornDate"];
            $postingDate = $oneItem["postingDate"];
            $storageLocation = $oneItem["storageLocationId"];
            $storageType = $oneItem["storageType"];
            $receivedQty = $oneItem["itemQty"];
            $itemUom = $oneItem["itemUom"] ?? '';
            $unitPrice = $oneItem["itemPrice"];
            $logRef = $oneItem["logRef"];
            $parentId = is_null($oneItem["parentId"]) ? 'NULL' : $oneItem["parentId"];

            $dbObj->queryInsert('INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $this->company_id . ',`parentId`='.$parentId.',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`storageLocationId`=' . $storageLocation . ',`storageType`="' . $storageType . '",`itemId`=' . $goodId . ',`itemQty`=' . $receivedQty . ',`itemUom`="' . $itemUom . '",`itemPrice`=' . $unitPrice . ',`refActivityName`="REV-DN",`logRef`="' . $logRef . '",`refNumber`="' . $reverseRefCode . '",`bornDate`="' . $bornDate . '",`postingDate`="' . $postingDate . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');
        }


        //Reverse the journal

        //Account reverse for PGI insert with REVERSEINV000001 reference---------------------------------------------------------
        if ($journal_id > 0) {
            $newInvoiceJournalId = 0;

            $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
            // console($journalObj);
            if ($journalObj["status"] == 'success') {
                $journalData = $journalObj["data"];
                $reversePostingDate = $journalData["postingDate"];

                $accounting = array();
                $accounting['journal']['parent_id'] = $journalData["parent_id"];
                $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
                $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['party_code'] = $journalData["party_code"];
                $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
                $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
                $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['documentDate'] = $journalData["documentDate"];
                $accounting['journal']['postingDate'] = $reversePostingDate;


                //credit details
                $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'credit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                    ];
                }

                //debit details
                $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
                foreach ($creditObj["data"] as $creditRow) {
                    $accounting['debit'][] = [
                        'glId' => $creditRow["glId"],
                        'subGlCode' => $creditRow["subGlCode"],
                        'subGlName' => $creditRow["subGlName"],
                        'debit_amount' => $creditRow["credit_amount"],
                        'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                    ];
                }

                $accPostingObj = new AccountingPosting();
                $returnDataAcc = $accPostingObj->post($accounting);
                if ($returnDataAcc['status'] == 'success') {
                    $newJournalId = $returnDataAcc['journalId'];

                    //Journal Status change
                    $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
                }
            }



            $dbObj->queryUpdate('UPDATE `erp_debit_note` SET `status`="reverse", `journal_id`=' . $newInvoiceJournalId . ' WHERE `dr_note_id`=' . $dr_note_id);
        }


        //goods journal new code 


        if ($goods_journal_id > 0) {
            $newInvoiceJournalId = 0;

            $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $goods_journal_id . ' AND `branch_id`=' . $this->branch_id);
            // console($journalObj);
            if ($journalObj["status"] == 'success') {
                $journalData = $journalObj["data"];
                $reversePostingDate = $journalData["postingDate"];

                $accounting = array();
                $accounting['journal']['parent_id'] = $journalData["parent_id"];
                $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
                $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['party_code'] = $journalData["party_code"];
                $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
                $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
                $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['documentDate'] = $journalData["documentDate"];
                $accounting['journal']['postingDate'] = $reversePostingDate;


                //credit details
                $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'credit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                    ];
                }

                //debit details
                $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
                foreach ($creditObj["data"] as $creditRow) {
                    $accounting['debit'][] = [
                        'glId' => $creditRow["glId"],
                        'subGlCode' => $creditRow["subGlCode"],
                        'subGlName' => $creditRow["subGlName"],
                        'debit_amount' => $creditRow["credit_amount"],
                        'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                    ];
                }

                $accPostingObj = new AccountingPosting();
                $returnDataAcc = $accPostingObj->post($accounting);
                if ($returnDataAcc['status'] == 'success') {
                    $newJournalId = $returnDataAcc['journalId'];

                    //Journal Status change
                    $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
                }
            }



            $dbObj->queryUpdate('UPDATE `erp_debit_note` SET `status`="reverse", `goods_journal_id`=' . $newInvoiceJournalId . ' WHERE `dr_note_id`=' . $dr_note_id);
            $debitsql = $dbObj->queryGet("SELECT * FROM `erp_debit_note` WHERE dr_note_id=$dr_note_id")['data'];
            $debitor_type = $debitsql['debitor_type'];
            $party_id = $debitsql['party_id'];
            $remark = $debitsql['remark'];
            $party_name = $debitsql['party_name'];
            $party_code = $debitsql['party_code'];
            $posting_date = $debitsql['postingDate'];
            $subtotal = $debitsql['total'];
            $createdby = $debitsql['created_by'];
            $taxComponents = $debitsql['taxComponents'];
            $taxComponent_tax = json_decode($taxComponents, true);
            $currentTime = date("Y-m-d H:i:s");

            $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
            $auditTrail = array();
            $auditTrail['basicDetail']['trail_type'] = 'REVERSE';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
            $auditTrail['basicDetail']['table_name'] = 'erp_debit_note';
            $auditTrail['basicDetail']['column_name'] = 'dr_note_id'; // Primary key column
            $auditTrail['basicDetail']['document_id'] = $dr_note_id;  // primary key
            $auditTrail['basicDetail']['party_type'] = $debitor_type;
            $auditTrail['basicDetail']['party_id'] = $party_id;
            $auditTrail['basicDetail']['action_code'] = $action_code;
            $auditTrail['basicDetail']['action_referance'] = '';
            $auditTrail['basicDetail']['action_title'] = 'Debit Note Reversed';  //Action comment
            $auditTrail['basicDetail']['action_name'] = 'Add';  //	Add/Update/Deleted
            $auditTrail['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
            $auditTrail['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
            $auditTrail['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
            $auditTrail['basicDetail']['action_sqlQuery'] = '';
            $auditTrail['basicDetail']['others'] = '';
            $auditTrail['basicDetail']['remark'] = $remark;
            $auditTrail['action_data']['Debit Note Detail']['party name'] = $party_name;
            $auditTrail['action_data']['Debit Note Detail']['party type'] = $debitor_type;
            $auditTrail['action_data']['Debit Note Detail']['party code'] = $party_code;
            $auditTrail['action_data']['Debit Note Detail']['postingDate'] = formatDateWeb($posting_date);
            $auditTrail['action_data']['Debit Note Detail']['total'] = decimalValuePreview($subtotal);
            foreach ($taxComponent_tax as $tax) {
                $auditTrail['action_data']['Invoice Details'][$tax['gstType']] = decimalValuePreview($tax['taxAmount']);
            }
            $auditTrail['action_data']['Debit Note Detail']['created_by'] = getCreatedByUser($createdby);
            $auditTrail['action_data']['Debit Note Detail']['updated_by'] = getCreatedByUser($createdby);

            $auditTrail['action_data']['Reverse Details']['Reversed By'] = getCreatedByUser($created_by);
            $auditTrail['action_data']['Reverse Details']['Reversed At'] = formatDateTime($currentTime);

            $auditTrailreturn = generateAuditTrail($auditTrail);
        }




        return $dbObj->queryFinish();
    }



    private function checkCreditNote($value)
    {

        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `cr_note_id`,`creditors_type`,`party_id`, `party_code`, `journal_id`,`goods_journal_id`,`party_name`,`credit_note_no`,`credit_note_no_serialized`,`creditNoteReference`,`postingDate`,`remark`,`source_address`,`destination_address`,`billing_address`,`shipping_address`,`contact_details`,`status`,`cgst`,`igst`,`sgst`,`total`,`adjustment` FROM `erp_credit_note` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `cr_note_id`=' . $value . '');

        // return $dbObj;

        if ($obj["status"] == "success" && $obj["data"]["status"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "Credit Note already Reversed.",
            ];
        }


        if ($obj["status"] == "success") {

            return [
                "status" => "success",
                "message" => "Ready for reverse posting",
                "cr_note_id" => $obj["data"]["cr_note_id"],
                "creditors_type" => $obj["data"]["creditors_type"],
                "party_id" => $obj["data"]["party_id"],
                "party_code" => $obj["data"]["party_code"],
                "party_name" => $obj["data"]["party_name"],
                "credit_note_no" => $obj["data"]["credit_note_no"],
                "journal_id" => $obj["data"]["journal_id"],
                "goods_journal_id" => $obj["data"]["goods_journal_id"],
                "creditNoteReference" => $obj["data"]["creditNoteReference"],
                "credit_note_no_serialized" => $obj["data"]["credit_note_no_serialized"]


            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for Credit Note Reverse posting",
                "obj" => $obj
            ];
        }
    }
    function reverseCreditNote($value)
    {
        // return 0;
        //return $value;

        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Credit Note");
        $dbObj->setSuccessMsg("Credit Note Reversed successfully!");
        $dbObj->setErrorMsg("Credit Note Reversed failed!");
        $creditNoteId = $value;
        $checkObj = $this->checkCreditNote($creditNoteId);

        // return $checkObj;

        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $cr_note_id = $checkObj["cr_note_id"];
        $credit_note_no = $checkObj["credit_note_no"];
        $journal_id = $checkObj["journal_id"];
        $goods_journal_id = $checkObj["goods_journal_id"];


        $reverseRefCode = "REV" . $credit_note_no;

        $stocks_log = $dbObj->queryGet("SELECT `storageLocationId`, `storageType`, `itemId`, -1 * `itemQty` AS `itemQty`, `itemUom`, `itemPrice`, `refActivityName`, `logRef`, `refNumber`, `bornDate`, `postingDate` FROM `erp_inventory_stocks_log` WHERE `refActivityName`='CN' AND `refNumber`='$credit_note_no' AND status='active'", true);

        // foreach ($stocks_log['data'] as $oneItem) {
        //     $goodId = $oneItem["itemId"];
        //     $bornDate = $oneItem["bornDate"];
        //     $postingDate = $oneItem["postingDate"];
        //     $storageLocation = $oneItem["storageLocationId"];
        //     $storageType = $oneItem["storageType"];
        //     $receivedQty = $oneItem["itemQty"];
        //     $itemUom = $oneItem["itemUom"] ?? '';
        //     $unitPrice = $oneItem["itemPrice"];
        //     $logRef = $oneItem["logRef"];

        //     $dbObj->queryInsert('INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $this->company_id . ',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`storageLocationId`=' . $storageLocation . ',`storageType`="' . $storageType . '",`itemId`=' . $goodId . ',`itemQty`=' . $receivedQty . ',`itemUom`="' . $itemUom . '",`itemPrice`=' . $unitPrice . ',`refActivityName`="REV-CN",`logRef`="' . $logRef . '",`refNumber`="' . $reverseRefCode . '",`bornDate`="' . $bornDate . '",`postingDate`="' . $postingDate . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');
        // }


        //Reverse the journal

        //Account reverse for PGI insert with REVERSEINV000001 reference---------------------------------------------------------


        // cn accounting
        $newJournalIdAcc = 0;
        $revAcc = [];
        if ($journal_id > 0) {
            $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
            // console($journalObj);
            if ($journalObj["status"] == 'success') {
                $journalData = $journalObj["data"];
                $reversePostingDate = $journalData["postingDate"];

                $accounting = array();
                $accounting['journal']['parent_id'] = $journalData["parent_id"];
                $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
                $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['party_code'] = $journalData["party_code"];
                $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
                $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
                $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['documentDate'] = $journalData["documentDate"];
                $accounting['journal']['postingDate'] = $reversePostingDate;


                //credit details
                $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'credit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                    ];
                }

                //debit details
                $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
                foreach ($creditObj["data"] as $creditRow) {
                    $accounting['debit'][] = [
                        'glId' => $creditRow["glId"],
                        'subGlCode' => $creditRow["subGlCode"],
                        'subGlName' => $creditRow["subGlName"],
                        'debit_amount' => $creditRow["credit_amount"],
                        'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                    ];
                }

                $accPostingObj = new AccountingPosting();
                $returnDataAcc = $accPostingObj->post($accounting);
                $revAcc = $returnDataAcc;
                if ($returnDataAcc['status'] == 'success') {
                    $newJournalId = $returnDataAcc['journalId'];
                    $newJournalIdAcc = $newJournalId;

                    //Journal Status change
                    $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
                }
            }

            // $dbObj->queryUpdate('UPDATE `erp_credit_note` SET `status`="reverse", `journal_id`=' . $newInvoiceJournalId . ' WHERE `cr_note_id`=' . $cr_note_id);
        }


        // pgi accounting

        $newGoodsJournalId = 0;
        $revGoodsAcc = [];
        if ($goods_journal_id > 0) {
            $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $goods_journal_id . ' AND `branch_id`=' . $this->branch_id);
            // console($journalObj);
            if ($journalObj["status"] == 'success') {
                $journalData = $journalObj["data"];
                $reversePostingDate = $journalData["postingDate"];

                $accounting = array();
                $accounting['journal']['parent_id'] = $journalData["parent_id"];
                $accounting['journal']['parent_slug'] = $journalData["parent_slug"];
                $accounting['journal']['refarenceCode'] = addslashes(stripslashes($journalData["refarenceCode"]));
                $accounting['journal']['remark'] = 'REV-' . addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['party_code'] = $journalData["party_code"];
                $accounting['journal']['party_name'] = addslashes(stripslashes($journalData["party_name"]));
                $accounting['journal']['journalEntryReference'] = $journalData["journalEntryReference"];
                $accounting['journal']['documentNo'] = addslashes(stripslashes($journalData["documentNo"]));
                $accounting['journal']['documentDate'] = $journalData["documentDate"];
                $accounting['journal']['postingDate'] = $reversePostingDate;


                //credit details
                $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $goods_journal_id, true);
                foreach ($debitObj["data"] as $debitRow) {
                    $accounting['credit'][] = [
                        'glId' => $debitRow["glId"],
                        'subGlCode' => $debitRow["subGlCode"],
                        'subGlName' => $debitRow["subGlName"],
                        'credit_amount' => $debitRow["debit_amount"],
                        'credit_remark' => 'Reverse ' . $debitRow["debit_remark"]
                    ];
                }

                //debit details
                $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $goods_journal_id, true);
                foreach ($creditObj["data"] as $creditRow) {
                    $accounting['debit'][] = [
                        'glId' => $creditRow["glId"],
                        'subGlCode' => $creditRow["subGlCode"],
                        'subGlName' => $creditRow["subGlName"],
                        'debit_amount' => $creditRow["credit_amount"],
                        'debit_remark' => 'Reverse ' . $creditRow["credit_remark"]
                    ];
                }

                $accPostingObj = new AccountingPosting();
                $returnDataAcc = $accPostingObj->post($accounting);
                $revGoodsAcc = $returnDataAcc;
                if ($returnDataAcc['status'] == 'success') {
                    $newJournalId = $returnDataAcc['journalId'];
                    $newGoodsJournalId = $newJournalId;

                    //Journal Status change
                    $dbObj->queryUpdate('UPDATE `' . ERP_ACC_JOURNAL . '` SET `reverse_jid`=' . $newJournalId . ' WHERE `id`=' . $journal_id);
                }
            }

            // $dbObj->queryUpdate('UPDATE `erp_credit_note` SET `status`="reverse", `goods_journal_id`=' . $newGoodsJournalId . ' WHERE `cr_note_id`=' . $cr_note_id);
        }


        if ($revAcc['status'] == 'success' && $revGoodsAcc['status'] == 'success') {
            $dbObj->queryUpdate('UPDATE `erp_credit_note` SET `status`="reverse", `journal_id`=' . $newJournalIdAcc . ' ,`goods_journal_id`=' . $newGoodsJournalId . ' WHERE `cr_note_id`=' . $cr_note_id);
        }

        return $dbObj->queryFinish();

        // return ['allquerry'=>$dbObj->queryFinish(),'accountingArrays'=>['cn'=>$revAcc,'pgi'=>$revGoodsAcc]];
    }

    //Formula to calculate  New Moving Weighted Price Revarsal
    function calculateNewMwpForRev($itemId, $newQty, $newPrice)
    {
        global $company_id;
        global $branch_id;
        global $location_id;

        $movingWeightedPrice = 0;

        $goodStockSummaryCheckSql = queryGet('SELECT `itemId`, `rmWhOpen`, `rmWhReserve`, `itemTotalQty`, `movingWeightedPrice` FROM `erp_inventory_stocks_summary` WHERE `company_id`=' . $company_id . ' AND `branch_id`=' . $branch_id . ' AND `location_id`=' . $location_id . ' AND `itemId`=' . $itemId);

        $prevTotalQty = $newQty;
        $prevMovingWeightedPrice = $goodStockSummaryCheckSql["data"]["movingWeightedPrice"] ?? 0;
        $prevTotalPrice = $prevTotalQty * $prevMovingWeightedPrice;

        $itemNewTotalQty = (float)$newQty;
        $itemNewTotalPrice = (float)$prevTotalPrice + (($newQty * $newPrice));
        $movingWeightedPrice = (float)($itemNewTotalPrice / $itemNewTotalQty);
        return $movingWeightedPrice;
    }
}
