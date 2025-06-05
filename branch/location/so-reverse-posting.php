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

    private function checkPGI($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `so_delivery_pgi_id`, `journal_id`, `pgi_no`,`invoiceStatus`,`status` FROM `erp_branch_sales_order_delivery_pgi` WHERE `companyId`=' . $this->company_id . ' AND `branchId`=' . $this->branch_id . ' AND `locationId`=' . $this->location_id . ' AND `so_delivery_pgi_id`=' . $value);

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
                "grnId" => $obj["data"]["so_delivery_pgi_id"],
                "grnCode" => $obj["data"]["pgi_no"],
                "journal_id" => $obj["data"]["journal_id"]
            ];
        } else {
            return [
                "status" => "warning",
                "message" => "Not eligible for GRN Reverse posting, please reverse IV first",
            ];
        }
    }
    private function checkInvoice($value)
    {
        $dbObj = new Database();

        $obj = $dbObj->queryGet('SELECT `so_invoice_id`,`pgi_id`, `pgi_journal_id`, `journal_id`,`invoice_no`,`so_number`,`status` FROM `' . ERP_BRANCH_SALES_ORDER_INVOICES . '` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `so_invoice_id`=' . $value);

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

    private function checkCollection($value)
    {
        $obj = $this->queryGet('SELECT `journal_id` FROM `erp_branch_sales_order_payments` WHERE `company_id`=' . $this->company_id . ' AND `branch_id`=' . $this->branch_id . ' AND `location_id`=' . $this->location_id . ' AND `payment_id`=' . $value);
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
            case "pgi":
                return $this->checkPGI($value);
            case "invoice":
                return $this->checkInvoice($value);
            case "collection":
                return $this->checkCollection($value);
            default:
                return ["status" => "warning", "message" => "Please provide a valid TYPE"];
        }
    }

    function reversePGI($value)
    {
        global $company_id;
        global $created_by;
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse PGI");
        $dbObj->setSuccessMsg("PGI Resersed successfully!");
        $dbObj->setErrorMsg("PGI Resersed failed!");

        $pgiId = $value;
        $checkObj = $this->checkPGI($pgiId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $pgiId = $checkObj["so_delivery_pgi_id"];
        $pgiCode = $checkObj["pgi_no"];
        $journal_id = $checkObj["journal_id"];

        $reverseRefCode = "REVERSE" . $pgiCode;

        //Reverse the journal


        //Account reverse for Invoice insert with REVERSEINV000001 reference--------------------------------------------------------

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

        //Invoice Status change
        $dbObj->queryUpdate('UPDATE `erp_branch_sales_order_delivery_pgi` SET `status`="reverse", `journal_id`=' . $newJournalId . ' WHERE `so_delivery_pgi_id`=' . $pgiId);
        $pgidetails=$dbObj->queryGet("SELECT * FROM `erp_branch_sales_order_delivery_pgi` WHERE `so_delivery_pgi_id` = $pgiId AND `company_id` = $company_id")['data'];
        $customerId=$pgidetails['customer_id'];
        $pgiNo=$pgidetails['pgi_no'];
        $soNumber=$pgidetails['so_number'];
        $deliveryNo=$pgidetails['delivery_no'];
        $soNumber=$pgidetails['so_number'];
        $customer_billing_address=$pgidetails['customer_billing_address'];
        $customer_shipping_address=$pgidetails['customer_shipping_address'];
        $pgiDate=$pgidetails['pgiDate'];
        $profitCenter=$pgidetails['profit_center'];
        $customerPO=$pgidetails['customer_po_no'];


        $currentTime = date("Y-m-d H:i:s");
        $action_code = time() . rand(11, 99) . rand(11, 99) . rand(11, 9999);
        $auditTrailPgi = array();
        $auditTrailPgi['basicDetail']['trail_type'] = 'REVERSE';  //'ADD','EDIT','DELETE','ACCENTRY,'ACCREVERSE','MAILSEND','MAILSEEN','APPROVED'
        $auditTrailPgi['basicDetail']['table_name'] = ERP_BRANCH_SALES_ORDER_DELIVERY_PGI;
        $auditTrailPgi['basicDetail']['column_name'] = 'so_delivery_pgi_id'; // Primary key column
        $auditTrailPgi['basicDetail']['document_id'] = $pgiId;  // primary key
        $auditTrailPgi['basicDetail']['party_type'] = 'customer';
        $auditTrailPgi['basicDetail']['party_id'] = $customerId;
        $auditTrailPgi['basicDetail']['document_number'] = $pgiNo;
        $auditTrailPgi['basicDetail']['action_code'] = $action_code;
        $auditTrailPgi['basicDetail']['action_referance'] = $soNumber;
        $auditTrailPgi['basicDetail']['action_title'] = 'PGI Reversed';  //Action comment
        $auditTrailPgi['basicDetail']['action_name'] = 'Add';     //	Add/Update/Deleted
        $auditTrailPgi['basicDetail']['action_type'] = 'Non-Monitory'; //Monitory/Non-Monitory
        $auditTrailPgi['basicDetail']['action_url'] = BASE_URL . $_SERVER['REQUEST_URI'];
        $auditTrailPgi['basicDetail']['action_previous_url'] = $_SERVER['HTTP_REFERER'];
        $auditTrailPgi['basicDetail']['action_sqlQuery'] = '';
        $auditTrailPgi['basicDetail']['others'] = '';
        $auditTrailPgi['basicDetail']['remark'] = '';

        $auditTrailPgi['action_data']['Pgi Details']['Pgi_no'] = $pgiNo;
        $auditTrailPgi['action_data']['Pgi Details']['Delivery_no'] = $deliveryNo;
        $auditTrailPgi['action_data']['Pgi Details']['So_number'] = $soNumber;
        // $auditTrailPgi['action_data']['Pgi Details']['Customer_id'] = $customerId;
        $auditTrailPgi['action_data']['Pgi Details']['Customer_billing_address'] = $customer_billing_address;
        $auditTrailPgi['action_data']['Pgi Details']['Customer_shipping_address'] = $customer_shipping_address;
        $auditTrailPgi['action_data']['Pgi Details']['PgiDate'] = formatDateWeb($pgiDate);
        $auditTrailPgi['action_data']['Pgi Details']['Profit_center'] = $profitCenter;
        $auditTrailPgi['action_data']['Pgi Details']['PgiStatus'] = 'open';
        // $auditTrailPgi['action_data']['Pgi Details']['InvoiceStatus'] = 9;
        $auditTrailPgi['action_data']['Pgi Details']['Customer_po_no'] = $customerPO;

        
        $auditTrail['action_data']['Reverse Details']['Reversed By'] = getCreatedByUser($created_by);
        $auditTrail['action_data']['Reverse Details']['Reversed At'] = formatDateTime($currentTime);

        $auditTrailreturn = generateAuditTrail($auditTrail);

        return $dbObj->queryFinish();
    }

    function reverseInvoice($value)
    {
        $dbObj = new Database(true);

        $dbObj->setActionName("Reverse Invoice");
        $dbObj->setSuccessMsg("Invoice Resersed successfully!");
        $dbObj->setErrorMsg("Invoice Resersed failed!");

        $invoiceId = $value;
        $checkObj = $this->checkInvoice($invoiceId);
        if ($checkObj["status"] != "success") {
            return $checkObj;
        }

        $invoiceId = $checkObj["so_invoice_id"];
        $pgi_id = $checkObj["pgi_id"] ?? '';
        $so_number = $checkObj["so_number"] ?? '';
        $invoiceCode = $checkObj["invoice_no"];
        $journal_id = $checkObj["journal_id"];
        $pgi_journal_id = $checkObj["pgi_journal_id"];

        $reverseRefCode = "REVERSE" . $invoiceCode;

        //Reverse the journal

        //Account reverse for PGI insert with REVERSEINV000001 reference---------------------------------------------------------

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        $journalData = $journalObj["data"];
        $newJvNo = $this->getNextJvNo();
        $reversePostingDate = date("Y-m-d");

        //journal entry
        $newJournalObj = $dbObj->queryInsert('INSERT INTO `erp_acc_journal` SET `company_id`=' . $journalData["company_id"] . ', `branch_id`=' . $journalData["branch_id"] . ', `location_id`=' . $journalData["location_id"] . ', `jv_no`=' . $newJvNo . ', `party_code`="' . $journalData["party_code"] . '", `party_name`="' . $journalData["party_name"] . '", `parent_id`=' . $journalData["parent_id"] . ', `parent_slug`="' . $journalData["parent_slug"] . '", `refarenceCode`="' . $journalData["refarenceCode"] . '", `journalEntryReference`="' . $journalData["journalEntryReference"] . '", `documentNo`="' . $journalData["documentNo"] . '", `documentDate`="' . $journalData["documentDate"] . '", `postingDate`="' . $reversePostingDate . '", `remark`="REVERSE-' . $journalData["refarenceCode"] . '", `journal_created_by`="' . $this->created_by . '", `journal_updated_by`="' . $this->updated_by . '"');

        $newpgiJournalId = $newJournalObj["insertedId"];

        //credit details
        $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $journal_id, true);
        foreach ($debitObj["data"] as $debitRow) {
            $dbObj->queryInsert('INSERT INTO `erp_acc_credit` SET `journal_id`=' . $newpgiJournalId . ',`glId`=' . $debitRow["glId"] . ',`subGlCode`="' . $debitRow["subGlCode"] . '",`subGlName`="' . $debitRow["subGlName"] . '",`credit_amount`=' . $debitRow["debit_amount"] . ',`credit_remark`="Reverse ' . $debitRow["debit_remark"] . '", `credit_created_by`="' . $this->created_by . '",`credit_updated_by`="' . $this->updated_by . '"');
        }

        //debit details
        $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $journal_id, true);
        foreach ($creditObj["data"] as $creditRow) {
            $dbObj->queryInsert('INSERT INTO `erp_acc_debit` SET `journal_id`=' . $newpgiJournalId . ',`glId`=' . $creditRow["glId"] . ',`subGlCode`="' . $creditRow["subGlCode"] . '",`subGlName`="' . $creditRow["subGlName"] . '",`debit_amount`=' . $creditRow["credit_amount"] . ',`debit_remark`="Reverse ' . $creditRow["credit_remark"] . '", `debit_created_by`="' . $this->created_by . '",`debit_updated_by`="' . $this->updated_by . '"');
        }

        //Account reverse for Invoice insert with REVERSEINV000001 reference--------------------------------------------------------

        $journalObj = $dbObj->queryGet('SELECT `id`, `company_id`, `branch_id`, `location_id`, `jv_no`, `party_code`, `party_name`, `parent_id`, `parent_slug`, `refarenceCode`, `journalEntryReference`, `documentNo`, `documentDate`, `postingDate`, `remark`, `journal_created_at`, `journal_created_by`, `journal_updated_at`, `journal_updated_by`, `journal_status` FROM `erp_acc_journal` WHERE `id`=' . $pgi_journal_id . ' AND `branch_id`=' . $this->branch_id);
        // console($journalObj);
        $journalData = $journalObj["data"];
        $newJvNo = $this->getNextJvNo();
        $reversePostingDate = date("Y-m-d");

        //journal entry
        $newJournalObj = $dbObj->queryInsert('INSERT INTO `erp_acc_journal` SET `company_id`=' . $journalData["company_id"] . ', `branch_id`=' . $journalData["branch_id"] . ', `location_id`=' . $journalData["location_id"] . ', `jv_no`=' . $newJvNo . ', `party_code`="' . $journalData["party_code"] . '", `party_name`="' . $journalData["party_name"] . '", `parent_id`=' . $journalData["parent_id"] . ', `parent_slug`="' . $journalData["parent_slug"] . '", `refarenceCode`="' . $journalData["refarenceCode"] . '", `journalEntryReference`="' . $journalData["journalEntryReference"] . '", `documentNo`="' . $journalData["documentNo"] . '", `documentDate`="' . $journalData["documentDate"] . '", `postingDate`="' . $reversePostingDate . '", `remark`="REVERSE-' . $journalData["refarenceCode"] . '", `journal_created_by`="' . $this->created_by . '", `journal_updated_by`="' . $this->updated_by . '"');

        $newInvoiceJournalId = $newJournalObj["insertedId"];

        //credit details
        $debitObj = $dbObj->queryGet('SELECT * FROM `erp_acc_debit` WHERE `journal_id`=' . $pgi_journal_id, true);
        foreach ($debitObj["data"] as $debitRow) {
            $dbObj->queryInsert('INSERT INTO `erp_acc_credit` SET `journal_id`=' . $newInvoiceJournalId . ',`glId`=' . $debitRow["glId"] . ',`subGlCode`="' . $debitRow["subGlCode"] . '",`subGlName`="' . $debitRow["subGlName"] . '",`credit_amount`=' . $debitRow["debit_amount"] . ',`credit_remark`="Reverse ' . $debitRow["debit_remark"] . '", `credit_created_by`="' . $this->created_by . '",`credit_updated_by`="' . $this->updated_by . '"');
        }

        //debit details
        $creditObj = $dbObj->queryGet('SELECT * FROM `erp_acc_credit` WHERE `journal_id`=' . $pgi_journal_id, true);
        foreach ($creditObj["data"] as $creditRow) {
            $dbObj->queryInsert('INSERT INTO `erp_acc_debit` SET `journal_id`=' . $newInvoiceJournalId . ',`glId`=' . $creditRow["glId"] . ',`subGlCode`="' . $creditRow["subGlCode"] . '",`subGlName`="' . $creditRow["subGlName"] . '",`debit_amount`=' . $creditRow["credit_amount"] . ',`debit_remark`="Reverse ' . $creditRow["credit_remark"] . '", `debit_created_by`="' . $this->created_by . '",`debit_updated_by`="' . $this->updated_by . '"');
        }

        //Invoice Status change
        $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER_INVOICES . '` SET `status`="reverse", `pgi_journal_id`=' . $newpgiJournalId . ', `journal_id`=' . $newInvoiceJournalId . ' WHERE `so_invoice_id`=' . $invoiceId);



        //SO Status change
        if (!empty($so_number) || $so_number != 0) {
            $dbObj->queryUpdate('UPDATE `' . ERP_BRANCH_SALES_ORDER . '` SET `approvalStatus`= 9 WHERE company_id=' . $this->company_id . ' AND branch_id=' . $this->branch_id . '  AND location_id=' . $this->location_id . ' AND so_number="' . $so_number);
        }

        //PGI Status change
        if (!empty($pgi_id) || $pgi_id != 0) {
            $dbObj->queryUpdate('UPDATE `erp_branch_sales_order_delivery_pgi` SET `invoiceStatus`=9, `pgiStatus`="open", `updated_by`=' . $this->updated_by . ' WHERE `so_delivery_pgi_id`=' . $pgi_id);
        }


        return $dbObj->queryFinish();
    }
}
?>
<!-- <link rel="stylesheet" href="../../public/assets/sales-order.css"> -->
<link rel="stylesheet" href="../../public/assets/listing.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<div class="content-wrapper">
    <section class="container-fluid">
        Reverse Posting Sales
        <?php

        $reversePostingObj = new ReversePosting();
        // $res = $reversePostingObj->reverseInvoice(229);


        ////// $res = $reversePostingObj->checkEligiblility("Payment", 5);
        console($res ?? []);

        ?>
    </section>
</div>
<?php require_once("../common/footer.php"); ?>
<script>
    console.log("lets start the Sales Reverse Posting");
</script>