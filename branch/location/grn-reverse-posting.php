<?php
require_once("../../app/v1/connection-branch-admin.php");
// administratorLocationAuth();
require_once("../common/header.php");
require_once("../common/navbar.php");
require_once("../common/sidebar.php");
require_once("../common/pagination.php");
require_once("../../app/v1/functions/company/func-ChartOfAccounts.php");
require_once("../../app/v1/functions/company/func-branches.php");

class ReversePosting
{
    private $company_id;
    private $branch_id;
    private $location_id;
    private $updated_by;
    private $created_by;

    private $dbObj;

    function __construct()
    {
        global $company_id, $branch_id, $location_id, $created_by, $updated_by;
        $this->company_id = $company_id;
        $this->branch_id = $branch_id;
        $this->location_id = $location_id;
        $this->updated_by = $updated_by;
        $this->created_by = $created_by;
    }

    private function checkGRN($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `grnId`, `grnPostingJournalId`, `ivPostingJournalId`,`grnCode`,`grnStatus` FROM `erp_grn` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `grnId`=' . $value);

        if ($obj["status"] == "success" && $obj["data"]["grnStatus"] == "reverse") {
            return [
                "status" => "warning",
                "message" => "GRN already Reversed.",
            ];
        }
        if ($obj["status"] == "success" && $obj["data"]["iv_status"] == 0) {
            $grnItemObj = $dbObj->queryGet('SELECT `grnGoodId`, `grnId`, `grnCode`, `goodName`, `goodDesc`, `goodId`, `goodCode`, `goodHsn`, `goodQty`, `receivedQty`, `unitPrice`, `cgst`, `sgst`, `igst`, `tds`, `goodstype`, `totalAmount`, `itemStocksQty`, `itemUOM`, `itemStorageLocation`, `grnType`, `grnGoodStatus` FROM `erp_grn_goods` WHERE `grnId`=' . $value, true);
            $stockShortageLog = [];
            foreach ($grnItemObj["data"] as $grnItem) {
                $checkQty = $dbObj->queryGet('SELECT IFNULL(SUM(`itemQty`),0) AS availableQty FROM `erp_inventory_stocks_log` WHERE `locationId`=' . $this->location_id . ' AND `storageLocationId`=' . $grnItem["itemStorageLocation"] . ' AND `itemId`=' . $grnItem["goodId"]);
                if ($checkQty["data"]["availableQty"] < $grnItem["receivedQty"]) {
                    $stockShortageLog[] = [
                        "goodId" => $grnItem["goodId"],
                        "goodCode" => $grnItem["goodCode"],
                        "goodName" => $grnItem["goodName"],
                        "receivedQty" => $grnItem["receivedQty"],
                        "availableQty" => $grnItem["availableQty"],
                        "storageLocation" => $grnItem["itemStorageLocation"],
                    ];
                }
            }
            if (count($stockShortageLog) == 0) {
                $this->grnItems = $grnItemObj["data"];
                return [
                    "status" => "success",
                    "message" => "Ready for reverse posting",
                    "grnId" => $obj["data"]["grnId"],
                    "grnCode" => $obj["data"]["grnCode"],
                    "journal_id" => $obj["data"]["grnPostingJournalId"],
                    "stock_items" => $grnItemObj["data"]
                ];
            } else {
                return [
                    "status" => "warning",
                    "message" => "Stock Shortage",
                    "journal_id" => $obj["data"]["grnPostingJournalId"],
                    "stock_items" => $stockShortageLog
                ];
            }
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for GRN Reverse posting, please reverse IV first",
            ];
        }
    }
    private function checkIV($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `grnIvId`,`ivPostingJournalId`,`grnIvCode`,`grnStatus` FROM `erp_grninvoice` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `grnIvId`=' . $value);

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
                "grnIvId" => $obj["data"]["grnIvId"],
                "grnIvCode" => $obj["data"]["grnIvCode"],
                "journal_id" => $obj["data"]["ivPostingJournalId"]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for GRNIV Reverse posting, please reverse Payment first",
            ];
        }
    }

    private function checkPayment($value)
    {
        $obj = queryGet('SELECT `journal_id` FROM `erp_grn_payments` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `payment_id`=' . $value);
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

    private function getNextJvNo()
    {
        $dbObj = new Database();
        $jvObj = $dbObj->queryGet('SELECT MAX(`jv_no`)+1 AS newJvNo FROM `erp_acc_journal` WHERE `branch_id`=' . $this->branch_id);
        return $jvObj["data"]["newJvNo"] ?? 0;
    }

    function checkEligiblility($type = null, $value = null)
    {
        if ($type == null) {
            return ["status" => "warning", "message" => "Please provide a TYPE to check"];
        }
        switch (strtolower($type)) {
            case "grn":
                return $this->checkGRN($value);
            case "iv":
                return $this->checkIV($value);
            case "payment":
                return $this->checkPayment($value);
            default:
                return ["status" => "warning", "message" => "Please provide a valid TYPE"];
        }
    }

    function reverseGRN($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse GRN");
        $dbObj->setSuccessMsg("GRN Resersed successfully!");
        $dbObj->setErrorMsg("GRN Resersed failed!");

        $grnId = $value;
        $checkObj = $this->checkGRN($grnId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $grnId = $checkObj["grnId"];
        $grnCode = $checkObj["grnCode"];
        $journal_id = $checkObj["journal_id"];
        $stock_items = $checkObj["stock_items"];

        $reverseRefCode = "REVERSE" . $grnCode;

        //Reverse the journal

        //GRN Status change
        $dbObj->queryUpdate('UPDATE `erp_grn` SET `grnStatus`="reverse" WHERE `grnId`=' . $grnId);

        //Item stock minus entry & Moving weighing update
        foreach ($stock_items as $oneItem) {
            $goodId = $oneItem["goodId"];
            $goodCode = $oneItem["goodCode"];
            $goodHsn = $oneItem["goodHsn"];
            $goodQty = $oneItem["goodQty"];
            $storageLocation = $oneItem["itemStorageLocation"];
            $receivedQty = $oneItem["receivedQty"] * -1;
            $unitPrice = $oneItem["unitPrice"];
            $totalAmount = $oneItem["totalAmount"];

            $dbObj->queryInsert('INSERT INTO `erp_inventory_stocks_log` SET `companyId`=' . $this->company_id . ',`branchId`=' . $this->branch_id . ',`locationId`=' . $this->location_id . ',`storageLocationId`=' . $storageLocation . ',`storageType`="rmWhOpen",`itemId`=' . $goodId . ',`itemQty`=' . $receivedQty . ',`itemPrice`=' . $unitPrice . ',`logRef`="' . $reverseRefCode . '",`refNumber`="' . $reverseRefCode . '", `createdBy`="' . $this->created_by . '", `updatedBy`="' . $this->updated_by . '"');
        }

        //Account reverse insert with REVERSEGRN000001 reference

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        console($journalObj);
        $journalData = $journalObj["data"];
        $newJvNo = $this->getNextJvNo();
        $reversePostingDate = date("Y-m-d");

        //journal entry
        $newJournalObj = $dbObj->queryInsert('INSERT INTO `erp_acc_journal` SET `company_id`=' . $journalData["company_id"] . ', `branch_id`=' . $journalData["branch_id"] . ', `location_id`=' . $journalData["location_id"] . ', `jv_no`=' . $newJvNo . ', `party_code`="' . $journalData["party_code"] . '", `party_name`="' . $journalData["party_name"] . '", `parent_id`=' . $journalData["parent_id"] . ', `parent_slug`="' . $journalData["parent_slug"] . '", `refarenceCode`="' . $journalData["refarenceCode"] . '", `journalEntryReference`="' . $journalData["journalEntryReference"] . '", `documentNo`="' . $journalData["documentNo"] . '", `documentDate`="' . $journalData["documentDate"] . '", `postingDate`="' . $reversePostingDate . '", `remark`="REVERSE-' . $journalData["refarenceCode"] . '", `journal_created_by`="' . $this->created_by . '", `journal_updated_by`="' . $this->updated_by . '"');

        $newJournalId = $newJournalObj["insertedId"];

        //credit details
        $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
        foreach ($debitObj["data"] as $debitRow) {
            $dbObj->queryInsert('INSERT INTO `erp_acc_credit` SET `journal_id`=' . $newJournalId . ',`glId`=' . $debitRow["glId"] . ',`subGlCode`="' . $debitRow["subGlCode"] . '",`subGlName`="' . $debitRow["subGlName"] . '",`credit_amount`=' . $debitRow["debit_amount"] . ',`credit_remark`="Reverse ' . $debitRow["debit_remark"] . '", `credit_created_by`="' . $this->created_by . '",`credit_updated_by`="' . $this->updated_by . '"');
        }

        //debit details
        $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
        foreach ($creditObj["data"] as $creditRow) {
            $dbObj->queryInsert('INSERT INTO `erp_acc_debit` SET `journal_id`=' . $newJournalId . ',`glId`=' . $creditRow["glId"] . ',`subGlCode`="' . $creditRow["subGlCode"] . '",`subGlName`="' . $creditRow["subGlName"] . '",`debit_amount`=' . $creditRow["credit_amount"] . ',`debit_remark`="Reverse ' . $creditRow["credit_remark"] . '", `debit_created_by`="' . $this->created_by . '",`debit_updated_by`="' . $this->updated_by . '"');
        }


        return $dbObj->queryFinish();
    }

    function reverseGRNIV($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse GRNIV");
        $dbObj->setSuccessMsg("GRNIV Resersed successfully!");
        $dbObj->setErrorMsg("GRNIV Resersed failed!");

        $grnIvId = $value;
        $checkObj = $this->checkIV($grnIvId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $grnIvId = $checkObj["grnIvId"];
        $grnIvCode = $checkObj["grnIvCode"];
        $journal_id = $checkObj["journal_id"];

        $reverseRefCode = "REVERSE" . $grnIvCode;

        //Reverse the journal


        //Account reverse insert with REVERSEGRNIV000001 reference

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        $journalData = $journalObj["data"];
        $newJvNo = $this->getNextJvNo();
        $reversePostingDate = date("Y-m-d");

        //journal entry
        $newJournalObj = $dbObj->queryInsert('INSERT INTO `erp_acc_journal` SET `company_id`=' . $journalData["company_id"] . ', `branch_id`=' . $journalData["branch_id"] . ', `location_id`=' . $journalData["location_id"] . ', `jv_no`=' . $newJvNo . ', `party_code`="' . $journalData["party_code"] . '", `party_name`="' . $journalData["party_name"] . '", `parent_id`=' . $journalData["parent_id"] . ', `parent_slug`="' . $journalData["parent_slug"] . '", `refarenceCode`="' . $journalData["refarenceCode"] . '", `journalEntryReference`="' . $journalData["journalEntryReference"] . '", `documentNo`="' . $journalData["documentNo"] . '", `documentDate`="' . $journalData["documentDate"] . '", `postingDate`="' . $reversePostingDate . '", `remark`="REVERSE-' . $journalData["refarenceCode"] . '", `journal_created_by`="' . $this->created_by . '", `journal_updated_by`="' . $this->updated_by . '"');

        $newJournalId = $newJournalObj["insertedId"];

        //credit details
        $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
        foreach ($debitObj["data"] as $debitRow) {
            $dbObj->queryInsert('INSERT INTO `erp_acc_credit` SET `journal_id`=' . $newJournalId . ',`glId`=' . $debitRow["glId"] . ',`subGlCode`="' . $debitRow["subGlCode"] . '",`subGlName`="' . $debitRow["subGlName"] . '",`credit_amount`=' . $debitRow["debit_amount"] . ',`credit_remark`="Reverse ' . $debitRow["debit_remark"] . '", `credit_created_by`="' . $this->created_by . '",`credit_updated_by`="' . $this->updated_by . '"');
        }

        //debit details
        $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
        foreach ($creditObj["data"] as $creditRow) {
            $dbObj->queryInsert('INSERT INTO `erp_acc_debit` SET `journal_id`=' . $newJournalId . ',`glId`=' . $creditRow["glId"] . ',`subGlCode`="' . $creditRow["subGlCode"] . '",`subGlName`="' . $creditRow["subGlName"] . '",`debit_amount`=' . $creditRow["credit_amount"] . ',`debit_remark`="Reverse ' . $creditRow["credit_remark"] . '", `debit_created_by`="' . $this->created_by . '",`debit_updated_by`="' . $this->updated_by . '"');
        }

        //GRN Status change
        $dbObj->queryUpdate('UPDATE `grninvoice` SET `grnStatus`="reverse", `ivPostingJournalId`=' . $newJournalId . ' WHERE `grnIvId`=' . $grnIvId);


        return $dbObj->queryFinish();
    }


    
}
?>
<!-- <link rel="stylesheet" href="../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<div class="content-wrapper">
    <section class="container-fluid">
        Reverse Posting
        <?php

        $reversePostingObj = new ReversePosting();
        $res = $reversePostingObj->reverseGRN(229);


        // $res = $reversePostingObj->checkEligiblility("Payment", 5);
        console($res ?? []);

        ?>
    </section>
</div>
<?php require_once("../common/footer.php"); ?>
<script>
    console.log("lets start the Reverse Posting");
</script>